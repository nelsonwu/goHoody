<?php  
	// Program: upload_process.php
	//
	
	// make a note of the location of the upload form in case we need it
	//$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'create_listing.php';
	$uploadForm = 'internal_profile.php';
	
	// name of the fieldname used for the file in the HTML form
	$fieldname = 'file';
	
	//name of the current page
	$page_title = "Upload Process";
	
	//Connect to @Hoody MySQL database
	include_once "php/misc.inc";
	include "php/hoody_functions.php";
	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	if (@$_POST['newbutton'] == "Cancel")
		header("Location: " . $working_directory . "dashboard/");
				
	$today = date("Y-m-d H:i:s"); 
	
	// grab data
	$user_name = mysql_real_escape_string(strip_tags(trim($_POST['user_name'])));
	$user_first_name = mysql_real_escape_string(strip_tags(trim($_POST['user_first_name'])));
	$user_id = mysql_real_escape_string(strip_tags(trim($_POST['user_id'])));
	$email = mysql_real_escape_string(strip_tags(trim($_POST['email'])));	
	$about_user = mysql_real_escape_string(strip_tags(trim($_POST['about_user'])));
	$country = mysql_real_escape_string(strip_tags(trim($_POST['country'])));
  	$street = mysql_real_escape_string(strip_tags(trim($_POST['street'])));
  	$city = mysql_real_escape_string(strip_tags(trim($_POST['city'])));
  	$state = mysql_real_escape_string(strip_tags(trim($_POST['state'])));
  	$postal_code = mysql_real_escape_string(strip_tags(trim($_POST['postal_code'])));
	
	if (!$about_user)
		$about_user = "Hello, I am " . $user_first_name . "!";
			
	$basic_query = "INSERT INTO Basic_User_Information(fb_uid, email, name, first_name, date_registered, about_me, pic_square, pic_big) 
					VALUES('$user_id', '$email', '$user_name', '$user_first_name', '$today', '$about_user', 'http://athoody.com/service_pictures/temp_user/temp_user_logo_thumb.png', 'http://athoody.com/service_pictures/temp_user/temp_user_logo_big.png')";
	$result = mysql_query($basic_query) or die (fatal_error(276, $user, $user, $today, $basic_query, mysql_error()));
	 
	$user_lookup_sql = "INSERT INTO User_Lookup(fb_uid,profile_name) VALUES('$user_id','$user_id')";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(277, $user, $user, $today, $user_lookup_sql, mysql_error()));	

	$lnglat = geocoding($street,$city,$state,$country,$postal_code);
	$lng = $lnglat["lng"];
	$lat = $lnglat["lat"];
	$address_query = "INSERT INTO User_Address(fb_uid,lng,lat) VALUES('$user_id','$lng','$lat')";		
	$result = mysql_query($address_query) or die (fatal_error(278, $user, $user, $today, $address_query, mysql_error()));
	
	// make a note of the location of the success page
	header('Location: profile/' . $user_id . '/');
?>
