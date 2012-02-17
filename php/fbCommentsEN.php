<?php
	//Connect to Hoody MySQL database
	include "misc.inc";
	include "hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	$url = explode('/', $_POST['url']);

	if ($url[3] == "ask")
	{
		// Extract category info from Category_Lookup table
		$url_lookup_sql = "SELECT * FROM Category_Lookup WHERE category_url='" . $url[4] . "'";
		$result = mysql_query($url_lookup_sql) or die (fatal_error(1, $user, $user, $today, $name_lookup_sql, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$category_id = $row['category_id'];
	
		$request_url ="https://graph.facebook.com/comments/?ids=http://gohoody.com/ask/" . $url[4] . "/";
	
		$requests = mysql_real_escape_string(file_get_contents($request_url));
		
		$query = "UPDATE Comments SET comments='$requests' WHERE category_id='$category_id'";
		$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));
		
		$sendto = "nelson.wu@gohoody.com";
		$optional_sendto = "mike.tang@gohoody.com";
	}
	else if ($url[3] == "service")
	{	
		$request_url ="https://graph.facebook.com/comments/?ids=http://gohoody.com/service/" . $url[4] . "/";
	
		$requests = mysql_real_escape_string(file_get_contents($request_url));
		
		$category_id = -$url[4];
		$lid = $url[4];
		
		$query = "SELECT * FROM Comments WHERE category_id=" . $category_id;
		$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));
		$row_num = mysql_num_rows($result);
		if (!$row_num)
		{
			$query = "INSERT INTO Comments(category_id,comments) VALUES('$category_id','$requests')";
			$result = mysql_query($query) or die (fatal_error(10, $user, $user, $today, $query, mysql_error()));
		}
		else
		{
			$query = "UPDATE Comments SET comments='$requests' WHERE category_id=" . $category_id;
			$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));
		}

		$query = "SELECT fb_uid FROM Listing_Overview WHERE listing_id=$lid";
		$result = mysql_query($query) or die (fatal_error(148, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$fb_uid = $row['fb_uid'];

		// Extract seller info from Basic_User_Information table
		$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
		$result = mysql_query($service_sql) or die (fatal_error(149, $user, $user, $today, $service_sql, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$sendto = $row['email'];
		$optional_sendto = $row['optional_email'];
	}
	

	// Fire off the notification email
	$client = new SoapClient('https://api.jangomail.com/api.asmx?WSDL');
	
	$parameters = array
	(
		'Username' => (string) 'athoody',
		'Password' => (string) 'projecthoodie',
		'FromEmail' => 'info@gohoody.com',
		'FromName' =>  'Hoody',
		'ToEmailAddress' => $sendto,
		'Subject' => $_POST['subject'],
		'MessageHTML' => $_POST['message'],
		'Options' => (string) 'CC=' . $optional_sendto
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
?>	
