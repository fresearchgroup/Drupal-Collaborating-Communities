<?php

namespace Drupal\quizz\Controller;

use Drupal\quizz\Controller\QuizTakeBaseController;
use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;

class QuizTakeQuestionController extends QuizTakeBaseController {

  /** @var QuizEntity */
  private $quiz;
  private $question;
  private $page_number;

  /** @var Result */
  private $result;
  private $quiz_uri;
  private $quiz_id;

  public function __construct(QuizEntity $quiz, Result $result, $question_number, $question) {
    drupal_set_title($quiz->title);

    $this->quiz = $quiz;
    $this->result = $result;
    $this->page_number = $question_number;
    $this->question = $question;

    // Legacy code
    $this->quiz_uri = 'quiz/' . $quiz->qid;
    $this->quiz_id = $quiz->qid;

    // Question disappeared or invalid session. Start over.
    if (!$question) {
      drupal_set_message(t('Invalid session.'), 'error');
      unset($_SESSION['quiz'][$this->quiz_id]);
      drupal_goto($this->quiz_uri);
    }

    if (module_exists('context')) {
      context_set('context', "quizz_quiz_taking", TRUE);
      context_set('context', "quizz_quiz_taking_{$quiz->type}", TRUE);
    }
  }

  public function render() {
    $content = $questions = array();

    // Mark this as the current question.
    $this->redirect($this->quiz, $this->page_number);

    // Added the progress info to the view.
    $i = 0;
    foreach ($this->result->layout as $idx => $question) {
      if (empty($question['qr_pid'])) {
        $questions[$idx] = ++$i; // Question has no parent. Show it in the jumper.
      }
    }

    $content['progress']['#markup'] = theme('quizz_progress', array(
        'quiz'          => $this->quiz,
        'questions'     => $questions,
        'current'       => $this->page_number,
        'allow_jumping' => $this->quiz->allow_jumping,
        'pager'         => count($questions) >= $this->quiz->getQuizType()->getConfig('quiz_pager_start', 100),
        'time_limit'    => $this->quiz->time_limit,
    ));

    $content['progress']['#weight'] = -50;

    if ($this->quiz->getQuizType()->getConfig('quiz_has_timer', 0)) {
      if (function_exists('jquery_countdown_add') && $this->quiz->time_limit) {
        $this->attachJs($this->result->time_start + $this->quiz->time_limit - REQUEST_TIME);
      }
    }

    $question_form = drupal_get_form('quiz_answer_form', $this->quiz, $this->question, $this->page_number, $this->result);
    $content['body']['question']['#markup'] = drupal_render($question_form);

    return $content;
  }

  /**
   * @param int $time
   */
  private function attachJs($time) {
    jquery_countdown_add('.countdown', array(
        'until'    => $time,
        'onExpiry' => 'quiz_take_finished',
        'compact'  => TRUE,
        'layout'   => t('Time left') . ': {hnn}{sep}{mnn}{sep}{snn}'
    ));

    // These are the two button op values that are accepted for answering questions.
    $vars = array('quiz_button_1' => t('Finish'), 'quiz_button_2' => t('Next'));
    drupal_add_js($vars, array('type' => 'setting'));
    drupal_add_js(drupal_get_path('module', 'quizz') . '/misc/js/quiz.take.count-down.js');
  }

}
