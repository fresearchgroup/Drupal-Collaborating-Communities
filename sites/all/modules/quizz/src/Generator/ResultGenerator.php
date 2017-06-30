<?php

namespace Drupal\quizz\Generator;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;

class ResultGenerator {

  /**
   * @param QuizEntity $quiz
   * @return Result
   */
  public function generate(QuizEntity $quiz) {
    /* @var $result Result */
    $result = entity_create('quiz_result', array(
        'quiz_qid'     => $quiz->qid,
        'quiz_vid'     => $quiz->vid,
        'uid'          => rand(0, 1),
        'time_start'   => REQUEST_TIME,
        'time_end'     => REQUEST_TIME + rand(15, 300),
        'released'     => '???',
        'score'        => '???',
        'is_invalid'   => FALSE,
        'is_evaluated' => '???',
        'time_left'    => 0,
    ));
    $result->save();
    return $result;
  }

}
