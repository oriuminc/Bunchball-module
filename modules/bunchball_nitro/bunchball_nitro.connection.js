Drupal.bunchball = Drupal.bunchball || {};

(function($) {
  Drupal.behaviors.bunchballNitroConnection = {
    attach: function (context, settings) {
      var connectionParams = Drupal.settings.bunchballNitroConnection.connectionParams;
      Drupal.bunchball.nitro = new Nitro(connectionParams);
    }
  };
}) (jQuery);
