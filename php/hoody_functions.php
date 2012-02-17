<?php
	// Desc:	index of coefficient = (100 - distance) * 1 + (degrees of separation)^2 *50 + (# of common friends) * 10 + (# of common interests) * 2 + popularity * 2
	// Input:	($uid1, $uid2, $distance, $popularity)
	// Output:	hoody index of coefficient	   
	function hoody_sort($uid1, $uid2, $distance, $popularity) 
	{ 
		// clean up the distance data
		if ($distance == -1)
			$distance = 0;
		else if ($distance > 100)
			$distance = 100;
		
		// Get the numbers of common friends
		$common_friends = count(common_check($uid1, $uid2, 9));
		
		// Get the degrees of separation
		$degrees_of_separation = 0;
		// if uid1 and uid2 are Facebook friends
		if (common_check($uid1, $uid2, 8))
			$degrees_of_separation = 2;
		else if ($common_friends > 0)
			$degrees_of_separation = 1;
		
		// Get the numbers of common interests
		$common_interests 	= count(common_check($uid1, $uid2, 1)) 
							+ count(common_check($uid1, $uid2, 2)) 
							+ count(common_check($uid1, $uid2, 3)) 
							+ count(common_check($uid1, $uid2, 4)) 
							+ count(common_check($uid1, $uid2, 5)) 
							+ count(common_check($uid1, $uid2, 6));
		
		// calculate the index of coefficient
		$result = (100 - $distance) * 1 + $degrees_of_separation * $degrees_of_separation * 50 + $common_friends * 10 + $common_interests * 2 + $popularity * 2;
		
		return $result;
	} 
	
	// Desc:	Check the common interests and friends among two people
	// Input:	$uid1, $uid2, $action
	// Actions:	1 -> activities, 2 - > interests, 3 -> music, 4 -> tv, 5 -> movies, 6 -> books, 8 -> 1st degree friendship, 9 -> common friendlist
	// Output:	Array of intersection
	function common_check($uid1, $uid2, $action) 
	{	
		if($action == 1 || $action == 2 || $action == 3 || $action == 4 || $action == 5 || $action == 6)
		{
			//activities check
			//show user interest list
			$interests_sql = "SELECT User_Interests.interests_id, interest FROM User_Interests, Interests_Lookup 
							WHERE (User_Interests.interests_id=Interests_Lookup.interests_id)&&fb_uid='$uid1'&&category=" . $action;	
			$result = mysql_query($interests_sql) or die (minor_error(184, $user, $user, $today, $interests_sql, mysql_error()));	
			$num = mysql_num_rows($result);
			for ($i=0; $i<$num; $i++)
			{
				$interest=mysql_result($result,$i,"interest");
				$user1_interestslist[] = $interest;
			}	
			//show viewer interest list
			$interests_sql = "SELECT User_Interests.interests_id, interest FROM User_Interests, Interests_Lookup 
							WHERE (User_Interests.interests_id = Interests_Lookup.interests_id)&&fb_uid=" . $uid2 . "&&category=" . $action;	
			$result = mysql_query($interests_sql) or die (minor_error(185, $user, $user, $today, $interests_sql, mysql_error()));	
			$num = mysql_num_rows($result);
			for ($i=0; $i<$num; $i++)
			{
				$interest=mysql_result($result,$i,"interest");
				$user2_interestslist[] = $interest;
			}
			
			$common_interests_list = array_intersect($user1_interestslist, $user2_interestslist);
			return $common_interests_list;
		}
		else if($action == 8)
		{
			//show user friendlist
			$friend_check_sql = "SELECT * FROM Friendlist WHERE uid1='$uid1' && uid2='$uid2'";
			$result = mysql_query($friend_check_sql) or die (minor_error(186, $user, $user, $today, $friend_check_sql, mysql_error()));
			$num = mysql_num_rows($result);
			if ($num > 0)
				return true;
			else
				return false;
		}
		else if($action == 9)
		{
			//show user friendlist
			$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$uid1'";
			$result = mysql_query($friendlist_sql) or die (minor_error(187, $user, $user, $today, $friendlist_sql, mysql_error()));
			$num = mysql_num_rows($result);
			for ($i=0; $i<$num; $i++)
			{
				$friend_uid=mysql_result($result,$i,"uid2");
				$user_friendlist[] = $friend_uid;
			}
			//show viewer friendlist
			$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$uid2'";
			$result = mysql_query($friendlist_sql) or die (minor_error(188, $user, $user, $today, $friendlist_sql, mysql_error()));
			$num = mysql_num_rows($result);
			for ($i=0; $i<$num; $i++)
			{
				$friend_uid=mysql_result($result,$i,"uid2");
				$my_friendlist[] = $friend_uid;
			}
			
			$common_friend_list = array_intersect($my_friendlist, $user_friendlist);
			return $common_friend_list;
		}
	}	
	
	// Desc:	A PHP based function take longitude and latitude data
	//			and calculate the distance between the two points
	// Input:	($lat1, $lng1, $lat2, $lng2)
	// Output:	distance in km	   
	function distance($lat1, $lng1, $lat2, $lng2) 
	{ 
	  $theta = $lng1 - $lng2; 
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
	  $dist = acos($dist); 
	  $dist = rad2deg($dist);
	  $result = $dist * 60 * 1.1515 * 1.609344;
	  $result = number_format($result, 1, '.', '');
	  
	  return $result;
	}
	
	function driving_distance($lat1,$lng1,$lat2,$lng2)
	{
		$start  = urlencode($start);
		$finish = urlencode($finish);
	
		$distance   = 'unknown';

		$url = 'http://maps.google.com/m/directions?dirflg=&saddr='.$lat1.','.$lng1.'&daddr='.$lat2.','.$lng2.'&hl=en&oi=nojs';
		if($data = file_get_contents($url))
		{
			if(preg_match('@<span[^>]+>([^<]+) (mi|km)</span>@smi', $data, $found))
			{
				$distanceNum    = trim($found[1]);
				$distanceUnit   = trim($found[2]);
	
				$distance = number_format($distanceNum, 2);
				if(strcmp($distanceUnit, 'km') == 0)
				{
					$distance = $distanceNum;
				}
			}
			else
			{
				return 'Could not find that route';
			}
			return $distance;
		}
		else
		{
			return 'Could not resolve URL';
		}
	}
	
	// Desc:	A PHP based function take utilize Google Maps API for 
	//			retrieving Longitude and Latitude information from
	//			address	
	// Input:	($street, $city, $state, $country, $area_code)
	// Output:	$output['lng'] = longitude
	//			$output['lat'] = latitude	   
	function geocoding($street,$city,$state,$country,$area_code)
	{
		define("MAPS_HOST", "maps.google.com");
		define("KEY", "ABQIAAAArXO6baEuLJHexd0PRnGkCxROoN0XQGx9kWs_TtpEPHPn1-MT3hTUJBft68uSqie8WGV1_AJLqlVFbg");
		
		// Initialize delay in geocode speed
		$delay = 0;
		$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;
		
		// Iterate through the rows, geocoding each address
		$geocode_pending = true;
		
		while ($geocode_pending) 
		{
			$address = $street . ", " . $city . ", " . $state . ", " . $area_code . ", " . $country;
			$request_url = $base_url . "&q=" . urlencode($address);
			$xml = simplexml_load_file($request_url) or die(minor_error(189, $user, $user, $today, $request_url, mysql_error()));
		
			$status = $xml->Response->Status->code;
			$output = array();
			if (strcmp($status, "200") == 0) 
			{
				// Successful geocode
				$geocode_pending = false;
				$coordinates = $xml->Response->Placemark->Point->coordinates;
				$coordinatesSplit = split(",", $coordinates);
				// Format: Longitude, Latitude, Altitude
				$output["lat"] = mysql_real_escape_string($coordinatesSplit[1]);
				$output["lng"] = mysql_real_escape_string($coordinatesSplit[0]);
				
				return $output;
			} 
			else if (strcmp($status, "620") == 0) 
			{
				// sent geocodes too fast
				$delay += 100000;
			} 
			else 
			{
				// failure to geocode
				$geocode_pending = false;
				//echo "Address " . $address . " failed to geocoded. ";
				//echo "Received status " . $status . "\n";
				echo "There seems to be something wrong with the address you provided, please try again.";
			}
			usleep($delay);
		}
	}
	
	// Desc:	
	// Input:	()
	// Output:		   
	function _make_url_clickable_cb($matches) 
	{
		$ret = '';
		$url = $matches[2];
	 
		if ( empty($url) )
			return $matches[0];
		// removed trailing [.,;:] from URL
		if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($url, -1);
			$url = substr($url, 0, strlen($url)-1);
		}
		return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
	}
 
	function _make_web_ftp_clickable_cb($matches) 
	{
		$ret = '';
		$dest = $matches[2];
		$dest = 'http://' . $dest;
	 
		if ( empty($dest) )
			return $matches[0];
		// removed trailing [,;:] from URL
		if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($dest, -1);
			$dest = substr($dest, 0, strlen($dest)-1);
		}
		return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
	}
 
	function _make_email_clickable_cb($matches) 
	{
		$email = $matches[2] . '@' . $matches[3];
		return $matches[1] . "via the \"Contact Seller\" button";
	}
 
	function make_clickable($ret) 
	{
		$ret = ' ' . $ret;
		// in testing, using arrays here was found to be faster
		$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
	 
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}
	

	// Desc:	convert time from yyyy-mm-dd hh:mm:ss to a much more friendly format
	//			for a timestamp that's:
	//				- more than 2 days ago: yyyy-mm-dd
	//				- 1 - 2 days ago: yesterday
	//				- up to 24 hours ago: hh hours ago
	//				- up to 60mins ago: mm minutes ago
	//				- up to 10min ago: just now
	//
	// Input:	$old_time, $new_time
	// Output:	string of text with the better formatted time	   
	function better_time($old_time, $new_time) 
	{
		$dateArr_old = multiexplode($old_time);
		$dateInt_old = mktime($dateArr_old[3], $dateArr_old[4], $dateArr_old[5], $dateArr_old[1], $dateArr_old[2], $dateArr_old[0]);
		
		$dateArr_new = multiexplode($new_time);
		$dateInt_new = mktime($dateArr_new[3], $dateArr_new[4], $dateArr_new[5], $dateArr_new[1], $dateArr_new[2], $dateArr_new[0]);
		
		$time_difference = $dateInt_new - $dateInt_old;
			
		// up to 10min ago: just now
		if ($time_difference < 600)
		{
			return "just now";
		}
		
		// up to 60mins ago: mm minutes ago
		else if ($time_difference < 3600)
		{
			$minutes = ($time_difference / 60);
			$minutes = floor($minutes);
			return $minutes . " minutes ago";
		}
		
		// up to 24 hours ago: hh hours ago
		else if ($time_difference < 86400)
		{
			$hours = ($time_difference / 3600);
			$hours = floor($hours);
			return $hours . " hours ago";
		}
				
		// 1 - 2 days ago: yesterday
		else if ($time_difference < 172800)
		{
			return "yesterday";
		}
				
		// more than 2 days ago: yyyy-mm-dd
		if ($time_difference > 172800)
		{	
			return $new_time = substr($old_time, 0, -9);
		}
	}
	
	function multiexplode($string)
	{
		$return_array = Array($string); // The array to return
		$d_count = 0;
		$delimiters = Array(",",":","|","-"," ");
		while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
		{
			$new_return_array = Array(); 
			foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
			{
				$put_in_new_return_array = explode($delimiters[$d_count],$el_to_split);
				foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
				{
					$new_return_array[] = $substr;
				}
			}
			$return_array = $new_return_array; // Replace the previous return array by the next version
			$d_count++;
		}
		return $return_array; // Return the exploded elements
	}

	// Desc:	A PHP based function handles the email operation
	// Input:	($message, $subj, $email_to, $email_from,$sender_name) 
	// Output:	email dispatch
	// Reference: http://api.jangomail.com/help/html/655d20ca-2164-c483-fb21-d3d0ee049155.htm	      
	function email($message,$subj,$email_to,$optional_email_to,$email_from,$sender_name) 
	{ 
		$client = new SoapClient('https://api.jangomail.com/api.asmx?WSDL');
		$parameters = array
		(
			'Username' => (string) 'athoody',
			'Password' => (string) 'projecthoodie',
			'FromEmail' => $email_from,
			'FromName' =>  $sender_name,
			'ToEmailAddress' =>  $email_to,
			'Subject' => $subj,
			//'MessagePlain' => $message,
			'MessageHTML' => $message,
			'Options' => (string) 'CC=' . $optional_email_to . ',BCC=receipt@gohoody.com'
		);
	
		//email
		try
		{
			$response = $client->SendTransactionalEmail($parameters);
		}
		catch(SoapFault $e)
		{
			echo $client->__getLastRequest();
		}
	}	
	
	// Desc: Error handling for fatal SQL error	
	// Input:	error_code, facebook login status, facebook uid, time, SQL query, SQL query error
	// Output:	email dispatch, and a page with the error message  
	function fatal_error($error_code, $user, $user, $time, $sql, $sql_error)
	{
		$page = '
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta property="fb:admins" content="28130239" />
					<link rel="icon" 
						  type="image/png" 
						  href="http://img.gohoody.com/attachements/favicon.png" />
					<title>Very Sorry!</title>
					
					<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">
					
					<link rel="stylesheet" href="http://gohoody.com/css/style.css" type="text/css"/>
					<link rel="stylesheet" href="http://gohoody.com/css/about_us.css" type="text/css"/>
					
					</head>
										
					<body>		
						<div id="header_bg"></div>
						<div id="header_bg2"></div>  
						<div id="content">  
						
						  <div id="navigation"> 
							<div id ="logo"><img src="http://img.gohoody.com/attachements/home_page/header/hoodylogo.png" width="250px" alt="Hoody!" /></div>
							<h1 id ="slogan">Find trusted services near you!</h1>
							<div id="icons">
							</div>
						  </div>
						  
						  <h1 class="about_title">Very Sorry</h1>
						  <div id="about_hoody">
							<div class="content_title">But it looks like you have stumble upon a problem (error code: ' . $error_code . ') with the site. But do not get discouraged. This error has been logged, and will be fixed shortly!</div><p></p>    
						  </div>     
						</div>    
						</body>   	
					</html>';
					
		if ($user)
			$status = "logged in";
		else
			$status = "not logged in";
			
		$browser = php_get_browser();

		$email = 	"<p>Error Code: " . $error_code . "</p>
					<p>Facebook Login Status: " . $status . "</p>
					<p>Facebook UID: " . $user . "</p>
					<p>URL: " .  selfURL() . "</p>
					<p>Platform: " . $browser[platform] . "</p>
					<p>Browser: " . $browser[browser] . "</p>
					<p>Version: " . $browser[version] . "</p>
					<p>Time: " . $time . "</p>
					<p>SQL Query: " . $sql . "</p>
					<p>SQL Error: " . $sql_error . "</p>
					<p>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</p>";
					
		email($email, "Hoody SQL error - " . $error_code, "nelson.wu@gohoody.com", "", "info@gohoody.com", "Hoody server");
		return $page;
	}	
	// Desc: Error handling for minor SQL error	
	// Input:	error_code, facebook login status, facebook uid, time, SQL query, SQL query error
	// Output:	email dispatch, and return the error message  
	function minor_error($error_code, $user, $user, $time, $sql, $sql_error)
	{
		$message = 'Very Sorry! But it looks like you have stumble upon a problem (error code: ' 
					. $error_code . ') with the site. But do not get discouraged. This error has been logged, and will be fixed shortly!';
		if ($user)
			$status = "logged in";
		else
			$status = "not logged in";
			
		$browser = php_get_browser();

		$email = 	"<p>Error Code: " . $error_code . "</p>
					<p>Facebook Login Status: " . $status . "</p>
					<p>Facebook UID: " . $user . "</p>
					<p>URL: " .  selfURL() . "</p>
					<p>Platform: " . $browser[platform] . "</p>
					<p>Browser: " . $browser[browser] . "</p>
					<p>Version: " . $browser[version] . "</p>
					<p>Time: " . $time . "</p>
					<p>SQL Query: " . $sql . "</p>
					<p>SQL Error: " . $sql_error . "</p>
					<p>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</p>";
					
		email($email, "Hoody SQL error - " . $error_code, "nelson.wu@gohoody.com", "", "info@gohoody.com", "Hoody server");
		return $message;
	}	
	
	
	// this function get rid of all the special characters and spaces
	function name_cleanup($string)
	{
		// Replace other special chars
		$specialCharacters = array(	'#' => '',
									'$' => '',
									'%' => '',
									'&' => '',
									'@' => '',
									'.' => '',
									'€' => '',
									'+' => '',
									'=' => '',
									'§' => '',
									'\\' => '',
									'/' => '',
									' ' => '', );
		
		while (list($character, $replacement) = each($specialCharacters)) 
			$string = str_replace($character, '-' . $replacement . '-', $string);
		
		$string = strtr($string, "ÀÁÂÃÄÅ? áâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
		
		// Remove all remaining other unknown characters
		$string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);
		$string = preg_replace('/^[\-]+/', '', $string);
		$string = preg_replace('/[\-]+$/', '', $string);
		$string = preg_replace('/[\-]{2,}/', ' ', $string);
		
		$return_array = Array($string); // The array to return
		$d_count = 0;
		$delimiters = Array(",",":","|","-"," ");
		while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
		{
			$new_return_array = Array(); 
			foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
			{
				$put_in_new_return_array = explode($delimiters[$d_count],$el_to_split);
				foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
					$new_return_array[] = $substr;
			}
			$return_array = $new_return_array; // Replace the previous return array by the next version
			$d_count++;
		}
		return implode($return_array); // Return the exploded elements
	}	
	
	// this function returns the current web page URL. Used for part of the error handling
	function selfURL() 
	{
		$s = empty($_SERVER["HTTPS"]) ? ''
			: ($_SERVER["HTTPS"] == "on") ? "s"
			: "";
		$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? ""
			: (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
	}
	function strleft($s1, $s2) 
	{
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	// this function returns the browser information
	function php_get_browser($agent = NULL)
	{ 
		$agent=$agent?$agent:$_SERVER['HTTP_USER_AGENT']; 
		$yu=array(); 
		$q_s=array("#\.#","#\*#","#\?#"); 
		$q_r=array("\.",".*",".?"); 
		$brows=parse_ini_file("php/lite_php_browscap.ini",true); 
		foreach($brows as $k=>$t)
		{ 
		  if(fnmatch($k,$agent))
		  { 
			  $yu['browser_name_pattern']=$k; 
			  $pat=preg_replace($q_s,$q_r,$k); 
			  $yu['browser_name_regex']=strtolower("^$pat$"); 
				foreach($brows as $g=>$r)
				{ 
				  if($t['Parent']==$g)
				  { 
					foreach($brows as $a=>$b)
					{ 
					  if($r['Parent']==$a)
					  { 
						$yu=array_merge($yu,$b,$r,$t); 
						foreach($yu as $d=>$z)
						{ 
						  $l=strtolower($d); 
						  $hu[$l]=$z; 
						} 
					  } 
					} 
				  } 
				} 
				break; 
		  } 
		} 
		return $hu; 
	} 
	
	function curPageURL() 
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") 
			$pageURL .= "s";
		
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		return $pageURL;
	}
?>