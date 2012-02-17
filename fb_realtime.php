<?php                                    
/**
 * This is sample subscription endpoint for using Facebook real-time update
 * See http://developers.facebook.com/docs/api/realtime to additional
 * documentation
 */
 
	//Connect to @Hoody MySQL database
	include "php/misc.inc";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";

// Please make sure to REPLACE the value of VERIFY_TOKEN 'abc' with 
// your own secret string. This is the value to pass to Facebook 
//  when add/modify this subscription.
$method = $_SERVER['REQUEST_METHOD']; 

$access_token = "192823134073322|MhZpmMoo_bikrMpWuxoVfyfn7vE";
$callback_url = "http://gohoody.com/development/fb_realtime.php"; 
$verify_token = "abc";
$appid = "192823134073322";
$user_uid = "516927476";                           
  
   
   
// In PHP, dots and spaces in query parameter names are converted to 
// underscores automatically. So we need to check "hub_mode" instead
//  of "hub.mode".                                                      
if ($method == 'GET' && $_GET['hub_mode'] == 'subscribe' && $_GET['hub_verify_token'] == $verify_token) {
  echo $_GET['hub_challenge'];
  exit;
  
} else if ($method == 'POST') {                                   
  $updates = json_decode(file_get_contents("php://input"), true); 
  // Replace with your own code here to handle the update 
  // Note the request must complete within 15 seconds.
  // Otherwise Facebook server will consider it a timeout and 
  // resend the push notification again.
  
  $update = implode("/", $update);
  
	
  
  
  
  error_log('updates = ' . print_r($updates, true));              
}

else {
	
	$param = array('access_token' => $access_token,
                'object' => 'user',
                'fields' => 'pic,music,movies',
                'callback_url' => $callback_url,
                'verify_token' => $verify_token
                );
$subs = $facebook->api('/'.$appid.'/subscriptions', 'POST', $param);  

//$param = array('access_token' => $access_token);
//$subs = $facebook->api('/'.$appid.'/subscriptions', $param);

print_r($subs);

}


//https://graph.facebook.com/192823134073322/subscriptions?access_token=192823134073322|MhZpmMoo_bikrMpWuxoVfyfn7vE
//https://graph.facebook.com/oauth/access_token?client_id=192823134073322&client_secret=cadcff0ca1411b30f975499ba148b8d3&grant_type=client_credentials
//https://graph.facebook.com/%3Cappid%3E/subscriptions?access_token=%3Caccess

?>