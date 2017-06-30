<?php

use Drupal\quizz\Controller\Admin\QuizAdminSettingsController;
use Drupal\quizz\Controller\Admin\QuizRevisionsAdminController;
use Drupal\quizz\Controller\QuestionFeedbackController;
use Drupal\quizz\Controller\QuizTakeController;
use Drupal\quizz\Controller\QuizTakeQuestionController;
use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;
use Drupal\quizz\Form\QuizAdminEntityForm;
use Drupal\quizz\Form\QuizAnsweringForm;
use Drupal\quizz\Form\QuizCategorizedForm;
use Drupal\quizz\Form\QuizForm;
use Drupal\quizz\Form\QuizQuestionsForm;
use Drupal\quizz\Form\QuizReportForm;
use Drupal\quizz\Form\QuizTypeForm;
use Drupal\quizz_question\Entity\Question;

/**
 * Form for /admin/quizz/settings/config.
 */
function quizz_admin_settings_form($form, $form_state) {
  $obj = new QuizAdminSettingsController();
  return $obj->getForm($form, $form_state);
}

function quizz_admin_settings_form_validate($form, $form_state) {
  $obj = new QuizAdminSettingsController();
  return $obj->validate($form, $form_state);
}

function quizz_admin_settings_form_submit($form, $form_state) {
  $obj = new QuizAdminSettingsController();
  return $obj->submit($form, $form_state);
}

function quizz_admin_entity_form($form, $form_state) {
  $obj = new QuizAdminEntityForm();
  return $obj->getForm($form, $form_state);
}

function quizz_admin_entity_form_validate($form, $form_state) {
  $obj = new QuizAdminEntityForm();
  return $obj->validateForm($form, $form_state);
}

function quizz_admin_entity_form_submit($form, $form_state) {
  $obj = new QuizAdminEntityForm();
  return $obj->submitForm($form, $form_state);
}

/**
 * Define quiz-type editing form.
 */
function quiz_type_form($form, &$form_state, $quiz_type, $op = 'edit') {
  $obj = new QuizTypeForm();
  return $obj->get($form, $form_state, $quiz_type, $op);
}

function quiz_type_form_submit_delete($form, &$form_state) {
  $form_state['redirect'] = 'admin/quizz/types/manage/' . $form_state['quiz_type']->type . '/delete';
}

function quiz_type_form_submit($form, &$form_state) {
  $obj = new QuizTypeForm();
  return $obj->submit($form, $form_state);
}

/**
 * @TODO Only list quiz type if user has permission to create it.
 * @return string
 */
function quizz_entity_adding_landing_page() {
  return theme_render_template(drupal_get_path('module', 'quizz') . '/theme/quiz-adding-landing.tpl.php', array(
      'quiz_types' => quizz_get_types()
  ));
}

/**
 * Callback for /quiz/add/%
 *
 * @param string $quiz_type_name
 */
function quizz_entity_adding_page($quiz_type_name) {
  $quiz = entity_create('quiz_entity', array('type' => $quiz_type_name));
  return entity_ui_get_form('quiz_entity', $quiz, 'add');
}

/**
 * Define quiz-entity editing form.
 */
function quiz_entity_form($form, &$form_state, $quiz, $op = 'edit') {
  $obj = new QuizForm($quiz);
  return $obj->get($form, $form_state, $op);
}

function quiz_entity_form_validate($form, &$form_state) {
  $quiz = $form_state['quiz_entity'];
  $obj = new QuizForm($quiz);
  return $obj->validate($form, $form_state);
}

function quiz_entity_form_submit($form, &$form_state) {
  $quiz = $form_state['quiz_entity'];
  $obj = new QuizForm($quiz);
  return $obj->submit($form, $form_state);
}

/**
 * Page callback for /quiz/%quizz/questions
 */
function quizz_admin_question_page($form, $form_state, QuizEntity $quiz) {
  // Vertical tabs does not play well without form.
  $form['hack_prefix'] = array('#weight' => -1000, '#markup' => '</div></form>');
  $form['hack_suffix'] = array('#weight' => 1000, '#markup' => '<form><div>');

  if ($quiz->randomization >= 3) {
    $form['quizz_categorized_form'] = drupal_get_form('quizz_categorized_form', $quiz);
    return $form;
  }

  ctools_include('dropdown.theme');

  $links = array(
      'title' => t('Create new question'),
      'class' => 'quiz-add-question ctools-button ctools-dropbutton',
      'image' => '',
      'links' => array(
          'options' => array(
              'query' => array('destination' => 'quiz/' . $quiz->identifier() . '/questions')
          )
      )
  );

  foreach (quizz_question_get_types() as $name => $question_type) {
    $links['links'][] = array(
        'title'      => $question_type->label,
        'href'       => 'quiz-question/add/' . str_replace('_', '-', $name),
        'query'      => array('destination' => 'quiz/' . $quiz->identifier() . '/questions'),
        'attributes' => array('title' => strip_tags($question_type->description)),
    );
  }

  $form['links'] = array('#markup' => theme_ctools_dropdown($links));

  $form['vtabs'] = array(
      '#type'            => 'vertical_tabs',
      'question_admin'   => array(
          '#type'  => 'fieldset',
          '#title' => t('Manage questions'),
          'form'   => drupal_get_form('quizz_questions_form', $quiz),
      ),
      'global_questions' => array(
          '#type'  => 'fieldset',
          '#title' => t('Question bank'),
          '#value' => views_get_view($quiz->getQuizType()->getConfig('quiz_views_question_bank', 'quizz_question_bank'))->preview(),
      ),
  );

  if (module_exists('field_group')) {
    $form['vtabs']['#type'] = 'horizontal_tabs';
    $form['vtabs']['#attached']['library'][] = array('field_group', 'horizontal-tabs');
  }

  return $form;
}

/**
 * Quiz's questions management form.
 */
function quizz_questions_form($form, $form_state, QuizEntity $quiz) {
  $obj = new QuizQuestionsForm();
  $array = $obj->formGet($form, $form_state, $quiz);
  $array['#validate'][] = 'quizz_questions_form_validate';
  return $array;
}

function quizz_questions_form_validate($form, $form_state) {
  $obj = new QuizQuestionsForm();
  return $obj->formValidate($form, $form_state);
}

function quizz_questions_form_submit($form, $form_state) {
  $obj = new QuizQuestionsForm();
  return $obj->formSubmit($form, $form_state);
}

function quizz_categorized_form($form, $form_state, QuizEntity $quiz) {
  $obj = new QuizCategorizedForm();
  return $obj->getForm($form, $form_state, $quiz);
}

function quizz_categorized_form_validate($form, &$form_state) {
  $obj = new QuizCategorizedForm();
  return $obj->formValidate($form, $form_state);
}

function quizz_categorized_form_submit($form, &$form_state) {
  $obj = new QuizCategorizedForm();
  return $obj->formSubmit($form, $form_state);
}

/**
 * Callback for quiz/%quiz
 */
function quizz_page(QuizEntity $quiz, $view_mode = 'full', $langcode = NULL) {
  return entity_view('quiz_entity', array($quiz), $view_mode, $langcode);
}

/**
 * Callback for /quiz/%quizz/revisions.
 *
 * @param QuizEntity $quiz
 */
function quizz_revisions_page(QuizEntity $quiz) {
  $obj = new QuizRevisionsAdminController();
  return $obj->render($quiz);
}

/**
 * Callback for quiz/%/take
 */
function quizz_take_page(QuizEntity $quiz, $page_number = NULL) {
  if (NULL !== $page_number && is_numeric($page_number)) {
    return !quizz_access_question($quiz, $page_number) ? drupal_access_denied() : quizz_take_question_page($quiz, $page_number);
  }

  global $user;
  $obj = new QuizTakeController($quiz, $user);
  return $obj->render();
}

/**
 * Callback for quiz/%/take/%. Take a quiz questions.
 *
 * @param QuizEntity $quiz A quiz entity
 * @param int $page_number
 *   A question number, starting at 1. Pages do not have question numbers. Quiz
 *   directions are considered part of the numbering.
 */
function quizz_take_question_page(QuizEntity $quiz, $page_number) {
  $result = $layout_item = NULL;
  $quiz_id = $quiz->qid;

  if (isset($_SESSION['quiz'][$quiz_id]['result_id'])) {
    $result = quizz_result_load($_SESSION['quiz'][$quiz_id]['result_id']);
  }

  // Load the page that the requested question belongs to.
  if ($result && ($_layout_item = $result->getPageItem($page_number))) {
    $layout_item = quizz_question_load($_layout_item['qid'], $_layout_item['vid']);
  }

  $controller = new QuizTakeQuestionController($quiz, $result, $page_number, $layout_item);
  return $controller->render();
}

/**
 * Jumper form.
 */
function quizz_jumper_form($form, $form_state, $questions, $current) {
  $form['#attached']['js'][] = drupal_get_path('module', 'quizz') . '/misc/js/quiz.jumper.js';
  $form['question_number'] = array(
      '#type'          => 'select',
      '#options'       => $questions,
      '#default_value' => $current,
  );
  $form['submit'] = array(
      '#type'       => 'submit',
      '#value'      => t('Jump'),
      '#attributes' => array('class' => array('js-hide')),
  );
  return $form;
}

function quizz_jumper_form_submit($form, &$form_state) {
  $quiz = quizz_load(quizz_get_id_from_url());
  $_SESSION['quiz'][$quiz->qid]['current'] = $form_state['values']['question_number'] - 1;
  $form_state['redirect'] = "quiz/" . $quiz->qid . "/take/" . $form_state['values']['question_number'];
}

/**
 * Question answering form.
 *
 * @param array $form
 * @param array $form_state
 * @param QuizEntity $quiz
 * @param Question $question
 * @param int $page_number
 * @param Result $result
 * @return array
 */
function quiz_answer_form($form, $form_state, QuizEntity $quiz, Question $question, $page_number, Result $result) {
  $obj = new QuizAnsweringForm($quiz, $question, $page_number, $result);
  if (is_array($question) || ('quiz_page' !== $question->getQuestionType()->handler)) {
    return $obj->getForm($form, $form_state, is_array($question) ? $question : array($question));
  }
  return $obj->getForm($form, $form_state, QuizAnsweringForm::findPageQuestions($result, $question));
}

function quiz_answer_form_validate($form, &$form_state) {
  $quiz = $form['#quiz'];
  $question = $form['#question'];
  $page_number = $form['#page_number'];
  $result = $form['#result'];
  $obj = new QuizAnsweringForm($quiz, $question, $page_number, $result);
  return $obj->formValidate($form, $form_state);
}

function quiz_answer_form_submit_back($form, &$form_state) {
  $quiz = $form['#quiz'];
  $question = $form['#question'];
  $page_number = $form['#page_number'];
  $result = $form['#result'];
  $obj = new QuizAnsweringForm($quiz, $question, $page_number, $result);
  return $obj->getSubmit()->formBackSubmit($form, $form_state);
}

function quiz_answer_form_submit_blank($form, &$form_state) {
  $quiz = $form['#quiz'];
  $question = $form['#question'];
  $page_number = $form['#page_number'];
  $result = $form['#result'];
  $obj = new QuizAnsweringForm($quiz, $question, $page_number, $result);
  return $obj->getSubmit()->formBlankSubmit($form, $form_state);
}

function quiz_answer_form_submit($form, &$form_state) {
  $quiz = $form['#quiz'];
  $question = $form['#question'];
  $page_number = $form['#page_number'];
  $result = $form['#result'];
  $obj = new QuizAnsweringForm($quiz, $question, $page_number, $result);
  return $obj->getSubmit()->formSubmit($form, $form_state);
}

function quizz_question_element_validate(&$element, &$form_state) {
  /* @var $question \Drupal\quizz_question\Entity\Question */
  $question = $form_state['build_info']['args'][1];
  $result = $form_state['build_info']['args'][3];
  return $question->getHandler()->elementValidate($result, $element, $form_state);
}

function quizz_report_form_element_validate(&$element, &$form_state) {
  return QuizReportForm::validateElement($element, $form_state);
}

/**
 * Callback for quiz/%/take/%quizz_question_number/feedback. Show feedback for a
 * question response.
 */
function quizz_question_feedback_page(QuizEntity $quiz, $page_number) {
  $result_id = empty($_SESSION['quiz'][$quiz->qid]['result_id']) ? $_SESSION['quiz']['temp']['result_id'] : $_SESSION['quiz'][$quiz->qid]['result_id'];
  $result = quizz_result_load($result_id);
  $obj = new QuestionFeedbackController($result);
  return $obj->render($page_number);
}

/**
 * Page callback for /quiz/%/results.
 * @param QuizEntity $quiz
 * @return type
 */
function quizz_results_page(QuizEntity $quiz) {
  $view_name = $quiz->getQuizType()->getConfig('quiz_views_results', 'quizz_results');
  return views_embed_view($view_name, 'default', $quiz->qid);
}

/**
 * Page callback for /quiz/%/my-results
 */
function quiz_results_user_page(QuizEntity $quiz) {
  global $user;
  $view_name = $quiz->getQuizType()->getConfig('quiz_views_user_results', 'quizz_user_results');
  return views_embed_view($view_name, 'default', $quiz->qid, $user->uid);
}

/**
 * Pass the correct mark to the theme so that theme authors can use an image.
 *
 * @ingroup themeable
 */
function theme_quizz_answer_result($variables) {
  return '<div class="quiz-score-icon ' . $variables['type'] . '"></div>';
}

/**
 * Theme a progress indicator for use during a quiz.
 *
 * @param $question_number
 *  The position of the current question in the sessions' array.
 * @param $num_of_question
 *  The number of questions for the quiz.
 * @return string
 *
 * @ingroup themeable
 */
function theme_quizz_progress($variables) {
  $output = '';
  $output .= '<div id="quiz-progress">';
  $text = 'Page <span id="quiz-question-number">!x</span> of <span id="quiz-num-questions">@y</span>';
  if ($variables['allow_jumping']) {
    // Show jump form.
    if ($variables['pager']) {
      $output .= theme('quizz_pager', array(
          'quiz'     => $variables['quiz'],
          'siblings' => $variables['quiz']->getQuizType()->getConfig('quiz_pager_siblings', 5),
          'current'  => $variables['current'],
          'total'    => count($variables['questions']),
      ));
    }
    else {
      $selectbox = drupal_get_form('quizz_jumper_form', $variables['questions'], $variables['current']);
      $output .= t($text, array('!x' => drupal_render($selectbox), '@y' => count($variables['questions'])));
    }
  }
  else {
    // Just text.
    $output .= t($text, array('!x' => $variables['current'], '@y' => count($variables['questions'])));
  }

  $output .= '</div>' . "\n";
  // Add div to be used by jQuery countdown
  if ($variables['time_limit']) {
    $output .= '<div class="countdown"></div>';
  }
  return $output;
}

/**
 * Theme the "no feedback" option.
 *
 * @return
 *  Themed html feedback.
 *
 * @ingroup themeable
 */
function theme_quizz_no_feedback() {
  return t('Thanks for taking the @quiz!', array('@quiz' => QUIZZ_NAME));
}

/**
 * Theme the stats on the views page
 */
function theme_quizz_view_stats($variables) {
  $quiz = $variables['quiz'];

  // Fetch data
  $stats[] = array('title' => t('Questions'), 'data' => isset($quiz->number_of_questions) ? $quiz->number_of_questions : 0);

  if ($quiz->show_attempt_stats) {
    $takes = $quiz->takes == 0 ? t('Unlimited') : $quiz->takes;
    $stats[] = array('title' => t('Attempts allowed'), 'data' => $takes);
  }

  if ($quiz->quiz_always) {
    $stats[] = array('title' => t('Available'), 'data' => t('Always'));
  }
  else {
    $stats[] = array('title' => t('Opens'), 'data' => format_date($quiz->quiz_open, 'short'));
    $stats[] = array('title' => t('Closes'), 'data' => format_date($quiz->quiz_close, 'short'));
  }

  if (!empty($quiz->pass_rate)) {
    $stats[] = array('title' => t('Pass rate'), 'data' => $quiz->pass_rate . ' %');
  }

  if (!empty($quiz->time_limit)) {
    $stats[] = array('title' => t('Time limit'), 'data' => quizz()->formatDuration($quiz->time_limit));
  }

  $stats[] = array('title' => t('Backwards navigation'), 'data' => $quiz->backwards_navigation ? t('Allowed') : t('Forbidden'));

  // Format and output the data
  $table = '';
  foreach ($stats as $stat) {
    $table .= '<tr>'
      . '<td class="quiz-view-table-title"><strong>' . $stat['title'] . ':</strong></td>'
      . '<td class="quiz-view-table-data"><em>' . $stat['data'] . '</em></td>'
      . '</tr>';
  }
  return '<table id="quiz-view-table">' . $table . '</table>';
}

/**
 * Callback for /quiz-result/%quizz_result.
 *
 * @param Result $result
 */
function quizz_result_page(Result $result) {
  return entity_view('quiz_result', array($result), 'default', $GLOBALS['language']->language, TRUE);
}

/**
 * Feedback form.
 */
function quizz_report_form($form, $form_state, $result, $questions) {
  $obj = new QuizReportForm();
  $form += $obj->getForm($form, $form_state, $result, $questions);

  if (isset($form['next'])) {
    $form['next']['#submit'] = array('quizz_report_form_submit_next');
  }

  if (isset($form['finish'])) {
    $form['finish']['#submit'] = array('quizz_report_form_submit_finish');
  }

  return $form;
}

function quizz_report_form_submit_next($form, &$form_state) {
  $obj = new QuizReportForm();
  return $obj->formSubmitFeedback($form, $form_state);
}

function quizz_report_form_submit_finish($form, &$form_state) {
  $obj = new QuizReportForm();
  return $obj->formEndSubmit($form, $form_state);
}

function quizz_report_form_submit($form, &$form_state) {
  $obj = new QuizReportForm();
  return $obj->formSubmit($form, $form_state);
}

/**
 * Theme the quiz pager.
 */
function theme_quizz_pager($variables) {
  return quizz()
      ->getQuizHelper()
      ->getTakeJumperHelper($variables['quiz'], $variables['total'], $variables['siblings'], $variables['current'])
      ->render();
}

/**
 * Theme a question selection table, adding drag and drop support.
 */
function theme_quizz_question_selection_table($variables) {
  $form = $variables['form'];

  // Building headers
  $headers = array(t('Question'), t('Type'), t('Actions'), t('Update'), t('Max score'), t('Auto update max score'), t('Delete'));
  if (isset($form['compulsories'])) {
    $headers[] = t('Compulsory');
  }
  $headers[] = t('Weight');
  $headers[] = t('Parent ID');
  $headers[] = array('data' => t('ID'), 'class' => array('tabledrag-hide'));

  // Building table body
  if (!empty($form['titles'])) {
    foreach (element_children($form['titles']) as $id) {
      $form['weights'][$id]['#attributes']['class'] = array('question-list-weight');
      $form['qr_ids'][$id]['#attributes']['class'] = array('relationship-id');
      $form['qr_pids'][$id]['#attributes']['class'] = array('relationship-pid');
      $rows[] = _quizz_get_question_row($form, $id, 'quiz_page' === $form['#question_handlers'][$id] ? 'tabledrag-root' : 'tabledrag-leaf');
    }
    // Make sure the same fields aren't rendered twice
    unset($form['types'], $form['view_links'], $form['remove_links'], $form['stayers']);
    unset($form['max_scores'], $form['auto_update_max_scores'], $form['revision'], $form['weights'], $form['titles'], $form['compulsories'], $form['qr_ids'], $form['qr_pids']);
  }

  // We hide the table if no questions have been added so that jQuery can show
  // it the moment the first question is beeing added.
  $html_attr = array();
  if (isset($form['no_questions'])) {
    $html_attr['style'] = "display:none;";
  }

  drupal_add_tabledrag('question-list', 'match', 'parent', 'relationship-pid', 'relationship-pid', 'relationship-id', TRUE);
  drupal_add_tabledrag('question-list', 'order', 'sibling', 'question-list-weight');

  return drupal_render($form['random_settings'])
    . theme('table', array(
        'header'     => $headers,
        'rows'       => !empty($rows) ? $rows : array(),
        'empty'      => t('No question'),
        'attributes' => array('id' => 'question-list') + $html_attr))
    . drupal_render_children($form);
}

/**
 * Helper function for theme_quizz_question_selection_table
 *
 * TODO: DELETE
 *
 * @see quizz_questions_form()
 * @see theme_quizz_question_selection_table()
 *
 * @param $sub_form
 *   Form definition array for a filtered questions list
 * @param $id
 *   Identifier used in $sub_form
 * @return table row
 *   Array defining a table row
 */
function _quizz_get_question_row($sub_form, $id, $item_type) {
  $type = $sub_form['types'][$id]['#markup'];

  $action = theme('item_list', array(
      'items'      => array(
          drupal_render($sub_form['view_links'][$id]),
          '<SPAN CLASS="q-remove" STYLE="display:none">' . drupal_render($sub_form['remove_links'][$id]) . '</SPAN>',
      ),
      'attributes' => array(
          'class' => array('links', 'inline'),
      ),
  ));
  $qr_pid = $sub_form['qr_pids'][$id]['#default_value'];
  $data_array = array(
      // The checkbox and the title
      ($qr_pid ? theme('indentation', array('size' => 1)) : NULL) . drupal_render($sub_form['titles'][$id]),
      $type,
      $action,
      isset($sub_form['revision'][$id]) ? drupal_render($sub_form['revision'][$id]) : t("Up to date"),
      drupal_render($sub_form['max_scores'][$id]),
      drupal_render($sub_form['auto_update_max_scores'][$id]),
      drupal_render($sub_form['stayers'][$id]),
  );
  if (isset($sub_form['compulsories'])) {
    $data_array[] = drupal_render($sub_form['compulsories'][$id]);
  }
  $data_array[] = drupal_render($sub_form['weights'][$id]);
  $data_array[] = drupal_render($sub_form['qr_pids'][$id]);
  $data_array[] = array(
      'class' => array('tabledrag-hide'),
      'data'  => drupal_render($sub_form['qr_ids'][$id]),
  );

  return array(
      'class' => array('q-row', 'draggable', $item_type),
      'id'    => 'q-' . $id,
      'data'  => $data_array
  );
}

/**
 * Themes a categorized quiz form
 */
function theme_quizz_categorized_form($variables) {
  $form = $variables['form'];

  drupal_add_js(drupal_get_path('module', 'quizz') . '/misc/js/quiz.admin.categorized-form.js');

  $rows = array();
  foreach ($form as $key => &$existing) {
    if (!is_numeric($key)) {
      continue;
    }
    $cols = array();
    $cols[] = drupal_render($existing['name']);
    $cols[] = drupal_render($existing['number']);
    $cols[] = drupal_render($existing['max_score']);
    $cols[] = drupal_render($existing['remove']);
    $cols[] = drupal_render($existing['weight']);
    $rows[] = array('data' => $cols, 'class' => array('draggable'));
  }

  $output = '';
  if (!empty($rows)) {
    $header = array(t('Category'), t('Number of questions'), t('Max score per question'), t('Remove'), t('Weight'));
    drupal_add_tabledrag('existing-terms', 'order', 'sibling', 'term-weight', NULL, NULL, TRUE);
    $output .= theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'existing-terms')));
  }
  return $output . drupal_render_children($form);
}

/**
 * Quiz result edit form.
 */
function quiz_result_form($form, &$form_state, Result $result) {
  global $user;

  $form_state['quiz_result'] = $result;

  field_attach_form('quiz_result', $result, $form, $form_state);

  if (($quiz = $result->getQuiz()) && $quiz->build_on_last) {
    if (entity_load('quiz_result', FALSE, array('quiz_vid' => $quiz->vid, 'uid' => $user->uid))) {
      $form['build_on_last'] = array(
          '#title'         => t('Keep answers from last attempt?'),
          '#type'          => 'radios',
          '#options'       => array(
              ''        => t('No answers'),
              'correct' => t('Only correct answers'),
              'all'     => t('All answers'),
          ),
          '#default_value' => $quiz->build_on_last,
          '#description'   => t('You can choose to keep previous answers or start a new attempt.'),
          '#access'        => $quiz->build_on_last,
      );
    }
  }

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
      '#type'  => 'submit',
      '#value' => t('Start @quiz', array('@quiz' => QUIZZ_NAME)),
  );

  return $form;
}

/**
 * Quiz result submit handler.
 */
function quiz_result_form_submit($form, &$form_state) {
  global $user;

  $base_result = $form_state['quiz_result'];
  $quiz = $base_result->getQuiz();
  field_attach_submit('quiz_result', $base_result, $form, $form_state);

  if (!empty($form_state['values']['build_on_last'])) {
    $quiz->build_on_last = $form_state['values']['build_on_last'];
  }

  quizz_entity_controller()
    ->getResultGenerator()
    ->generate($base_result->getQuiz(), $user, $base_result)
  ;
}
