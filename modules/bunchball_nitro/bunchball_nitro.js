// JavaScript closure. Defined jQuery $.
(function ($) {
  $(document).ready(function() {
      // TODO: bind the actions to functions below
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

  // TODO: populate this properly with session Key in Drupal.settings..
  var sessionKey = 'thisIsASessionKeyFromUser.Login';
  
  var queryString = 'method=user.logAction&sessionKey=' + sessionKey + '&tags=';
  queryString += tags;
  
  alert(queryString + ' was called');
  // TODO: nitro.callAPI(queryString, "nitroCallback"); 
}

function nitroCallback(data, token) {
  // this is a stub that can be used later to track responses from the server.
}