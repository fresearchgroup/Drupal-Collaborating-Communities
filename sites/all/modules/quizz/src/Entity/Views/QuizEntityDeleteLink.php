<?php

namespace Drupal\quizz\Entity\Views;

class QuizEntityDeleteLink extends QuizEntityLink {

  function render_link($quiz, $values) {
    if (entity_access('delete', 'quiz_entity', $quiz)) {
      $uri = entity_uri('quiz_entity', $quiz);
      $this->options['alter']['make_link'] = TRUE;
      $this->options['alter']['path'] = $uri['path'] . '/delete';
      $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
      return $text;
    }
  }

}
