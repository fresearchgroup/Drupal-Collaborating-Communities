<?php

namespace Drupal\quizz\Controller\Admin;

use Drupal\quizz\Entity\QuizEntity;

class QuizRevisionsAdminController {

  public function render(QuizEntity $quiz) {
    $revisions = db_query('SELECT qr.vid, qr.log, qr.changed, qr.revision_uid, u.uid, u.name'
      . ' FROM {quiz_entity_revision} qr'
      . '   LEFT JOIN {users} u ON qr.revision_uid = u.uid'
      . ' WHERE qr.qid = :qid'
      . ' ORDER BY qr.vid DESC', array(':qid' => $quiz->qid))->fetchAll();

    $rows = array();
    foreach ($revisions as $revision) {
      $row = array(
          t('!datetime by !name', array(
              '!datetime' => format_date($revision->changed),
              '!name'     => l($revision->name, "user/{$revision->uid}"),
          )),
          l(t('revert'), "quiz/{$quiz->qid}/revisions/{$revision->vid}/revert"),
          l(t('delete'), "quiz/{$quiz->qid}/revisions/{$revision->vid}/delete"),
      );

      $row[0] .= '<div class="log">' . (!empty($revision->log) ? $revision->log : '<em>' . t('empty') . '</em>') . '</div>';

      if ($quiz->vid == $revision->vid) {
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
