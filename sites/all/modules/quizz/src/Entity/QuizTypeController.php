<?php

namespace Drupal\quizz\Entity;

use DatabaseTransaction;
use EntityAPIControllerExportable;

class QuizTypeController extends EntityAPIControllerExportable {

  public function save($entity, DatabaseTransaction $transaction = NULL) {
    $return = parent::save($entity, $transaction);

    $this->addBodyField($entity->type);

    return $return;
  }

  /**
   * Add default body field to a quiz type
   */
  private function addBodyField($bundle) {
    if (!field_info_field('quiz_body')) {
      field_create_field(array(
          'field_name'   => 'quiz_body',
          'type'         => 'text_with_summary',
          'entity_types' => array('quiz_entity'),
      ));
    }

    if (!field_info_instance('quiz_entity', 'quiz_body', $bundle)) {
      field_create_instance(array(
          'field_name'  => 'quiz_body',
          'entity_type' => 'quiz_entity',
          'bundle'      => $bundle,
          'label'       => t('Body'),
          'widget'      => array(
              'type'     => 'text_textarea_with_summary',
              'weight'   => 0,
              'settings' => array('rows' => 5, 'summary_rows' => 3),
          ),
          'settings'    => array('display_summary' => TRUE),
          'display'     => array(
              'default' => array('label' => 'hidden', 'type' => 'text_default'),
          ),
      ));
    }
  }

}
