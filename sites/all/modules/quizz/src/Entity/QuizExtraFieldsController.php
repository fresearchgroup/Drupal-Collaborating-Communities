<?php

namespace Drupal\quizz\Entity;

class QuizExtraFieldsController extends \EntityDefaultExtraFieldsController {

  public function fieldExtraFields() {
    $extra = array();

    // User comes from old version, there's no quiz type yet
    if (!db_table_exists('quiz_entity') || !db_table_exists('quiz_question_type')) {
      return $extra;
    }

    if ($types = quizz_get_types()) {
      foreach (array_keys($types) as $name) {
        $extra['quiz_entity'][$name] = array(
            'display' => $this->getQuizDisplayFields(),
            'form'    => $this->getQuizFormExtraFields(),
        );

        $extra['quiz_result'][$name] = array(
            'display' => array(
                'score'         => array('label' => t('Score'), 'weight' => -10),
                'feedback'      => array('label' => t('Feedback'), 'weight' => -5),
                'feedback_form' => array('label' => t('Feedback form'), 'weight' => 0),
            ),
        );
      }
    }

    return $extra;
  }

  private function getQuizDisplayFields() {
    return array(
        'take'  => array(
            'label'       => t('Take @quiz button', array('@quiz' => QUIZZ_NAME)),
            'description' => t('The take button.'),
            'weight'      => 10,
        ),
        'stats' => array(
            'label'       => t('@quiz summary', array('@quiz' => QUIZZ_NAME)),
            'description' => t('@quiz summary', array('@quiz' => QUIZZ_NAME)),
            'weight'      => 9,
        ),
    );
  }

  private function getQuizFormExtraFields() {
    $elements = array(
        'quiz_help' => array('weight' => -50, 'label' => t('Explanation or submission guidelines')),
        'title'     => array('weight' => -30, 'label' => t('Title')),
        'vtabs'     => array('weight' => 50, 'label' => t('Quiz options')),
        'language'  => array('weight' => -20, 'label' => t('Language')),
    );

    if (!module_exists('locale')) {
      unset($elements['language']);
    }

    return $elements;
  }

}
