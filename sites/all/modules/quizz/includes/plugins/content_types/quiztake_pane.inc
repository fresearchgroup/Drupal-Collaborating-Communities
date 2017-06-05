<?php

use Drupal\quizz\Controller\QuizTakeController;

/**
 * This plugin array is more or less self documenting
 */
$plugin = array(
    'title'            => t('@quiz take pane', array('@quiz' => QUIZZ_NAME)),
    'single'           => TRUE,
    'render callback'  => 'quizz_take_pane_content_type_render',
    'category'         => array(t('Quiz'), -9),
    'description'      => t('@quiz take pane description.', array('@quiz' => QUIZZ_NAME)),
    'required context' => new ctools_context_required(QUIZZ_NAME, 'quiz_entity'),
);

/**
 * Run-time rendering of the body of the block (content type)
 * See ctools_plugin_examples for more advanced info
 */
function quizz_take_pane_content_type_render($subtype, $conf, $panel_args, $context = NULL) {
  if (!empty($context) && empty($context->data)) {
    return;
  }
  $quiz = isset($context->data) ? clone($context->data) : NULL;
  $block = new stdClass();

  if (empty($quiz)) {
    $block->delta = 'placeholder';
    $block->title = t('Placeholder title.');
    $block->content = t('Placeholder content goes here.');
    return $block;
  }

  if (!quizz_take_access($quiz)) {
    return;
  }

  $block->module = 'node';
  $block->delta = $quiz->qid;
  if (isset($quiz->rendered_content)) {
    $block->content = $quiz->rendered_content;
  }
  else {
    module_load_include('pages.inc', 'quizz');
    $block->content = quizz_take_page($quiz);
  }
  return $block;
}
