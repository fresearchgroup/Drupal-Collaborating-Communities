<?php

namespace Drupal\quizz\Form\QuizForm;

use Drupal\quizz\Entity\QuizEntity;

class FormValidation {

  /** @var QuizEntity */
  private $quiz;

  /** @var array */
  private $form;

  /** @var array */
  private $form_state;

  public function __construct($form, &$form_state, QuizEntity $quiz = NULL) {
    $this->form = $form;
    $this->form_state = &$form_state;

    if (NULL !== $quiz) {
      $this->quiz = $quiz;
    }
    else {
      $this->quiz = entity_ui_controller('quiz_entity')->entityFormSubmitBuildEntity($form, $form_state);
      $this->quiz->result_options = $this->quiz->result_options['ro_tabs'];
      unset($this->quiz->result_options['result_options__ro_tabs__active_tab']);
    }
  }

  public function validate() {
    $this->validateResultOptions();
    $this->validateTakingOptions();

    // Notify field widgets to validate their data.
    field_attach_form_validate('quiz_entity', $this->quiz, $this->form, $this->form_state);
  }

  private function validateResultOptions() {
    if (!empty($this->quiz->pass_rate)) {
      if (!quizz_valid_integer($this->quiz->pass_rate, 0, 100)) {
        form_set_error('pass_rate', t('"Passing rate" must be a number between 0 and 100.'));
      }
    }

    if (isset($this->quiz->result_options) && count($this->quiz->result_options) > 0) {
      $taken_values = array();
      foreach ($this->quiz->result_options as $option) {
        $this->validateResultOption($option, $taken_values);
      }
    }
  }

  private function validateResultOption($option, &$taken_values) {
    if (empty($option['option_name']) && !$this->isEmptyHTML($option['option_summary']['value'])) {
      return form_set_error('option_summary', t('Range has a summary, but no name.'));
    }

    if (empty($option['option_name'])) {
      return;
    }

    if (empty($option['option_summary']['value'])) {
      form_set_error('option_summary', t('Range has no summary text.'));
    }

    if ($this->quiz->pass_rate && (isset($option['option_start']) || isset($option['option_end']))) {
      // Check for a number between 0-100.
      foreach (array('option_start' => 'start', 'option_end' => 'end') as $bound => $bound_text) {
        if (!quizz_valid_integer($option[$bound], 0, 100)) {
          form_set_error($bound, t('The range %start value must be a number between 0 and 100.', array(
              '%start' => $bound_text
          )));
        }
      }

      // Check that range end >= start.
      if ($option['option_start'] > $option['option_end']) {
        form_set_error('option_start', t('The start must be less than the end of the range.'));
      }

      // Check that range doesn't collide with any other range.
      $option_range = range($option['option_start'], $option['option_end']);
      if ($intersect = array_intersect($taken_values, $option_range)) {
        form_set_error('option_start', t('The ranges must not overlap each other. (%intersect)', array('%intersect' => implode(',', $intersect))));
      }
      else {
        $taken_values = array_merge($taken_values, $option_range);
      }
    }
  }

  private function validateTakingOptions() {
    // Don't check dates if the quiz is always available.
    if (!$this->quiz->quiz_always) {
      $_open = mktime(0, 0, 0, $this->quiz->quiz_open['month'], $this->quiz->quiz_open['day'], $this->quiz->quiz_open['year']);
      $_close = mktime(0, 0, 0, $this->quiz->quiz_close['month'], $this->quiz->quiz_close['day'], $this->quiz->quiz_close['year']);
      if ($_open > $_close) {
        form_set_error('quiz_close', t('"Close date" must be later than the "open date".'));
      }
    }

    if (isset($this->quiz->time_limit)) {
      if (!quizz_valid_integer($this->quiz->time_limit, 0)) {
        form_set_error('time_limit', t('"Time limit" must be a positive number.'));
      }
    }

    if ($this->quiz->allow_jumping && empty($this->quiz->allow_skipping)) {
      // @todo when we have pages of questions, we have to check that jumping is
      // not enabled, and randomization is not enabled unless there is only 1 page
      form_set_error('allow_skipping', t('If jumping is allowed, skipping must also be allowed.'));
    }
  }

  /**
   * Helper function used when figuring out if a textfield or textarea is empty.
   *
   * Solves a problem with some wysiwyg editors inserting spaces and tags
   * without content.
   *
   * @param string $html The html to evaluate
   * @return bool
   *  TRUE if the field is empty(can still be tags there) false otherwise.
   */
  private function isEmptyHTML($html) {
    return drupal_strlen(trim(str_replace('&nbsp;', '', strip_tags($html, '<img><object><embed>')))) == 0;
  }

}
