<?php

namespace Drupal\quizz_question;

use Drupal\quizz\Entity\Answer;
use Drupal\quizz_question\Entity\Question;
use stdClass;

interface ResponseHandlerInterface {

  /**
   * Validates response from a quiz taker. If the response isn't valid the quiz
   * taker won't be allowed to proceed.
   * @return bool
   */
  public function isValid();

  /**
   * Check to see if the answer is marked as correct.
   * @return bool
   */
  public function isCorrect();

  /**
   * Indicate whether the response has been evaluated (scored) yet.
   * Questions that require human scoring (e.g. essays) may need to manually
   * toggle this.
   *
   * @return bool
   */
  public function isEvaluated();

  /**
   * Save the current response.
   * Method is called when user's answer is saved.
   */
  public function save();

  /**
   * Delete the response.
   * Method is called when user's answer is deleted.
   */
  public function delete();

  /**
   * On answer entity being loaded.
   *
   * @TODO: Update response handler to use this method.
   */
  public function onLoad(Answer $answer);

  /**
   * Calculate the score for the response.
   * @return int
   */
  public function score();

  /**
   * Returns stored max score if it exists, if not the max score is calculated and returned.
   *
   * @param bool $weight_adjusted
   *  If the returned max score shall be adjusted according to the max_score the question has in a quiz
   * @return int
   */
  public function getQuestionMaxScore($weight_adjusted = TRUE);

  /**
   * Get the user's response.
   * @return mixed
   */
  public function getResponse();

  /**
   * Creates the report form for the admin pages, and for when a user gets
   * feedback after answering questions.
   *
   * The report is a form to allow editing scores and the likes while viewing
   * the report form
   *
   * @return array $form
   */
  public function getReportForm();

  /**
   * @return array
   */
  public function getReportFormScore();

  /**
   * Get the submit function for the report Form.
   *
   * @TODO: Should be a direct callback, ref to a global function, not cool.
   *
   * @return string
   *  Submit function as a string, empty string if no submit function
   */
  public function getReportFormSubmit();

  /**
   * Represent the response as a stdClass object.
   *
   * Convert data to an object that has the following properties:
   *  score, result_id, question_qid, question_vid, is_correct, …
   *
   * @return Answer
   */
  public function toBareObject();

  /**
   * Get question score.
   *
   * @return stdClass
   */
  public function getQuestionScore(Question $question);
}
