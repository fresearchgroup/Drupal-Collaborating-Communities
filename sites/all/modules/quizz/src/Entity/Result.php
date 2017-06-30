<?php

namespace Drupal\quizz\Entity;

use Drupal\quizz_question\Entity\Question;
use Entity;

class Result extends Entity {

  /** @var int */
  public $result_id;

  /** @var string */
  public $type = 'quiz';

  /** @var QuizEntity */
  private $quiz;

  /** @var int */
  public $quiz_qid;

  /** @var int */
  public $quiz_vid;

  /** @var int Author ID */
  public $uid;

  /** @var int Start timestamp */
  public $time_start;

  /** @var int End timestamp */
  public $time_end;

  /** @var bool */
  public $released;

  /** @var ??? */
  public $score;

  /** @var bool */
  public $is_invalid;

  /** @var bool Indicates whether or not a quiz result is evaluated. */
  public $is_evaluated;

  /** @var int */
  public $time_left;

  /** @var array */
  public $layout = array();

  /**
   * Get quiz entity.
   *
   * @return QuizEntity
   */
  public function getQuiz() {
    if (NULL == $this->quiz) {
      $this->quiz = quizz_load(NULL, $this->quiz_vid);
    }
    return $this->quiz;
  }

  public function countPages() {
    $count = 0;
    foreach ($this->layout as $item) {
      if (('quiz_page' === $item['type']) || empty($item['qr_pid'])) {
        $count++;
      }
    }
    return $count;
  }

  public function isLastPage($page_number) {
    return $page_number == $this->countPages();
  }

  public function getNextPageNumber($page_number) {
    if ($this->isLastPage($page_number)) {
      return $page_number;
    }
    return $page_number + 1;
  }

  public function getPageItem($page_number) {
    $number = 0;
    foreach ($this->layout as $item) {
      if (('quiz_page' === $item['type']) || empty($item['qr_pid'])) {
        if (++$number == $page_number) {
          return $item;
        }
      }
    }
  }

  /**
   * Checks if the user has access to save score for his quiz.
   */
  public function canAccessOwnScore($account) {
    if (user_access('score any quiz', $account)) {
      return TRUE;
    }

    if ($quiz = quizz_load(NULL, $this->quiz_vid)) {
      return user_access('score own quiz', $account) && ($quiz->uid == $account->uid);
    }

    return FALSE;
  }

  /**
   * Dtermine if a user has access to view a specific quiz result.
   *
   * @return boolean
   *  True if access, false otherwise
   */
  public function canAccessOwnResult($account) {
    // Check if the quiz taking has been completed.
    if ($this->time_end > 0 && $this->uid == $account->uid) {
      return TRUE;
    }
    return $this->canAccessOwnScore($account) ? TRUE : FALSE;
  }

  /**
   * Can the quiz taker view the requested review?
   *
   * There's a workaround in here: @kludge
   *
   * When review for the question is enabled, and it is the last question,
   * technically it is the end of the quiz, and the "end of quiz" review
   * settings apply. So we check to make sure that we are in question taking
   * and the feedback is viewed within 5 seconds of completing the question/quiz.
   */
  public function canReview($op) {
    // Check what context the result is in.
    if ($this->time_end && arg(2) !== 'take') {
      // Quiz is over. Pull from the "at quiz end" settings.
      return !empty($this->getQuiz()->review_options['end'][$op]);
    }

    // Quiz ongoing. Pull from the "after question" settings.
    if (!$this->time_end || $this->time_end >= REQUEST_TIME - 5) {
      return !empty($this->getQuiz()->review_options['question'][$op]);
    }

    return FALSE;
  }

  /**
   * Deletes results for a quiz according to the keep results setting
   *
   * @param int $uid
   *  ID of user account.
   * @return bool
   *  TRUE if results where deleted.
   */
  public function maintenance($uid) {
    return entity_get_controller('quiz_result')
        ->getMaintainer()
        ->maintenance($uid, $this);
  }

  /**
   * Load answer entity by question object.
   *
   * @param Question $question
   * @return Answer
   */
  public function loadAnswerByQuestion(Question $question) {
    foreach ($this->layout as $item) {
      if ($question->vid == $item['vid']) {
        $conds = array('result_id' => $this->result_id, 'question_vid' => $question->vid);
        if ($find = entity_load('quiz_result_answer', NULL, $conds)) {
          $answer = reset($find);
          $answer->type = $question->type;
          return $answer;
        }
      }
    }
  }

}
