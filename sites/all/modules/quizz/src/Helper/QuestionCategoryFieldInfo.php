<?php

namespace Drupal\quizz\Helper;

class QuestionCategoryFieldInfo {

  private $name;

  public function __construct($field_name = 'quizz_question_category') {
    $this->name = $field_name;
  }

  public function getField() {
    return field_info_field($this->name);
  }

  public function getTableName() {
    return _field_sql_storage_tablename($this->getField());
  }

  public function getColumnName() {
    return _field_sql_storage_columnname($this->name, 'tid');
  }

}
