(function($) {
  Drupal.behaviors.bunchballNitroNML = {
    attach: function (context, settings) {
      if (typeof Drupal.bunchball.nitro !== "undefined") {
        Drupal.bunchball.nitro.refreshNML();
      }
    }
  };
}) (jQuery);
