<?php

namespace Drupal\quizz_question\Entity;

use EntityDefaultUIController;

class QuestionTypeUIController extends EntityDefaultUIController {

  /**
   * Overrides hook_menu() defaults.
   */
  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['description'] = strtr('Manage !quiz question types, including fields.', array('!quiz' => QUIZZ_NAME));
    return $items;
  }

  protected function overviewTableHeaders($conditions, $rows, $additional_header = array()) {
    $additional_header[] = t('Handler');
    return parent::overviewTableHeaders($conditions, $rows, $additional_header);
  }

  protected function overviewTableRow($conditions, $id, $entity, $additional_cols = array()) {
    $additional_cols[] = $entity->handler;
    $row = parent::overviewTableRow($conditions, $id, $entity, $additional_cols);
    return $row;
  }

}
