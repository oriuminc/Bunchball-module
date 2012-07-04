(function ($) {
  Drupal.behaviors.request_queue_runner = {
    attach: function(context, settings) {
      var queues, url;
      if (Drupal.settings.request_queue && Drupal.settings.request_queue.queues) {
        queues = Drupal.settings.request_queue.queues;
      }
      else {
        queues = ['request_queue'];
      }
      url = Drupal.settings.basePath + 'request_queue/?queues=' + queues.join(',');
      $.ajax({"url": url});
    }
  };
}(jQuery));