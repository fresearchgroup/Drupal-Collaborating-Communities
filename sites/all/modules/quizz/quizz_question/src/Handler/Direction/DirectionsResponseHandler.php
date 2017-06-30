<?php

namespace Drupal\quizz_question\Handler\Direction;

use Drupal\quizz_question\ResponseHandler;

/**
 * This module uses the question interface to define something which is actually
 * not a question.
 *
 * A Quiz Directions node is a placeholder for adding directions to a quiz. It
 * can be inserted any number of times into a quiz. Example uses may include:
 *
 * - Initial quiz-wide directions
 * - Section directions, e.g. "The next five questions are multiple choice,
 *    please…" (Won't work if the question order is randomized)
 * - Final confirmation, e.g. "You have answered all questions. Click submit to
 *    submit this quiz."
 */

/**
 * Extension of QuizQuestionResponse
 */
class DirectionsResponseHandler extends ResponseHandler {

  /**
   * {@inheritdoc}
   */
  public function score() {
    // First, due to popular demand, if the directions are at the beginning of
    // the quiz, we restart the timer after the user has read the question.
    $quiz_key = 'quiz_' . $this->result;
    if (isset($_SESSION[$quiz_key]['previous_quiz_questions'])) {
      // Reset the timer.
      if (1 === count($_SESSION[$quiz_key]['previous_quiz_questions'])) {
        $this->result->time_start = REQUEST_TIME;
        $this->result->save();
      }
    }

    // Set the score
    return $this->score = 0;
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
