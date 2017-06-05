<?php

namespace Drupal\quizz\Entity;

use DatabaseTransaction;
use Drupal\quizz\Entity\Result\Maintainer;
use Drupal\quizz\Entity\Result\Render;
use Drupal\quizz\Entity\Result\ScoreIO;
use Drupal\quizz\Entity\Result\Writer;
use EntityAPIController;

class ResultController extends EntityAPIController {

  /** @var ScoreIO */
  private $score_io;

  /** @var Writer */
  private $writer;

  /** @var Maintainer */
  private $maintainer;

  public function getWriter() {
    if (NULL === $this->writer) {
      $this->writer = new Writer();
    }
    return $this->writer;
  }

  /**
   * @return ScoreIO
   */
  public function getScoreIO() {
    if (NULL === $this->score_io) {
      $this->score_io = new ScoreIO();
    }
    return $this->score_io;
  }

  public function setScoreCalculator($score_calculator) {
    $this->score_io = $score_calculator;
    return $this;
  }

  public function getMaintainer() {
    if (NULL === $this->maintainer) {
      $this->maintainer = new Maintainer();
    }
    return $this->maintainer;
  }

  public function load($ids = array(), $conditions = array()) {
    $entities = parent::load($ids, $conditions);

    foreach ($entities as $result) {
      $this->loadLayout($result);
    }

    return $entities;
  }

  /**
   * Attach the layout which previously used to be stored on the result.
   *
   * @param Result $result
   */
  private function loadLayout(Result $result) {
    $layout = entity_load('quiz_result_answer', FALSE, array('result_id' => $result->result_id));

    foreach ($layout as $answer) {
      $result->layout[$answer->number] = array(
          'display_number' => $answer->number,
          'qid'            => $answer->question_qid,
          'vid'            => $answer->question_vid,
          'number'         => $answer->number,
          'type'           => quizz_question_load($answer->question_qid)->type,
      );

      // @kludge
      // This is bulky but now we have to manually find the type and parents of
      // the question. This is the only information that is not stored in the
      // quiz attempt. We reference back to the quiz relationships for this
      // current version to get the hieararchy.
      $select = db_select('quiz_results', 'result');
      $select->innerJoin('quiz_relationship', 'relationship', 'result.quiz_vid = relationship.quiz_vid');
      $select->innerJoin('quiz_question_entity', 'question', 'relationship.question_qid = question.qid');
      $select
        ->fields('question', array('type'))
        ->fields('relationship', array('qr_id', 'qr_pid'))
        ->condition('result.result_id', $result->result_id)
        ->condition('question.qid', $answer->question_qid);

      if ($extra = $select->execute()->fetchObject()) {
        $result->layout[$answer->number] += array(
            'qr_id'  => $extra->qr_id,
            'qr_pid' => $extra->qr_pid,
        );
      }
    }
    ksort($result->layout, SORT_NUMERIC);
  }

  /**
   * {@inheritdoc}
   * @param Result $result
   */
  public function buildContent($result, $view_mode = 'full', $langcode = NULL, $content = array()) {
    $quiz = quizz_load($result->quiz_qid);
    $quiz_revision = quizz_load(NULL, $result->quiz_vid);
    $obj = new Render($quiz, $quiz_revision, $result);
    $obj->render($content);
    return parent::buildContent($result, $view_mode, $langcode, $content);
  }

  /**
   * @global \stdlClass $user
   * @param Result $result
   * @param DatabaseTransaction $transaction
   */
  public function save($result, DatabaseTransaction $transaction = NULL) {
    global $user;

    $return = parent::save($result, $transaction);

    // Delete old results if necessary.
    $result->maintenance($user->uid);

    return $return;
  }

  public function delete($result_ids, DatabaseTransaction $transaction = NULL) {
    $return = parent::delete($result_ids, $transaction);
    $this->callHandlerDeleteMethod($result_ids);
    return $return;
  }

  /**
   * Call handler owner to delete the answer.
   *
   * @param int[] $result_ids
   */
  private function callHandlerDeleteMethod($result_ids) {
    $select = db_select('quiz_answer_entity', 'answer');
    $select->fields('answer', array('result_id', 'question_qid', 'question_vid'));
    $select->condition('answer.result_id', $result_ids);
    $answers = $select->execute()->fetchAll();
    foreach ($answers as $answer) {
      $question_revision = quizz_question_load(NULL, $answer->question_vid);
      if ($handler = $question_revision->getResponseHandler($answer->result_id)) {
        $handler->delete();
      }
    }
    db_delete('quiz_answer_entity')->condition('result_id', $result_ids)->execute();
  }

}
