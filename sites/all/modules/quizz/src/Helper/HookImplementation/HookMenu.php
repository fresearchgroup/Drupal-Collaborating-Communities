<?php

namespace Drupal\quizz\Helper\HookImplementation;

class HookMenu {

  public function execute() {
    $items = array();

    $items += $this->getQuizAdminMenuItems();
    $items += $this->getQuizUserMenuItems();

    return $items;
  }

  private function getQuizAdminMenuItems() {
    $items = array();

    // Admin pages.
    $items['admin/quizz'] = array(
        'title'            => '@quiz',
        'title arguments'  => array('@quiz' => QUIZZ_NAME),
        'description'      => 'View results, score answers, run reports and edit configurations.',
        'page callback'    => 'system_admin_menu_block_page',
        'access arguments' => array('administer quiz configuration', 'score any quiz', 'score own quiz', 'view any quiz results', 'view results for own quiz'),
        'access callback'  => 'quizz_access_multi_or',
        'type'             => MENU_NORMAL_ITEM,
        'file'             => 'system.admin.inc',
        'file path'        => drupal_get_path('module', 'system'),
    );

    $items['admin/quizz/settings'] = array(
        'title'            => '@quiz settings',
        'title arguments'  => array('@quiz' => QUIZZ_NAME),
        'description'      => 'Change settings for the all Quiz project modules.',
        'page callback'    => 'system_admin_menu_block_page',
        'access arguments' => array('administer quiz configuration'),
        'type'             => MENU_NORMAL_ITEM,
        'file'             => 'system.admin.inc',
        'file path'        => drupal_get_path('module', 'system'),
    );

    $items['admin/quizz/settings/config'] = array(
        'title'            => '@quiz configuration',
        'title arguments'  => array('@quiz' => QUIZZ_NAME),
        'description'      => 'Configure the Quiz module.',
        'page callback'    => 'drupal_get_form',
        'page arguments'   => array('quizz_admin_settings_form'),
        'access arguments' => array('administer quiz configuration'),
        'type'             => MENU_NORMAL_ITEM, // optional
        'file'             => 'includes/quizz.pages.inc',
    );

    $items['admin/quizz/settings/quiz-form'] = array(
        'title'            => '@quiz form configuration',
        'title arguments'  => array('@quiz' => QUIZZ_NAME),
        'description'      => 'Configure default values for the quiz creation form.',
        'page callback'    => 'drupal_get_form',
        'page arguments'   => array('quizz_admin_entity_form'),
        'access arguments' => array('administer quiz configuration'),
        'type'             => MENU_NORMAL_ITEM, // optional
        'file'             => 'includes/quizz.pages.inc',
    );

    $items['admin/quizz/reports'] = array(
        'title'            => '@quiz reports and scoring',
        'title arguments'  => array('@quiz' => QUIZZ_NAME),
        'description'      => 'View reports and score answers.',
        'page callback'    => 'system_admin_menu_block_page',
        'access arguments' => array('view any quiz results', 'view results for own quiz'),
        'access callback'  => 'quizz_access_multi_or',
        'type'             => MENU_NORMAL_ITEM,
        'file'             => 'system.admin.inc',
        'file path'        => drupal_get_path('module', 'system'),
    );

    return $items;
  }

  private function getQuizUserMenuItems() {
    $items = array();

    // User pages.
    $items['user/%/quiz-results/%quizz_result/view'] = array(
        'title'            => 'User results',
        'access arguments' => array(3),
        'access callback'  => 'quizz_access_my_result',
        'file'             => 'includes/quizz.pages.inc',
        'page callback'    => 'quizz_result_page',
        'page arguments'   => array(3),
        'type'             => MENU_CALLBACK,
    );

    return $items;
  }

}
