<?php

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;

/**
 * @file
 * Quiz hook implementations and helper functions (not API).
 */

/**
 * Implements hook_help().
 */
function quizz_help($path, $arg) {
  switch ($path) {
    case 'admin/help#quiz':
      return _filter_autop(t('The quiz module allows users to administer a quiz, as a sequence of questions, and track the answers given. It allows for the creation of questions (and their answers), and organizes these questions into a quiz. Its target audience includes educational institutions, online training programs, employers, and people who just want to add a fun activity for their visitors to their Drupal site.

The quiz module has a plethora of permission options. Unless you take care setting your permissions, the quiz module might not do everything you want it to do.

For more information about quiz, and resources on how to use quiz, see the <a href="http://drupal.org/project/quiz">Quiz project website</a>'));
    case 'quiz/add#quiz':
      return t('A collection of questions designed to create interactive tests');
  }
}

/**
 * Implements hook_views_api().
 */
function quizz_views_api() {
  return array('api' => '3.0', 'path' => drupal_get_path('module', 'quizz') . '/includes/views');
}

/**
 * Implements hook_views_default_views().
 */
function quizz_views_default_views() {
  $files = file_scan_directory(drupal_get_path('module', 'quizz') . '/includes/views/defaults', '/\.view\.inc/');
  $views = array();
  foreach (array_keys($files) as $path) {
    $view = require $path;
    $views[$view->name] = $view;
  }
  return $views;
}

/**
 * Implements hook_ctools_plugin_directory() to let the system know
 * where our task and task_handler plugins are.
 */
function quizz_ctools_plugin_directory($owner, $plugin_type) {
  if ($owner === 'page_manager') {
    return 'includes/plugins/page_manager/' . $plugin_type;
  }
  if ($owner === 'ctools' && $plugin_type === 'content_types') {
    return 'includes/plugins/content_types';
  }
}

/**
 * Implements hook_ctools_plugin_api().
 */
function quizz_ctools_plugin_api($module, $api) {
  if ($module === 'page_manager' && $api === 'pages_default') {
    return array('version' => 1);
  }
}

/**
 * Implements hook_entity_info().
 */
function quizz_entity_info() {
  return quizz()->getHookImplementation()->getHookEntityInfo()->execute();
}

/**
 * Implements hook_permission().
 */
function quizz_permission() {
  return quizz()->getHookImplementation()->getHookPermission()->execute();
}

/**
 * Implements hook_admin_paths().
 */
function quizz_admin_paths() {
  $items = array();

  if (variable_get('node_admin_theme', TRUE)) {
    $items['quiz/add'] = TRUE;
    $items['quiz/add/*'] = TRUE;
    $items['quiz/*/edit'] = TRUE;
    $items['quiz/*/edit/*'] = TRUE;
    $items['quiz/*/delete'] = TRUE;
    $items['quiz-question/add'] = TRUE;
    $items['quiz-question/add/*'] = TRUE;
    $items['quiz-question/*/edit'] = TRUE;
    $items['quiz-question/*/delete'] = TRUE;
  }

  return $items;
}

/**
 * Implements hook_action_info().
 */
function quizz_action_info() {
  $actions = array();

  $actions['quizz_add_qestion_to_quiz_action'] = array(
      'type'             => 'quiz_question_entity',
      'label'            => t('Add questions to @quiz', array('@quiz' => QUIZZ_NAME)),
      'configurable'     => FALSE,
      'vbo_configurable' => FALSE,
  );

  return $actions;
}

/**
 * Implements hook_cron().
 *
 * Remove old quiz results that haven't been finished.
 */
function quizz_cron() {
  foreach (quizz_get_types() as $name => $quiz_type) {
    if ($rm_time = $quiz_type->getConfig('quiz_remove_partial_quiz_record', 0)) { // time = 0 for never.
      $sql = 'DELETE result FROM {quiz_results} result';
      $sql .= ' INNER JOIN {quiz_entity} quiz ON result.quiz_qid = quiz.qid';
      $sql .= ' WHERE (result.time_end = 0) AND (result.time_start < :time) AND (quiz.type = :type)';
      db_query($sql, array(':type' => $name, ':time' => time() - $rm_time));
    }
  }
}

/**
 * Implements hook_menu().
 */
function quizz_menu() {
  return quizz()->getHookImplementation()->getHookMenu()->execute();
}

/**
 * Implements hook_menu_alter()
 */
function quizz_menu_alter(&$items) {
  $items['admin/quizz/types/manage/%quizz_type/result/fields']['title'] = 'Result fields';
  $items['admin/quizz/types/manage/%quizz_type/result/fields']['weight'] = 3;
  $items['admin/quizz/types/manage/%quizz_type/result/display']['title'] = 'Result display';
  $items['admin/quizz/types/manage/%quizz_type/result/display']['weight'] = 4;

  $items['admin/quizz/question-types/manage/%quizz_question_type/answer/fields']['title'] = 'Answer fields';
  $items['admin/quizz/question-types/manage/%quizz_question_type/answer/fields']['weight'] = 5;
  $items['admin/quizz/question-types/manage/%quizz_question_type/answer/display']['title'] = 'Answer display';
  $items['admin/quizz/question-types/manage/%quizz_question_type/answer/display']['weight'] = 7;
}

/**
 * Implements hook_menu_local_tasks_alter().
 *
 * Add action link to 'quiz/add' on 'admin/content/quizz' page.
 */
function quizz_menu_local_tasks_alter(&$data, $router_item, $root_path) {
  switch ($root_path) {
    case 'admin/content/quizz':
      if (($item = menu_get_item('quiz/add')) && $item['access']) {
        $data['actions']['output'][] = array('#theme' => 'menu_local_action', '#link' => $item);
      }
      break;
  }
}

/**
 * Implements hook_theme().
 */
function quizz_theme($existing, $type, $theme, $path) {
  return array(
      'quizz_view_stats'               => array(
          'variables' => array('quiz' => NULL),
          'file'      => 'includes/quizz.pages.inc'
      ),
      'quizz_categorized_form'         => array(
          'render element' => 'form',
          'file'           => 'includes/quizz.pages.inc'
      ),
      'quizz_progress'                 => array(
          'variables' => array(
              'quiz'            => null,
              'question_number' => NULL,
              'num_questions'   => NULL,
              'allow_jumping'   => NULL,
              'time_limit'      => NULL),
          'file'      => 'includes/quizz.pages.inc',
      ),
      'quizz_no_feedback'              => array(
          'file'      => 'includes/quizz.pages.inc',
          'variables' => array(),
      ),
      'quizz_question_selection_table' => array(
          'file'           => 'includes/quizz.pages.inc',
          'render element' => 'form',
      ),
      'quizz_answer_result'            => array(
          'file'      => 'includes/quizz.pages.inc',
          'variables' => array(),
      ),
      'quizz_report_form'              => array(
          'render element' => 'form',
          'path'           => $path . '/theme',
          'template'       => 'quiz-report-form',
      ),
      'quiz_browser'                   => array(
          'render element' => 'form',
          'file'           => 'includes/quizz.pages.inc',
      ),
      'quiz_question_score'            => array(
          'variables' => array('score' => NULL, 'max_score' => NULL),
          'path'      => $path . '/theme',
          'template'  => 'quiz-question-score',
      ),
      'quizz_pager'                    => array(
          'variables' => array('quiz' => null, 'total' => 0, 'current' => 0, 'siblings' => 0),
      ),
  );
}

/**
 * Implements hook_user_cancel().
 */
function quizz_user_cancel($edit, $account, $method) {
  quizz()->getHookImplementation()->getHookUserCancel($account, $method)->execute();
}

/**
 * Implements hook_mail().
 */
function quizz_mail($key, &$message, $params) {
  global $user;

  switch ($key) {
    case 'notice':
      list($quiz, $score, $result_id, $target) = $params;
      list($subject, $body) = quizz()->getMailHelper()->buildNotice($user, $quiz, $score, $result_id, $target);
      $message['subject'] = $subject;
      $message['body'][] = $body;
      break;
  }
}

/**
 * Implements hook_quiz_scored().
 *
 * Performs actions like sending quiz results over email at the end of quiz.
 */
function quizz_quiz_scored($quiz, $score, $result_id) {
  return quizz()->getHookImplementation()->getHookQuizScored($quiz, $score, $result_id)->execute();
}

/**
 * Helper function to check if the user has any of a given list of permissions.
 *
 * @param args
 *   Any number of permissions.
 * @return
 *   TRUE if the user has access to any of the arguments given.
 */
function quizz_access_multi_or() {
  foreach (func_get_args() as $perm) {
    if (user_access($perm)) {
      return TRUE;
    }
  }
}

/**
 * Helper function to determine if a user has access to view a specific quiz result.
 *
 * @param Result|int $result
 * @return boolean
 *  True if access, false otherwise
 */
function quizz_access_my_result($result) {
  global $user;

  if (is_numeric($result) && !$result = quizz_result_load($result)) {
    throw new RuntimeException('Invalid result ID.');
  }

  return $result->canAccessOwnResult($user);
}

/**
 * Helper function to determine if a user has access to the different results
 * pages.
 *
 * @param QuizEntity $quiz
 * @param int $result_id
 * @return boolean
 */
function quizz_access_results($quiz, $result_id = NULL) {
  global $user;
  return quizz()->getQuizHelper()->getAccessHelper()->canAccessResults($user, $quiz, $result_id);
}

/**
 * Helper function to determine if a user has access to view his quiz results
 *
 * @param QuizEntity $quiz
 */
function quizz_access_my_results($quiz) {
  global $user;
  return quizz()->getQuizHelper()->getAccessHelper()->canAccessMyResults($quiz, $user);
}

/**
 * Does the current user have access to take the quiz?
 *
 * @param QuizEntity $quiz
 */
function quizz_take_access($quiz) {
  global $user;
  return quizz()->getQuizHelper()->getAccessHelper()->canTakeQuiz($quiz, $user);
}

/**
 * Menu check to see if user has access to take this question.
 */
function quizz_access_question($quiz, $page_number) {
  return quizz()->getQuizHelper()->getAccessHelper()->canAccessQuestion($quiz, $page_number);
}

/**
 * Access callback for quiz-type.
 *
 * @param string $op
 * @param string $type
 * @param stdClass $account
 */
function quizz_type_access($op, $type = NULL, $account = NULL) {
  return user_access('administer quiz configuration', $account);
}

/**
 * Access callback for quiz-entity.
 * @param string $op
 * @param QuizEntity $quiz
 * @param stdClass $account
 */
function quizz_entity_access_callback($op, $quiz = NULL, $account = NULL) {
  switch ($op) {
    case 'create':
      return user_access('create quiz content', $account);
    case 'update':
      return user_access('edit any quiz content', $account);
    case 'view':
      if ((NULL !== $quiz) && !$quiz->status) {
        return user_access('edit any quiz content', $account);
      }
      return user_access('access quiz', $account);
  }
  return TRUE;
}

/**
 * Validate that a node is of type quiz, and that the user has access to it.
 *
 * @param QuizEntity $quiz
 * @param string $op
 * @return bool
 */
function quizz_type_confirm($quiz, $op = NULL) {
  if (in_array($op, array('view', 'update', 'delete', 'create'))) {
    return entity_access($op, 'quiz_entity', $quiz);
  }
  return user_access(NULL === $op ? 'access quiz' : $op);
}

/**
 * Menu access check for question feedback.
 * @param QuizEntity $quiz
 * @param int $page_number
 * @return boolean
 */
function quizz_question_feedback_access(QuizEntity $quiz, $page_number) {
  if (($page_number <= 0) || !array_filter($quiz->review_options['question'])) {
    return FALSE;
  }

  $result_id = empty($_SESSION['quiz'][$quiz->qid]['result_id']) ? $_SESSION['quiz']['temp']['result_id'] : $_SESSION['quiz'][$quiz->qid]['result_id'];
  $result = quizz_result_load($result_id);
  $question_vid = $result->layout[$page_number]['vid'];

  return (bool) quizz_answer_controller()->loadByResultAndQuestion($result_id, $question_vid);
}

/**
 * Access callback for /quiz/%/revisions.
 * @param QuizEntity $quiz
 * @return boolean
 */
function quizz_revision_tab_access(QuizEntity $quiz) {
  if ($quiz->qid == $quiz->vid) {
    return FALSE;
  }
  return entity_access('update', 'quiz_entity', $quiz);
}

/**
 * Access callback for /quiz/%/results.
 * @param QuizEntity $quiz
 * @return boolean
 */
function quizz_results_tab_access(QuizEntity $quiz) {
  $sql = 'SELECT 1 FROM {quiz_results} WHERE quiz_qid = :id AND time_end IS NOT NULL';
  if (!db_query($sql, array(':id' => $quiz->qid))->fetchColumn()) {
    return FALSE;
  }
  return entity_access('update', 'quiz_entity', $quiz);
}

/**
 * Access callback for /quiz/%/my-results.
 * @param QuizEntity $quiz
 * @return boolean
 */
function quizz_my_results_tab_access(QuizEntity $quiz) {
  global $user;
  $sql = 'SELECT 1 FROM {quiz_results} WHERE quiz_qid = :id AND uid = :uid AND time_end IS NOT NULL';
  $con = array(':id' => $quiz->qid, ':uid' => $user->uid);
  if (!db_query($sql, $con)->fetchColumn()) {
    return FALSE;
  }
  return entity_access('update', 'quiz_entity', $quiz);
}
