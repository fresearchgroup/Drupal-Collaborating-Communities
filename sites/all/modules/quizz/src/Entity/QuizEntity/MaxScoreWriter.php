<?php

namespace Drupal\quizz\Entity\QuizEntity;

class MaxScoreWriter {

  /**
   * Updates the max_score property on the specified quizzes
   *
   * @param int[] $quiz_vids
   *  Array with the vid's of the quizzes to update
   */
  public function update(array $quiz_vids) {
    if (empty($quiz_vids)) {
      return;
    }
    $this->doUpdateAlwaysQuizzes($quiz_vids);
    $this->doUpdateCategorizedRandom($quiz_vids);
    $this->doUpdateChangedProperties($quiz_vids);
    $this->doUpdateResults($quiz_vids);
  }

  private function doUpdateChangedProperties($quiz_vids) {
    // Update changed timestamp of quiz revisions
    db_update('quiz_entity_revision')
      ->fields(array('changed' => REQUEST_TIME))
      ->condition('vid', $quiz_vids)
      ->execute();

    // Update changed timestamp of quiz
    db_update('quiz_entity')
      ->fields(array('changed' => REQUEST_TIME))
      ->condition('vid', $quiz_vids)
      ->execute();
  }

  /**
   * @param int[] $quiz_vids
   */
  private function doUpdateAlwaysQuizzes($quiz_vids) {
    // Max score = random questions's score + always questions's score
    $score_random = 'max_score_for_random * number_of_random_questions';
    $score_always = 'SELECT COALESCE(SUM(max_score), 0) '
      . ' FROM {quiz_relationship} relationship'
      . ' WHERE relationship.question_status = :status AND quiz_vid = {quiz_entity_revision}.vid';
    db_update('quiz_entity_revision')
      ->expression('max_score', "($score_random) + ($score_always)", array(':status' => QUIZZ_QUESTION_ALWAYS))
      ->condition('vid', $quiz_vids)
      ->execute();
  }

  /**
   * @param int[] $quiz_vids
   */
  private function doUpdateCategorizedRandom($quiz_vids) {
    // If quiz as question mode = QUESTION_ALWAYS
    // Max score = sum of max score of each question in quiz.
    $_score = 'SELECT COALESCE(SUM(qt.max_score * qt.number), 0)';
    $_score .= ' FROM {quiz_entity_terms} qt';
    $_score .= ' WHERE qt.qid = {quiz_entity_revision}.qid AND qt.vid = {quiz_entity_revision}.vid';
    db_update('quiz_entity_revision')
      ->expression('max_score', "($_score)")
      ->condition('randomization', QUIZZ_QUESTION_CATEGORIZED_RANDOM)
      ->condition('vid', $quiz_vids)
      ->execute();
  }

  /**
   * @param int[] $quiz_vids
   */
  private function doUpdateResults($quiz_vids) {
    // Find quiz revisions those have max score <> 0
    // @QUESTION: Why we need this condition?
    $_quiz_vids = db_query('SELECT vid'
      . ' FROM {quiz_entity_revision}'
      . ' WHERE vid IN (:vid) AND max_score <> :max_score', array(
        ':vid'       => $quiz_vids,
        ':max_score' => 0))->fetchCol();
    if (empty($_quiz_vids)) {
      return;
    }

    $points_awarded = 'SELECT COALESCE(SUM(answer.points_awarded), 0) FROM {quiz_answer_entity} answer WHERE answer.result_id = {quiz_results}.result_id';
    $points_max = 'SELECT max_score FROM {quiz_entity_revision} qnp WHERE qnp.vid = {quiz_results}.quiz_vid';
    db_update('quiz_results')
      ->expression('score', "ROUND(100 * ($points_awarded) / ($points_max))")
      ->condition('quiz_vid', $_quiz_vids)
      ->execute();
  }

}
