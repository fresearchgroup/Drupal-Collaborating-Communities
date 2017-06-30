<?php

namespace Drupal\quizz\Generator;

use Drupal\quizz\Entity\QuizEntity;

class Generator {

  /** @var string[] */
  private $quiz_types;

  /** @var string[] */
  private $question_types;

  /** @var int Maximum number of quizzes per type. */
  private $quiz_limit;

  /** @var int Maximum number of questions per quiz. */
  private $question_limit;

  /** @var int Maximum number of results per quiz. */
  private $result_limit;

  /** @var QuizGenerator */
  private $quiz_generator;

  /** @var QuestionGenerator */
  private $question_generator;

  /** @var ResultGenerator */
  private $result_generator;

  public function __construct($quiz_types, $question_types, $quiz_limit, $question_limit, $result_limit) {
    $this->quiz_generator = new QuizGenerator();
    $this->question_generator = new QuestionGenerator();
    $this->result_generator = new ResultGenerator();

    $this->quiz_types = $quiz_types;
    $this->question_types = $question_types;
    $this->quiz_limit = $quiz_limit;
    $this->question_limit = $question_limit;
    $this->result_limit = $result_limit;
  }

  public function generate() {
    module_load_include('inc', 'devel_generate', 'devel_generate.fields');
    module_load_include('inc', 'devel_generate', 'devel_generate');

    foreach ($this->quiz_types as $quiz_type) {
      $limit = rand(1, $this->quiz_limit);
      for ($i = 0; $i < $limit; ++$i) {
        $this->generateQuiz($quiz_type, $limit);
      }
    }
  }

  private function generateQuiz($quiz_type) {
    $quiz = $this->quiz_generator->generate($quiz_type);
    drupal_set_message('Geneated quiz: ' . $quiz->link());

    $this->generateQuizQuestions($quiz);
    $this->generateQuizResults($quiz);
  }

  private function generateQuizQuestions(QuizEntity $quiz) {
    $question_limit = rand(1, $this->question_limit);
    for ($i = 0; $i < $question_limit; ++$i) {
      $question_type = array_rand($this->question_types);
      $this
        ->question_generator
        ->generate($question_type)
        ->getHandler()
        ->saveRelationships($quiz->qid, $quiz->vid)
      ;
    }
  }

  private function generateQuizResults(QuizEntity $quiz) {
    $result_limit = rand(1, $this->result_limit);
    for ($i = 0; $i < $result_limit; ++$i) {
      $this->result_generator->generate($quiz);
    }
  }

}
