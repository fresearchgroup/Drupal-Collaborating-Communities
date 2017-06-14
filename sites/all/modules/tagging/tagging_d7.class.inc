<?php

class tagging_d7 extends tagging_d6 {

  private static $theme = null;

  public function hook_menu() {
    $items = parent::hook_menu();
    $items['admin/config/content/tagging'] = $items['admin/settings/tagging/settings'];
    unset($items['admin/settings/tagging/settings']);
    return $items;
  }

  public function hook_theme() {
    if (!tagging_d7::$theme) {
      $theme = parent::hook_theme();
      foreach ($theme as $key => $arr) {
        $arr['variables'] = $arr['arguments'];
        unset($arr['arguments']);
        $theme[$key] = $arr;
      }
      tagging_d7::$theme = $theme;
    }
    return tagging_d7::$theme;
  }

  public function hook_tagging_form_alter(&$form, $form_state, $form_id) {}
  
  public function hook_tagging_form_taxonomy_form_vocabulary_alter(&$form, $form_state) {}

  public function theme_tagging_widget($args) {
    return parent::theme_tagging_widget_button($args['element']);
  }

  public function theme_tagging_widget_button($args) {
    return parent::theme_tagging_widget_button($args['vid']);
  }

  public function theme_tagging_widget_wrapper($args) {
    return parent::theme_tagging_widget_wrapper($args['content']);
  }

  public function theme_tagging_tags_list($args) {
    return parent::theme_tagging_tags_list($args['tags'], $args['vid']);
  }

  public function theme_tagging_suggestions_list($args) {
    return parent::theme_tagging_suggestions_list($args['suggestions'], $args['vid']);
  }

  public function theme() {
    $args = func_get_args();
    return call_user_func_array('theme', $args);
  }

  public function  _tagging_widget_javascript($form_element = null, &$form_state = array()) {
    drupal_add_css(drupal_get_path('module', 'tagging') . '/css/tagging.css');
    drupal_add_js(drupal_get_path('module', 'tagging') . '/js/tagging.plugin.js');
    // WE init the D7 version instead
    drupal_add_js(drupal_get_path('module', 'tagging') . '/js/tagging.init.d7.js');
    return $form_element;
  }
}