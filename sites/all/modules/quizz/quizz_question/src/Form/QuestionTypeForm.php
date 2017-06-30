<?php

namespace Drupal\quizz_question\Form;

use Drupal\quizz_question\Entity\QuestionType;

class QuestionTypeForm {

  public function get($form, &$form_state, QuestionType $question_type, $op) {
    if ($op === 'clone') {
      $question_type->label .= ' (cloned)';
      $question_type->type = '';
    }

    $form['#question_type'] = $question_type;

    $this->getTitle($form, $question_type);
    $form['vtabs'] = array('#type' => 'vertical_tabs', '#weight' => 5);
    $this->basicInformation($form, $question_type);
    $this->getHandlerForm($question_type, $form);
    $this->getActions($op, $question_type, $form);

    return $form;
  }

  private function getHandlerForm(QuestionType $question_type, &$form) {
    $form['vtabs']['configuration'] = array(
        '#type'            => 'fieldset',
        '#title'           => t('Configuration'),
        '#tree'            => TRUE,
        'auto_revisioning' => array(
            '#type'          => 'checkbox',
            '#title'         => t('Auto revisioning'),
            '#default_value' => $question_type->getConfig('auto_revisioning', 1),
            '#description'   => t('It is strongly recommended that auto revisioning is always on. It makes sure that when a question or quiz is changed a new revision is created if the current revision has been answered. If this feature is switched off result reports might be broken because a users saved answer might be connected to a wrong version of the quiz and/or question she was answering. All sorts of errors might appear.'),
        ),
        'autotitle_length' => array(
            '#type'          => 'textfield',
            '#title'         => t('Length of automatically set question titles'),
            '#size'          => 3,
            '#maxlength'     => 3,
            '#description'   => t('Integer between 0 and 128. If the question creator doesn\'t set a question title the system will make a title automatically. Here you can decide how long the autotitle can be.'),
            '#default_value' => $question_type->getConfig('autotitle_length', 50),
        ),
//        …
//        'quiz_index_questions' => array(
//            '#type'          => 'checkbox',
//            '#title'         => t('Index questions'),
//            '#default_value' => $question_type->getConfig('quiz_index_questions', 1),
//            '#description'   => t('If you turn this off, questions will not show up in search results.'),
//        ),
    );

    if (!empty($question_type->is_new)) {
      return;
    }

    // @TODO: Add QuestionHandlerInterface::questionTypeConfigForm($question_type)
    if (($handler = $question_type->getHandler()) && method_exists($handler, 'questionTypeConfigForm')) {
      $handler_form = $handler->questionTypeConfigForm($question_type);
    }
    elseif (($fn = $question_type->handler . '_quiz_question_config') && function_exists($fn)) {
      $handler_form = $fn($question_type);
    }

    if (!empty($handler_form)) {
      $form['vtabs']['configuration'] += $handler_form;

      if (!empty($handler_form['#validate'])) {
        foreach ($handler_form['#validate'] as $validator) {
          $form['#validate'][] = $validator;
        }
        unset($handler_form['#validate']);
      }

      if (!empty($handler_form['#submit'])) {
        foreach ($handler_form['#submit'] as $validator) {
          $form['#submit'][] = $validator;
        }
        unset($handler_form['#submit']);
      }
    }
  }

  private function getActions($op, QuestionType $question_type, &$form) {
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save question type'), '#weight' => 40);

    if (!$question_type->isLocked() && $op !== 'add' && $op !== 'clone') {
      $form['actions']['delete'] = array(
          '#type'                    => 'submit',
          '#value'                   => t('Delete question type'),
          '#weight'                  => 45,
          '#limit_validation_errors' => array(),
          '#submit'                  => array('quiz_question_type_form_submit_delete')
      );
    }
  }

  private function getTitle(&$form, QuestionType $question_type) {
    $form['label'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Label'),
        '#default_value' => $question_type->label,
        '#description'   => t('The human-readable name of this question type.'),
        '#required'      => TRUE,
        '#size'          => 30,
    );

    // Machine-readable type name.
    $form['type'] = array(
        '#type'          => 'machine_name',
        '#default_value' => isset($question_type->type) ? $question_type->type : '',
        '#maxlength'     => 32,
        '#disabled'      => $question_type->isLocked() && $op !== 'clone',
        '#machine_name'  => array('exists' => 'quizz_question_type_load', 'source' => array('label')),
        '#description'   => t('A unique machine-readable name for this question type. It must only contain lowercase letters, numbers, and underscores.'),
    );
  }

  private function basicInformation(&$form, QuestionType $question_type) {
    $has_category = !empty($question_type->type) && NULL !== field_info_instance('quiz_question_entity', 'field_question_category', $question_type->type);

    $form['vtabs']['basic_information'] = array(
        '#type'           => 'fieldset',
        '#title'          => t('Basic informations'),
        'create_category' => array(
            '#type'          => 'checkbox',
            '#title'         => t('Include question category field'),
            '#description'   => t('Quiz queries for random categorized questions using this field.'),
            '#default_value' => $has_category,
            '#disabled'      => $has_category,
        ),
        'description'     => array(
            '#type'          => 'textarea',
            '#title'         => t('Description'),
            '#description'   => t('Describe this question type. The text will be displayed on the Add new question page.'),
            '#default_value' => $question_type->description,
        ),
        'help'            => array(
            '#type'          => 'textarea',
            '#title'         => t('Explanation or submission guidelines'),
            '#description'   => t('This text will be displayed at the top of the page when creating or editing question of this type.'),
            '#default_value' => $question_type->help,
        ),
    );

    $provider_options = array();
    foreach (quizz_question_get_handler_info() as $name => $info) {
      $provider_options[$name] = $info['name'];
    }

    $form['vtabs']['basic_information']['handler'] = array(
        '#weight'        => -10,
        '#type'          => 'select',
        '#required'      => TRUE,
        '#title'         => t('Question handler'),
        '#description'   => t('Can not be changed after question type created.'),
        '#options'       => $provider_options,
        '#disabled'      => !empty($question_type->handler),
        '#default_value' => $question_type->handler,
    );

    // Multilingual support
    if (module_exists('locale')) {
      $form['vtabs']['basic_information']['multilingual'] = array(
          '#type'          => 'radios',
          '#title'         => t('Multilingual support'),
          '#default_value' => isset($question_type->data['multilingual']) ? $question_type->data['multilingual'] : 0,
          '#options'       => array(t('Disabled'), t('Enabled')),
          '#description'   => t('Enable multilingual support for this quiz type. If enabled, a language selection field will be added to the editing form, allowing you to select from one of the <a href="!languages">enabled languages</a>. If disabled, new posts are saved with the default language. Existing content will not be affected by changing this option.', array('!languages' => url('admin/config/regional/language'))),
      );
    }
  }

  public function validate($form, &$form_state) {
    if (!quizz_valid_integer($form_state['values']['configuration']['autotitle_length'], 0, 128)) {
      $msg = t('The autotitle length value must be an integer between 0 and 128.');
      form_set_error('configuration][autotitle_length', $msg);
    }
  }

  public function submit($form, &$form_state) {
    /* @var $question_type QuestionType */
    $question_type = entity_ui_form_submit_build_entity($form, $form_state);
    $question_type->description = filter_xss_admin($question_type->description);
    $question_type->help = filter_xss_admin($question_type->help);

    if (isset($question_type->multilingual)) {
      $question_type->data['multilingual'] = (int) $question_type->multilingual;
      unset($question_type->multilingual);
    }

    if (!empty($form_state['values']['configuration'])) {
      foreach ($form_state['values']['configuration'] as $name => $value) {
        $question_type->setConfig($name, $value);
      }
    }

    $question_type->save();
    $form_state['redirect'] = 'admin/quizz/question-types';
  }

}
