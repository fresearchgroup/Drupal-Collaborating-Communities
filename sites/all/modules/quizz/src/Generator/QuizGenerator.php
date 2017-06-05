<?php

namespace Drupal\quizz\Generator;

use Drupal\quizz\Entity\QuizEntity;

class QuizGenerator {

  /**
   * @param string $quiz_type
   * @return QuizEntity
   */
  public function generate($quiz_type) {
    $quiz = entity_create('quiz_entity', array(
        'type'    => $quiz_type,
        'title'   => devel_create_greeking(rand(5, 10), TRUE),
        'uid'     => rand(0, 1),
        'created' => REQUEST_TIME,
        'changed' => REQUEST_TIME,
    ));
    $quiz->save();
    return $quiz;
  }

}
