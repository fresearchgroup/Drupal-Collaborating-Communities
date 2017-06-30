<?php

namespace Drupal\quizz_question\Form;

use Drupal\quizz_question\Entity\Question;
use Drupal\quizz\Entity\QuizEntity;

class QuestionForm {

  private $question;

  public function __construct(Question $question) {
    $this->question = $question;
  }

  public function getForm(array &$form_state = NULL, QuizEntity $quiz = NULL) {
    global $language;

    if (!isset($form_state['storage']['quiz']) && NULL !== $quiz) {
      $form_state['storage']['quiz'] = $quiz;
    }

    if (module_exists('locale') && $this->question->getQuestionType()->data['multilingual']) {
      $language_options = array();
      foreach (language_list() as $langcode => $lang) {
        $language_options[$langcode] = $lang->name;
      }

      $form['language'] = array(
          '#type'          => count($language_options) < 5 ? 'radios' : 'select',
          '#title'         => t('Language'),
          '#options'       => $language_options,
          '#default_value' => isset($this->question->language) ? $this->question->language : $language->language,
      );
    }

    $this->getFormTitle($form);

    $form['feedback'] = array(
        '#type'          => 'text_format',
        '#title'         => t('Question feedback'),
        '#default_value' => !empty($this->question->feedback) ? $this->question->feedback : '',
        '#format'        => !empty($this->question->feedback_format) ? $this->question->feedback_format : filter_default_format(),
        '#description'   => t('This feedback will show when configured and the user answers a question, regardless of correctness.'),
    );

    $this->getPublishingOptions($form);
    $this->getActions($form);

    $form['question_handler'] = array('#weight' => 0, $this->question->getHandler()->getCreationForm($form_state));

    // Attach custom fields
    field_attach_form('quiz_question_entity', $this->question, $form, $form_state);

    return $form;
  }

  private function getActions(&$form) {
    $form['actions']['#weight'] = 100;
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save question'));
    if (!empty($this->question->qid)) {
      $form['actions']['delete'] = array(
          '#type'   => 'submit',
          '#value'  => t('Delete'),
          '#submit' => array('quiz_question_entity_form_submit_delete')
      );
    }
  }

  private function getFormTitle(&$form) {
    $form['title'] = array('#type' => 'value', '#value' => $this->question->title);

    // Allow user to set title?
    if (user_access('edit question titles')) {
      $form['title'] = array(
          '#type'          => 'textfield',
          '#title'         => t('Title'),
          '#maxlength'     => 255,
          '#default_value' => $this->question->title,
          '#required'      => FALSE,
          '#weight'        => -10,
          '#description'   => t('Add a title that will help distinguish this question from other questions. This will not be seen during the @quiz.', array('@quiz' => QUIZZ_NAME)),
      );

      $form['title']['#attached']['js'] = array(
          drupal_get_path('module', 'quizz_question') . '/misc/js/quiz-question.auto-title.js',
          array(
              'type' => 'setting',
              'data' => array(
                  'quiz_max_length' => $this->question->getQuestionType()->getConfig('autotitle_length', 50)
              ),
          ),
      );
    }
  }

  private function getPublishingOptions(&$form) {
    $form['publishing'] = array(
        '#type'           => 'fieldset',
        '#title'          => t('Publishing'),
        '#collapsible'    => TRUE,
        '#weight'         => 49,
        'publishing_tabs' => array('#type' => 'vertical_tabs'),
        'publishing'      => array(
            '#type'  => 'fieldset',
            '#title' => t('Publishing options'),
            '#group' => 'publishing_tabs',
            'status' => array(
                '#type'          => 'checkbox',
                '#title'         => t('Published'),
                '#default_value' => isset($this->question->status) ? $this->question->status : TRUE,
                '#tree'          => TRUE,
            ),
        ),
    );

    $form['publishing']['revision_information'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Revision information'),
        '#collapsible' => TRUE,
        '#collapsed'   => TRUE,
        '#group'       => 'publishing_tabs',
        '#attributes'  => array('class' => array('node-form-revision-information')),
        '#attached'    => array('js' => array(drupal_get_path('module', 'node') . '/node.js')),
        '#weight'      => 20,
        '#access'      => TRUE,
    );

    $form['publishing']['revision_information']['revision'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Create new revision'),
        '#default_value' => $this->question->getQuestionType()->getConfig('auto_revisioning', 1),
        '#state'         => array('checked' => array('textarea[name="log"]' => array('empty' => FALSE))),
    );

    $form['publishing']['revision_information']['log'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Revision log message'),
        '#row'           => 4,
        '#default_value' => '',
        '#description'   => t('Provide an explanation of the changes you are making. This will help other authors understand your motivations.'),
    );

    if ($this->question->getHandler()->hasBeenAnswered()) {
      $this->question->is_new_revision = 1;
      $this->question->log = t('The current revision has been answered. We create a new revision so that the reports from the existing answers stays correct.');
    }
  }

}
