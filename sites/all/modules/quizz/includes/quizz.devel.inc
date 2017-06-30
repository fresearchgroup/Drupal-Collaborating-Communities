<?php

use Drupal\quizz\Generator\Generator;

function quizz_generate_form($form, $form_state) {
  foreach (quizz_get_types() as $name => $quiz_type) {
    $quiz_options[$name] = $quiz_type->label;
  }

  $form['quiz_types'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Quiz types'),
      '#options'       => $quiz_options,
      '#default_value' => array('quiz'),
  );

  $question_options = array();
  foreach (quizz_question_get_types() as $name => $question_type) {
    $question_options[$name] = $question_type->label;
  }

  $form['quiz_question_types'] = array(
      '#type'     => 'checkboxes',
      '#title'    => t('Question types'),
      '#options'  => $question_options,
      '#required' => TRUE,
  );

  $form['quiz_limit'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Maximum number of quizzes per type.'),
      '#default_value' => 10,
      '#size'          => 10,
      '#required'      => TRUE,
  );

  $form['quiz_question_limit'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Maximum number of questions per quiz.'),
      '#default_value' => 30,
      '#size'          => 10,
  );

  $form['quiz_result_limit'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Maximum number of results per quiz.'),
      '#default_value' => 3,
      '#size'          => 10,
  );

  $form['submit'] = array(
      '#type'  => 'submit',
      '#value' => t('Generate')
  );

  return $form;
}

function quizz_generate_form_submit($form, &$form_state) {
  $quiz_types = array_filter($form_state['values']['quiz_types']);
  $quiz_limit = $form_state['values']['quiz_limit'];
  $question_types = array_filter($form_state['values']['quiz_question_types']);
  $question_limit = $form_state['values']['quiz_question_limit'];
  $result_limit = $form_state['values']['quiz_result_limit'];
  $generator = new Generator($quiz_types, $question_types, $quiz_limit, $question_limit, $result_limit);
  $generator->generate();
}
