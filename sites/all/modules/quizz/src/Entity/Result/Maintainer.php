<?php

namespace Drupal\quizz\Entity\Result;

use Drupal\quizz\Entity\Result;

class Maintainer {

  /**
   * Deletes results for a quiz according to the keep results setting
   *
   * @param int $uid
   *  ID of user account.
   * @param Result $result
   *  The result id of the latest result for the current user
   * @return
   *  TRUE if results where deleted.
   */
  public function maintenance($uid, Result $result) {
    // Do nothing if:
    //  1. Result is not evaluated
    //  2. Anonymous user.
    //  3. Quiz entity is invalid
    if (!$result->is_evaluated || !$uid || (!$quiz = $result->getQuiz())) {
      return FALSE;
    }

    switch ($quiz->keep_results) {
      case QUIZZ_KEEP_BEST:
        return $this->keepBestResult($uid, $quiz);

      case QUIZZ_KEEP_LATEST:
        return $this->keepLatestResult($uid, $quiz, $result->result_id);
    }

    return FALSE;
  }

  private function keepBestResult($uid, $quiz) {
    $sql = 'SELECT result_id FROM {quiz_results}';
    $sql .= ' WHERE quiz_qid = :qid AND uid = :uid AND is_evaluated = 1';
    $sql .= ' ORDER BY score DESC';
    if (!$best_result_id = db_query($sql, array(':qid' => $quiz->qid, ':uid' => $uid))->fetchField()) {
      return FALSE;
    }

    $result_ids = db_query('SELECT result_id
          FROM {quiz_results}
          WHERE quiz_qid = :qid
            AND uid = :uid
            AND result_id != :best_rid
            AND is_evaluated = :is_evaluated', array(
        ':qid'          => $quiz->qid,
        ':uid'          => $uid,
        ':is_evaluated' => 1,
        ':best_rid'     => $best_result_id
      ))->fetchCol();
    return $this->maintainDoDeleteResults($result_ids);
  }

  private function keepLatestResult($uid, $quiz, $result_id) {
    $result_ids = db_query('SELECT result_id
            FROM {quiz_results}
            WHERE quiz_qid = :qid
              AND uid = :uid
              AND is_evaluated = :is_evaluated
              AND result_id != :result_id', array(
        ':qid'          => $quiz->qid,
        ':uid'          => $uid,
        ':is_evaluated' => 1,
        ':result_id'    => $result_id
      ))->fetchCol();
    return $this->maintainDoDeleteResults($result_ids);
  }

  private function maintainDoDeleteResults(array $result_ids) {
    if (!empty($result_ids)) {
      entity_delete_multiple('quiz_result', $result_ids);
      return TRUE;
    }
    return FALSE;
  }

}
