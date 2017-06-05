<?php

namespace Drupal\quizz\Entity\QuizEntity;

use Drupal\quizz\Helper\FormHelper;
use Drupal\quizz\Entity\QuizEntity;

/**
 * Read and write default properties for quiz entity.
 *
 * We have a small trick here: status = -1
 */
class DefaultPropertiesIO extends FormHelper {

  /**
   * Returns the users default settings.
   *
   * @param bool $remove_ids
   * @param string $type
   * @return array
   */
  public function get($remove_ids = TRUE, $type = NULL) {
    if ($quiz = $this->getUserDefaults($remove_ids, $type)) {
      return $quiz;
    }
    return $this->getSystemDefaults($remove_ids, $type);
  }

  public function getUserDefaults($remove_ids = TRUE, $type = NULL) {
    global $user;

    // We found user defaults.
    $conds = array('status' => -1, 'uid' => $user->uid, 'qid' => 0, 'vid' => 0, 'type' => $type);
    if ($quizzes = entity_load('quiz_entity', FALSE, $conds)) {
      $quiz = reset($quizzes);
      if ($remove_ids) {
        $quiz->qid = $quiz->uid = $quiz->vid = $quiz->quiz_open = $quiz->quiz_close = NULL;
      }
      return $quiz;
    }
  }

  public function getSystemDefaults($remove_ids = TRUE, $type = 'quiz') {
    // Found global defaults.
    $conds = array('status' => -1, 'uid' => 0, 'type' => $type);
    if ($quizzes = entity_load('quiz_entity', FALSE, $conds)) {
      if (($quiz = reset($quizzes)) && $remove_ids) {
        $quiz->qid = $quiz->uid = $quiz->vid = $quiz->quiz_open = $quiz->quiz_close = NULL;
      }
      return $quiz;
    }
    return entity_create('quiz_entity', $this->getQuizDefaultPropertyValues($type));
  }

  /**
   * Returns default values for all quiz settings.
   *
   * @return mixed[]
   *   Array of default values.
   */
  public function getQuizDefaultPropertyValues($type = NULL) {
    $defaults = array();
    if ($type && $question_type = quizz_type_load($type)) {
      $defaults = $question_type->getConfigurations();
    }

    return $defaults + array(
        'status'                     => -1,
        'aid'                        => NULL,
        'allow_jumping'              => 0,
        'allow_resume'               => 1,
        'allow_skipping'             => 1,
        'always_available'           => TRUE,
        'backwards_navigation'       => 1,
        'build_on_last'              => '',
        'has_userpoints'             => 0,
        'keep_results'               => 2,
        'mark_doubtful'              => 0,
        'max_score'                  => 0,
        'max_score_for_random'       => 1,
        'number_of_random_questions' => 0,
        'pass_rate'                  => 75,
        'quiz_always'                => 1,
        'quiz_close'                 => 0,
        'quiz_open'                  => 0,
        'randomization'              => 0,
        'repeat_until_correct'       => 0,
        'review_options'             => array(
            'question' => array(),
            'end'      => array(
                'choice'                    => 'choice',
                'quiz_question_view_teaser' => 'quiz_question_view_teaser',
            )
        ),
        'show_attempt_stats'         => 1,
        'show_passed'                => 1,
        'summary_default'            => '',
        'summary_default_format'     => filter_fallback_format(),
        'summary_pass'               => '',
        'summary_pass_format'        => filter_fallback_format(),
        'takes'                      => 0,
        'tid'                        => 0,
        'time_limit'                 => 0,
        'userpoints_tid'             => 0,
    );
  }

  /**
   * Update quiz's default settings for context user.
   * @global \stdClass $user
   * @param QuizEntity $_quiz
   */
  public function updateUserDefaultSettings(QuizEntity $_quiz) {
    global $user;

    $quiz = clone $_quiz;

    $quiz->aid = !empty($quiz->aid) ? $quiz->aid : 0;
    $quiz->summary_pass = is_array($quiz->summary_pass) ? $quiz->summary_pass['value'] : $quiz->summary_pass;
    $quiz->summary_pass_format = is_array($quiz->summary_pass) ? $quiz->summary_pass['format'] : isset($quiz->summary_pass_format) ? $quiz->summary_pass_format : filter_fallback_format();
    $quiz->summary_default = is_array($quiz->summary_default) ? $quiz->summary_default['value'] : $quiz->summary_default;
    $quiz->summary_default_format = is_array($quiz->summary_default) ? $quiz->summary_default['format'] : isset($quiz->summary_default_format) ? $quiz->summary_default_format : filter_fallback_format();
    $quiz->tid = isset($quiz->tid) ? $quiz->tid : 0;

    if (!empty($quiz->remember_settings)) {
      // Save user defaults.
      $u_quiz = clone $quiz;
      $u_quiz->uid = $user->uid;

      // Find ID of old entry.
      $conditions = array('status' => -1, 'uid' => $user->uid, 'qid' => 0, 'vid' => 0);
      if ($quizzes = entity_load('quiz_entity', FALSE, $conditions)) {
        $_user_quiz = reset($quizzes);
        $u_quiz->qid = $_user_quiz->qid;
        $u_quiz->vid = $_user_quiz->vid;
      }
      else {
        $u_quiz->qid = $u_quiz->vid = NULL;
      }
      $this->saveQuizSettings($u_quiz);
    }

    if (!empty($quiz->remember_global)) {
      $s_quiz = clone $quiz;
      $s_quiz->uid = 0;

      // Find ID of old entry
      $conditions = array('status' => -1, 'uid' => 0);
      if ($quizzes = entity_load('quiz_entity', FALSE, $conditions, TRUE)) {
        $_system_quiz = reset($quizzes);
        $s_quiz->qid = $_system_quiz->qid;
        $s_quiz->vid = $_system_quiz->vid;
      }
      else {
        $s_quiz->qid = $s_quiz->vid = NULL;
      }
      return $this->saveQuizSettings($s_quiz);
    }
  }

  /**
   * Insert or update the quiz entity properties accordingly.
   */
  private function saveQuizSettings(QuizEntity $quiz) {
    $quiz->title = '';
    $quiz->status = -1;
    return $quiz->save();
  }

}
