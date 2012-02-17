window.fbAsyncInit = function() {
	
	FB.init({appId: '192823134073322', status: true, cookie: true, xfbml: true});
     
    FB.Event.subscribe('auth.login', function(response) {
    	// do something with response
		login();
	});
    FB.Event.subscribe('auth.logout', function(response) {
    	// do something with response
        logout();
    });
    FB.getLoginStatus(function(response) {
		if (response.session) {
        	// logged in and connected user, someone you know
			login();
        }
	});
};
            
(function() {
	
	var e = document.createElement('script');
    e.type = 'text/javascript';
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
	
}());



 			
function login(){
	FB.api('/me', function(response) {
		var query = FB.Data.query('SELECT uid, first_name, current_location, pic_square FROM user WHERE uid={0}', response.id);
				
		//Login user information
        query.wait(function(rows) {
		
    	});
		

	});
}
			
//Clear the Login user information when the user logs out
function logout(){
	document.getElementById('fb_button_link').innerHTML = 'Login';
	$("#login_user").fadeOut(500).delay(500);
	document.getElementById('login_user').innerHTML ='';
	$("#welcome_message").fadeIn(500);	
	//document.getElementById('login_user').style.display = "none";
	
}

function facebookLogin() {
	
	FB.login( function(response) {
		if (response.session) {
		  if (response.perms) {
	
			 window.location = window.location;
		  } 
		} 
	}, {perms: 'user_about_me,user_location,email,publish_stream' });
	
}
function facebookLogout() {
	FB.logout(function(response) {
		if (response.session) {
		   window.location = window.location;
		} else { window.location = window.location; }
		window.location = window.location;
	});
}