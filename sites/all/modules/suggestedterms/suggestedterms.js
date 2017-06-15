(function ($) {
Drupal.behaviors.suggestedTerms = {
  attach: function (context) {

    // Update the list to reflect the contents of the field on pageload.
    toggleTermsInList();

    // Get all the suggestedterm links
    $('a.suggestedterm').each ( function() {
      // Change the path to an anchor.
      $(this).attr('href', '#');

      // Update the text field from the list on click.
      $(this).bind('click', function(event) {
          event.preventDefault();
          // Get the form item's input object and make an array of its text.
          var input = $(this).closest('.form-type-textfield').find('input');
          toggleTermsInField(this, input);
        }); // end bind

        // Update the list from the textfield on keyup.
        $(this).closest('.form-type-textfield').find('input').bind('keyup', toggleTermsInList);

      }); // end a.suggestedterm

      /**
       * Adds term to/removes it from the field when the term is clicked.
       * @param item
       *  The term that's being clicked. An <a> element.
       * @param input
       *  The input field that's being updated.
       */
      function toggleTermsInField(item, input) {
        // The text of the link element.
        var text = item.text;
        // An array of words in the text field.
        var input_array = $.map(input.val().split(','), $.trim);

        // If it's not already in the input field, add it.
        if ($.inArray(text, input_array) < 0) {
          // If it's not the first item in the field, prefix with comma.
          if (input.val().length > 0) {
            input.val(input.val() + ', ');
          }
          // Append the value to the field value.
          input.val(input.val() + text);
          // Mark it as removed from the available options.
          $(item).addClass('remove');
        }
        else {
          // Remove the clicked item from the input array.
          input_array.splice( $.inArray(text, input_array), 1 );
          // Convert the array to a comma-separated string.
          var input_string = input_array.join(', ');
          // Set the input value to the new string, sans clicked item.
          input.val(input_string);
          // Put it back in the list of available options.
          $(item).removeClass('remove');
        }
      }

      /**
       * Updates the classes on the list of selected terms to reflect field.
       */
      function toggleTermsInList() {
      var termlist = $('div.suggestedterms');
      var input_array = $.map($(termlist.closest('.form-type-textfield').find('input')).val().split(','), $.trim);
      // Go through the list of suggestions.
      $(termlist).closest('.form-type-textfield').find('a.suggestedterm').each(function () {
        // If the suggestion hasn't been used, don't mark it 'remove'.
        if($.inArray($(this).text(), input_array) < 0) {
          $(this).removeClass('remove');
        }
        // But if it has been used, then do mark it 'remove'.
        else {
          $(this).addClass('remove');
        }
      });
    }

    } // end of attach:
} // end of Drupal.behaviors.suggestedTerms
})(jQuery);
