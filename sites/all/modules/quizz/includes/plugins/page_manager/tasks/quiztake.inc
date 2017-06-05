<?php

use Drupal\quizz\Entity\QuizEntity;

/**
 * Specialized implementation of hook_page_manager_task_tasks(). See api-task.html for
 * more information.
 */
function quizz_quiztake_page_manager_tasks() {
  return array(
      // This is a 'page' task and will fall under the page admin UI
      'task type'                => 'page',
      'title'                    => t('Take @quiz', array('@quiz' => QUIZZ_NAME)),
      'admin title'              => t('The take page for @quiz.', array('@quiz' => QUIZZ_NAME)),
      'admin description'        => t('When enabled, this overrides the default view at quiz/%quizz/take'),
      'admin path'               => 'quiz/%quizz/take',
      // Menu hooks so that we can alter the quiz/%quiz menu entry to point to us.
      'hook menu alter'          => 'quizz_quiztake_menu_alter',
      // This is task uses 'context' handlers and must implement these to give the
      // handler data it needs.
      'handler type'             => 'context',
      'get arguments'            => 'quizz_quiztake_get_arguments',
      'get context placeholders' => 'quizz_quiztake_get_contexts',
      // Allow this to be enabled or disabled:
      'disabled'                 => variable_get('quiz_quiztake_disabled', TRUE),
      'enable callback'          => 'quizz_quiztake_enable',
  );
}

/**
 * Callback defined by quizz_quiztake_page_manager_tasks().
 *
 * Alter menu item so that quiz/%quizz/take comes here.
 */
function quizz_quiztake_menu_alter(&$items, $task) {
  if (variable_get('quiz_quiztake_disabled', TRUE)) {
    return;
  }

  $callback = $items['quiz/%quizz/take']['page callback'];

  // Override the quiz edit handler for our purpose.
  if ($callback === 'quiz_take' || variable_get('page_manager_override_anyway', FALSE)) {
    $items['quiz/%quizz/take']['file path'] = $task['path'];
    $items['quiz/%quizz/take']['file'] = $task['file'];
    $items['quiz/%quizz/take']['page callback'] = 'quizz_quiztake';
  }
  elseif (!empty($GLOBALS['page_manager_enabling_quiz'])) {
    $msg = t('Page manager module is unable to enable Quiz take entity because some other module already has overridden with %callback.', array(
        '%callback' => $callback
    ));
    drupal_set_message($msg, 'warning');
  }
}

/**
 * Entry point for our overridden quiz.
 *
 * This function asks its assigned handlers who, if anyone, would like
 * to run with it. If no one does, it passes through to the main node draft page.
 */
function quizz_quiztake(QuizEntity $quiz) {
  // Load my task plugin
  $task = page_manager_get_task('quiztake');

  // Load the node into a context.
  ctools_include('context');
  ctools_include('context-task-handler');
  $contexts = ctools_context_handler_get_task_contexts($task, '', array($quiz));

  $output = ctools_context_handler_render($task, '', $contexts, array($quiz->qid));
  if ($output !== FALSE) {
    return $output;
  }

  $function = 'quizz_take_page';
  foreach (module_implements('page_manager_override') as $module) {
    $call = $module . '_page_manager_override';
    if (($rc = $call('quiz')) && function_exists($rc)) {
      $function = $rc;
      break;
    }
  }

  return $function($quiz);
}

/**
 * Callback to enable/disable the page from the UI.
 */
function quizz_quiztake_enable($cache, $status) {
  variable_set('quiz_quiztake_disabled', $status);

  // Set a global flag so that the menu routine knows it needs
  // to set a message if enabling cannot be done.
  if (!$status) {
    $GLOBALS['page_manager_enabling_quiz'] = TRUE;
  }
}

/**
 * Callback to get arguments provided by this task handler.
 *
 * Since this is the node view and there is no UI on the arguments, we
 * create dummy arguments that contain the needed data.
 */
function quizz_quiztake_get_arguments($task, $subtask_id) {
  return array(
      array(
          'keyword'    => 'quiz_entity',
          'identifier' => t('@quiz being taken', array('@quiz' => QUIZZ_NAME)),
          'id'         => 1,
          'name'       => 'entity_id:quiz_entity',
          'settings'   => array(),
      ),
  );
}

/**
 * Callback to get context placeholders provided by this handler.
 */
function quizz_quiztake_get_contexts($task, $subtask_id) {
  return ctools_context_get_placeholders_from_argument(
    quizz_quiztake_get_arguments($task, $subtask_id)
  );
}
