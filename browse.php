<?php		
	$page_title = "Browse Services";
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	include "featured_listings.inc";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";	
				
	if ($user)
	{
		$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$user'";
		$result = mysql_query($friendlist_sql) or die (fatal_error(190, $user, $user, $today, $friendlist_sql, mysql_error()));
		$num = mysql_num_rows($result);
		for ($i=0; $i<$num; $i++)
		{
			$friend_uid=mysql_result($result,$i,"uid2");
			$buyer_fb_friendlist[] = $friend_uid;
		}
		
		if ($_POST['location'])
		{
			$location_sql = "SELECT * FROM Location_Lookup WHERE location_id='" . $_POST['location'] . "'";
			$result = mysql_query($location_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
			$lnglat_row= mysql_fetch_array($result);
			$user_lng = $lnglat_row['lng'];
			$user_lat = $lnglat_row['lat'];
		}
		
		// use user's location as the centre of the search query location
		else
        {
			$address_sql = "SELECT area_code,street,lng,lat FROM User_Address WHERE fb_uid='$user'";
			$result = mysql_query($address_sql) or die (fatal_error(191, $user, $user, $today, $address_sql, mysql_error()));
			$lnglat_row = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_lng = $lnglat_row['lng'];
			$user_lat = $lnglat_row['lat'];
			$user_area_code = $lnglat_row['area_code'];
			$user_street = $lnglat_row['street'];  
        }
	
		// Distance Sort
		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE status=1 && fb_uid!='$user'";
		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(192, $user, $user, $today, $create_table_sql, mysql_error()));
		$num_result_check = mysql_query("SELECT * FROM result_table"); 
		
		$add_column_sql = "ALTER TABLE result_table ADD distance int(10), ADD city varchar(30), ADD hoody_sort int(5)";
		$add_clumn_result = mysql_query($add_column_sql)  or die (fatal_error(195, $user, $user, $today, $add_column_sql, mysql_error()));

		$sql = "SELECT listing_id,fb_uid,popularity FROM result_table";
		$r=mysql_query($sql) or die (fatal_error(196, $user, $user, $today, $sql, mysql_error()));
		
		while($w=mysql_fetch_array($r))
		{
			extract($w);	
			
			// Distance calculating
			$lnglat_sql = "SELECT listing_location,city,lng,lat,listing_range FROM Listing_Location WHERE listing_id='$listing_id'";
			$service_result = mysql_query($lnglat_sql) or die (fatal_error(197, $user, $user, $today, $lnglat_sql, mysql_error()));
			$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
			// if the service takes place at seller's home
			if ($service_row['listing_location'] == 0)
			{
				$sql = "SELECT lng,lat,city FROM User_Address WHERE fb_uid='$fb_uid'";
				$service_result = mysql_query($sql) or die (fatal_error(198, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
				
				// if the user lives within seller's range, update the distance/city		
				if ($distance < 100)
				{
					// hoody_sort
					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(199, $user, $user, $today, $sql, mysql_error()));
				}
				// if the user lives seller's range, take the listing off the table
				else
				{
					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(200, $user, $user, $today, $sql, mysql_error()));	
				}
			}	
			// if the service takes place at another location
			else if ($service_row['listing_location'] == 2)
			{	
				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
				// if the user lives within seller's range, update the distance/city		
				if ($distance < 100)
				{
					// hoody_sort
					if ($fb_uid == $user && $sort == "hoody")
						$hoody_sort = 0;
					else if ($sort == "hoody")
						$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(203, $user, $user, $today, $sql, mysql_error()));
				}
				// if the user lives seller's range, take the listing off the table
				else
				{
					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(204, $user, $user, $today, $sql, mysql_error()));	
				}
			}
			// if the service takes place at buyer's home
			else if ($service_row['listing_location'] == 1)
			{
				$sql = "SELECT lng,lat FROM User_Address WHERE fb_uid='$fb_uid'";
				$range_result = mysql_query($sql) or die (fatal_error(205, $user, $user, $today, $sql, mysql_error()));
				$range_row = mysql_fetch_array($range_result,MYSQL_ASSOC);				
				$range = (int)distance($user_lat,$user_lng,$range_row['lat'],$range_row['lng']);	
				// if the user lives outside of seller's range
				if ($range > $service_row['listing_range'])
				{	
					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(206, $user, $user, $today, $sql, mysql_error()));	
				}
				// if user lives within the seller's range 
				else
				{
					// hoody_sort
					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
				}
			}
		} // end of while($w=mysql_fetch_array($r))
	}	
	
	// if the user is not logged in
	else
	{
		// if the user is trying to access distance sort or hoody smart sort without being logged in, reset the sort to default sort
		if ($sort == "distance" || $sort == "hoody")
			$sort = "";
		
		if ($_POST['location'])
		{
			$location_sql = "SELECT * FROM Location_Lookup WHERE location_id='" . $_POST['location'] . "'";
			$result = mysql_query($location_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
			$lnglat_row= mysql_fetch_array($result);
			$user_lng = $lnglat_row['lng'];
			$user_lat = $lnglat_row['lat'];
		}
		
		// use user's location as the centre of the search query location
		else
        {
			$location_sql = "SELECT * FROM Location_Lookup WHERE location_id='1'";
			$result = mysql_query($location_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
			$lnglat_row= mysql_fetch_array($result);
			$user_lng = $lnglat_row['lng'];
			$user_lat = $lnglat_row['lat'];
        }
	
		// Distance Sort
		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview WHERE status=1";
		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(192, $user, $user, $today, $create_table_sql, mysql_error()));
		$num_result_check = mysql_query("SELECT * FROM result_table"); 
		$number=mysql_num_rows($num_result_check); 
		
		$add_column_sql = "ALTER TABLE result_table ADD distance int(10), ADD city varchar(30), ADD hoody_sort int(5)";
		$add_clumn_result = mysql_query($add_column_sql)  or die (fatal_error(195, $user, $user, $today, $add_column_sql, mysql_error()));

		$sql = "SELECT listing_id,fb_uid,popularity FROM result_table";
		$r=mysql_query($sql) or die (fatal_error(196, $user, $user, $today, $sql, mysql_error()));
		
		while($w=mysql_fetch_array($r))
		{
			extract($w);	
			
			// Distance calculating
			$lnglat_sql = "SELECT listing_location,city,lng,lat,listing_range FROM Listing_Location WHERE listing_id='$listing_id'";
			$service_result = mysql_query($lnglat_sql) or die (fatal_error(197, $user, $user, $today, $lnglat_sql, mysql_error()));
			$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
			// if the service takes place at seller's home
			if ($service_row['listing_location'] == 0)
			{
				$sql = "SELECT lng,lat,city FROM User_Address WHERE fb_uid='$fb_uid'";
				$service_result = mysql_query($sql) or die (fatal_error(198, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
				
				// if the user lives within seller's range, update the distance/city		
				//if ($distance < 100)
				{
					// hoody_sort
					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(199, $user, $user, $today, $sql, mysql_error()));
				}
			
			}		
			// if the service takes place at another location
			else if ($service_row['listing_location'] == 2)
			{	
				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
				// if the user lives within seller's range, update the distance/city		
				//if ($distance < 100)
				{
					// hoody_sort
					if ($fb_uid == $user && $sort == "hoody")
						$hoody_sort = 0;
					else if ($sort == "hoody")
						$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(203, $user, $user, $today, $sql, mysql_error()));
				}
				// if the user lives seller's range, take the listing off the table
			
			}
			// if the service takes place at buyer's home
			else if ($service_row['listing_location'] == 1)
			{
				$sql = "SELECT lng,lat FROM User_Address WHERE fb_uid='$fb_uid'";
				$range_result = mysql_query($sql) or die (fatal_error(205, $user, $user, $today, $sql, mysql_error()));
				$range_row = mysql_fetch_array($range_result,MYSQL_ASSOC);				
				$range = (int)distance($user_lat,$user_lng,$range_row['lat'],$range_row['lng']);	
				// if the user lives outside of seller's range
				if ($range > $service_row['listing_range'])
				{	
					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(206, $user, $user, $today, $sql, mysql_error()));	
				}
				// if user lives within the seller's range 
				else
				{
					// hoody_sort
					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
				}
			}
		} // end of while($w=mysql_fetch_array($r))
	}	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody: <?php echo $page_title; ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/service/"/>
    <meta property="og:image" content="http://img.gohoody.com/attachements/logo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="Hoody: <?php echo $page_title; ?>"/>
          
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary; ?>attachements/favicon.png" />
<title><?php print($page_title) ?></title>

<!--CSS Begins-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/browse.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<!--CSS Ends-->

<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Jockey+One' rel='stylesheet' type='text/css'>

<!--Javascript Begins-->
<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!--Notifications-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.gritter.js"></script>

<!--Javascript Ends-->
</head>
<body onload="initialize()">
<?php 
	flush(); 
	include "html/title_bar_new2.inc";  
?>

<div id="content">
    <div id='left_sect'>
          <div id='just_ask_section'>
        	<div id='just_ask_title'> Categories </div>
 			<a class='ask_link' href='<?php echo $working_directory; ?>ask/marketing-and-business-development-gta/'> Marketing and BizDev </a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/graphic-design-gta/'> Graphic Design </a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/web-development-and-programming-gta/'> Web Development and Programming </a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/computer-and-electronics-gta/'> Computers and Electronics </a>
			<a class='ask_link' href='<?php echo $working_directory; ?>ask/photography-gta/'> Photography </a>
			<a class='ask_link' href='<?php echo $working_directory; ?>ask/transportation-and-auto-gta/'> Trasportation and Auto </a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/fashion-and-beauty-gta/'> Fashion and Beauty</a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/academic-and-education-gta/'> Education </a>
            <a class='ask_link' href='<?php echo $working_directory; ?>ask/home-and-garden-gta/'> Home and Garden </a>
            <a class='more_link' href='<?php echo $working_directory; ?>search/'> see all </a>
        </div> <!--end#just_ask_section-->
    </div>  <!--end #left_sect-->
    
    <div id='right_sect'>
            <div id='featured_sect'>
                <div class='featured_title'>Featured Services <a class='see_more_title' href='<?php echo $working_directory; ?>search.php?sort=popularity'>see more</a></div>
        <?php 
		    $query = 	"SELECT title,price,pricing_model,listing_id,fb_uid FROM Listing_Overview WHERE 
						listing_id = '$raw_featured_1' || listing_id = '$raw_featured_2' || listing_id = '$raw_featured_3' || listing_id = '$raw_featured_4' || listing_id = '$raw_featured_5' || 
						listing_id = '$raw_featured_6' || listing_id = '$raw_featured_7' || listing_id = '$raw_featured_10' || 
						listing_id = '$raw_featured_11' || listing_id = '$raw_featured_12' || listing_id = '$raw_featured_13' || listing_id = '$raw_featured_14' || listing_id = '$raw_featured_15' || 
						listing_id = '$raw_featured_16' || listing_id = '$raw_featured_17' || listing_id = '$raw_featured_18' || listing_id = '$raw_featured_19' || listing_id = '$raw_featured_20' || 
						listing_id = '$raw_featured_21' || listing_id = '$raw_featured_22' || listing_id = '$raw_featured_23' || listing_id = '$raw_featured_24' || listing_id = '$raw_featured_25' || 
						listing_id = '$raw_featured_26' || listing_id = '$raw_featured_27' || listing_id = '$raw_featured_28' || listing_id = '$raw_featured_29' || listing_id = '$raw_featured_30' ORDER BY RAND() ";
            $features_result = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
        ?>
		<?php for ($counter = 1 ; $counter < 9 && $row= mysql_fetch_array($features_result) ; $counter++): ?>
        <?php
            // Extract pictures for the listing
            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
            $picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
            $url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
        ?>  
                    <div class='featured'>
                    <div class='featured_box'>
                        <div class='service_pic'>
                            <a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
                            <img src='<?php echo $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=125&w=158&zc=1"; ?>' height='125px' width='158px' alt=''/></a>
                        </div>
                        <div class='featured_service_info'>
                            <div class='service_name'><a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'><?php echo $row[title]; ?></a></div>
                            <div class='service_price'><?php
                                                                if ($row[price]==0)
                                                                    echo "FREE"; 
                                                                else if (!$row[pricing_model])
                                                                    echo "$".$row[price]." / job";
                                                                else if ($row[pricing_model])
                                                                    echo "$".$row[price]." / hour";
                                                        ?></div>
                        </div>
                    </div> <!--end #featured_box-->
                    <div class='box_shadow'>
                        <div class='landscape_shadow'><img src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' width='176px' alt=''/></div>
                    </div>
                </div><!--end #featured-->
        <?php endfor; //for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) ?>
            </div> <!--end#featured_sect-->
            
            
            <div id='map_sect'>
                <div class='featured_title'>Services Around You 
                
                <form name="location_form" id='location_dropdown_form' action="<?php echo curPageURL(); ?>" method="POST" >
                    <div id='dropdown_text'> Location: </div>
                    <select id='location_dropdown' name="location" onchange='this.form.submit()'>
                <?php if ($user): ?>
                        <option>Services near me</option>
                <?php endif; ?>
                <?php
                        $location_sql = "SELECT * FROM Location_Lookup";
                        $location_result = mysql_query($location_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
                ?>
                <?php	while ($row= mysql_fetch_array($location_result)): // begin to show results set, now you can display the results returned ?>
                <?php	extract($row); ?>
                        <option value="<?php echo $location_id; ?>" <?php	if ($_POST['location'] == $location_id) 
                                                                                echo "selected";
                                                                            else if (empty($_POST['location']) && empty($user) && $location_id == 1)
                                                                                echo "selected";
                                                                    ?>><?php echo $location_name; ?></option>
                <?php endwhile; //while ($row= mysql_fetch_array($result)) ?>
                    </select>
                </form>

                
                </div>
                
             

                
                <div id='map_box'>
                    <img id='map_shadow_top' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p4.png' alt=''/>
                    <!--<img id='map_static' src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $user_lat . ", " . $user_lng; ?>&zoom=12&size=740x600&sensor=false" />-->
                    <div id='map_canvas' style='width: 715px; height: 350px;'> </div>
                    <img id='map_loading' src="<?php echo $domain_secondary;?>attachements/loading_grey.gif" />
                    <img id='map_shadow' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p3.png' alt=''/>
                </div>
                
                <div id='map_list'>
                    <div id='list_box'>
                        
                    </div>
                    
                
                </div>
                
            
            </div>
            
            
		<?php if ($user): ?>
        <div id='at_home_sect'>
        	<div class='featured_title'>Services that come to you<a class='see_more_title' href='<?php echo $working_directory; ?>search.php?sort=distance'>see more</a></div>
		<?php 
			$query = "SELECT title,price,pricing_model,listing_id,fb_uid FROM result_table WHERE distance='-1' ORDER BY hoody_sort  DESC";
            $result1 = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
        ?>
        <?php for ($counter = 1 ; $counter < 11 && $row= mysql_fetch_array($result1) ; $counter++): ?>
        <?php
            // Extract pictures for the listing
            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
            $picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
            $url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
        ?>  
            <div class='recent'>
                <div class='recent_box'>
                    <div class='recent_service_pic'>
                        <a title="<?php echo $row[title]; ?>" href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
                            <img src='<?php echo $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=105&w=123&zc=1"; ?>' height='105px' width='123px' alt='<?php echo $row[title]; ?>' />
                        </a>
                    </div>
                </div> <!--end #recent_box-->
                <div class='box_shadow'>
                    <!--<div class='landscape_shadow'><img src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' width='176px' alt=''/></div>-->
                </div>
            </div><!--end #recent-->
        <?php endfor; //for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) ?>
        </div> <!--end#recent_sect-->
        <?php endif; ?>            
            
            
        <?php if ($user): ?>
        <div id='recommanded_sect'>
        	<div class='featured_title'>Recommanded for you <a class='see_more_title' href='<?php echo $working_directory; ?>search.php?sort=hoody'>see more</a></div>
		<?php 
			$query = "SELECT title,price,pricing_model,listing_id,fb_uid FROM result_table ORDER BY hoody_sort  DESC";
            $result1 = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
        ?>
        <?php for ($counter = 1 ; $counter < 9 && $row= mysql_fetch_array($result1) ; $counter++): ?>
        <?php
            
			
			$common_activities_list = common_check($row[fb_uid], $user, 1);
			$common_interests_list = common_check($row[fb_uid], $user, 2);
			$common_music_list = common_check($row[fb_uid], $user, 3);
			$common_tv_list = common_check($row[fb_uid], $user, 4);
			$common_movies_list = common_check($row[fb_uid], $user, 5);
			$common_books_list = common_check($row[fb_uid], $user, 6);
			$common_friend_list = common_check($row[fb_uid], $user, 9);
			
			
			
			
			
			
			// Extract pictures for the listing
            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
            $picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
            $url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
        ?>  
            <div class='recommend'>
                <div class='recommend_box'>
                    <div class='recommend_service_pic'>
                        <a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
                            <img src='<?php echo $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=125&w=157&zc=1"; ?>' height='125px' width='157px' alt=''/>
                        </a>
                    </div>
                    
                    <div class='recommend_service_title'>
                    	<div class='service_name'><a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'><?php echo $row[title]; ?></a></div>
                    </div>
                    
		<?php
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT pic_square,name,first_name FROM Basic_User_Information WHERE fb_uid=" . $row[fb_uid];
			$result = mysql_query($service_sql) or die (fatal_error(256, $user, $user, $today, $service_sql, mysql_error()));
			$seller_row = mysql_fetch_array($result,MYSQL_ASSOC);
			
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid=" . $row[fb_uid];
			$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$user_profile_row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		
		?>                    
                    
                    
                    
                    <div class='recommend_seller_info'> 
                        <div class='recommend_seller_pic'>
                            <a href='<?php echo $working_directory . "profile/" . $user_profile_row['profile_name'] . "/"; ?>'>
                                <img src='<?php echo $seller_row['pic_square']; ?>' height='30px' width='30px' alt=''/>
                            </a>
                        </div>
                        <div class='recommend_seller_name'>
							<a href='<?php echo $working_directory . "profile/" . $user_profile_row['profile_name'] . "/"; ?>'><?php echo $seller_row['name']; ?></a>
                        </div>
                    </div>    
                    
                </div> <!--end #recent_box-->
               
                <div class='recommend_info'>
                
                    
                    <div class='recommend_info_title'> You and seller </div>    
                    
                    <div class='recommend_commons'>
                    
                    	<?php
							$num_of_interests = count($common_activities_list) + count($common_interests_list) + count($common_music_list) + 
							count($common_tv_list) + count($common_movies_list) + count($common_books_list);
							if ($num_of_interests > 0) 
							{
								if ($num_of_interests == 1) 
								{
									echo " <div class='recommend_interests'> <img class='interest_icon' src='http://dev.gohoody.com/attachements/fb_hand.png'> <em>" . $num_of_interests . "</em> common interest </div>" ;
								}
								else
								{
									echo " <div class='recommend_interests'> <img class='interest_icon' src='http://dev.gohoody.com/attachements/fb_hand.png'> <em>" . $num_of_interests . "</em> common interests </div>";
								}
							}
							if (count($common_friend_list) > 0) 
							{
								if (count($common_friend_list) == 1) 
								{
									echo "<div class='recommend_friends'> <img class='interest_icon' src='http://dev.gohoody.com/attachements/fb_friend.png'> <em>" . count($common_friend_list) . "</em> mutual friend </div>" ;
								}
								else
								{
									echo "<div class='recommend_friends'> <img class='interest_icon' src='http://dev.gohoody.com/attachements/fb_friend.png'> <em>" . count($common_friend_list) . "</em> mutual friends </div>";
								}
							}
						?>
									
				
                    </div> <!--end #recommend_commons-->
                
					<!--<?php if (count($common_activities_list) > 0) echo count($common_activities_list) . " commmon favourite activities";?>
                    <?php if (count($common_interests_list) > 0) echo count($common_interests_list) . " commmon interests";?>
                    <?php if (count($common_music_list) > 0) echo count($common_music_list) . " commmon favourite music";?>
                    <?php if (count($common_tv_list) > 0) echo count($common_tv_list) . " commmon favourite tv shows";?>
                    <?php if (count($common_movies_list) > 0) echo count($common_movies_list) . " commmon favourite movies";?>
                    <?php if (count($common_books_list) > 0) echo count($common_books_list) . " commmon favourite books";?>
                    <?php if (count($common_friend_list) > 0) echo count($common_friend_list) . " commmon friends";?>-->
            	</div>

            </div><!--end #recommend-->
            
            
           
            
        <?php endfor; //for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) ?>
        </div> <!--end#recent_sect-->
        <?php endif; ?>    
            
            
            
            
            
    	<div id='featured_seller_sect'>
        	<div class='featured_title'>Featured Sellers</div>
            <div class='featured_seller' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/nelson/'><img src='<?php echo $domain_secondary;?>attachements/UofT/nelson.jpg' width='130px' height='130px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/nelson/'>Nelson Wu</a></div>
                <div class='seller_info'>Crazy About Apple, Motor Racing, and Polar Bears</div>
                <div class='indv_service'>
                	<a title='Computer Tune-up' href='<?php echo $working_directory; ?>service/385/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305759031-3446271885_b3ee5cb8ae_z.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a  title='Digital SLR Photography Course for Beginners' href='<?php echo $working_directory; ?>service/387/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305761668-2164996776_1f03516e69_z.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Computer Networking Setup' href='<?php echo $working_directory; ?>service/384/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305758479-3449116183_758f523471_z.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            <div class='featured_seller' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/mike/'><img src='<?php echo $domain_secondary;?>attachements/UofT/mike.jpg' width='130px' height='130px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/mike/'>Mike Tang</a></div>
                <div class='seller_info'>Tech-geek, entrepreneur, risk-taker, and drinker of tea.</div>
                <div class='indv_service'>
                	<a title='Smartphone/Tablet Setup' href='<?php echo $working_directory; ?>service/398/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1306082589-photo%201.JPG&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Editing service for documents and reports' href='<?php echo $working_directory; ?>service/600/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315212701-trial-hero-20090106.png&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Singing Buddy' href='<?php echo $working_directory; ?>service/388/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305778479-Mic.JPG&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            
            <div class='featured_seller' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/annayip/'><img src='<?php echo $domain_secondary;?>attachements/UofT/anna.jpg' width='130px' height='130px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/annayip/'>Anna Yip</a></div>
                <div class='seller_info'>I'm a student in marketing</div>
                <div class='indv_service'>
                	<a title='Print media design' href='<?php echo $working_directory; ?>service/568/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315107542-die-cut-brochure-design.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Personal Shopper ;)' href='<?php echo $working_directory; ?>service/570/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315108768-rome-personal-shopper.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            <div class='featured_seller' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/ivan/'><img src='<?php echo $domain_secondary;?>attachements/UofT/ivan.jpg' width='130px' height='130px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/ivan/'>Ivan Kostynyk</a></div>
                <div class='seller_info'>Graphic design student at OCADU. Passtionate about Graphic Design and Tech</div>
                <div class='indv_service'>
                	<a title='Graphic Design/Typography' href='<?php echo $working_directory; ?>service/472/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1308020348-ON-typeface.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                 </div>
                <div class='indv_service'>
                	<a title='Concept Industrial Design' href='<?php echo $working_directory; ?>service/571/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1314934349-258303_10150336858742837_634717836_9979495_3990650_o.jpg&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                 </div>
                <div class='indv_service'>
                	<a title='Custom Typography'  href='<?php echo $working_directory; ?>service/583/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315099254-IMG_2303.JPG&h=50&w=50&zc=1' width='50px' height='50px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
        </div> <!--end #featured_seller_sect-->
		
		<div id='recent_sect'>
        	<div class='featured_title'>Recently Listed Services <a class='see_more_title' href='<?php echo $working_directory; ?>search.php?sort=date'>see more</a></div>
		<?php 
			$query = "SELECT title,price,pricing_model,listing_id,fb_uid FROM result_table ORDER BY listed_time  DESC";
            $result1 = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
        ?>
        <?php for ($counter = 1 ; $counter < 11 && $row= mysql_fetch_array($result1) ; $counter++): ?>
        <?php
            // Extract pictures for the listing
            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
            $picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
            $url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
        ?>  
            <div class='recent'>
                <div class='recent_box'>
                    <div class='recent_service_pic'>
                        <a title="<?php echo $row[title]; ?>" href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
                            <img src='<?php echo $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=105&w=123&zc=1"; ?>' height='105px' width='123px' alt=''/>
                        </a>
                    </div>
                </div> <!--end #recent_box-->
                <div class='box_shadow'>
                    <!--<div class='landscape_shadow'><img src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' width='176px' alt=''/></div>-->
                </div>
            </div><!--end #recent-->
        <?php endfor; //for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) ?>
        </div> <!--end#recent_sect-->
        
        
        
    

        
        
        
        
        
        
        
    </div>	<!--end#right_sect-->
</div> <!--end of #content-->
<?php include "html/footer.inc"; ?>

<!-- Google Maps -->
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    // Create the map 
    // No need to specify zoom and center as we fit the map further down.
    var map = new google.maps.Map(document.getElementById("map_canvas"), {
      zoom: 12,
    center: new google.maps.LatLng(<?php echo $user_lat . ", " . $user_lng; ?>),
	  mapTypeId: google.maps.MapTypeId.ROADMAP,
      streetViewControl: false,
	  	  mapTypeControl: false,
		  scrollwheel: false,
    });
 
    // Create the shared infowindow with two DIV placeholders
    // One for a text string, the other for the StreetView panorama.
    var content = document.createElement("DIV");
    var title = document.createElement("DIV");
    content.appendChild(title);
    var infowindow = new google.maps.InfoWindow({
   content: content
    });

    // Define the list of markers.
    // This could be generated server-side with a script creating the array.
    var markers = [
<?php 
	$query = 	"SELECT title,price,pricing_model,listing_id,fb_uid FROM result_table WHERE distance!='-1' && distance < 1000 ORDER BY hoody_sort  DESC";
	$features_result = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
	
	for ($i=1; $row= mysql_fetch_array($features_result); $i++)
	{
		// Extract pictures for the listing
		$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
		$picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
		$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
		$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
		$url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
		$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
		
		
		if ($row[price]==0)
			$price = "FREE"; 
		else if (!$row[pricing_model])
			$price =  "$".$row[price]." / job";
		else if ($row[pricing_model])
			$price =  "$".$row[price]." / hour";
        
		// Extract pictures for the listing
		$lnglat_sql = "SELECT * FROM Listing_Location WHERE listing_id=" . $row[listing_id];
		$lnglat_result = mysql_query($lnglat_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
		$lnglat_row = mysql_fetch_array($lnglat_result,MYSQL_ASSOC);
		if ($lnglat_row[listing_location] == 0)
		{
			$lnglat_sql = "SELECT * FROM User_Address WHERE fb_uid=" . $row[fb_uid];
			$lnglat_result = mysql_query($lnglat_sql) or die (fatal_error(260, $user, $user, $today, $sql, mysql_error()));
			$lnglat_row = mysql_fetch_array($lnglat_result,MYSQL_ASSOC);
			echo   "{	name: '<div class=\"map_cont\"> <a class=\"map_popup_link\" title=\"" . $row[title] . "\" href=\"" . $working_directory . "service/" . $row[listing_id] . "/\">" .
									"<img class=\"map_popup_img\" src=\"" . $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=100&w=120&zc=1\"" .  
									"height=\"100px\"/><p class=\"map_service_title\">" . $row[title] . 
								"</p></a><p>" . $price . "</p></div>', " .
						"lat: " . $lnglat_row[lat] . "," . 
						"lng: " . $lnglat_row[lng] . "},";
		}
		// if the service takes place at another location
		else if ($lnglat_row[listing_location] == 2)
			echo   "{	name: '<div class=\"map_cont\"> <a class=\"map_popup_link\" title=\"" . $row[title] . "\" href=\"" . $working_directory . "service/" . $row[listing_id] . "/\">" .
									"<img class=\"map_popup_img\" src=\"" . $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=100&w=120&zc=1\"" .  
									"height=\"100px\"/><p class=\"map_service_title\">" . $row[title] . 
								"</p></a><p>" . $price . "</p></div>', " .
						"lat: " . $lnglat_row[lat] . "," . 
						"lng: " . $lnglat_row[lng] . "},";
	}		
?>  	 					
    ];
	
	  var image = new google.maps.MarkerImage('<?php echo $domain_secondary;?>attachements/map_marker.png',
		new google.maps.Size(40,66),
		new google.maps.Point(0,0),
		new google.maps.Point(20,66));
  var shadow = new google.maps.MarkerImage('<?php echo $domain_secondary;?>attachements/map_marker_shadow.png',
		new google.maps.Size(76,66),
		new google.maps.Point(0,0),
		new google.maps.Point(20,66));
  var shape = {
  				coord: [26,0,28,1,30,2,31,3,33,4,34,5,35,6,35,7,36,8,37,9,37,10,38,11,38,12,38,13,39,14,39,15,39,16,39,17,39,18,39,19,39,20,39,21,39,22,39,23,39,24,39,25,38,26,38,27,38,28,37,29,37,30,36,		31,35,32,34,33,33,34,32,35,31,36,29,37,28,38,25,39,22,40,22,41,22,42,22,43,22,44,22,45,22,46,22,47,22,48,22,49,22,50,22,51,22,52,22,53,22,54,22,55,22,56,22,57,22,58,22,59,22,60,21,61,21,62,21,63,21,64,20,65,19,65,18,64,18,63,18,62,18,61,18,60,18,59,18,58,18,57,18,56,17,55,17,54,17,53,17,52,17,51,17,50,17,49,17,48,17,47,17,46,17,45,17,44,17,43,17,42,17,41,17,40,14,39,12,38,10,37,8,36,7,35,6,34,5,33,4,32,3,31,3,30,2,29,2,28,1,27,1,26,1,25,0,24,0,23,0,22,0,21,0,20,0,19,0,18,0,17,0,16,0,15,0,14,1,13,1,12,2,11,2,10,3,9,3,8,4,7,5,6,6,5,7,4,8,3,9,2,11,1,14,0,26,0],
  				type: 'poly'
};
    // Create the markers
    for (index in markers) addMarker(markers[index]);
    function addMarker(data) {
   var marker = new google.maps.Marker({
  position: new google.maps.LatLng(data.lat, data.lng),
  map: map,
        title: data.name,
        shadow: shadow,
        icon: image,
        shape: shape,
   });
   google.maps.event.addListener(marker, "click", function() {
  openInfoWindow(marker);
   });
    }

    // Handle the DOM ready event to create the StreetView panorama
    // as it can only be created once the DIV inside the infowindow is loaded in the DOM.
    var panorama = null;
    var pin = new google.maps.MVCObject();
    google.maps.event.addListenerOnce(infowindow, "domready", function() {
      panorama.bindTo("position", pin);
    });
    
    // Set the infowindow content and display it on marker click.
    // Use a 'pin' MVCObject as the order of the domready and marker click events is not garanteed.
    function openInfoWindow(marker) {
   title.innerHTML = marker.getTitle();
   pin.set("position", marker.getPosition());
   infowindow.open(map, marker);
    }
  }
</script>


</body>
</html>
    
