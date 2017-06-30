<?php

namespace Drupal\quizz_question\Handler\Direction;

use Drupal\quizz_question\QuestionHandler;

/**
 * Extension of QuizQuestion.
 */
class DirectionsQuestionHandler extends QuestionHandler {

  protected $body_field_title = 'Direction';

  /**
   * {@inheritdoc}
   */
  public function getAnsweringForm(array $form_state = NULL, $result_id) {
    $form = parent::getAnsweringForm($form_state, $result_id);
    $form['tries'] = array('#type' => 'hidden', '#value' => 0);
    $form['empty_space'] = array('#type' => 'markup', '#value' => '<br/>');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreationForm(array &$form_state = NULL) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getMaximumScore() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function isGraded() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFeedback() {
    return FALSE;
  }

}
