<?php

namespace Drupal\quizz\Entity\Views;

use views_handler_field_node_link;

class QuizEntityLink extends views_handler_field_node_link {

  function render_link($quiz, $values) {
    if (entity_access('view', 'quiz_entity', $quiz)) {
      $uri = entity_uri('quiz_entity', $quiz);
      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = $uri['path'];
      $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
      return $text;
    }
  }

}
