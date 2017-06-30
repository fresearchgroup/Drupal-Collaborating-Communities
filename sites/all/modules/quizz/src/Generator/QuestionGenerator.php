<?php

namespace Drupal\quizz\Generator;

use Drupal\quizz_question\Entity\Question;
use RuntimeException;

class QuestionGenerator {

  /**
   * @param string $question_type
   * @return Question
   * @throws RuntimeException
   */
  public function generate($question_type) {
    $question_array = array(
        'type'      => $question_type,
        'comment'   => 2,
        'changed'   => REQUEST_TIME,
        'moderate'  => 0,
        'promote'   => 0,
        'revision'  => 1,
        'log'       => '',
        'status'    => 1,
        'sticky'    => 0,
        'revisions' => NULL,
        'language'  => LANGUAGE_NONE,
        'title'     => devel_create_greeking(rand(5, 20), TRUE),
        'body'      => array(LANGUAGE_NONE => array(array('value' => devel_create_para(rand(20, 50), 1)))),
    );

    switch (quizz_question_type_load($question_type)->handler) {
      case 'truefalse':
        $question_array += $this->dummyTrueFalseQuestion();
        break;
      case 'short_answer':
        $question_array +=$this->dummyShortAnswerQuestion();
        break;
      case 'long_answer':
        $question_array += $this->dummyLongAnswerQuestion();
        break;
      case 'multichoice':
        $question_array += $this->dummyMultichoiceQuestion();
        break;
      case 'quiz_directions':
      case 'quiz_page':
        break;
      default:
        throw new RuntimeException('Unsupported question: ' . quizz_question_type_load($question_type)->handler);
    }

    /* @var $question Question */
    $question = entity_create('quiz_question_entity', $question_array);
    $question->save();
    devel_generate_fields($question, 'quiz_question_entity', $question->type);

    return $question;
  }

  private function dummyTrueFalseQuestion() {
    return array('correct_answer' => rand(0, 1));
  }

  private function dummyShortAnswerQuestion() {
    return array(
        'correct_answer_evaluation' => rand(ShortAnswerQuestion::ANSWER_MATCH, ShortAnswerQuestion::ANSWER_MANUAL),
        'correct_answer'            => devel_create_greeking(rand(10, 20)),
    );
  }

  private function dummyLongAnswerQuestion() {
    return array(
        'rubric' => devel_create_greeking(rand(10, 20))
    );
  }

  private function dummyMultichoiceQuestion() {
    $array = array(
        'choice_multi'   => array_rand(array(0, 1)),
        'choice_random'  => array_rand(array(0, 1)),
        'choice_boolean' => array_rand(array(0, 1)),
    );

    $rand = $array['choice_multi'] ? 1 : rand(2, 10);
    for ($i = 0; $i < $rand; ++$i) {
      $array['alternatives'][] = array(
          'answer'                 => array(
              'value'  => devel_create_greeking(rand(2, 10)),
              'format' => 'filtered_html',
          ),
          'feedback_if_chosen'     => array(
              'value'  => devel_create_greeking(rand(5, 10)),
              'format' => 'filtered_html',
          ),
          'feedback_if_not_chosen' => array(
              'value'  => devel_create_greeking(rand(5, 10)),
              'format' => 'filtered_html',
          ),
          'score_if_chosen'        => 1,
          'score_if_not_chosen'    => 0,
      );
    }

    return $array;
  }

}
