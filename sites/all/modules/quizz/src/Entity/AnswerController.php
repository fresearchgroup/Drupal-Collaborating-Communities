<?php

namespace Drupal\quizz\Entity;

use DatabaseTransaction;
use EntityAPIController;

class AnswerController extends EntityAPIController {

  /**
   * {@inheritdoc}
   * @param Answer[] $answers
   */
  protected function attachLoad(&$answers, $revision_id = FALSE) {
    foreach ($answers as $answer) {
      $answer->bundle(); // Make sure entity has bundle property.
    }
    return parent::attachLoad($answers, $revision_id);
  }

  /**
   * {@inheritdoc}
   * @param Answer $answer
   */
  public function save($answer, DatabaseTransaction $transaction = NULL) {
    $answer->bundle();
    if (!empty($answer->id)) {
      $answer->is_new = FALSE;
    }
    $answer->points_awarded = round($answer->points_awarded);
    return parent::save($answer, $transaction);
  }

  /**
   * Load answer by Result & questions IDs.
   *
   * @param int $result_id
   * @param int $question_vid
   * @return Answer
   */
  public function loadByResultAndQuestion($result_id, $question_vid) {
    $conditions = array('result_id' => $result_id, 'question_vid' => $question_vid);
    if ($return = entity_load('quiz_result_answer', FALSE, $conditions)) {
      return reset($return);
    }
  }

}
