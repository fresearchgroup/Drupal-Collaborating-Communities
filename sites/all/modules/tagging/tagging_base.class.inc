<?php

class tagging_base {
  private static $instance = null;
  /**
   *
   * @return tagging_base
   */
  public static function instance() {
    if(!tagging_base::$instance) {
      $impl = '';
      if(defined('VERSION')) {
        if(strpos(VERSION, '6') === 0) {
          $impl = 'tagging_d6';
          require_once(drupal_get_path('module', 'tagging') . '/tagging_d6.class.inc');
        } else {
          $impl = 'tagging_d7';
          require_once(drupal_get_path('module', 'tagging') . '/tagging_d6.class.inc');
          require_once(drupal_get_path('module', 'tagging') . '/tagging_d7.class.inc');
        }
      }
      tagging_base::$instance = new $impl();
    }
    return tagging_base::$instance;
  }
  /**
   *
   * @return array
   */
  public function hook_menu() {
    return array();
  }
  /**
   *
   * @return array
   */
  public function hook_theme() {
    return array();
  }
}