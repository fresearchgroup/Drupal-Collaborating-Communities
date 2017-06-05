<?php

/**
 * @file
 * Views field handler that translates question status integer constants (as
 * defined in quiz.module) into their human-readable string counterparts.
 */
class quiz_views_handler_field_question_status extends views_handler_field {

  var $text_map = array();

  function pre_render(&$values) {
    $this->text_map = array(
        QUIZZ_QUESTION_RANDOM             => t('Random'), // 'Random-ly' better?
        QUIZZ_QUESTION_ALWAYS             => t('Always'),
        QUIZZ_QUESTION_NEVER              => t('Never'),
        QUIZZ_QUESTION_CATEGORIZED_RANDOM => t('Categorized random questions'),
    );
  }

  function render($values) {
    return $this->text_map[$values->{$this->field_alias}];
  }

}
