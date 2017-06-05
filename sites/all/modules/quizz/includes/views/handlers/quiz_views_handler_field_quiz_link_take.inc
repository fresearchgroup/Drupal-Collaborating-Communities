<?php

/**
 * @file
 * Definition of quiz_views_handler_field_quiz_link_take.
 */

/**
 * Field handler to present a link to take a quiz entity.
 *
 * @ingroup views_field_handlers
 */
class quiz_views_handler_field_quiz_link_take extends views_handler_field_node_link {

  /**
   * Renders the link.
   */
  function render_link($quiz, $values) {
    // Ensure user has access to take this quiz.
    if (!quizz_take_access($quiz)) {
      return;
    }

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = "quiz/$quiz->qid/take";

    $text = !empty($this->options['text']) ? $this->options['text'] : t('Take quiz');
    return $text;
  }

}
