<?php

namespace Drupal\quizz\Entity\QuizEntity;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Relationship;
use PDO;

class QuestionIO {

  private $quiz;

  public function __construct(QuizEntity $quiz) {
    $this->quiz = $quiz;
  }

  /**
   * Retrieves a list of questions (to be taken) for a given quiz.
   *
   * If the quiz has random questions this function only returns a random
   * selection of those questions. This function should be used to decide
   * what questions a quiz taker should answer.
   *
   * This question list is stored in the user's result, and may be different
   * when called multiple times. It should only be used to generate the layout
   * for a quiz attempt and NOT used to do operations on the questions inside of
   * a quiz.
   *
   * @return array[] Array of relationships.
   */
  public function getQuestionList() {
    if (QUIZZ_QUESTION_CATEGORIZED_RANDOM == $this->quiz->randomization) {
      return $this->buildCategoziedQuestionList();
    }
    return $this->getRequiredQuestions();
  }

  /**
   * Builds the questionlist for quizzes with categorized random questions
   */
  public function buildCategoziedQuestionList() {
    if (!$question_types = array_keys(quizz_question_get_types())) {
      return array();
    }

    $questions = array();
    $question_ids = array();
    $total_count = 0;
    foreach ($this->quiz->getTerms() as $term) {
      $select = db_select('quiz_question_entity', 'question');
      if (!empty($question_ids)) {
        $select->condition('question.qid', $question_ids, 'NOT IN');
      }

      $table = quizz()->getQuestionCategoryField()->getTableName();
      $column = quizz()->getQuestionCategoryField()->getColumnName();

      $select->join($table, 'tn', 'question.qid = tn.entity_id AND entity_type = :quiz_question', array(':quiz_question' => 'quiz_question_entity'));
      $find = $select
        ->fields('question', array('qid', 'vid', 'type'))
        ->fields('tn', array($column))
        ->condition('question.status', 1)
        ->condition('question.type', $question_types)
        ->condition('tn.' . $column, $term->tid)
        ->range(0, $term->number)
        ->orderRandom()
        ->execute()
      ;
      $count = 0;
      while ($question = $find->fetchAssoc()) {
        $count++;
        $question['tid'] = $term->tid;
        $question['number'] = $count + $total_count;
        $questions[] = $question;
        $question_ids[] = $question['qid'];
      }
      $total_count += $count;
      if ($count < $term->number) {
        return array(); // Not enough questions
      }
    }

    return $questions;
  }

  /**
   * @return array
   */
  private function getRequiredQuestions() {
    $select = db_select('quiz_relationship', 'relationship');
    $select->innerJoin('quiz_question_entity', 'question', 'relationship.question_qid = question.qid');

    // Sub relationship
    $cond_1 = 'relationship.qr_pid = sub_relationship.qr_id';
    $cond_2 = 'relationship.qr_pid IS NULL AND relationship.qr_id = sub_relationship.qr_id';
    $select->leftJoin('quiz_relationship', 'sub_relationship', "($cond_1) OR ($cond_2)");

    $select->addField('relationship', 'question_qid', 'qid');
    $select->addField('relationship', 'question_vid', 'vid');
    $select->addField('question', 'type');
    $select->fields('relationship', array('qr_id', 'qr_pid', 'question_status', 'weight', 'max_score'));
    $query = $select
      ->condition('relationship.quiz_vid', $this->quiz->vid)
      ->condition('relationship.question_status', QUIZZ_QUESTION_ALWAYS)
      ->condition('question.status', 1)
      ->orderBy('sub_relationship.weight')
      ->orderBy('relationship.weight')
      ->execute();

    // Just to make it easier on us, let's use a 1-based index.
    $i = 1;
    $relationships = array();
    while ($relationship = $query->fetchAssoc()) {
      $relationships[$i++] = $relationship;
    }

    // Get random questions for the remainder.
    if ($this->quiz->number_of_random_questions > 0) {
      $random_relationships = $this->getRandomQuestions();
      $relationships = array_merge($relationships, $random_relationships);

      // Unable to find enough requested random questions.
      if ($this->quiz->number_of_random_questions > count($random_relationships)) {
        return array();
      }
    }

    // Shuffle questions if required.
    if ($this->quiz->randomization > 0) {
      return $this->doShuffle($relationships);
    }

    return $relationships;
  }

  private function doShuffle($relationships) {
    $items = array();
    $mark = NULL;
    foreach ($relationships as $i => $relationship) {
      if ($mark) {
        if ($relationship['type'] === 'quiz_page') {
          // Found another page.
          shuffle($items);
          array_splice($relationships, $mark, $i - $mark - 1, $items);
          $mark = 0;
          $items = array();
        }
        else {
          $items[] = $relationship;
        }
      }

      if ($relationship['type'] === 'quiz_page') {
        $mark = $i;
      }
    }

    if ($mark) {
      shuffle($items);
      array_splice($relationships, $mark, $i - $mark, $items);
    }
    elseif (is_null($mark)) {
      shuffle($relationships);
    }

    return $relationships;
  }

  /**
   * Get an array list of random questions for a quiz.
   *
   * @return array[] Array of qid/vid combos for quiz questions.
   */
  private function getRandomQuestions() {
    $amount = $this->quiz->number_of_random_questions;
    if ($this->quiz->tid > 0) {
      return $this->getRandomTaxonomyQuestionIds($this->quiz->tid, $amount);
    }
    return $this->doGetRandomQuestion($amount);
  }

  private function doGetRandomQuestion($amount) {
    $select = db_select('quiz_relationship', 'relationship');
    $select->join('quiz_question_entity', 'question', 'relationship.question_qid = question.qid');
    $select->addField('relationship', 'question_qid', 'qid');
    $select->addField('relationship', 'question_vid', 'vid');
    $select->addExpression(':true', 'random', array(':true' => TRUE));
    $select->addExpression(':number', 'relative_max_score', array(':number' => $this->quiz->max_score_for_random));
    return $select
        ->fields('relationship', array('qr_id', 'qr_pid', 'question_status'))
        ->fields('question', array('type'))
        ->condition('relationship.quiz_vid', $this->quiz->vid)
        ->condition('relationship.quiz_qid', $this->quiz->vid)
        ->condition('relationship.question_status', QUIZZ_QUESTION_RANDOM)
        ->condition('question.status', 1)
        ->orderRandom()
        ->range(0, $amount)
        ->execute()
        ->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Get all of the question qid/vids by taxonomy term ID.
   *
   * @param int $term_id
   * @param int $amount
   *
   * @return
   *   Array of qid/vid combos, like array(array('qid'=>1, 'vid'=>2)).
   */
  public function getRandomTaxonomyQuestionIds($term_id, $amount) {
    if (!$term_id || !$term = taxonomy_term_load($term_id)) {
      return array();
    }

    // Flatten the taxonomy tree, and just keep term id's.
    $term_ids[] = $term->tid;
    if ($tree = taxonomy_get_tree($term->vid, $term->tid)) {
      foreach ($tree as $term) {
        $term_ids[] = $term->tid;
      }
    }

    // Get all published questions with one of the allowed term ids.
    $query = db_select('question', 'question');
    $query->innerJoin('taxonomy_index', 'tn', 'question.qid = tn.qid');
    $query->addExpression(1, 'random');

    return $query
        ->fields('question', array('qid', 'vid'))
        ->condition('question.status', 1)
        ->condition('tn.tid', $term_ids)
        ->condition('question.type', array_keys(quizz_question_get_types()))
        ->orderRandom()
        ->range(0, $amount)
        ->execute()->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @param Relationship[] $relationships
   * @return boolean TRUE if update was successful, FALSE otherwise.
   */
  public function setRelationships(array $relationships) {
    if (empty($this->quiz->old_vid)) {
      db_delete('quiz_relationship')
        ->condition('quiz_vid', $this->quiz->vid)
        ->execute();
    }

    // This is not an error condition.
    if (!empty($relationships)) {
      $this->doSetRelationships($relationships);
      $this->quiz->getController()->getMaxScoreWriter()->update(array($this->quiz->vid));
    }

    return TRUE;
  }

  /**
   * @param Relationship[] $relationships
   */
  private function doSetRelationships($relationships) {
    foreach ($relationships as $relationship) {
      if (isset($relationship->question_status)) {
        if (QUIZZ_QUESTION_NEVER == $relationship->question_status) {
          continue;
        }
      }

      // Update to latest OR use the version given.
      $question_qid = $relationship->question_qid;
      $question_vid = $relationship->question_vid;

      if (!empty($relationship->refresh)) {
        $sql = 'SELECT vid FROM {quiz_question_entity} WHERE qid = :qid';
        $question_vid = db_query($sql, array(':qid' => $relationship->question_qid))->fetchField();
      }

      $values = array(
          'quiz_qid'              => $this->quiz->qid,
          'quiz_vid'              => $this->quiz->vid,
          'question_qid'          => $question_qid,
          'question_vid'          => $question_vid,
          'question_status'       => isset($relationship->question_status) ? $relationship->question_status : NULL,
          'weight'                => $relationship->weight,
          'max_score'             => (int) $relationship->max_score,
          'auto_update_max_score' => (int) $relationship->auto_update_max_score,
          'qr_pid'                => $relationship->qr_pid,
          'qr_id'                 => !empty($this->quiz->old_vid) ? NULL : $relationship->qr_id,
          'old_qr_id'             => $relationship->qr_id,
      );

      foreach ($values as $k => $v) {
        $relationship->{$k} = $v;
      }

      $relationship->save();
    }

    // Update the parentage when a new revision is created.
    // @todo this is copy pasta from quiz_update_quiz_relationship
    foreach ($relationships as $relationship) {
      $_relationships = entity_load('quiz_relationship', FALSE, array(
          'qr_pid'   => $relationship->old_qr_id,
          'quiz_vid' => $this->quiz->vid,
          'quiz_qid' => $this->quiz->qid,
        ), TRUE);

      foreach ($_relationships as $_relationship) {
        $_relationship->qr_pid = $relationship->qr_id;
        $_relationship->save();
      }
    }
  }

}
