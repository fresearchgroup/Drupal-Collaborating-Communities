<?php

namespace Drupal\quizz\Controller;

use Drupal\quizz\Entity\QuizEntity;

class QuizTakeBaseController {

  /**
   * Update the session for this quiz to the active question.
   *
   * @param QuizEntity $quiz
   * @param int $page_number
   *   Question number starting at 1.
   */
  public function redirect(QuizEntity $quiz, $page_number) {
    $_SESSION['quiz'][$quiz->qid]['current'] = $page_number;
  }

}
