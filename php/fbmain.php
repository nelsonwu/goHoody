<?php
    // Program: fbmain.php
	// Desc:	PHP codes for handling Facebook connect 	
	//
	
	require 'facebook.php';

	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => $facebook_app_id,
	  'secret' => $facebook_app_secret,
	));
	
	// Get User ID
	$user = $facebook->getUser();
	
	// We may or may not have this data based on whether the user is logged in.
	//
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.
	
	if ($user) {
	  try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	  } catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}
	
	// Login or logout url will be needed depending on current user state.
	if ($user) {
		$logoutUrl = $facebook->getLogoutUrl();
	  
	} else {
		$loginUrl   = $facebook->getLoginUrl(
												array(
													'scope'	=> 'user_about_me,user_location,email,publish_stream'
												)
    										);
		$loginUrl_create   = $facebook->getLoginUrl(
														array(
															'scope'	=> 'user_about_me,user_location,email,publish_stream',
															'redirect_uri'=> $working_directory . 'service.php?lid=' . $_GET['lid'] . '&convert=1'
														)
    												);
	}
	
	

	$today = date("Y-m-d H:i:s"); 
   
    //if user is logged in and session is valid.
    if ($user)
	{ 
        // query user's Facebook user information
		$fql1    =   "select about_me,name,first_name,current_location,pic_square,pic_big,pic_small,pic,email,uid,activities,interests,music,tv,movies,books from user where uid=" . $user;
		$param1  =   array(
			'method'    => 'fql.query',
			'query'     => $fql1,
			'callback'  => ''
		);
		$fql_user_info_result   =   $facebook->api($param1);
		
		// query user's Facebook friendslist
		$fql2 = "SELECT uid1 FROM friend WHERE uid2=" . $user;
		$param2  =   array(
				'method'    => 'fql.query',
				'query'     => $fql2,
				'callback'  => ''
				);
		$fql_friendslist_result   =   $facebook->api($param2);
     
		$facebook_name = addslashes($fql_user_info_result[0][name]);
		$facebook_first_name = addslashes($fql_user_info_result[0][first_name]);
		$facebook_pic = $fql_user_info_result[0][pic];
		$facebook_pic_square = $fql_user_info_result[0][pic_square];
		$facebook_pic_small = $fql_user_info_result[0][pic_small];
		$facebook_pic_big = $fql_user_info_result[0][pic_big];
		$facebook_email = $fql_user_info_result[0][email]; 
		$facebook_city = addslashes($fql_user_info_result[0][current_location][city]);
		$facebook_state = addslashes($fql_user_info_result[0][current_location][state]);
		$facebook_country = addslashes($fql_user_info_result[0][current_location][country]);
		
		$activities_string = explode(', ', $fql_user_info_result[0][activities]);
		$interests_string = explode(', ', $fql_user_info_result[0][interests]);
		$music_string = explode(', ', $fql_user_info_result[0][music]);
		$tv_string = explode(', ', $fql_user_info_result[0][tv]);
		$movies_string = explode(', ', $fql_user_info_result[0][movies]);
		$books_string = explode(', ', $fql_user_info_result[0][books]);
				
		//determine if the user is already in the database, add user into the database if not
		$sql = "SELECT * FROM Basic_User_Information WHERE fb_uid='$user'";
		$result = mysql_query($sql) or die (fatal_error(5, $user, $user, $today, $sql, mysql_error()));
		$num = mysql_num_rows($result);
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		extract($row);		
		
// Adding new user into database
		if ($num == 0) //login name not found
		{			
			$facebook_about_me = addslashes($fql_user_info_result[0][about_me]);		
			if (!$facebook_about_me)
				$facebook_about_me = "Hello, I am " . $facebook_first_name . "!";
			
			$basic_query = "INSERT INTO Basic_User_Information(fb_uid,email,name,first_name,pic_square,pic_big,pic_small,pic,date_registered,about_me,access_token) VALUES('$user','$facebook_email','$facebook_name','$facebook_first_name','$facebook_pic_square','$facebook_pic_big','$facebook_pic_small','$facebook_pic','$today','$facebook_about_me','$facebook_access_token')";
			$result = mysql_query($basic_query) or die (fatal_error(6, $user, $user, $today, $basic_query, mysql_error()));
			 
			// code for adding the user name into the user name lookup table 
			$name = strtolower($facebook_name);
			$name = name_cleanup($name);
			$name_lookup_num = 1;
			for ($i=1; $name_lookup_num!=0; $i++)
			{
				$query = "SELECT * FROM User_Lookup WHERE profile_name='$name'";
				$result = mysql_query($query) or die (fatal_error(7, $user, $user, $today, $query, mysql_error()));
				$name_lookup_num = mysql_num_rows($result);
				if ($name_lookup_num == 0)
				{
					$user_lookup_sql = "INSERT INTO User_Lookup(fb_uid,profile_name) VALUES('$user','$name')";
					$result = mysql_query($user_lookup_sql) or die (fatal_error(8, $user, $user, $today, $user_lookup_sql, mysql_error()));	
					break;
				}
				$name = $name . $i;
			}
			
			// code for adding user's address into the database
			if ($facebook_country)
			{
				$lnglat = geocoding(NULL,$facebook_city,$facebook_state,$facebook_country,NULL);
				$lng = $lnglat["lng"];
				$lat = $lnglat["lat"];
				$address_query = "INSERT INTO User_Address(fb_uid, city, state, country,lng,lat) VALUES('$user', '$facebook_city','$facebook_state','$facebook_country','$lng','$lat')";
			}
			else 
				$address_query = "INSERT INTO User_Address(fb_uid) VALUES('$user')";
				
			$result = mysql_query($address_query) or die (fatal_error(9, $user, $user, $today, $address_query, mysql_error()));
		} // end of if ($num == 0)
		
		//determine if the user is already in the database, add user into the database if not
		$sql = "SELECT fb_uid FROM User_Address WHERE fb_uid='$user'";
		$address_result = mysql_query($sql) or die ("Couldn't execute query - checking membership");
		$address_num = mysql_num_rows($address_result);
		
		if ($address_num == 0) //login name not found
		{			
			if ($facebook_country)
			{
				$lnglat = geocoding(NULL,$facebook_city,$facebook_state,$facebook_country,NULL);
				$lng = $lnglat["lng"];
				$lat = $lnglat["lat"];
				$address_query = "INSERT INTO User_Address(fb_uid, city, state, country,lng,lat) VALUES('$user', '$facebook_city','$facebook_state','$facebook_country','$lng','$lat')";
			}
			else 
				$address_query = "INSERT INTO User_Address(fb_uid) VALUES('$user')";

			$result = mysql_query($address_query) or die (fatal_error(10, $user, $user, $today, $address_query, mysql_error()));
		} // end of if ($num == 0)
// End adding user to database		
		
		if ($email != $facebook_email && $facebook_email)
		{
			$email_sql = "UPDATE Basic_User_Information SET email='$facebook_email' WHERE fb_uid='$user'";
			$email_result = mysql_query($email_sql) or die (fatal_error(11, $user, $user, $today, $email_sql, mysql_error()));
			$refresh_action = 1;
		}
		if ($name != $facebook_name && $facebook_name)
		{
			$name_sql = "UPDATE Basic_User_Information SET name='$facebook_name',first_name='$facebook_first_name' WHERE fb_uid='$user'";
			$name_result = mysql_query($name_sql) or die (fatal_error(12, $user, $user, $today, $name_sql, mysql_error()));
			$refresh_action = 1;
		}
		if ($pic != $facebook_pic && $facebook_pic)
		{
			$pic_sql = "UPDATE Basic_User_Information SET pic='$facebook_pic',pic_small='$facebook_pic_small',pic_big='$facebook_pic_big',pic_square='$facebook_pic_square' WHERE fb_uid='$user'";
			$pic_result = mysql_query($pic_sql) or die (fatal_error(13, $user, $user, $today, $pic_sql, mysql_error()));
			$refresh_action = 1;
		}	
		if ($access_token != $facebook_access_token && $facebook_access_token)
		{
			$access_token_sql = "UPDATE Basic_User_Information SET access_token='$facebook_access_token' WHERE fb_uid='$user'";
			$access_token_result = mysql_query($access_token_sql) or die (fatal_error(14, $user, $user, $today, $access_token_sql, mysql_error()));
			$refresh_action = 1;
		}	
		if (count($fql_friendslist_result) != $friends_count)
		{
			//Update the friends count
			$friends_count_sql = "UPDATE Basic_User_Information SET friends_count='" . count($fql_friendslist_result) . "' WHERE fb_uid='$user'";
			$result = mysql_query($friends_count_sql) or die (fatal_error(15, $user, $user, $today, $friends_count_sql, mysql_error()));
			
			//Delete all the users friends in the friend list
			$friendlist_delete_sql = "DELETE FROM Friendlist WHERE uid1='$user'";
			$result = mysql_query($friendlist_delete_sql) or die (fatal_error(16, $user, $user, $today, $friendlist_delete_sql, mysql_error()));
			
			//Insert back all the friends into the users friend list
			$friendlist_add_sql = "INSERT INTO Friendlist (uid1, uid2) VALUES ";
			$i = 0;					  
			foreach ($fql_friendslist_result as $key => $friend_fbuid)
			{
				if($i == 0)
					$friendlist_add_sql .= "('" . $user . "', " . $friend_fbuid[uid1] . ")";
				else
					$friendlist_add_sql .= ",('" . $user . "', " . $friend_fbuid[uid1] . ")";
				
				$i++;
			}
							  
			$result = mysql_query($friendlist_add_sql) or die (fatal_error(17, $user, $user, $today, $friendlist_add_sql, mysql_error()));				  
			$refresh_action = 1;
		}
		if (count($activities_string) != $activities_count)	
		{
			//Update the activities interest count
			$activities_count_sql = "UPDATE Basic_User_Information SET activities_count='" . count($activities_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($activities_count_sql) or die (fatal_error(18, $user, $user, $today, $activities_count_sql, mysql_error()));
			
			//Get rid of any duplicates within the array
			$activities_string = array_unique($activities_string);
			if (array_filter($interests_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($activities_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(19, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$activities_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($activities_add_sql) or die (fatal_error(20, $user, $user, $today, $activities_add_sql, mysql_error()));	
						$activities_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$activities_index[] = $row['interests_id'];
					}
				}
				
				$activities_index = array_filter($activities_index);
				
				//Delete all the users activities in user's interests list
				$activities_delete_sql = "DELETE FROM User_Interests WHERE category=1&&fb_uid='$user'";
				$result = mysql_query($activities_delete_sql) or die (fatal_error(111, $user, $user, $today, $activities_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$activities_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($activities_index as $key)
				{
					if($i == 0)
						$activities_add_sql .= "('" . $user . "', 1, " . $key . ")";
					else
						$activities_add_sql .= ",('" . $user . "', 1, " . $key . ")";
					$i++;
				}
				$result = mysql_query($activities_add_sql) or die (fatal_error(21, $user, $user, $today, $activities_add_sql, mysql_error()));				  
				$refresh_action = 1;	
			} //end of if (array_filter($interests_string))
		} //end of if (count($activities_string) != $activities_count)	
	
		//interests update	
		if (count($interests_string) != $interests_count)	
		{
			//Update the activities interest count
			$interests_count_sql = "UPDATE Basic_User_Information SET interests_count='" . count($interests_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($interests_count_sql) or die (fatal_error(22, $user, $user, $today, $interests_count_sql, mysql_error()));
			
			//Get rid of any duplicates within the array
			$interests_string = array_unique($interests_string);
			if (array_filter($interests_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($interests_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(23, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$interests_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($interests_add_sql) or die (fatal_error(24, $user, $user, $today, $interests_add_sql, mysql_error()));	
						$interests_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$interests_index[] = $row['interests_id'];
					}
				}
	
				$interests_index = array_filter($interests_index);			
				
				//Delete all the users activities in user's interests list
				$interests_delete_sql = "DELETE FROM User_Interests WHERE category=2&&fb_uid='$user'";
				$result = mysql_query($interests_delete_sql) or die (fatal_error(25, $user, $user, $today, $interests_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$interests_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($interests_index as $key)
				{
					if($i == 0)
						$interests_add_sql .= "('" . $user . "', 2, " . $key . ")";
					else
						$interests_add_sql .= ",('" . $user . "', 2, " . $key . ")";
					$i++;
				}
				$result = mysql_query($interests_add_sql) or die (fatal_error(26, $user, $user, $today, $interests_add_sql, mysql_error()));				  
				$refresh_action = 1;
			} //end of if (array_filter($interests_string))
		} //end of if (count($interests_string) != $interests_count)
		
		//interests update	
		if (count($music_string) != $music_count)	
		{
			//Update the activities interest count
			$music_count_sql = "UPDATE Basic_User_Information SET music_count='" . count($music_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($music_count_sql) or die (fatal_error(27, $user, $user, $today, $music_count_sql, mysql_error()));
	
			//Get rid of any duplicates within the array
			$music_string = array_unique($music_string);
			
			if (array_filter($music_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($music_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(28, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$music_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($music_add_sql) or die (fatal_error(29, $user, $user, $today, $music_add_sql, mysql_error()));	
						$music_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$music_index[] = $row['interests_id'];
					}
				}
				
				$music_index = array_filter($music_index);
							
				//Delete all the users activities in user's interests list
				$music_delete_sql = "DELETE FROM User_Interests WHERE category=3&&fb_uid='$user'";
				$result = mysql_query($music_delete_sql) or die (fatal_error(30, $user, $user, $today, $music_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$music_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($music_index as $key)
				{
					if($i == 0)
						$music_add_sql .= "('" . $user . "', 3, " . $key . ")";
					else
						$music_add_sql .= ",('" . $user . "', 3, " . $key . ")";
					$i++;
				}	
				$result = mysql_query($music_add_sql) or die (fatal_error(31, $user, $user, $today, $music_add_sql, mysql_error()));
				$refresh_action = 1;	
			} //end of if (array_filter($music_string))
		} //end of if (count($music_string) != $music_count)	
	
		//interests update	
		if (count($tv_string) != $tv_count)	
		{
			//Update the activities interest count
			$tv_count_sql = "UPDATE Basic_User_Information SET tv_count='" . count($tv_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($tv_count_sql) or die (fatal_error(32, $user, $user, $today, $tv_count_sql, mysql_error()));
	
			//Get rid of any duplicates within the array
			$tv_string = array_unique($tv_string);
			
			if (array_filter($tv_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($tv_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(33, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$tv_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($tv_add_sql) or die (fatal_error(34, $user, $user, $today, $tv_add_sql, mysql_error()));	
						$tv_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$tv_index[] = $row['interests_id'];
					}
				}
				
				$tv_index = array_filter($tv_index);
				
				//Delete all the users activities in user's interests list
				$activities_delete_sql = "DELETE FROM User_Interests WHERE category=4&&fb_uid='$user'";
				$result = mysql_query($activities_delete_sql) or die (fatal_error(35, $user, $user, $today, $activities_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$tv_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($tv_index as $key)
				{
					if($i == 0)
						$tv_add_sql .= "('" . $user . "', 4, " . $key . ")";
					else
						$tv_add_sql .= ",('" . $user . "', 4, " . $key . ")";
					$i++;
				}
				$result = mysql_query($tv_add_sql) or die (fatal_error(36, $user, $user, $today, $tv_add_sql, mysql_error()));				  
				$refresh_action = 1;
			} //end of if (array_filter($tv_string))
		} //end of if (count($tv_string) != $row['tv_count'])	
		
		//interests update	
		if (count($movies_string) != $movies_count)	
		{
			//Update the activities interest count
			$movies_count_sql = "UPDATE Basic_User_Information SET movies_count='" . count($movies_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($movies_count_sql) or die (fatal_error(37, $user, $user, $today, $movies_count_sql, mysql_error()));
	
			//Get rid of any duplicates within the array
			$movies_string = array_unique($movies_string);
			if (array_filter($movies_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($movies_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(38, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$movies_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($movies_add_sql) or die (fatal_error(39, $user, $user, $today, $movies_add_sql, mysql_error()));	
						$movies_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$movies_index[] = $row['interests_id'];
					}
				}
				
				$movies_index = array_filter($movies_index);
				
				//Delete all the users activities in user's interests list
				$movies_delete_sql = "DELETE FROM User_Interests WHERE category=5&&fb_uid='$user'";
				$result = mysql_query($movies_delete_sql) or die (fatal_error(40, $user, $user, $today, $movies_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$movies_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($movies_index as $key)
				{
					if($i == 0)
						$movies_add_sql .= "('" . $user . "', 5, " . $key . ")";
					else
						$movies_add_sql .= ",('" . $user . "', 5, " . $key . ")";
					$i++;
				}
				$result = mysql_query($movies_add_sql) or die (fatal_error(41, $user, $user, $today, $movies_add_sql, mysql_error()));				  
				$refresh_action = 1;
			} //end of if (array_filter($movies_string))
		} //end of if (count($movies_string) != $movies_count)
		
		//interests update	
		if (count($books_string) != $books_count)	
		{
			//Update the activities interest count
			$books_count_sql = "UPDATE Basic_User_Information SET books_count='" . count($books_string) . "' WHERE fb_uid='$user'";
			$result = mysql_query($books_count_sql) or die (fatal_error(42, $user, $user, $today, $books_count_sql, mysql_error()));
	
			//Get rid of any duplicates within the array
			$books_string = array_unique($books_string);
			
			if (array_filter($books_string))
			{
				// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
				foreach ($books_string as $key)
				{		
					$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key) . "'";
					$result = mysql_query($query) or die (fatal_error(43, $user, $user, $today, $query, mysql_error()));
					$num = mysql_num_rows($result);
					
					//service listing name not found
					if ($num == 0) 
					{
						//Insert back all the friends into the users friend list
						$books_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key) . "')";
						$result = mysql_query($books_add_sql) or die (fatal_error(44, $user, $user, $today, $books_add_sql, mysql_error()));	
						$books_index[] = mysql_insert_id();
					}
					else
					{
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$books_index[] = $row['interests_id'];
					}
				}
								
				$books_index = array_filter($books_index);
				
				//Delete all the users activities in user's interests list
				$books_delete_sql = "DELETE FROM User_Interests WHERE category=6&&fb_uid='$user'";
				$result = mysql_query($books_delete_sql) or die (fatal_error(45, $user, $user, $today, $books_delete_sql, mysql_error()));
		
				//Insert back all the activities into the users interests list
				$books_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
				$i = 0;					  
				foreach ($books_index as $key)
				{
					if($i == 0)
						$books_add_sql .= "('" . $user . "', 6, " . $key . ")";
					else
						$books_add_sql .= ",('" . $user . "', 6, " . $key . ")";
					$i++;
				}
				$result = mysql_query($books_add_sql) or die (fatal_error(46, $user, $user, $today, $books_add_sql, mysql_error()));				  
				$refresh_action = 1;
			} //end of if (array_filter($books_string))
		} //end of if (count($books_string) != $books_count)	
		
		if ($refresh_action == 1)
			header("Refresh: 0;");
    } // end of if ($user)	
?>
