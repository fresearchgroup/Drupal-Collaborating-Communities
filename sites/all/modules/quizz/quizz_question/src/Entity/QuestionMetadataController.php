<?php

namespace Drupal\quizz_question\Entity;

use EntityDefaultMetadataController;

class QuestionMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['uid']['type'] = 'user';
    $properties['created']['type'] = 'date';
    $properties['created']['setter callback'] = 'entity_property_verbatim_set';
    $properties['created']['setter permission'] = 'administer quiz questions';
    $properties['changed']['type'] = 'date';
    $properties['changed']['setter callback'] = 'entity_property_verbatim_set';
    $properties['changed']['setter permission'] = 'administer quiz questions';

    return $info;
  }

}
