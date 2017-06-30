<?php

namespace Drupal\quizz_question\Controller;

use Drupal\quizz_question\Entity\Question;

class QuestionRevisionsController {

  public function render(Question $question) {
    $revisions = db_query('SELECT qr.vid, qr.log, qr.changed, qr.revision_uid, u.uid, u.name'
      . ' FROM {quiz_question_revision} qr'
      . '   LEFT JOIN {users} u ON qr.revision_uid = u.uid'
      . ' WHERE qr.qid = :qid'
      . ' ORDER BY qr.vid DESC', array(':qid' => $question->qid))->fetchAll();

    $rows = array();
    foreach ($revisions as $revision) {
      $row = array(
          t('!datetime by !name', array(
              '!datetime' => format_date($revision->changed),
              '!name'     => l($revision->name, "user/{$revision->uid}"),
          )),
          l(t('revert'), "quiz-question/{$question->qid}/revisions/{$revision->vid}/revert"),
          l(t('delete'), "quiz-question/{$question->qid}/revisions/{$revision->vid}/delete"),
      );

      $row[0] .= '<div class="log">' . (!empty($revision->log) ? $revision->log : '<em>' . t('empty') . '</em>') . '</div>';

      if ($question->vid == $revision->vid) {
        unset($row[2]);
        $row[1] = array('data' => t('current revision'), 'colspan' => 2);
      }

      $rows[] = $row;
    }

    return array(
        '#theme'  => 'table',
        '#header' => array(t('Revision'), array('data' => t('Operations'), 'colspan' => 2)),
        '#rows'   => $rows);
  }

}
