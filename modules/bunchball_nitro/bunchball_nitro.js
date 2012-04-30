// JavaScript closure. Defined jQuery $.
(function ($) {
  $(document).ready(function() {
      // bind the actions to functions below
  });
  
}) (jQuery);


function doSomethingFun() {
    alert('This Is Fun!!');
}

function userViewedPhoto() {
  submitNitroAPICall(tags);
}

function userViewedVideo() {
  submitNitroAPICall(tags);
}

function userViewedPhoto() {
  submitNitroAPICall(tags);
}

function submitNitroAPICall(tags) {
  if(nitro == null) {
    return;
  }
  
  var sessionKey = 'thisIsASessionKeyFromUser.Login';
  
  var queryString = 'method=user.logAction&sessionKey=' + sessionKey + '&tags=';
  queryString += tags;
  
  alert(queryString + ' was called');
  // nitro.callAPI(queryString, "nitroCallback"); 
}

function nitroCallback(data, token) {
  
}