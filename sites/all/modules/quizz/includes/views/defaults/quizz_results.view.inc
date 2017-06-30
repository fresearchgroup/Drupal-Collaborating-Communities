<?php

$view = new view();
$view->name = 'quizz_results';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'quiz_results';
$view->human_name = 'Quiz results';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Quiz results';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'perm';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = '10';
$handler->display->display_options['style_plugin'] = 'table';
/* No results behavior: Global: Text area */
$handler->display->display_options['empty']['area']['id'] = 'area';
$handler->display->display_options['empty']['area']['table'] = 'views';
$handler->display->display_options['empty']['area']['field'] = 'area';
$handler->display->display_options['empty']['area']['empty'] = TRUE;
$handler->display->display_options['empty']['area']['content'] = 'There are no attempts on this Quiz yet.';
$handler->display->display_options['empty']['area']['format'] = 'filtered_html';
/* Relationship: Quiz result: Uid */
$handler->display->display_options['relationships']['uid']['id'] = 'uid';
$handler->display->display_options['relationships']['uid']['table'] = 'quiz_results';
$handler->display->display_options['relationships']['uid']['field'] = 'uid';
/* Field: Bulk operations: Quiz result */
$handler->display->display_options['fields']['views_bulk_operations']['id'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['table'] = 'quiz_results';
$handler->display->display_options['fields']['views_bulk_operations']['field'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['display_type'] = '1';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['enable_select_all_pages'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['force_single'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['entity_load_capacity'] = '10';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_operations'] = array(
    'action::views_bulk_operations_delete_item'              => array(
        'selected'            => 1,
        'postpone_processing' => 0,
        'skip_confirmation'   => 0,
        'override_label'      => 0,
        'label'               => '',
    ),
    'action::views_bulk_operations_delete_revision'          => array(
        'selected'            => 0,
        'postpone_processing' => 0,
        'skip_confirmation'   => 0,
        'override_label'      => 0,
        'label'               => '',
    ),
    'action::views_bulk_operations_script_action'            => array(
        'selected'            => 0,
        'postpone_processing' => 0,
        'skip_confirmation'   => 0,
        'override_label'      => 0,
        'label'               => '',
    ),
    'action::views_bulk_operations_modify_action'            => array(
        'selected'            => 0,
        'postpone_processing' => 0,
        'skip_confirmation'   => 0,
        'override_label'      => 0,
        'label'               => '',
        'settings'            => array(
            'show_all_tokens' => 1,
            'display_values'  => array(
                '_all_' => '_all_',
            ),
        ),
    ),
    'action::views_bulk_operations_argument_selector_action' => array(
        'selected'          => 0,
        'skip_confirmation' => 0,
        'override_label'    => 0,
        'label'             => '',
        'settings'          => array(
            'url' => '',
        ),
    ),
    'action::system_send_email_action'                       => array(
        'selected'            => 0,
        'postpone_processing' => 0,
        'skip_confirmation'   => 0,
        'override_label'      => 0,
        'label'               => '',
    ),
);
/* Field: Quiz result: Quiz_vid */
$handler->display->display_options['fields']['quiz_vid']['id'] = 'quiz_vid';
$handler->display->display_options['fields']['quiz_vid']['table'] = 'quiz_results';
$handler->display->display_options['fields']['quiz_vid']['field'] = 'quiz_vid';
$handler->display->display_options['fields']['quiz_vid']['label'] = 'Revision #';
$handler->display->display_options['fields']['quiz_vid']['separator'] = '';
/* Field: User: Name */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'users';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['relationship'] = 'uid';
$handler->display->display_options['fields']['name']['label'] = 'Username';
$handler->display->display_options['fields']['name']['link_to_user'] = FALSE;
/* Field: Quiz result: Date started */
$handler->display->display_options['fields']['time_start']['id'] = 'time_start';
$handler->display->display_options['fields']['time_start']['table'] = 'quiz_results';
$handler->display->display_options['fields']['time_start']['field'] = 'time_start';
$handler->display->display_options['fields']['time_start']['date_format'] = 'short';
/* Field: Quiz result: Date finished */
$handler->display->display_options['fields']['time_end']['id'] = 'time_end';
$handler->display->display_options['fields']['time_end']['table'] = 'quiz_results';
$handler->display->display_options['fields']['time_end']['field'] = 'time_end';
$handler->display->display_options['fields']['time_end']['date_format'] = 'short';
/* Field: Quiz result: Score */
$handler->display->display_options['fields']['score']['id'] = 'score';
$handler->display->display_options['fields']['score']['table'] = 'quiz_results';
$handler->display->display_options['fields']['score']['field'] = 'score';
/* Field: Quiz result: Evaluated */
$handler->display->display_options['fields']['is_evaluated']['id'] = 'is_evaluated';
$handler->display->display_options['fields']['is_evaluated']['table'] = 'quiz_results';
$handler->display->display_options['fields']['is_evaluated']['field'] = 'is_evaluated';
$handler->display->display_options['fields']['is_evaluated']['not'] = 0;
/* Field: Quiz result: Label */
$handler->display->display_options['fields']['result_id']['id'] = 'result_id';
$handler->display->display_options['fields']['result_id']['table'] = 'quiz_results';
$handler->display->display_options['fields']['result_id']['field'] = 'result_id';
$handler->display->display_options['fields']['result_id']['exclude'] = TRUE;
/* Field: Global: Custom text */
$handler->display->display_options['fields']['nothing']['id'] = 'nothing';
$handler->display->display_options['fields']['nothing']['table'] = 'views';
$handler->display->display_options['fields']['nothing']['field'] = 'nothing';
$handler->display->display_options['fields']['nothing']['label'] = 'Operations';
$handler->display->display_options['fields']['nothing']['alter']['text'] = 'View';
$handler->display->display_options['fields']['nothing']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['nothing']['alter']['path'] = 'quiz-result/[result_id]';
/* Contextual filter: Quiz result: Quiz_qid */
$handler->display->display_options['arguments']['quiz_qid']['id'] = 'quiz_qid';
$handler->display->display_options['arguments']['quiz_qid']['table'] = 'quiz_results';
$handler->display->display_options['arguments']['quiz_qid']['field'] = 'quiz_qid';
$handler->display->display_options['arguments']['quiz_qid']['default_action'] = 'empty';
$handler->display->display_options['arguments']['quiz_qid']['default_argument_type'] = 'fixed';
$handler->display->display_options['arguments']['quiz_qid']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['quiz_qid']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['quiz_qid']['summary_options']['items_per_page'] = '25';

return $view;
