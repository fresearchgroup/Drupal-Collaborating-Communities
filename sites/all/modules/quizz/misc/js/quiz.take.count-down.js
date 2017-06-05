var quiz_take_finished;

(function (window, $, Drupal) {
  var button_1 = Drupal.settings.quiz_button_1;
  var button_2 = Drupal.settings.quiz_button_2;

  quiz_take_finished = function () {
    // Find all buttons with a name of 'op'.
    var buttons = $('input[type=submit][name=op], button[type=submit][name=op]');

    // Filter out the ones that don't have the right op value.
    buttons = buttons.filter(function () {
      return (this.value === button_1) || (this.value === button_2);
    });

    if (buttons.length === 1) {
      // Since only one button was found, this must be it.
      buttons.click();
    }
    else {
      // Zero, or more than one buttons were found; fall back on a page refresh.
      window.location = window.location.href;
    }
  };

})(window, jQuery, Drupal);
