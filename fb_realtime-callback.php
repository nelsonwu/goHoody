<?php
$uid1 = "516927476";


include "php/misc.inc";
$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

//Link to Facebook PHP SDK
include "php/facebook.php";

$facebook = new Facebook(array(
  'appId'  => $facebook_app_id,
  'secret' => $facebook_app_secret,
  'cookie' => true,
));

define('VERIFY_TOKEN', 'blah');                                    
$method = $_SERVER['REQUEST_METHOD'];                             
                                             
if ($method == 'GET' && $_GET['hub_mode'] == 'subscribe' && $_GET['hub_verify_token'] == VERIFY_TOKEN) 
	echo $_GET['hub_challenge']; 

else
//else if ($method == 'POST') 
{                                   
	$updates = json_decode(file_get_contents("php://input"), true); 
	 
/////////////////////////////////////  
$sql = "UPDATE Basic_User_Information SET about_me='1' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);	
	
	
	//get the uids of the users that made changes
	foreach ($updates['entry'] as $key)
	{		
		$user = $key['uid'];
		
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='2' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
			
		foreach ($key['changed_fields'] as $key2)
		{
			$sql = "SELECT access_token FROM Basic_User_Information WHERE fb_uid='$user'";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
						
			if ($key2 == 'pic')
			{
				$fql = "select pic_square,pic_big,pic_small,pic from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
		
				$facebook_pic = $fqlResult[0][pic];
				$facebook_pic_square = $fqlResult[0][pic_square];
				$facebook_pic_small = $fqlResult[0][pic_small];
				$facebook_pic_big = $fqlResult[0][pic_big];
				
				$sql = "UPDATE Basic_User_Information SET pic='$facebook_pic',pic_small='$facebook_pic_small',pic_big='$facebook_pic_big',pic_square='$facebook_pic_square' WHERE fb_uid='" . $user . "'";
				$result = mysql_query($sql); 
			}
			if ($key2 == 'friends')
			{
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET about_me='3' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
				
				// code for updating user's friendslist into the database
				$fql = "SELECT uid1 FROM friend WHERE uid2=" . $user;
				
				
				
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='31' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
				
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'access_token' => $row['access_token'],
					'callback'  => ''
				);
						
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='32' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);

				$fqlResult1   =   $facebook->api($param);
				
				
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='4' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
				
				//Update the friends count
				$friends_count_sql = "UPDATE Basic_User_Information SET friends_count='" . count($fqlResult1) . "' WHERE fb_uid='$user'";
				$result = mysql_query($friends_count_sql);
				
				//Delete all the users friends in the friend list
				$friendlist_delete_sql = "DELETE FROM Friendlist WHERE uid1='$user'";
				$result = mysql_query($friendlist_delete_sql);
				
				
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET about_me='5' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
				
				
				//Insert back all the friends into the users friend list
				$friendlist_add_sql = "INSERT INTO Friendlist (uid1, uid2) VALUES ";
				$i = 0;					  
				foreach ($fqlResult1 as $key3 => $friend_fbuid)
				{
					if($i == 0)
						$friendlist_add_sql .= "('" . $user . "', " . $friend_fbuid[uid1] . ")";
					else
						$friendlist_add_sql .= ",('" . $user . "', " . $friend_fbuid[uid1] . ")";
					
					$i++;
				}
								  
				$result = mysql_query($friendlist_add_sql);	
				
				
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='6' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);
			
			}
//			if ($key2 == 'email')
//			{
//				$fql = "select email from user where uid=" . $user;
//				//http://developers.facebook.com/docs/reference/fql/
//				$param  =   array(
//					'method'    => 'fql.query',
//					'query'     => $fql,
//					'access_token' => $row['access_token'],
//					'callback'  => ''
//				);
//				$fqlResult   =   $facebook->api($param);
//				$facebook_email = $fqlResult[0][email]; 	
//				
//				$sql = "UPDATE Basic_User_Information SET email='$facebook_email' WHERE fb_uid='" . $user . "'";
//				$result = mysql_query($sql); 
//			}
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			if ($key2 == 'name' || $key2 == 'first_name')
			{
				$fql = "select name,first_name from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
		 
				$facebook_name = $fqlResult[0][name];
				$facebook_first_name = $fqlResult[0][first_name];
								
				$sql = "UPDATE Basic_User_Information SET name='$facebook_name',first_name='$facebook_first_name' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($sql); 
			}
			if ($key2 == 'activities')
			{
				
/////////////////////////////////////
$sql = "UPDATE Basic_User_Information SET flyer_about_me='4' WHERE fb_uid='" . $uid1 . "'";
$result = mysql_query($sql);

				
				$fql = "select activities from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param); 	
				$activities_string = explode(', ', $fqlResult[0][activities]);	//1		
				
				//Update the activities interest count
				$activities_count_sql = "UPDATE Basic_User_Information SET activities_count='" . count($activities_string) . "' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($activities_count_sql);
				
				//Get rid of any duplicates within the array
				$activities_string = array_unique($activities_string);
				if (array_filter($interests_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($activities_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$activities_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($activities_add_sql);	
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
					$activities_delete_sql = "DELETE FROM User_Interests WHERE category=1&&fb_uid='" . $key['uid'] . "'";
					$result = mysql_query($activities_delete_sql);
			
					//Insert back all the activities into the users interests list
					$activities_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($activities_index as $key4)
					{
						if($i == 0)
							$activities_add_sql .= "('" . $key['uid'] . "', 1, " . $key4 . ")";
						else
							$activities_add_sql .= ",('" . $key['uid'] . "', 1, " . $key4 . ")";
						$i++;
					}
					$result = mysql_query($activities_add_sql) or die (mysql_error() . " - " . $activities_add_sql);				  
				} //end of if (array_filter($interests_string))	  
			}
			if ($key2 == 'interests')
			{
				$fql = "select interests from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);	
				$interests_string = explode(', ', $fqlResult[0][interests]); //2
				
				//Update the activities interest count
				$interests_count_sql = "UPDATE Basic_User_Information SET interests_count='" . count($interests_string) . "' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($interests_count_sql);
				
				//Get rid of any duplicates within the array
				$interests_string = array_unique($interests_string);
				if (array_filter($interests_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($interests_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$interests_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($interests_add_sql);	
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
					$interests_delete_sql = "DELETE FROM User_Interests WHERE category=2&&fb_uid='" . $key['uid'] . "'";
					$result = mysql_query($interests_delete_sql);
			
					//Insert back all the activities into the users interests list
					$interests_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($interests_index as $key4)
					{
						if($i == 0)
							$interests_add_sql .= "('" . $key['uid'] . "', 2, " . $key4 . ")";
						else
							$interests_add_sql .= ",('" . $key['uid'] . "', 2, " . $key4 . ")";
						$i++;
					}
					$result = mysql_query($interests_add_sql) or die (mysql_error() . " - " . $interests_add_sql);				  
				} //end of if (array_filter($interests_string))						
			}
			if ($key2 == 'music')
			{
				$sql = "UPDATE Basic_User_Information SET about_me='changed second layer 1' WHERE fb_uid='" . $uid1 . "'";
				$result = mysql_query($sql);
				
				
				
				
				$fql = "select music from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
				$music_string = explode(', ', $fqlResult[0][music]); //3
				
				//Update the activities interest count
				$music_count_sql = "UPDATE Basic_User_Information SET music_count='" . count($music_string) . "' WHERE fb_uid='" . $user . "'";
				$result = mysql_query($music_count_sql);
		
				//Get rid of any duplicates within the array
				$music_string = array_unique($music_string);
				
				if (array_filter($music_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($music_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$music_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($music_add_sql);	
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
					$music_delete_sql = "DELETE FROM User_Interests WHERE category=3&&fb_uid='" . $user . "'";
					$result = mysql_query($music_delete_sql);
			
					//Insert back all the activities into the users interests list
					$music_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($music_index as $key4)
					{
						if($i == 0)
							$music_add_sql .= "('" . $user . "', 3, " . $key4 . ")";
						else
							$music_add_sql .= ",('" . $user . "', 3, " . $key4 . ")";
						$i++;
					}	
					$result = mysql_query($music_add_sql) or die (mysql_error() . " - " . $music_add_sql);
				} //end of if (array_filter($music_string)) 
				
				
				$sql = "UPDATE Basic_User_Information SET about_me='changed second layer 2' WHERE fb_uid='" . $uid1 . "'";
				$result = mysql_query($sql);
			}
			if ($key2 == 'tv')
			{
				$fql = "select tv from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
				$tv_string = explode(', ', $fqlResult[0][tv]); //4
				
				//Update the activities interest count
				$tv_count_sql = "UPDATE Basic_User_Information SET tv_count='" . count($tv_string) . "' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($tv_count_sql);
		
				//Get rid of any duplicates within the array
				$tv_string = array_unique($tv_string);
				
				if (array_filter($tv_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($tv_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$tv_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($tv_add_sql);	
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
					$activities_delete_sql = "DELETE FROM User_Interests WHERE category=4&&fb_uid='" . $key['uid'] . "'";
					$result = mysql_query($activities_delete_sql);
			
					//Insert back all the activities into the users interests list
					$tv_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($tv_index as $key4)
					{
						if($i == 0)
							$tv_add_sql .= "('" . $key['uid'] . "', 4, " . $key4 . ")";
						else
							$tv_add_sql .= ",('" . $key['uid'] . "', 4, " . $key4 . ")";
						$i++;
					}
					$result = mysql_query($tv_add_sql) or die (mysql_error() . " - " . $tv_add_sql);				  
				} //end of if (array_filter($tv_string))
			}
			if ($key2 == 'movies')
			{
				$fql = "select movies from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
		 
				$movies_string = explode(', ', $fqlResult[0][movies]); //5
				
				
				//Update the activities interest count
				$movies_count_sql = "UPDATE Basic_User_Information SET movies_count='" . count($movies_string) . "' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($movies_count_sql);
		
				//Get rid of any duplicates within the array
				$movies_string = array_unique($movies_string);
				if (array_filter($movies_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($movies_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$movies_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($movies_add_sql);	
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
					$movies_delete_sql = "DELETE FROM User_Interests WHERE category=5&&fb_uid='" . $key['uid'] . "'";
					$result = mysql_query($movies_delete_sql);
			
					//Insert back all the activities into the users interests list
					$movies_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($movies_index as $key4)
					{
						if($i == 0)
							$movies_add_sql .= "('" . $key['uid'] . "', 5, " . $key4 . ")";
						else
							$movies_add_sql .= ",('" . $key['uid'] . "', 5, " . $key4 . ")";
						$i++;
					}
					$result = mysql_query($movies_add_sql) or die (mysql_error() . " - " . $movies_add_sql);				  
				} //end of if (array_filter($movies_string))
			}
			if ($key2 == 'books')
			{
				$fql = "select books from user where uid=" . $user;
				//http://developers.facebook.com/docs/reference/fql/
				$param  =   array(
					'method'    => 'fql.query',
					'query'     => $fql,
					'callback'  => ''
				);
				$fqlResult   =   $facebook->api($param);
				$books_string = explode(', ', $fqlResult[0][books]); //6
				
				//Update the activities interest count
				$books_count_sql = "UPDATE Basic_User_Information SET books_count='" . count($books_string) . "' WHERE fb_uid='" . $key['uid'] . "'";
				$result = mysql_query($books_count_sql);
		
				//Get rid of any duplicates within the array
				$books_string = array_unique($books_string);
				
				if (array_filter($books_string))
				{
					// either add the new interest in the database if it's not already there, or record down the interests_id for the ones that are already there
					foreach ($books_string as $key3)
					{		
						$query = "SELECT interests_id FROM Interests_Lookup WHERE interest='" . addslashes($key3) . "'";
						$result = mysql_query($query);
						$num = mysql_num_rows($result);
						
						//service listing name not found
						if ($num == 0) 
						{
							//Insert back all the friends into the users friend list
							$books_add_sql = "INSERT INTO Interests_Lookup (interest) VALUES ('" . addslashes($key3) . "')";
							$result = mysql_query($books_add_sql);	
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
					$books_delete_sql = "DELETE FROM User_Interests WHERE category=6&&fb_uid='" . $key['uid'] . "'";
					$result = mysql_query($books_delete_sql);
			
					//Insert back all the activities into the users interests list
					$books_add_sql = "INSERT INTO User_Interests (fb_uid, category, interests_id) VALUES ";
					$i = 0;					  
					foreach ($books_index as $key4)
					{
						if($i == 0)
							$books_add_sql .= "('" . $key['uid'] . "', 6, " . $key4 . ")";
						else
							$books_add_sql .= ",('" . $key['uid'] . "', 6, " . $key4 . ")";
						$i++;
					}
					$result = mysql_query($books_add_sql) or die (mysql_error() . " - " . $books_add_sql);				  
					$refresh_action = 1;
				} //end of if (array_filter($books_string))
			}
		}
	}	

	error_log('updates = ' . print_r($updates, true)); 
	             
}
?>


