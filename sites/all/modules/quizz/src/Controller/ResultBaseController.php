<?php

namespace Drupal\quizz\Controller;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Entity\Result;

abstract class ResultBaseController {

  /** @var QuizEntity */
  protected $quiz;

  /** @var QuizEntity */
  protected $quiz_revision;

  /** @var Result */
  protected $result;

  /** @var int */
  protected $quiz_id;

  /**
   * The score information as returned by quiz_calculate_score().
   */
  protected $score;

  /** @var \stdClass */
  protected $author;

  public function __construct(QuizEntity $quiz, QuizEntity $quiz_revision, $result) {
    $this->quiz = $quiz;
    $this->quiz_revision = $quiz_revision;
    $this->result = $result;
    $this->quiz_id = $this->result->quiz_qid;
    $this->score = quizz_result_controller()->getScoreIO()->calculate($this->result);
    $this->author = user_load($this->result->uid);
  }

  /**
   * Get answer data for a specific result.
   *
   * @param QuizEntity $this->quiz_revision
   * @param int $this->result->result_id
   * @return
   *   Array of answers.
   */
  protected function getAnswers() {
    $sql = "SELECT ra.question_qid, ra.question_vid, question.type, rs.max_score, qt.max_score as term_max_score"
      . " FROM {quiz_answer_entity} ra "
      . "   LEFT JOIN {quiz_question_entity} question ON ra.question_qid = question.qid"
      . "   LEFT JOIN {quiz_results} r ON ra.result_id = r.result_id"
      . "   LEFT OUTER JOIN {quiz_relationship} rs ON (ra.question_vid = rs.question_vid) AND rs.quiz_vid = r.quiz_vid"
      . "   LEFT OUTER JOIN {quiz_entity_terms} qt ON (qt.vid = :vid AND qt.tid = ra.tid) "
      . " WHERE ra.result_id = :rid "
      . " ORDER BY ra.number, ra.answer_timestamp";
    $ids = db_query($sql, array(':vid' => $this->quiz_revision->vid, ':rid' => $this->result->result_id));
    while ($row = $ids->fetch()) {
      if ($answer = $this->getAnswer($row)) {
        $answers[] = $answer;
      }
    }
    return !empty($answers) ? $answers : array();
  }

  private function getAnswer($row) {
    // Questions picked from term id's won't be found in the quiz_relationship table
    if ($row->max_score === NULL) {
      if ($this->quiz_revision->randomization == QUIZZ_QUESTION_NEVER && isset($this->quiz_revision->tid) && $this->quiz_revision->tid > 0) {
        $row->max_score = $this->quiz_revision->max_score_for_random;
      }
      elseif (QUIZZ_QUESTION_CATEGORIZED_RANDOM == $this->quiz_revision->randomization) {
        $row->max_score = $row->term_max_score;
      }
    }

    if (!$module = quizz_question_type_load($row->type)->getHandlerModule()) {
      return;
    }

    // Invoke hook_get_report().
    if (!$report = module_invoke($module, 'get_report', $row->question_qid, $row->question_vid, $this->result->result_id)) {
      return;
    }

    // Add max score info to the question.
    if (!isset($report->score_weight)) {
      $report->qnr_max_score = $row->max_score;
      $report->score_weight = !$report->max_score ? 0 : ($row->max_score / $report->max_score);
    }

    return $report;
  }

  /**
   * Get the summary message for a completed quiz.
   *
   * Summary is determined by whether we are using the pass / fail options, how
   * the user did, and where the method is called from.
   *
   * @todo Need better feedback for when a user is viewing their quiz results
   *   from the results list (and possibily when revisiting a quiz they can't take
   *   again).
   *
   * @return
   *   Filtered summary text or null if we are not displaying any summary.
   */
  public function getSummaryText() {
    $summary = array();
    $admin = arg(0) === 'admin';
    $quiz_format = (isset($this->quiz_revision->quiz_question_body[LANGUAGE_NONE][0]['format'])) ? $this->quiz_revision->quiz_question_body[LANGUAGE_NONE][0]['format'] : NULL;

    if (!$admin) {
      if (!empty($this->score['result_option'])) {
        // Unscored quiz, return the proper result option.
        $summary['result'] = check_markup($this->score['result_option'], $quiz_format);
      }
      else {
        $result_option = $this->pickResultOption($this->quiz_revision, $this->score['percentage_score']);
        $summary['result'] = is_object($result_option) ? check_markup($result_option->option_summary, $result_option->option_summary_format) : '';
      }
    }

    // If we are using pass/fail, and they passed.
    if ($this->quiz_revision->pass_rate > 0 && $this->score['percentage_score'] >= $this->quiz_revision->pass_rate) {
      // If we are coming from the admin view page.
      if ($admin) {
        $summary['passfail'] = t('The user passed this @quiz.', array('@quiz' => QUIZZ_NAME));
      }
      elseif (!$this->quiz->getQuizType()->getConfig('quiz_use_passfail', 1)) {
        // If there is only a single summary text, use this.
        if (trim($this->quiz_revision->summary_default) != '') {
          $summary['passfail'] = check_markup($this->quiz_revision->summary_default, $quiz_format);
        }
      }
      elseif (trim($this->quiz_revision->summary_pass) != '') {
        // If there is a pass summary text, use this.
        $summary['passfail'] = check_markup($this->quiz_revision->summary_pass, $this->quiz_revision->summary_pass_format);
      }
    }
    // If the user did not pass or we are not using pass/fail.
    else {
      // If we are coming from the admin view page, only show a summary if we are
      // using pass/fail.
      if ($admin) {
        if ($this->quiz_revision->pass_rate > 0) {
          $summary['passfail'] = t('The user failed this @quiz.', array('@quiz' => QUIZZ_NAME));
        }
        else {
          $summary['passfail'] = t('the user completed this @quiz.', array('@quiz' => QUIZZ_NAME));
        }
      }
      elseif (trim($this->quiz_revision->summary_default) != '') {
        $summary['passfail'] = check_markup($this->quiz_revision->summary_default, $this->quiz_revision->summary_default_format);
      }
    }
    return $summary;
  }

  /**
   * Get summary text for a particular score from a set of result options.
   *
   * @param QuizEntity $quiz
   * @param int $score
   *   The user's final score.
   *
   * @return string
   *   Summary text for the user's score.
   */
  private function pickResultOption(QuizEntity $quiz, $score) {
    foreach ($quiz->result_options as $option) {
      if ($score < $option['option_start'] || $score > $option['option_end']) {
        continue;
      }
      return (object) array('option_summary' => $option['option_summary'], 'option_summary_format' => $option['option_summary_format']);
    }
  }

}
