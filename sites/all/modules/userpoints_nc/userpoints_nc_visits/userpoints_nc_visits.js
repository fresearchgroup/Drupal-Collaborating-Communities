(function ($) {
  Drupal.behaviors.userpointsNCVisitsFieldsetSummaries = {
    attach: function (context) {
      // This is both for the userpoints settings and the node type settings
      // vertical tab.
      $('fieldset#edit-userpoints-nc-visits', context).drupalSetSummary(function (context) {
        var params = {
          '@points': $('#edit-userpoints-nc-visits-points', context).val(),
          '%category': $('select#edit-userpoints-nc-visits-category :selected', context).text()
        };

        if ($('#edit-userpoints-nc-visits-enabled').is(':checked')) {
          return Drupal.t('Enabled, @points (%category) for visiting content.', params);
        }
        else {
          // If disabled on the node type settings, the other settings don't
          // matter so we don't display them.
          if ($('form#node-type-form').length) {
            return Drupal.t('Disabled.');
          }
          else {
            return Drupal.t('Disabled, @points (%category) for visiting content.', params);
          }
        }
      })
    }
  };

})(jQuery);