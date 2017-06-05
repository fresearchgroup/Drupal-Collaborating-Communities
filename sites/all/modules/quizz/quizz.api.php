<?php

use Drupal\quizz\Entity\QuizEntity;

/**
 * NOTES ON DEVELOPING EXTENSIONS FOR QUIZ
 * =======================================
 *
 * DEVELOPING NEW QUESTION TYPES:
 *
 * You need to create a new module that extends the existing
 * question type core. The multichoice question type provides a precise example.
 *
 * Here are the steps:
 *
 *  1. Create a new module
 *  2. Use your module's .install file to create the necessary tables
 *  3. Implements hook_quizz_question_info()
 *  4. Define classes that extends Drupal\quizz_question\QuestionHandler and Drupal\quizz_question\ResponseHandler.
 *
 *
 * Hooks provided by Quiz module.
 * =======================================
 *
 * These entity types provided by Quiz also have entity API hooks.
 *
 * quiz (settings for quiz entities)
 * quiz_result (quiz attempt/result)
 * quiz_result_answer (answer to a specific question in a quiz result)
 * quiz_relationship (relationship from quiz to question)
 *
 * So for example
 *
 * hook_quiz_result_presave(&$course_report)
 *   - Runs before a result is saved to the DB.
 * hook_quiz_relationship_insert($course_object_fulfillment)
 *  - Runs when a new question is added to a quiz.
 *
 * Enjoy :)
 */

/**
 * Implements hook_quizz_question_info().
 */
function hook_quizz_question_info() {
  return array(
      'long_answer' => array(
          'name'              => t('Example question type'),
          'description'       => t('An example question type that does something.'),
          'question provider' => 'ExampleAnswerQuestion',
          'response provider' => 'ExampleAnswerResponse',
          'module'            => 'quizz_question',
      ),
  );
}

/**
 * Implements hook_quizz_question_info_alter().
 */
function hook_quizz_question_info_alter(&$info) {
  // …
}

/**
 * Implements hook_quiz_begin().
 *
 * Fired when a new quiz result is created.
 */
function hook_quiz_begin(QuizEntity $quiz, $result_id) {

}

/**
 * Implements hook_quiz_finished().
 *
 * Fired after the last question is submitted.
 */
function hook_quiz_finished(QuizEntity $quiz, $score, $data) {

}

/**
 * Implements hook_quiz_scored().
 *
 * Fired when a quiz is evaluated.
 */
function hook_quiz_scored(QuizEntity $quiz, $score, $result_id) {

}

/**
 * @see \Drupal\quizz_question\ResponseHandler::getReportForm()
 * @param array $labels Associated string[]
 */
function hook_quiz_feedback_labels_alter(&$labels) {

}
