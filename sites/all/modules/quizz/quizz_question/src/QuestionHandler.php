<?php

namespace Drupal\quizz_question;

use Drupal\quizz\Controller\QuestionFeedbackController;
use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;
use Drupal\quizz_question\Entity\Question;
use Drupal\quizz_question\Entity\QuestionController;
use Drupal\quizz_question\Entity\QuestionType;
use Drupal\quizz_question\Form\QuestionForm;

/**
 * Question handlers are made by extending these generic methods and abstract
 * methods. Check multichoice question handler for example.
 */
abstract class QuestionHandler implements QuestionHandlerInterface {

  /**
   * @var \Drupal\quizz_question\Entity\Question
   * The current question entity.
   */
  public $question = NULL;

  /** @var mixed[] */
  public $properties = NULL;

  /** @var string */
  protected $body_field_title = 'Question';

  /** @var string */
  protected $base_table = 'quiz_truefalse_question';

  /** @var string */
  protected $base_answer_table = 'quiz_truefalse_answer';

  /**
   * QuizQuestion constructor stores the node object.
   *
   * @param Question $question
   */
  public function __construct(Question $question) {
    $this->question = $question;
  }

  /**
   * {@inheritdoc}
   *
   * Create body field for new entity bundle (question type).
   */
  public function onNewQuestionTypeCreated(QuestionType $question_type) {
    if (!field_info_instance('quiz_question_entity', 'quiz_question_body', $question_type->type)) {
      $bundle = $question_type->type;

      if (!field_info_field('quiz_question_body')) {
        field_create_field(array(
            'field_name'   => 'quiz_question_body',
            'type'         => 'text_with_summary',
            'entity_types' => array('quiz_question_entity'),
        ));
      }

      field_create_instance(array(
          'field_name'  => 'quiz_question_body',
          'entity_type' => 'quiz_question_entity',
          'bundle'      => $bundle,
          'label'       => t('Question'),
          'widget'      => array(
              'type'     => 'text_textarea_with_summary',
              'weight'   => -20,
              'settings' => array('rows' => 5, 'summary_rows' => 3),
          ),
          'settings'    => array('display_summary' => FALSE),
          'display'     => array(
              'teaser' => array('label' => 'hidden', 'type' => 'text_summary_or_trimmed', 'settings' => array('trim_length' => 600)),
              'full'   => array('label' => 'hidden', 'type' => 'text_default'),
          ),
      ));
    }
  }

  /**
   * Allow question types to override the body field title
   *
   * @return string
   *  The title for the body field
   */
  public function getBodyFieldTitle() {
    return t($this->body_field_title);
  }

  /**
   * Returns a node form to quiz_question_entity_form
   *
   * Adds default form elements, and fetches question type specific elements from their
   * implementation of getCreationForm
   *
   * @param array $form_state
   * @return unknown_type
   */
  public function getEntityForm(array &$form_state = NULL, QuizEntity $quiz = NULL) {
    $obj = new QuestionForm($this->question);
    return $obj->getForm($form_state, $quiz);
  }

  /**
   * {@inheritdoc}
   */
  public function view() {
    $output['question_type'] = array(
        '#weight' => -2,
        '#prefix' => '<div class="question_type_name">',
        '#suffix' => '</div>',
    );
    return array('#markup' => $this->question->getQuestionType()->label) + $output;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    if (isset($this->properties)) {
      return $this->properties;
    }
    return $this->properties = array();
  }

  /**
   * Responsible for handling insert/update of question-specific data.
   * This is typically called from within the Node API, so there is no need
   * to save the node.
   *
   * The $is_new flag is set to TRUE whenever the node is being initially
   * created.
   *
   * A save function is required to handle the following three situations:
   * - A new node is created ($is_new is TRUE)
   * - A new node *revision* is created ($is_new is NOT set, because the
   *   node itself is not new).
   * - An existing node revision is modified.
   *
   * @see hook_update and hook_insert in quiz_question.module
   *
   * @param $is_new
   *  TRUE when the node is initially created.
   */
  public function save($is_new = FALSE) {
    // We call the abstract function 'onSave' to save type specific data
    $this->onSave($is_new);

    // Save what quizzes this question belongs to.
    $this->saveRelationships();
    if ($this->question->revision && !QuestionController::$disable_invoking) {
      $auto_revisioning = $this->question->getQuestionType()->getConfig('auto_revisioning', 1);
      if (user_access('manual quiz revisioning') && !$auto_revisioning) {
        unset($_GET['destination']);
        unset($_REQUEST['edit']['destination']);
        drupal_goto("quiz-question/{$this->question->qid}/revision-actions");
      }
    }
  }

  /**
   * Get the form through which the user will answer the question.
   *
   * @param array $form_state
   * @param int $result_id
   * @return array
   */
  public function getAnsweringForm(array $form_state = NULL, $result_id) {
    return array('#element_validate' => array('quizz_question_element_validate'));
  }

  /**
   * Element validator (for repeat until correct).
   */
  public function elementValidate(Result $result, &$element, &$form_state) {
    if ((!$quiz = $result->getQuiz()) || !$quiz->repeat_until_correct) {
      return;
    }

    $input = $form_state['values']['question'][$this->question->qid]['answer'];
    $handler = $this->question->getResponseHandler($result->result_id);
    $handler->setAnswerInput($input);
    if (!$handler->isCorrect()) {
      $this->onRepeatUntiCorrect($result, $element);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onRepeatUntiCorrect(Result $result, array &$element, $msg = NULL) {
    form_set_error('', NULL !== $msg ? $msg : t('The answer was incorrect. Please try again.'));
    $obj = new QuestionFeedbackController($result);
    $feedback_form = $obj->buildFormArray($this->question);
    $element['feedback'] = array('#weight' => 100, '#markup' => drupal_render($feedback_form));
  }

  /**
   * Save this Question to the specified Quiz.
   *
   * @param int $quiz_qid
   * @param int $quiz_vid
   * @return bool
   *  TRUE if relationship is made.
   */
  public function saveRelationships($quiz_qid = NULL, $quiz_vid = NULL) {
    if (!$quiz_qid || !$quiz_vid || !$quiz = quizz_load($quiz_qid, $quiz_vid)) {
      return FALSE;
    }

    // We need to revise the quiz if it has been answered.
    if ($quiz->isAnswered()) {
      $quiz->is_new_revision = 1;
      $quiz->clone_relationships = 1;
      $quiz->save();
      drupal_set_message(t('New revision has been created for the @quiz %n', array('%n' => $quiz->title, '@quiz' => QUIZZ_NAME)));
    }

    $values = array();
    $values['quiz_qid'] = $quiz->qid;
    $values['quiz_vid'] = $quiz->vid;
    $values['question_qid'] = $this->question->qid;
    $values['question_vid'] = $this->question->vid;
    $values['max_score'] = $this->getMaximumScore();
    $values['auto_update_max_score'] = $this->autoUpdateMaxScore() ? 1 : 0;
    $values['weight'] = 1 + db_query('SELECT MAX(weight) FROM {quiz_relationship} WHERE quiz_vid = :vid', array(':vid' => $quiz->vid))->fetchField();
    $values['question_status'] = $quiz->randomization == 2 ? QUIZZ_QUESTION_RANDOM : QUIZZ_QUESTION_ALWAYS;
    entity_create('quiz_relationship', $values)->save();

    // Update max_score for relationships if auto update max score is enabled
    // for question
    $update_quiz_ids = array();
    $sql = 'SELECT quiz_vid as vid FROM {quiz_relationship} WHERE question_qid = :qid AND question_vid = :vid AND auto_update_max_score = 1';
    $result = db_query($sql, array(
        ':qid' => $this->question->qid,
        ':vid' => $this->question->vid));
    foreach ($result as $record) {
      $update_quiz_ids[] = $record->vid;
    }

    db_update('quiz_relationship')
      ->fields(array('max_score' => $this->getMaximumScore()))
      ->condition('question_qid', $this->question->qid)
      ->condition('question_vid', $this->question->vid)
      ->condition('auto_update_max_score', 1)
      ->execute();

    if (!empty($update_quiz_ids)) {
      quizz_entity_controller()->getMaxScoreWriter()->update($update_quiz_ids);
    }

    quizz_entity_controller()->getMaxScoreWriter()->update(array($quiz->vid));

    return TRUE;
  }

  /**
   * Finds out if a question has been answered or not
   *
   * This function also returns TRUE if a quiz that this question belongs to
   * have been answered. Even if the question itself haven't been answered.
   * This is because the question might have been rendered and a user is about
   * to answer it…
   *
   * @return string
   *   true if question has been answered or is about to be answered…
   */
  public function hasBeenAnswered() {
    if (!isset($this->question->vid)) {
      return FALSE;
    }

    $answered = db_query_range('SELECT 1 '
      . ' FROM {quiz_results} qnres '
      . ' JOIN {quiz_relationship} qrel ON (qnres.quiz_vid = qrel.quiz_vid) '
      . ' WHERE qrel.question_vid = :question_vid', 0, 1, array(':question_vid' => $this->question->vid))->fetch();

    return $answered ? TRUE : FALSE;
  }

  /**
   * Determines if the user can view the correct answers
   *
   * @todo grabbing the node context here probably isn't a great idea
   *
   * @return boolean
   *   true if the view may include the correct answers to the question
   */
  public function viewCanRevealCorrect() {
    global $user;

    $reveal_correct[] = user_access('view any quiz question correct response');
    $reveal_correct[] = ($user->uid == $this->question->uid);
    if (array_filter($reveal_correct)) {
      return TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete($single_revision) {
    $id = $single_revision ? $this->question->vid : $this->question->qid;

    // @TODO: We should delete answer entities instead of answer's properties.
    if ($this->base_answer_table) {
      $sql = 'DELETE p';
      $sql .= ' FROM {quiz_truefalse_answer} p';
      $sql .= ' INNER JOIN {quiz_answer_entity} a ON p.question_vid = a.question_vid';
      $sql .= ' WHERE a.' . ($single_revision ? 'question_vid' : 'question_qid') . ' = :id';
      db_query($sql, array(':id' => $id));
    }

    if ($this->base_table) {
      $sql = 'DELETE q';
      $sql .= ' FROM {' . $this->base_table . '} q';
      $sql .= ' WHERE q.' . ($single_revision ? 'vid' : 'qid') . ' = :id';
      db_query($sql, array(':id' => $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array &$form) {

  }

  /**
   * {@inheritdoc}
   */
  public function getCreationForm(array &$form_state = NULL) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function onSave($is_new = FALSE) {

  }

  /**
   * If it returns true, it means the max_score is updated for all occurrences of
   * this question in quizzes.
   * @return bool
   */
  protected function autoUpdateMaxScore() {
    return FALSE;
  }

  public function validateAnsweringForm(array &$form, array &$form_state = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function isGraded() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function hasFeedback() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  function getReportForm(Result $result, Question $question) {
    $question->findLegacyMaxScore($result);
    return $question
        ->getResponseHandler($result->result_id, isset($question->answers[0]) ? $question->answers[0] : NULL)
        ->getReportForm();
  }

}
