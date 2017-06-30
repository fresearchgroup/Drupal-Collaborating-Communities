<?php

namespace Drupal\quizz\Form;

use Drupal\quizz_question\Entity\Question;
use Drupal\quizz\Entity\Answer;
use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;
use Drupal\quizz\Form\QuizAnsweringForm\FormSubmission;
use stdClass;

class QuizAnsweringForm {

  /** @var QuizEntity */
  private $quiz;
  private $question;
  private $page_number;

  /** @var Result */
  private $result;

  /** @var int */
  private $quiz_id;

  /** @var FormSubmission */
  private $submit;

  public function __construct($quiz, $question, $page_number, $result) {
    $this->quiz = $quiz;
    $this->question = $question;
    $this->page_number = $page_number;
    $this->result = $result;
    $this->quiz_id = $quiz->qid;
  }

  /**
   * Build question list in page.
   * @param Result $result
   * @param stdClass $page
   */
  public static function findPageQuestions(Result $result, Question $page) {
    $page_id = NULL;
    $questions = array(quizz_question_load($page->qid));

    foreach ($result->layout as $item) {
      if ($item['vid'] == $page->vid) {
        $page_id = $item['qr_id'];
        break;
      }
    }

    foreach ($result->layout as $item) {
      if ($page_id == $item['qr_pid']) {
        $questions[] = quizz_question_load($item['qid']);
      }
    }

    return $questions;
  }

  /**
   * Get the form to show to the quiz taker.
   *
   * @param Question[] $questions
   *   A list of questions to get answers from.
   * @param $result_id
   *   The result ID for this attempt.
   */
  public function getForm($form, &$form_state, $questions) {
    $form['#attributes']['class'] = array('answering-form');
    $form['#quiz'] = $this->quiz;
    $form['#question'] = $this->question;
    $form['#page_number'] = $this->page_number;
    $form['#result'] = $this->result;

    foreach ($questions as $question) {
      $this->buildQuestionItem($question, $this->result->loadAnswerByQuestion($question), $form, $form_state);
    }

    // Build buttons
    $allow_skipping = isset($question->type) ? $question->type !== 'quiz_directions' : $question->type;
    $this->buildSubmitButtons($form, $allow_skipping);

    return $form;
  }

  private function buildQuestionItem(Question $question, Answer $answer, &$form, &$form_state) {
    $handler = $question->getHandler();

    // Element for a single question
    $element = $handler->getAnsweringForm($form_state, $this->result->result_id);

    $output = entity_view('quiz_question_entity', array($question), 'default', NULL, TRUE);
    unset($output['quiz_question_entity'][$question->qid]['answers']);

    $form['question'][$question->qid] = array(
        '#attributes' => array('class' => array(drupal_html_class('quiz-question-' . $question->type))),
        '#type'       => 'container',
        '#tree'       => TRUE,
        '#parents'    => array('question', $question->qid),
        'header'      => $output,
        'answer'      => $element,
    );

    // Should we disable this question?
    if (empty($this->quiz->allow_change) && quizz_result_is_question_answered($this->result, $question)) {
      // This question was already answered, and not skipped.
      $form['question'][$question->qid]['#disabled'] = TRUE;
    }

    // Attach custom fields
    field_attach_form('quiz_result_answer', $answer, $form['question'][$question->qid], $form_state);

    if ($this->quiz->mark_doubtful) {
      $form['question'][$question->qid]['is_doubtful'] = array(
          '#type'          => 'checkbox',
          '#title'         => t('doubtful'),
          '#weight'        => 1,
          '#prefix'        => '<div class="mark-doubtful checkbox enabled"><div class="toggle"><div>',
          '#suffix'        => '</div></div></div>',
          '#default_value' => $answer->is_doubtful,
      );
    }
  }

  private function buildSubmitButtons(&$form, $allow_skipping) {
    $is_last = $this->result->isLastPage($this->page_number);

    $form['navigation']['#type'] = 'actions';

    if (!empty($this->quiz->backwards_navigation) && (arg(3) != 1)) {
      // Backwards navigation enabled, and we are looking at not the first
      // question. @todo detect when on the first page.
      $form['navigation']['back'] = array(
          '#weight'                  => 10,
          '#type'                    => 'submit',
          '#value'                   => t('Back'),
          '#submit'                  => array('quiz_answer_form_submit_back'),
          '#limit_validation_errors' => array(),
      );

      if ($is_last) {
        $form['navigation']['#last'] = TRUE;
        $form['navigation']['last_text'] = array(
            '#weight' => 0,
            '#markup' => '<p><em>' . t('This is the last question. Press Finish to deliver your answers') . '</em></p>',
        );
      }
    }

    $form['navigation']['submit'] = array(
        '#weight' => 30,
        '#type'   => 'submit',
        '#value'  => $is_last ? t('Finish') : t('Next'),
        '#submit' => array('quiz_answer_form_submit'),
    );

    // @TODO: Check this
    $form['navigation']['skip'] = array(
        '#weight'                  => 20,
        '#type'                    => 'submit',
        '#value'                   => $is_last ? t('Leave blank and finish') : t('Leave blank'),
        '#access'                  => $allow_skipping,
        '#submit'                  => array('quiz_answer_form_submit_blank'),
        '#limit_validation_errors' => array(),
        '#access'                  => $this->quiz->allow_skipping,
    );

    // Question handler may provide extra buttons, merge buttons to master form.
    foreach ($form['question'] as $id => &$elements) {
      if (!empty($elements['answer']['navigation'])) {
        $form['navigation'] += $elements['answer']['navigation'];
        unset($elements['answer']['navigation']);
      }

      if (!empty($elements['answer'][$id]['navigation'])) {
        $form['navigation'] += $elements['answer'][$id]['navigation'];
        unset($elements['answer'][$id]['navigation']);
      }
    }

    // Display a confirmation dialogue if this is the last question and a user
    // is able to navigate backwards but not forced to answer correctly.
    if ($is_last && $this->quiz->backwards_navigation && !$this->quiz->repeat_until_correct) {
      $form['#attributes']['class'][] = 'quiz-answer-confirm';
      $form['#attributes']['data-confirm-message'] = t("By proceeding you won't be able to go back and edit your answers.");
      $form['#attached']['js'][] = drupal_get_path('module', 'quizz') . '/misc/js/quiz.answering.confirm.js';
    }
  }

  /**
   * Validation callback for quiz question submit.
   */
  public function formValidate(&$form, &$form_state) {
    $time_reached = $this->quiz->time_limit && (REQUEST_TIME > ($this->result->time_start + $this->quiz->time_limit));

    // Let's not validate anything, because the input won't get saved in submit either.
    if ($time_reached) {
      return;
    }

    // There was an answer submitted.
    foreach (array_keys($form_state['values']['question']) as $question_id) {
      if ($_question = quizz_question_load($question_id)) {
        $_question->getHandler()->validateAnsweringForm($form, $form_state);
      }
    }
  }

  public function getSubmit() {
    if (null === $this->submit) {
      $this->submit = new FormSubmission($this->quiz, $this->result, $this->page_number);
    }
    return $this->submit;
  }

}
