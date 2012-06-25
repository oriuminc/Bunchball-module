(function($) {
  Drupal.behaviors.bunchball = {
    attach: function (context, settings) {
      if (typeof nitro !== "undefined") {
        nitro.refreshNML();
      }
    }
  };
}) (jQuery);
