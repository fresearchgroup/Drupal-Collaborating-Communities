<?php

namespace Drupal\quizz\Helper\Quiz;

class TakeJumperHelper {

  private $quiz;
  private $total;
  private $siblings;
  private $current;

  public function __construct($quiz, $total, $siblings, $current) {
    $this->quiz = $quiz;
    $this->total = $total;
    $this->siblings = $siblings;
    $this->current = $current;
  }

  public function render() {
    return theme('item_list', array(
      'items'      => $this->buildItems(),
      'attributes' => array('class' => array('pager'))
    ));
  }

  private function buildItems() {
    $items = array();
    $items[] = array(
      'class' => array('pager-first'),
      'data'  => l(t('first'), "quiz/" . $this->quiz->qid . "/take/1"),
    );

    foreach ($this->buildElements() as $i) {
      if ($i == $this->current) {
        $items[] = array(
          'class' => array('pager-current'),
          'data'  => $i,
        );
      }
      else {
        $items[] = array(
          'class' => array('pager-item'),
          'data'  => l($i, "quiz/" . $this->quiz->qid . "/take/{$i}"),
        );
      }
    }

    $items[] = array(
      'class' => array('pager-last'),
      'data'  => l(t('last'), "quiz/" . $this->quiz->qid . "/take/{$this->total}"),
    );

    return $items;
  }

  /**
   * Help us with special pagination.
   *
   * Why not the Drupal theme_pager()?
   *
   * It uses query strings. We have access on each menu argument (quiz question
   * number) so we unfortunately cannot use it.
   */
  private function buildElements($perpage = 1) {
    $result = array();

    if (isset($this->total, $perpage) === true) {
      $result = range(1, ceil($this->total / $perpage));

      if (isset($this->current, $this->siblings) === true) {
        if (($this->siblings = floor($this->siblings / 2) * 2 + 1) >= 1) {
          $result = array_slice($result, max(0, min(count($result) - $this->siblings, intval($this->current) - ceil($this->siblings / 2))), $this->siblings);
        }
      }
    }

    return $result;
  }

}
