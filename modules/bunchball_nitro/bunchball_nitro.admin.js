(function($) {
  Drupal.behaviors.bunchball = {
    attach: function (context, settings) {
      
      // find the checkboxes
      $("input[type='checkbox']").each(function (i) {
        var the_checkbox = this;
        
        // get references to related textfields..
        $(this).parent().parent().children('div.form-type-textfield').each(function (j) {
          var the_tf_container = this;
          
          // if the checkbox isn't selected, mark as hidden
           if(!$(the_checkbox).attr('checked')) {
             $(the_tf_container).hide();
           }

          $(the_checkbox).click(function(){
            $(the_tf_container).toggle();
          });
        });
      });
    }
  };
}) (jQuery);
