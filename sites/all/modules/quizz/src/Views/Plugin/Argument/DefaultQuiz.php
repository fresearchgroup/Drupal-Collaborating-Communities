<?php

namespace Drupal\quizz\Views\Plugin\Argument;

use views_plugin_argument_default;

class DefaultQuiz extends views_plugin_argument_default {

  function get_argument() {
    if ($quiz = menu_get_object('quiz_entity', 1)) {
      return $quiz->qid;
    }

    if (arg(0) == 'quiz' && is_numeric(arg(1))) {
      return arg(1);
    }
  }

}
