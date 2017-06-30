<?php

# ---------------------------------------------------------------
# To be removed
# ---------------------------------------------------------------

/**
 * Implements hook_node_presave().
 */
function quiz_node_presave($node) {
  if (($node->type === 'quiz') && !empty($node->aid) && $aid = actions_function_lookup($node->aid)) {
    $node->aid = $aid; // What's AID?
  }
}

/**
 * Implements hook_node_access_records().
 */
function quiz_question_node_access_records($node) {
  $grants = array();

  // Restricting view access to question nodes outside quizzes.
  if (in_array($node->type, array_keys(quizz_question_get_handler_info()))) {
    // This grant is for users having 'view quiz question outside of a quiz'
    // permission. We set a priority of 2 because OG has a 1 priority and we
    // want to get around it.
    $grants[] = array(
        'realm'        => 'quiz_question',
        'gid'          => 1,
        'grant_view'   => 1,
        'grant_update' => 0,
        'grant_delete' => 0,
        'priority'     => 2,
    );
  }

  return $grants;
}

/**
 * Implements hook_view().
 */
function quiz_question_view($node, $view_mode) {
  if ($view_mode === 'search_index' && !variable_get('quiz_index_questions', 1)) {
    $node->body = '';
    $node->content = array();
    $node->title = '';
    $node->taxonomy = array();
    return $node;
  }

  if ($view_mode === 'teaser') {
    $node->content['question_teaser'] = array(
        '#prefix' => '<div class="question_type_name">',
        '#markup' => node_type_get_type($node)->name,
        '#suffix' => '</div>',
        '#weight' => -100,
    );
  }

  // normal node view
  if ($view_mode !== 'teaser') {
    $node->content += quiz_question_get_provider($node, TRUE)->view();
  }

  return $node;
}

/**
 * Implements hook_form_alter().
 */
function quiz_question_entity_form_alter(&$form) {
  // Remove revision fieldset if user don't have access to revise quiz manually.
  if (!user_access('manual quiz revisioning') || variable_get('quiz_auto_revisioning', 1)) {
    $e = $form['revision_information'];
    $e['revision']['#type'] = 'value';
    $e['revision']['#value'] = $e['revision']['#default_value'];
    $e['log']['#type'] = 'value';
    $e['log']['#value'] = $e['log']['#default_value'];
    $e['#access'] = FALSE;
  }
}
