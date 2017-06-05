<?php

namespace Drupal\quizz_question\Handler\Page;

use Drupal\quizz_question\ResponseHandler;

class PageResonseHandler extends ResponseHandler {

  /**
   * {@inheritdoc}
   */
  public function score() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function isCorrect() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getReportForm() {
    return array('#no_report' => TRUE);
  }

}
