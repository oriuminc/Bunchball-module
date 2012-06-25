(function($) {
  Drupal.behaviors.bunchballNitroNML = {
    attach: function (context, settings) {
      if (typeof nitro !== "undefined") {
        nitro.refreshNML();
      }
    }
  };
}) (jQuery);
