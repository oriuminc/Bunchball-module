(function($) {
  Drupal.behaviors.bunchballNitroNotification = {
    attach: function (context, settings) {
      if (typeof Drupal.bunchball.nitro !== "undefined") {
        var notificationInterval = Number(Drupal.settings.bunchball_nitro_notification.notificationInterval);
        Drupal.bunchball.nitro.showPendingNotifications();
        setInterval(showNotifications, notificationInterval);
      }
    }
  };

  function showNotifications () {
    Drupal.bunchball.nitro.showPendingNotifications();
  }
}) (jQuery);
