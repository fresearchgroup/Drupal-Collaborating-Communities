<?php

namespace Drupal\quizz\Form\QuizTypeForm;

class FormDefinition {

  /** @var \Drupal\quizz\Entity\QuizType */
  private $quiz_type;

  public function __construct($quiz_type) {
    $this->quiz_type = $quiz_type;
  }

  public function get($op) {
    if ($op === 'clone') {
      $this->quiz_type->label .= ' (cloned)';
      $this->quiz_type->type = '';
    }

    $form['#quiz_type'] = $this->quiz_type;

    $form['label'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Label'),
        '#default_value' => $this->quiz_type->label,
        '#description'   => t('The human-readable name of this !quiz type.', array('!quiz' => QUIZZ_NAME)),
        '#required'      => TRUE,
        '#size'          => 30,
    );

    // Multilingual support
    if (module_exists('locale')) {
      $form['multilingual'] = array(
          '#type'          => 'radios',
          '#title'         => t('Multilingual support'),
          '#default_value' => isset($this->quiz_type->data['multilingual']) ? $this->quiz_type->data['multilingual'] : 0,
          '#options'       => array(t('Disabled'), t('Enabled')),
          '#description'   => t('Enable multilingual support for this quiz type. If enabled, a language selection field will be added to the editing form, allowing you to select from one of the <a href="!languages">enabled languages</a>. If disabled, new posts are saved with the default language. Existing content will not be affected by changing this option.', array('!languages' => url('admin/config/regional/language'))),
      );
    }

    // Machine-readable type name.
    $form['type'] = array(
        '#type'          => 'machine_name',
        '#default_value' => isset($this->quiz_type->type) ? $this->quiz_type->type : '',
        '#maxlength'     => 32,
        '#disabled'      => $this->quiz_type->isLocked() && $op !== 'clone',
        '#machine_name'  => array('exists' => 'quizz_type_load', 'source' => array('label')),
        '#description'   => t('A unique machine-readable name for this !quiz type. It must only contain lowercase letters, numbers, and underscores.', array('!quiz' => QUIZZ_NAME)),
    );

    $form['vtabs'] = array('#type' => 'vertical_tabs', '#weight' => 5);
    $this->basicInformation($form);
    $this->configViews($form);
    $this->configuration($form);
    $this->getActions($op, $form);

    return $form;
  }

  private function basicInformation(&$form) {
    $form['vtabs']['basic_information'] = array(
        '#type'       => 'fieldset',
        '#title'      => t('Basic informations'),
        'description' => array(
            '#type'          => 'textarea',
            '#title'         => t('Description'),
            '#description'   => t('Describe this !quiz type. The text will be displayed on the Add new !quiz page.', array('!quiz' => QUIZZ_NAME)),
            '#default_value' => $this->quiz_type->description,
        ),
        'help'        => array(
            '#type'          => 'textarea',
            '#title'         => t('Explanation or submission guidelines'),
            '#description'   => t('This text will be displayed at the top of the page when creating or editing !quiz of this type.', array('!quiz' => QUIZZ_NAME)),
            '#default_value' => $this->quiz_type->help,
        ),
    );
  }

  private function configViews(&$form) {
    $form['vtabs']['views'] = array(
        '#tree'  => TRUE,
        '#type'  => 'fieldset',
        '#title' => t('Views'),
    );

    $views = views_get_all_views();
    $bank_options = array();
    $result_options = array();
    foreach ($views as $name => $view) {
      if ('quiz_results' === $view->base_table) {
        $result_options[$name] = $view->human_name;
      }
      elseif ('quiz_question_entity' === $view->base_table) {
        $bank_options[$name] = $view->human_name;
      }
    }

    $form['vtabs']['views']['quiz_views_question_bank'] = array(
        '#type'          => 'select',
        '#title'         => t('Question bank'),
        '#options'       => $bank_options,
        '#default_value' => $this->quiz_type->getConfig('quiz_views_question_bank', 'quizz_question_bank'),
        '#description'   => t('View is used as question bank at /quiz/%/questions'),
    );

    $form['vtabs']['views']['quiz_views_results'] = array(
        '#type'          => 'select',
        '#title'         => t('Quiz results'),
        '#options'       => $result_options,
        '#default_value' => $this->quiz_type->getConfig('quiz_views_results', 'quizz_results'),
        '#description'   => t('Views to list all results at /quiz/%/results.'),
    );

    $form['vtabs']['views']['quiz_views_user_results'] = array(
        '#type'          => 'select',
        '#title'         => t('Quiz user results'),
        '#options'       => $result_options,
        '#default_value' => $this->quiz_type->getConfig('quiz_views_user_results', 'quizz_user_results'),
        '#description'   => t('Views to list all results of logged-in user at /quiz/%/my-results.'),
    );
  }

  private function configuration(&$form) {
    $config = isset($this->quiz_type->data['configuration']) ? $this->quiz_type->data['configuration'] : array();

    $form['vtabs']['configuration'] = array(
        '#tree'      => TRUE,
        '#type'      => 'fieldset',
        '#title'     => t('Configuration'),
        'quiz_durod' => array(
            '#type'          => 'checkbox',
            '#title'         => t('Delete results when a user is deleted'),
            '#default_value' => $this->quiz_type->getConfig('quiz_durod', 0),
            '#description'   => t('When a user is deleted delete any and all results for that user.'),
        ),
    );

    $form['vtabs']['configuration']['quiz_auto_revisioning'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Auto revisioning'),
        '#default_value' => isset($config['quiz_auto_revisioning']) ? $config['quiz_auto_revisioning'] : 1,
        '#description'   => t('It is strongly recommended that auto revisioning is always on. It makes sure that when a question or quiz is changed a new revision is created if the current revision has been answered. If this feature is switched off result reports might be broken because a users saved answer might be connected to a wrong version of the quiz and/or question she was answering. All sorts of errors might appear.'),
    );

    $form['vtabs']['configuration']['quiz_use_passfail'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Allow quiz creators to set a pass/fail option when creating a @quiz.', array('@quiz' => strtolower(QUIZZ_NAME))),
        '#default_value' => isset($config['quiz_use_passfail']) ? $config['quiz_use_passfail'] : 1,
        '#description'   => t('Check this to display the pass/fail options in the @quiz form. If you want to prohibit other quiz creators from changing the default pass/fail percentage, uncheck this option.', array('@quiz' => QUIZZ_NAME)),
    );

    $form['vtabs']['configuration']['quiz_has_timer'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Display timer'),
        '#default_value' => $this->quiz_type->getConfig('quiz_has_timer', 0),
        '#disabled'      => !function_exists('jquery_countdown_add'),
        '#description'   => t("!jquery_countdown is an <strong>optional</strong> module for Quiz. It is used to display a timer when taking a quiz. Without this timer, the user will not know how much time they have left to complete the Quiz", array(
            '!jquery_countdown' => l(t('JQuery Countdown'), 'http://drupal.org/project/jquery_countdown'),
        )),
    );

    $form['vtabs']['configuration']['build_on_last'] = array(
        '#type'          => 'radios',
        '#options'       => array(
            ''        => t('Fresh attempt every time'),
            'correct' => t('Prepopulate with correct answers from last result'),
            'all'     => t('Prepopulate with all answers from last result'),
        ),
        '#title'         => t('Each attempt builds on the last'),
        '#default_value' => isset($config['build_on_last']) ? $config['build_on_last'] : '',
        '#description'   => t('Instead of starting a fresh @quiz, new attempts will be created based on the last attempt, with correct answers prefilled.', array('@quiz' => QUIZZ_NAME)),
    );

    $form['vtabs']['configuration']['quiz_remove_partial_quiz_record'] = array(
        '#type'          => 'select',
        '#title'         => t('Remove incomplete quiz records (older than)'),
        '#options'       => $this->removePartialQuizRecordValue(),
        '#description'   => t('Number of days to keep incomplete quiz attempts.'),
        '#default_value' => isset($config['quiz_remove_partial_quiz_record']) ? $config['quiz_remove_partial_quiz_record'] : 604800,
    );

    $form['vtabs']['configuration']['quiz_default_close'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Default number of days before a @quiz is closed', array('@quiz' => QUIZZ_NAME)),
        '#default_value' => isset($config['quiz_default_close']) ? $config['quiz_default_close'] : 30,
        '#size'          => 4,
        '#maxlength'     => 4,
        '#description'   => t('Supply a number of days to calculate the default close date for new quizzes.'),
    );

    $form['vtabs']['configuration']['quiz_max_result_options'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Maximum result options'),
        '#description'   => t('Set the maximum number of result options (categorizations for scoring a quiz). Set to 0 to disable result options.'),
        '#default_value' => isset($config['quiz_max_result_options']) ? $config['quiz_max_result_options'] : 5,
        '#size'          => 2,
        '#maxlength'     => 2,
        '#required'      => FALSE,
    );

    $form['vtabs']['configuration']['quiz_pager_start'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Pager start'),
        '#size'          => 3,
        '#maxlength'     => 3,
        '#description'   => t('If a quiz has this many questions, a pager will be displayed instead of a select box.'),
        '#default_value' => isset($config['quiz_pager_start']) ? $config['quiz_pager_start'] : 100,
    );

    $form['vtabs']['configuration']['quiz_pager_siblings'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Pager siblings'),
        '#size'          => 3,
        '#maxlength'     => 3,
        '#description'   => t('Number of siblings to show.'),
        '#default_value' => isset($config['quiz_pager_siblings']) ? $config['quiz_pager_siblings'] : 5,
    );
  }

  /**
   * Helper function returning number of days as values and corresponding
   * number of seconds as array keys.
   *
   * @return array
   */
  private function removePartialQuizRecordValue() {
    return array(
        '0'        => t('Never'),
        '86400'    => t('1 day'),
        '172800'   => t('!num days', array('!num' => 2)),
        '259200'   => t('!num days', array('!num' => 3)),
        '345600'   => t('!num days', array('!num' => 4)),
        '432000'   => t('!num days', array('!num' => 5)),
        '518400'   => t('!num days', array('!num' => 6)),
        '604800'   => t('!num days', array('!num' => 7)),
        '691200'   => t('!num days', array('!num' => 8)),
        '777600'   => t('!num days', array('!num' => 9)),
        '864000'   => t('!num days', array('!num' => 10)),
        '950400'   => t('!num days', array('!num' => 11)),
        '1036800'  => t('!num days', array('!num' => 12)),
        '1123200'  => t('!num days', array('!num' => 13)),
        '1209600'  => t('!num days', array('!num' => 14)),
        '1296000'  => t('!num days', array('!num' => 15)),
        '1382400'  => t('!num days', array('!num' => 16)),
        '1468800'  => t('!num days', array('!num' => 17)),
        '1555200'  => t('!num days', array('!num' => 18)),
        '1641600'  => t('!num days', array('!num' => 19)),
        '1728000'  => t('!num days', array('!num' => 20)),
        '1814400'  => t('!num days', array('!num' => 21)),
        '1900800'  => t('!num days', array('!num' => 22)),
        '1987200'  => t('!num days', array('!num' => 23)),
        '2073600'  => t('!num days', array('!num' => 24)),
        '2160000'  => t('!num days', array('!num' => 25)),
        '2246400'  => t('!num days', array('!num' => 26)),
        '2332800'  => t('!num days', array('!num' => 27)),
        '2419200'  => t('!num days', array('!num' => 28)),
        '2505600'  => t('!num days', array('!num' => 29)),
        '2592000'  => t('!num days', array('!num' => 30)),
        '3024000'  => t('!num days', array('!num' => 35)),
        '3456000'  => t('!num days', array('!num' => 40)),
        '3888000'  => t('!num days', array('!num' => 45)),
        '4320000'  => t('!num days', array('!num' => 50)),
        '4752000'  => t('!num days', array('!num' => 55)),
        '5184000'  => t('!num days', array('!num' => 60)),
        '5616000'  => t('!num days', array('!num' => 65)),
        '6048000'  => t('!num days', array('!num' => 70)),
        '6480000'  => t('!num days', array('!num' => 75)),
        '6912000'  => t('!num days', array('!num' => 80)),
        '7344000'  => t('!num days', array('!num' => 85)),
        '7776000'  => t('!num days', array('!num' => 90)),
        '8208000'  => t('!num days', array('!num' => 95)),
        '8640000'  => t('!num days', array('!num' => 100)),
        '9072000'  => t('!num days', array('!num' => 105)),
        '9504000'  => t('!num days', array('!num' => 110)),
        '9936000'  => t('!num days', array('!num' => 115)),
        '10368000' => t('!num days', array('!num' => 120)),
    );
  }

  private function getActions($op, &$form) {
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save quiz type'), '#weight' => 40);

    if (!$this->quiz_type->isLocked() && $op != 'add' && $op != 'clone') {
      $form['actions']['delete'] = array(
          '#type'                    => 'submit',
          '#value'                   => t('Delete !quiz type', array('!quiz' => QUIZZ_NAME)),
          '#weight'                  => 45,
          '#limit_validation_errors' => array(),
          '#submit'                  => array('quiz_type_form_submit_delete')
      );
    }
  }

}
