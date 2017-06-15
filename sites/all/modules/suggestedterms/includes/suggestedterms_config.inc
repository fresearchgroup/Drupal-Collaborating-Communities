<?php
/**
 * @file
 * Configuration and admin for suggested terms
 */


/**
* define constants
*/
define('SUGGESTEDTERMS_SORT_POPULAR', 0);
define('SUGGESTEDTERMS_SORT_NAME', 1);
define('SUGGESTEDTERMS_SORT_RECENT', 2);
define('SUGGESTEDTERMS_SORT_WEIGHT', 3);
define('SUGGESTEDTERMS_SORT_USED', 4);

define('SUGGESTEDTERMS_DISPLAY_ALL', 1);
define('SUGGESTEDTERMS_DISPLAY_USED', 2);

define('SUGGESTEDTERMS_DISPLAY_FIELDSET', 0);

/**
 * Implementation of hook_help().
 */
function suggestedterms_help($path, $args) {
  $output = '';

  switch ($path) {
    case "admin/help#suggestedterms":
      $output = '<p>' . t("Replaces the default \'description\' text on free-tagging textfields on node edit pages with a list of taxonomy terms in that vocabulary, with appropriate labeling") . '</p>';
      break;
  }

  return $output;
}

/**
 * Implementation of hook_permission().
 */
function suggestedterms_permission() {
  return array(
    'administer suggested terms' => array(
      'title' => t('Administer suggested terms'),
      'description' => t('User can adjust settings such as how to sort the suggested terms.'),
    )
  );
}

/**
 * Implementation of hook_menu().
 */
function suggestedterms_menu() {

  $items = array();

  $items['admin/config/content/suggestedterms'] = array(
    'title' => 'Suggested terms',
    'description' => 'Provide integrated suggestions of recent or popular tags from the given vocabulary.',
    'page callback' => 'drupal_get_form',
    'page arguments' => array('suggestedterms_admin'),
    'access arguments' => array('administer suggested terms'),
    'type' => MENU_NORMAL_ITEM,
  );

  return $items;
}


/**
 * Administration page for suggested terms.
 */
function suggestedterms_admin() {

  $form['suggestedterms_maximum_displayed'] = array(
    '#type' => 'textfield',
    '#title' => t('Maximum number of links'),
    '#default_value' => variable_get('suggestedterms_maximum_displayed', 5),
    '#size' => 2,
    '#maxlength' => 2,
    '#description' => t('The maximum number of links to display in the block. Enter 0 to show all.'),
  );
  $form['suggestedterms_sort_order'] = array(
    '#type' => 'radios',
    '#title' => t('Link sort order'),
    '#default_value' => variable_get('suggestedterms_sort_order', SUGGESTEDTERMS_SORT_NAME),
    '#description' => t("The sort order for the links displayed."),
    '#options' => array(
      SUGGESTEDTERMS_SORT_POPULAR => t('Most used'),
      SUGGESTEDTERMS_SORT_NAME => t('Alphabetically'),
      SUGGESTEDTERMS_SORT_WEIGHT => t('Weight'),
      SUGGESTEDTERMS_SORT_RECENT => t('Most recently added'),
      SUGGESTEDTERMS_SORT_USED => t('Most recently used'),
     ),
  );
  $form['suggestedterms_display_mode'] = array(
    '#type' => 'radios',
    '#title' => t('Which terms to display'),
    '#default_value' => variable_get('suggestedterms_display_mode', SUGGESTEDTERMS_DISPLAY_ALL),
    '#description' => t("Whether to display all defined terms or only the ones previously used."),
    '#options' => array(
       SUGGESTEDTERMS_DISPLAY_USED => t('Previously-used terms'),
       SUGGESTEDTERMS_DISPLAY_ALL => t('All terms'),
    ),
  );
  $form['suggestedterms_display_fieldset'] = array(
    '#type' => 'checkbox',
    '#prefix' => '<h4>Display Options</h4>',
    '#title' => t('Display the terms in a collapsed fieldset'),
    '#default_value' => variable_get('suggestedterms_display_fieldset', SUGGESTEDTERMS_DISPLAY_FIELDSET),
    '#description' => t('The terms will be wrapped in a fieldset that is collapsed by default'),
  );
  $form['suggestedterms_cache_expiration'] = array(
    '#type' => 'textfield',
    '#title' => t('Cache expiration'),
    '#default_value' => variable_get('suggestedterms_cache_expiration', 600),
    '#size' => 3,
    '#maxlength' => 4,
    '#description' => t('The maximum age of the suggested terms list cache, in seconds. Defaults to 600 (ten minutes). Set to "0" for no explicit cache expiration.'),
  );

  $form['#submit'] = array('suggestedterms_cache_clear');

  return system_settings_form($form);
}

/**
 * Helper function that can be added to form submit.
 */
function suggestedterms_cache_clear() {
  cache_clear_all('suggestedterms_data', 'cache');
}