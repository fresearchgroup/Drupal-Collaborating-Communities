<?php

/**
 * @file
 * Contains code for Tokenising a given content.
 * It takes $content as input string and has a
 * Word Tokeniser - returns an array of words
 * and also a sentence Tokeniser- Returns an array of sentences.
 */


class MltagTokenizer {
  /**
   * Delimiter value.
   */
  protected $delim;
  /**
   * Generated tokens for the string.
   */
  protected $tokens;
  /**
   * Split a text separated by space into array of words.
   *
   * @param string $string
   *   Content separated by spaces that will be splitted into array of words
   *
   * @return array
   *   Array of words
   */
  public function __construct($string, $delim = ' ') {
    $this->delim = $delim;
    $string = self::stringClean($string);
    $this->tokens = self::stringTokenizer($string, $delim);
  }
  /**
   * Return List of generated tokens.
   * @return array
   *   List of generated tokens
   */
  public function getTokens() {
    return $this->tokens;
  }
  /**
   * Clean a given string of html tags, extra spaces etc.
   *
   * @param string $string
   *   String to clean
   *
   * @return string
   *   Cleaned string
   */
  public function stringClean($string) {
    // Convert everthing to lowercase.
    $string = drupal_strtolower($string);
    // Remove html tags.
    $string = strip_tags($string);
    // For end of line (or begining).
    $string = trim($string);
    // Remove multiple tabs or spaces from everywhere.
    $string = preg_replace('/\s\s+/', ' ', $string);
    $string = preg_replace('/[\.]+/', '\.', $string);
    $string = preg_replace('/[\!]+/', '\!', $string);
    // Drupal_set_message($string);
    return $string;
  }
  /**
   * Tokenize a given string.
   *
   * @param string $string
   *   String to tokenize
   * @param char $delim
   *   Delimiter character
   *
   * @return array
   *   List of generated tokens
   */
  public static function stringTokenizer($string, $delim = ' ') {
    $words = explode($delim, $string);
    $tokens = array();
    foreach ($words as $word) {
      $word = strtok($word, '\'');
      // If word ends with . and its Not an abbrevation M.S.
      $endswith = self::endsWith($word);
      if ($endswith == '.' && strpos($word, '.') != drupal_strlen($word) - 1) {
        array_push($tokens, $word);
      }
      // If word ends with .
      elseif ($endswith == '.' || $endswith == ',' || $endswith == ';' || $endswith == '?' || $endswith == ':') {
        $word = trim(preg_replace('/[^a-z0-9]/', '', $word));
        if (!empty($word)) {
          array_push($tokens, $word);
        }
        // array_push($tokens, '.');
      }
      // If word ends with comma ,
      /*
      else if ($endswith == ',') {
      array_push($tokens, substr($word, 0, -1));
      //array_push($tokens, ',');
      }
      else if ($endswith == ';') {
      array_push($tokens, substr($word, 0, -1));
      //array_push($tokens, ';');
      }
      else if ($endswith == '?') {
      array_push($tokens, substr($word, 0, -1));
      //array_push($tokens, ';');
      }
      else if ($endswith == ':') {
      array_push($tokens, substr($word, 0, -1));
      //array_push($tokens, ':');
      }*/
      else {
        $word = trim(preg_replace('/[^a-z0-9]/', '', $word));
        if ($word != '' ||$word != NULL) {
          array_push($tokens, $word);
        }
      }
    }
    return $tokens;
  }
  /**
   * Return last parts of string.
   */
  public static function endsWith($haystack, $len = 1) {
    return drupal_substr($haystack, drupal_strlen($haystack) - $len, $len);
  }
}
/**
 * For finding out sentences in a string
 * First I break them into words to analyze abbreviations like U.S.A
 * and then I combine them into a string only to be exploded later
 * by common end points of a sentence like - dot, ?, !, ; etc.
 */
class MltagTokenizeSentence {

  /**
   * Delimiter value
   */
  protected $delim;
  /**
   * Generated tokens for the string
   */
  protected $tokens;
  /**
   * Generated sentences from the text
   */
  protected $sentences;
  /**
   * Split a text separated by space into array of words.
   *
   * @param string $string
   *   Content separated by spaces that will be splitted into array of words
   *
   * @return array
   *   Array of words
   */
  public function __construct($string, $delim = ' ') {
    $this->delim = $delim;
    $string = self::contentClean($string);
    $this->sentences = self::sentenceTokenizer($string, $delim);
  }
  /**
   * List of generated tokens.
   * @return array
   *   List of generated tokens
   */
  public function getSentences() {
    return $this->sentences;
  }
  /**
   * Clean a given string of html tags, extra spaces etc.
   *
   * @param string $string
   *   String to clean
   *
   * @return string
   *   Cleaned string
   */
  public function contentClean($string) {
    // Convert everthing to lowercase.
    $string = drupal_strtolower($string);
    // Remove html tags.
    $string = strip_tags($string);
    // For end of line (or begining).
    $string = trim($string);

    // Remove multiple tabs or spaces from everywhere.
    $string = preg_replace('/\s\s+/', ' ', $string);
    return $string;
  }
  /**
   * Tokenize a given string.
   *
   * And return the sentences after combining the tokens together and then
   * exploding them to capture the individual sentences together.
   *
   * @param string $string
   *   String to tokenize
   * @param char $delim
   *   Delimiter character
   *
   * @return array
   *   List of generated sentences
   */
  public static function sentenceTokenizer($string, $delim = ' ') {
    $words = explode($delim, $string);
    $tokens = array();
    $sentences = array();
    foreach ($words as $word) {
      $word = strtok($word, '\'');
      // If word ends with . and its Not an abbrevation M.S.
      $endswith = self::endsWith($word);
      if ($endswith == '.' && strpos($word, '.') != drupal_strlen($word) - 1) {
        array_push($tokens, $word);
      }
      // If word ends with .
      elseif ($endswith == '.') {
        $word = trim(preg_replace('/[^a-z0-9]/', '', $word));
        if (!empty($word)) {
          array_push($tokens, $word);
          array_push($tokens, '.');
        }
      }
      elseif ($endswith == ';') {
        array_push($tokens, drupal_substr($word, 0, -1));
        array_push($tokens, ';');
      }
      elseif ($endswith == '?') {
        array_push($tokens, drupal_substr($word, 0, -1));
        array_push($tokens, ';');
      }
      elseif ($endswith == '!') {
        array_push($tokens, drupal_substr($word, 0, -1));
        array_push($tokens, '!');
      }
      else {
        $word = trim(preg_replace('/[^a-z0-9]/', '', $word));
        if (!empty($word)) {
          array_push($tokens, $word);
        }
      }
    }
    $content = implode(' ', $tokens);
    $sentences = preg_split('/[\.\;\?\:\!]+/', $content, NULL, PREG_SPLIT_NO_EMPTY);
    return $sentences;
  }
  /**
   * Return last parts of string.
   */
  public static function endsWith($haystack, $len = 1) {
    return drupal_substr($haystack, drupal_strlen($haystack) - $len, $len);
  }
}
