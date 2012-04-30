// JavaScript closure. Defined jQuery $.
(function ($) {
  $(document).ready(function() {
      // TODO: bind the actions to functions below
      // alert(Drupal.settings.bunchball_nitro.sessionkey);
      // userViewedPhoto();
  });
  
}) (jQuery);


function userViewedPhoto() {
  submitNitroAPICall('PHOTO_VIEWED');
}

function userViewedVideo() {
  submitNitroAPICall('PHOTO_VIEWED');
}

function userViewedNode() {
  submitNitroAPICall('CONTENT_VIEWED');
}

function submitNitroAPICall(tags) {
  if(nitro == null) {
    return;
  }

  // populate this properly with session Key in Drupal.settings..
  var sessionKey = Drupal.settings.bunchball_nitro.sessionkey;
  
  var queryString = 'method=user.logAction&sessionKey=' + sessionKey + '&tags=';
  queryString += tags;
  
  alert(queryString + ' was called');
  // TODO: nitro.callAPI(queryString, "nitroCallback"); 
}

function nitroCallback(data, token) {
  // this is a stub that can be used later to track responses from the server.
}