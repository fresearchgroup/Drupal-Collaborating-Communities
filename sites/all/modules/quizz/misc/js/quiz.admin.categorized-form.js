(function ($, Drupal) {

  Drupal.behaviors.quiz_categorized = {
    attach: function (context) {
      $('#browse-for-term:not(.quiz-processed)', context)
        .addClass('quiz-processed')
        .click(function (event) {
          event.preventDefault();
          $('#edit-term').focus().val('*').trigger('keyup');
        });

      $('#edit-term', context)
        .click(function () {
          if ('*' === $(this).val()) {
            $(this).val('');
          }
        });
    }};

}(jQuery, Drupal));
