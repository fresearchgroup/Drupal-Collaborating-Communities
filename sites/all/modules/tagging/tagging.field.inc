<?php
// Drupal 7 field implementation by iStryker
// Author: iStryker

/**
 * Implements hook_field_widget_info().
 */
function tagging_field_widget_info() {
  return array(
    'tagging_taxonomy_autocomplete' => array(
      'label' => t('Tagging module autocomplete javascript UI widget'),
      'field types' => array('taxonomy_term_reference'),
      'settings' => array(
        'size' => 40,
        'autocomplete_path' => 'taxonomy/autocomplete',
      ),
      'behaviors' => array(
        'multiple values' => FIELD_BEHAVIOR_CUSTOM,
      ),
    ),
  );
}


/**
 * Implements hook_field_widget_form().
 */
function tagging_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {

  $settings = $instance['widget']['settings'];

  // iStryker April 7th, 2011: maybe a problem in the future.  only 1 allowed value is allowed so far.
  // there is an option for parent, which I have no clue what it is for
  $machine_name = $field['settings']['allowed_values'][0]['vocabulary'];

  //query database to get the vocabulary id
  $vids = db_query('SELECT tv.vid FROM {taxonomy_vocabulary} tv WHERE tv.machine_name = :machine_name', array(':machine_name' => $machine_name));
  //I don't know if I need to foreach here, but it doesn't break anything.  Could allow multiple vocabularys in the future
  //Pulled from Example #1 of http://api.drupal.org/api/drupal/includes--database--database.inc/function/db_query/7
  foreach ($vids as $vid) {
    
    $tags = array();
    foreach ($items as $item) {
      $term = isset($item['taxonomy_term']) ? $item['taxonomy_term'] : taxonomy_term_load($item['tid']);
      $tags[] = $term->name;
    }
    sort($tags);

    // order (temporary comments)
    // Active Tags: term1(x), term2(x), etc
    // Textfield (autocomplete),  Add button
    // Suggestions

    $element['field_terms'] = array(
      '#markup' => theme('tagging_tags_list', array('tags' => $tags, 'vid' => $vid->vid)),
    );

    $element['input_field'] = array (
      '#type' => 'item',
      '#attributes' => array(
        'class' => array(
          'tagging-widget-input-wrapper',
          'clearfix',
        )
      ),
    );

    $element['input_field']['textfield'] = array(
      '#type' => 'textfield',
      '#default_value' => '',
      '#autocomplete_path' => $settings['autocomplete_path'] . '/' . $field['field_name'],
      '#size' => $settings['size'],
      '#maxlength' => 1024,
      '#attributes' => array(
        'class' => array(
          'tagging-widget-input',
          'tagging-widget-input-' . $vid->vid,
        ),
        'id' => 'tagging-widget-input-' . $vid->vid,
      ),
    );

    $element['input_field']['button'] = array(
      '#markup' => theme('tagging_widget_button', array('element' => $element, 'vid' => $vid->vid)),
    );

    $element['suggestions']['#markup'] = theme('tagging_suggestions_list', array('suggestions' => _tagging_get_suggestions($vid->vid, $form['nid']['#value']), 'vid' => $vid->vid));

    // Stores terms
    $element['input_field']['terms'] = array(
      '#type' => 'hidden',
      '#default_value' => taxonomy_implode_tags($tags),
      '#size' => $settings['size'],
      '#maxlength' => 1024,
      '#attributes' => array('class' => array('tagging-widget-target-' . $vid->vid)),
      '#theme_wrappers' => array(),
    );
  }
  $element['#after_build'][] = '_tagging_widget_javascript';
  $element += array(
    '#type' => 'item',
    '#attributes' => array('class' =>
                       'tagging-widget-input-wrapper',
                       'clearfix'
                     ),
    '#element_validate' => array('tagging_taxonomy_autocomplete_validate'),
  );


  return $element;
}


/**
 * Implements hook_field_widget_settings_form().
 */
function tagging_field_widget_settings_form($field, $instance) {
}

/**
 * Implements hook_field_widget_error().
 */
function tagging_field_widget_error($element, $error, $form, &$form_state) {
  form_error($element, $error['message']);
}

/**
 *
 * Called by form to validate terms
 * @param $element
 * @param $form_state
 */
function tagging_taxonomy_autocomplete_validate($element, &$form_state) {
	// combines the value(s) in the textfield and saved values
	if (!empty($element['input_field']['terms']['#value'])) {
		 if (!empty($element['input_field']['textfield']['#value'])) {
		   $element['#value'] =  $element['input_field']['textfield']['#value'] . ',' . $element['input_field']['terms']['#value'];
		 }
		 else {
	     $element['#value'] = $element['input_field']['terms']['#value'];
		 }

    taxonomy_autocomplete_validate($element, $form_state);
	}
}
