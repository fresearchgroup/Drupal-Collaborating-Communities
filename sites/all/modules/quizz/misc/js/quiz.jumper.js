(function ($, Drupal) {

  Drupal.behaviors.quizJumper = {
    attach: function (context) {
      $("#edit-question-number:not(.quizJumper-processed)", context)
        .addClass("quizJumper-processed")
        .change(function () {
          $(this).parents('form:eq(0)').trigger('submit');
        });

      $("#quiz-jumper-no-js:not(.quizJumper-processed)")
        .hide()
        .addClass("quizJumper-processed");
    }
  };

})(jQuery, Drupal);
