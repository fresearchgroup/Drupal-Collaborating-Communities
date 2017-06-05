<?php

namespace Drupal\quizz\Entity\Result;

use Drupal\quizz\Entity\Answer;
use Drupal\quizz\Entity\QuizEntity;

class Writer {

  /**
   * Store a quiz question result.
   *
   * @param QuizEntity $quiz
   * @param Answer $answer
   * @param array $options
   *  Array with options that affect the behavior of this function.
   *    ['set_msg'] - Sets a message if the last question was skipped.
   */
  public function saveAnswer(QuizEntity $quiz, Answer $answer, $options) {
    if ($id = $this->findAnswerId($answer)) {
      $answer->id = $id;
      $answer->is_new = FALSE;
    }

    $answer->is_skipped = isset($answer->is_skipped) ? $answer->is_skipped : FALSE;

    if (!empty($answer->is_skipped)) {
      !empty($options['set_msg']) && drupal_set_message(t('Last question skipped.'));
      $answer->is_correct = FALSE;
    }

    $answer->answer_timestamp = REQUEST_TIME;
    $answer->tid = ($quiz->randomization == 3 && $answer->tid) ? $answer->tid : 0;
    $answer->number = isset($options['question_data']['number']) ? $options['question_data']['number'] : 0;
    $score = !empty($answer->is_skipped) ? 0 : (int) ($answer->is_correct); // @TODO: Why 1/0?
    $answer->points_awarded = $score * $this->findScale($quiz, $answer, $options);
    $answer->save();
  }

  private function findAnswerId($response) {
    return db_query("SELECT id
        FROM {quiz_answer_entity}
        WHERE question_vid = :question_vid AND result_id = :result_id", array(
          ':question_vid' => $response->question_vid,
          ':result_id'    => $response->result_id
      ))->fetchField();
  }

  /**
   * Points are stored pre-scaled in the quiz_answer_entity table
   *
   * @param QuizEntity $quiz
   * @param Answer $answer
   * @return int
   */
  private function findScale(QuizEntity $quiz, Answer $answer, $options) {
    $ssql = '(SELECT max_score FROM {quiz_question_revision} WHERE qid = :question_qid AND vid = :question_vid)';

    if ($quiz->randomization < 2) {
      return db_query("
          SELECT (max_score/{$ssql}) as scale
          FROM {quiz_relationship}
          WHERE quiz_qid = :quiz_qid
            AND quiz_vid = :quiz_vid
            AND question_qid = :question_qid
            AND question_vid = :question_vid", array(
            ':quiz_qid'     => $quiz->qid,
            ':quiz_vid'     => $quiz->vid,
            ':question_qid' => $answer->question_qid,
            ':question_vid' => $answer->question_vid
        ))->fetchField();
    }

    if ($quiz->randomization == QUIZZ_QUESTION_NEVER) {
      return db_query("
          SELECT (max_score_for_random/{$ssql}) as scale
          FROM {quiz_entity_revision}
          WHERE vid = :quiz_vid", array(
            ':question_qid' => $answer->question_qid,
            ':question_vid' => $answer->question_vid,
            ':quiz_vid'     => $quiz->vid
        ))->fetchField();
    }

    if ($quiz->randomization == QUIZZ_QUESTION_CATEGORIZED_RANDOM) {
      if (isset($options['question_data']['tid'])) {
        $answer->tid = $options['question_data']['tid'];
      }

      return db_query("
          SELECT (max_score/{$ssql}) as scale
          FROM {quiz_entity_terms} WHERE vid = :vid AND tid = :tid", array(
            ':question_qid' => $answer->question_qid,
            ':question_vid' => $answer->question_vid,
            ':vid'          => $quiz->vid,
            ':tid'          => $answer->tid
        ))->fetchField();
    }
  }

}
