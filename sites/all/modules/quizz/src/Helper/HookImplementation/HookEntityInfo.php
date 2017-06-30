<?php

namespace Drupal\quizz\Helper\HookImplementation;

class HookEntityInfo {

  public function execute() {
    // User comes from old version, there's no table defined yet. Must upgrade
    // schema first.
    if (!db_table_exists('quiz_type')) {
      return array();
    }

    return array(
        'quiz_type'          => $this->getQuizTypeInfo(),
        'quiz_entity'        => $this->getQuizInfo(),
        'quiz_relationship'  => $this->getQuizQuestionRelationshipInfo(),
        'quiz_result_type'   => $this->getQuizResultTypeInfo(),
        'quiz_result'        => $this->getQuizResultInfo(),
        'quiz_answer_type'   => $this->getQuizAnswerTypeInfo(),
        'quiz_result_answer' => $this->getQuizAnswerInfo(),
    );
  }

  private function getQuizTypeInfo() {
    return array(
        'label'            => t('!quiz type', array('!quiz' => QUIZZ_NAME)),
        'plural label'     => t('!quiz types', array('!quiz' => QUIZZ_NAME)),
        'description'      => t('Types of !quiz.', array('!quiz' => QUIZZ_NAME)),
        'entity class'     => 'Drupal\quizz\Entity\QuizType',
        'controller class' => 'Drupal\quizz\Entity\QuizTypeController',
        'base table'       => 'quiz_type',
        'fieldable'        => FALSE,
        'bundle of'        => 'quiz_entity',
        'exportable'       => TRUE,
        'entity keys'      => array('id' => 'id', 'name' => 'type', 'label' => 'label'),
        'access callback'  => 'quizz_type_access',
        'module'           => 'quizz',
        'admin ui'         => array(
            'path'             => 'admin/quizz/types',
            'file'             => 'includes/quizz.pages.inc',
            'controller class' => 'Drupal\quizz\Entity\QuizTypeUIController',
        ),
    );
  }

  private function getQuizInfo() {
    $entity_info = array(
        'label'                         => QUIZZ_NAME,
        'description'                   => t('!quiz entity', array('!quiz' => QUIZZ_NAME)),
        'entity class'                  => 'Drupal\quizz\Entity\QuizEntity',
        'controller class'              => 'Drupal\quizz\Entity\QuizController',
        'metadata controller class'     => 'Drupal\quizz\Entity\QuizMetadataController',
        'extra fields controller class' => 'Drupal\quizz\Entity\QuizExtraFieldsController',
        'views controller class'        => 'Drupal\quizz\Entity\QuizViewsController',
        'base table'                    => 'quiz_entity',
        'revision table'                => 'quiz_entity_revision',
        'fieldable'                     => TRUE,
        'entity keys'                   => array('id' => 'qid', 'bundle' => 'type', 'revision' => 'vid', 'label' => 'title'),
        'bundle keys'                   => array('bundle' => 'type'),
        'access callback'               => 'quizz_entity_access_callback',
        'label callback'                => 'entity_class_label',
        'uri callback'                  => 'entity_class_uri',
        'module'                        => 'quizz',
        'bundles'                       => array(),
        'view modes'                    => array(
            'teaser' => array('label' => t('Teaser'), 'custom settings' => TRUE),
            'full'   => array('label' => t('Full'), 'custom settings' => TRUE),
        ),
        'admin ui'                      => array(
            'path'             => 'admin/content/quizz',
            'file'             => 'includes/quizz.pages.inc',
            'controller class' => 'Drupal\quizz\Entity\QuizUIController',
        ),
    );

    // User may come from 4.x, where the table is not available yet
    if (db_table_exists('quiz_type')) {
      // Add bundle info but bypass entity_load() as we cannot use it here.
      foreach (db_select('quiz_type', 'qt')->fields('qt')->execute()->fetchAllAssoc('type') as $type => $info) {
        $entity_info['bundles'][$type] = array(
            'label' => $info->label,
            'admin' => array(
                'path'             => 'admin/quizz/types/manage/%quizz_type',
                'real path'        => 'admin/quizz/types/manage/' . $type,
                'bundle argument'  => 4,
                'access arguments' => array('administer quiz'),
            ),
        );
      }
    }

    // Support entity cache module.
    if (module_exists('entitycache')) {
      $entity_info['field cache'] = FALSE;
      $entity_info['entity cache'] = TRUE;
    }

    return $entity_info;
  }

  private function getQuizQuestionRelationshipInfo() {
    return array(
        'label'                     => t('Quiz question relationship'),
        'entity class'              => 'Drupal\quizz\Entity\Relationship',
        'controller class'          => 'EntityAPIController',
        'base table'                => 'quiz_relationship',
        'entity keys'               => array('id' => 'qr_id'),
        'views controller class'    => 'EntityDefaultViewsController',
        'metadata controller class' => 'Drupal\quizz\Entity\RelationshipMetadataController',
    );
  }

  private function getQuizResultTypeInfo() {
    return array(
        'label'        => t('Quiz result type'),
        'plural label' => t('Quiz result types'),
        'description'  => t('Types of result.'),
        'bundle of'    => 'quiz_result',
        'admin ui'     => array(),
      ) + $this->getQuizTypeInfo();
  }

  private function getQuizResultInfo() {
    $info = array(
        'label'                     => t('Quiz result'),
        'entity class'              => 'Drupal\quizz\Entity\Result',
        'controller class'          => 'Drupal\quizz\Entity\ResultController',
        'base table'                => 'quiz_results',
        'entity keys'               => array('id' => 'result_id', 'bundle' => 'type', 'label' => 'result_id'),
        'bundle keys'               => array('bundle' => 'type'),
        'views controller class'    => 'EntityDefaultViewsController',
        'metadata controller class' => 'Drupal\quizz\Entity\ResultMetadataController',
        'fieldable'                 => TRUE,
        'view modes'                => array(
            'result' => array('label' => t('Result'), 'custom settings' => TRUE),
        ),
    );

    // User may come from 4.x, where the table is not available yet
    if (db_table_exists('quiz_type')) {
      // Add bundle info but bypass entity_load() as we cannot use it here.
      foreach (db_select('quiz_type', 'qt')->fields('qt')->execute()->fetchAllAssoc('type') as $name => $question_type) {
        $info['bundles'][$name] = array(
            'label' => $question_type->label,
            'admin' => array(
                'path'             => 'admin/quizz/types/manage/%quizz_type/result',
                'real path'        => 'admin/quizz/types/manage/' . $name . '/result',
                'bundle argument'  => 4,
                'access arguments' => array('administer quiz'),
            ),
        );
      }
    }

    return $info;
  }

  private function getQuizAnswerTypeInfo() {
    $info = array(
        'label'        => t('Answer type'),
        'plural label' => t('Answer types'),
        'description'  => t('Types of answer.'),
        'bundle of'    => 'quiz_result_answer',
        'admin ui'     => array(),
    );

    $question_entity_info = quizz_question_entity_info();
    if (isset($question_entity_info['quiz_question_type'])) {
      $info += $question_entity_info['quiz_question_type'];
    }

    return $info;
  }

  private function getQuizAnswerInfo() {
    $info = array(
        'label'                     => t('Quiz result answer'),
        'entity class'              => 'Drupal\quizz\Entity\Answer',
        'controller class'          => 'Drupal\quizz\Entity\AnswerController',
        'base table'                => 'quiz_answer_entity',
        'entity keys'               => array('id' => 'id', 'bundle' => 'type', 'label' => 'id'),
        'bundle keys'               => array('bundle' => 'type'),
        'views controller class'    => 'EntityDefaultViewsController',
        'metadata controller class' => 'Drupal\quizz\Entity\AnswerMetadataController',
        'fieldable'                 => TRUE,
        'bundles'                   => array(),
    );

    // User may come from old version, where the table is not available yet
    if (db_table_exists('quiz_question_type')) {
      // Add bundle info but bypass entity_load() as we cannot use it here.
      $rows = db_select('quiz_question_type', 'qt')->fields('qt')->execute()->fetchAllAssoc('type');
      foreach ($rows as $name => $row) {
        $info['bundles'][$name] = array(
            'label' => $row->label,
            'admin' => array(
                'path'             => 'admin/quizz/question-types/manage/%quizz_question_type/answer',
                'real path'        => 'admin/quizz/question-types/manage/' . $name . '/answer',
                'bundle argument'  => 4,
                'access arguments' => array('administer quiz questions'),
            ),
        );
      }
    }

    return $info;
  }

}
