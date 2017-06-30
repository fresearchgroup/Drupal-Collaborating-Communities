<?php

namespace Drupal\quizz_question\Schema;

class Schema7060 {

  public function get() {
    $schema = array();
    $schema += $this->getQuestionSchema();
    $schema += $this->getQuestionRevisionSchema();
    $schema += $this->getQuestionTypeSchema();
    return $schema;
  }

  private function getQuestionSchema() {
    $schema['quiz_question_entity'] = array(
        'description' => 'Store quiz questions',
        'fields'      => array(
            'qid'      => array('type' => 'serial', 'not null' => TRUE, 'description' => 'Primary Key: Unique question item ID.'),
            'vid'      => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0),
            'type'     => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'default' => '', 'description' => 'The {quiz_question_type}.type of this quiz.'),
            'language' => array('type' => 'varchar', 'description' => 'The {languages}.language of this question.', 'length' => 12, 'not null' => TRUE, 'default' => ''),
            'status'   => array('type' => 'int', 'not null' => TRUE, 'default' => 1, 'description' => 'Boolean indicating whether the quiz is published (visible to non-administrators).'),
            'title'    => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE, 'default' => '', 'description' => 'The title of this question, always treated as non-markup plain text.'),
            'created'  => array('type' => 'int', 'not null' => FALSE, 'description' => 'The Unix timestamp when the question was created.'),
            'changed'  => array('type' => 'int', 'not null' => FALSE, 'description' => 'The Unix timestamp when the question was most recently saved.'),
            'uid'      => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0, 'description' => 'Author ID of question.'),
        ),
        'primary key' => array('qid'),
        'indexes'     => array(
            'i_ids'      => array('qid', 'vid'),
            'i_vid'      => array('vid'),
            'i_bundle'   => array('type'),
            'i_language' => array('language'),
            'i_created'  => array('created'),
            'i_changed'  => array('changed'),
            'i_author'   => array('uid'),
            'i_status'   => array('status'),
        ),
    );
    return $schema;
  }

  private function getQuestionRevisionSchema() {
    $schema['quiz_question_revision'] = array(
        'description' => 'Entity revision table for question content with fields.',
        'fields'      => array(
            'qid'             => array('type' => 'int', 'not null' => TRUE, 'description' => 'The id this revision belongs to'),
            'vid'             => array('type' => 'serial', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The primary identifier for this version.'),
            'revision_uid'    => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0, 'description' => 'Author of question revision.'),
            'log'             => array('type' => 'text', 'size' => 'big', 'description' => t('A log message associated with the revision.')),
            'title'           => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE, 'default' => '', 'description' => 'The title of this question revision, always treated as non-markup plain text.'),
            'changed'         => array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'description' => 'The Unix timestamp when the question was most recently saved.'),
            'max_score'       => array('type' => 'int', 'unsigned' => TRUE, 'default' => 0),
            'feedback'        => array('type' => 'text'),
            'feedback_format' => array('type' => 'varchar', 'length' => 255, 'not null' => FALSE),
        ),
        'primary key' => array('vid'),
        'indexes'     => array(
            'i_qid'     => array('qid'),
            'i_author'  => array('revision_uid'),
            'i_changed' => array('changed'),
        ),
    );

    return $schema;
  }

  private function getQuestionTypeSchema() {
    $schema['quiz_question_type'] = array(
        'description' => 'Stores information about all defined question types.',
        'fields'      => array(
            'id'          => array('type' => 'serial', 'not null' => TRUE, 'description' => 'Primary Key: Unique question type ID.'),
            'type'        => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'description' => 'The machine-readable name of this question type.'),
            'handler'     => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE, 'description' => 'Question handler type (shortanswer, longanswer, truefalse, …)'),
            'label'       => array('type' => 'varchar', 'length' => 255, 'not null' => TRUE, 'default' => '', 'description' => 'The human-readable name of this question type.'),
            'weight'      => array('type' => 'int', 'not null' => TRUE, 'default' => 0, 'size' => 'tiny', 'description' => 'The weight of this question type in relation to others.'),
            'data'        => array('type' => 'text', 'not null' => FALSE, 'size' => 'big', 'serialize' => TRUE, 'description' => 'A serialized array of additional data related to this question type.'),
            'status'      => array('type' => 'int', 'not null' => TRUE, 'default' => 0x01, 'size' => 'tiny', 'description' => 'The exportable status of the entity.'),
            'disabled'    => array('type' => 'int', 'not null' => TRUE, 'default' => 0x00, 'size' => 'tiny', 'description' => 'Status of module. Set to 0 if admin would like disable dis question type.'),
            'module'      => array('type' => 'varchar', 'length' => 255, 'not null' => FALSE, 'description' => 'The name of the providing module if the entity has been defined in code.'),
            'description' => array('type' => 'text', 'not null' => FALSE, 'size' => 'medium', 'translatable' => TRUE, 'description' => 'A brief description of this question type.'),
            'help'        => array('type' => 'text', 'not null' => FALSE, 'size' => 'medium', 'translatable' => TRUE, 'description' => 'Help information shown to the user when creating a question entity of this type.'),
        ),
        'primary key' => array('id'),
        'unique keys' => array('type' => array('type')),
        'indexes'     => array(
            'i_priority' => array('weight'),
            'i_type'     => array('type'),
            'i_handler'  => array('handler'),
            'i_status'   => array('disabled')
        ),
    );
    return $schema;
  }

}
