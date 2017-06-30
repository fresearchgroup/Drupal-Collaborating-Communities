<?php

// Plugins are described by creating a $plugin array which will be used by the
// system that includes this file.
$plugin = array(
  'single' => TRUE,
  'title' => t('Search API page search box'),
  'icon' => 'icon_search.png',
  'description' => t('The search box that links to a specific search results page.'),
  'category' => t('Widgets'),
  'edit form' => 'search_api_page_search_box_content_type_edit_form',
  'render callback' => 'search_api_page_search_box_content_type_render',
  'admin title' => 'search_api_page_search_box_content_type_admin_title',
  'defaults' => array(
    'page' => '',
  ),
);

/**
 * Renders the custom content type.
 */
function search_api_page_search_box_content_type_render($subtype, $conf, $panel_args, $context) {
  $page = search_api_page_load($conf['page']);
  if ($page && $page->enabled && search_api_page_access($conf['page'])) {
    $block = new stdClass();
    $block->module  = 'search_api_page';
    $block->delta   = 'result';
    $block->subject = t($page->name);
    $block->content = drupal_get_form('search_api_page_search_form_' . $page->machine_name, $page, NULL, TRUE);
    $block->content['#contextual_links']['search_api_page'] = array(
      'admin/config/search/search_api/page',
      array($page->machine_name),
    );
    return $block;
  }
  return NULL;
}

/**
 * Returns an edit form for custom type settings.
 *
 * @see search_api_page_search_box_content_type_edit_form_submit()
 */
function search_api_page_search_box_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $pages = array();
  foreach (search_api_page_load_multiple(FALSE, array('enabled' => TRUE)) as $page => $info) {
    $pages[$info->machine_name] = $info->name;
  }

  $form['page'] = array(
    '#type' => 'select',
    '#title' => t('Search API Page'),
    '#options' => $pages,
    '#default_value' => $conf['page'],
    '#required' => TRUE,
  );

  return $form;
}

/**
 * Form submission handler for search_api_page_search_box_content_type_edit_form().
 */
function search_api_page_search_box_content_type_edit_form_submit($form, &$form_state) {
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

/**
 * Returns the administrative title for a type.
 */
function search_api_page_search_box_content_type_admin_title($subtype, $conf, $context) {
  $info = search_api_page_load($conf['page']);
  $page = isset($info->name) ? $info->name : t('Missing/broken type');
  return t('%page Search API page search box', array('%page' => $page));
}
