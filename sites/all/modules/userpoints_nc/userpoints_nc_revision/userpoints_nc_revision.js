(function ($) {
  Drupal.behaviors.userpointsNCRevisionFieldsetSummaries = {
    attach: function (context) {
      // This is both for the userpoints settings and the node type settings
      // vertical tab.
      $('fieldset#edit-userpoints-nc-revision', context).drupalSetSummary(function (context) {
        var params = {
          '@points': $('#edit-userpoints-nc-revision-points', context).val(),
          '%category': $('select#edit-userpoints-nc-revision-category :selected', context).text()
        };

        if ($('#edit-userpoints-nc-revision-enabled').is(':checked')) {
          return Drupal.t('Enabled, @points (%category) for a new revision.', params);
        }
        else {
          // If disabled on the node type settings, the other settings don't
          // matter so we don't display them.
          if ($('form#node-type-form').length) {
            return Drupal.t('Disabled.');
          }
          else {
            return Drupal.t('Disabled, @points (%category) for a new revision.', params);
          }
        }
      })
    }
  };

})(jQuery);