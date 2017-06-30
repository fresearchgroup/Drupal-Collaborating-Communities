Drupal.behaviors.mltag_help_button = {
  attach: function (context, settings) {
    jQuery(".mltag_help_button_wrapper").click(function() {
        jQuery(".mltag_help_main_wrapper").toggle(800);
        $text = jQuery(".mltag_help_button_wrapper").text();
        if($text == 'Help') {
          jQuery(".mltag_help_button_wrapper").text("X");
          jQuery(".mltag_help_button_wrapper").css({"background-image": "linear-gradient(to bottom, #B43C45, #93090B)","background": "-webkit-gradient(linear, left top, left bottom, from(#B43C45), to(#93090B))"});
        }
        else {
          jQuery(".mltag_help_button_wrapper").text("Help");
          jQuery(".mltag_help_button_wrapper").css({"background-image": "linear-gradient(to bottom, #5BC0DE, #2F96B4)","background": "-webkit-gradient(linear, left top, left bottom, from(#5BC0DE), to(#2F96B4))"});
        }
      });
  }
};
