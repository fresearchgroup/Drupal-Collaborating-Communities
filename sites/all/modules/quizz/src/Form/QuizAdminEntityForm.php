<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Form\QuizForm\FormValidation;

class QuizAdminEntityForm {

  public function getForm($form, $form_state) {
    // basic form
    $dummy_quiz = quizz_entity_controller()->getSettingIO()->getSystemDefaults(FALSE);

    $entity_form = new QuizForm($dummy_quiz);
    $form += $entity_form->get($form, $form_state, 'add');

    $form['direction'] = array(
        '#markup' => t('Here you can change the default @quiz settings for new users.', array('@quiz' => QUIZZ_NAME)),
        '#weight' => -10,
    );

    // unset values we can't or won't let the user edit default values for
    unset($form['title']);
    unset($form['body_field']);
    unset($form['taking']['aid']);
    unset($form['taking']['addons']);
    unset($form['quiz_availability']['quiz_open']);
    unset($form['quiz_availability']['quiz_close']);
    unset($form['result_options']);
    unset($form['number_of_random_questions']);
    unset($form['remember_global']);
    unset($form['actions']['submit']);
    unset($form['actions']['delete']);

    $form['remember_settings']['#type'] = 'value';
    $form['remember_settings']['#default_value'] = TRUE;
    $form['submit'] = array('#type' => 'submit', '#value' => t('Save'));

    return $form;
  }

  public function validateForm($form, &$form_state) {
    $quiz = entity_create('quiz_entity', array(
        'qid'               => $form['#quiz']->qid,
        'vid'               => $form['#quiz']->vid,
        'remember_settings' => 0,
        'remember_global'   => 1,
      ) + $form_state['values']);
    $validator = new FormValidation($form, $form_state, $quiz);
    $validator->validate();
  }

  public function submitForm($form, &$form_state) {
    $quiz = entity_create('quiz_entity', array(
        'qid'               => $form['#quiz']->qid,
        'vid'               => $form['#quiz']->vid,
        'remember_settings' => 0,
        'remember_global'   => 1,
      ) + $form_state['values']);

    quizz_entity_controller()->getSettingIO()->updateUserDefaultSettings($quiz);
    $form_state['quiz'] = $quiz;
  }

}
