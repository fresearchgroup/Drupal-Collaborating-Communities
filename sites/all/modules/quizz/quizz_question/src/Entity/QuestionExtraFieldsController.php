<?php

namespace Drupal\quizz_question\Entity;

use EntityDefaultExtraFieldsController;

class QuestionExtraFieldsController extends EntityDefaultExtraFieldsController {

  public function fieldExtraFields() {
    $extra = array();

    foreach (array_keys(quizz_question_get_types()) as $name) {
      $this->defineExtraFields($name, $extra);
    }

    return $extra;
  }

  private function defineExtraFields($type, &$extra) {
    $extra['quiz_question_entity'][$type] = array(
        'display' => array(
            'question_handler' => array(
                'label'       => t("Handler fields"),
                'description' => t("Custom fields defined by question handler."),
                'weight'      => -5,
            ),
        ),
        'form'    => array(
            'title'            => array(
                'label'       => t('Title'),
                'description' => t("Question's title."),
                'weight'      => -10,
            ),
            'question_handler' => array(
                'label'       => t("Handler fields"),
                'description' => t("Custom fields defined by question handler."),
                'weight'      => -5,
            ),
            'feedback'         => array(
                'label'       => t('Question feedback'),
                'description' => '',
                'weight'      => -1,
            ),
            'publishing'       => array(
                'label'  => t('Publishing options'),
                'weight' => 99,
            ),
        ),
    );

    if (module_exists('locale')) {
      $extra['quiz_question_entity'][$type]['form']['language'] = array(
          'label'       => t('Language'),
          'description' => t('Language selector'),
          'weight'      => -20,
      );
    }
  }

}
