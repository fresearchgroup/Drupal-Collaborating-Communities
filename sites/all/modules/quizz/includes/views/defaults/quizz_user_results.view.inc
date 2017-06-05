<?php

$view = new view();
$view->name = 'quizz_user_results';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'quiz_results';
$view->human_name = 'Quiz user results';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'My results';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'perm';
$handler->display->display_options['access']['perm'] = 'view own quiz results';
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
$handler->display->display_options['arguments']['quiz_qid']['default_action'] = 'access denied';
$handler->display->display_options['arguments']['quiz_qid']['default_argument_type'] = 'fixed';
$handler->display->display_options['arguments']['quiz_qid']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['quiz_qid']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['quiz_qid']['summary_options']['items_per_page'] = '25';
/* Contextual filter: Quiz result: Uid */
$handler->display->display_options['arguments']['uid']['id'] = 'uid';
$handler->display->display_options['arguments']['uid']['table'] = 'quiz_results';
$handler->display->display_options['arguments']['uid']['field'] = 'uid';
$handler->display->display_options['arguments']['uid']['default_action'] = 'default';
$handler->display->display_options['arguments']['uid']['default_argument_type'] = 'current_user';
$handler->display->display_options['arguments']['uid']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['uid']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['uid']['summary_options']['items_per_page'] = '25';
$handler->display->display_options['arguments']['uid']['specify_validation'] = TRUE;
$handler->display->display_options['arguments']['uid']['validate']['type'] = 'user';
/* Filter criterion: Quiz result: Evaluated */
$handler->display->display_options['filters']['is_evaluated']['id'] = 'is_evaluated';
$handler->display->display_options['filters']['is_evaluated']['table'] = 'quiz_results';
$handler->display->display_options['filters']['is_evaluated']['field'] = 'is_evaluated';
$handler->display->display_options['filters']['is_evaluated']['value'] = '1';

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page_1');
$handler->display->display_options['defaults']['arguments'] = FALSE;
/* Contextual filter: Quiz result: Quiz_qid */
$handler->display->display_options['arguments']['quiz_qid']['id'] = 'quiz_qid';
$handler->display->display_options['arguments']['quiz_qid']['table'] = 'quiz_results';
$handler->display->display_options['arguments']['quiz_qid']['field'] = 'quiz_qid';
$handler->display->display_options['arguments']['quiz_qid']['default_argument_type'] = 'fixed';
$handler->display->display_options['arguments']['quiz_qid']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['quiz_qid']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['quiz_qid']['summary_options']['items_per_page'] = '25';
/* Contextual filter: Quiz result: Uid */
$handler->display->display_options['arguments']['uid']['id'] = 'uid';
$handler->display->display_options['arguments']['uid']['table'] = 'quiz_results';
$handler->display->display_options['arguments']['uid']['field'] = 'uid';
$handler->display->display_options['arguments']['uid']['default_action'] = 'default';
$handler->display->display_options['arguments']['uid']['default_argument_type'] = 'current_user';
$handler->display->display_options['arguments']['uid']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['uid']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['uid']['summary_options']['items_per_page'] = '25';
$handler->display->display_options['arguments']['uid']['specify_validation'] = TRUE;
$handler->display->display_options['arguments']['uid']['validate']['type'] = 'user';
$handler->display->display_options['path'] = 'user/%/quiz-results';
$handler->display->display_options['menu']['type'] = 'tab';
$handler->display->display_options['menu']['title'] = 'My results';
$handler->display->display_options['menu']['weight'] = '0';
$handler->display->display_options['menu']['context'] = 0;
$handler->display->display_options['menu']['context_only_inline'] = 0;

return $view;
