<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Form\QuizTypeForm\FormDefinition;

class QuizTypeForm {

  public function get($form, &$form_state, $quiz_type, $op) {
    $obj = new FormDefinition($quiz_type);
    return $obj->get($op);
  }

  /**
   * Form API submit callback for the type form.
   */
  public function submit(&$form, &$form_state) {
    $quiz_type = entity_ui_form_submit_build_entity($form, $form_state);
    $quiz_type->description = filter_xss_admin($quiz_type->description);
    $quiz_type->help = filter_xss_admin($quiz_type->help);

    if (isset($quiz_type->multilingual)) {
      $quiz_type->data['multilingual'] = (int) $quiz_type->multilingual;
      unset($quiz_type->multilingual);
    }

    $quiz_type->data['configuration'] = array_merge($quiz_type->configuration, $quiz_type->views);
    $quiz_type->save();
    $form_state['redirect'] = 'admin/quizz/types';
  }

}
