<?php

namespace Drupal\quizz\Entity;

use EntityDefaultMetadataController;

class ResultMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['quiz_qid']['type'] = 'quiz_entity';
    $properties['quiz_vid']['type'] = 'integer';
    $properties['uid']['type'] = 'user';
    $properties['time_start']['label'] = 'Date started';
    $properties['time_start']['type'] = 'date';
    $properties['time_end']['label'] = 'Date finished';
    $properties['time_end']['type'] = 'date';
    $properties['released']['type'] = 'date';
    $properties['score']['label'] = 'Score';
    $properties['score']['type'] = 'integer';
    $properties['is_invalid']['label'] = 'Invalid';
    $properties['is_invalid']['type'] = 'boolean';
    $properties['is_evaluated']['label'] = 'Evaluated';
    $properties['is_evaluated']['type'] = 'boolean';
    $properties['time_left']['type'] = 'duration';

    return $info;
  }

}
