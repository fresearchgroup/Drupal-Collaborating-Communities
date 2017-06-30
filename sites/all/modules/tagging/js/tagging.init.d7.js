(function ($) {
    Drupal.behaviors.tagging_d7 = {
        attach:function (context, settings) {
            $('input.tagging-widget-input:not(.tagging-processed)').tagging();
        }
    };
})(jQuery);