<?php

namespace Drupal\quizz\Entity;

use EntityDefaultMetadataController;

class RelationshipMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['quiz_qid']['type'] = 'quiz_entity';
    $properties['quiz_vid']['type'] = 'integer';
    $properties['qr_pid']['type'] = 'quiz_question_entity';
    $properties['question_qid']['type'] = 'quiz_question_entity';
    $properties['question_vid']['type'] = 'integer';

    return $info;
  }

}
