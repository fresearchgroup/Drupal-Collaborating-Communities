<?php

namespace Drupal\quizz\Entity\Result;

use Drupal\quizz\Controller\ResultBaseController;

/**
 * Callback for:
 *
 *  - quiz-result/%
 *  - user/%/quiz-results/%quizz_result/view
 *
 * Show result page for a given result. Check issue #2362097
 */
class Render extends ResultBaseController {

  public function render(array &$output) {
    $bc = drupal_get_breadcrumb();
    $bc[] = $this->quiz->link();
    drupal_set_breadcrumb($bc);

    if (!$this->score['is_evaluated']) {
      $msg = t('Parts of this @quiz have not been evaluated yet. The score below is not final.', array(
          '@quiz' => QUIZZ_NAME
      ));
      drupal_set_message($msg, 'warning');
    }

    $this->doRenderScore($output);
    $this->doRenderFeedback($output, $summary = $this->getSummaryText());
    $output['feedback_form'] = array(
        drupal_get_form('quizz_report_form', $this->result, $this->getAnswers())
    );
  }

  private function doRenderScore(&$output) {
    global $user;

    if (!$this->result->canReview('score')) {
      return;
    }

    $params = array(
        '%num_correct'    => $this->score['numeric_score'],
        '%question_count' => $this->score['possible_score'],
        '!username'       => ($user->uid == $this->author->uid) ? t('You') : theme('username', array('account' => $this->author)),
        '@score'          => $this->score['percentage_score'],
        '!yourtotal'      => ($user->uid == $this->author->uid) ? t('Your') : t('Total'),
    );

    $output['score'] = array(
        'possible' => array(
            '#prefix' => '<div id="quiz_score_possible">',
            '#markup' => t('!username got %num_correct of %question_count possible points.', $params),
            '#suffix' => '</div>',
        ),
        'percent'  => array(
            '#prefix' => '<div id="quiz_score_percent">',
            '#markup' => t('!yourtotal score: @score%', $params),
            '#suffix' => '</div>',
        ),
    );
  }

  private function doRenderFeedback(&$output, $summary) {
    if (!$this->result->canReview('quiz_feedback')) {
      return;
    }

    if (isset($summary['passfail'])) {
      $output['feedback']['passfail'] = array(
          '#prefix' => '<div id="quiz_summary">',
          '#markup' => $summary['passfail'],
          '#suffix' => '</div>',
      );
    }

    if (isset($summary['result'])) {
      $output['feedback']['result'] = array(
          '#prefix' => '<div id="quiz_summary">',
          '#markup' => $summary['result'],
          '#suffix' => '</div>',
      );
    }
  }

}
