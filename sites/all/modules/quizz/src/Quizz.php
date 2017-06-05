<?php

namespace Drupal\quizz;

use Drupal\quizz\Helper\HookImplementation;
use Drupal\quizz\Helper\MailHelper;
use Drupal\quizz\Helper\QuestionCategoryFieldInfo;
use Drupal\quizz\Helper\QuizHelper;
use Drupal\quizz\Quizz;

/**
 * Wrapper for helper classes. We just use classes to organise functions, make
 * them easier to access, able to override, there is no OOP in helper classes
 * yet.
 *
 * Quiz.quizHelper — Helper for quiz entity/object.
 * Quiz.mailHelper — Build/format email messages.
 * Quiz.quizHelper.settingHelper - Get/Set/… quiz settings.
 * Quiz.quizHelper.resultHelper — Helper methods for quiz's results.
 * Quiz.quizHelper.accessHelper — Access helpers
 *
 * Extends this class and sub classes if you would like override things.
 *
 * You should not create object directly from this class, use quiz() factory
 * function instead — which support overriding from module's side.
 */
class Quizz {

  private $hookImplementation;
  private $quizHelper;
  private $mailHelper;
  private $questionCategoryField;

  /**
   * @return HookImplementation
   */
  public function getHookImplementation() {
    if (null === $this->hookImplementation) {
      $this->hookImplementation = new HookImplementation();
    }
    return $this->hookImplementation;
  }

  public function setHookImplementation($hookImplementation) {
    $this->hookImplementation = $hookImplementation;
    return $this;
  }

  /**
   * @return QuizHelper
   */
  public function getQuizHelper() {
    if (null === $this->quizHelper) {
      $this->quizHelper = new QuizHelper();
    }
    return $this->quizHelper;
  }

  /**
   * Inject quizHelper.
   *
   * @param QuizHelper $quizHelper
   * @return Quizz
   */
  public function setQuizHelper($quizHelper) {
    $this->quizHelper = $quizHelper;
    return $this;
  }

  /**
   * @return MailHelper
   */
  public function getMailHelper() {
    if (null === $this->mailHelper) {
      $this->mailHelper = new MailHelper();
    }
    return $this->mailHelper;
  }

  /**
   * Inject mail helper.
   *
   * @param MailHelper $mailHelper
   * @return Quizz
   */
  public function setMailHelper($mailHelper) {
    $this->mailHelper = $mailHelper;
    return $this;
  }

  /**
   * Format a number of seconds to a hh:mm:ss format.
   *
   * @param int $time_in_sec
   * @return string Time in "min : sec" format.
   */
  public function formatDuration($time_in_sec) {
    $hours = intval($time_in_sec / 3600);
    $min = intval(($time_in_sec - $hours * 3600) / 60);
    $sec = $time_in_sec % 60;
    if (strlen($min) == 1) {
      $min = '0' . $min;
    }
    if (strlen($sec) == 1) {
      $sec = '0' . $sec;
    }
    return "$hours:$min:$sec";
  }

  /**
   * Retrieve list of vocabularies for all quiz question types.
   *
   * @return
   *   An array containing a vocabulary list.
   */
  function getVocabularies() {
    $vocabularies = array();
    $types = array_keys(quizz_question_get_handler_info());
    foreach ($types as $type) {
      foreach (taxonomy_get_vocabularies($type) as $vid => $vocabulary) {
        $vocabularies[$vid] = $vocabulary;
      }
    }
    return $vocabularies;
  }

  /**
   * @return QuestionCategoryFieldInfo
   */
  public function getQuestionCategoryField() {
    if (NULL == $this->questionCategoryField) {
      $this->questionCategoryField = new QuestionCategoryFieldInfo();
    }
    return $this->questionCategoryField;
  }

}
