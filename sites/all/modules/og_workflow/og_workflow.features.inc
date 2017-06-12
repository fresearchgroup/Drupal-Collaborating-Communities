<?php

/**
 * @file
 * Integrates og_workflow with features.
 */

define('OG_WORKFLOW_FEATURES_AUTHOR', 'og_workflow_features_author');

/**
 * Implements hook_features_export().
 * Tell workflow and og to export related workflows and roles respectively.
 */
function og_workflow_features_export($data, &$export, $module_name = '') {
  $export['dependencies']['features'] = 'features';
  $export['dependencies']['workflow'] = 'workflow';

  $pipe = array();
  foreach ($data as $name) {
    $export['features']['og_workflow'][$name] = $name;

    // Depend on the workflow feature of the same name to provide the framework.
    $pipe['workflow'][$name] = $name;

    // Add related og roles.
    $og_roles = _og_workflow_get_og_roles($name);
    if (!empty($og_roles)) {
      if (empty($pipe['og_features_role'])) {
        $pipe['og_features_role'] = $og_roles;
      }
      else {
        $pipe['og_features_role'] += $og_roles;
      }
    }
  }
  return $pipe;
}

/**
 * Implements hook_features_export_options().
 * Export all workflows (by name) that are related to og_workflow.
 */
function og_workflow_features_export_options() {
  $workflows = array();
  foreach (_og_workflow_get_workflows() as $wid => $name) {
    $workflows[$name] = $name;
  }
  return $workflows;
}

/**
 * Implements hook_features_export_render().
 * Export all workflows that are related to og_workflow by exporting their transitions.
 */
function og_workflow_features_export_render($module, $data) {
  $translatables = $code = array();
  $code[] = '  $og_workflows = array();';
  foreach ($data as $name) {
    if ($transitions = _og_workflow_get_transitions($name)) {
      $workflow['name'] = $name;
      $workflow['transitions'] = $transitions;
      $export = features_var_export($workflow, '  ');
      $identifier = features_var_export($name);
      $code[] = '  ';
      $code[] = "  // Exported og_workflow: $name";
      $code[] = "  \$og_workflows[{$identifier}] = {$export};";
      $code[] = "";
    }
  }
  $code[] = '  return $og_workflows;';
  $code = implode("\n", $code);
  return array('og_workflow_default_transitions' => $code);
}

/**
 * Implements hook_features_revert().
 */
function og_workflow_features_revert($module) {
  foreach (features_get_default('og_workflow', $module) as $key => $workflow) {
    _og_workflow_update_transitions($workflow);
  }
}

/**
 * Implements hook_export_rebuild().
 */
function og_workflow_features_export_rebuild($module) {
  og_workflow_features_revert($module);
}


////////////// Helpers

/**
 * Return workflows which have associated OG Workflow transitions.
 *
 * @return array
 *   Associative array of workflow names keyed by wid
 */
function _og_workflow_get_workflows() {
  $query = db_select('workflows', 'w');
  $query->join('workflow_states', 'ws', 'ws.wid = w.wid');
  $query->join('og_workflow_transitions', 'ogwt', 'ws.sid = ogwt.sid');
  $workflows = $query->fields('w', array('wid', 'name'))
    ->execute()
    ->fetchAllKeyed();
  return $workflows;
}

/**
 * Get a set of dependent og_roles based on the workflow with the given name.
 *
 * @param string $name
 *   The name of the given workflow to get og_roles dependencies for.
 */
function _og_workflow_get_og_roles($name) {
  $cached = &drupal_static(__FUNCTION__, array());
  if (empty($cached[$name])) {
    $roles = array();
    if ($transitions = _og_workflow_get_transitions($name)) {
      foreach ($transitions as $transition) {
        $roles = array_merge($roles, $transition->roles);
      }
      // Unset the author pseudo role.
      unset($roles[OG_WORKFLOW_FEATURES_AUTHOR]);
    }
    $cached[$name] = $roles;
  }
  return $cached[$name];
}

/**
 * Returns an array of workflow transitions.
 *
 * @param string $name
 *   A workflow name to return the workflow transitions for.
 *
 * @return array
 *   An associative array of transitions and roles ordered by workflow
 *   name, source state, and finally target state to provide a consistent code
 *   output.
 */
function _og_workflow_get_transitions($name) {
  $cached = &drupal_static(__FUNCTION__, array());

  if (empty($cached[$name])) {
    $query = db_select('og_workflow_transitions', 'owt');
    $query->join('workflow_states', 'wss', 'wss.sid = owt.sid');
    $query->join('workflow_ states', 'wst', 'wst.sid = owt.target_sid');
    $query->join('workflows', 'w', 'wss.wid = w.wid');
    $query->addField('wst', 'state', 'target_state');
    $query->fields('owt', array('roles'))
      ->fields('wss', array('state'))
      ->condition('w.name', $name)
      ->orderBy('wss.state')
      ->orderBy('wst.state');
    $result = $query->execute()->fetchAll();

    $rolemap = array();
    $transitions = array();
    foreach ($result as $transition) {
      unset($transition->wid);
      $roles = explode(',', $transition->roles);
      $transition->roles = array();
      if (!empty($roles)) {
        foreach ($roles as $role) {
          if ($role == 'author') {
            $transition->roles[OG_WORKFLOW_FEATURES_AUTHOR] = OG_WORKFLOW_FEATURES_AUTHOR;
          }
          elseif (!empty($role)) {
            if (!isset($rolemap[$role])) {
              $query = db_select('og_role', 'ogr')
                ->fields('ogr', array('group_type', 'group_bundle', 'name'))
                ->condition('ogr.gid', 0)
                ->condition('ogr.rid', $role);
              $og_role = $query->execute()->fetchObject();
              $key = implode(":", array($og_role->group_type, $og_role->group_bundle, $og_role->name));
              $rolemap[$role] = $key;
            }
            $transition->roles[$rolemap[$role]] = $rolemap[$role];
          }
        }
      }
      $key = $transition->state . '->' . $transition->target_state;
      $transitions[$key] = $transition;
    }
    $cached[$name] = $transitions;
  }
  return $cached[$name];
}

/**
 * Given a workflow this brings the og_workflow_transitions consistent.
 *
 * @param array $workflow
 *   An associative array containing the workflow name and code definition.
 */
function _og_workflow_update_transitions($workflow) {
  $cached = &drupal_static(__FUNCTION__, array());
  if ($dbworkflow = workflow_get_workflows_by_name($workflow['name'])) {
    foreach ($workflow['transitions'] as $transition) {
    
      // Lookup state ids from name.
      foreach (array('state', 'target_state') as $state) {
        if (empty($cached['sidmap'][$transition[$state]])) {
          $squery = db_select('workflow_states', 'ws')
            ->condition('ws.wid', $dbworkflow->wid)
            ->condition('ws.state', $transition[$state])
            ->fields('ws', array('sid'));
          $result = $squery->execute()->fetchAll();
          if (count($result) != 1) {
            drupal_set_message("Could not restore workflow '" . $workflow['name'] . "' because "
                              ."there is invalid or missing data in the workflow table.");
            continue; // Try to restore remaining data.
          }
          $cached['sidmap'][$transition[$state]] = $result[0]->sid;
        }
      }

      // Lookup role ids from name.
      $mapped_roles = array();
      foreach ($transition['roles'] as $role_string) {
        if ($role_string == OG_WORKFLOW_FEATURES_AUTHOR) {
          $mapped_roles['author'] = 'author';
        }
        else {
          if (empty($cached['ridmap'][$role_string])) {
            list($entity, $bundle, $role) = explode(':', $role_string);
            $query = db_select('og_role', 'ogr');
            $query->condition('ogr.gid', 0)
              ->condition('group_type', $entity)
              ->condition('group_bundle', $bundle)
              ->condition('name', $role)
              ->fields('ogr', array('rid'));
            $result = $query->execute()->fetchAll();
            if (count($result) != 1) {
              drupal_set_message("Could not restore workflow '" . $workflow['name'] . "' because "
                                ."there is invalid or missing data in the or_role table.");
              continue; // Try to restore remaining data.
            }
            $cached['ridmap'][$role_string] = current($result)->rid;
          }
          $mapped_roles[$cached['ridmap'][$role_string]] = $cached['ridmap'][$role_string];
        }
      }

      // Update the corresponding transition.
      $role_field = implode(',', $mapped_roles);
      db_merge('og_workflow_transitions')
        ->key(array('sid' => $cached['sidmap'][$transition['state']],
                    'target_sid' => $cached['sidmap'][$transition['target_state']]))
        ->fields(array('roles' => $role_field))
        ->execute();
    }
  }
}
