<?php
	$page_title = "Listing Page";
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	include "featured_listings.inc";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";
			
	// Get the search variable from URL
  	$var = @$_GET['q'] ;
	$var = mysql_real_escape_string($var);
	$sort = @$_GET['sort'];
	$s = (int) $_GET['s'];
  	$trimmed = trim($var); //trim whitespace from the stored variable
	$trimmed_array = explode(" ",$trimmed);
	
	// set default sort to date
	if($sort == "")
		$sort = "date";

	// rows to return
	$limit=20; 
		
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
		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE (title LIKE '%$trimmed%' OR listing_description like '%$trimmed%' OR listing_id like '%$trimmed%') AND status=1";
		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(192, $user, $user, $today, $create_table_sql, mysql_error()));
		$num_result_check = mysql_query("SELECT * FROM result_table"); 
		$number=mysql_num_rows($num_result_check); 
		
		// Show all listings if search result returns 0
		if ($number == 0)
		{
			$drop_temp_table_sql = "DROP TABLE result_table";
			$drop_table_result = mysql_query($drop_temp_table_sql)  or die (fatal_error(193, $user, $user, $today, $drop_temp_table_sql, mysql_error()));
			$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE status=1";
			$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(194, $user, $user, $today, $create_table_sql, mysql_error()));
		}
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
			if ($service_row['listing_location'] == 0 && $fb_uid != $user)
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
			// if the service takes place at seller's home, and the user is the seller
			else if ($service_row['listing_location'] == 0 && $fb_uid == $user)
			{
				$sql = "SELECT city FROM User_Address WHERE fb_uid='$fb_uid'";
				$service_result = mysql_query($sql) or die (fatal_error(201, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
				
				$sql = "UPDATE result_table SET distance ='0',hoody_sort = '0' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ($sql)  or die (fatal_error(202, $user, $user, $today, $sql, mysql_error()));	
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
			else if ($service_row['listing_location'] == 1 && $fb_uid != $user)
			{
				$sql = "SELECT lng,lat FROM User_Address WHERE fb_uid='$fb_uid'";
				$range_result = mysql_query($sql) or die (fatal_error(205, $user, $user, $today, $sql, mysql_error()));
				$range_row = mysql_fetch_array($range_result,MYSQL_ASSOC);				
				$distance = (int)distance($user_lat,$user_lng,$range_row['lat'],$range_row['lng']);	
				// if the user lives outside of seller's range
				if ($distance > $service_row['listing_range'])
				{	
					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(206, $user, $user, $today, $sql, mysql_error()));	
				}
				// if user lives within the seller's range 
				else
				{
					// hoody_sort
					$hoody_sort = hoody_sort($user, $fb_uid, -1, $popularity);
					// service location
					$city = $service_row['city'];
					$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
				}
			}
			else if ($service_row['listing_location'] == 1 && $fb_uid == $user)
			{
				// service location
				$city = $service_row['city'];
				$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '0' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ($sql)  or die (fatal_error(208, $user, $user, $today, $sql, mysql_error()));
			}
			
			
			
			
			
			
			
			
			// if the service takes place remotely
			else if ($service_row['listing_location'] == 3 && $fb_uid != $user)
			{
				
				// hoody_sort
				$hoody_sort = hoody_sort($user, $fb_uid, -1, $popularity);
				// service location
				$city = $service_row['city'];
				$sql = "UPDATE result_table SET distance ='-2', city = 'virtual', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
				
			}
			else if ($service_row['listing_location'] == 3 && $fb_uid == $user)
			{
				// service location
				$city = $service_row['city'];
				$sql = "UPDATE result_table SET distance ='-2', city = 'virtual', hoody_sort = '0' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ($sql)  or die (fatal_error(208, $user, $user, $today, $sql, mysql_error()));
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
		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE (title LIKE '%$trimmed%' OR listing_description like '%$trimmed%' OR listing_id like '%$trimmed%') AND status=1";
		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(192, $user, $user, $today, $create_table_sql, mysql_error()));
		$num_result_check = mysql_query("SELECT * FROM result_table"); 
		$number=mysql_num_rows($num_result_check); 
		
		// Show all listings if search result returns 0
		if ($number == 0)
		{
			$drop_temp_table_sql = "DROP TABLE result_table";
			$drop_table_result = mysql_query($drop_temp_table_sql)  or die (fatal_error(193, $user, $user, $today, $drop_temp_table_sql, mysql_error()));
			$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE status=1";
			$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(194, $user, $user, $today, $create_table_sql, mysql_error()));
		}
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
					$sql = "UPDATE result_table SET distance ='-1', city = 'home', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
					$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
				}
			}
			
			
			
			
			// if the service takes place at buyer's home
			else if ($service_row['listing_location'] == 3)
			{
				// hoody_sort
				$hoody_sort = hoody_sort($user, $fb_uid, -1, $popularity);
				// service location
				$city = $service_row['city'];
				$sql = "UPDATE result_table SET distance ='-2', city = 'virtual', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
				$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
			}
			
			
			
			
			
		} // end of while($w=mysql_fetch_array($r))
	}		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody: Listing Page"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/search/"/>
    <meta property="og:image" content="http://img.gohoody.com/attachements/logo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="Hoody: Listing Page"/>
          
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php print($page_title) ?></title>

<!--CSS Begins-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/service_listings.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<!--CSS Ends-->

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

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

<!--AJAX inline load-->
<script type="text/javascript">
	$(document).ready(function(){
		$("#loadmorebutton").click(function (){
			$('#loadmorebutton').html('<img src="<?php echo $domain_secondary;?>attachements/search/ajax-loader.gif" />');
			$.ajax({
				url: "loadmore.php?lastid=" + $(".pic_container:last").attr("id") + "&q=<?php echo $var; ?>&sort=<?php echo $sort; ?>",
				success: function(html){
					if(html){
						$("#auto_load").append(html);
						$('#loadmorebutton').html('Load More');
					}else{
						$('#loadmorebutton').replaceWith('<center>No more services to show.</center>');}}});});});
	function go_to_service(url){
		window.location = url;}
	function highlight(listing_name){
		$(listing_name).css('text-decoration','underline');}
	function unhighlight(listing_name){
		$(listing_name).css('text-decoration','none');}

//Google Maps
function initialize() {
  var myLatlng = new google.maps.LatLng(<?php echo $user_lat . "," . $user_lng; ?>);
  var myOptions = {
    zoom: 10,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	mapTypeControl: false,
	scrollwheel: false,
	streetViewControl: false,}
  var map = new google.maps.Map(document.getElementById("location_map"), myOptions);
  var image = '<?php echo $domain_secondary;?>attachements/map_marker.png';
  var myLatLng = new google.maps.LatLng(<?php echo $user_lat . "," . $user_lng; ?>);
  var beachMarker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      icon: image});}
function loadScript() {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
  document.body.appendChild(script);}
window.onload = loadScript;
</script>
<!--Javascript Ends-->
</head><body>
<?php 
	flush(); 
	include "html/title_bar_new2.inc";  
?>
<div id="content">
<?php	
	// Sort - default sort by date
	if ($sort == "popularity")
		$query = "SELECT * FROM result_table ORDER BY popularity  DESC" ; 
	else if ($sort == "distance")
		$query = "SELECT * FROM result_table ORDER BY distance  ASC" ; 
	else if ($sort == "hoody")
		$query = "SELECT * FROM result_table ORDER BY hoody_sort  DESC" ;
	else
		$query = "SELECT * FROM result_table ORDER BY listed_time  DESC" ; 
	// Execute the query to  get number of rows that contain search kewords
	$numresults=mysql_query ($query);
	$numrows =mysql_num_rows ($numresults);
	
	// next determine if 's' has been passed to script, if not use 0.
	// 's' is a variable that gets set as we navigate the search result pages.
	if (empty($s)) 
		$s=0;

	// get results
	$query .= " limit $s,$limit";
	$result = mysql_query($query) or die(minor_error(215, $user, $user, $today, $query, mysql_error()));
?>
<?php if (empty($var)): ?>
<?php 
	$query = "SELECT title,price,pricing_model,listing_id,fb_uid FROM Listing_Overview 
	WHERE listing_id = '$featured_listings_1' || listing_id = '$featured_listings_2' || listing_id = '$featured_listings_3' || listing_id = '$featured_listings_4' || listing_id = '$featured_listings_5'";
	$result1 = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
?>
	
<?php endif; //if (empty($var)): ?>
    <div id='left_sect'>
	  <div id='scroller' class='fixedElement'>
        <ul id="sort_options">
          <li class="list_title" id="sort_by"><b>Sort by</b></li>
          <li class="list_item" href='<?php echo "<a href='" . $_SERVER['SCRIPT_NAME'] . "?sort=date";?>'>
			<?php 
				if ($sort != "date")
				{
					echo "<a href='" . $_SERVER['SCRIPT_NAME'] . "?sort=date";
					if($var!=NULL) 
						echo "&q=".$var;
					echo "'>Most Recent</a>";
				}
				else
					echo "<b>Most Recent</b>"
			?>
          </li> 
          <li class="list_item"><?php 
                      if ($sort != "popularity")
                      {
                          echo "<a href='" . $_SERVER['SCRIPT_NAME'] . "?sort=popularity"; 
                              if($var!=NULL) echo "&q=".$var;
                          echo "'>Popularity</a> ";
                      }
                      else
                          echo "<b>Popularity</b>";
              ?></li>
          <li class="list_item"><?php
                    if ($user && $sort != "distance")
                    {
                        if($var != NULL)
                            $post_q = "&q=" .$var;
                        if ($user_area_code == NULL && $user_street == NULL)
                            echo "Distance";
                        else
                            echo "<a href='" . $_SERVER['SCRIPT_NAME'] . "?sort=distance" . $post_q . "'>Distance</a>";
                    }
                    else if ($user && $sort == "distance")
					  	echo "<b>Distance</b>";
					else 
					  	echo "<div class='disabled' >Distance</div>";	
              ?></li>
          <li class="list_item"><?php
                    if ($user && $sort != "hoody")
                     	echo "<a href='" . $_SERVER['SCRIPT_NAME'] . "?sort=hoody" . $post_q . "'>Recommeded for you</a>";
                    else if ($user && $sort == "hoody")
                      	echo "<b>Recommended for you</b>";
					else
						echo "<div class='disabled' >Recommended for you</div>";	
              					?></li>
        </ul> 
                
        <form name="location_form" action="<?php echo curPageURL(); ?>" method="POST" >
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
</form><div id='location_map' style='width: 175px; height: 150px;'> </div>    
      </div>
    </div>  <!--end #left_sect-->
    <div id='right_sect'>
<?php if ($number == 0 && $var != ""): ?>
We can't find any relavent results near your neighbourhood. Why not take a look of some other fantastic services instead.
<?php endif; //if ($number == 0 && $var != "") ?>
      <div id="service_list">
        <table class="service_table" id="auto_load">
            <col width="16%">
            <col width="50%">
            <col width="20%">
            <col width="14%">
<?php while ($row= mysql_fetch_array($result)): // begin to show results set, now you can display the results returned ?>
<?php
	extract($row);
	$location = "";
	$degree_separation = "";					
	if ($user)
	{	
		if ($distance == -1)
			$location = "Buyer's home";
		else if ($distance == -2)
			$location = "Service takes place virtually";
		else
		{
			$location = $city;
			if ($user_area_code != NULL || $user_street != NULL)
				$location = $location . "<p class='distance_text'>(" . $distance . "km away)</p>";
		}
	}
	else
	{
		if ($city == "home")
			$location = "Buyer's home";
		else if ($city == "virtual")
			$location = "Service takes place virtually";
		else
			$location = $city;
	}

	// Add a thumbnail picture for each service
	// Extract pictures for the listing
	$picture_sql = "SELECT picture_id_1,picture_count FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (minor_error(219, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	if ($picture_row != NULL)
	{
		extract($picture_row);

		if ($picture_count != 0)
		{
			$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
			$url_result = mysql_query($url_sql) or die (minor_error(220, $user, $user, $today, $url_sql, mysql_error()));
			$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
			extract($url_row);
		}		
	}
?>
<!--onmouseover='highlight("#title_<?php echo $listing_id ?>");' onmouseout='unhighlight("#title_<?php echo $listing_id ?>");'-->
            <tr  >
            	<th><div class='pic_container' id='<?php
															if ($sort == "popularity")
																echo $popularity; 
															else if ($sort == "distance")
																echo $distance; 
															else if ($sort == "hoody")
																echo $hoody_sort; 	
															else
																echo $listed_time; 
														?>'>	
                        <div class='picture'><a href="<?php echo $working_directory . "service/" . $listing_id . "/"; ?>">
                        	<?php if($URL == "hoodylogo.jpg"): ?>
                            <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/hoodylogo2.jpg&h=75&w=75&zc=1"/></a>
                        	<?php else: ?>
                            <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=75&w=75&zc=1"/></a>
                        	<?php endif; ?>
                        </div>
                       
					</div>
				</th>
                <td class='info_cell' id='title_<?php echo $listing_id ?>'><p class='service_info'><a href="<?php echo $working_directory . "service/" . $listing_id . "/"; ?>"><?php echo $title; ?></a><p></td>
                <td class='info_cell'><p class='service_info' ><?php echo $location; ?></p></td>
                <td class='info_cell' id='price_column'><p class='service_info' id='price_column_content' ><?php
																	if ($price==0)
																		echo "FREE";
																	else
																	{
																		echo "$".$price;
																		if ($pricing_model==0)
																			echo " / job";
																		else 
																			echo " / hr";	
																	}			
																?></p></td>
           </tr>
<?php endwhile; //while ($row= mysql_fetch_array($result)) ?>
        </table>
	  <div id='controls'>
<?php if ($numrows == 0): ?>
        <h4>Results</h4>
        <p>Sorry, your search: &quot;<?php echo $trimmed; ?>&quot; returned zero results</p>
<?php endif; //if ($numrows == 0) ?>
      </div>
    </div>  <!--end of #service_list-->
    <div id="loadmoreajaxloader" style="display:none;"><center><img src="ajax-loader.gif" /></center></div>
    <button id="loadmorebutton">Load More</button>
   </div>	<!--end#right_sect-->
</div> <!--end of #content-->
<?php include "html/footer.inc"; ?>
<script type="text/javascript">
	   $(document).ready(function(){
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('a[title]').qtip({
			   style: {
				  classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'top left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {
					  x: 10,  y: 10}},
			  hide: {fixed: true // Helps to prevent the tooltip from hiding ocassionally when tracking!
			  },})});
	  //For fixed left column effect
	  var scrollY = $('.fixedElement').offset().top;
	  $(window).scroll(function(e){ 
		if ($(window).scrollTop() > scrollY ){ 
		  $('.fixedElement').css({'position': 'fixed', 'top': '0px'});}
		else {
			$('.fixedElement').css({'position': 'relative', 'top': ''});}});
</script>
</body>
</html>
    
