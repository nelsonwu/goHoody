<?php
	// Program: edit.php
						
	//Connect to @Hoody MySQL database
	include "misc.inc";
	include "hoody_functions.php";	
	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Capture data from page
	$value = $_POST['value'];
	$id_string = explode("_", $_POST['id']);
	$action_id = $id_string[0];
	$id = $id_string[1];
		
	// sanitizing input
	$value = mysql_real_escape_string(strip_tags(trim($value)));
	
	switch ($action_id) {
		//Update service title    
		case 1:
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			$query = "UPDATE Listing_Overview SET title='$value' WHERE listing_id='$id'";
			$result = mysql_query($query) or die (minor_error(190, $fbme, $uid, $today, $query, mysql_error()));
			echo $value;
			break;
	
		//Update service description
		case 2:
			$query = "UPDATE Listing_Overview SET listing_description='$value' WHERE listing_id='$id'";
			$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));			
			echo $_POST['value'];
			break;

		//Update price
		case 3:
			$value = filter_var($value, FILTER_VALIDATE_INT);
			$query = "UPDATE Listing_Overview SET price='$value' WHERE listing_id='$id'";
			$result = mysql_query($query) or die (minor_error(192, $fbme, $uid, $today, $query, mysql_error()));
			echo $value;
			break;
			
		//Update pricing model
		case 4:
			if ($value == "per job")
			{
				$value = 0;
				$query = "UPDATE Listing_Overview SET pricing_model='$value' WHERE listing_id='$id'";			
			}
			else if ($value == 'per hour')
			{	
				$value = 1;
				$query = "UPDATE Listing_Overview SET pricing_model='$value' WHERE listing_id='$id'";			
			}
			else if ($value == "free")
			{
				$value = 8;
				$query = "UPDATE Listing_Overview SET price='0' WHERE listing_id='$id'";		
			}
			$result = mysql_query($query) or die (minor_error(193, $fbme, $uid, $today, $query, mysql_error()));
			echo $_POST['value'];			
			break;
	
		//Update review
		case 5:
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			$query = "UPDATE Confirmed_Transactions SET review='$content' WHERE transaction_id='$id'";
			$result = mysql_query($query) or die (minor_error(194, $fbme, $uid, $today, $query, mysql_error()));
			echo $content;
			break;

		//Update About
		case 6:
			$value = filter_var($value, FILTER_SANITIZE_STRING);			
			$query = "UPDATE Basic_User_Information SET about_me='$value' WHERE fb_uid='$id'";
			$result = mysql_query($query) or die (minor_error(195, $fbme, $uid, $today, $query, mysql_error()));
			echo $value;
			break;
						
		//Update optional email address
			case 7:
				$value = filter_var($value, FILTER_SANITIZE_EMAIL);
				if(filter_var($value, FILTER_VALIDATE_EMAIL) || $value == "")
				{
					$query = "UPDATE Basic_User_Information SET optional_email='$value' WHERE fb_uid='$id'";
					$result = mysql_query($query) or die (minor_error(196, $fbme, $uid, $today, $query, mysql_error()));
					echo $value;
					break;
				}
				else
				{
					echo "E-mail is not valid";
					break;
				}
			
		//Update linkedin profile
		case 8:
			$value = filter_var($value, FILTER_SANITIZE_URL);
			if(filter_var($value, FILTER_VALIDATE_URL) || $value == "")
			{
				$query = "UPDATE Basic_User_Information SET linkedin_profile='$value' WHERE fb_uid='$id'";
				$result = mysql_query($query) or die (minor_error(197, $fbme, $uid, $today, $query, mysql_error()));
				echo $value;
				break;	
			}
			else
			{
				echo "URL is not valid";
				break;
			}	

		//Update pricing
		case 9:
			echo $value;
			break;	
		
		//Update address - country	
		case 10:
			$query = "UPDATE User_Address SET country='$value' WHERE fb_uid='$id'";
			$result = mysql_query($query) or die (minor_error(198, $fbme, $uid, $today, $query, mysql_error()));
			echo $value;
			break;			
		
		//Update address - area code
		case 11:
			$value = filter_var($value, FILTER_SANITIZE_STRING);	
					
			$address_sql = "SELECT city,state,country,area_code,street FROM User_Address WHERE fb_uid='$id'";
			$result = mysql_query($address_sql) or die (minor_error(199, $fbme, $uid, $today, $address_sql, mysql_error()));
			$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
			$database_country = $row1['country'];
			
			$lnglat = geocoding(NULL,NULL,NULL,$database_country,$value);
			$lng = $lnglat["lng"];
			$lat = $lnglat["lat"];
								
			$address_sql = "UPDATE User_Address SET street='',state='',city='',area_code='$value',lng='$lng',lat='$lat' WHERE fb_uid='$id'";
			$address_result = mysql_query($address_sql) or die (minor_error(200, $fbme, $uid, $today, $address_sql, mysql_error()));
			echo $value;
			break;			
	
		//cases for flyer.php updates - description
		case 12:					
			$query = "UPDATE Listing_Overview SET flyer_description='" . $_POST['value'] . "' WHERE listing_id='$id'";
			$result = mysql_query($query) or die (minor_error(201, $fbme, $uid, $today, $query, mysql_error()));			
			echo $_POST['value'];
			break;			
			
		//cases for flyer.php updates - about me			
		case 13:		
			$query = "UPDATE Basic_User_Information SET flyer_about_me='" . $_POST['value'] . "' WHERE fb_uid='$id'";
			$result = mysql_query($query) or die (minor_error(202, $fbme, $uid, $today, $query, mysql_error()));
			echo $_POST['value'];
			break;								
	}	
 ?>