<?php

namespace Drupal\quizz\Entity;

use EntityDefaultViewsController;

class QuizViewsController extends EntityDefaultViewsController {

  public function views_data() {
    $data = parent::views_data();

    $data['quiz_entity']['view_node']['field'] = array(
        'title'   => t('Link'),
        'help'    => t('Provide a simple link to the !quiz.', array('!quiz' => QUIZZ_NAME)),
        'handler' => 'Drupal\quizz\Entity\Views\QuizEntityLink',
    );

    $data['quiz_entity']['edit_node']['field'] = array(
        'title'   => t('Edit link'),
        'help'    => t('Provide a simple link to edit the !quiz.', array('!quiz' => QUIZZ_NAME)),
        'handler' => 'Drupal\quizz\Entity\Views\QuizEntityEditLink',
    );

    $data['quiz_entity']['delete_node']['field'] = array(
        'title'   => t('Delete link'),
        'help'    => t('Provide a simple link to delete the !quiz.', array('!quiz' => QUIZZ_NAME)),
        'handler' => 'Drupal\quizz\Entity\Views\QuizEntityDeleteLink',
    );

    return $data;
  }

}
