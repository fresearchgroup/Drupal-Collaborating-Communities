<?php
/**
 * @file
 * This file contains no working PHP code; it exists to provide additional
 * documentation for doxygen as well as to document hooks in the standard
 * Drupal manner.
 */

/**
 * @defgroup anguarljs AngularJS module integrations.
 *
 * Module integrations with the angularjs module.
 */

/**
 * @defgroup angularjs_hooks AngularJS hooks
 * @{
 * Hooks that can be implemented by other modules in order to extend angularjs.
 */

/**
 * Provide a list of controllers exposed by this module
 *
 * @param string $version
 *          The version of AngularJS being used on the site
 *          
 * @return array An array containing AngularJS controllers in the format
 *         provided to drupal_add_js
 */
function hook_angularjs_controllers() {
  return array(
    drupal_get_path('angularjs') . '/js/controllers.js' => array() 
  );
}

/**
 * Provide a list of filters exposed by this module
 *
 * @param string $version
 *          The version of AngularJS being used on the site
 *          
 * @return array An array containing AngularJS filters in the format
 *         provided to drupal_add_js
 */
function hook_angularjs_filters() {
  return array(
    drupal_get_path('angularjs') . '/js/filters.js' => array() 
  );
}

/**
 * Provide a list of directives exposed by this module
 *
 * @param string $version
 *          The version of AngularJS being used on the site
 *          
 * @return array An array containing AngularJS directives in the format
 *         provided to drupal_add_js
 */
function hook_angularjs_directives() {
  return array(
    drupal_get_path('angularjs') . '/js/directives.js' => array() 
  );
}

/**
 * Provide a list of services exposed by this module
 *
 * @param string $version
 *          The version of AngularJS being used on the site
 *          
 * @return array An array containing AngularJS services in the format
 *         provided to drupal_add_js
 */
function hook_angularjs_services() {
  return array(
    drupal_get_path('angularjs') . '/js/services.js' => array() 
  );
}

/**
 * Alter the list of controllers exposed by any module
 *
 * @param array $controllers
 *          The list of controllers exposed by any module
 *          
 * @param string $version
 *          The version of AngularJS
 */
function hook_angularjs_controllers_alter(&$controllers, $version) {
  foreach($controllers as $key => &$value) {
    $value['scope'] = 'footer';
  }
}

/**
 * Alter the list of filters exposed by any module
 *
 * @param array $filters
 *          The list of filters exposed by any module
 *          
 * @param string $version
 *          The version of AngularJS
 */
function hook_angularjs_filters_alter(&$filters, $version) {
  foreach($filters as $key => &$value) {
    $value['scope'] = 'footer';
  }
}

/**
 * Alter the list of directives exposed by any module
 *
 * @param array $directives
 *          The list of directives exposed by any module
 *          
 * @param string $version
 *          The version of AngularJS
 */
function hook_angularjs_directives_alter(&$directives, $version) {
  foreach($directives as $key => &$value) {
    $value['scope'] = 'footer';
  }
}

/**
 * Alter the list of services exposed by any module
 *
 * @param array $services
 *          The list of services exposed by any module
 *          
 * @param string $version
 *          The version of AngularJS
 */
function hook_angularjs_services_alter(&$services, $version) {
  foreach($services as $key => &$value) {
    $value['scope'] = 'footer';
  }
}