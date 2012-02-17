<?php
	// Program: flyer.php
	//
	
	//Connect to @Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";	
	
	// check for a listing_id in the URL:
	$page_title = NULL;
	if (isset($_GET['lid'])) 
	{	
		//Typecast it to an integer:
		$lid = (int) $_GET['lid'];
		//An invalid $_GET['lid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($lid > 0) 
		{			
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT * FROM Listing_Overview WHERE listing_id=$lid&&(status=1||status=0)";
			$result = mysql_query($query) or die (fatal_error(104, $user, $user, $today, $query, mysql_error()));
			$num = mysql_num_rows($result);
			
			//service listing name not found
			if ($num == 0) 
				header("Location: " . $working_directory);
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			$seller_uid = $fb_uid;
			
			// Updated 4/12
			// Takes care of the textarea linebreaks and tabs
			if (!$flyer_description)
			{
				$description = nl2br($listing_description);
				$description = str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$description);
				$description = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$description);
			}
			else
				$description = $flyer_description;
						
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT about_me,name,pic_square,flyer_about_me FROM Basic_User_Information WHERE fb_uid='$seller_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(105, $user, $user, $today, $service_sql, mysql_error()));
			$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_pic3 = $row3['pic_square'];
			$seller_name = $row3['name'];
			// Updated 4/12
			// Takes care of the textarea linebreaks and tabs
			if (!$row3['flyer_about_me'])
			{
				$about_me = nl2br($row3['about_me']);
				$about_me = str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$about_me);
				$about_me = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$about_me);
			}
			else
				$about_me = $row3['flyer_about_me'];			
			
			// Extract pictures for the listing
			$picture_sql = "SELECT * FROM Listing_Pictures WHERE listing_id='$lid'";
			$picture_result = mysql_query($picture_sql) or die (fatal_error(106, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			$picture_id = $picture_row['picture_id_1'];
			$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id'";
			$url_result = mysql_query($url_sql) or die (fatal_error(107, $user, $user, $today, $url_sql, mysql_error()));
			$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
			$picture_url = $url_row['URL'];

			// Updated 3/29
			// Tweaks for the listing address
			// Updated 3/31
			// Add lng/lat component
			$sql = "SELECT * FROM Listing_Location WHERE listing_id='$lid'";
			$result = mysql_query($sql) or die (fatal_error(108, $user, $user, $today, $sql, mysql_error()));
			$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($service_row);	
			
			// if the service takes place at seller's home
			if ($listing_location == 0)
			{
				$sql = "SELECT * FROM User_Address WHERE fb_uid='$seller_uid'";
				$result = mysql_query($sql) or die (fatal_error(109, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
				extract($service_row);
				// Google Maps takes $database_* variable to generate the map	
				$database_city = $city;
				$database_state = $state;
				$database_country = $country;
				$database_street = $street;
				$database_areacode = $area_code;
				$database_lng = $lng;
				$database_lat = $lat;
			}
			// if the service takes place at another location
			else if ($listing_location == 2)
			{
				$database_city = $city;
				$database_state = $state;
				$database_country = $country;
				$database_street = $street;
				$database_areacode = $area_code;
				$database_lng = $lng;
				$database_lat = $lat;
			}
			else if ($listing_location == 1)
			{
				$sql = "SELECT * FROM User_Address WHERE fb_uid='$seller_uid'";
				$result = mysql_query($sql) or die (fatal_error(110, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
				extract($service_row);
				// Google Maps takes $database_* variable to generate the map	
				$database_lng = $lng;
				$database_lat = $lat;
			}			
		} // End of if ($lid > 0)
	} // End of if (isset($_GET['lid']))
	
	else
		header("Location: " . $working_directory);
	
	$page_title = $title;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="<?php 
	if ($_GET['colour'] == 1)
		echo $working_directory . "css/flyer.css"; 
	else if ($_GET['colour'] == 0)
		echo $working_directory . "css/flyer_bw.css";
?>" type="text/css" media="screen" />

<!--Facebook meta properties -->
    <meta property="og:title" content="<?php echo $page_title; ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/flyer/<?php echo $lid; ?>/"/>
    <meta property="og:image" content="<?php echo $domain_secondary . "service_pictures/" . $picture_url[0]; ?>"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="<?php echo $title. " Service Offered by " . $seller_name . ", Hosted By Hoody"; ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!-- jQuery library -->
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>

<!-- QR Code -->
<script language="javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	var queryString = '';
	var dataUrl = '';
	
	function onLoadCallback() {
		if (dataUrl.length > 0) {
			var query = new google.visualization.Query(dataUrl);
			query.setQuery(queryString);
			query.send(handleQueryResponse);
		} else {
			var dataTable = new google.visualization.DataTable();
			
			draw(dataTable);
		}
	}
	
	function draw(dataTable) {
		var vis = new google.visualization.ImageChart(document.getElementById('chart'));
		var options = {
			chs: '265x265',
			cht: 'qr',
			chld: 'L|0',
			chl: 'http://gohoody.com/service/<?php echo $lid; ?>/'
		};
		vis.draw(dataTable, options);
	}
	
	function handleQueryResponse(response) {
		if (response.isError()) {
			alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
			return;
		}
		draw(response.getDataTable());
	}
	
	google.load("visualization", "1", {packages:["imagechart"]});
	google.setOnLoadCallback(onLoadCallback);
</script>

<!--Google Analytic Tracking Code-->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23022233-1']);
  _gaq.push(['_setDomainName', '.gohoody.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>
<?php flush(); ?>
<body>    
    <div id='service_section_clean'>
        <div id='title'> <?php echo "$title";?></div>
    	<div id='service_picture'><?php echo "<img id='service_pic' src= \"". $domain_secondary . "resizer.php?src=" . $domain_secondary . "service_pictures/$picture_url&w=250&zc=1 \" alt='' />"; ?></div>	
		<div id='service_description'><?php echo "$description"; ?></div>
    </div>
    <div id='contact_info'>
    	<div class='title1'> Contact Info </div>
        <div id='contact_left'>
            <div id='seller'>
                <img id='seller_picture' src="<?php echo $seller_pic3; ?>" alt="" />
                <div id='seller_name'><?php echo $seller_name;?></div>
            </div>
            <div id='about_seller'>
                <div id='about_content'><?php echo "$about_me"; ?></div>
            </div> 
            <div id='service_link'></div>
        </div>    
            
        <div id='qr_code'>
        	<div id="chart"></div>
            <div id='service_url'>gohoody.com/service/<?php echo $lid; ?>/ </div>
        </div>
    </div>
    
	<div id='service_price'>
    	<div class='title1'> Price </div> 
        <div id='price'> 
<?php 		
		if ($price==0)
			echo "FREE";
		else 
		{
			echo "$" . $price; 
			if ($pricing_model==0)
				echo " per job";
			else 
				echo " per hour";
		} 
?>
        </div>
    </div>
    
    <div id='service_location'>
    	<div class='title1'> Service Location </div>
<?php if ($listing_location == 0): // if the service takes place at seller's home ?>
           <img src="http://maps.google.com/maps/api/staticmap?zoom=12&size=274x195&maptype=roadmap&markers=icon:<?php echo $domain_secondary;?>attachements/map_marker.png%7C<?php echo $database_lat . "," . $database_lng; ?>&style=element:labels&sensor=false"  alt="" />
<?php if ($show_address == 1): // Generate the HTML code for the address field box ?>
          <div class='location_address'><?php
			  if ($database_areacode)
				echo $database_areacode;
			  else
				echo "$database_street, $database_city, $database_state";
          ?></div>
<?php endif; //if ($show_address == 1) ?>
<?php elseif ($listing_location == 2): // if the service takes place at another location ?>
          <img src="http://maps.google.com/maps/api/staticmap?zoom=12&size=274x195&maptype=roadmap&markers=icon:<?php echo $domain_secondary;?>attachements/map_marker.png%7C<?php echo $database_lat . "," . $database_lng; ?>&style=element:labels&sensor=false"  alt="" />
          <div class='location_address'><?php echo $database_street . ", " . $database_city . ", " . $database_state; ?></div>
<?php elseif ($listing_location == 1): ?>
          <div class='location_address2'>This service is hosted at the buyer's home.</div>
<?php endif; //if ($listing_location == 0)?>
    </div>    
	<div id='footer'> 
    	<div id='hoody_link'> GoHoody.com </div>
        <div id='hoody_logo'><img src="<?php echo $domain_secondary; ?>attachements/hoodylogo.png" width="80px" alt="Hoody!"></div>
    </div>
</body>
</html>