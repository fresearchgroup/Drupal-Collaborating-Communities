<?php

namespace Drupal\quizz\Entity\Result;

use Drupal\quizz\Entity\Result;
use stdClass;

class ScoreIO {

  /**
   * Calculates the score user received on quiz.
   *
   * @param Result $result
   *
   * @return array
   *   Contains three elements: question_count, num_correct and percentage_score.
   */
  public function calculate(Result $result) {
    $quiz = $result->getQuiz();

    // 2. Callback into the modules and let them do the scoring.
    // @todo after 4.0: Why isn't the scores already saved? They should be
    // Fetched from the db, not calculated…
    $scores = array();
    $count = 0;

    foreach ($result->layout as $layout_item) {
      if (!$question = quizz_question_load($layout_item['qid'], $layout_item['vid'])) {
        continue;
      }

      // Questions picked from term id's won't be found in the quiz_relationship table
      if ($question->max_score === NULL && isset($quiz->tid) && $quiz->tid > 0) {
        $question->max_score = $quiz->max_score_for_random;
      }

      $scores[] = $question->getResponseHandler($result->result_id)->getQuestionScore($question);
      ++$count;
    }

    // 3. Sum the results.
    $possible_score = 0;
    $total_score = 0;
    $is_evaluated = TRUE;
    foreach ($scores as $score) {
      $possible_score += $score->possible;
      $total_score += $score->attained;
      // Flag the entire quiz if one question has not been evaluated.
      if (isset($score->is_evaluated)) {
        $is_evaluated &= $score->is_evaluated;
      }
    }

    // 4. Return the score.
    return array(
        'question_count'   => $count,
        'possible_score'   => $possible_score,
        'numeric_score'    => $total_score,
        'percentage_score' => ($possible_score == 0) ? 0 : round(($total_score * 100) / $possible_score),
        'is_evaluated'     => $is_evaluated,
    );
  }

  /**
   * @TODO: Use entity API instead of direct db writing.
   *
   * Update a score for a quiz.
   *
   * This updates the quiz entity results table.
   *
   * It is used in cases where a quiz score is changed after the quiz has been
   * taken. For example, if a long answer question is scored later by a human,
   * then the quiz should be updated when that answer is scored.
   *
   * Important: The value stored in the table is the *percentage* score.
   *
   * @param Result $result
   *
   * @return
   *   The score as an integer representing percentage. E.g. 55 is 55%.
   */
  public function updateTotalScore(Result $result) {
    global $user;

    $score = $this->calculate($result);

    db_update('quiz_results')
      ->fields(array('score' => $score['percentage_score']))
      ->condition('result_id', $result->result_id)
      ->execute();

    if ($score['is_evaluated']) {
      $quiz = $result->getQuiz();
      module_invoke_all('quiz_scored', $quiz, $score, $result->result_id);

      $result->maintenance($user->uid);

      db_update('quiz_results')
        ->fields(array('is_evaluated' => 1))
        ->condition('result_id', $result->result_id)
        ->execute();
    }

    return $score['percentage_score'];
  }

}
