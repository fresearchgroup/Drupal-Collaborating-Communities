<?php

namespace Drupal\quizz_question\Entity;

use DatabaseTransaction;
use Drupal\quizz_question\Entity\QuestionType;
use EntityAPIControllerExportable;

class QuestionTypeController extends EntityAPIControllerExportable {

  /**
   * {@inheritdoc}
   * @param QuestionType $question_type
   * @param DatabaseTransaction $transaction
   */
  public function save($question_type, DatabaseTransaction $transaction = NULL) {
    $return = parent::save($question_type, $transaction);

    if (!QuestionController::$disable_invoking) {
      $question_type
        ->getHandler(NULL, TRUE)
        ->onNewQuestionTypeCreated($question_type)
      ;
    }

    if (!empty($question_type->create_category)) {
      $this->createQuestionCategory($question_type);
    }

    return $return;
  }

  private function createQuestionCategory(\Drupal\quizz_question\Entity\QuestionType $question_type) {
    if (!taxonomy_vocabulary_machine_name_load('quiz_question_category')) {
      $vocab = new \stdClass();
      $vocab->name = t('Question category');
      $vocab->machine_name = 'quiz_question_category';
      $vocab->description = '';
      $vocab->module = 'taxonomy';
      taxonomy_vocabulary_save($vocab);
    }

    if (!field_info_field('quizz_question_category')) {
      field_create_field(array(
          'field_name'   => 'quizz_question_category',
          'type'         => 'taxonomy_term_reference',
          'entity_types' => array('quiz_question_entity'),
          'settings'     => array(
              'allowed_values' => array(
                  array('parent' => 0, 'vocabulary' => 'quiz_question_category')
              ),
          ),
      ));
    }

    if (!field_info_instance('quiz_question_entity', 'quizz_question_category', $question_type->type)) {
      field_create_instance(array(
          'field_name'  => 'quizz_question_category',
          'entity_type' => 'quiz_question_entity',
          'bundle'      => $question_type->type,
          'label'       => t('Question category'),
          'widget'      => array(
              'module'   => 'options',
              'type'     => 'options_select',
              'weight'   => -75,
              'settings' => array(),
          ),
          'settings'    => array('display_summary' => TRUE),
          'display'     => array(
              'full'   => array('type' => 'hidden'),
              'full'   => array('type' => 'hidden'),
              'teaser' => array('type' => 'hidden'),
          ),
      ));
    }
  }

}
