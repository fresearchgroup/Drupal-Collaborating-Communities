<?php

namespace Drupal\quizz_question;

use Drupal\quizz\Entity\Result;
use Drupal\quizz_question\Entity\Question;
use Drupal\quizz_question\Entity\QuestionType;

interface QuestionHandlerInterface {

  /**
   * Get the maximum possible score for this question.
   * @return int
   */
  public function getMaximumScore();

  /**
   * Get the form used to create a new question.
   * @param array $form_state
   * @return array Form structure
   */
  public function getCreationForm(array &$form_state = NULL);

  /**
   * Return a result report for a question response.
   *
   * The retaurned value is a form array because in some contexts the scores in
   * the form is editable
   *
   * @param Result $result
   * @param Question $question
   */
  public function getReportForm(Result $result, Question $question);

  /**
   * Provides validation for question before it is created.
   *
   * When a new question is created and initially submited, this is
   * called to validate that the settings are acceptible.
   *
   * @param array $form
   */
  public function validate(array &$form);

  /**
   * Save question type specific node properties
   * @param bool $is_new
   */
  public function onSave($is_new = FALSE);

  /**
   * To be called when new question type created.
   */
  public function onNewQuestionTypeCreated(QuestionType $question_type);

  /**
   * Method is called when user retry.
   * @param Result $result
   * @param array $element
   */
  public function onRepeatUntiCorrect(Result $result, array &$element);

  /**
   * Is this question graded?
   * Questions like Quiz Directions, Quiz Page, and Scale are not.
   * @return bool
   */
  public function isGraded();

  /**
   * Does this question type give feedback?
   * Questions like Quiz Directions and Quiz Pages do not.
   * By default, questions give feedback
   * @return bool
   */
  public function hasFeedback();

  /**
   * Delete question data from the database. Called by question's controller.
   * @param bool $delete_revision
   */
  public function delete($delete_revision);

  /**
   * Getter function returning properties to be loaded when question is loaded.
   * Called by question's controler.
   * @return array
   */
  public function load();

  /**
   * Retrieve information relevant for viewing question. Called by question's
   * controller ::buildContent().
   * @return array
   */
  public function view();
}
