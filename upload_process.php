<?php  
	// Program: upload_process.php
	//
	
	// make a note of the current working directory, relative to root.
	$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
	
	// make a note of the directory that will recieve the uploaded files
	$uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . '/service_pictures/';
	
	// make a note of the location of the upload form in case we need it
	//$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'create_listing.php';
	$uploadForm = 'create.php';
	
	// name of the fieldname used for the file in the HTML form
	$fieldname = 'file';
	
	//name of the current page
	$page_title = "Upload Process";
	
	//set variable for updating attached pictures
	$listing_exist = 0;
	
	//Connect to @Hoody MySQL database
	include_once "php/misc.inc";
	include "php/hoody_functions.php";
	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	if (@$_POST['newbutton'] == "Cancel")
		header("Location: " . $working_directory . "dashboard/");
		
	$today = date("Y-m-d H:i:s"); 
		
	// grab data from create_listing.php
	$user = $_POST['uid'];
	$title = $_POST['title'];
	$listing_description = $_POST['listing_description'];
  	$price = $_POST['price'];
  	$picture_id_1 = $_POST['picture_id_1'];
  	$picture_id_2 = $_POST['picture_id_2'];
  	$picture_id_3 = $_POST['picture_id_3'];
  	$picture_id_4 = $_POST['picture_id_4'];
  	$picture_id_5 = $_POST['picture_id_5'];
	$picture_orders = $_POST['picture_orders'];
  	$location1 = $_POST['location1'];
  	$location2 = $_POST['location2'];
	$range = $_POST['range'];
  	$street = $_POST['street'];
  	$city = $_POST['city'];
	$show = $_POST['show'];
	$show = (int)$show;
	$keep_pictures = $_POST['keep_pictures'];
	$keep_pictures = (int)$keep_pictures;
	$pricing_model = $_POST['pricing_model'];
	$pricing_model = (int)$pricing_model;
	if ($pricing_model == 9)
	{
		$price = 0;
		$pricing_model = 0;
	}
	$upload_pictures = array_filter(explode("<br/>", $_POST['uploaded_files']));
	
	//Clean the data
	$title = strip_tags(trim($title));
	$listing_description = strip_tags(trim($listing_description));
	$price = strip_tags(trim($price));
	$picture_id_1 = strip_tags(trim($picture_id_1));
	$picture_id_2 = strip_tags(trim($picture_id_2));
	$picture_id_3 = strip_tags(trim($picture_id_3));
	$picture_id_4 = strip_tags(trim($picture_id_4));
	$picture_id_5 = strip_tags(trim($picture_id_5));
	$home_range = strip_tags(trim($home_range));
	$other_range = strip_tags(trim($other_range));
	$street = strip_tags(trim($street));
	$city = strip_tags(trim($city));
	
	// mysql_real_escape_string implementation to prevent SQL injection attack
	$title = mysql_real_escape_string($title);
	$listing_description = mysql_real_escape_string($listing_description);
	$price = mysql_real_escape_string($price);
	$picture_id_1 = mysql_real_escape_string($picture_id_1);
	$picture_id_2 = mysql_real_escape_string($picture_id_2);
	$picture_id_3 = mysql_real_escape_string($picture_id_3);
	$picture_id_4 = mysql_real_escape_string($picture_id_4);
	$picture_id_5 = mysql_real_escape_string($picture_id_5);
	$location1 = mysql_real_escape_string($location1);
	$location2 = mysql_real_escape_string($location2);
	$range = mysql_real_escape_string($range);
	$street = mysql_real_escape_string($street);
	$city = mysql_real_escape_string($city);	

if (isset($_GET['lid'])) 
{	
	//Typecast it to an integer:
	$lid = (int) $_GET['lid'];
	//An invalid $_GET['lid'] value would be typecast to 0
	
	// Now let's deal with the uploaded files

	// possible PHP upload errors
	$errors = array(1 => 'php.ini max file size exceeded', 
					2 => 'html form max file size exceeded', 
					3 => 'file upload was only partial', 
					4 => 'no file was attached');
	
	// check the upload form was actually submitted else print form
	isset($_POST['submit'])
		or error('the upload form is neaded', $uploadForm);
		
	// check if any files were uploaded and if 
	// so store the active $_FILES array keys
	$active_keys = array();
	foreach($_FILES[$fieldname]['name'] as $key => $filename)
	{
		if(!empty($filename))
			$active_keys[] = $key;
	}
	
	// check at least one file was uploaded
	//count($active_keys) or error('No files were uploaded', $uploadForm);
			
	// check for standard uploading errors
	foreach($active_keys as $key)
	{
		($_FILES[$fieldname]['error'][$key] == 0)
			or error($_FILES[$fieldname]['tmp_name'][$key].': '.$errors[$_FILES[$fieldname]['error'][$key]], $uploadForm);
	}
		
	// check that the file we are working on really was an HTTP upload
	foreach($active_keys as $key)
	{
		@is_uploaded_file($_FILES[$fieldname]['tmp_name'][$key])
			or error($_FILES[$fieldname]['tmp_name'][$key].' not an HTTP upload', $uploadForm);
	}
		
	// validation... since this is an image upload script we 
	// should run a check to make sure the upload is an image
	foreach($active_keys as $key)
	{
		$file = $_FILES[$fieldname]['name'][$key];
		$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $file);
		if ($ext != "gif" && $ext != "GIF" && $ext != "JPEG" && $ext != "JPG" && $ext != "jpeg" && $ext != "jpg" && $ext != "PNG" && $ext != "png")
			error('one or more uploaded files were not of acceptable image format', $uploadForm);
		@getimagesize($_FILES[$fieldname]['tmp_name'][$key])
			or error('one or more uploaded files were not of acceptable images', $uploadForm);
	}
		
	//array for storing the upload picture index
	$picture_index = array();
	$picture_count = 0;
	
	// make a unique filename for the uploaded file and check it is 
	// not taken... if it is keep trying until we find a vacant one
	foreach($active_keys as $key)
	{
		$picture_count++;
		$now = time();
		$upload_filename = '';
		while(file_exists($uploadFilename[$key] = $uploadsDirectory.$now.'-'.$_FILES[$fieldname]['name'][$key]))
		{
			$now++;
		}
		$upload_filename = $now.'-'.$_FILES[$fieldname]['name'][$key];
		//Insert data into the Listing_Pictures table
		$query = "INSERT INTO Pictures_Lookup (URL) VALUES('$upload_filename')";
		$result = mysql_query($query) or die (fatal_error(123, $user, $user, $today, $query, mysql_error()));
		$picture_index[] = mysql_insert_id();	
	}
	
	// now let's move the file to its final and allocate it with the new filename
	foreach($active_keys as $key)
	{
		@move_uploaded_file($_FILES[$fieldname]['tmp_name'][$key], $uploadFilename[$key])
			or error('receiving directory insuffiecient permission', $uploadForm);
	}
	
	//$lid must have a valid value
	if ($lid > 0) 
	{
		//Get the information from the database for this service:
		$query = "SELECT status FROM Listing_Overview WHERE listing_id=$lid&&fb_uid=$user";
		$result = mysql_query($query) or die (fatal_error(112, $user, $user, $today, $query, mysql_error()));
		$num = mysql_num_rows($result);
		
		//service listing name not found
		if ($num == 0) 
			header("Location: lost.php");
		
		//Update data in Listing_Overview table
		$query = "UPDATE Listing_Overview SET title='$title',price='$price',pricing_model='$pricing_model',listing_description='$listing_description',status=1 WHERE listing_id='$lid'";
		$result = mysql_query($query) or die (fatal_error(113, $user, $user, $today, $query, mysql_error()));

		//make a note of the location of the success page
		$uploadSuccess = 'service/' . $lid . '/';			
		
		//set variable for updating attached pictures
		$listing_exist = 1;
		
		// Adds feature to show/hide listing address in the listing description
		// INSERT location information into the database
		if ($location1 == "at_home")
		{
			$query = "UPDATE Listing_Location SET listing_location=0,show_address='$show' WHERE listing_id='$lid'";
			$result = mysql_query($query) or die (fatal_error(114, $user, $user, $today, $query, mysql_error()));
		} // End of if ($location1 = "at_home")
		else if ($location1 == "away_home")
		{
			if ($location2 == "buyer_home")
			{
				$query = "UPDATE Listing_Location SET listing_location=1,listing_range='$range' WHERE listing_id='$lid'";
				$result = mysql_query($query) or die (fatal_error(115, $user, $user, $today, $query, mysql_error()));
			}
			else if ($location2 == "other")
			{
				$sql = "SELECT country,state FROM User_Address WHERE fb_uid='$user'";
				$result = mysql_query($sql) or die (fatal_error(116, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
				extract($service_row);	
				
				// Adding lng/lat component via Google geocoding API
				$lnglat = geocoding($street,$city,$state,$country);
				$lng = $lnglat["lng"];
				$lat = $lnglat["lat"];
	
				$query = "UPDATE Listing_Location SET listing_location=2,country='$country',state='$state',city='$city',street='$street',show_address='$show',lng='$lng',lat='$lat' WHERE listing_id='$lid'";
				$result = mysql_query($query) or die (fatal_error(117, $user, $user, $today, $query, mysql_error()));

			}

		} // End of else if ($location1 == "away_home")
		
		else if ($location1 == "virtual")
		{
			$query = "UPDATE Listing_Location SET listing_location=3 WHERE listing_id='$lid'";
			$result = mysql_query($query) or die (fatal_error(115, $user, $user, $today, $query, mysql_error()));
		} // End of else if ($location2 == "virtual")		
		

		
		
		
		
		
		
		
		
		// If users don't want to keep pictures, refresh all the pictures from the database
		if ($keep_pictures == 0 && $picture_count != 0)
		{
			// update data into the Listing_Pictures table
			$query = "UPDATE Listing_Pictures SET picture_id_1='$picture_index[0]',picture_id_2='$picture_index[1]',picture_id_3='$picture_index[2]'
			,picture_id_4='$picture_index[3]',picture_id_5='$picture_index[4]',picture_count='$picture_count' WHERE listing_id='$lid'";
			$result = mysql_query($query) or die (fatal_error(124, $user, $user, $today, $query, mysql_error()));
		}	
		else if ($picture_count != 0)
		{
			$counter1 = 0;
			$counter2 = 1;
			while ($counter2 <= 5)
			{
				if ($counter2 <= $keep_pictures)
				{
					$counter2++;
					continue;
				}
				else
				{
					$query = "UPDATE Listing_Pictures SET picture_id_" . $counter2 . "='$picture_index[$counter1]' WHERE listing_id='$lid'";
					$result = mysql_query($query) or die (fatal_error(125, $user, $user, $today, $query, mysql_error()));
					$counter1++;
					$counter2++;
				}	
			}
			$picture_count = $picture_count + $keep_pictures;
			if ($picture_count > 5)
			{
				$picture_count = 5;
			}
			// update data into the Listing_Pictures table
			$query = "UPDATE Listing_Pictures SET picture_count='$picture_count' WHERE listing_id='$lid'";
			$result = mysql_query($query) or die (fatal_error(126, $user, $user, $today, $query, mysql_error()));
		}	
	} // End of if ($lid > 0)
} // End of if (isset($_GET['lid']))
	
else
{	
	// Now let's deal with the uploaded files
	if(empty($upload_pictures))
	{
		// possible PHP upload errors
		$errors = array(1 => 'php.ini max file size exceeded', 
						2 => 'html form max file size exceeded', 
						3 => 'file upload was only partial', 
						4 => 'no file was attached');
		
		// check the upload form was actually submitted else print form
		isset($_POST['submit'])
			or error('the upload form is neaded', $uploadForm);
			
		// check if any files were uploaded and if 
		// so store the active $_FILES array keys
		$active_keys = array();
		foreach($_FILES[$fieldname]['name'] as $key => $filename)
		{
			if(!empty($filename))
				$active_keys[] = $key;
		}
		
		// check at least one file was uploaded
		//count($active_keys) or error('No files were uploaded', $uploadForm);
				
		// check for standard uploading errors
		foreach($active_keys as $key)
		{
			($_FILES[$fieldname]['error'][$key] == 0)
				or error($_FILES[$fieldname]['tmp_name'][$key].': '.$errors[$_FILES[$fieldname]['error'][$key]], $uploadForm);
		}
			
		// check that the file we are working on really was an HTTP upload
		foreach($active_keys as $key)
		{
			@is_uploaded_file($_FILES[$fieldname]['tmp_name'][$key])
				or error($_FILES[$fieldname]['tmp_name'][$key].' not an HTTP upload', $uploadForm);
		}
			
		// validation... since this is an image upload script we 
		// should run a check to make sure the upload is an image
		foreach($active_keys as $key)
		{
			$file = $_FILES[$fieldname]['name'][$key];
			$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $file);
			if ($ext != "gif" && $ext != "GIF" && $ext != "JPEG" && $ext != "JPG" && $ext != "jpeg" && $ext != "jpg" && $ext != "PNG" && $ext != "png")
				error('one or more uploaded files were not of acceptable image format', $uploadForm);
			@getimagesize($_FILES[$fieldname]['tmp_name'][$key])
				or error('one or more uploaded files were not of acceptable images', $uploadForm);
		}
			
		//array for storing the upload picture index
		$picture_index = array();
		$picture_count = 0;
		
		// make a unique filename for the uploaded file and check it is 
		// not taken... if it is keep trying until we find a vacant one
		foreach($active_keys as $key)
		{
			$picture_count++;
			$now = time();
			$upload_filename = '';
			while(file_exists($uploadFilename[$key] = $uploadsDirectory.$now.'-'.$_FILES[$fieldname]['name'][$key]))
			{
				$now++;
			}
			$upload_filename = $now.'-'.$_FILES[$fieldname]['name'][$key];
			//Insert data into the Listing_Pictures table
			$query = "INSERT INTO Pictures_Lookup (URL) VALUES('$upload_filename')";
			$result = mysql_query($query) or die (fatal_error(123, $user, $user, $today, $query, mysql_error()));
			$picture_index[] = mysql_insert_id();	
		}
		
		// now let's move the file to its final and allocate it with the new filename
		foreach($active_keys as $key)
		{
			@move_uploaded_file($_FILES[$fieldname]['tmp_name'][$key], $uploadFilename[$key])
				or error('receiving directory insuffiecient permission', $uploadForm);
		}
	}
	
	
	
	//Insert data into Listing_Overview table
	//note: listing_id is auto_incrementally generated from this table
	// if user is a registered member
	if($user > 0)
		$query = 	"INSERT INTO Listing_Overview(fb_uid,title,price,pricing_model,listed_time,listing_description,status) 
					VALUES('$user','$title','$price','$pricing_model','$today','$listing_description',1)";
	// if user doesn't yet have an account on Hoody
	else
		$query = 	"INSERT INTO Listing_Overview(fb_uid,title,price,pricing_model,listed_time,listing_description,status) 
					VALUES('$user','$title','$price','$pricing_model','$today','$listing_description',3)";
					
	$result = mysql_query($query) or die (fatal_error(118, $user, $user, $today, $query, mysql_error()));
	$listing_id = mysql_insert_id();

	// make a note of the location of the success page
	$uploadSuccess = 'service/' . $listing_id . '/';

	// Adds feature to show/hide listing address in the listing description
	// INSERT location information into the database
	// seller's home -> 0 , buyer's home -> 1, other -> 2
	if ($location1 == "at_home")
	{
		$query = "INSERT INTO Listing_Location(listing_id,listing_location,show_address) VALUES('$listing_id',0,'$show')";
		$result = mysql_query($query) or die (fatal_error(119, $user, $user, $today, $query, mysql_error()));
	} // End of if ($location1 = "at_home")
	else if ($location1 == "away_home")
	{
		if ($location2 == "buyer_home")
		{
			$query = "INSERT INTO Listing_Location(listing_id,listing_location,listing_range) VALUES('$listing_id',1,'$range')";
			$result = mysql_query($query) or die (fatal_error(120, $user, $user, $today, $query, mysql_error()));
		}
		else if ($location2 == "other")
		{
			$sql = "SELECT country,state FROM User_Address WHERE fb_uid='$user'";
			$result = mysql_query($sql) or die (fatal_error(121, $user, $user, $today, $sql, mysql_error()));
			$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($service_row);	

			// Adding lng/lat component via Google geocoding API
			$lnglat = geocoding($street,$city,$state,$country);
			$lng = $lnglat["lng"];
			$lat = $lnglat["lat"];
				
			$query =	"INSERT INTO Listing_Location(listing_id,listing_location,country,state,city,street,show_address,lng,lat)
						VALUES('$listing_id',2,'$country','$state','$city','$street','$show','$lng','$lat')";
			$result = mysql_query($query) or die (fatal_error(122, $user, $user, $today, $query, mysql_error()));
		}
	} // End of else if ($location1 = "away_home")
	
	else if ($location1 == "virtual")
	{
		$query = "INSERT INTO Listing_Location(listing_id,listing_location) VALUES('$listing_id',3)";
		$result = mysql_query($query) or die (fatal_error(115, $user, $user, $today, $query, mysql_error()));
	} // End of else if ($location2 == "virtual")		
	
	// legacy picture upload
	if(empty($upload_pictures))
	{
		if ($picture_count == 0)
		{
			$picture_index[0] = 136;
			// Insert data into the Listing_Pictures table
			$query = "INSERT INTO Listing_Pictures(listing_id,picture_id_1,picture_count) VALUES('$listing_id','$picture_index[0]',1)";
			$result = mysql_query($query) or die (fatal_error(127, $user, $user, $today, $query, mysql_error()));
		}
		else
		{
			// Insert data into the Listing_Pictures table
			$query = 	"INSERT INTO Listing_Pictures(listing_id,picture_id_1,picture_id_2,picture_id_3,picture_id_4,picture_id_5,picture_count)
						VALUES('$listing_id','" . $picture_index[0] . "','" . $picture_index[1] . "','" . $picture_index[2] . "','" . $picture_index[3] . "','" . $picture_index[4] . "','$picture_count')";
			$result = mysql_query($query) or die (fatal_error(128, $user, $user, $today, $query, mysql_error()));
		}
	}
	// fancy picture upload
	else if ($picture_orders)
	{
		$picture_orders = explode(",", $picture_orders);
		$pictures_count = count($picture_orders);
		// Insert data into the Listing_Pictures table
		$query = 	"INSERT INTO Listing_Pictures(listing_id,picture_id_1,picture_id_2,picture_id_3,picture_id_4,picture_id_5,picture_count)
					VALUES('$listing_id','" . $picture_orders[0] . "','" . $picture_orders[1] . "','" . $picture_orders[2] . "','" . $picture_orders[3] . "','" . $picture_orders[4] . "','" . $pictures_count . "')";
		$result = mysql_query($query) or die (fatal_error(128, $user, $user, $today, $query, mysql_error()));
		$picture_index[0] = $upload_pictures[1];
	}
	else if (count($upload_pictures) > 5)
	{
		$pictures_count = count($upload_pictures);
		// Insert data into the Listing_Pictures table
		$query = 	"INSERT INTO Listing_Pictures(listing_id,picture_id_1,picture_id_2,picture_id_3,picture_id_4,picture_id_5,picture_count)
					VALUES('$listing_id','" . $upload_pictures[$pictures_count - 4] . "','" . $upload_pictures[$pictures_count - 3] . "','" . $upload_pictures[$pictures_count - 2] . "','" . $upload_pictures[$pictures_count - 1] . "','" . $upload_pictures[$pictures_count] . "',
					'5')";
		$result = mysql_query($query) or die (fatal_error(128, $user, $user, $today, $query, mysql_error()));
		$picture_index[0] = $upload_pictures[1];
	}
	
	else
	{
		// Insert data into the Listing_Pictures table
		$query = 	"INSERT INTO Listing_Pictures(listing_id,picture_id_1,picture_id_2,picture_id_3,picture_id_4,picture_id_5,picture_count)
					VALUES('$listing_id','" . $upload_pictures[1] . "','" . $upload_pictures[2] . "','" . $upload_pictures[3] . "','" . $upload_pictures[4] . "','" . $upload_pictures[5] . "',
					'" . count($upload_pictures) . "')";
		$result = mysql_query($query) or die (fatal_error(128, $user, $user, $today, $query, mysql_error()));
		$picture_index[0] = $upload_pictures[1];
	}
	
	// If you got this far, everything has worked and the file has been successfully saved.
	// We are now going to redirect the client to the success page.
	if ($user > 0)	
		$uploadSuccess .= "post-to-facebook/";
}
header('Location: ' . $uploadSuccess);

// make an error handler which will be used if the upload fails
function error($error, $location, $seconds = 5)
{
	header("Refresh: $seconds; URL=\"$location\"");
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".
	'"http://www.w3.org/TR/html4/strict.dtd">'."\n\n".
	'<html lang="en">'."\n".
	'	<head>'."\n".
	'		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."\n\n".
	'	<title>Upload error</title>'."\n\n".
	'	</head>'."\n\n".
	'	<body>'."\n\n".
	'	<div id="Upload">'."\n\n".
	'		<h1>Upload failure</h1>'."\n\n".
	'		<p>An error has occured: '."\n\n".
	'		<span class="red">' . $error . '...</span>'."\n\n".
	'	 	The upload form is reloading</p>'."\n\n".
	'	 </div>'."\n\n".
	'</html>';
	exit;
} // end error handler
?>
