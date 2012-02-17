window.fbAsyncInit = function() {
   FB.init({
    appId  : '192823134073322',
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true, // parse XFBML
    channelURL : 'http://WWW.MYDOMAIN.COM/channel.html', // channel.html file
    oauth  : true, // enable OAuth 2.0
	"reloadIfSessionStateChanged" : true
  });
  
  FB.Event.subscribe('auth.login', function(response) {
    	// do something with response
	login();	
  });

  FB.getLoginStatus(function(response) {
	if (response.session) {
        	// logged in and connected user, someone you know
	login();
    }
  });
};
(function(d){
   var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   d.getElementsByTagName('head')[0].appendChild(js);
}(document));



function login(){
	FB.api('/me', function(response) {
		var query = FB.Data.query('SELECT uid, first_name, current_location, pic_square FROM user WHERE uid={0}', response.id);
				
		//Login user information
        query.wait(function(rows) {
    	});
	});
}

function facebookLogin() {
	
	
	
	FB.login(function(response) {
	   if (response.authResponse) {
		 
		 FB.api('/me', function(response) {
		   window.location.reload();
  
		 });
	   } else {
		 
	   }
	 }, {scope: 'user_about_me,user_location,email,publish_stream'});
	 
}


function facebookLogout() {
	FB.logout(function(response) {
		window.location.reload();
	});
}

