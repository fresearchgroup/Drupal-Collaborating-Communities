<?php

namespace Drupal\quizz\Entity;

use DatabaseTransaction;
use Drupal\quizz\Entity\QuizEntity\DefaultPropertiesIO;
use Drupal\quizz\Entity\QuizEntity\MaxScoreWriter;
use Drupal\quizz\Entity\QuizEntity\ResultGenerator;
use Drupal\quizz\Entity\QuizEntity\Stats;
use EntityAPIController;
use stdClass;

class QuizController extends EntityAPIController {

  /** @var DefaultPropertiesIO */
  private $default_properties_io;

  /** @var Stats */
  private $stats;

  /** @var MaxScoreWriter */
  private $max_score_writer;

  /** @var ResultGenerator */
  private $result_generator;

  /**
   * @return DefaultPropertiesIO
   */
  public function getSettingIO() {
    if (NULL === $this->default_properties_io) {
      $this->default_properties_io = new DefaultPropertiesIO();
    }
    return $this->default_properties_io;
  }

  public function getStats() {
    if (NULL === $this->stats) {
      $this->stats = new Stats();
    }
    return $this->stats;
  }

  public function getMaxScoreWriter() {
    if (NULL === $this->max_score_writer) {
      $this->max_score_writer = new MaxScoreWriter();
    }
    return $this->max_score_writer;
  }

  public function getResultGenerator() {
    if (NULL === $this->result_generator) {
      $this->result_generator = new ResultGenerator();
    }
    return $this->result_generator;
  }

  /**
   * Get the feedback options for Quizzes.
   */
  public function getFeedbackOptions() {
    $feedback_options = array();

    $entity_info = entity_get_info('quiz_question_entity');
    foreach ($entity_info['view modes'] as $view_mode => $info) {
      if ($view_mode !== 'full' && $info['custom settings']) {
        $feedback_options["quiz_question_view_{$view_mode}"] = t('Question') . ': ' . $info['label'];
      }
    }

    $feedback_options += array(
        'attempt'           => t('Attempt'),
        'choice'            => t('Choices'),
        'correct'           => t('Whether correct'),
        'score'             => t('Score'),
        'answer_feedback'   => t('Answer feedback'),
        'question_feedback' => t('Question feedback'),
        'solution'          => t('Correct answer'),
        'quiz_feedback'     => t('@quiz feedback', array('@quiz' => QUIZZ_NAME)),
    );

    drupal_alter('quiz_feedback_options', $feedback_options);

    return $feedback_options;
  }

  /**
   * @param QuizEntity $quiz
   */
  public function buildContent($quiz, $view_mode = 'full', $langcode = NULL, $content = array()) {
    global $user;

    $extra_fields = field_extra_fields_get_display($this->entityType, $quiz->type, $view_mode);

    // Render Stats
    if ($extra_fields['stats']['visible']) {
      // Number of questions is needed on the statistics page.
      $quiz->number_of_questions = $quiz->number_of_random_questions;
      $quiz->number_of_questions += $this->getStats()->countAlwaysQuestions($quiz->vid);

      $content['stats'] = array(
          '#markup' => theme('quizz_view_stats', array('quiz' => $quiz)),
          '#weight' => $extra_fields['stats']['weight'],
      );
    }

    // Render take button
    if ($extra_fields['take']['visible']) {
      $markup = l(t('Start @quiz', array('@quiz' => QUIZZ_NAME)), 'quiz/' . $quiz->qid . '/take', array(
          'attributes' => array(
              'class' => array('quiz-start-link', 'btn', 'btn-success')
          ),
      ));

      if (TRUE !== $checking = $quiz->isAvailable($user)) {
        $markup = $checking;
      }

      $content['take'] = array(
          '#prefix' => '<div class="quiz-not-available">',
          '#suffix' => '</div>',
          '#weight' => $extra_fields['take']['weight'],
          '#markup' => $markup,
      );
    }

    $this->contextFlag("quizz_quiz_page_{$view_mode}", TRUE);

    return parent::buildContent($quiz, $view_mode, $langcode, $content);
  }

  public function load($ids = array(), $conditions = array()) {
    $entities = parent::load($ids, $conditions);

    // quiz_entity_revision.review_options => serialize = TRUE already, not sure
    // why it's string here
    foreach ($entities as $entity) {
      $vids[] = $entity->vid;
      if (!empty($entity->review_options) && is_string($entity->review_options)) {
        $entity->review_options = unserialize($entity->review_options);
      }
    }

    if (!empty($vids)) {
      $result_options = db_select('quiz_result_options', 'ro')
        ->fields('ro')
        ->condition('ro.quiz_vid', $vids)
        ->execute();
      foreach ($result_options->fetchAll() as $result_option) {
        $entities[$result_option->quiz_qid]->result_options[] = (array) $result_option;
      }
    }

    return $entities;
  }

  /**
   * @param QuizEntity $quiz
   * @param DatabaseTransaction $transaction
   */
  public function save($quiz, DatabaseTransaction $transaction = NULL) {
    // QuizFeedbackTest::testFeedback() failed without this, mess!
    if (empty($quiz->is_new_revision)) {
      $quiz->is_new = $quiz->revision = 0;
    }

    if ($return = parent::save($quiz, $transaction)) {
      $this->saveResultOptions($quiz);
      isset($quiz->path) && $this->savePath($quiz);
      return $return;
    }
  }

  /**
   * Force save revision author ID.
   *
   * @global stdClass $user
   * @param QuizEntity $quiz
   */
  protected function saveRevision($quiz) {
    global $user;
    $quiz->revision_uid = $user->uid;
    $return = parent::saveRevision($quiz);

    if (!empty($quiz->clone_relationships) && ($quiz->vid != $quiz->old_vid)) {
      $this->cloneRelationship($quiz, $quiz->old_vid);
    }

    return $return;
  }

  private function cloneRelationship(QuizEntity $quiz, $previous_vid) {
    // The cloning logic implemented somewhere. This legacy code should be removed later.
    if ($quiz->getQuestionIO()->getQuestionList()) {
      return;
    }

    if (!$revision = quizz_load(NULL, $previous_vid, TRUE)) {
      return;
    }

    if (QUIZZ_QUESTION_CATEGORIZED_RANDOM == $quiz->randomization) {
      foreach ($revision->getTerms() as $term) {
        $term->vid = $quiz->vid;
        drupal_write_record('quiz_entity_terms', $term);
      }
      return;
    }

    foreach ($revision->getQuestionIO()->getQuestionList() as $relationship) {
      if (empty($relationship['random'])) {
        if ($relationship = quizz_relationship_load($relationship['qr_id'])) {
          $relationship->qr_id = NULL;
          $relationship->quiz_vid = $quiz->vid;
          $relationship->save();
        }
      }
    }
  }

  /**
   * Delete path aliases.
   *
   * @param int[] $quiz_ids
   */
  private function deletePath(array $quiz_ids) {
    foreach ($quiz_ids as $quiz_id) {
      if ($path = path_load(array('source' => "quiz/{$quiz_id}"))) {
        path_delete($path['pid']);
      }
    }
  }

  private function savePath(QuizEntity $quiz) {
    $path = $quiz->path;

    // Ensure fields for programmatic executions.
    if ($path['alias'] = trim($path['alias'])) {
      $langcode = entity_language('quiz_entity', $quiz);
      $uri = entity_uri('quiz_entity', $quiz);

      $path['language'] = isset($langcode) ? $langcode : LANGUAGE_NONE;
      $path['source'] = $uri['path'];

      // Delete old alias if user erased it.
      if (!empty($path['pid']) && empty($path['alias'])) {
        path_delete($path['pid']);
      }

      path_save($path);
    }
  }

  private function saveResultOptions(QuizEntity $quiz) {
    db_delete('quiz_result_options')
      ->condition('quiz_vid', $quiz->vid)
      ->execute();

    $query = db_insert('quiz_result_options')
      ->fields(array('quiz_qid', 'quiz_vid', 'option_name', 'option_summary', 'option_summary_format', 'option_start', 'option_end'));

    foreach ($quiz->result_options as $option) {
      if (empty($option['option_name'])) {
        continue;
      }

      // When this function called direct from node form submit the
      // $option['option_summary']['value'] and $option['option_summary']['format'] are we need
      // But when updating a quiz entity eg. on manage questions page, this values
      // come from loaded node, not from a submitted form.
      if (is_array($option['option_summary'])) {
        $option['option_summary_format'] = $option['option_summary']['format'];
        $option['option_summary'] = $option['option_summary']['value'];
      }

      $query->values(array(
          'quiz_qid'              => $quiz->qid,
          'quiz_vid'              => $quiz->vid,
          'option_name'           => $option['option_name'],
          'option_summary'        => $option['option_summary'],
          'option_summary_format' => $option['option_summary_format'],
          'option_start'          => $option['option_start'],
          'option_end'            => $option['option_end']
      ));
    }

    $query->execute();
  }

  public function delete($ids, DatabaseTransaction $transaction = NULL) {
    $return = parent::delete($ids, $transaction);

    // Delete path aliases
    $this->deletePath($ids);

    // Delete quiz results
    $query = db_select('quiz_results');
    $query->fields('quiz_results', array('result_id'));
    $query->condition('quiz_qid', $ids);
    if ($result_ids = $query->execute()->fetchCol()) {
      entity_delete_multiple('quiz_result', $result_ids);
    }

    db_delete('quiz_relationship')->condition('quiz_qid', $ids)->execute();
    db_delete('quiz_results')->condition('quiz_qid', $ids)->execute();
    db_delete('quiz_result_options')->condition('quiz_qid', $ids)->execute();

    return $return;
  }

  /**
   * Check a user/quiz combo to see if the user passed the given quiz.
   *
   * This will return TRUE if the user has passed the quiz at least once, and
   * FALSE otherwise. Note that a FALSE may simply indicate that the user has not
   * taken the quiz.
   *
   * @param int $uid
   * @param int $quiz_vid
   */
  public function isPassed($uid, $quiz_vid) {
    $passed = db_query(
      'SELECT COUNT(result_id) AS passed_count
       FROM {quiz_results} result
       INNER JOIN {quiz_entity_revision} revision ON (result.quiz_vid = revision.vid)
       WHERE result.quiz_vid = :vid AND result.uid = :uid AND score >= pass_rate', array(
        ':vid' => $quiz_vid,
        ':uid' => $uid
      ))->fetchField();

    // Force into boolean context.
    return ($passed !== FALSE && $passed > 0);
  }

  /**
   * @param int $quiz_id
   * @param \stdClass $account
   * @return NULL|\Drupal\quizz\Entity\Result
   */
  public function findBestResult($quiz_id, $account) {
    $sql = 'SELECT result_id FROM {quiz_results}';
    $sql .= ' WHERE quiz_qid = :qid AND uid = :uid AND archived = 0';
    $sql .= ' ORDER BY score DESC LIMIT 1';
    if ($result_id = db_query($sql, array(':qid' => $quiz_id, ':uid' => $account->uid))->fetchColumn()) {
      return quizz_result_load($result_id);
    }
  }

  /**
   * Returns the result ID for any current result set for the given quiz.
   *
   * @param int $quiz_id
   * @param int $uid
   * @param int $time
   *   Timestamp used to check whether the quiz is still open. Default: current
   *   time.
   *
   * @return int
   *   If a quiz is still open and the user has not finished the quiz,
   *   return the result set ID so that the user can continue. If no quiz is in
   *   progress, this will return 0.
   */
  public function findActiveResultId($quiz_id, $uid, $time = NULL) {
    $sql = 'SELECT result.result_id'
      . ' FROM {quiz_results} result'
      . '   INNER JOIN {quiz_entity_revision} quiz ON result.quiz_vid = quiz.vid'
      . ' WHERE'
      . '   (quiz.quiz_always = :quiz_always OR (:between BETWEEN quiz.quiz_open AND quiz.quiz_close))'
      . '   AND result.quiz_qid = :qid '
      . '   AND result.uid = :uid '
      . '   AND result.time_end IS NULL';

    // Get any quiz that is open, for this user, and has not already been completed.
    return (int) db_query($sql, array(
          ':quiz_always' => 1,
          ':between'     => $time ? $time : REQUEST_TIME,
          ':qid'         => $quiz_id,
          ':uid'         => $uid
      ))->fetchField();
  }

  /**
   * Get data for all terms belonging to a Quiz with categorized random questions
   * @param int $quiz_vid
   */
  public function getTerms($quiz_vid) {
    return db_query(
        'SELECT term_data.name, term.*
         FROM {quiz_entity_terms} term
         INNER JOIN {taxonomy_term_data} term_data ON term.tid = term_data.tid
         WHERE term.vid = :vid ORDER BY term.weight', array(':vid' => $quiz_vid)
      )->fetchAll();
  }

  protected function contextFlag($name, $value) {
    if (module_exists('context')) {
      context_set('context', $name, $value);
    }
  }

}
