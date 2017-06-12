<?php
/**
 * @file
 * angularjs_map.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function angularjs_map_views_default_views() {
  $export = array();

  $view = new view();
  $view->name = 'earthquakes';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Earthquakes';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'time';
  $handler->display->display_options['cache']['results_lifespan'] = '3600';
  $handler->display->display_options['cache']['results_lifespan_custom'] = '0';
  $handler->display->display_options['cache']['output_lifespan'] = '3600';
  $handler->display->display_options['cache']['output_lifespan_custom'] = '0';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'views_geojson';
  $handler->display->display_options['style_options']['data_source']['value'] = 'geofield';
  $handler->display->display_options['style_options']['data_source']['latitude'] = 'title';
  $handler->display->display_options['style_options']['data_source']['longitude'] = 'title';
  $handler->display->display_options['style_options']['data_source']['bbox_top'] = 'title';
  $handler->display->display_options['style_options']['data_source']['bbox_right'] = 'title';
  $handler->display->display_options['style_options']['data_source']['bbox_bottom'] = 'title';
  $handler->display->display_options['style_options']['data_source']['bbox_left'] = 'title';
  $handler->display->display_options['style_options']['data_source']['name_field'] = 'title';
  $handler->display->display_options['style_options']['data_source']['description_field'] = '';
  $handler->display->display_options['style_options']['jsonp_prefix'] = '';
  $handler->display->display_options['style_options']['using_views_api_mode'] = 0;
  /* Field: Content: Title */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['label'] = '';
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = FALSE;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = FALSE;
  /* Field: Content: Location */
  $handler->display->display_options['fields']['field_location']['id'] = 'field_location';
  $handler->display->display_options['fields']['field_location']['table'] = 'field_data_field_location';
  $handler->display->display_options['fields']['field_location']['field'] = 'field_location';
  $handler->display->display_options['fields']['field_location']['label'] = '';
  $handler->display->display_options['fields']['field_location']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_location']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_location']['click_sort_column'] = 'wkt';
  $handler->display->display_options['fields']['field_location']['type'] = 'geofield_geojson';
  $handler->display->display_options['fields']['field_location']['settings'] = array(
    'data' => 'full',
  );
  /* Field: Content: CDI */
  $handler->display->display_options['fields']['field_cdi']['id'] = 'field_cdi';
  $handler->display->display_options['fields']['field_cdi']['table'] = 'field_data_field_cdi';
  $handler->display->display_options['fields']['field_cdi']['field'] = 'field_cdi';
  $handler->display->display_options['fields']['field_cdi']['label'] = '';
  $handler->display->display_options['fields']['field_cdi']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_cdi']['settings'] = array(
    'thousand_separator' => '',
    'decimal_separator' => '.',
    'scale' => '2',
    'prefix_suffix' => 1,
  );
  /* Field: Content: Felt */
  $handler->display->display_options['fields']['field_felt']['id'] = 'field_felt';
  $handler->display->display_options['fields']['field_felt']['table'] = 'field_data_field_felt';
  $handler->display->display_options['fields']['field_felt']['field'] = 'field_felt';
  $handler->display->display_options['fields']['field_felt']['label'] = '';
  $handler->display->display_options['fields']['field_felt']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_felt']['settings'] = array(
    'thousand_separator' => '',
    'prefix_suffix' => 1,
  );
  /* Field: Content: Magnitude */
  $handler->display->display_options['fields']['field_magnitude']['id'] = 'field_magnitude';
  $handler->display->display_options['fields']['field_magnitude']['table'] = 'field_data_field_magnitude';
  $handler->display->display_options['fields']['field_magnitude']['field'] = 'field_magnitude';
  $handler->display->display_options['fields']['field_magnitude']['label'] = '';
  $handler->display->display_options['fields']['field_magnitude']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_magnitude']['settings'] = array(
    'thousand_separator' => '',
    'decimal_separator' => '.',
    'scale' => '2',
    'prefix_suffix' => 1,
  );
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
  /* Filter criterion: Content: Published */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 1;
  $handler->display->display_options['filters']['status']['group'] = 1;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Filter criterion: Content: Type */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'earthquake' => 'earthquake',
  );

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page_1');
  $handler->display->display_options['path'] = 'earthquakes';
  $export['earthquakes'] = $view;

  return $export;
}
