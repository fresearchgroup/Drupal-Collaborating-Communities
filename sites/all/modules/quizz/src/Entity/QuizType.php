<?php

namespace Drupal\quizz\Entity;

use Entity;

class QuizType extends Entity {

  public $type;
  public $label;
  public $description;
  public $help;
  public $weight = 0;

  /** @var mixed[] Extra info for quiz type. */
  public $data;

  public function __construct(array $values = array()) {
    parent::__construct($values, 'quiz_type');
  }

  /**
   * Returns whether the quiz type is locked, thus may not be deleted or renamed.
   *
   * Quiz types provided in code are automatically treated as locked, as well
   * as any fixed quiz type.
   */
  public function isLocked() {
    return isset($this->status) && empty($this->is_new) && (($this->status & ENTITY_IN_CODE) || ($this->status & ENTITY_FIXED));
  }

  public function getConfigurations() {
    return isset($this->data['configuration']) ? $this->data['configuration'] : array();
  }

  public function getConfig($name, $default = NULL) {
    if (isset($this->data['configuration'][$name])) {
      return $this->data['configuration'][$name];
    }
    return $default;
  }

  public function setConfig($name, $value) {
    $this->data['configuration'][$name] = $value;
    return $this;
  }

}
