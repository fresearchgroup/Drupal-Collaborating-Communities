<?php

/*
 * The form where you set the variables.
 */
function apachesolr_sort_page_form() {
  $query = apachesolr_drupal_query('apachesolr_sort');
  $sorts = $query->getAvailableSorts();
  $form['apachesolr_sort_enable'] = array(
    '#type' => 'fieldset',
    '#title' => t('enable/disable sort fields'),
    '#tree' => FALSE,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['apachesolr_sort_weight'] = array(
    '#type' => 'fieldset',
    '#title' => t('adapt the weight on sort fields'),
    '#tree' => FALSE,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  foreach ($sorts as $key => $sort)    {
    $variable = 'apachesolr_sort_sort_' . $key;
    $weight_variable = 'apachesolr_sort_sort_weight_' . $key;

    $form['apachesolr_sort_enable'][$variable] = array(
      '#type' => 'checkbox',
      '#title' => $sort['title'],
      '#default_value' => variable_get($variable, TRUE),
      '#description' => t('enable this sort. '),
    );
    $form['apachesolr_sort_weight'][$weight_variable] = array(
      '#type' => 'textfield',
      '#title' => t('The weight of ' . $sort['title']),
      '#default_value' => variable_get($weight_variable, 0),
      '#description' => t('Change the order of the facest by altering this weight. '),
      '#size' => 5,
    );
  }

  $form['apachesolr_sort_default'] = array(
    '#type' => 'fieldset',
    '#title' => t('Set the default sort value if no keywords are given'),
    '#description' => t('This sets the default sort value when no keyword is given. The sort when a keyword is given will still be relevancy'),
    '#tree' => FALSE,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  foreach ($sorts as $option => $values) {
    $options[$option] = $values['title'];
  }
  $form['apachesolr_sort_default']["sort_default_key"] = array(
    '#type' => 'select',
    '#title' => t('The default Sort for search pages'),
    '#options' => $options,
    '#default_value' => variable_get('sort_default_key', ''),
  );
  $form['apachesolr_sort_default']["sort_default_direction"] = array(
    '#type' => 'select',
    '#title' => t('The default Sort for search pages'),
    '#options' => array('asc' => t('Ascending'), 'desc' => t('Descending')),
    '#default_value' => variable_get('sort_default_direction', 'asc'),
  );

  return system_settings_form($form);
}


/*
 * The submit handler of the form that saves the input content into the DB.
 */
function apachesolr_sort_page_form_submit($form, &$form_state) {
  foreach ($form['#post'] as $key => $field) {
    if (is_array($field)) {
      $keys = array_keys($field);
      if (isset($field['apachesolr_sort_sort_' . $key])) {
          variable_set('apachesolr_sort_sort_' . $key, TRUE);
      }
      else {
        variable_set('apachesolr_sort_sort_' . $key, FALSE);
      }
      variable_set('apachesolr_sort_sort_weight_' . $key, $field['apachesolr_sort_sort_weight_' . $key]);
    }
  }
}

/**
 * Displays the sort form as a dropdown.
 */
function apachesolr_sort_sort_form_($form, &$form_state, SolrBaseQuery $query, array $sorts, array $solrsort) {
  $toggle = array('asc' => 'desc', 'desc' => 'asc');

  $form['apachesolr_sort_query'] = array(
    '#type' => 'value',
    '#value' => $query,
  );

  // Build sort options.
  $sort_options = array();
  foreach ($sorts as $name => $data) {
    $sort_options[$name] = check_plain($data['title']);
  }

  $form['apachesolr_sort_name'] = array(
    '#type' => 'select',
    '#title' => t('Field'),
    '#options' => $sort_options,
    '#default_value' => $solrsort['#name'],
  );

  $form['apachesolr_sort_direction'] = array(
    '#type' => 'select',
    '#title' => t('Direction'),
    '#options' => array(
      'asc' => t('Ascending'),
      'desc' => t('Descending'),
    ),
    '#default_value' => $solrsort['#direction'],
  );

  $form['actions'] = array(
    '#type' => 'actions',
    '#weight' => 20,
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Sort results'),
  );

  $form['#submit'][] = 'apachesolr_sort_sort_form_submit';

  return $form;
}

/**
 * Form submission handler for apachesolr_sort_apachesolr_sort_form().
 */
function apachesolr_sort_sort_form_submit($form, &$form_state) {
  $query = $form_state['values']['apachesolr_sort_query'];
  $name = $form_state['values']['apachesolr_sort_name'];
  $direction = $form_state['values']['apachesolr_sort_direction'];

  // Sets the Solr sort, gets query string parameters.
  $query->setSolrsort($name, $direction);
  $params = array_merge($_GET, $query->getSolrsortUrlQuery());
  $params = drupal_get_query_parameters($params, array('q', 'page'));

  // For sone reason query->getSolrsortUrlQuery() doesn't seem to handle score.
  if ('score' == $name) {
    unset($params['solrsort']);
  }
  // Redirect to a URL with the solrsort params included.
  $form_state['redirect'] = array(current_path(), array('query' => $params));
}
