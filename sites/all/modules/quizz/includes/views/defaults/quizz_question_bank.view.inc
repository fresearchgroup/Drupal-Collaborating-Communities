<?php

$view = new view();
$view->name = 'quizz_question_bank';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'quiz_question_entity';
$view->human_name = 'Question Bank';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['use_ajax'] = TRUE;
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'none';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
    'title' => 'title',
    'type'  => 'type',
);
$handler->display->display_options['style_options']['default'] = '-1';
$handler->display->display_options['style_options']['info'] = array(
    'title' => array(
        'sortable'           => 1,
        'default_sort_order' => 'asc',
        'align'              => '',
        'separator'          => '',
        'empty_column'       => 0,
    ),
    'type'  => array(
        'sortable'           => 1,
        'default_sort_order' => 'asc',
        'align'              => '',
        'separator'          => '',
        'empty_column'       => 0,
    ),
);
/* Relationship: Question: Uid */
$handler->display->display_options['relationships']['uid']['id'] = 'uid';
$handler->display->display_options['relationships']['uid']['table'] = 'quiz_question_entity';
$handler->display->display_options['relationships']['uid']['field'] = 'uid';
$handler->display->display_options['relationships']['uid']['label'] = 'Author';
/* Field: Question: Question ID */
$handler->display->display_options['fields']['qid']['id'] = 'qid';
$handler->display->display_options['fields']['qid']['table'] = 'quiz_question_entity';
$handler->display->display_options['fields']['qid']['field'] = 'qid';
$handler->display->display_options['fields']['qid']['exclude'] = TRUE;
$handler->display->display_options['fields']['qid']['separator'] = '';
/* Field: Bulk operations: Question */
$handler->display->display_options['fields']['views_bulk_operations']['id'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['table'] = 'quiz_question_entity';
$handler->display->display_options['fields']['views_bulk_operations']['field'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['display_type'] = '1';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['enable_select_all_pages'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['force_single'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['entity_load_capacity'] = '10';
$handler->display->display_options['fields']['views_bulk_operations']['vbo_settings']['skip_batching'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['vbo_operations'] = array(
    'action::quizz_add_qestion_to_quiz_action'               => array(
        'selected'            => 1,
        'postpone_processing' => 0,
        'skip_confirmation'   => 1,
        'override_label'      => 0,
        'label'               => '',
    ),
    'action::views_bulk_operations_delete_item'              => array(
        'selected'            => 0,
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
/* Field: Question: Label */
$handler->display->display_options['fields']['title']['id'] = 'title';
$handler->display->display_options['fields']['title']['table'] = 'quiz_question_entity';
$handler->display->display_options['fields']['title']['field'] = 'title';
$handler->display->display_options['fields']['title']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['title']['alter']['path'] = 'quiz-question/[qid]';
/* Field: Question: Type */
$handler->display->display_options['fields']['type']['id'] = 'type';
$handler->display->display_options['fields']['type']['table'] = 'quiz_question_entity';
$handler->display->display_options['fields']['type']['field'] = 'type';
/* Field: User: Name */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'users';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['relationship'] = 'uid';
$handler->display->display_options['fields']['name']['label'] = 'Author';
/* Field: Question: Changed */
$handler->display->display_options['fields']['changed']['id'] = 'changed';
$handler->display->display_options['fields']['changed']['table'] = 'quiz_question_entity';
$handler->display->display_options['fields']['changed']['field'] = 'changed';
$handler->display->display_options['fields']['changed']['date_format'] = 'short';
$handler->display->display_options['fields']['changed']['second_date_format'] = 'long';
/* Sort criterion: Question: Changed */
$handler->display->display_options['sorts']['changed']['id'] = 'changed';
$handler->display->display_options['sorts']['changed']['table'] = 'quiz_question_entity';
$handler->display->display_options['sorts']['changed']['field'] = 'changed';
$handler->display->display_options['sorts']['changed']['order'] = 'DESC';
$handler->display->display_options['sorts']['changed']['exposed'] = TRUE;
$handler->display->display_options['sorts']['changed']['expose']['label'] = 'Updated';
/* Filter criterion: Question: Type */
$handler->display->display_options['filters']['type']['id'] = 'type';
$handler->display->display_options['filters']['type']['table'] = 'quiz_question_entity';
$handler->display->display_options['filters']['type']['field'] = 'type';
$handler->display->display_options['filters']['type']['exposed'] = TRUE;
$handler->display->display_options['filters']['type']['expose']['operator_id'] = 'type_op';
$handler->display->display_options['filters']['type']['expose']['label'] = 'Type';
$handler->display->display_options['filters']['type']['expose']['operator'] = 'type_op';
$handler->display->display_options['filters']['type']['expose']['identifier'] = 'type';
$handler->display->display_options['filters']['type']['expose']['remember_roles'] = array(
    2 => '2',
    1 => 0,
    3 => 0,
);

return $view;
