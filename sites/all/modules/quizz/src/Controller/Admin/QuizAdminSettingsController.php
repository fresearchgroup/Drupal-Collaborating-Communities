<?php

namespace Drupal\quizz\Controller\Admin;

class QuizAdminSettingsController {

  /**
   * This builds the main settings form for the quiz module.
   */
  public function getForm($form, &$form_state) {
    $links = array('!userpoints' => l(t('UserPoints'), 'http://drupal.org/project/userpoints', array(
            'attributes' => array('target' => '_blank'))
    ));

    $form['quiz_has_userpoints'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('User Points'),
        '#default_value' => variable_get('quiz_has_userpoints', 0),
        '#description'   => t('!userpoints is an <strong>optional</strong> module for Quiz. It provides ways for users to gain or lose points for performing certain actions on your site like completing a Quiz.', $links),
        '#disabled'      => !module_exists('userpoints'),
    );

    $form['quiz_name'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Display name'),
        '#default_value' => QUIZZ_NAME,
        '#description'   => t('Change the name of the quiz type. Do you call it <em>test</em> or <em>assessment</em> instead? Change the display name of the module to something else. By default, it is called <em>Quiz</em>.'),
        '#required'      => TRUE,
    );

    $form['quiz_email_settings'] = array(
        '#type'        => 'vertical_tabs',
        '#title'       => t('Notifications'),
        '#description' => t('Send results to quiz author/attendee via email. Configure email subject and body.'),
    );

    $form['quiz_email_settings']['taker'] = array(
        '#type'  => 'fieldset',
        '#title' => t('Email for Quiz takers'),
    );

    $form['quiz_email_settings']['taker']['quiz_email_results'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Email results to quiz takers'),
        '#default_value' => variable_get('quiz_email_results', 0),
        '#description'   => t('Check this to send users their results at the end of a quiz.')
    );

    $form['quiz_email_settings']['taker']['quiz_email_results_subject_taker'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Configure email subject'),
        '#description'   => t('This format will be used when sending quiz results at the end of a quiz.'),
        '#default_value' => variable_get('quiz_email_results_subject_taker', $this->formatEmailResults('subject', 'taker')),
    );

    $form['quiz_email_settings']['taker']['quiz_email_results_body_taker'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Configure email Format'),
        '#description'   => t('This format will be used when sending @quiz results at the end of a quiz. !title(quiz title), !sitename, !taker(quiz takers username), !date(time when quiz was finished), !minutes(How many minutes the quiz taker spent taking the quiz), !desc(description of the quiz), !correct(points attained), !total(max score for the quiz), !percentage(percentage score), !url(url to the result page) and !author are placeholders.'),
        '#default_value' => variable_get('quiz_email_results_body_taker', $this->formatEmailResults('body', 'taker')),
    );

    $form['quiz_email_settings']['author'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Email for Quiz authors'),
        '#collapsible' => FALSE,
    );

    $form['quiz_email_settings']['author']['quiz_results_to_quiz_author'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Email all results to quiz author.'),
        '#default_value' => variable_get('quiz_results_to_quiz_author', 0),
        '#description'   => t('Check this to send quiz results for all users to the quiz author.'),
    );

    $form['quiz_email_settings']['author']['quiz_email_results_subject'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Configure email subject'),
        '#description'   => t('This format will be used when sending quiz results at the end of a quiz. Authors and quiz takers gets the same format.'),
        '#default_value' => variable_get('quiz_email_results_subject', $this->formatEmailResults('subject', 'author')),
    );

    $form['quiz_email_settings']['author']['quiz_email_results_body'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Configure E-mail Format'),
        '#description'   => t('This format will be used when sending quiz results at the end of a quiz. !title(quiz title), !sitename, !taker(quiz takers username), !date(time when quiz was finished), !minutes(How many minutes the quiz taker spent taking the quiz), !desc(description of the quiz), !correct(points attained), !total(max score for the quiz), !percentage(percentage score), !url(url to the result page) and !author are placeholders.'),
        '#default_value' => variable_get('quiz_email_results_body', $this->formatEmailResults('body', 'author')),
    );

    return system_settings_form($form);
  }

  /**
   * Validation of the Form Settings form.
   *
   * Checks the values for the form administration form for quiz settings.
   */
  public function validate($form, &$form_state) {
    if (!quizz_valid_integer($form_state['values']['quiz_default_close'])) {
      form_set_error('quiz_default_close', t('The default number of days before a quiz is closed must be a number greater than 0.'));
    }

    if (!quizz_valid_integer($form_state['values']['quiz_max_result_options'], 0, 100)) {
      form_set_error('quiz_max_result_options', t('The number of result options must be an integer between 0 and 100.'));
    }

    if (!$this->isPlain($form_state['values']['quiz_name'])) {
      form_set_error('quiz_name', t('The quiz name must be plain text.'));
    }
  }

  /**
   * Submit the admin settings form
   */
  public function submit($form, &$form_state) {
    if (QUIZZ_NAME !== $form_state['values']['quiz_name']) {
      variable_set('quiz_name', $form_state['values']['quiz_name']);
      define(QUIZZ_NAME, $form_state['values']['quiz_name']);
      menu_rebuild();
    }
  }

  /**
   * This functions returns the default email subject and body format which will
   * be used at the end of quiz.
   */
  private function formatEmailResults($type, $target) {
    global $user;

    if ($type === 'subject') {
      return quizz()->getMailHelper()->formatSubject($target, $user);
    }

    if ($type === 'body') {
      return quizz()->getMailHelper()->formatBody($target, $user);
    }
  }

  /**
   * Helper function used when validating plain text.
   *
   * @param $value
   *   The value to be validated.
   *
   * @return
   *   TRUE if plain text FALSE otherwise.
   */
  private function isPlain($value) {
    return ($value === check_plain($value));
  }

}
