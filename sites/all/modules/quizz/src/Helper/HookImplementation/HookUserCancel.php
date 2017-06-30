<?php

namespace Drupal\quizz\Helper\HookImplementation;

use Drupal\quizz\Entity\QuizType;

class HookUserCancel {

  private $account;
  private $method;

  public function __construct($account, $method) {
    $this->account = $account;
    $this->method = $method;
  }

  public function execute() {
    foreach (quizz_get_types() as $quiz_type) {
      if ($quiz_type->getConfig('quiz_durod', 0)) {
        $this->deleteResultsByQuizType($quiz_type);
      }
    }
  }

  private function deleteResultsByQuizType(QuizType $quiz_type) {
    $sql = 'SELECT result_id FROM {quiz_results} WHERE uid = :uid AND type = :type';
    $con = array(':uid' => $this->account->uid, ':type' => $quiz_type->type);
    if ($result_ids = db_query($sql, $con)->fetchCol()) {
      entity_delete_multiple('quiz_result', $result_ids);
    }
  }

}
