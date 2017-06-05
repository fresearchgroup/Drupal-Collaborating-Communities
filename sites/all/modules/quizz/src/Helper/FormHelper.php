<?php

namespace Drupal\quizz\Helper;

/**
 * Date and time routines for use with quiz module.
 * - Based on event module
 * - All references to event variables should be optional
 */
class FormHelper {

  /**
   * Formats a GMT timestamp to local date values using time zone offset supplied.
   * All timestamp values in event nodes are GMT and translated for display here.
   *
   * Pulled from event
   *
   * Time zone settings are applied in the following order
   * 1. If supplied, time zone offset is applied
   * 2. If user time zones are enabled, user time zone offset is applied
   * 3. If neither 1 nor 2 apply, the site time zone offset is applied
   *
   * @param $format
   *   The date() format to apply to the timestamp.
   * @param $timestamp
   *   The GMT timestamp value.
   * @param $offset
   *   Time zone offset to apply to the timestamp.
   * @return gmdate() formatted date value
   */
  private function date($format, $timestamp, $offset = NULL) {
    global $user;

    if (isset($offset)) {
      $timestamp += $offset;
    }
    elseif (variable_get('configurable_timezones', 1) && $user->uid && strlen($user->timezone)) {
      $timestamp += $user->timezone;
    }
    else {
      $timestamp += variable_get('date_default_timezone', 0);
    }

    // make sure we apply the site first day of the week setting for dow requests
    $result = gmdate($format, $timestamp);
    return $result;
  }

  protected function getUserpointsType() {
    $userpoints_terms = taxonomy_get_tree(userpoints_get_vid());
    $userpoints_tids = array(0 => t('Select'));
    foreach ($userpoints_terms as $userpoints_term) {
      $userpoints_tids[$userpoints_term->tid] = str_repeat('-', $userpoints_term->depth) . $userpoints_term->name;
    }
    return $userpoints_tids;
  }

}
