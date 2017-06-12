<?php
/**
 * AngularJS configuration form
 * 
 * @param array $form
 * @param array $form_state
 * @return array
 */
function angularjs_settings_form($form, &$form_state) {
  $form['angularjs_version'] = array(
    '#type' => 'select',
    '#title' => t('AngularJS version'),
    '#options' => angularjs_version_options(),
    '#default_value' => variable_get('angularjs_version', ANGULARJS_DEFAULT_VERSION),
    '#description' => t("Select which version of AngularJS that's used on the site"),
    '#ajax' => array(
      'callback' => 'angularjs_settings_files_callback',
      'wrapper' => 'angularjs-file-list',
      'method' => 'replace',
      'effect' => 'fade' 
    ) 
  );
  
  // Check to see if we're in AJAX and use the submitted value
  $version = (TRUE === isset($form_state['values'])) ? $form_state['values']['angularjs_version'] : variable_get('angularjs_version', ANGULARJS_DEFAULT_VERSION);
  
  // So we can get the correct list of files
  $files = angularjs_version_files($version);
  
  $file_list = array_combine($files, $files);
  
  $form['angularjs_files'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Files'),
    '#options' => $file_list,
    '#default_value' => variable_get('angularjs_files', $files),
    '#description' => t('Which files should be loaded as part of the library'),
    '#prefix' => '<div id="angularjs-file-list">',
    '#suffix' => '</div>' 
  );
  
  $form['angularjs_compression_type'] = array(
    '#type' => 'radios',
    '#title' => t('AngularJS compression level'),
    '#options' => array(
      'min' => t('Production (minified)'),
      '' => t('Development (uncompressed)') 
    ),
    '#default_value' => variable_get('angularjs_compression_type', 'min') 
  );
  
  $form['angularjs_cdn'] = array(
    '#type' => 'select',
    '#title' => t('AngularJS CDN'),
    '#options' => array(
      '0' => t('No'),
      '1' => t('Yes') 
    ),
    '#default_value' => variable_get('angularjs_cdn', '0'),
    '#description' => t('Whether to use the Google CDN') 
  );
  
  return system_settings_form($form);
}

/**
 * Returns a list of AngularJS versions
 * 
 * @return array
 */
function angularjs_version_options() {
  $versions = angularjs_version_files();
  $options = drupal_map_assoc(array_keys($versions));
  return $options;
}

/**
 * Form validator for AngularJS configuration form
 *
 * @param array $form          
 * @param array $form_state          
 */
function angularjs_settings_form_validate(&$form, &$form_state) {
  // Filter out unused files
  $form_state['values']['angularjs_files'] = array_filter(array_values($form_state['values']['angularjs_files']));
  
  cache_clear_all('angularjs_*', 'cache', TRUE);
}

/**
 * AJAX callback to change AngularJS version
 *
 * @param array $form          
 * @param array $form_state          
 * @return array
 */
function angularjs_settings_files_callback($form, $form_state) {
  return $form['angularjs_files'];
}
