<?php

namespace Drupal\quizz\Entity;

use Drupal\quizz_question\Entity\Question;
use Entity;

class Relationship extends Entity {

  public $qr_id;
  public $quiz_qid;
  public $quiz_vid;
  public $qr_pid;
  public $question_qid;
  public $question_vid;
  public $question_status;
  public $weight;
  public $max_score;
  public $auto_update_max_score;

  /**
   * Get question object.
   *
   * @return Question
   */
  public function getQuestion() {
    return quizz_question_load($this->question_qid, $this->question_vid);
  }

}
