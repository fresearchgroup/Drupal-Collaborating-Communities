<?php

namespace Drupal\quizz_truefalse;

use Drupal\quizz\Entity\Answer;
use Drupal\quizz_question\Entity\Question;
use Drupal\quizz_question\ResponseHandler;

class TrueFalseResponse extends ResponseHandler {

  /**
   * {@inheritdoc}
   * @var string
   */
  protected $base_table = 'quiz_truefalse_answer';

  public function __construct($result_id, Question $question, $input = NULL) {
    parent::__construct($result_id, $question, $input);

    if (NULL === $input) {
      if (($answer = $this->loadAnswerEntity()) && ($input = $answer->getInput())) {
        $this->answer = $input->answer;
        $this->score = $input->score;
      }
    }
    else {
      $this->answer = $input;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    db_insert('quiz_truefalse_answer')
      ->fields(array(
          'answer_id' => $this->loadAnswerEntity()->id,
          'answer'    => (int) $this->answer,
          'score'     => (int) $this->getScore(),
      ))
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function score() {
    return $this->getResponse() == $this->question->getHandler()->getCorrectAnswer() ? 1 : 0;
  }

  public function onLoad(Answer $answer) {
    $input = db_select('quiz_truefalse_answer', 'input')
      ->fields('input', array('answer', 'score'))
      ->condition('answer_id', $answer->id)
      ->execute()
      ->fetchObject();

    if ($input) {
      $answer->setInput($input);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFeedbackValues() {
    $input = $this->answer;
    $correct_answer = !empty($this->question->correct_answer);

    return array(
        array(
            'choice'          => t('True'),
            'attempt'         => $input ? quizz_icon('selected') : '',
            'correct'         => $input == 1 ? quizz_icon($correct_answer ? 'correct' : 'incorrect') : '',
            'score'           => intval($correct_answer == 1 && $input == 1),
            'answer_feedback' => '',
            'solution'        => $correct_answer == 1 ? quizz_icon('should') : '',
            'quiz_feedback'   => t('@quiz feedback', array('@quiz' => QUIZZ_NAME)),
        ),
        array(
            'choice'          => t('False'),
            'attempt'         => !$input ? quizz_icon('selected') : '',
            'correct'         => $input == 0 ? (quizz_icon(!$correct_answer ? 'correct' : 'incorrect')) : '',
            'score'           => intval($correct_answer == 0 && $input == 0),
            'answer_feedback' => '',
            'solution'        => $correct_answer == 0 ? quizz_icon('should') : '',
        )
    );
  }

}
