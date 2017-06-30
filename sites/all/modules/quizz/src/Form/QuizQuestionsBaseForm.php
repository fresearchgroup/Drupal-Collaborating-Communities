<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Entity\QuizEntity;

abstract class QuizQuestionsBaseForm {

  /**
   * Adds checkbox for creating new revision. Checks it by default if answers exists.
   *
   * @param array $form FAPI form(array)
   * @param QuizEntity $quiz
   */
  protected function addRevisionCheckbox(&$form, &$quiz) {
    // Recommend and preselect to create the quiz as a new revision if it already has been answered
    if ($quiz->isAnswered()) {
      $rev_default = TRUE;
      $rev_description = t('This quiz has been answered. To maintain correctness of existing answers and reports, changes should be saved as a new revision.');
    }
    else {
      $rev_default = in_array('revision', variable_get('node_options_quiz', array()));
      $rev_description = t('Allow question status changes to create a new revision of the quiz?');
    }

    if (user_access('manual quiz revisioning') && !$quiz->getQuizType()->getConfig('quiz_auto_revisioning', 1)) {
      $form['new_revision'] = array(
          '#type'          => 'checkbox',
          '#default_value' => $rev_default,
          '#title'         => t('New revision'),
          '#description'   => $rev_description,
      );
    }
    else {
      $form['new_revision'] = array('#type' => 'value', '#value' => $rev_default);
    }
  }

}
