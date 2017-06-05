<?php

namespace Drupal\quizz\Schema;

class Schema7060 {

  public function get() {
    $schema = array();
    $schema += $this->getQuizTypeSchema();
    $schema += $this->getQuizSchema();
    $schema += $this->getResultSchema();
    $schema += $this->getRelationshipSchema();
    return $schema;
  }

  private function getQuizTypeSchema() {
    $schema['quiz_type'] = array(
        'description' => 'Stores information about all defined quiz types.',
        'fields'      => array(
            'id'          => array('type' => 'serial', 'not null' => TRUE, 'description' => 'Primary Key: Unique quiz type ID.'),
            'type'        => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'description' => 'The machine-readable name of this quiz type.'),
            'label'       => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE, 'default' => '', 'description' => 'The human-readable name of this quiz type.'),
            'weight'      => array('type' => 'int', 'not null' => TRUE, 'default' => 0, 'size' => 'tiny', 'description' => 'The weight of this quiz type in relation to others.'),
            'data'        => array('type' => 'text', 'not null' => FALSE, 'size' => 'big', 'serialize' => TRUE, 'description' => 'A serialized array of additional data related to this quiz type.'),
            'status'      => array('type' => 'int', 'not null' => TRUE, 'default' => 0x01, 'size' => 'tiny', 'description' => 'The exportable status of the entity.'),
            'module'      => array('type' => 'varchar', 'length' => 255, 'not null' => FALSE, 'description' => 'The name of the providing module if the entity has been defined in code.'),
            'description' => array('type' => 'text', 'not null' => FALSE, 'size' => 'medium', 'translatable' => TRUE, 'description' => 'A brief description of this quiz type.'),
            'help'        => array('type' => 'text', 'not null' => FALSE, 'size' => 'medium', 'translatable' => TRUE, 'description' => 'Help information shown to the user when creating a quiz entity of this type.'),
        ),
        'primary key' => array('id'),
        'unique keys' => array('type' => array('type')),
    );

    return $schema;
  }

  private function getQuizSchema() {
    $schema['quiz_entity'] = array(
        'description' => 'Store quiz items',
        'fields'      => array(
            'qid'      => array('type' => 'serial', 'not null' => TRUE, 'description' => 'Primary Key: Unique quiz item ID.'),
            'vid'      => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'type'     => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'default' => '', 'description' => 'The {quiz_type}.type of this quiz.'),
            'language' => array('type' => 'varchar', 'description' => 'The {languages}.language of this quiz.', 'length' => 12, 'not null' => TRUE, 'default' => ''),
            'status'   => array('type' => 'int', 'not null' => TRUE, 'default' => 1, 'description' => 'Boolean indicating whether the quiz is published (visible to non-administrators).'),
            'title'    => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE, 'default' => '', 'description' => 'The title of this quiz, always treated as non-markup plain text.'),
            'created'  => array('type' => 'int', 'not null' => FALSE, 'description' => 'The Unix timestamp when the quiz was created.'),
            'changed'  => array('type' => 'int', 'not null' => FALSE, 'description' => 'The Unix timestamp when the quiz was most recently saved.'),
            'uid'      => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0, 'description' => 'Author ID of the quiz.'),
        ),
        'primary key' => array('qid'),
        'unique keys' => array('vid' => array('vid')),
        'indexes'     => array(
            'quiz_created'     => array('created'),
            'quiz_changed'     => array('changed'),
            'quiz_status_type' => array('status', 'type', 'qid'),
            'language'         => array('language'),
        ),
    );

    $schema['quiz_entity_revision'] = array(
        'description' => 'Entity revision table for quiz content with fields.',
        'fields'      => array(
            'qid'                        => array('type' => 'int', 'not null' => TRUE, 'description' => 'The id this revision belongs to'),
            'vid'                        => array('type' => 'serial', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The primary identifier for this version.'),
            'revision_uid'               => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0, 'description' => 'Author of quiz revision.'),
            'log'                        => array('type' => 'text', 'size' => 'big', 'description' => t('A log message associated with the revision.')),
            'changed'                    => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The Unix timestamp when the quiz was most recently saved.'),
            'aid'                        => array('type' => 'varchar', 'length' => 255, 'not null' => FALSE),
            'number_of_random_questions' => array('type' => 'int', 'size' => 'small', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'max_score_for_random'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 1),
            'pass_rate'                  => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE),
            'summary_pass'               => array('type' => 'text'),
            'summary_pass_format'        => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE,),
            'summary_default'            => array('type' => 'text',),
            'summary_default_format'     => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE,),
            'randomization'              => array('type' => 'int', 'size' => 'small', 'not null' => TRUE, 'default' => 0),
            'backwards_navigation'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 1),
            'keep_results'               => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 2), // QUIZ_KEEP_ALL = 2
            'repeat_until_correct'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'quiz_open'                  => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'quiz_close'                 => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'takes'                      => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'show_attempt_stats'         => array('type' => 'int', 'size' => 'tiny', 'unsigned' => FALSE, 'not null' => TRUE, 'default' => 1),
            'time_limit'                 => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'quiz_always'                => array('type' => 'int', 'size' => 'tiny', 'not null' => TRUE, 'default' => 0),
            'tid'                        => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0,),
            'has_userpoints'             => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'userpoints_tid'             => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'time_left'                  => array('type' => 'int', 'size' => 'small', 'not null' => TRUE, 'default' => 0),
            'max_score'                  => array('type' => 'int', 'not null' => TRUE, 'default' => 0),
            'allow_skipping'             => array('type' => 'int', 'size' => 'small', 'not null' => TRUE, 'default' => 0),
            'allow_resume'               => array('type' => 'int', 'size' => 'small', 'not null' => TRUE, 'default' => 1),
            'allow_jumping'              => array('type' => 'int', 'size' => 'tiny', 'unsigned' => FALSE, 'not null' => TRUE, 'default' => 0),
            'allow_change'               => array('type' => 'int', 'size' => 'tiny', 'not null' => TRUE, 'default' => 1),
            'show_passed'                => array('type' => 'int', 'size' => 'tiny', 'unsigned' => FALSE, 'not null' => TRUE, 'default' => 1),
            'mark_doubtful'              => array('type' => 'int', 'size' => 'tiny', 'not null' => TRUE, 'default' => 0),
            'review_options'             => array('type' => 'text', 'serialize' => TRUE),
            'build_on_last'              => array('type' => 'varchar', 'length' => '255', 'not null' => TRUE, 'default' => ''),
        ),
        'primary key' => array('vid'),
        'indexes'     => array('fpid' => array('qid', 'vid')),
    );

    return $schema;
  }

  private function getResultSchema() {
    // Quiz specific options concerning availability and access to scores.
    // Create the quiz entity results table
    $schema['quiz_results'] = array(
        'description' => 'Table storing the total results for a quiz',
        'fields'      => array(
            'result_id'    => array('type' => 'serial', 'size' => 'normal', 'unsigned' => TRUE, 'not null' => TRUE),
            'type'         => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'default' => '', 'description' => 'The {quiz_type}.type of this result.'),
            'quiz_qid'     => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'ID of quiz entity'),
            'quiz_vid'     => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Version ID of quiz entity'),
            'uid'          => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Author ID'),
            'time_start'   => array('type' => 'int', 'unsigned' => FALSE),
            'time_end'     => array('type' => 'int', 'unsigned' => FALSE),
            'released'     => array('type' => 'int', 'unsigned' => TRUE, 'default' => 0),
            'score'        => array('type' => 'int', 'not null' => TRUE, 'default' => 0),
            'is_invalid'   => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'is_evaluated' => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0, 'description' => 'Indicates whether or not a quiz result is evaluated.'),
            'time_left'    => array('type' => 'int', 'size' => 'small', 'not null' => TRUE, 'default' => 0),
        ),
        'primary key' => array('result_id'),
        'indexes'     => array(
            'bundle'       => array('type'),
            'user_results' => array('uid', 'quiz_vid', 'quiz_qid'),
            'vid'          => array('quiz_vid'),
        ),
    );

    // Information about a particular question in a result
    $schema['quiz_answer_entity'] = array(
        'description' => 'Table storing information about the results for the questions',
        'fields'      => array(
            'id'               => array('type' => 'serial', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The result answer ID',),
            'result_id'        => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'question_qid'     => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'question_vid'     => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'tid'              => array('type' => 'int', 'unsigned' => TRUE),
            'is_correct'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0,),
            'is_skipped'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'points_awarded'   => array('type' => 'int', 'size' => 'tiny', 'unsigned' => FALSE, 'not null' => TRUE, 'default' => 0,),
            'answer_timestamp' => array('type' => 'int', 'unsigned' => TRUE, 'not null' => FALSE, 'default' => NULL),
            'number'           => array('type' => 'int', 'size' => 'small', 'unsigned' => FALSE, 'not null' => TRUE, 'default' => 1,),
            'is_doubtful'      => array('type' => 'int', 'not null' => TRUE, 'default' => 0, 'size' => 'tiny'),
        ),
        'primary key' => array('id'),
        'unique keys' => array(
            'result_answer' => array('result_id', 'question_qid', 'question_vid'),
        ),
        'indexes'     => array('result_id' => array('result_id')),
    );

    // Allows custom feedback based on the results of a user completing a quiz.
    $schema['quiz_result_options'] = array(
        'description' => 'Table storing result options for quizzes. Several result options may belong to a single quiz.',
        'fields'      => array(
            'option_id'             => array('type' => 'serial', 'size' => 'normal', 'unsigned' => TRUE, 'not null' => TRUE),
            'quiz_qid'              => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'ID of quiz entity'),
            'quiz_vid'              => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Version ID of quiz entity'),
            'option_name'           => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE),
            'option_summary'        => array('type' => 'text'),
            'option_summary_format' => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE),
            'option_start'          => array('type' => 'int', 'unsigned' => TRUE, 'default' => 0),
            'option_end'            => array('type' => 'int', 'unsigned' => TRUE, 'default' => 0),
        ),
        'primary key' => array('option_id'),
        'indexes'     => array('quiz_id' => array('quiz_vid', 'quiz_qid'))
    );

    return $schema;
  }

  private function getRelationshipSchema() {
    $schema['quiz_relationship'] = array(
        'description' => 'Table storing what questions belong to what quizzes aware of revision.',
        'fields'      => array(
            'qr_id'                 => array('type' => 'serial', 'size' => 'normal', 'unsigned' => TRUE, 'not null' => TRUE),
            'quiz_qid'              => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'quiz_vid'              => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'qr_pid'                => array('type' => 'int', 'unsigned' => TRUE, 'not null' => FALSE, 'default' => NULL, 'description' => 'ID of parent page (question entity)'),
            'question_qid'          => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'question_vid'          => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE),
            'question_status'       => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 1),
            'weight'                => array('type' => 'int', 'not null' => TRUE, 'default' => 0),
            'max_score'             => array('type' => 'int', 'not null' => TRUE, 'default' => 0),
            'auto_update_max_score' => array('type' => 'int', 'size' => 'tiny', 'not null' => TRUE, 'default' => 0),
        ),
        'primary key' => array('qr_id'),
        'unique keys' => array(
            'parent_child' => array('quiz_qid', 'quiz_vid', 'question_qid', 'question_vid'),
        ),
        'indexes'     => array(
            'parent_id' => array('quiz_vid'),
            'child_id'  => array('question_vid'),
        ),
    );

    $schema['quiz_entity_terms'] = array(
        'description' => 'Table storing what terms belongs to what quiz for categorized random quizzes',
        'fields'      => array(
            'qid'       => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Question ID'),
            'vid'       => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Version ID'),
            'weight'    => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The terms weight decides the order of the terms'),
            'tid'       => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Term ID'),
            'max_score' => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Max score for each question marked with this term'),
            'number'    => array('type' => 'int', 'size' => 'tiny', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'Number of questions to be drawn from this term'),
        ),
        'primary key' => array('vid', 'tid'),
        'indexes'     => array('version' => array('vid')),
    );

    return $schema;
  }

}
