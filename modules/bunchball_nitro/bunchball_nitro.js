// JavaScript closure. Defined jQuery $.
(function ($) {
  
  $(document).ready(function() {
    nitro.getUserId(gotCurrentUserId);
    nitro.showPendingNotifications();
  });  
  
}) (jQuery);

var _currentUserId = '';
var _userCommandsArray = new Array();

// callback function for acquiring the User ID of the current user...
function gotCurrentUserId(inUserId) {
  _currentUserId = inUserId;
  
  // user viewed content...
  if (typeof Drupal.settings.bunchball_nitro.node_id != 'undefined') {
    // we are in a node... 
    userViewedContent();
  }
  
  // TODO: bind the actions to functions below
  
}

// ViewedContent is called because the user is currently viewing content.
function userViewedContent() {
  var title    = Drupal.settings.bunchball_nitro.node_title;
  var type     = Drupal.settings.bunchball_nitro.node_type;
  var cat      = Drupal.settings.bunchball_nitro.node_cat;
  var sentTags = 'View_'+type+', Title: '+title+', Category: ' +cat;
  
  // add requests for all players into the array to walk through.
  var inObj = new Object();
  inObj.uid = _currentUserId;
  inObj.tags = sentTags;
  inObj.ses = '';
  _userCommandsArray.push(inObj);
  
  var uid = Drupal.settings.bunchball_nitro.node_uid;
  
  // TODO: here is where you'd check to make sure the users isn't the same as
  // the creator.
  
  inObj = new Object();
  inObj.uid = uid;
  inObj.tags = sentTags;
  inObj.ses = '';
  _userCommandsArray.push(inObj);
  
  nitroIterateQueue();
}

function submitNitroAPICall(tags) {
  if(typeof nitro == 'undefined') {
    return;
  }

  var queryString = 'method=user.logAction&sessionKey=';
  queryString += _userCommandsArray[0].ses + '&tags=';
  queryString += tags;

  // alert(queryString + ' was called with user ' + _userCommandsArray[0].uid);
  nitro.callAPI(queryString, "nitroCallback"); 
}

function nitroCallback(data, token) {
  // this is a stub that can be used later to track responses from the server.

  // alert(JSON.stringify(data));
  
  // remove from array
  if (_userCommandsArray.length > 0) {
    _userCommandsArray.splice(0, 1);
  }
  
  nitroIterateQueue();
}

function nitroLogin(userId) {
  
  // build the login request
  var loginQuery = "method=user.login&";
  loginQuery += "apiKey="+connectionParams.apiKey+"&";
  loginQuery += "userId="+userId+"&";
  loginQuery += "ts="+connectionParams.timeStamp+"&";
  loginQuery += "sig=" + connectionParams.signature;
  
  nitro.callAPI(loginQuery, "nitroLoginCallback");
}

function nitroLoginCallback(data, token) {
  // this is a stub that can be used later to track responses from the server.
  _userCommandsArray[0].ses = data['Nitro']['Login']['sessionKey'];
  
  // do the nitro API call..
  submitNitroAPICall(_userCommandsArray[0].tags);
}


function nitroIterateQueue() {
  // proceed to log in next user
  if (_userCommandsArray.length > 0) {
    nitroLogin(_userCommandsArray[0].uid);
  }
}

