<?php

namespace Drupal\quizz\Helper\HookImplementation;

class HookPermission {

  public function execute() {
    $permissions = array(
        'administer quiz configuration' => array(
            'title'           => t('Administer quiz configuration'),
            'description'     => t('Control the various settings and behaviours of quiz'),
            'restrict access' => TRUE,
        ),
    );
    $permissions += $this->getQuizEntityPermissions();
    $permissions += $this->getScorePermissions();
    $permissions += $this->getResultPermissions();
    $permissions += $this->getQuestionPermissions();

    return $permissions;
  }

  private function getQuizEntityPermissions() {
    $items = array();
    $items['access quiz'] = array(
        'title'       => t('Take quiz'),
        'description' => t('Can access (take) all quizzes.'),
    );
    $items['create quiz content']['title'] = t('Create quiz content');
    $items['edit any quiz content']['title'] = t('Edit any quiz content');

    // Control revisioning, only assign this permission to users who understand
    // who permissions work. Note: If a quiz or question is changed and not
    // revisioned you will also change existing result reports.
    $items['manual quiz revisioning'] = array(
        'title'       => t('Manual quiz revisioning'),
        'description' => t('Quizzes are revisioned automatically each time they are changed. This allows you to do revisions manually.'),
    );
    return $items;
  }

  private function getResultPermissions() {
    return array(
        // viewing results:
        'view any quiz results'                   => array(
            'title'       => t('View any quiz results'),
            'description' => t('Can view results for all quizzes and users.'),
        ),
        'view own quiz results'                   => array(
            'title'       => t('View own quiz results'),
            'description' => t('Quiz takers can view their own results, also when quiz is not passed.'),
        ),
        'view results for own quiz'               => array(
            'title'       => t('View results for own quiz'),
            'description' => t('Quiz makers can view results for their own quizzes.'),
        ),
        // deleting results:
        'delete any quiz results'                 => array(
            'title' => t('Delete any quiz results'),
        ),
        'delete results for own quiz'             => array(
            'title' => t('Delete own quiz results'),
        ),
        // Allow the user to see the correct answer, when viewed outside a quiz
        'view any quiz question correct response' => array(
            'title'       => t('View any quiz question correct response'),
            'description' => t('Allow the user to see the correct answer, when viewed outside a quiz.'),
        ),
    );
  }

  private function getScorePermissions() {
    return array(
        'score any quiz'          => array('title' => t('Score any quiz')),
        'score own quiz'          => array('title' => t('Score own quiz')),
        'score taken quiz answer' => array(
            'title'       => t('score taken quiz answer'),
            'description' => t('Allows attendee to score questions needing manual evaluation.'),
        ),
    );
  }

  private function getQuestionPermissions() {
    return array(
        // Allow a quiz question to be viewed outside of a test.
        'view quiz question outside of a quiz' => array(
            'title'       => t('View quiz question outside of a quiz'),
            'description' => t('Questions can only be accessed through taking a quiz (not as individual nodes) unless this permission is given.'),
        ),
        // Allows users to pick a name for their questions. Otherwise this is auto
        // generated.
        'edit question titles'                 => array(
            'title'       => t('Edit question titles'),
            'description' => t('Questions automatically get a title based on the question text. This allows titles to be set manually.'),
        ),
    );
  }

}
