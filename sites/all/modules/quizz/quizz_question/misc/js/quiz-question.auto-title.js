(function ($, Drupal) {
  var body_query = '#edit-body textarea:eq(1), #edit-quiz-question-body-und-0-value';

  function quizUpdateTitle() {
    var body = $(body_query).val().replace(/<\/?[^>]+>/gi, '');
    var max_length = parseInt(Drupal.settings.quiz_max_length);
    var title = body.length <= max_length ? body.substring(0, max_length) : body.substring(0, max_length - 3) + "…";
    $('#edit-title').val(title);
  }

  $(document).ready(function () {
    $(body_query).keyup(quizUpdateTitle);

    // Do not use auto title if a title already has been set
    if ($('#edit-title').val().length > 0) {
      $(body_query).unbind("keyup", quizUpdateTitle);
    }

    $('#edit-title').keyup(function () {
      $(body_query).unbind("keyup", quizUpdateTitle);
    });
  });

})(jQuery, Drupal);
