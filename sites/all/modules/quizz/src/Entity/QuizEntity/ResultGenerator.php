<?php

namespace Drupal\quizz\Entity\QuizEntity;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;
use EntityFieldQuery;
use RuntimeException;

/**
 * Generate result entity with dummy page/question layout.
 *
 * This class is used when use start taking a quiz.
 */
class ResultGenerator {

  /**
   * @param QuizEntity $quiz
   * @param Result $result
   * @return Result
   * @throws RuntimeException
   */
  public function generate(QuizEntity $quiz, $account, Result $base_result = NULL) {
    if (!$questions = $quiz->getQuestionIO()->getQuestionList()) {
      throw new RuntimeException(t(
        'No questions were found. Please !assign before trying to take this @quiz.', array(
          '@quiz'   => QUIZZ_NAME,
          '!assign' => l(t('assign questions'), 'quiz/' . $quiz->identifier() . '/questions')
      )));
    }

    // Build on the last attempt the user took. If this quiz has build on last
    // attempt set, we need to search for a previous attempt with the same
    // version of the current quiz.
    if ($quiz->build_on_last) {
      $query = new EntityFieldQuery();
      $query_results = $query->entityCondition('entity_type', 'quiz_result')
        ->propertyCondition('uid', $account->uid)
        ->propertyCondition('quiz_qid', $quiz->qid)
        ->propertyCondition('quiz_vid', $quiz->vid)
        ->propertyOrderBy('time_start', 'DESC')
        ->range(0, 1)
        ->execute();

      // Found an existing result we need to rebuild from. We also need to retain the version.
      if (!empty($query_results['quiz_result'])) {
        $prev_result = quizz_result_load(key($query_results['quiz_result']));
      }
    }

    return $this->doGenerate($quiz, $questions, $account, $base_result, isset($prev_result) ? $prev_result : NULL);
  }

  private function doGenerate(QuizEntity $quiz, $questions, $account, Result $base_result = NULL, Result $prev_result = NULL) {
    // correct item numbers
    $count = $display_count = 0;
    $question_list = array();

    foreach ($questions as &$question) {
      $display_count++;
      $question['number'] = ++$count;
      if ($question['type'] !== 'quiz_page') {
        $question['display_number'] = $display_count;
      }
      $question_list[$count] = $question;
    }

    // Write the layout for this result.
    $result = NULL !== $base_result ? $base_result : entity_create('quiz_result', array('type' => $quiz->type));
    $result->quiz_qid = $quiz->identifier();
    $result->quiz_vid = $quiz->vid;
    $result->uid = $account->uid;
    $result->time_start = REQUEST_TIME;
    $result->layout = $question_list;
    $result->save();

    foreach ($question_list as $i => $question) {
      entity_create('quiz_result_answer', array(
          'result_id'    => $result->result_id,
          'question_qid' => $question['qid'],
          'question_vid' => $question['vid'],
          'tid'          => isset($question['tid']) ? $question['tid'] : NULL,
          'number'       => $i,
      ))->save();
    }

    if (NULL !== $prev_result) {
      $this->cloneResult($quiz, $prev_result, $result);
    }

    $_SESSION['quiz'][$quiz->qid] = array('result_id' => $result->result_id, 'current' => 1);
    module_invoke_all('quiz_begin', $quiz, $result->result_id);

    return quizz_result_load($result->result_id);
  }

  /**
   * Clone a result, and its correct answers. Do not finish.
   */
  private function cloneResult(QuizEntity $quiz, $prev_result, $result) {
    foreach ($prev_result->layout as $question_info) {
      $question = quizz_question_load($question_info['qid'], $question_info['vid']);
      $handler = $question->getResponseHandler($prev_result->result_id);

      // Override the existing response.
      if (('all' === $quiz->build_on_last) || $handler->isCorrect()) {
        $handler->setResultId($result->result_id);
        $handler->save();
      }
    }

    return $result;
  }

}
