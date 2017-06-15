<?php
/**
 * @file
 * Functions to build up the suggested terms list
 */


/**
* Creates all spans from vocabulary terms
*
* @return
*   A string containing the clickable spans for the suggestedterms module.
*/
function _suggestedterms_build_suggestions($vid, $sort_order, $display_what, $selected_terms) {
  // If we have a cached query, use that.
  $terms_markup = &drupal_static(__FUNCTION__);
  if (!isset($terms_markup)) {
    if ($cache = cache_get('suggestedterms_data')) {
      $terms_markup = $cache->data;
    }
    else {
      // Use left join to display all entries, inner join to diaplay only used ones.
      $query_type = ($display_what == SUGGESTEDTERMS_DISPLAY_ALL) ? " LEFT " : " INNER ";

      // Get max number of terms set by admin.
      $total_terms = array(
        0 => variable_get('suggestedterms_maximum_displayed', 5),
      );
      $query = db_select('taxonomy_term_data', 't');
      $query->fields('t', array('name'));
      $query->condition('t.vid', $vid, '=');
      if ($total_terms[0]) {
        $query->range(0, $total_terms[0]);
      }
      // Get sort order set by admin.
      switch ($sort_order) {
        case SUGGESTEDTERMS_SORT_POPULAR:
          if ($query_type == ' INNER ') {
            $query->innerJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          elseif ($query_type = ' LEFT ') {
            $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          else {
            debug('Join type should be LEFT or INNER, but something else got passed instead.');
          }
          $query->addExpression('COUNT(t.name)', 'total');
          $query->groupBy('t.name');
          $query->orderBy('total', 'DESC');
          break;

        case SUGGESTEDTERMS_SORT_RECENT:
          // List all the terms in the specified vocabulary and sort by term ID
          // (We assume larger numbers were added more recently).
          $query->groupBy('t.name');
          $query->orderBy('t.tid', 'DESC');
          if ($query_type == ' INNER ') {
            $query->innerJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          elseif ($query_type = ' LEFT ') {
            $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          else {
            debug('Join type should be LEFT or INNER, but something else got passed instead.');
          }
          break;

        case SUGGESTEDTERMS_SORT_WEIGHT:
          // List all the terms in the specified vocabulary and sort by weight.
          if ($query_type == ' INNER ') {
            $query->innerJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          elseif ($query_type = ' LEFT ') {
            $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          else {
            debug('Join type should be LEFT or INNER, but something else got passed instead.');
          }
          $query->groupBy('name');
          $query->orderBy('weight', 'ASC');
          $query->orderBy('name');
          break;

        case SUGGESTEDTERMS_SORT_USED:
          // List all the terms in the specified vocabulary that are used on
          // nodes. Order by the date the node was last changed.
          if ($query_type == ' INNER ') {
            $query->innerJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
            $query->innerJoin('node', 'n', 'ti.nid = n.nid');
          }
          elseif ($query_type = ' LEFT ') {
            $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
            $query->leftJoin('node', 'n', 'ti.nid = n.nid');
          }
          else {
            debug('Join type should be LEFT or INNER, but something else got passed instead.');
          }
          $query->orderBy('n.changed', 'DESC');
          $query->orderBy('name', 'ASC');
          $query->distinct();
          break;

        default:
          // List all the term names in the specified vocabulary and order
          // alphabetically.
          $query->groupBy('t.name');
          $query->orderBy('t.name');
          if ($query_type == ' INNER ') {
            $query->innerJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          elseif ($query_type = ' LEFT ') {
            $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
          }
          else {
            debug('Join type should be LEFT or INNER, but something else got passed instead.');
          }
          break;
      }

      // Run the query
      $result = $query->execute();
      $terms = array();
      $selected = FALSE;
      foreach ($result as $record) {
      $query_result[] = $record->name;
        if (is_array($selected_terms) && in_array($record->name, $selected_terms)) {
          $selected = TRUE;
        }
        $variables = array(
          'name' => check_plain($record->name),
          'selected' => $selected,
        );
        $theme_output = theme('suggestedterm', $variables);
        $terms[] = $theme_output;
        $selected = FALSE;
      }
      // Turn that big array of terms into a string for output and return it.
      $terms_markup = '<div class="suggestedterms">' . implode(', ', $terms) . '</div>';

      // If a lifetime is set for this cache, expire it at the end.
      $suggestedterms_cache_expiration = variable_get('suggestedterms_cache_expiration');
      $expires = $suggestedterms_cache_expiration ? time() + $suggestedterms_cache_expiration : CACHE_TEMPORARY;
        cache_set('suggestedterms_data', $terms_markup, 'cache', $expires);
    }
  }
  return $terms_markup;
}