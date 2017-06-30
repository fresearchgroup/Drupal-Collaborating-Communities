<?php

namespace Drupal\quizz_question\Entity;

use EntityDefaultViewsController;

class QuestionViewsController extends EntityDefaultViewsController {

  public function views_data() {
    $data = parent::views_data();

    // Define custom data for views
    // …

    return $data;
  }

}
