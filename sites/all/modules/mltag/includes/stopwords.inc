<?php
/**
 * @file
 * Code for Removing stopwords from content.
 */

class MltagStopwords {
  protected $stopwords;
  /**
   * Function constructor.
   */
  public function __construct() {
    $this->stopwords = self::prepareStopwords();
  }
  /**
   * Prepares a stopwords list using the stopwords_en.inc file.
   */
  public function prepareStopwords() {
    module_load_include('inc', 'mltag', 'stopwords/stopwords_en');
    $stopwords_from_file = mltag_stopwords_en();
    $stopwords_user = variable_get('stopwords_textfield', NULL);
    if (trim($stopwords_user) != NULL || trim($stopwords_user) != '') {
      $stopwords_user = explode(', ', variable_get('stopwords_textfield'));
      $this->stopwords = array_merge($stopwords_from_file, $stopwords_user);
    }
    else {
      $this->stopwords = $stopwords_from_file;
    }
    array_unique($this->stopwords);
    return $this->stopwords;
  }
  /**
   * Remove Stopwords.
   */
  public function removeStopwords($tokens) {
    $stopwords = $this->stopwords;
    $new_tokens = array_diff($tokens, $stopwords);
    // No need for the below code as changes added to the Tokenizer itself.
    // special character array.
    return $new_tokens;
  }
  /**
   * Remove Stopwords.
   */
  public function sentenceRemoveStopwords($sentences) {
    module_load_include('inc', 'porterstemmer', 'includes/standard-stemmer');//this was added
    $stopwords = $this->stopwords;
    foreach ($sentences as $key => $value) {
      $array_temp = array();
      $array_temp = explode(' ', $value);
      $array_temp = array_diff($array_temp, $stopwords);
      $array_temp = array_values($array_temp);
      $output = '';
      foreach ($array_temp as $value1) {
        $word = porterstemmer_stem($value1);
        $output .= $word . ' ';
      }
      $sentences["$key"] = trim($output);
    }
    return $sentences;
  }
}
