<?php

/**
 * @file
 * Definition of views_handler_field_user_picture_initials.
 */

/**
 * Field handler to provide simple renderer that allows using a themed user link.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_user_picture_initials extends views_handler_field_user_picture {
  function render($values) {
    $account = user_load($this->get_value($values, 'uid'));
    if (empty($account->picture)) {
      return user_picture_initials_render_initials($account);
    }
    return parent::render($values);
  }
}
