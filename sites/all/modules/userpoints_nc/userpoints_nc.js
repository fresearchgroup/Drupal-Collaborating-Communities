(function ($) {
  Drupal.behaviors.userpointsNCFieldsetSummaries = {
    attach: function (context) {
      // This is both for the userpoints settings and the node type settings
      // vertical tab.
      $('fieldset#edit-userpoints-nc', context).drupalSetSummary(function (context) {
        var params = {
          '@points': $('#edit-userpoints-nc-points', context).val(),
          '%category': $('select#edit-userpoints-nc-category :selected', context).text()
        };

        if ($('#edit-userpoints-nc-enabled').is(':checked')) {
          if ($('#edit-userpoints-nc-published-only').is(':checked')) {
            return Drupal.t('Enabled (published only), @points (%category) for content.', params);
          }
          else {
            return Drupal.t('Enabled (all), @points (%category) for content.', params);
          }
        }
        else {
          // If disabled on the node type settings, the other settings don't
          // matter so we don't display them.
          if ($('form#node-type-form').length) {
            return Drupal.t('Disabled.');
          }
          else {
            if ($('#edit-userpoints-nc-published-only').is(':checked')) {
              return Drupal.t('Disabled (published only), @points (%category) for content.', params);
            }
            else {
              return Drupal.t('Disabled (all), @points (%category) for content.', params);
            }
          }
        }
      })
    }
  };

  Drupal.behaviors.userpointsNCCommentFieldsetSummaries = {
    attach: function (context) {
      // This is both for the userpoints settings and the node type settings
      // vertical tab.
      $('fieldset#edit-userpoints-nc-comment', context).drupalSetSummary(function (context) {
        var params = {
          '@points': $('#edit-userpoints-nc-comment-points', context).val(),
          '%category': $('select#edit-userpoints-nc-comment-category :selected', context).text()
        };

        if ($('#edit-userpoints-nc-comment-enabled').is(':checked')) {
          if ($('#edit-userpoints-nc-comment-published-only').is(':checked')) {
            return Drupal.t('Enabled (published only), @points (%category) for comments.', params);
          }
          else {
            return Drupal.t('Enabled (all), @points (%category) for comments.', params);
          }
        }
        else {
          // If disabled on the node type settings, the other settings don't
          // matter so we don't display them.
          if ($('form#node-type-form').length) {
            return Drupal.t('Disabled.');
          }
          else {
            if ($('#edit-userpoints-nc-comment-published-only').is(':checked')) {
              return Drupal.t('Disabled (published only), @points (%category) for comments.', params);
            }
            else {
              return Drupal.t('Disabled (all), @points (%category) for comments.', params);
            }
          }
        }
      })
    }
  };

})(jQuery);