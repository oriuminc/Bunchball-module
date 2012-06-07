// JavaScript closure. Defined jQuery $.
(function($) {
  Drupal.behaviors.bunchball = {
    attach: function (context, settings) {
      if (typeof nitro !== "undefined") {
        nitro.getUserId(gotCurrentUserId);
        nitro.showPendingNotifications();
      }
    }
  };

}) (jQuery);

var _currentUserId = '';
var _userCommandsArray = new Array();
var _thePlayer = '';

// callback function for acquiring the User ID of the current user...
function gotCurrentUserId(inUserId) {
  _currentUserId = inUserId;
  
  // user viewed content...
  if (typeof Drupal.settings.bunchball_nitro.node_id != 'undefined') {
    // we are in a node... 
    userViewedContent();
  }

  // TODO: bind the actions to functions below
  (function ($) {
//    #LikePluginPagelet a
//    iframe.twitter-share-button a
//    #plusone span#button
//    span.tumblr a

    // Facebook
//    $('#LikePluginPagelet a').click(function(){
//      alert('Facebook');
//      return true;
//    });
    
    // Twitter
//    $('iframe.twitter-share-button a').click(function(){
//      alert('twitter');
//      return true;
//    });
    
    // Pinterest
    //$('').click(function(){});
    
    // Google+
//    $('span.gplus div#___plusone_0').click(function(){
//      alert('Google Plus');
//      return true;
//    });
//    $('span.gplus div#___plusone_0').bind("click", function(e){
//      alert('Google Plus');
//      return true;
//    });
//    $('span.gplus iframe').contents().find("#button").bind("click", function(e){
//      alert('Google Plus');
//      return true;
//    });
    
    // Tumblr
    $('span.tumblr a').click(function(){
      nitroSocialShareClicked("Tumblr");
      return true;
    });
    
  }) (jQuery);
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
    action = "Video_Watch_Finish";
  } else if (newState == 1) { // playing
    action = "Video_Watch_Start";
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