<?php
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
//	//Link to Facebook PHP SDK
//    include "php/fbmain.php";
//    $config['baseurl']  =   $working_directory. "index.php";		

	$limit=20; 

	if($_GET['lastid'])
	{
		$category_id = @$_GET['c'] ;
//		$var = @$_GET['q'] ;
//		$var = mysql_real_escape_string($var);
//		$sort = @$_GET['sort'];	
//		$trimmed = trim($var); //trim whitespace from the stored variable
//		$trimmed_array = explode(" ",$trimmed);
//	
//		if ($user)
//		{
//			// Check if the seller is a Facebook friend
//			// Request for buyer's Facebook Friendlist
//			$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$user'";
//			$result = mysql_query($friendlist_sql) or die (minor_error(221, $user, $user, $today, $url_sql, mysql_error()));
//			$num = mysql_num_rows($result);
//			for ($i=0; $i<$num; $i++)
//			{
//				$friend_uid=mysql_result($result,$i,"uid2");
//				$buyer_fb_friendlist[] = $friend_uid;
//			}
//				
//			$address_sql = "SELECT area_code,street,lng,lat FROM User_Address WHERE fb_uid='$user'";
//			$result = mysql_query($address_sql) or die (minor_error(222, $user, $user, $today, $url_sql, mysql_error()));
//			$lnglat_row = mysql_fetch_array($result,MYSQL_ASSOC);
//			$user_lng = $lnglat_row['lng'];
//			$user_lat = $lnglat_row['lat'];
//			$user_area_code = $lnglat_row['area_code'];
//			$user_street = $lnglat_row['street'];  
//		
//			// Distance Sort
//			$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE (title LIKE '%$trimmed%' OR listing_description like '%$trimmed%' OR listing_id like '%$trimmed%') AND status=1";
//			$create_table_result = mysql_query($create_table_sql)  or die (minor_error(223, $user, $user, $today, $url_sql, mysql_error()));
//			$num_result_check = mysql_query("SELECT * FROM result_table"); 
//			$number=mysql_num_rows($num_result_check); 
//			
//			// Show all listings if search result returns 0
//			if ($number == 0)
//			{
//				$drop_temp_table_sql = "DROP TABLE result_table";
//				$drop_table_result = mysql_query($drop_temp_table_sql)  or die (minor_error(224, $user, $user, $today, $url_sql, mysql_error()));
//				$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE status=1";
//				$create_table_result = mysql_query($create_table_sql)  or die (minor_error(4, $user, $user, $today, $create_table_sql, mysql_error()));
//			}
//		
//			$add_column_sql = "ALTER TABLE result_table ADD distance int(10), ADD city varchar(30), ADD hoody_sort int(5)";
//			$add_clumn_result = mysql_query($add_column_sql)  or die (minor_error(225, $user, $user, $today, $url_sql, mysql_error()));
//	
//			$sql = "SELECT listing_id,fb_uid,popularity FROM result_table";
//			$r=mysql_query($sql) or die (error_page(605));
//			while($w=mysql_fetch_array($r))
//			{
//				extract($w);	
//			
//				// Distance calculating
//				$lnglat_sql = "SELECT listing_location,city,lng,lat,listing_range FROM Listing_Location WHERE listing_id='$listing_id'";
//				$service_result = mysql_query($lnglat_sql) or die (minor_error(226, $user, $user, $today, $url_sql, mysql_error()));
//				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
//				// if the service takes place at seller's home
//				if ($service_row['listing_location'] == 0 && $fb_uid != $user)
//				{
//					$sql = "SELECT lng,lat,city FROM User_Address WHERE fb_uid='$fb_uid'";
//					$service_result = mysql_query($sql) or die (minor_error(227, $user, $user, $today, $url_sql, mysql_error()));
//					$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
//					$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
//					
//					// if the user lives within seller's range, update the distance/city		
//					if ($distance < 100)
//					{
//						// hoody_sort
//						$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//						// service location
//						$city = $service_row['city'];
//						$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(228, $user, $user, $today, $url_sql, mysql_error()));
//					}
//					// if the user lives seller's range, take the listing off the table
//					else
//					{
//						$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(229, $user, $user, $today, $url_sql, mysql_error()));				
//					}		
//				}
//				// if the service takes place at seller's home, and the user is the seller
//				else if ($service_row['listing_location'] == 0 && $fb_uid == $user)
//				{
//					$sql = "SELECT city FROM User_Address WHERE fb_uid='$fb_uid'";
//					$service_result = mysql_query($sql) or die (minor_error(230, $user, $user, $today, $url_sql, mysql_error()));
//					$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
//					
//					$sql = "UPDATE result_table SET distance ='0',hoody_sort = '0' WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (minor_error(231, $user, $user, $today, $url_sql, mysql_error()));	
//				}		
//				// if the service takes place at another location
//				else if ($service_row['listing_location'] == 2)
//				{	
//					$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
//					// if the user lives within seller's range, update the distance/city		
//					if ($distance < 100)
//					{
//						// hoody_sort
//						if ($fb_uid == $user && $sort == "hoody")
//							$hoody_sort = 0;
//						else if ($sort == "hoody")
//							$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//						// service location
//						$city = $service_row['city'];
//						$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(232, $user, $user, $today, $url_sql, mysql_error()));
//					}
//					// if the user lives seller's range, take the listing off the table
//					else
//					{
//						$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(233, $user, $user, $today, $url_sql, mysql_error()));				
//					}
//				}
//				// if the service takes place at buyer's home
//				else if ($service_row['listing_location'] == 1 && $fb_uid != $user)
//				{
//					$sql = "SELECT lng,lat FROM User_Address WHERE fb_uid='$fb_uid'";
//					$range_result = mysql_query($sql) or die (minor_error(234, $user, $user, $today, $url_sql, mysql_error()));
//					$range_row = mysql_fetch_array($range_result,MYSQL_ASSOC);				
//					$range = (int)distance($user_lat,$user_lng,$range_row['lat'],$range_row['lng']);	
//					// if the user lives outside of seller's range
//					if ($range > $service_row['listing_range'])
//					{	
//						$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(235, $user, $user, $today, $url_sql, mysql_error()));	
//					}
//					// if user lives within the seller's range 
//					else
//					{
//						// hoody_sort
//						$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//						// service location
//						$city = $service_row['city'];
//						$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//						$update_result = mysql_query ($sql)  or die (minor_error(236, $user, $user, $today, $url_sql, mysql_error()));
//					}
//				}
//				else if ($service_row['listing_location'] == 1 && $fb_uid == $user)
//				{
//					// service location
//					$city = $service_row['city'];
//					$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '0' WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (minor_error(237, $user, $user, $today, $url_sql, mysql_error()));
//				}
//			} 
//		}		
//		
//		else
		{
			$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview WHERE category_id='$category_id' AND status=1";
			$create_table_result = mysql_query($create_table_sql)  or die (minor_error(238, $user, $user, $today, $url_sql, mysql_error()));					
			$add_column_sql = "ALTER TABLE result_table ADD city varchar(30)";
			$add_clumn_result = mysql_query($add_column_sql)  or die (minor_error(239, $user, $user, $today, $url_sql, mysql_error()));
			
			$sql = "SELECT listing_id,fb_uid,city FROM result_table";
			$r=mysql_query($sql) or die (error_page(614));
			while($w=mysql_fetch_array($r))
			{
				extract($w);	
				$lnglat_sql = "SELECT listing_location,city FROM Listing_Location WHERE listing_id='$listing_id'";
				$service_result = mysql_query($lnglat_sql) or die (minor_error(240, $user, $user, $today, $url_sql, mysql_error()));
				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
				$city = $service_row['city'];
				// if the service takes place at seller's home
				if ($service_row['listing_location'] == 0)
				{
					$sql = "SELECT city FROM User_Address WHERE fb_uid='$fb_uid'";
					$service_result = mysql_query($sql) or die (minor_error(241, $user, $user, $today, $url_sql, mysql_error()));
					$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);	
					$city = $service_row['city'];
				}
				else if ($service_row['listing_location'] == 1)
					$city = "home";	
						
				$sql = "UPDATE result_table SET city = '$city' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ( $sql )  or die (minor_error(242, $user, $user, $today, $url_sql, mysql_error()));
			} 
		}		
		
        // Sort - default sort by date
        if ($sort == "popularity")
            $query = 'SELECT * FROM result_table WHERE popularity < "'.$_GET['lastid'].'" ORDER BY popularity  DESC' ; 
        else if ($sort == "distance")
            $query = 'SELECT * FROM result_table WHERE distance > "'.$_GET['lastid'].'" ORDER BY distance  ASC' ; 
		else if ($sort == "hoody")
            $query = 'SELECT * FROM result_table WHERE hoody_sort < "'.$_GET['lastid'].'" ORDER BY hoody_sort  DESC' ;
        else
            $query = 'SELECT * FROM result_table WHERE listed_time < "'.$_GET['lastid'].'" ORDER BY listed_time  DESC'; 
        
        // next determine if 's' has been passed to script, if not use 0.
        // 's' is a variable that gets set as we navigate the search result pages.
        if (empty($s)) 
            $s=0;
    
        // get results
        $query .= " limit $s,$limit";
        $result = mysql_query($query) or die(minor_error(4, $user, $user, $today, $url_sql, mysql_error()));	
	
		// begin to show results set
		// now you can display the results returned
		while ($row= mysql_fetch_array($result)) 
		{
			extract($row);
			$location = "";
			$degree_separation = "";					
			if ($user)
			{	
				if ($distance == -1)
					$location = "Buyer's home";
				else
				{
					$location = $city;
				}
				if ($distance != -1 && ($user_area_code != NULL || $user_street != NULL))
					$location = $location . "<p class='distance_text'> (" . $distance . "km away)</p>";	
			}
			else
			{
				if ($city == "home")
					$location = "Buyer's home";
				else
				{
					$location = $city ;
				}			
			}
			
			// Add a thumbnail picture for each service
			// Extract pictures for the listing
			$picture_sql = "SELECT picture_id_1,picture_count FROM Listing_Pictures WHERE listing_id='$listing_id'";
			$picture_result = mysql_query($picture_sql) or die (minor_error(243, $user, $user, $today, $url_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			if ($picture_row != NULL)
			{
				extract($picture_row);
	
				if ($picture_count != 0)
				{
					$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
					$url_result = mysql_query($url_sql) or die (minor_error(244, $user, $user, $today, $url_sql, mysql_error()));
					$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
					extract($url_row);
				}		
			}
			
			echo 	"<tr onclick='go_to_service(\"" . $working_directory . "service/" . $listing_id . "/ \");' onmouseover='highlight(\"#title_" .$listing_id . "\");' onmouseout='unhighlight(\"#title_" .$listing_id . "\");'>
					<th>
					  <div class='pic_container' id='";
			if ($sort == "popularity")
				echo $popularity; 
			else if ($sort == "distance")
				echo $distance; 
			else if ($sort == "hoody")
				echo $hoody_sort; 
			else
				echo $listed_time; 			  				  
			echo	"'>
						<div class='picture'>
						  <a  href=\"" . $working_directory . "service/$listing_id/\">";
			if($URL == "hoodylogo.jpg")
				echo		'<img src="http://img.gohoody.com/resizer.php?src=http://img.gohoody.com/service_pictures/hoodylogo2.jpg&h=75&w=75&zc=1"/>';
            else
            	echo		'<img src="http://img.gohoody.com/resizer.php?src=http://img.gohoody.com/service_pictures/' . $URL . '&h=75&w=75&zc=1"/>';
			echo	"	  </a>
						</div>
					  </div>
					</th>
					<td class='info_cell' id='title_" . $listing_id. "'><p class='service_info'><a  href=\"" . $working_directory . "service/$listing_id/\">$title</a><p></td>
					<td class='info_cell'><p class='service_info' >$location</p></td>
					<td class='info_cell' id='price_column'><p class='service_info' id='price_column_content' >";	
			if ($price==0)
				echo "FREE";
			else
			{
				echo "$".$price;
				if ($pricing_model==0)
					echo " / hr";
				else 
					echo " / job";	
			}			
			echo	"</p></td>
					
					</tr>";
		}
		
	}
?>