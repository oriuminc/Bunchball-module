(function($) {
  Drupal.behaviors.bunchballNitroContent = {
    attach: function (context, settings) {
      if (typeof nitro !== "undefined") {
        nitro.getUserId(gotCurrentUserId);
      }
    }
  };
}) (jQuery);

var _currentUserId = '';
var _userCommandsArray = new Array();
var _thePlayer = '';
var _playerPlayed = 0;

// Callback function for acquiring the User ID of the current user.
function gotCurrentUserId(inUserId) {
  _currentUserId = inUserId;

  // User viewed content.
  if (typeof Drupal.settings.bunchball_nitro.node_id !== 'undefined') {
    // We are in a node.
    userViewedContent();
  }
}

// ViewedContent is called because the user is currently viewing content.
function userViewedContent() {
  var title    = Drupal.settings.bunchball_nitro.node_title;
  var type     = Drupal.settings.bunchball_nitro.node_type;
  var cat      = Drupal.settings.bunchball_nitro.node_cat;
  var bb_tag   = Drupal.settings.bunchball_nitro.node_action;
  var sentTags = bb_tag + ', Title: ' + title + ', Category: ' + cat;
  
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

  nitroCallback("data", "token");
  nitro.callAPI(queryString, "nitroCallback"); 
}

function nitroCallback(data, token) {
  // remove from array
  if (_userCommandsArray.length > 0) {
    _userCommandsArray.splice(0, 1);
  }
  
  nitroIterateQueue();
}

function nitroLogin(userId) {
  
  // build the login request
  var loginQuery = "method=user.login&";
  loginQuery += "apiKey=" + connectionParams.apiKey + "&";
  loginQuery += "userId=" + userId + "&";
  loginQuery += "ts=" + connectionParams.timeStamp + "&";
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

function onYouTubePlayerReady(playerId) {
  // attach the listener
  (function ($) {
    $("div.oembed-video .oembed-content object embed").each(function(i){
      _thePlayer = this;
      _thePlayer.addEventListener("onStateChange", "nitroVideoStateChange");
    });
  }) (jQuery);
}

// event listeners
function nitroVideoStateChange(newState) {
  // empty action to prevent anything from going forward
  var action = "";
  
  if (newState == 0) { // ended
    action = Drupal.settings.bunchball_nitro.artist_end;
    _playerPlayed = 0;
  } else if (newState == 1 && _playerPlayed == 0) { // playing not started
    action = Drupal.settings.bunchball_nitro.artist_start;
    _playerPlayed = 1;
  }
  
  // only continue if there is something in Action
  if (action.length > 1) {
    action = action + ",Artist: " + Drupal.settings.bunchball_nitro.artist_name 
      + ", Category: " + Drupal.settings.bunchball_nitro.artist_cat;
    
    var inObj = new Object();
    inObj.uid = _currentUserId;
    inObj.tags = action;
    inObj.ses = '';
    _userCommandsArray.push(inObj);
    
    nitroIterateQueue();
  }
}

function nitroSocialShareClicked(network) {
  if(network.length > 0) {
    var action = "Share_Link, Network: " + network;
    
    var inObj = new Object();
    inObj.uid = _currentUserId;
    inObj.tags = action;
    inObj.ses = '';
    _userCommandsArray.push(inObj);
    
    nitroIterateQueue();
  }
}