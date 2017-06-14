<?php

class tagging_d6 extends tagging_base {

  public function hook_menu() {
    $items = array();
    $items['admin/settings/tagging/settings'] = array(
        'type' => MENU_NORMAL_ITEM,
        'title' => 'Tagging configuration',
        'description' => 'Configuration options for the tagging module',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('tagging_admin_settings'),
        'access arguments' => array('administer site configuration'),
        'file' => 'tagging.admin.inc'
    );
    return $items;
  }

  public function hook_theme() {
    $theme = array(
        'tagging_widget' => array('arguments' => array('element' => NULL)),
        'tagging_widget_input' => array('arguments' => array('element' => NULL)),
        'tagging_widget_button' => array('arguments' => array('vid' => NULL)),
        'tagging_widget_wrapper' => array('arguments' => array('content' => NULL, 'vid' => NULL)),
        'tagging_suggestions_list' => array('arguments' => array('suggestions' => array(), 'vid' => NULL)),
        'tagging_tags_list' => array('arguments' => array('tags' => array(), 'vid' => NULL)),
    );
    return $theme;
  }

  public function hook_tagging_form_taxonomy_form_vocabulary_alter(&$form, $form_state) {
    $form['settings']['tagging_inject'] = array(
        '#type' => 'checkbox',
        '#title' => t('Tagging Widget'),
        '#weight' => 10,
        '#default_value' => variable_get('tagging_inject_' . $form['vid']['#value'], 0),
        '#description' => t('Use the visual tagging-widget, when this taxonomy is shown to the user'),
    );
    $form['#submit'][] = 'tagging_form_vocabulary_submit';
  }

  public function hook_tagging_form_alter(&$form, $form_state, $form_id) {
// Check if we are editting a node and we actually have a tag taxonomy.
    if ($form['#id'] == 'node-form' && isset($form['taxonomy']['tags'])) {
      foreach ($form['taxonomy']['tags'] as $id => $values) {
        if (variable_get('tagging_inject_' . $id, 0) == 1) {
          $form['taxonomy']['tags'][$id]['#type'] = 'tagging_widget';
          $form['taxonomy']['tags'][$id]['#vid'] = $id;
          $form['taxonomy']['tags'][$id]['#nid'] = $form['nid']['#value'];
        }
      }
    }

// Check if user_terms is present
    if (isset($form['user_terms'])) {
// Iterate all the available vocabularies
      foreach ($form['user_terms'] as $key => $value) {
// Only act on form items containing vocabularies
        if (strstr($key, 'user_terms_')) {
// Extract the vid
          $id = str_replace('user_terms_', '', $key);
// Make sure it's numeric
          if (is_numeric($id)) {
// Check if the tagging widget is enabled for this
            if (variable_get('tagging_inject_' . $id, 0)) {
              $form['user_terms'][$key]['#type'] = 'tagging_widget';
              $form['user_terms'][$key]['#vid'] = $id;
              $form['user_terms'][$key]['#uid'] = $form['#uid'];
            }
          }
        }
      }
    }
  }

  public function theme_tagging_widget($element = null) {
    $vid = $element['#vid'];
    $cur_tags = '';

    $id = "tagging-widget-input-$vid";
    $fieldset['#title'] = $element['#title'];
    $fieldset['#collapsed'] = false;
// Well this is the original. We actually just need it in the background, so that we
// dont need to fight in the validation filter. We can let the normal taxonomy.modul
// validators and submitters do their job.
    $element['#type'] = 'hidden';
    $element['#attributes']['class'] = "tagging-widget-target-$vid";
    $old_input = $this->theme('hidden', array('element' => $element));

// Ok now lets render the input textfield we will use for the tagging
    $element['#type'] = 'textfield';
// Its convention that the tagging-widget-VID class it addid. That vid will be used
// to get the unique "context" id for all the other wrappers and functions.
    $element['#attributes']['class'] = "tagging-widget-input $id";
    $element['#id'] = $id;
    $element['#title'] = "";

    if ($element['#description'] == '') {
      $element['#description'] = t("Type and press enter to add the tag");
    }
    $element['#field_suffix'] = theme('tagging_widget_button', $vid) . $old_input;
    $output .= $this->theme('tagging_tags_list', array('tags' => split(',', $element['#value']), 'vid' => $vid))
            . '<div class="tagging-widget-input-wrapper clearfix">'
            . $this->theme('textfield', array('element' => $element))
            . '</div>'
            . $this->theme('tagging_suggestions_list', array('suggestions' => $element['#suggestions'], 'vid' => $vid));
    $fieldset['#value'] = $output;
    $output = $this->theme('fieldset', array('element' => $fieldset));
    return $this->theme('tagging_widget_wrapper', array('content' => $output, 'vid' => $vid));
  }

  public function theme_tagging_widget_button($vid = NULL) {
    return '<a class="tagging-button-container" href="#" title="' . t('Add') . '"><span class="tagging-button tagging-button-' . $vid . '"></span></a>';
  }

  public function theme_tagging_widget_wrapper($content = NULL, $vid = NULL) {
    return "<div id='tagging-widget-container'>" . $content . "</div>";
  }

  public function theme_tagging_tags_list($tags = array(), $vid = NULL) {
    $cur_tags = '';
// they are sorted already
    foreach ($tags as $term) {
      if ($term == '') {
        continue;
      }
// TODO: use # here...but out of any reason it gets encoded to %23 in html
// using trim here, as mostly the old tagging system was "one, two, three"..
      $term = "<span class='tagging-tag'>" . _tagging_check_tag($term) . "</span>";

      $link = l($term, '', array('html' => true, 'attributes' => array('title' => t('Remove tag'), 'fragment' => '#')));
      $cur_tags .= $link;
    }
    return "<div class='tagging-curtags-wrapper tagging-curtags-wrapper-$vid'><label>" . t('Assigned Tags:') . "</label>$cur_tags</div>";
    /*
      $cur_tags = '';
      // they are sorted already
      foreach ($tags as $term) {
      if ($term == '') {
      continue;
      }
      // TODO: use # here...but out of any reason it gets encoded to %23 in html
      // using trim here, as mostly the old tagging system was "one, two, three"..
      $term = "<span class='tagging-tag' id=tagging-tag-" . $term->tid . "'>" . _tagging_check_tag($term->name) . "</span>";

      // iStryker TODO: I believe l function has change for 7
      $link = l($term, '', array('html' => TRUE, 'attributes' => array('title' => t('Remove tag'), 'fragment' => '#')));
      $cur_tags .= $link;
      }
      return "<div class='tagging-curtags-wrapper tagging-curtags-wrapper-" . $vid . "'><label>" . t('Assigned Tags:') . "</label>$cur_tags</div>";
     *
     */
  }

  public function theme_tagging_suggestions_list($suggestions = array(), $vid = NULL) {
    $suggested_tags = '';
// They are sorted already.
    $addNew = t('Add this new tag');
    $addExisting = t('Add this already existing tag');
    foreach ($suggestions as $term) {
      if ($term['#name'] == '') {
        continue;
      }
// TODO: Use # here...but out of any reason it gets encoded to %23 in html.
      $title = $addExisting;
      $class = 'tagging-suggest-tag';
      if (array_key_exists('#new', $term) && $term['#new'] == TRUE) {
        $class .= ' tagging-suggestion-new';
        $title = $addNew;
      }
      $term = "<span id='tagging-$vid' class='{$class}'>" . _tagging_check_tag($term['#name']) . "</span>";
      $link = l($term, '', array('html' => true, 'attributes' => array('title' => $title)));
      $suggested_tags .= $link;
    }
// If we have any suggestions, then place the wrapper.
    if ($suggested_tags != '') {
      return "<div class='tagging-suggestions-wrapper tagging-suggestions-wrapper-$vid'><label>"
      . t('Suggestions') . ":</label>$suggested_tags</div>";
    }
// We have no suggestions, so no need to show any output
    return '';
  }

  /**
   * For compatibility with drupal 7, all calls to the theme-function within this class should be made through this method, using drupal 7 theme funciton call style.
   * Important: put the keys in the right order, so the call can be properly converted into a drupa6-theme-funciton call.
   */
  public function theme($theme_name, $arr) {
    $args = array_merge(array($theme_name), array_values($arr));
    return call_user_func_array('theme', $args);
  }

  public function _tagging_widget_javascript($form_element = null, &$form_state = array()) {
    drupal_add_css(drupal_get_path('module', 'tagging') . '/css/tagging.css');
    drupal_add_js(drupal_get_path('module', 'tagging') . '/js/tagging.plugin.js');
    drupal_add_js(drupal_get_path('module', 'tagging') . '/js/tagging.init.d6.js');
    return $form_element;
  }

}
