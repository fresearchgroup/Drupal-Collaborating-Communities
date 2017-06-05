<?php

namespace Drupal\quizz\Entity;

use EntityDefaultUIController;

class QuizTypeUIController extends EntityDefaultUIController {

  /**
   * Overrides hook_menu() defaults.
   */
  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['description'] = strtr('Manage !quiz types, including fields.', array('!quiz' => QUIZZ_NAME));
    return $items;
  }

}
