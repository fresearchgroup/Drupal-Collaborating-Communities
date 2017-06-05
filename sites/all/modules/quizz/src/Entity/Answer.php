<?php

namespace Drupal\quizz\Entity;

use Drupal\quizz_question\Entity\Question;
use Drupal\quizz_question\ResponseHandlerInterface;
use Entity;

class Answer extends Entity {

  public $id;
  public $type;
  public $result_id;
  public $question_qid;
  public $question_vid;
  public $tid;
  public $is_correct;
  public $is_skipped;
  public $points_awarded;
  public $answer_timestamp;
  public $number;
  public $is_doubtful;

  /** @var Question */
  private $question;

  /** @var mixed Custom input, structure is upto question handler. */
  private $input;

  public function bundle() {
    if (NULL == $this->type) {
      $sql = 'SELECT type FROM {quiz_question_entity} WHERE vid = :vid';
      $this->type = db_query($sql, array(':vid' => $this->question_vid))->fetchColumn();
    }
    return parent::bundle();
  }

  public function getInput() {
    return $this->input;
  }

  public function setInput($input) {
    $this->input = $input;
    return $this;
  }

  public function getQuestion() {
    if (NULL === $this->question) {
      $this->question = quizz_question_load(NULL, $this->question_vid);
    }
    return $this->question;
  }

  /**
   * @return ResponseHandlerInterface
   */
  public function getHandler() {
    return $this->getQuestion()->getResponseHandler($this->result_id, $this->getInput());
  }

}
