<?php

use Drupal\quizz_question\Entity\Question;

/**
 * Implements hook_help().
 */
function quizz_question_help($path, $args) {
  if ($path == 'admin/help#quizz_quesion') {
    return t('Support for Quiz question types.');
  }
}

/**
 * Implements hook_permission()
 */
function quizz_question_permission() {
  $perms = array();

  $perms['administer quiz question types'] = array(
      'title'           => t('Administer quiz question types'),
      'description'     => t('Have all permissions on all question types.'),
      'restrict access' => TRUE,
  );

  $perms['administer quiz questions'] = array(
      'title'           => t('Administer quiz questions'),
      'description'     => t('Have all permissions on all questions.'),
      'restrict access' => TRUE,
  );

  $perms['view any questions'] = array(
      'title' => t('View any questions'),
  );

  foreach (quizz_question_get_types() as $name => $info) {
    $perms += array(
        "create $name question"     => array(
            'title' => t('%type_name: Create new question', array('%type_name' => $info->label)),
        ),
        "edit own $name question"   => array(
            'title' => t('%type_name: Edit own question', array('%type_name' => $info->label)),
        ),
        "edit any $name question"   => array(
            'title' => t('%type_name: Edit any question', array('%type_name' => $info->label)),
        ),
        "delete own $name question" => array(
            'title' => t('%type_name: Delete own question', array('%type_name' => $info->label)),
        ),
        "delete any $name question" => array(
            'title' => t('%type_name: Delete any question', array('%type_name' => $info->label)),
        ),
    );
  }

  return $perms;
}

/**
 * Implements hook_theme().
 */
function quizz_question_theme() {
  $items['quizz_question_feedback'] = array(
      'variables' => NULL,
      'pattern'   => 'quizz_question_feedback__',
  );
  return $items;
}

/**
 * Implements hook_menu_local_tasks_alter().
 *
 * Add action link to 'quiz-question/add' on 'admin/content/quizz-questions' page.
 */
function quizz_question_menu_local_tasks_alter(&$data, $router_item, $root_path) {
  switch ($root_path) {
    case 'admin/content/quizz-questions':
      if (($item = menu_get_item('quiz-question/add')) && $item['access']) {
        $data['actions']['output'][] = array('#theme' => 'menu_local_action', '#link' => $item);
      }
      break;
    case 'quiz-question/%':
      if (($item = menu_get_item('admin/content/quizz-questions')) && $item['access']) {
        $item['title'] = t('Manage questions');
        $data['actions']['output'][] = array('#theme' => 'menu_local_action', '#link' => $item);
      }
      break;
  }
}

/**
 * Implements hook_entity_info()
 */
function quizz_question_entity_info() {
  $info = array();

  // User may come from old version, where the table is not available yet
  if (!db_table_exists('quiz_question_type')) {
    return $info;
  }

  $info['quiz_question_type'] = array(
      'label'            => t('Question type'),
      'plural label'     => t('Question types'),
      'description'      => t('Types of question entity.'),
      'entity class'     => 'Drupal\quizz_question\Entity\QuestionType',
      'controller class' => 'Drupal\quizz_question\Entity\QuestionTypeController',
      'base table'       => 'quiz_question_type',
      'fieldable'        => FALSE,
      'bundle of'        => 'quiz_question_entity',
      'exportable'       => TRUE,
      'entity keys'      => array('id' => 'id', 'name' => 'type', 'label' => 'label'),
      'access callback'  => 'quizz_question_type_access',
      'module'           => 'quizz_question',
      'admin ui'         => array(
          'path'             => 'admin/quizz/question-types',
          'file'             => 'quizz_question.pages.inc',
          'controller class' => 'Drupal\quizz_question\Entity\QuestionTypeUIController',
      ),
  );

  $info['quiz_question_entity'] = array(
      'label'                         => t('Question'),
      'plural label'                  => t('Questions'),
      'description'                   => t('!quiz question entity', array('!quiz' => QUIZZ_NAME)),
      'entity class'                  => 'Drupal\quizz_question\Entity\Question',
      'controller class'              => 'Drupal\quizz_question\Entity\QuestionController',
      'metadata controller class'     => 'Drupal\quizz_question\Entity\QuestionMetadataController',
      'extra fields controller class' => 'Drupal\quizz_question\Entity\QuestionExtraFieldsController',
      'views controller class'        => 'Drupal\quizz_question\Entity\QuestionViewsController',
      'base table'                    => 'quiz_question_entity',
      'revision table'                => 'quiz_question_revision',
      'fieldable'                     => TRUE,
      'entity keys'                   => array('id' => 'qid', 'bundle' => 'type', 'revision' => 'vid', 'label' => 'title'),
      'bundle keys'                   => array('bundle' => 'type'),
      'access callback'               => 'quizz_question_access_callback',
      'label callback'                => 'entity_class_label',
      'uri callback'                  => 'entity_class_uri',
      'module'                        => 'quizz_question',
      'bundles'                       => array(),
      'view modes'                    => array(
          'teaser' => array('label' => t('Teaser'), 'custom settings' => TRUE),
          'full'   => array('label' => t('Full'), 'custom settings' => TRUE),
      ),
      'admin ui'                      => array(
          'path'             => 'admin/content/quizz-questions',
          'file'             => 'quizz_question.pages.inc',
          'controller class' => 'Drupal\quizz_question\Entity\QuestionUIController',
      ),
  );

  // Add bundle info but bypass entity_load() as we cannot use it here.
  $rows = db_select('quiz_question_type', 'qt')->fields('qt')->execute()->fetchAllAssoc('type');
  foreach ($rows as $name => $row) {
    $info['quiz_question_entity']['bundles'][$name] = array(
        'label' => $row->label,
        'admin' => array(
            'path'             => 'admin/quizz/question-types/manage/%quizz_question_type',
            'real path'        => 'admin/quizz/question-types/manage/' . $name,
            'bundle argument'  => 4,
            'access arguments' => array('administer quiz'),
        ),
    );
  }

  return $info;
}

/**
 * Implements hook_quizz_question_info().
 */
function quizz_question_quizz_question_info() {
  $info['quiz_directions'] = array(
      'name'              => t('@quiz directions', array('@quiz' => QUIZZ_NAME)),
      'description'       => t('@quiz directions allow you to drop in directions anywhere in a quiz. You can also use them to insert a confirmation step at the end of the quiz.', array('@quiz' => QUIZZ_NAME)),
      'question provider' => 'Drupal\quizz_question\Handler\Direction\DirectionsQuestionHandler',
      'response provider' => 'Drupal\quizz_question\Handler\Direction\DirectionsResponseHandler',
      'module'            => 'quizz_question',
  );

  $info['quiz_page'] = array(
      'name'              => t('@quiz page', array('@quiz' => QUIZZ_NAME)),
      'description'       => t('Quiz pages allow you display questions across multiple pages.'),
      'question provider' => 'Drupal\quizz_question\Handler\Page\PageQuestionHandler',
      'response provider' => 'Drupal\quizz_question\Handler\Page\PageResonseHandler',
      'module'            => 'quizz_question',
  );

  return $info;
}

/**
 * Implements hook_get_report().
 */
function quizz_question_get_report($question_id, $question_vid, $result_id) {
  $question = quizz_question_load(NULL, $question_vid);
  if (!$handler = $question->getResponseHandler($result_id)) {
    drupal_set_message(t('Unable to load question with qid %qid and vid %vid', array(
        '%qid' => $question_id,
        '%vid' => $question_vid
      )), 'error');
    return FALSE;
  }

  $report = $handler->getReport();
  $handler->question->answers[$report['answer_id']] = $report;
  $handler->question->correct = $report['is_correct'];
  return $handler->question;
}

/**
 * Implements hook_modules_enable().
 *
 * Create default question bundle base on new question handlers enabled.
 *
 * @param string[] $modules
 */
function quizz_question_modules_enabled($modules) {
  foreach ($modules as $module) {
    if (!$info = module_invoke($module, 'quizz_question_info')) {
      continue;
    }

    foreach ($info as $handler_name => $handler_info) {
      // Do not create if already have one
      $sql = 'SELECT 1 FROM {quiz_question_type} WHERE handler = :handler';
      if (db_query($sql, array(':handler' => $handler_name))->fetchColumn()) {
        continue;
      }

      entity_create('quiz_question_type', array(
          'type'        => 'default_' . $handler_name,
          'label'       => $handler_info['name'],
          'status'      => 0,
          'handler'     => $handler_name,
          'description' => isset($handler_info['description']) ? $handler_info['description'] : '',
      ))->save();
    }
  }
}

/**
 * Implements hook_modules_disabled().
 *
 * @param string[] $modules
 */
function quizz_question_modules_disabled($modules) {
  $handler_names = array();

  foreach ($modules as $module) {
    if ($info = module_invoke($module, 'quizz_question_info')) {
      $handler_names[] = $module;
    }
  }

  if (!empty($handler_names)) {
    db_update('quiz_question_type')
      ->fields(array('disabled' => 1))
      ->condition('handler', $handler_names)
      ->execute()
    ;
  }
}

/**
 * Access callback for question type entity.
 * @return boolean
 */
function quizz_question_type_access($op, $question_type = NULL, $account = NULL) {
  return user_access('administer quiz question types');
}

/**
 * Access callback for question entity.
 *
 * @param string $op
 * @param Question|null $question
 * @param stdClass $account
 * @return bool
 */
function quizz_question_access_callback($op, $question = NULL, $account = NULL) {
  switch ($op) {
    case 'update':
      if (user_access('edit any question content', $account)) {
        return TRUE;
      }

      if ($question) {
        return user_access('edit any ' . $question->type . ' question', $account);
      }

      return FALSE;

    case 'view':
      return user_access('view quiz question outside of a quiz', $account) || user_access('view any questions', $account);

    case 'delete':
      if (user_access('delete any question content', $account)) {
        return TRUE;
      }

      if ($question) {
        return user_access('delete any ' . $question->type . ' question', $account);
      }

      return FALSE;
  }

  return TRUE;
}
