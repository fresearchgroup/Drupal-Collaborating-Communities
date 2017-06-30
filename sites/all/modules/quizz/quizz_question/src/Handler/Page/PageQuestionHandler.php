<?php

namespace Drupal\quizz_question\Handler\Page;

use Drupal\quizz_question\QuestionHandler;

class PageQuestionHandler extends QuestionHandler {

  protected $body_field_title = 'Page';

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

  public function isGraded() {
    return FALSE;
  }

  function getAnsweringForm(array $form_state = NULL, $result_id) {
    return array('#type' => 'hidden');
  }

  public function hasFeedback() {
    return FALSE;
  }

}
