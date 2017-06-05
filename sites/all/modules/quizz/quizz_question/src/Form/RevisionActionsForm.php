<?php

namespace Drupal\quizz_question\Form;

use Drupal\quizz_question\Entity\Question;
use EntityFieldQuery;

/**
 * @TODO This is unreachable code. Read more at https://www.drupal.org/node/2374407
 */
class RevisionActionsForm {

  /**
   * Create the form for the revision actions page
   *
   * Form for deciding what to do with the quizzes a question is member of when the
   * question is revised
   *
   * @return array
   */
  public function get($form, $form_state, Question $question) {
    $form['quizzes'] = array();

    $form['#question'] = $question;

    $query = new EntityFieldQuery();
    $find = $query->entityCondition('entity_type', 'quiz_relationship')
      ->propertyCondition('question_qid', $question->qid)
      ->execute();
    $relationships = entity_load('quiz_relationship', array_keys($find['quiz_relationship']));

    $form['intro']['#markup'] = t('You have created a new revision of a question that belongs to %num quizzes. Choose what you want to do with the different quizzes.', array(
        '%num' => count($relationships)
    ));

    // Create a form element for each quiz
    foreach ($relationships as $relationship) {
      $quiz = quizz_load($relationship->quiz_qid);
      $answered = $quiz->isAnswered();

      $form['quizzes']['#tree'] = TRUE;
      $form['quizzes'][$quiz->qid]['revise'] = array(
          '#type'          => 'radios',
          '#title'         => check_plain($quiz->title) . ' - ' . ($answered ? t('answered') : t('unanswered')) . ', ' . ($quiz->status ? t('published') : t('unpublished')),
          '#options'       => array(
              'update'  => t('Update'),
              'revise'  => t('Create new revision'),
              'nothing' => t('Do nothing'),
          ),
          '#default_value' => ($answered ? 'revise' : 'update'),
      );
      $form['quizzes'][$quiz->qid]['status'] = array(
          '#type'          => 'checkbox',
          '#title'         => $quiz->status ? t('Leave published') : t('Publish'),
          '#default_value' => $quiz->status,
      );
    }

    $form['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

    // Help texts
    $form['update_expl'] = array(
        '#type'        => 'item',
        '#title'       => t('Update'),
        '#description' => t('Replace the old revision of the question with the new revision. This may affect reporting. It is the default when the most recent Quiz revision has not been answered.'),
    );
    $form['revise_expl'] = array(
        '#type'        => 'item',
        '#title'       => t('Create new revision'),
        '#description' => t('If the current revision of a Quiz has been answered, you should make a new revision to ensure that existing answer statistics and reports remain correct.')
        . '<br/>' . t('If the new revision of the question only corrects spelling errors, you do not need to create a new revision of the Quiz.'),
    );
    $form['nothing_expl'] = array(
        '#type'        => 'item',
        '#title'       => t('Do nothing'),
        '#description' => t('The quiz will not be revised, and will still use the old revision of the question.'),
    );
    return $form;
  }

  public function submit($form, &$form_state) {
    $question = $form['#question'];

    foreach ($form_state['values']['quizzes'] as $quiz_id => $actions) {
      // Get the current version of the questions.
      $quiz = quizz_load($quiz_id);
      $query = new EntityFieldQuery();
      $find = $query->entityCondition('entity_type', 'quiz_relationship')
        ->propertyCondition('quiz_qid', $quiz->qid)
        ->propertyCondition('quiz_vid', $quiz->vid)
        ->execute();

      $relationships = entity_load('quiz_question_relationship', array_keys($find['quiz_relationship']));
      foreach ($relationships as $relationship) {
        // We found the existing question.
        if ($relationship->question_qid == $question->qid) {
          $relationship->question_vid = $question->vid;
        }
      }

      if ($actions['revise'] === 'revise') {
        $quiz->is_new_revision = TRUE;
        $quiz->save();
      }

      $quiz->getQuestionIO()->setRelationships($relationships);
    }
  }

}
