<?php

namespace Drupal\quizz\Controller;

use Drupal\quizz\Entity\Result;
use Drupal\quizz_question\Entity\Question;

class QuestionFeedbackController {

  /** @var Result */
  private $result;

  public function __construct(Result $result) {
    $this->result = $result;
  }

  public function render($page_number) {
    $question = quizz_question_load($this->result->layout[$page_number]['qid']);
    return $this->buildFormArray($question);
  }

  public function buildFormArray(Question $question) {
    require_once DRUPAL_ROOT . '/' . drupal_get_path('module', 'quizz') . '/includes/quizz.pages.inc';
    return drupal_get_form('quizz_report_form', $this->result, array($question));
  }

}
