<?php

namespace Drupal\quizz\Helper;

class MailHelper {

  /**
   * Build subject and body for notice email.
   *
   * @param \stdClass $account
   * @param \stdClass $quiz
   * @param type $score
   * @param type $result_id
   * @param type $target
   * @return array($subject, $body)
   */
  public function buildNotice($account, $quiz, $score, $result_id, $target) {
    $body = field_get_items('quiz_entity', $quiz, 'quiz_body');
    $substitutions = array(
        '!title'      => $quiz->title,
        '!sitename'   => variable_get('site_name', 'Quiz'),
        '!taker'      => $account->name,
        '!author'     => $quiz->name,
        '!title'      => check_plain($quiz->title),
        '!date'       => format_date(REQUEST_TIME),
        '!desc'       => $body ? $body[0]['value'] : '',
        '!correct'    => isset($score['numeric_score']) ? $score['numeric_score'] : 0,
        '!total'      => $score['possible_score'],
        '!percentage' => $score['percentage_score'],
        '!url'        => url("user/{$account->uid}/quiz-results/{$result_id}/view", array('absolute' => TRUE)),
        '!minutes'    => db_query("SELECT CEIL((time_end - time_start)/60) FROM {quiz_results} WHERE result_id = :result_id AND time_end", array(':result_id' => $result_id))->fetchField()
    );
    $type = $target !== 'author' ? '_taker' : '';
    $test = variable_get('quiz_email_results_body' . $type, $this->formatBody($target, $account));

    return array(
        t(variable_get('quiz_email_results_subject' . $type, $this->formatSubject($target, $account)), $substitutions, array('langcode' => $account->language)),
        t($test, $substitutions, array('langcode' => $account->language))
    );
  }

  public function formatSubject($target, $account) {
    if ($target === 'author') {
      return t('!title Results Notice from !sitename');
    }

    if ($target === 'taker') {
      return t('!title Results Notice from !sitename');
    }
  }

  public function formatBody($target, $account) {
    if ($target === 'author') {
      return t('Dear !author') . "\n\n" .
        t('!taker attended the @quiz !title on !date', array('@quiz' => QUIZZ_NAME)) . "\n" .
        t('Test Description : !desc') . "\n" .
        t('!taker got !correct out of !total points in !minutes minutes. Score given in percentage is !percentage') . "\n" .
        t('You can access the result here !url') . "\n";
    }

    if ($target === 'taker') {
      return t('Dear !taker') . "\n\n" .
        t('You attended the @quiz !title on !date', array('@quiz' => QUIZZ_NAME)) . "\n" .
        t('Test Description : !desc') . "\n" .
        t('You got !correct out of !total points in !minutes minutes. Score given in percentage is !percentage') . "\n" .
        t('You can access the result here !url') . "\n";
    }
  }

}
