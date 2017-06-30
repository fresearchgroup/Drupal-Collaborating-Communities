<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Entity\Result;
use Drupal\quizz_question\Entity\Question;

class QuizReportForm {

  /**
   * Form for showing feedback, and for editing the feedback if necessary…
   *
   * @param array $form
   * @param array $form_state
   * @param Result $result
   * @param Question[] $questions
   * @return array
   */
  public function getForm($form, $form_state, Result $result, $questions) {
    $form['#tree'] = TRUE;

    foreach ($questions as $question) {
      $form_to_add = $question->getHandler()->getReportForm($result, $question);

      if (isset($form_to_add['submit'])) {
        $show_submit = TRUE;
      }

      if (!isset($form_to_add['#no_report'])) {
        $form_to_add['#element_validate'][] = 'quizz_report_form_element_validate';
        $form[] = $form_to_add;
      }
    }

    // The submit button is only shown if one or more of the questions has input elements
    if (!empty($show_submit)) {
      $form['submit'] = array('#type' => 'submit', '#value' => t('Save score'));
    }

    if (arg(4) === 'feedback') {
      // @todo figure something better than args.
      $quiz = quizz_load(quizz_get_id_from_url());
      $quiz_id = $quiz->qid;
      if (empty($_SESSION['quiz'][$quiz_id])) { // Quiz is done.
        $form['finish'] = array('#type' => 'submit', '#value' => t('Finish'));
      }
      else {
        $form['next'] = array('#type' => 'submit', '#value' => t('Next question'));
      }
    }

    return $form;
  }

  /**
   * Submit handler to go to the next question from the question feedback.
   */
  public function formSubmitFeedback($form, &$form_state) {
    $quiz_id = quizz_get_id_from_url();
    $form_state['redirect'] = "quiz/{$quiz_id}/take/" . $_SESSION['quiz'][$quiz_id]['current'];
  }

  /**
   * Validate a single question sub-form.
   */
  public static function validateElement(&$element, &$form_state) {
    $question = quizz_question_load($element['qid']['#value'], $element['vid']['#value']);
    if ($handler = $question->getResponseHandler($element['result_id']['#value'])) {
      $handler->validateReportForm($element, $form_state);
    }
  }

  /**
   * Submit the report form
   *
   * We go through the form state values and submit all questiontypes with
   * validation functions declared.
   */
  public function formSubmit($form, &$form_state) {
    global $user;

    $quiz = $result = NULL;

    foreach ($form_state['values'] as $key => $q_values) {
      // Questions has numeric keys in the report form. Or questions store the
      // name of the validation function with the key 'submit'. Or the submit
      // function is not exist
      if (!is_numeric($key) || !isset($q_values['submit']) || !function_exists($q_values['submit'])) {
        continue;
      }

      if (NULL === $result) {
        $result = quizz_result_load($q_values['result_id']);
        $quiz = $result->getQuiz();
      }

      $q_values['quiz'] = $quiz;

      // We call the submit function provided by the question
      call_user_func($q_values['submit'], $q_values);
    }

    // Scores may have been changed. We take the necessary actions
    $this->updateLastTotalScore($result->result_id, $quiz->vid);
    $changed = db_update('quiz_results')
      ->fields(array('is_evaluated' => 1))
      ->condition('result_id', $result->result_id)
      ->execute();
    $results_got_deleted = $result->maintenance($user->uid);

    // A message saying the quiz is unscored has already been set. We unset it here…
    if ($changed > 0) {
      $this->removeUnscoredMessage();
    }

    // Notify the user if results got deleted as a result of him scoring an answer.
    $add = $quiz->keep_results == QUIZZ_KEEP_BEST && $results_got_deleted ? ' ' . t('Note that this @quiz is set to only keep each users best answer.', array('@quiz' => QUIZZ_NAME)) : '';

    $score_data = $this->getScoreArray($result->result_id, $quiz->vid, TRUE);

    module_invoke_all('quiz_scored', $quiz, $score_data, $result->result_id);

    drupal_set_message(t('The scoring data you provided has been saved.') . $add);
    if (user_access('score taken quiz answer') && !user_access('view any quiz results')) {
      if ($result && $result->uid == $user->uid) {
        $form_state['redirect'] = 'quiz-result/' . $result->result_id;
      }
    }
  }

  /**
   * Submit handler to go to the quiz results from the last question's feedback.
   */
  public function formEndSubmit($form, &$form_state) {
    $result_id = $_SESSION['quiz']['temp']['result_id'];
    $form_state['redirect'] = "quiz-result/{$result_id}";
  }

  /**
   * Helper function to remove the message saying the quiz haven't been scored
   */
  private function removeUnscoredMessage() {
    if (!empty($_SESSION['messages']['warning'])) {
      // Search for the message, and remove it if we find it.
      foreach ($_SESSION['messages']['warning'] as $key => $val) {
        if ($val == t('This @quiz has not been scored yet.', array('@quiz' => QUIZZ_NAME))) {
          unset($_SESSION['messages']['warning'][$key]);
        }
      }
      // Clean up if the message array was left empty
      if (empty($_SESSION['messages']['warning'])) {
        unset($_SESSION['messages']['warning']);
        if (empty($_SESSION['messages'])) {
          unset($_SESSION['messages']);
        }
      }
    }
  }

  /**
   * Returns an array of score information for a quiz
   *
   * @param int $result_id
   * @param int $quiz_vid
   * @param int $is_evaluated
   * @return array
   */
  private function getScoreArray($result_id, $quiz_vid, $is_evaluated) {
    $properties = db_query(
      'SELECT max_score, number_of_random_questions
          FROM {quiz_entity_revision}
          WHERE vid = :vid', array(':vid' => $quiz_vid))->fetchObject();

    $total_score = db_query(
      'SELECT SUM(points_awarded)
          FROM {quiz_answer_entity}
          WHERE result_id = :result_id', array(':result_id' => $result_id))->fetchField();

    $question_count = $properties->number_of_random_questions;
    $question_count += quizz_entity_controller()->getStats()->countAlwaysQuestions($quiz_vid);

    return array(
        'question_count'   => $question_count,
        'possible_score'   => $properties->max_score,
        'numeric_score'    => $total_score,
        'percentage_score' => ($properties->max_score == 0) ? 0 : round(($total_score * 100) / $properties->max_score),
        'is_evaluated'     => $is_evaluated,
    );
  }

  /**
   * Updates the total score using only one mySql query.
   *
   * @param $result_id
   * @param int $quiz_vid
   *  Quiz version ID
   */
  private function updateLastTotalScore($result_id, $quiz_vid) {
    $subq1 = db_select('quiz_answer_entity', 'a');
    $subq1
      ->condition('a.result_id', $result_id)
      ->addExpression('SUM(a.points_awarded)');

    $score = $subq1->execute()->fetchField();
    $max_score = quizz_load(NULL, $quiz_vid)->max_score;
    $final_score = round(100 * ($score / $max_score));

    db_update('quiz_results')
      ->expression('score', $final_score)
      ->condition('result_id', $result_id)
      ->execute();
  }

}
