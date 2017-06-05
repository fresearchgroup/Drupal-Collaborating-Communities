<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Form\QuizQuestionsBaseForm;

class QuizQuestionsForm extends QuizQuestionsBaseForm {

  /**
   * Handles "manage questions" tab.
   *
   * Displays form which allows questions to be assigned to the given quiz.
   *
   * This function is not used if the question assignment type "categorized random questions" is chosen
   *
   * @param $form_state
   * @param QuizEntity $quiz
   * @return string
   */
  public function formGet(&$form, $form_state, QuizEntity $quiz) {
    // Display questions in this quiz.
    $form['question_list'] = array(
        '#type'           => 'container',
        '#title'          => t('Questions in this @quiz', array('@quiz' => QUIZZ_NAME)),
        '#theme'          => 'quizz_question_selection_table',
        '#attributes'     => array('id' => 'mq-fieldset'),
        'question_status' => array('#tree' => TRUE),
    );

    // Add randomization settings if this quiz allows randomized questions
    $this->addFieldsForRandomQuiz($form, $quiz);

    // @todo deal with $include_random
    if (!$relationships = $quiz->getQuestionIO()->getQuestionList()) {
      $form['question_list']['no_questions'] = array(
          '#markup' => '<div id = "no-questions">' . t('There are currently no questions in this @quiz. Assign existing questions by using the question browser below. You can also use the links above to create new questions.', array('@quiz' => QUIZZ_NAME)) . '</div>',
      );
    }

    // We add the questions to the form array
    $this->addQuestionsToForm($form, $relationships, $quiz);

    // Show the number of questions in the table header.
    # $always_count = isset($form['question_list']['titles']) ? count($form['question_list']['titles']) : 0;
    # $form['question_list']['#title'] .= ' (' . $always_count . ')';
    // Give the user the option to create a new revision of the quiz
    $this->addRevisionCheckbox($form, $quiz);

    // Timestamp is needed to avoid multiple users editing the same quiz at the same time.
    $form['timestamp'] = array('#type' => 'hidden', '#default_value' => REQUEST_TIME);

    // Action buttons
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

    return $form;
  }

  /**
   * Add fields for random quiz to the quizz_questions_form
   *
   * @param array $form
   *   FAPI form array
   * @param QuizEntity $quiz
   */
  private function addFieldsForRandomQuiz(&$form, $quiz) {
    if ($quiz->randomization != 2) {
      return;
    }
    $form['question_list']['random_settings'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Settings for random questions'),
        '#collapsible' => TRUE,
    );
    $form['question_list']['random_settings']['num_random_questions'] = array(
        '#type'          => 'textfield',
        '#size'          => 3,
        '#maxlength'     => 3,
        '#weight'        => -5,
        '#title'         => t('Number of random questions'),
        '#description'   => t('The number of questions to be randomly selected each time someone takes this quiz'),
        '#default_value' => isset($quiz->number_of_random_questions) ? $quiz->number_of_random_questions : 10,
    );
    $form['question_list']['random_settings']['max_score_for_random'] = array(
        '#type'          => 'textfield',
        '#size'          => 3,
        '#maxlength'     => 3,
        '#weight'        => -5,
        '#title'         => t('Max score for each random question'),
        '#default_value' => isset($quiz->max_score_for_random) ? $quiz->max_score_for_random : 1,
    );
    if ($quiz->randomization == 3) {
      $terms = $this->taxonomySelect($quiz->tid);
      if (!empty($terms) && function_exists('taxonomy_get_vocabularies')) {
        $form['question_list']['random_settings']['random_term_id'] = array(
            '#type'          => 'select',
            '#title'         => t('Terms'),
            '#size'          => 1,
            '#options'       => $this->taxonomySelect($quiz->tid),
            '#default_value' => $quiz->tid,
            '#description'   => t('Randomly select from questions with this term, or choose from the question pool below'),
            '#weight'        => -4,
        );
      }
    }
  }

  /**
   * Prints a taxonomy selection form for each vocabulary.
   *
   * @param $tid
   *   Default selected value(s).
   * @return
   *   HTML output to print to screen.
   */
  private function taxonomySelect($tid = 0) {
    $options = array();
    foreach (quizz()->getVocabularies() as $vid => $vocabulary) {
      $temp = taxonomy_form($vid, $tid);
      $options = array_merge($options, $temp['#options']);
    }
    return $options;
  }

  /**
   * Adds the questions in the $questions array to the form
   *
   * @param array $form
   * @param array[] $relationships
   * @param QuizEntity $quiz
   */
  private function addQuestionsToForm(&$form, $relationships, $quiz) {
    $form['question_list']['weights'] = array('#tree' => TRUE);
    $form['question_list']['qr_ids'] = array('#tree' => TRUE);
    $form['question_list']['qr_pids'] = array('#tree' => TRUE);
    $form['question_list']['max_scores'] = array('#tree' => TRUE);
    $form['question_list']['auto_update_max_scores'] = array('#tree' => TRUE);
    $form['question_list']['stayers'] = array('#tree' => TRUE);
    $form['question_list']['revision'] = array('#tree' => TRUE);
    if ($quiz->randomization == 2) {
      $form['question_list']['compulsories'] = array('#tree' => TRUE);
    }

    foreach ($relationships as $relationship) {
      $relationship = is_array($relationship) ? (object) $relationship : $relationship;
      $question = quizz_question_load($relationship->qid);
      $handler = $question->getHandler();

      $fieldset = 'question_list';
      $id = "{$relationship->qid}-{$relationship->vid}";

      $form['question_list']['#question_handlers'][$id] = $question->getQuestionType()->handler;

      $form[$fieldset]['weights'][$id] = array(
          '#type'          => 'textfield',
          '#size'          => 3,
          '#maxlength'     => 4,
          '#default_value' => $relationship->weight,
      );

      $form[$fieldset]['qr_pids'][$id] = array(
          '#type'          => 'textfield',
          '#size'          => 3,
          '#maxlength'     => 4,
          '#default_value' => $relationship->qr_pid,
      );

      $form[$fieldset]['qr_ids'][$id] = array(
          '#type'          => 'textfield',
          '#size'          => 3,
          '#maxlength'     => 4,
          '#default_value' => $relationship->qr_id,
      );

      // Quiz directions don't have scoring…
      $form[$fieldset]['max_scores'][$id] = array(
          '#type'          => $handler->isGraded() ? 'textfield' : 'hidden',
          '#size'          => 2,
          '#maxlength'     => 2,
          '#disabled'      => isset($question->auto_update_max_score) ? $question->auto_update_max_score : FALSE,
          '#default_value' => isset($relationship->max_score) ? $relationship->max_score : 0,
          '#states'        => array(
              'disabled' => array("#edit-auto-update-max-scores-$id" => array('checked' => TRUE))
          ),
      );

      $form[$fieldset]['auto_update_max_scores'][$id] = array(
          '#type'          => $handler->isGraded() ? 'checkbox' : 'hidden',
          '#default_value' => isset($question->auto_update_max_score) ? $question->auto_update_max_score : 0,
      );

      // Add checkboxes to remove questions in js disabled browsers…
      $form[$fieldset]['stayers'][$id] = array(
          '#type'          => 'checkbox',
          '#default_value' => 0,
          '#attributes'    => array('class' => array('q-staying')),
      );

      //Add checkboxes to mark compulsory questions for randomized quizzes.
      if ($quiz->randomization == 2) {
        $form[$fieldset]['compulsories'][$id] = array(
            '#type'          => 'checkbox',
            '#default_value' => ($relationship->question_status == QUIZZ_QUESTION_ALWAYS) ? 1 : 0,
            '#attributes'    => array('class' => array('q-compulsory')),
        );
      }

      if (user_access('view quiz question outside of a quiz')) {
        $link_options = array(
            'attributes' => array('class' => array('handle-changes')),
        );
        $question_titles = l($question->title, 'quiz-question/' . $question->qid, $link_options);
      }
      else {
        $question_titles = check_plain($question->title);
      }

      $handler_info = $question->getHandlerInfo();
      $form[$fieldset]['titles'][$id] = array('#markup' => $question_titles);
      $form[$fieldset]['types'][$id] = array(
          '#markup'        => $handler_info['name'],
          '#question_type' => $question->type,
      );

      $form[$fieldset]['view_links'][$id] = array(
          '#markup' => l(
            t('Edit'), 'quiz-question/' . $question->qid . '/edit', array(
              'query'      => drupal_get_destination(),
              'attributes' => array('class' => array('handle-changes')),
            )
          ),
          '#access' => entity_access('update', 'quiz_entity', $question),
      );

      // For js enabled browsers questions are removed by pressing a remove link
      $form[$fieldset]['remove_links'][$id] = array(
          '#markup' => '<a href="#" class="rem-link">' . t('Remove') . '</a>',
      );

      // Add a checkbox to update to the latest revision of the question
      if ($relationship->vid == $question->vid) {
        $update_cell = array('#markup' => t('<em>Up to date</em>'));
      }
      else {
        $update_cell = array(
            '#type'  => 'checkbox',
            '#title' => l(t('Latest'), 'quiz-question/' . $question->qid . '/revisions/' . $question->vid . '/view')
            . ' of ' .
            l(t('revisions'), 'quiz-question/' . $question->qid . '/revisions'),
        );
      }
      $form[$fieldset]['revision'][$id] = $update_cell;
    }
  }

  /**
   * Validate that the supplied questions are real.
   */
  public function formValidate($form, $form_state) {
    if (!$quiz = quizz_load(quizz_get_id_from_url())) {
      $msg = t('A critical error has occured. Please report error code 28 on the quiz project page.');
      form_set_error('changed', $msg);
      return;
    }

    if ($quiz->changed > $form_state['values']['timestamp']) {
      $msg = t('This content has been modified by another user, changes cannot be saved.');
      form_set_error('changed', $msg);
    }

    $already_checked = array();

    // Make sure the number of random questions is a positive number
    if (isset($form_state['values']['num_random_questions']) && !quizz_valid_integer($form_state['values']['num_random_questions'], 0)) {
      form_set_error('num_random_questions', 'The number of random questions needs to be a positive number');
    }

    // Make sure the max score for random questions is a positive number
    if (isset($form_state['values']['max_score_for_random']) && !quizz_valid_integer($form_state['values']['max_score_for_random'], 0)) {
      form_set_error('max_score_for_random', 'The max score for random questions needs to be a positive number');
    }

    $weight_map = isset($form_state['values']['weights']) ? $form_state['values']['weights'] : array();
    foreach (array_keys($weight_map) as $id) {
      list($question_qid, ) = explode('-', $id, 2);

      // If a question isn't one of the question types we remove it from the question list
      $valid_question = db_select('quiz_question_entity', 'question')
        ->fields('question', array('qid'))
        ->condition('question.qid', $question_qid)
        ->execute()
        ->fetchField();
      if (!$valid_question) {
        form_set_error('none', 'One of the supplied questions was invalid. It has been removed from the quiz.');
        unset($form_state['values']['weights'][$id]);
      }
      // We also make sure that we don't have duplicate questions in the quiz.
      elseif (in_array($question_qid, $already_checked)) {
        form_set_error('none', 'A duplicate question has been removed. You can only ask a question once per quiz.');
        unset($form_state['values']['weights'][$id]);
      }
      else {
        $already_checked[] = $question_qid;
      }
    }

    // We make sure max score is a positive number
    $max_scores = isset($form_state['values']['max_scores']) ? $form_state['values']['max_scores'] : array();
    foreach ($max_scores as $id => $max_score) {
      if (!quizz_valid_integer($max_score, 0)) {
        form_set_error("max_scores][$id", t('Max score needs to be a positive number'));
      }
    }
  }

  /**
   * @param array $form_state
   * @return QuizEntity
   */
  private function formSubmitFindQuiz($form_state) {
    $quiz = quizz_load(quizz_get_id_from_url());

    // Update the refresh latest quizzes table so that we know what the users latest quizzes are
    $auto_revisioning = $quiz->getQuizType()->getConfig('quiz_auto_revisioning', 1);
    $new_revision = $auto_revisioning ? $quiz->isAnswered() : (bool) $form_state['values']['new_revision'];
    if ($new_revision) {
      $quiz->is_new_revision = $new_revision;
      $quiz->old_vid = $quiz->vid;
      $quiz->save();
    }

    return $quiz;
  }

  /**
   * Submit function for quiz_questions.
   * Updates from the "manage questions" tab.
   */
  public function formSubmit($form, &$form_state) {
    $quiz = $this->formSubmitFindQuiz($form_state);

    $this->formSubmitQuestionBrowser($form_state);

    $weight_map = isset($form_state['values']['weights']) ? $form_state['values']['weights'] : array();
    $qr_pids_map = isset($form_state['values']['qr_pids']) ? $form_state['values']['qr_pids'] : array();
    $qr_ids_map = isset($form_state['values']['qr_ids']) ? $form_state['values']['qr_ids'] : array();
    $max_scores = isset($form_state['values']['max_scores']) ? $form_state['values']['max_scores'] : array();
    $auto_update_max_scores = isset($form_state['values']['auto_update_max_scores']) ? $form_state['values']['auto_update_max_scores'] : array();
    $refreshes = isset($form_state['values']['revision']) ? $form_state['values']['revision'] : NULL;
    $stayers = isset($form_state['values']['stayers']) ? $form_state['values']['stayers'] : array();
    $compulsories = isset($form_state['values']['compulsories']) ? $form_state['values']['compulsories'] : NULL;
    $num_random = isset($form_state['values']['num_random_questions']) ? $form_state['values']['num_random_questions'] : 0;
    $quiz->max_score_for_random = isset($form_state['values']['max_score_for_random']) ? $form_state['values']['max_score_for_random'] : 1;
    $term_id = isset($form_state['values']['random_term_id']) ? (int) $form_state['values']['random_term_id'] : 0;

    // Store what questions belong to the quiz
    $relationships = $this->updateItems($quiz, $weight_map, $max_scores, $auto_update_max_scores, $refreshes, $stayers, $qr_ids_map, $qr_pids_map, $compulsories, $stayers);

    // If using random questions and no term ID is specified, make sure we have enough.
    if (empty($term_id)) {
      $assigned_random = 0;
      foreach ($relationships as $relationship) {
        if (QUIZZ_QUESTION_RANDOM == $relationship->question_status) {
          ++$assigned_random;
        }
      }

      // Adjust number of random questions downward to match number of selected questions..
      if ($num_random > $assigned_random) {
        $num_random = $assigned_random;
        drupal_set_message(t('The number of random questions for this @quiz have been lowered to %anum to match the number of questions you assigned.', array('@quiz' => QUIZZ_NAME, '%anum' => $assigned_random), array('langcode' => 'warning')));
      }
    }
    else {
      // Warn user if not enough questions available with this term_id.
      $available_random = $quiz->getQuestionIO()->getRandomTaxonomyQuestionIds($term_id, $num_random);
      if ($num_random > $available_random) {
        $num_random = $available_random;
        drupal_set_message(t('There are currently not enough questions assigned to this term (@random). Please lower the number of random quetions or assign more questions to this taxonomy term before taking this @quiz.', array('@random' => $available_random, '@quiz' => QUIZZ_NAME)), 'error');
      }
    }

    // Get sum of max_score
    $query = db_select('quiz_relationship', 'qnr');
    $query->addExpression('SUM(max_score)', 'sum');
    $query->condition('quiz_vid', $quiz->vid);
    $query->condition('question_status', QUIZZ_QUESTION_ALWAYS);
    $score = $query->execute()->fetchAssoc();

    // Update the quiz's properties.
    $quiz->number_of_random_questions = $num_random ? $num_random : 0;
    $quiz->max_score_for_random = $quiz->max_score_for_random;
    $quiz->tid = $term_id;
    $quiz->max_score = $quiz->max_score_for_random * $quiz->number_of_random_questions + $score['sum'];

    if (entity_save('quiz_entity', $quiz)) {
      drupal_set_message(t('Questions updated successfully.'));
    }
    else {
      drupal_set_message(t('There was an error updating the @quiz.', array('@quiz' => QUIZZ_NAME)), 'error');
    }
  }

  /**
   * Takes care of the browser part of the submitted form values.
   *
   * This function changes the form_state to reflect questions added via the browser.
   * (Especially if js is disabled)
   */
  private function formSubmitQuestionBrowser(&$form_state) {
    // Find the biggest weight:
    $weight = isset($form_state['values']['weights']) ? max($form_state['values']['weights']) : 0;

    // If a question is chosen in the browser, add it to the question list if it isn't already there
    $titles = isset($form_state['values']['browser']['table']['titles']) ? $form_state['values']['browser']['table']['titles'] : array();
    foreach ($titles as $id) {
      if ($id) {
        list($question_qid, $question_vid) = explode('-', $id, 2);
        $question = quizz_question_load($question_qid, $question_vid);
        $form_state['values']['weights'][$id] = ++$weight;
        $form_state['values']['max_scores'][$id] = $question->max_score;
        $form_state['values']['stayers'][$id] = 1;
      }
    }
  }

  /**
   * Update a quiz set of items with new weights and membership
   * @param QuizEntity $quiz
   * @param int $weight_map
   *   Weights for each question(determines the order in which the question will be taken by the quiz taker)
   * @param $max_scores
   *   Array of max scores for each question
   * @param $new_revision
   *   Array of boolean values determining if the question is to be updated to the newest revision
   * @param $refreshes
   *   True if we are creating a new revision of the quiz
   * @param $stayers
   *   Questions added to the quiz
   * @param bool[] $compulsories
   *   Array of boolean values determining if the question is compulsory or not.
   * @return array set of questions after updating
   */
  private function updateItems(QuizEntity $quiz, $weight_map, $max_scores, $auto_update_max_scores, $refreshes, $stayers, $qr_ids, $qr_pids, $compulsories = NULL) {
    $relationships = array();

    foreach ($weight_map as $id => $weight) {
      if ($stayers[$id]) {
        continue;
      }

      list($question_qid, $question_vid) = explode('-', $id, 2);
      $relationship = entity_create('quiz_relationship', array(
          'quiz_qid'              => $quiz->qid,
          'quiz_vid'              => $quiz->vid,
          'question_qid'          => (int) $question_qid,
          'question_vid'          => (int) $question_vid,
          'weight'                => $weight,
          'auto_update_max_score' => $auto_update_max_scores[$id],
          'qr_pid'                => $qr_pids[$id] > 0 ? $qr_pids[$id] : NULL,
          'qr_id'                 => $qr_ids[$id] > 0 ? $qr_ids[$id] : NULL,
          'refresh'               => isset($refreshes[$id]) && $refreshes[$id] == 1,
          'question_status'       => QUIZZ_QUESTION_ALWAYS,
      ));

      if (isset($compulsories) && $compulsories[$id] != 1) {
        $relationship->question_status = QUIZZ_QUESTION_RANDOM;
        $max_scores[$id] = $quiz->max_score_for_random;
      }

      $relationship->max_score = $max_scores[$id];

      // Add item as an object in the questions array.
      $relationships[] = $relationship;
    }

    $quiz->getQuestionIO()->setRelationships($relationships);

    return $relationships;
  }

}
