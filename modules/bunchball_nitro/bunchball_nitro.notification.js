(function($) {
  Drupal.behaviors.bunchballNitroNotification = {
    attach: function (context, settings) {
      if (typeof nitro !== "undefined") {
        var notificationInterval = Number(Drupal.settings.bunchball_nitro.notificationInterval);
        nitro.showPendingNotifications();
        setInterval(nitro.showPendingNotifications, notificationInterval);
      }
    }
  };
}) (jQuery);
