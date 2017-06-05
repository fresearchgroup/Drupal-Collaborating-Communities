(function ($, Drupal) {

  Drupal.behaviors.quizAnswerConfirm = {
    attach: function (context) {
      $('form.quiz-answer-confirm #edit-submit', context).once(function () {
        var message = $(this).data('confirm-message');

        // Return false to avoid submitting if user aborts
        $(this).submit(function () {
          return confirm(message);
        });
      });
    }
  };

})(jQuery, Drupal);
