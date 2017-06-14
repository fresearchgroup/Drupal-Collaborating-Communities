
// Author: Eugen Mayer (http://kontextwork.de)
// Copyright 2010

(function ($) {
  Drupal.behaviors.tagging_d6 = function() {
    var obj = {
      attach: function() {
        $('input.tagging-widget-input:not(.tagging-processed)').tagging();
      }
    };
    obj.attach();
    //return obj;
  };
})(jQuery);