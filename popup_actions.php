<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database - Error Code: 101");
	
	$action = (int) $_GET['action'];
	$fb_uid = (int) $_GET['fb_uid'];
	$user = (int) $_GET['user_uid'];
	
	if(empty($user))
		die("please make sure you are logged into Hoody");
		
	$rid = (int) $_GET['rid'];
	$lid = (int) $_GET['lid'];

	$today = date("Y-m-d H:i:s"); 
	
	$user_sql = "SELECT name,pic_square,email FROM Basic_User_Information WHERE fb_uid='$user'";
	$result3 = mysql_query($user_sql) or die (fatal_error(254, $user, $user, $today, $user_sql, mysql_error()));
	$row = mysql_fetch_array($result3,MYSQL_ASSOC);
	$user_pic_square = $row['pic_square'];
	$user_name = $row['name'];
	$user_email = $row['email'];
	
	if (!$lid)
		$lid = (int) $_POST['lid'];

	$contact_seller = make_clickable($_POST[contact_seller]);

	
	// Action:
	// 1 --> contact seller (Service Description + User Dashboard)
	// 2 --> contact customer (User Dashboard)
	// 3 --> Thank you email (User Dashboard)
	// 4 --> report listing abuse (Service Description + User Dashboard)
	// 5 --> report user abuse (User Dashboard)
	// 6 --> review processing
	// 7 --> Name my own price

	if ($action == 6 && $rid > 0 && $_POST['rating'] != NULL)
	{			
		$rating = $_POST['rating'];
		$review = $_POST['review'];
		$rating = mysql_real_escape_string($rating);
		$review = mysql_real_escape_string($review);
		$database_rating = (int)$rating;
		
		
		$query = "SELECT listing_id FROM Confirmed_Transactions WHERE transaction_id='$rid'";
		$result = mysql_query($query) or die (fatal_error(134, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$listing_id = $row['listing_id'];
		
		$query = "SELECT title,price,pricing_model,fb_uid,listing_description FROM Listing_Overview WHERE listing_id='$listing_id'";
		$result = mysql_query($query) or die (fatal_error(134, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		extract($row);
		
		$query = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
		$result = mysql_query($query) or die (fatal_error(135, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		extract($row);
		
		
		// Extract pictures for the listing
		$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
		$picture_result = mysql_query($picture_sql) or die (fatal_error(257, $user, $user, $today, $picture_sql, mysql_error()));
		$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
				
		$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id=" . $picture_row['picture_id_1'];
		$url_result = mysql_query($url_sql) or die (fatal_error(258, $user, $user, $today, $url_sql, mysql_error()));
		$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
		$picture_url = $url_row['URL'];
			
		if ($pricing_model == 0)
			$pricing_model_text = " per job";
		else if ($pricing_model == 1)
			$pricing_model_text = " per hour";
			
		if ($price == 0)
			$price_text = "Free";
		else 
			$price_text = "$ " . $price . $pricing_model_text;
			
			
		//Link to Facebook PHP SDK
		include_once "php/fbmain.php";
		$config['baseurl']  =   $working_directory. "index.php";	
		
		//if the transaction hasn't been confirmed
		if ($transaction_date == NULL)
		{			
			$query = "UPDATE Confirmed_Transactions SET review_status=1,review='$review',rating='$database_rating',transaction_date='$today' WHERE transaction_id='$rid'";
			$result = mysql_query($query) or die (fatal_error(142, $user, $user, $today, $query, mysql_error()));
			$query = "UPDATE Contact_History SET transaction_status=1 WHERE contact_id='$rid'";
			$result_update = mysql_query($query) or die (fatal_error(143, $user, $user, $today, $query, mysql_error()));
			
			// Updated 4/12
			// Incorperating popularity index
			$query = "UPDATE Listing_Overview SET popularity = (15 + popularity) WHERE listing_id = '$listing_id'";
			$result = mysql_query($query) or die (fatal_error(144, $user, $user, $today, $query, mysql_error()));
						
			if($_POST['facebook'])
			{
				try 
				{						
					$wallpostpage = $facebook->api('/me/feed', 'post',
									array(
									  'message' 	=> 'My review of the ' . $title . ' service on Hoody' ,
									  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url,
									  'link'    	=> 'http://gohoody.com/service/' . $listing_id . '/',
									  'name'    	=> $title,
									  'caption' 	=> $price_text,
									  'description' => nl2br($review),
									  'source' 		=> '',
									  'cb'      	=> ''
									  )
					);
				} 
				catch (FacebookApiException $e) 
				{
					 print_r($o);
				}
			}	
		}
		else
		{
			if ($review_status == 0)
			{
				// Updated 4/12
				// Incorperating popularity index
				$query = "UPDATE Listing_Overview SET popularity = (5 + popularity) WHERE listing_id = '$listing_id'";
				$result = mysql_query($query) or die (fatal_error(145, $user, $user, $today, $query, mysql_error()));
			}
			$query = "UPDATE Confirmed_Transactions SET review_status=1,review='$review',rating='$database_rating' WHERE transaction_id='$rid'";
			$result = mysql_query($query) or die (fatal_error(146, $user, $user, $today, $query, mysql_error()));
			
			if($_POST['facebook'])
			{
				try 
				{						
					$wallpostpage = $facebook->api('/me/feed', 'post',
									array(
									  'message' 	=> 'My review of the ' . $title . ' service on Hoody' ,
									  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url,
									  'link'    	=> 'http://gohoody.com/service/' . $listing_id . '/',
									  'name'    	=> $title,
									  'caption' 	=> $price_text,
									  'description' => nl2br($review),
									  'source' 		=> '',
									  'cb'      	=> ''
									  )
					);
				} 
				catch (FacebookApiException $e) 
				{
					 print_r($o);
				}
			}	
		}
	}
	else if ($action > 0 && $fb_uid > 0)
	{
		if ($action == 1 && $lid > 0) 
		{
			$buyer_name = $user_name;
			
			//Loop up the user profile name of the sender
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(147, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_name = $row4['profile_name'];
			
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(148, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$title = $row['title'];

			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(149, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_email = $row['email'];
			$seller_optional_email = $row['optional_email'];
						
			// if the email text field is not empty
			// put the email code as a function into the email.php include file 
			if(isset($_POST['contact']))
			{
				$message = "<head>
							<style type='text/css'>
							#templateContainer tbody tr td #templateFooter tbody tr td table tbody tr #social div {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h4 {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p strong {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								font-family: Arial, Helvetica, sans-serif;
							}
							body p {
								color: #3C3C3C;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								color: #322E3D;
							}
							</style>
							</head>
							
							<body>
							<div align='center'>
							  <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer'>
								<tbody>
								  <tr>
									<td align='center' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600' id='templateBody'>
									  <tbody>
										<tr>
										  <td valign='top'><table border='0' cellpadding='20' cellspacing='0' width='100%'>
											<tbody>
											  <tr mc:repeatable=''>
												<td valign='top'><div mc:edit='std_content00'>
												  <h2>" . $title . "</h2>
												  <hr />
												  <p><img src='". $user_pic_square . "' /> - <a href='http://gohoody.com/profile/" . $user_profile_name . "/'>". $buyer_name . "</a></p>
												  <p>" . nl2br($contact_seller) . "<br /></p>
												  <hr />
												  <p><em>Date Sent: " . $today . " | Link to the service: <a href='http://gohoody.com/service/". $lid ."/'>". $title ."</a></em></p>
												  </div></td>
												</tr>
											  </tbody>
											</table></td>
										  </tr>
										</tbody>
									</table></td>
								  </tr>
								</tbody>
							  </table>
							</div>
							</body>
							</html>		
							";
				
				$subj =  $buyer_name . ' sent you a message from Hoody'; 
				
				email($message,$subj,$seller_email,$seller_optional_email,$user_email,$buyer_name);
				
				//determine if the user is already in the contact history database, add user into the database if not
				//update contact time if the  contact history is already in the database
				$sql = "SELECT * FROM Contact_History WHERE listing_id='$lid'&&fb_uid='$user'";
				$result = mysql_query($sql) or die (fatal_error(150, $user, $user, $today, $sql, mysql_error()));
				$num = mysql_num_rows($result);
	
				if ($num == 0 && $user != $fb_uid) //login name not found
				{			
					$contact_seller = mysql_real_escape_string($contact_seller);
					//Insert data into Contact_History table
					//note: listing_id is auto_incrementally generated from this table
					$query = "INSERT INTO Contact_History(listing_id,fb_uid,buyer_email,contact_time,email_body) VALUES('$lid','$user','$user_email','$today','$contact_seller')";
					$result = mysql_query($query) or die (fatal_error(151, $user, $user, $today, $query, mysql_error()));
					$contact_id = mysql_insert_id();
					
					$query = "INSERT INTO Confirmed_Transactions(listing_id,fb_uid,transaction_id) VALUES('$lid','$user','$contact_id')";
					$result = mysql_query($query) or die (fatal_error(152, $user, $user, $today, $query, mysql_error()));
					// Updated 4/12
					// Incorperating popularity index
					$query = "UPDATE Listing_Overview SET popularity = (3 + popularity) WHERE listing_id = '$lid'";
					$result = mysql_query($query) or die (fatal_error(153, $user, $user, $today, $query, mysql_error()));
				} // end of if ($num == 0)
				
				// if the transaction is still not completed, just update the contact time		
				else if ($transaction_status == 0 && $user != $fb_uid)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(154, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is not completed and user has already remove this from his interested service list
				// update the contact time, and relist it on user's dashboard	
				else if ($transaction_status == 2)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET transaction_status=0,contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(155, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is completed, just update the contact time		
				else if ($transaction_status == 1)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(156, $user, $user, $today, $query, mysql_error()));
				}
			} //End of if (isset($_POST['contact']))
		} // End of if ($action == 1 && $lid > 0)
		
		else if ($action == 1 && $lid == 0) 
		{
			$buyer_name = $user_name;
			
			//Loop up the user profile name of the sender
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(157, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_name = $row4['profile_name'];
						
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(158, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_email = $row['email'];
			$seller_optional_email = $row['optional_email'];
						
			// if the email text field is not empty
			// put the email code as a function into the email.php include file 
			if(isset($_POST['contact']))
			{
				$message = "<head>
							<style type='text/css'>
							#templateContainer tbody tr td #templateFooter tbody tr td table tbody tr #social div {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h4 {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p strong {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								font-family: Arial, Helvetica, sans-serif;
							}
							body p {
								color: #3C3C3C;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								color: #322E3D;
							}
							</style>
							</head>
							
							<body>
							<div align='center'>
							  <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer'>
								<tbody>
								  <tr>
									<td align='center' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600' id='templateBody'>
									  <tbody>
										<tr>
										  <td valign='top'><table border='0' cellpadding='20' cellspacing='0' width='100%'>
											<tbody>
											  <tr mc:repeatable=''>
												<td valign='top'><div mc:edit='std_content00'>
												  <h2>Hoody message from " . $buyer_name . "</h2>
												  <hr />
												  <p><img src='". $user_pic_square . "' /> - <a href='http://gohoody.com/profile/" . $user_profile_name . "/'>". $buyer_name . "</a></p>
												  <p>" . nl2br($contact_seller) . "<br /></p>
												  <hr />
												  <p><em>Date Sent: " . $today . "</em></p>
												  </div></td>
												</tr>
											  </tbody>
											</table></td>
										  </tr>
										</tbody>
									</table></td>
								  </tr>
								</tbody>
							  </table>
							</div>
							</body>
							</html>		
							";
				
				$subj =  $buyer_name . ' sent you a message from Hoody'; 
				
				email($message,$subj,$seller_email,$seller_optional_email,$user_email,$buyer_name);
			} //End of if (isset($_POST['contact']))
		} // End of if ($action == 1 && $lid == 0)
		else if ($action == 2 && $lid > 0) 
		{
			$seller_name = $user_name;
			
			//Loop up the user profile name of the sender
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(159, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_name = $row4['profile_name'];
						
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(160, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$title = $row['title'];

			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(161, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$buyer_email = $row['email'];
			$buyer_optional_email = $row['optional_email'];
						
			// if the email text field is not empty
			// Updated 4/1
			// put the email code as a function into the email.php include file 
			if(isset($_POST['contact']))
			{
				$message = "<head>
							<style type='text/css'>
							#templateContainer tbody tr td #templateFooter tbody tr td table tbody tr #social div {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h4 {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p strong {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								font-family: Arial, Helvetica, sans-serif;
							}
							body p {
								color: #3C3C3C;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								color: #322E3D;
							}
							</style>
							</head>
							
							<body>
							<div align='center'>
							  <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer'>
								<tbody>
								  <tr>
									<td align='center' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600' id='templateBody'>
									  <tbody>
										<tr>
										  <td valign='top'><table border='0' cellpadding='20' cellspacing='0' width='100%'>
											<tbody>
											  <tr mc:repeatable=''>
												<td valign='top'><div mc:edit='std_content00'>
												  <h2>" . $title . "</h2>
												  <hr />
												  <p><img src='". $user_pic_square . "' /> - <a href='http://gohoody.com/profile/" . $user_profile_name . "/'>". $seller_name . "</a></p>
												  <p>" . nl2br($contact_seller) . "<br /></p>
												  <hr />
												  <p><em>Date Sent: " . $today . " | Link to the service: <a href='http://gohoody.com/service/". $lid ."/'>". $title ."</a></em></p>
												  </div></td>
												</tr>
											  </tbody>
											</table></td>
										  </tr>
										</tbody>
									</table></td>
								  </tr>
								</tbody>
							  </table>
							</div>
							</body>
							</html>		
							";
				
				$subj =  $seller_name . ' sent you a message from Hoody'; 
				
				email($message,$subj,$buyer_email,$buyer_optional_email,$user_email,$seller_name);
				
				//determine if the user is already in the contact history database, add user into the database if not
				//update contact time if the  contact history is already in the database
				$sql = "SELECT * FROM Contact_History WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
				$result = mysql_query($sql) or die (fatal_error(162, $user, $user, $today, $sql, mysql_error()));
				$num = mysql_num_rows($result);
					
				// if the transaction is still not completed, just update the contact time		
				if ($transaction_status == 0)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
					$result = mysql_query($query) or die (fatal_error(163, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is not completed and user has already remove this from his interested service list
				// update the contact time, and relist it on user's dashboard	
				else if ($transaction_status == 2)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET transaction_status=0,contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
					$result = mysql_query($query) or die (fatal_error(164, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is completed, just update the contact time		
				else if ($transaction_status == 1)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
					$result = mysql_query($query) or die (fatal_error(165, $user, $user, $today, $query, mysql_error()));
				}
			} //End of if ($_POST[contact_seller] != "")
		} // End of else if ($action == 2)
		else if ($action == 3 && $lid > 0) 
		{
			$seller_name = $user_name;
			
			//Loop up the user profile name of the sender
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(166, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_name = $row4['profile_name'];
						
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(167, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$title = $row['title'];

			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(168, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$buyer_email = $row['email'];
			$buyer_optional_email = $row['optional_email'];
						
			// if the email text field is not empty
			// Updated 4/1
			// put the email code as a function into the email.php include file 
			if(isset($_POST['contact']))
			{
				$message = "<head>
							<style type='text/css'>
							#templateContainer tbody tr td #templateFooter tbody tr td table tbody tr #social div {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h4 {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p strong {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								font-family: Arial, Helvetica, sans-serif;
							}
							body p {
								color: #3C3C3C;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								color: #322E3D;
							}
							</style>
							</head>
							
							<body>
							<div align='center'>
							  <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer'>
								<tbody>
								  <tr>
									<td align='center' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600' id='templateBody'>
									  <tbody>
										<tr>
										  <td valign='top'><table border='0' cellpadding='20' cellspacing='0' width='100%'>
											<tbody>
											  <tr mc:repeatable=''>
												<td valign='top'><div mc:edit='std_content00'>
												  <h2>Thanks for buying " . $title . "</h2>
												  <hr />
												  <p><img src='". $user_pic_square . "' /> - <a href='http://gohoody.com/profile/" . $user_profile_name . "/'>". $seller_name . "</a></p>
												  <p>" . nl2br($contact_seller) . "<br /></p>
												  <hr />
												  <p><em>Date Sent: " . $today . " | Link to the service: <a href='http://gohoody.com/service/". $lid ."/'>". $title ."</a></em></p>
												  </div></td>
												</tr>
											  </tbody>
											</table></td>
										  </tr>
										</tbody>
									</table></td>
								  </tr>
								</tbody>
							  </table>
							</div>
							</body>
							</html>		
							";
				
				$subj =  $seller_name . ' sent you a message from Hoody'; 
				
				email($message,$subj,$buyer_email,$buyer_optional_email,$user_email,$seller_name);
				
				// Update the transaction status to confirmed transaction
				$query = "UPDATE Contact_History SET transaction_status=1 WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
				$result_update = mysql_query($query) or die (fatal_error(169, $user, $user, $today, $query, mysql_error()));
					
				$query = "UPDATE Confirmed_Transactions SET transaction_date='$today' 
				WHERE listing_id='$lid'&&fb_uid='$fb_uid'";
				$result_update = mysql_query($query) or die (fatal_error(170, $user, $user, $today, $query, mysql_error()));
				
				// Updated 4/12
				// Incorperating popularity index
				$query = "UPDATE Listing_Overview SET popularity = (10 + popularity) WHERE listing_id = '$lid'";
				$result = mysql_query($query) or die (fatal_error(171, $user, $user, $today, $query, mysql_error()));
			} //End of if ($_POST[contact_seller] != "")
		} // End of else if ($action == 3)
		else if ($action == 4 && $lid > 0) 
		{								
			$message = 'report listing abuse';
			$subj =  'Report Abuse - listing id: ' . $lid; 
			
			email($message,$subj,'notify@gohoody.com','',$user_email,$user_name);
		} // End of else if ($action == 4)
		else if ($action == 5 && $lid > 0) 
		{			
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(172, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$abuser_name = $row['name'];
						
			$message = 'report user abuse';
			$subj =  'Report ' . $abuser_name . ' abusing - listing id: ' . $lid; 
			
			email($message,$subj,'notify@gohoody.com','',$user_email,$user_name);
		} // End of else if ($action == 5)
		
		
		
		
		
		
		
		
		else if ($action == 7) 
		{
			$buyer_name = $user_name;
			
			//Loop up the user profile name of the sender
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(147, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_name = $row4['profile_name'];
			
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(148, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$title = $row['title'];

			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT email,optional_email FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(149, $user, $user, $today, $service_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_email = $row['email'];
			$seller_optional_email = $row['optional_email'];
						
			// if the email text field is not empty
			// put the email code as a function into the email.php include file 
			if(isset($_POST['contact']))
			{
				$message = "<head>
							<style type='text/css'>
							#templateContainer tbody tr td #templateFooter tbody tr td table tbody tr #social div {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h4 {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p strong {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div p {
								font-family: Arial, Helvetica, sans-serif;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								font-family: Arial, Helvetica, sans-serif;
							}
							body p {
								color: #3C3C3C;
							}
							#templateContainer tbody tr td #templateBody tbody tr td table tbody tr td div h2 {
								color: #322E3D;
							}
							</style>
							</head>
							
							<body>
							<div align='center'>
							  <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer'>
								<tbody>
								  <tr>
									<td align='center' valign='top'><table border='0' cellpadding='0' cellspacing='0' width='600' id='templateBody'>
									  <tbody>
										<tr>
										  <td valign='top'><table border='0' cellpadding='20' cellspacing='0' width='100%'>
											<tbody>
											  <tr mc:repeatable=''>
												<td valign='top'><div mc:edit='std_content00'>
												  <h2>" . $title . "</h2>
												  <hr />
												  <p><img src='". $user_pic_square . "' /> - <a href='http://gohoody.com/profile/" . $user_profile_name . "/'>". $buyer_name . "</a></p>
												  <p>I would like to offer you $" . $_POST['price'] . " for your service.</p>
												  <p>Could you kindly let me know if you are willing to accept it?<br /></p>
												  <hr />
												  <p><em>Date Sent: " . $today . " | Link to the service: <a href='http://gohoody.com/service/". $lid ."/'>". $title ."</a></em></p>
												  </div></td>
												</tr>
											  </tbody>
											</table></td>
										  </tr>
										</tbody>
									</table></td>
								  </tr>
								</tbody>
							  </table>
							</div>
							</body>
							</html>		
							";
				
				
				$subj =  $buyer_name . ' sent you a message from Hoody'; 
				
				email($message,$subj,$seller_email,$seller_optional_email,$user_email,$buyer_name);
				
				//determine if the user is already in the contact history database, add user into the database if not
				//update contact time if the  contact history is already in the database
				$sql = "SELECT * FROM Contact_History WHERE listing_id='$lid'&&fb_uid='$user'";
				$result = mysql_query($sql) or die (fatal_error(150, $user, $user, $today, $sql, mysql_error()));
				$num = mysql_num_rows($result);
	
				if ($num == 0 && $user != $fb_uid) //login name not found
				{			
					$contact_seller = mysql_real_escape_string($contact_seller);
					//Insert data into Contact_History table
					//note: listing_id is auto_incrementally generated from this table
					$query = "INSERT INTO Contact_History(listing_id,fb_uid,buyer_email,contact_time,email_body) VALUES('$lid','$user','$user_email','$today','$contact_seller')";
					$result = mysql_query($query) or die (fatal_error(151, $user, $user, $today, $query, mysql_error()));
					$contact_id = mysql_insert_id();
					
					$query = "INSERT INTO Confirmed_Transactions(listing_id,fb_uid,transaction_id) VALUES('$lid','$user','$contact_id')";
					$result = mysql_query($query) or die (fatal_error(152, $user, $user, $today, $query, mysql_error()));
					// Updated 4/12
					// Incorperating popularity index
					$query = "UPDATE Listing_Overview SET popularity = (3 + popularity) WHERE listing_id = '$lid'";
					$result = mysql_query($query) or die (fatal_error(153, $user, $user, $today, $query, mysql_error()));
				} // end of if ($num == 0)
				
				// if the transaction is still not completed, just update the contact time		
				else if ($transaction_status == 0 && $user != $fb_uid)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(154, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is not completed and user has already remove this from his interested service list
				// update the contact time, and relist it on user's dashboard	
				else if ($transaction_status == 2)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET transaction_status=0,contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(155, $user, $user, $today, $query, mysql_error()));
				}
				
				// if the transaction is completed, just update the contact time		
				else if ($transaction_status == 1)
				{
					$row = mysql_fetch_array($result,MYSQL_ASSOC);	
					extract($row);
					$query = "UPDATE Contact_History SET contact_time='$today' WHERE listing_id='$lid'&&fb_uid='$user'";
					$result = mysql_query($query) or die (fatal_error(156, $user, $user, $today, $query, mysql_error()));
				}
			} //End of if (isset($_POST['contact']))
		} // End of if ($action == 1 && $lid > 0)
		
		
		
		
		
		
		
	}
	header("Location: popup_success.php");
?>