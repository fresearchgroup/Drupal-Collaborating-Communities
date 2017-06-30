<?php

/**
 * Implements hook_views_plugins().
 */
function quizz_views_plugins() {
  return array(
      'argument default' => array(
          'quiz_entity' => array(
              'title'   => t('Quiz ID from URL'),
              'handler' => 'Drupal\quizz\Views\Plugin\Argument\DefaultQuiz',
          ),
      ),
  );
}

/**
 * Implements hook_views_handlers().
 *
 * @todo use hook_views_data_alter to assign these field/filter handlers to the
 * automatically generated Entity API views definitions.
 */
function quizz_views_handlers() {
  $handlers = array();
  $handlers['quiz_views_handler_field_takes']['parent'] = 'views_handler_field';
  $handlers['quiz_views_handler_field_quiz_link_take']['parent'] = 'views_handler_field_node_link';
  $handlers['quiz_views_handler_field_time']['parent'] = 'views_handler_field';
  $handlers['quiz_views_handler_field_question_status']['parent'] = 'views_handler_field';
  $handlers['quiz_views_handler_field_user_quiz_state']['parent'] = 'views_handler_field';
  $handlers['quiz_views_handler_field_score_aggregate']['parent'] = 'views_handler_field_numeric';
  $handlers['quiz_views_handler_field_number_questions']['parent'] = 'views_handler_field';
  $handlers['quiz_views_handler_filter_question_status']['parent'] = 'views_handler_filter_in_operator';
  $handlers['quiz_views_handler_filter_quiz_qid']['parent'] = 'views_handler_filter';
  $handlers['quiz_views_handler_filter_user_quiz_state']['parent'] = 'views_handler_filter';
  $handlers['quiz_views_handler_filter_user_nullable']['parent'] = 'views_handler_filter';
  $handlers['quiz_views_handler_relationship_vid_from_qid']['parent'] = 'views_handler_relationship';
  $handlers['quiz_views_handler_argument_quiz_qid']['parent'] = 'views_handler_argument_numeric';
  $handlers['quiz_views_handler_argument_user_uid_nullable']['parent'] = 'views_handler_argument_user_uid';
  $handlers['quiz_views_handler_filter_quiz_question_type']['parent'] = 'views_handler_filter_node_type';
  $handlers['quiz_views_handler_filter_quiz_question']['parent'] = 'views_handler_filter_boolean_operator';
  return array(
      'info'     => array('path' => dirname(__FILE__) . '/handlers'),
      'handlers' => $handlers,
  );
}

/**
 * Implements hook_views_data_alter().
 */
function quizz_views_data_alter(&$data) {
  $data['quiz_entity_revision']['table']['join'] = array(
      'quiz_entity' => array('left_field' => 'vid', 'field' => 'vid', 'type' => 'INNER'),
  );

  $data['quiz_results']['table']['join'] = array(
      'quiz_entity'          => array('left_field' => 'vid', 'field' => 'quiz_vid'),
      'quiz_entity_revision' => array('left_field' => 'vid', 'field' => 'quiz_vid'),
  );

  $data['quiz_relationship']['table']['join'] = array(
      'quiz_entity'          => array('left_field' => 'vid', 'field' => 'quiz_vid', 'type' => 'INNER'),
      'quiz_entity_revision' => array('left_field' => 'vid', 'field' => 'quiz_vid', 'type' => 'INNER'),
  );

  if (module_exists('date_views')) {
    $data['quiz_question']['created']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_question']['changed']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_question_entity']['created']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_question_entity']['changed']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_results']['time_start']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_results']['time_start']['filter']['handler'] = 'date_views_filter_handler_simple';
    $data['quiz_results']['time_end']['filter']['handler'] = 'date_views_filter_handler_simple';
  }
}
