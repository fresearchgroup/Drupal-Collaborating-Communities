<?php

namespace Drupal\quizz\Form\QuizForm;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz\Helper\FormHelper;

class FormDefinition extends FormHelper {

  /** @var QuizEntity */
  private $quiz;

  public function __construct($quiz) {
    $this->quiz = $quiz;

    if (empty($this->quiz->qid) && 'admin' !== arg(0)) {
      global $user;

      // If this is a new quiz we apply the user defaults for the quiz settings.
      if (!entity_load('quiz_entity', FALSE, array('uid' => $user->uid))) {
        $msg = t('You are making your first @quiz. On this page you set the attributes, most of which you may tell the system to remember as defaults for the future. On the next screen you can add questions.', array('@quiz' => QUIZZ_NAME));
        drupal_set_message($msg);
      }

      $defaults = quizz_entity_controller()->getSettingIO()->get(TRUE, $this->quiz->type);
      foreach ($defaults as $k => $v) {
        if (!isset($this->quiz->{$k}) || is_null($this->quiz->{$k})) {
          $this->quiz->{$k} = $v;
        }
      }
    }

    if ('admin' === arg(0)) {
      $this->quiz->status = -1;
    }
  }

  /**
   * Main endpoint to get structure for quiz entity editing form.
   *
   * @param array $form
   * @param array $form_state
   * @param string $op
   * @return array
   */
  public function get($form, &$form_state, $op) {
    global $language;

    $quiz_type = quizz_type_load($this->quiz->type);

    if (!empty($quiz_type->help)) {
      $form['quiz_help'] = array(
          '#prefix' => '<div class="quiz-help">',
          '#markup' => check_plain($quiz_type->help),
          '#suffix' => '</div>',
      );
    }

    $form['#attributes']['class'] = array('quiz-entity-form');
    $form['#quiz'] = $this->quiz;

    $form['title'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Title'),
        '#default_value' => isset($this->quiz->title) ? $this->quiz->title : '',
        '#description'   => t('The name of this @quiz.', array('@quiz' => QUIZZ_NAME)),
        '#required'      => TRUE,
        '#weight'        => -20,
    );

    if (module_exists('locale') && $quiz_type->data['multilingual']) {
      $language_options = array();
      foreach (language_list() as $langcode => $lang) {
        $language_options[$langcode] = $lang->name;
      }

      $form['language'] = array(
          '#type'          => count($language_options) < 5 ? 'radios' : 'select',
          '#title'         => t('Language'),
          '#options'       => $language_options,
          '#default_value' => isset($this->quiz->language) ? $this->quiz->language : $language->language,
      );
    }

    // Provides details in vertical tabs.
    $form['vtabs'] = array('#type' => 'vertical_tabs');

    if (module_exists('field_group')) {
      $form['vtabs'] = array(
          '#type'     => 'horizontal_tabs',
          '#attached' => array(
              'library' => array(array('field_group', 'horizontal-tabs'))
          )
      );
    }

    $this->defineTakingOptions($form);
    $this->defineUserPointOptionsFields($form);
    $this->defineAvailabilityOptionsFields($form);
    $this->definePassFailOptionsFields($form);
    $this->defineResultFeedbackFields($form);
    $this->definePublishingOptionsFields($form);

    // Attach custom fields by admin
    if ('admin' !== arg(0)) {
      field_attach_form('quiz_entity', $this->quiz, $form, $form_state);
    }

    $form['actions'] = array(
        '#type'   => 'action',
        '#weight' => 100,
        'submit'  => array('#type' => 'submit', '#value' => t('Save')),
    );

    if (!empty($this->quiz->qid)) {
      $form['actions']['delete'] = array(
          '#type'   => 'submit',
          '#value'  => t('Delete'),
          '#suffix' => l(t('Cancel'), 'admin' === arg(0) ? 'admin/content/quizz' : 'quiz/' . $this->quiz->qid),
      );
    }

    return $form;
  }

  private function defineTakingOptions(&$form) {
    $form['taking'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Taking'),
        '#collapsible' => TRUE,
        '#attributes'  => array('id' => 'taking-fieldset'),
        '#group'       => 'vtabs',
        '#weight'      => -2,
        'taking_tabs'  => array('#type' => 'vertical_tabs')
    );

    $form['taking']['taking_tabs']['basic'] = array(
        '#type'                => 'fieldset',
        '#title'               => t('Basic'),
        '#collapsible'         => TRUE,
        'allow_resume'         => array(
            '#type'          => 'checkbox',
            '#title'         => t('Allow resume'),
            '#default_value' => $this->quiz->allow_resume,
            '#description'   => t('Allow users to leave this @quiz incomplete and then resume it from where they left off.', array('@quiz' => QUIZZ_NAME)),
        ),
        'allow_skipping'       => array(
            '#type'          => 'checkbox',
            '#title'         => t('Allow skipping'),
            '#default_value' => $this->quiz->allow_skipping,
            '#description'   => t('Allow users to skip questions in this @quiz.', array('@quiz' => QUIZZ_NAME)),
        ),
        'allow_jumping'        => array(
            '#type'          => 'checkbox',
            '#title'         => t('Allow jumping'),
            '#default_value' => $this->quiz->allow_jumping,
            '#description'   => t('Allow users to jump to any question using a menu or pager in this @quiz.', array('@quiz' => QUIZZ_NAME)),
        ),
        'allow_change'         => array(
            '#type'          => 'checkbox',
            '#title'         => t('Allow changing answers'),
            // https://www.drupal.org/node/2354355#comment-9241781
            '#default_value' => isset($this->quiz->allow_change) ? $this->quiz->allow_change : 1,
            '#description'   => t('If the user is able to visit a previous question, allow them to change the answer.'),
        ),
        'backwards_navigation' => array(
            '#type'          => 'checkbox',
            '#title'         => t('Backwards navigation'),
            '#default_value' => $this->quiz->backwards_navigation,
            '#description'   => t('Allow users to go back and revisit questions already answered.'),
        ),
        'repeat_until_correct' => array(
            '#type'          => 'checkbox',
            '#title'         => t('Repeat until correct'),
            '#default_value' => $this->quiz->repeat_until_correct,
            '#description'   => t('Require the user to retry the question until answered correctly.'),
        ),
        'mark_doubtful'        => array(
            '#type'          => 'checkbox',
            '#title'         => t('Mark doubtful'),
            '#default_value' => $this->quiz->mark_doubtful,
            '#description'   => t('Allow users to mark their answers as doubtful.'),
        ),
        'show_passed'          => array(
            '#type'          => 'checkbox',
            '#title'         => t('Show passed status'),
            '#default_value' => $this->quiz->show_passed,
            '#description'   => t('Show a message if the user has previously passed the @quiz.', array('@quiz' => QUIZZ_NAME)),
        ),
        'time_limit'           => array(
            '#type'          => 'textfield',
            '#title'         => t('Time limit'),
            '#default_value' => isset($this->quiz->time_limit) ? $this->quiz->time_limit : 0,
            '#description'   => t('Set the maximum allowed time in seconds for this @quiz. Use 0 for no limit.', array('@quiz' => QUIZZ_NAME))
            . '<br/>' . t('It is recommended to install the !countdown module, and enable the option in !link to show the time left to the user.', array(
                '!link'      => l('Quiz configuration', 'admin/quizz/settings/config'),
                '!countdown' => l('jquery_countdown', 'http://drupal.org/project/jquery_countdown'),
            ))
        ),
    );

    $form['taking']['taking_tabs']['build_on_last_tab'] = array(
        '#type'         => 'fieldset',
        '#title'        => t('Build on last'),
        'build_on_last' => array(
            '#type'          => 'radios',
            '#options'       => array(
                ''        => t('Fresh attempt every time'),
                'correct' => t('Prepopulate with correct answers from last result'),
                'all'     => t('Prepopulate with all answers from last result'),
            ),
            '#title'         => t('Each attempt builds on the last'),
            '#default_value' => $this->quiz->build_on_last,
            '#description'   => t('Instead of starting a fresh @quiz, new attempts will be created based on the last attempt, with correct answers prefilled.', array('@quiz' => QUIZZ_NAME)),
        ),
    );

    $form['taking']['taking_tabs']['randomization_tab'] = array(
        '#type'         => 'fieldset',
        '#title'        => t('Randomize questions'),
        'randomization' => array(
            '#type'          => 'radios',
            '#title'         => t('Randomize questions'),
            '#options'       => array(
                t('No randomization'),
                t('Random order'),
                t('Random questions'),
                t('Categorized random questions'),
            ),
            '#description'   => t('<strong>Random order</strong> - all questions display in random order')
            . '<br/>' . t("<strong>Random questions</strong> - specific number of questions are drawn randomly from this @quiz's pool of questions", array('@quiz' => QUIZZ_NAME))
            . '<br/>' . t('<strong>Categorized random questions</strong> - specific number of questions are drawn from each specified taxonomy term'),
            '#default_value' => $this->quiz->randomization,
        ),
    );
    $form['taking']['taking_tabs']['review_options'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Review'),
        '#collapsible' => FALSE,
        '#tree'        => TRUE,
    );

    $review_options = quizz_entity_controller()->getFeedbackOptions();
    foreach (array('question' => t('After the question'), 'end' => t('After the @quiz', array('@quiz' => QUIZZ_NAME))) as $key => $when) {
      $form['taking']['taking_tabs']['review_options'][$key] = array(
          '#title'         => $when,
          '#type'          => 'checkboxes',
          '#options'       => $review_options,
          '#default_value' => isset($this->quiz->review_options[$key]) ? $this->quiz->review_options[$key] : array(),
      );
    }

    $form['taking']['taking_tabs']['multiple_takes'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Multiple takes'),
        '#collapsible' => FALSE,
        '#attributes'  => array('id' => 'multiple-takes-fieldset'),
        '#description' => t('Allow users to take this quiz multiple times.'),
    );
    $form['taking']['taking_tabs']['multiple_takes']['takes'] = array(
        '#type'          => 'select',
        '#title'         => t('Allowed number of attempts'),
        '#default_value' => $this->quiz->takes,
        '#options'       => array(t('Unlimited')) + range(0, 10),
        '#description'   => t('The number of times a user is allowed to take this @quiz. <strong>Anonymous users are only allowed to take @quiz that allow an unlimited number of attempts.</strong>', array('@quiz' => QUIZZ_NAME)),
    );
    $form['taking']['taking_tabs']['multiple_takes']['show_attempt_stats'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Display allowed number of attempts'),
        '#default_value' => $this->quiz->show_attempt_stats,
        '#description'   => t('Display the allowed number of attempts on the starting page for this @quiz.', array('@quiz' => QUIZZ_NAME)),
    );

    if (user_access('delete any quiz results') || user_access('delete results for own quiz')) {
      $form['taking']['taking_tabs']['multiple_takes']['keep_results'] = array(
          '#type'          => 'radios',
          '#title'         => t('Store results'),
          '#description'   => t('These results should be stored for each user.'),
          '#options'       => array(t('The best'), t('The newest'), t('All')),
          '#default_value' => $this->quiz->keep_results,
      );
    }
    else {
      $form['taking']['taking_tabs']['multiple_takes']['keep_results'] = array(
          '#type'  => 'value',
          '#value' => $this->quiz->keep_results,
      );
    }
  }

  private function defineUserPointOptionsFields($form) {
    if (!function_exists('userpoints_userpointsapi') || !variable_get('quiz_has_userpoints', 1)) {
      return;
    }

    $form['userpoints'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Userpoints'),
        '#collapsible' => TRUE,
        '#group'       => 'vtabs',
    );

    $form['userpoints']['has_userpoints'] = array(
        '#type'          => 'checkbox',
        '#default_value' => (isset($this->quiz->has_userpoints) ? $this->quiz->has_userpoints : 1),
        '#title'         => t('Enable UserPoints Module Integration'),
        '#description'   => t('If checked, marks scored in this @quiz will be credited to userpoints. For each correct answer 1 point will be added to user\'s point.', array('@quiz' => QUIZZ_NAME)),
    );

    $form['userpoints']['userpoints_tid'] = array(
        '#type'          => 'select',
        '#options'       => $this->getUserpointsType(),
        '#title'         => t('Userpoints Category'),
        '#states'        => array(
            'visible' => array(':input[name=has_userpoints]' => array('checked' => TRUE)),
        ),
        '#default_value' => isset($this->quiz->userpoints_tid) ? $this->quiz->userpoints_tid : 0,
        '#description'   => t('Select the category to which user points to be added. To add new category see <a href="!url">admin/structure/taxonomy/userpoints</a>', array('!url' => url('admin/structure/taxonomy/userpoints'))),
    );
  }

  /**
   * Set up the availability options.
   */
  private function defineAvailabilityOptionsFields(&$form) {
    $form['quiz_availability'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Availability'),
        '#collapsible' => TRUE,
        '#attributes'  => array('id' => 'availability-fieldset'),
        '#group'       => 'vtabs',
    );

    $form['quiz_availability']['quiz_always'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Always available'),
        '#default_value' => $this->quiz->quiz_always,
        '#description'   => t('Ignore the open and close dates.'),
        '#disabled'      => !module_exists('date_popup'),
    );

    if (module_exists('date_popup')) {
      $format = 'Y-m-d H:i';
      $start = !empty($this->quiz->quiz_open) ? $this->quiz->quiz_open : REQUEST_TIME;
      $close = !empty($this->quiz->quiz_close) ? $this->quiz->quiz_close : REQUEST_TIME + 86400 * $this->quiz->getQuizType()->getConfig('quiz_default_close', 30);

      $form['quiz_availability']['quiz_open'] = array(
          '#type'          => 'date_popup',
          '#title'         => t('Open date'),
          '#default_value' => date($format, $start),
          '#description'   => t('The date this @quiz will become available.', array('@quiz' => QUIZZ_NAME)),
          '#states'        => array(
              'visible' => array(':input[name=quiz_always]' => array('checked' => FALSE)),
          ),
      );

      $form['quiz_availability']['quiz_close'] = array(
          '#type'          => 'date_popup',
          '#title'         => t('Close date'),
          '#default_value' => date($format, $close),
          '#description'   => t('The date this @quiz will become unavailable.', array('@quiz' => QUIZZ_NAME)),
          '#states'        => $form['quiz_availability']['quiz_open']['#states']
      );
    }
    else {
      $form['quiz_availability']['help']['#markup'] = t('Enable the Date Popup (date_popup) module from the !date project to enable support for open and close dates.', array('!date' => l('Date', 'http://drupal.org/project/date')));
    }
  }

  private function definePassFailOptionsFields(&$form) {
    // Quiz summary options.
    $form['summary_options'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Pass/fail'),
        '#collapsible' => TRUE,
        '#attributes'  => array('id' => 'summary_options-fieldset'),
        '#group'       => 'vtabs',
    );

    // If pass/fail option is checked, present the form elements.
    if ($this->quiz->getQuizType()->getConfig('quiz_use_passfail', 1)) {
      $form['summary_options']['pass_rate'] = array(
          '#type'          => 'textfield',
          '#title'         => t('Passing rate for @quiz (%)', array('@quiz' => QUIZZ_NAME)),
          '#default_value' => $this->quiz->pass_rate,
          '#description'   => t('Passing rate for this @quiz as a percentage score.', array('@quiz' => QUIZZ_NAME)),
          '#required'      => FALSE,
      );
      $form['summary_options']['summary_pass'] = array(
          '#type'          => 'text_format',
          '#base_type'     => 'textarea',
          '#title'         => t('Summary text if passed'),
          '#default_value' => $this->quiz->summary_pass,
          '#cols'          => 60,
          '#description'   => t("Summary text for when the user passes the @quiz. Leave blank to not give different summary text if passed, or if not using the \"percent to pass\" option above. If not using the \"percentage needed to pass\" field above, this text will not be used.", array('@quiz' => QUIZZ_NAME)),
          '#format'        => isset($this->quiz->summary_pass_format) && !empty($this->quiz->summary_pass_format) ? $this->quiz->summary_pass_format : NULL,
      );
    }
    // If the pass/fail option is unchecked, use the default and hide it.
    else {
      $form['summary_options']['pass_rate'] = array(
          '#type'     => 'hidden',
          '#value'    => $this->quiz->pass_rate,
          '#required' => FALSE,
      );
    }
    // We use a helper to enable the wysiwyg module to add an editor to the
    // textarea.
    $form['summary_options']['helper']['summary_default'] = array(
        '#type'          => 'text_format',
        '#base_type'     => 'textarea',
        '#title'         => t('Default summary text'),
        '#default_value' => $this->quiz->summary_default,
        '#cols'          => 60,
        '#description'   => t("Default summary. Leave blank if you don't want to give a summary."),
        '#format'        => isset($this->quiz->summary_default_format) && !empty($this->quiz->summary_default_format) ? $this->quiz->summary_default_format : NULL,
    );

    // Number of random questions, max score and tid for random questions are set on
    // the manage questions tab. We repeat them here so that they're not removed
    // if the quiz is being updated.
    $num_rand = (isset($this->quiz->number_of_random_questions)) ? $this->quiz->number_of_random_questions : 0;
    $form['number_of_random_questions'] = array('#type' => 'value', '#value' => $num_rand);
    $max_score_for_random = (isset($this->quiz->max_score_for_random)) ? $this->quiz->max_score_for_random : 0;
    $form['max_score_for_random'] = array('#type' => 'value', '#value' => $max_score_for_random);
    $form['tid'] = array('#type' => 'value', '#value' => (isset($this->quiz->tid)) ? $this->quiz->tid : 0);
  }

  private function defineResultFeedbackFields(&$form) {
    $options = !empty($this->quiz->result_options) ? $this->quiz->result_options : array();
    $num_options = max(count($options), $this->quiz->getQuizType()->getConfig('quiz_max_result_options', 5));

    if ($num_options > 0) {
      $form['result_options'] = array(
          '#type'        => 'fieldset',
          '#title'       => t('Result feedback'),
          '#collapsible' => TRUE,
          '#tree'        => TRUE,
          '#attributes'  => array('id' => 'result_options-fieldset'),
          '#group'       => 'vtabs',
          'ro_tabs'      => array('#type' => 'vertical_tabs')
      );

      for ($i = 0; $i < $num_options; $i++) {
        $option = (count($options) > 0) ? array_shift($options) : NULL; // grab each option in the array
        $form['result_options']['ro_tabs'][$i] = array(
            '#type'        => 'fieldset',
            '#title'       => t('Result Option ') . ($i + 1),
            '#collapsible' => TRUE,
        );
        $form['result_options']['ro_tabs'][$i]['option_name'] = array(
            '#type'          => 'textfield',
            '#title'         => t('Range title'),
            '#default_value' => isset($option['option_name']) ? $option['option_name'] : '',
            '#maxlength'     => 40,
            '#size'          => 40,
            '#description'   => t('e.g., "A" or "Passed"'),
        );
        $form['result_options']['ro_tabs'][$i]['option_start'] = array(
            '#type'          => 'textfield',
            '#title'         => t('Percentage low'),
            '#description'   => t('Show this result for scored @quiz in this range (0-100).', array('@quiz' => QUIZZ_NAME)),
            '#default_value' => isset($option['option_start']) ? $option['option_start'] : '',
            '#size'          => 5,
        );
        $form['result_options']['ro_tabs'][$i]['option_end'] = array(
            '#type'          => 'textfield',
            '#title'         => t('Percentage high'),
            '#description'   => t('Show this result for scored @quiz in this range (0-100).', array('@quiz' => QUIZZ_NAME)),
            '#default_value' => isset($option['option_end']) ? $option['option_end'] : '',
            '#size'          => 5,
        );
        $form['result_options']['ro_tabs'][$i]['option_summary'] = array(
            '#type'          => 'text_format',
            '#base_type'     => 'textarea',
            '#title'         => t('Feedback'),
            '#default_value' => isset($option['option_summary']) ? $option['option_summary'] : '',
            '#description'   => t("This is the text that will be displayed when the user's score falls in this range."),
            '#format'        => isset($option['option_summary_format']) ? $option['option_summary_format'] : NULL,
        );
        if (isset($option['option_id'])) {
          $form['result_options']['ro_tabs'][$i]['option_id'] = array(
              '#type'  => 'hidden',
              '#value' => isset($option['option_id']) ? $option['option_id'] : '',
          );
        }
      }
    }
  }

  private function definePublishingOptionsFields(&$form) {
    $form['publishing'] = array(
        '#type'           => 'fieldset',
        '#title'          => t('Publishing'),
        '#collapsible'    => TRUE,
        '#group'          => 'vtabs',
        '#weight'         => -10,
        'publishing_tabs' => array('#type' => 'vertical_tabs'),
        'publishing'      => array(
            '#type'  => 'fieldset',
            '#title' => t('Publishing options'),
            '#group' => 'publishing_tabs',
            'status' => array(
                '#type'          => 'checkbox',
                '#title'         => t('Published'),
                '#default_value' => isset($this->quiz->status) ? $this->quiz->status : TRUE,
            ),
        ),
    );

    $form['remember'] = array(
        '#type'             => 'fieldset',
        '#title'            => t('Remember'),
        '#collapsible'      => TRUE,
        '#group'            => 'publishing_tabs',
        'remember_settings' => array(
            '#type'        => 'checkbox',
            '#title'       => t('Remember my settings'),
            '#description' => t('If this box is checked most of the @quiz specific settings you have made will be remembered and will be your default settings next time you create a @quiz.', array('@quiz' => QUIZZ_NAME)),
        ),
        'remember_global'   => array(
            '#type'        => 'checkbox',
            '#title'       => t('Remember as global'),
            '#description' => t('If this box is checked most of the @quiz specific settings you have made will be remembered and will be everyone\'s default settings next time they create a @quiz.', array('@quiz' => QUIZZ_NAME)),
            '#access'      => user_access('administer quiz configuration'),
        ),
    );

    $this->definePathAliasFields($form);

    $form['revision_information'] = array(
        '#type'   => 'fieldset',
        '#title'  => t('Revision information'),
        '#group'  => 'publishing_tabs',
        '#weight' => 20,
        '#access' => TRUE,
    );

    $auto_revisioning = !empty($this->quiz->is_new) ? 0 : $this->quiz->getQuizType()->getConfig('quiz_auto_revisioning', 1);

    $form['revision_information']['revision'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Create new revision'),
        '#default_value' => $auto_revisioning,
        '#state'         => array('checked' => array('textarea[name="log"]' => array('empty' => FALSE))),
    );

    $form['revision_information']['log'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Revision log message'),
        '#row'           => 4,
        '#default_value' => !$auto_revisioning ? '' : t('The current revision has been answered. We create a new revision so that the reports from the existing answers stays correct.'),
        '#description'   => t('Provide an explanation of the changes you are making. This will help other authors understand your motivations.'),
    );

    if ($auto_revisioning && !user_access('manual quiz revisioning')) {
      $form['revision_information']['revision']['#type'] = 'value';
      $form['revision_information']['revision']['#value'] = 1;
      $form['revision_information']['log']['#type'] = 'value';
      $form['revision_information']['log']['#value'] = $form['revision_information']['log']['#default_value'];
      $form['revision_information']['#access'] = FALSE;
      if ($this->quiz->isAnswered()) {
        $this->quiz->revision = 1;
        $this->quiz->log = t('The current revision has been answered. We create a new revision so that the reports from the existing answers stays correct.');
      }
    }

    // Force create revision, even admin, if question is already answered.
    if (!empty($this->quiz->is_new) && $this->quiz->isAnswered()) {
      $form['revision_information']['revision']['#disabled'] = TRUE;
    }

    // @see QuizController::cloneRelationship()
    $form['clone_relationships'] = array('#type' => 'hidden', '#value' => TRUE);
  }

  private function definePathAliasFields(&$form) {
    if (!module_exists('path')) {
      return;
    }

    $path = array();

    if (!empty($this->quiz->qid)) {
      $uri = entity_uri('quiz_entity', $this->quiz);
      $conditions = array('source' => $uri['path']);
      if (($langcode = entity_language('quiz_entity', $this->quiz)) && ($langcode != LANGUAGE_NONE)) {
        $conditions['language'] = $langcode;
      }

      if (!$path = path_load($conditions)) {
        $path = array();
      }
    }

    $path += array(
        'pid'      => NULL,
        'source'   => !empty($uri['path']) ? $uri['path'] : NULL,
        'alias'    => '',
        'language' => isset($langcode) ? $langcode : LANGUAGE_NONE,
    );

    $form['path'] = array(
        '#tree'             => TRUE,
        '#type'             => 'fieldset',
        '#title'            => t('URL path settings'),
        '#group'            => 'publishing_tabs',
        '#attributes'       => array('class' => array('path-form')),
        '#attached'         => array('js' => array(drupal_get_path('module', 'path') . '/path.js')),
        '#access'           => user_access('create url aliases') || user_access('administer url aliases'),
        '#weight'           => 30,
        '#element_validate' => array('path_form_element_validate'),
        'alias'             => array(
            '#type'          => 'textfield',
            '#title'         => t('URL alias'),
            '#default_value' => $path['alias'],
            '#maxlength'     => 255,
            '#description'   => t('Optionally specify an alternative URL by which this content can be accessed. For example, type "about" when writing an about page. Use a relative path and don\'t add a trailing slash or the URL alias won\'t work.'),
        ),
        'pid'               => array('#type' => 'value', '#value' => $path['pid']),
        'source'            => array('#type' => 'value', '#value' => $path['source']),
        'language'          => array('#type' => 'value', '#value' => $path['language']),
    );
  }

}
