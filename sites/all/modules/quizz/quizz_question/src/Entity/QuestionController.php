<?php

namespace Drupal\quizz_question\Entity;

use DatabaseTransaction;
use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Relationship;
use EntityAPIController;
use stdClass;

class QuestionController extends EntityAPIController {

  /**
   * Allow disable invoking question handler.
   * @var bool
   */
  public static $disable_invoking = FALSE;

  /**
   * Implements EntityAPIControllerInterface.
   *
   * @param Question $question
   * @param DatabaseTransaction $transaction
   */
  public function save($question, DatabaseTransaction $transaction = NULL) {
    if (isset($question->feedback) && is_array($question->feedback)) {
      $question->feedback_format = $question->feedback['format'];
      $question->feedback = $question->feedback['value'];
    }

    $question->feedback = !empty($question->feedback) ? $question->feedback : '';
    $question->feedback_format = !empty($question->feedback_format) ? $question->feedback_format : filter_default_format();

    if (!static::$disable_invoking) {
      $question->max_score = $question->getHandler()->getMaximumScore();
    }
    elseif (!isset($question->max_score)) {
      $question->max_score = 0;
    }

    // Auto title
    if (!drupal_strlen($question->title) || !user_access('edit question titles')) {
      // Notice: String offset cast occurred in _field_invoke_multiple() (line 325 of …/modules/field/field.attach.inc).
      $body = @field_view_field('quiz_question_entity', $question, 'quiz_question_body');
      if (!empty($body[0]['#markup'])) {
        $max_length = $question->getQuestionType()->getConfig('autotitle_length', 50);
        $question->title = truncate_utf8(strip_tags($body[0]['#markup']), $max_length, TRUE, TRUE);
      }
    }

    return parent::save($question, $transaction);
  }

  /**
   * Force save revision author ID.
   *
   * @global stdClass $user
   * @param Question $question
   */
  protected function saveRevision($question) {
    global $user;

    if (!static::$disable_invoking) { // script is running
      $question->revision_uid = $user->uid;
    }

    return parent::saveRevision($question);
  }

  public function load($ids = array(), $conditions = array()) {
    // Do not load question with disabled handlers.
    if (!isset($conditions['type'])) {
      $conditions['type'] = array_keys(quizz_question_get_types());
    }

    $questions = parent::load($ids, $conditions);

    /* @var $question Question */
    foreach ($questions as $question) {
      if (!static::$disable_invoking) {
        foreach ($question->getHandler()->load() as $k => $v) {
          $question->$k = $v;
        }
      }
    }

    return $questions;
  }

  /**
   * {@inheritdoc}
   * @param Question $question
   */
  public function invoke($hook, $question) {
    if (static::$disable_invoking) {
      return parent::invoke($hook, $question);
    }

    switch ($hook) {
      case 'insert':
        $question->getHandler()->save($is_new = TRUE);
        break;

      case 'update':
        $question->getHandler()->save($is_new = FALSE);
        break;

      case 'delete':
        db_delete('quiz_answer_entity')->condition('question_qid', $question->qid)->execute();
        $question->getHandler()->delete($only_this_version = FALSE);
        break;

      case 'revision_delete':
        db_delete('quiz_answer_entity')->condition('question_vid', $question->vid)->execute();
        $question->getHandler()->delete($only_this_version = TRUE);
        break;
    }

    return parent::invoke($hook, $question);
  }

  /**
   * Implements EntityAPIControllerInterface.
   * @param Question $question
   * @param string $view_mode
   * @param string $langcode
   * @param string $content
   */
  public function buildContent($question, $view_mode = 'full', $langcode = NULL, $content = array()) {
    if (!static::$disable_invoking && ('full' === $view_mode)) {
      $content += $question->getHandler()->view();
    }
    return parent::buildContent($question, $view_mode, $langcode, $content);
  }

  /**
   * Find relationship object between a quiz and a question.
   * @param QuizEntity $quiz
   * @param Question $question
   * @return Relationship
   */
  public function findRelationship(QuizEntity $quiz, Question $question) {
    $conds = array('quiz_vid' => $quiz->vid, 'question_vid' => $question->vid);
    if ($relationships = entity_load('quiz_relationship', FALSE, $conds)) {
      return reset($relationships);
    }
  }

}
