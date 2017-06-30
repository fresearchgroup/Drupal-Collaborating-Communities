<?php

namespace Drupal\quizz\Entity;

use Drupal\quizz\Entity\QuizEntity\QuestionIO;
use Drupal\quizz_question\Entity\Question;
use Entity;

class QuizEntity extends Entity {

  /** @var int Quiz ID */
  public $qid;

  /** @var int Quiz Revision ID */
  public $vid;

  /** @var int Version ID of quiz before new revision created. */
  public $old_vid;

  /**
   * Status of a quiz.
   *
   * If status is -1, quiz is used as default properties when user uses quiz
   *  creating form.
   *
   * @see \Drupal\quizz\Entity\QuizEntity\DefaultPropertiesIO.
   * @var int
   */
  public $status = TRUE;

  /** @var string */
  public $language = LANGUAGE_NONE;

  /** @var string The name of the quiz type. */
  public $type = 'quiz';

  /** @var string The quiz label. */
  public $title;

  /** @var integer The user id of the quiz owner. */
  public $uid;

  /** @var integer The Unix timestamp when the quiz was created. */
  public $created;

  /** @var integer The Unix timestamp when the quiz was most recently saved. */
  public $changed;

  /** @var int */
  public $pass_rate;

  /** @var bool */
  public $allow_jumping;

  /** @var bool */
  public $repeat_until_correct;

  /** @var array */
  public $result_options = array();

  /** @var bool Magic flag to create new revision on save */
  public $is_new_revision;

  /** @var string ('', correct, all) */
  public $build_on_last;

  /** @var string Revision log */
  public $log;

  /** @var int */
  public $quiz_open;

  /** @var int */
  public $quiz_close;

  /**
   * Enum: QUIZ_KEEP_BEST, QUIZ_KEEP_LATEST, QUIZ_KEEP_ALL.
   *
   * @var int
   */
  public $keep_results = QUIZZ_KEEP_ALL;

  /** @var int */
  public $randomization;

  /** @var QuestionIO */
  private $question_io;

  /** @var bool Set to TRUE if we want clone relationships on save new quiz revision. */
  public $clone_relationships;

  public function __construct(array $values = array()) {
    parent::__construct($values, 'quiz_entity');
  }

  /**
   * @return QuizController
   */
  public function getController() {
    return quizz_entity_controller();
  }

  /**
   * @return QuizType
   */
  public function getQuizType() {
    return quizz_type_load($this->type);
  }

  public function save() {
    global $user;

    // Entity datetime
    $this->changed = isset($this->changed) ? $this->changed : time();

    if ($this->is_new = isset($this->is_new) ? $this->is_new : 0) {
      $this->created = time();
      if (NULL === $this->uid) {
        $this->uid = $user->uid;
      }
    }

    // Default properties
    foreach ($this->getController()->getSettingIO()->getQuizDefaultPropertyValues() as $k => $v) {
      if (!isset($this->{$k})) {
        $this->{$k} = $v;
      }
    }

    if (!empty($this->is_new_revision) && !empty($this->vid)) {
      $this->old_vid = $this->vid;
    }

    return parent::save();
  }

  /**
   * Default quiz entity uri.
   */
  protected function defaultUri() {
    return array('path' => 'quiz/' . $this->identifier());
  }

  public function url() {
    $uri = $this->defaultUri();
    return $uri['path'];
  }

  public function link() {
    return l($this->title, $this->url());
  }

  /**
   * @return QuestionIO
   */
  public function getQuestionIO() {
    if (NULL === $this->question_io) {
      $this->question_io = new QuestionIO($this);
    }
    return $this->question_io;
  }

  /**
   * Get data for all terms belonging to a Quiz with categorized random questions.
   *
   * @return array
   */
  public function getTerms() {
    return $this->getController()->getTerms($this->vid);
  }

  /**
   * Add question to quiz.
   *
   * @TODO: Move this to QuestionIO.
   *
   * @param Question $question
   * @return boolean
   */
  public function addQuestion(Question $question) {
    $questions = $this->getQuestionIO()->getQuestionList();

    // Do not add a question if it's already been added.
    foreach ($questions as $_question) {
      if ($question->vid == $_question['vid']) {
        return FALSE;
      }
    }

    // Otherwise let's add a relationship!
    return $question->getHandler()->saveRelationships($this->qid, $this->vid);
  }

  /**
   * Find out if a quiz is available for taking or not for a specific user.
   *
   * @return bool
   *  TRUE if available
   */
  public function isAvailable($account) {
    if (!$account->uid && $this->takes > 0) {
      $msg = 'This @quiz only allows %num_attempts attempts. Anonymous users can only access quizzes that allows an unlimited number of attempts.';
      return t($msg, array('@quiz' => QUIZZ_NAME, '%num_attempts' => $this->takes));
    }

    if (entity_access('update', 'quiz_entity', $this) || $this->quiz_always) {
      return TRUE;
    }

    // Compare current GMT time to the open and close dates (which should still
    // be in GMT time).
    if ((REQUEST_TIME >= $this->quiz_close) || (REQUEST_TIME < $this->quiz_open)) {
      return t('This @quiz is closed.', array('@quiz' => QUIZZ_NAME));
    }

    return entity_access('view', 'quiz_entity', $this, $account);
  }

  public function isPassed($uid) {
    return $this->getController()->isPassed($uid, $this->vid);
  }

  /**
   * @param \stdClass $account
   * @return NULL|\Drupal\quizz\Entity\Result
   */
  public function findBestResult($account) {
    $this->getController()->findBestResult($this->qid, $account);
  }

  /**
   * Finds out if a quiz has been answered or not.
   *
   * @return bool
   */
  public function isAnswered() {
    $sql = 'SELECT 1 FROM {quiz_results} r WHERE r.quiz_vid = :vid';
    return db_query($sql, array(':vid' => $this->vid))->fetchColumn() ? TRUE : FALSE;
  }

}
