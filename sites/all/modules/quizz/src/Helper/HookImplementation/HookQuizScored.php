<?php

namespace Drupal\quizz\Helper\HookImplementation;

class HookQuizScored {

  private $quiz;
  private $score;
  private $result_id;
  private $taker;

  public function __construct($quiz, $score, $result_id) {
    $this->quiz = $quiz;
    $this->score = $score;
    $this->result_id = $result_id;
    $this->taker = db_query('SELECT u.uid, u.mail'
      . ' FROM {users} u '
      . '   JOIN {quiz_results} qnr ON u.uid = qnr.uid '
      . ' WHERE result_id = :rid', array(':rid' => $this->result_id))->fetch();
  }

  /**
   * @TODO Rules
   */
  public function execute() {
    if (variable_get('quiz_email_results', 0) && $this->taker->uid != 0 && $this->score['is_evaluated']) {
      drupal_mail('quizz', 'notice', $this->taker->mail, NULL, array($this->quiz, $this->score, $this->result_id, 'taker'));
      drupal_set_message(t("The results has been sent to the user's email address."));
    }
    $this->executeUserPoints();
  }

  /**
   * Calls userpoints functions to credit user point based on number of correct
   * answers.
   */
  private function executeUserPoints() {
    if (!$this->quiz->has_userpoints || !$this->taker->uid || !$this->score['is_evaluated']) {
      return;
    }

    //Looking up the tid of the selected Userpoint vocabulary
    $selected_tid = db_query("SELECT ti.tid
      FROM {taxonomy_index} ti
      JOIN {taxonomy_term_data} td
        ON td.tid = ti.tid AND td.vid = :vid
      WHERE ti.qid = :qid", array(':qid' => $this->quiz->qid, ':vid' => userpoints_get_vid()))->fetchField();

    $variables = array(
        '@title' => $this->quiz->title,
        '@quiz'  => QUIZZ_NAME,
        '@time'  => date('l jS \of F Y h:i:s A'),
    );

    $params = array(
        'points'      => $this->score['numeric_score'],
        'description' => t('Attended @title @quiz on @time', $variables),
        'tid'         => $selected_tid,
    );

    if ($this->quiz->userpoints_tid != 0) {
      $params['tid'] = $this->quiz->userpoints_tid;
    }

    userpoints_userpointsapi($params);
  }

}
