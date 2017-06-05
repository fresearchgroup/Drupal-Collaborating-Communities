<?php

namespace Drupal\quizz\Form;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Form\QuizQuestionsBaseForm;

class QuizCategorizedForm extends QuizQuestionsBaseForm {

  /**
   * Form for managing what questions should be added to a quiz with categorized random questions.
   *
   * @param array $form_state
   * @param QuizEntity $quiz
   *  The quiz entity
   */
  public function getForm($form, $form_state, QuizEntity $quiz) {
    $form['#tree'] = TRUE;
    $form['#theme'] = 'quizz_categorized_form';
    $form['#quiz'] = $quiz;

    $this->existingTermsForm($form, $form_state, $quiz);
    $this->categorizedNewTermForm($form, $form_state, $quiz);

    $form['qid'] = array('#type' => 'value', '#value' => $quiz->qid);
    $form['vid'] = array('#type' => 'value', '#value' => $quiz->vid);
    $form['tid'] = array('#type' => 'value', '#value' => NULL);

    // Give the user the option to create a new revision of the quiz
    $this->addRevisionCheckbox($form, $quiz);

    // Timestamp is needed to avoid multiple users editing the same quiz at the same time.
    $form['timestamp'] = array('#type' => 'hidden', '#default_value' => REQUEST_TIME);
    $form['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

    return $form;
  }

  /**
   * @param array $form
   * @param array $form_state
   * @param QuizEntity $quiz
   */
  private function existingTermsForm(&$form, $form_state, $quiz) {
    if ($terms = $quiz->getTerms()) {
      if (empty($form_state['input']) && !$quiz->getQuestionIO()->buildCategoziedQuestionList()) {
        drupal_set_message(t('There are not enough questions in the requested categories.'), 'warning');
      }
    }

    foreach ($terms as $term) {
      $form[$term->tid]['name'] = array('#markup' => check_plain($term->name));
      $form[$term->tid]['number'] = array('#type' => 'textfield', '#size' => 3, '#default_value' => $term->number);
      $form[$term->tid]['max_score'] = array('#type' => 'textfield', '#size' => 3, '#default_value' => $term->max_score);
      $form[$term->tid]['remove'] = array('#type' => 'checkbox', '#default_value' => 0);
      $form[$term->tid]['weight'] = array(
          '#type'          => 'textfield',
          '#size'          => 3,
          '#default_value' => $term->weight,
          '#attributes'    => array('class' => array('term-weight')),
      );
    }
  }

  /**
   * Form for adding new terms to a quiz
   *
   * @see quizz_categorized_form
   */
  private function categorizedNewTermForm(&$form, $form_state, $quiz) {
    $form['new'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Add category'),
        '#collapsible' => FALSE,
        '#collapsed'   => FALSE,
        '#tree'        => FALSE,
    );
    $form['new']['term'] = array(
        '#type'              => 'textfield',
        '#title'             => t('Category'),
        '#description'       => t('Type in the name of the term you would like to add questions from.'),
        '#autocomplete_path' => "quiz/" . $quiz->qid . "/questions/term_ahah",
        '#field_suffix'      => '<a id="browse-for-term" href="javascript:void(0)">' . t('browse') . '</a>',
    );
    $form['new']['number'] = array(
        '#type'        => 'textfield',
        '#title'       => t('Number of questions'),
        '#description' => t('How many questions would you like to draw from this term?'),
    );
    $form['new']['max_score'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Max score for each question'),
        '#description'   => t('The number of points a user will be awarded for each question he gets correct.'),
        '#default_value' => 1,
    );
  }

  /**
   * Validate the categorized form
   */
  function formValidate($form, &$form_state) {
    /* @var $quiz QuizEntity */
    $quiz = $form['#quiz'];

    if (!quizz_valid_integer($quiz->qid)) {
      $msg = t('A critical error has occured. Please report error code 28 on the quiz project page.');
      form_set_error('changed', $msg);
      return;
    }

    if ($quiz->changed > $form_state['values']['timestamp']) {
      $msg = t('This content has been modified by another user, changes cannot be saved.');
      form_set_error('changed', $msg);
    }

    if (!empty($form_state['values']['term'])) {
      $tid = $this->getIdFromString($form_state['values']['term']);
      if ($tid === FALSE) {
        $terms = static::searchTerms($form_state['values']['term']);
        $num_terms = count($terms);
        if ($num_terms == 1) {
          $tid = key($terms);
        }
        elseif ($num_terms > 1) {
          form_set_error('term', t('You need to be more specific, or use the autocomplete feature. The term name you entered matches several terms: %terms', array('%terms' => implode(', ', $terms))));
        }
        elseif ($num_terms == 0) {
          form_set_error('term', t("The term name you entered doesn't match any registered question terms."));
        }
      }

      if (in_array($tid, array_keys($form))) {
        form_set_error('term', t('The category you are trying to add has already been added to this quiz.'));
      }
      else {
        form_set_value($form['tid'], $tid, $form_state);
      }

      if (!quizz_valid_integer($form_state['values']['number'])) {
        form_set_error('number', t('The number of questions needs to be a positive integer'));
      }

      if (!quizz_valid_integer($form_state['values']['max_score'], 0)) {
        form_set_error('max_score', t('The max score needs to be a positive integer or 0'));
      }
    }
  }

  /**
   * Searches for an id in the end of a string.
   *
   * Id should be written like "(id:23)"
   *
   * @param string $string
   *  The string where we will search for an id
   * @return int
   *  The matched integer
   */
  private function getIdFromString($string) {
    $matches = array();
    preg_match('/\(id:(\d+)\)$/', $string, $matches);
    return isset($matches[1]) ? (int) $matches[1] : FALSE;
  }

  /**
   * Submit the categorized form
   */
  public function formSubmit($form, $form_state) {
    $quiz = quizz_load($form_state['values']['qid'], $form_state['values']['vid']);
    $quiz->number_of_random_questions = 0;

    // Update the refresh latest quizzes table so that we know what the users latest quizzes are
    if ($quiz->getQuizType()->getConfig('quiz_auto_revisioning', 1)) {
      $is_new_revision = $quiz->isAnswered();
    }
    else {
      $is_new_revision = (bool) $form_state['values']['new_revision'];
    }
    if (!empty($form_state['values']['tid'])) {
      $quiz->number_of_random_questions += $this->categorizedAddTerm($form, $form_state);
    }
    $quiz->number_of_random_questions += $this->categorizedUpdateTerms($form, $form_state);
    if ($is_new_revision) {
      $quiz->is_new_revision = 1;
    }

    // We save the node to update its timestamp and let other modules react to the update.
    // We also do this in case a new revision is required…
    $quiz->save();
  }

  /**
   * Adds a term to a categorized quiz
   *
   * This is a helper function for the submit function.
   */
  private function categorizedAddTerm($form, $form_state) {
    drupal_set_message(t('The term was added'));

    // Needs to be set to avoid error-message from db:
    $form_state['values']['weight'] = 0;
    drupal_write_record('quiz_entity_terms', $form_state['values']);
    return $form_state['values']['number'];
  }

  /**
   * Update the categoriez belonging to a quiz with categorized random questions.
   *
   * Helper function for quizz_categorized_form_submit
   */
  private function categorizedUpdateTerms(&$form, &$form_state) {
    $changed = array();
    $removed = array();
    $num_questions = 0;
    foreach ($form_state['values'] as $key => $existing) {
      if (is_numeric($key)) {
        $this->categorizedUpdateTerm($form, $form_state, $key, $existing, $num_questions, $changed, $removed);
      }
    }

    if (!empty($changed)) {
      $msg = t('Updates were made for the following terms: %terms', array('%terms' => implode(', ', $changed)));
      drupal_set_message($msg);
    }

    if (!empty($removed)) {
      $msg = t('The following terms were removed: %terms', array('%terms' => implode(', ', $removed)));
      drupal_set_message($msg);
    }

    return $num_questions;
  }

  private function categorizedUpdateTerm($form, $form_state, $key, $existing, &$num_questions, &$changed, &$removed) {
    if (!$existing['remove']) {
      $num_questions += $existing['number'];
    }

    foreach (array('weight', 'max_score', 'number') as $id) {
      if ($existing[$id] != $form[$key][$id]['#default_value'] && !$existing['remove']) {
        $existing['qid'] = $form_state['values']['qid'];
        $existing['vid'] = $form_state['values']['vid'];
        $existing['tid'] = $key;
        if (empty($existing['weight'])) {
          $existing['weight'] = 1;
        }
        $changed[] = $form[$key]['name']['#markup'];
        drupal_write_record('quiz_entity_terms', $existing, array('vid', 'tid'));
        break;
      }
      elseif ($existing['remove']) {
        db_delete('quiz_entity_terms')
          ->condition('tid', $key)
          ->condition('vid', $form_state['values']['vid'])
          ->execute();
        $removed[] = $form[$key]['name']['#markup'];
        break;
      }
    }
  }

  /**
   * Helper function for finding terms…
   *
   * @param string $start
   *  The start of the string we are looking for
   */
  public static function searchTerms($start, $all = FALSE) {
    if (!$sql_args = array_keys(quizz()->getVocabularies())) {
      return array();
    }

    $query = db_select('taxonomy_term_data', 't')
      ->fields('t', array('name', 'tid'))
      ->condition('t.vid', $sql_args);
    if (!$all) {
      $query->condition('t.name', '%' . $start . '%', 'LIKE');
    }
    $result = $query->execute();

    // @TODO: Don't user db_fetch_object
    $terms = array();
    while ($row = $result->fetch()) {
      $terms[$row->tid] = $row->name;
    }

    return $terms;
  }

  /**
   * Callback for quiz/%/questions/term_ahah. Ahah function for finding terms…
   *
   * @param string $start
   *  The start of the string we are looking for
   */
  public static function categorizedTermAhah($start) {
    foreach (static::searchTerms($start, $start == '*') as $key => $value) {
      $return["$value (id:$key)"] = $value;
    }
    drupal_json_output(!empty($return) ? $return : array());
  }

}
