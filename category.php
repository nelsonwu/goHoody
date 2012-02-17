 <?php
	// Program: category.php
	//
	// Error Code Range: 600 - 699 (current last: 622)
			
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
	
	if (isset($_GET['url'])) {
		// Extract category info from Category_Lookup table
		$url_lookup_sql = "SELECT * FROM Category_Lookup WHERE category_url='" . $_GET['url'] . "'";
		$result = mysql_query($url_lookup_sql) or die (fatal_error(1, $user, $user, $today, $name_lookup_sql, mysql_error()));
		if (!mysql_num_rows($result))
			header("Location: " . $working_directory . "lost/");
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$category_id = $row['category_id'];
		$category_name = $row['category_name'];
		
		$location_sql = "SELECT * FROM Location_Lookup WHERE location_id	='" . $row['location_id'] . "'";
		$result = mysql_query($location_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
		$row2 = mysql_fetch_array($result,MYSQL_ASSOC);
		$location_name = $row2['location_name'];
		$lng = $row2['lng'];		
		$lat = $row2['lat'];						
	} // End of else if (isset($_GET['name']))	
	
	//If the name variable is not set
	else
		header("Location: " . $working_directory . "lost/");
	
	$page_title = "Just Ask! - " . $category_name . " for " . $location_name;	
	
	//$url = "http://gohoody.com/ask/" . $_GET['url'] . "/";
//	$request_url ="https://graph.facebook.com/comments/?ids=" . $url;
//
//    $requests = file_get_contents($request_url);

	// rows to return
	$limit=20; 
		
//	if ($user)
//	{
//		$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$user'";
//		$result = mysql_query($friendlist_sql) or die (fatal_error(190, $user, $user, $today, $friendlist_sql, mysql_error()));
//		$num = mysql_num_rows($result);
//		for ($i=0; $i<$num; $i++)
//		{
//			$friend_uid=mysql_result($result,$i,"uid2");
//			$buyer_fb_friendlist[] = $friend_uid;
//		}
//		
//		$address_sql = "SELECT area_code,street,lng,lat FROM User_Address WHERE fb_uid='$user'";
//		$result = mysql_query($address_sql) or die (fatal_error(191, $user, $user, $today, $address_sql, mysql_error()));
//		$lnglat_row = mysql_fetch_array($result,MYSQL_ASSOC);
//		$user_lng = $lnglat_row['lng'];
//		$user_lat = $lnglat_row['lat'];
//		$user_area_code = $lnglat_row['area_code'];
//		$user_street = $lnglat_row['street'];  
//	
//		// Distance Sort
//		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE category_id='$category_id' AND status=1";
//		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(192, $user, $user, $today, $create_table_sql, mysql_error()));
//		$num_result_check = mysql_query("SELECT * FROM result_table"); 
//
//		$add_column_sql = "ALTER TABLE result_table ADD distance int(10), ADD city varchar(30), ADD hoody_sort int(5)";
//		$add_clumn_result = mysql_query($add_column_sql)  or die (fatal_error(195, $user, $user, $today, $add_column_sql, mysql_error()));
//
//		$sql = "SELECT listing_id,fb_uid,popularity FROM result_table";
//		$r=mysql_query($sql) or die (fatal_error(196, $user, $user, $today, $sql, mysql_error()));
//		
//		while($w=mysql_fetch_array($r))
//		{
//			extract($w);	
//			
//			// Distance calculating
//			$lnglat_sql = "SELECT listing_location,city,lng,lat,listing_range FROM Listing_Location WHERE listing_id='$listing_id'";
//			$service_result = mysql_query($lnglat_sql) or die (fatal_error(197, $user, $user, $today, $lnglat_sql, mysql_error()));
//			$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
//			// if the service takes place at seller's home
//			if ($service_row['listing_location'] == 0 && $fb_uid != $user)
//			{
//				$sql = "SELECT lng,lat,city FROM User_Address WHERE fb_uid='$fb_uid'";
//				$service_result = mysql_query($sql) or die (fatal_error(198, $user, $user, $today, $sql, mysql_error()));
//				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
//				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
//				
//				// if the user lives within seller's range, update the distance/city		
//				if ($distance < 100)
//				{
//					// hoody_sort
//					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//					// service location
//					$city = $service_row['city'];
//					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(199, $user, $user, $today, $sql, mysql_error()));
//				}
//				// if the user lives seller's range, take the listing off the table
//				else
//				{
//					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(200, $user, $user, $today, $sql, mysql_error()));	
//				}
//			}
//			// if the service takes place at seller's home, and the user is the seller
//			else if ($service_row['listing_location'] == 0 && $fb_uid == $user)
//			{
//				$sql = "SELECT city FROM User_Address WHERE fb_uid='$fb_uid'";
//				$service_result = mysql_query($sql) or die (fatal_error(201, $user, $user, $today, $sql, mysql_error()));
//				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);				
//				
//				$sql = "UPDATE result_table SET distance ='0',hoody_sort = '0' WHERE listing_id='$listing_id'" ;
//				$update_result = mysql_query ($sql)  or die (fatal_error(202, $user, $user, $today, $sql, mysql_error()));	
//			}		
//			// if the service takes place at another location
//			else if ($service_row['listing_location'] == 2)
//			{	
//				$distance = (int)distance($user_lat,$user_lng,$service_row['lat'],$service_row['lng']);
//				// if the user lives within seller's range, update the distance/city		
//				if ($distance < 100)
//				{
//					// hoody_sort
//					if ($fb_uid == $user && $sort == "hoody")
//						$hoody_sort = 0;
//					else if ($sort == "hoody")
//						$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//					// service location
//					$city = $service_row['city'];
//					$sql = "UPDATE result_table SET distance ='$distance', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(203, $user, $user, $today, $sql, mysql_error()));
//				}
//				// if the user lives seller's range, take the listing off the table
//				else
//				{
//					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(204, $user, $user, $today, $sql, mysql_error()));	
//				}
//			}
//			// if the service takes place at buyer's home
//			else if ($service_row['listing_location'] == 1 && $fb_uid != $user)
//			{
//				$sql = "SELECT lng,lat FROM User_Address WHERE fb_uid='$fb_uid'";
//				$range_result = mysql_query($sql) or die (fatal_error(205, $user, $user, $today, $sql, mysql_error()));
//				$range_row = mysql_fetch_array($range_result,MYSQL_ASSOC);				
//				$range = (int)distance($user_lat,$user_lng,$range_row['lat'],$range_row['lng']);	
//				// if the user lives outside of seller's range
//				if ($range > $service_row['listing_range'])
//				{	
//					$sql = "DELETE FROM result_table WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(206, $user, $user, $today, $sql, mysql_error()));	
//				}
//				// if user lives within the seller's range 
//				else
//				{
//					// hoody_sort
//					$hoody_sort = hoody_sort($user, $fb_uid, $distance, $popularity);
//					// service location
//					$city = $service_row['city'];
//					$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '$hoody_sort' WHERE listing_id='$listing_id'" ;
//					$update_result = mysql_query ($sql)  or die (fatal_error(207, $user, $user, $today, $sql, mysql_error()));
//				}
//			}
//			else if ($service_row['listing_location'] == 1 && $fb_uid == $user)
//			{
//				// service location
//				$city = $service_row['city'];
//				$sql = "UPDATE result_table SET distance ='-1', city = '$city', hoody_sort = '0' WHERE listing_id='$listing_id'" ;
//				$update_result = mysql_query ($sql)  or die (fatal_error(208, $user, $user, $today, $sql, mysql_error()));
//			}
//		} // end of while($w=mysql_fetch_array($r))
//	}	
//	
//	else
	{
		// if the user is trying to access distance sort or hoody smart sort without being logged in, reset the sort to default sort
		if ($sort == "distance" || $sort == "hoody")
			$sort = "";

		$create_table_sql = "create temporary table result_table SELECT listing_id,fb_uid,title,price,pricing_model,listed_time,popularity FROM Listing_Overview  WHERE category_id='$category_id' AND status=1";
		$create_table_result = mysql_query($create_table_sql)  or die (fatal_error(209, $user, $user, $today, $create_table_sql, mysql_error()));
		$num_result_check = mysql_query("SELECT * FROM result_table"); 
		
		$add_column_sql = "ALTER TABLE result_table ADD city varchar(30)";
		$add_clumn_result = mysql_query($add_column_sql)  or die (fatal_error(211, $user, $user, $today, $add_column_sql, mysql_error()));
		
		$sql = "SELECT listing_id,fb_uid,city FROM result_table";
		$r=mysql_query($sql) or die (fatal_error(178, $user, $user, $today, $sql, mysql_error()));
		while($w=mysql_fetch_array($r))
		{
			extract($w);	
			$lnglat_sql = "SELECT listing_location,city FROM Listing_Location WHERE listing_id='$listing_id'";
			$service_result = mysql_query($lnglat_sql) or die (fatal_error(212, $user, $user, $today, $lnglat_sql, mysql_error()));
			$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);
			$city = $service_row['city'];
			// if the service takes place at seller's home
			if ($service_row['listing_location'] == 0)
			{
				$sql = "SELECT city FROM User_Address WHERE fb_uid='$fb_uid'";
				$service_result = mysql_query($sql) or die (fatal_error(213, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($service_result,MYSQL_ASSOC);	
				$city = $service_row['city'];
			}
			else if ($service_row['listing_location'] == 1)
				$city = "home";	
					
			$sql = "UPDATE result_table SET city = '$city' WHERE listing_id='$listing_id'" ;
			$update_result = mysql_query ( $sql )  or die (fatal_error(214, $user, $user, $today, $sql, mysql_error()));
		} 
	}		

	// Extract Facebook comments for SEO
	$STD_PATTERN = "@@userpicture@@<a href='@@userlink@@'>@@username@@</a> <BR> @@message@@ <BR> @@formatteddate@@<HR>";
	
	/* PLEASE DON'T MODIFY THE CODE BELOW THIS COMMENT */
	class SEO_FBComments {
		const GRAPH_COMMENTS_URL = "https://graph.facebook.com/comments/?ids=";
		private $pattern;
		private $pageUrl;
	
		/**
		 * @param string $pattern
		 * @param bool $debug
		 */
		public function __construct($pattern = null, $debug = null) {
			$this->pageUrl = "http://gohoody.com/ask/" . $_GET['url'] . "/";
			$this->pattern = $this->getPattern($pattern);
			
			if(is_null($debug)) $debug = ($_REQUEST["debug"] == "1");
			
		
			$this->echoComments();
		}
		
		function echoComments() {
			$oldTimezone = ini_get("date.timezone");
			ini_set("date.timezone", "UTC");
			
			$comments = $this->GetFBCommentsHTML($this->pageUrl, $this->pattern);
			$comments = "<div class='fb_comments'>$comments</div>";
			
			ini_set("date.timezone", $oldTimezone);
			
			echo $comments;
		}
		
		function getPattern($pattern) {
			global $STD_PATTERN;
			
			if(is_null($pattern)) $pattern = $_REQUEST["pattern"];
			if(!$pattern) $pattern = $STD_PATTERN;
			
			return $pattern;
		}
		
		/**
		 * Retrieves a list of Facebook comments 
		 * from the Comments Plugin
		 * 
		 * @param string $ids
		 * @return array
		 */
		function GetFBComments($ids) {		
		// Extract category info from Category_Lookup table
			$url_lookup_sql = "SELECT * FROM Category_Lookup WHERE category_url='" . $_GET['url'] . "'";
			$result = mysql_query($url_lookup_sql) or die (fatal_error(1, $user, $user, $today, $name_lookup_sql, mysql_error()));
			$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
			$category_id = $row4['category_id'];
	
			$query = "SELECT comments FROM Comments WHERE category_id='$category_id'";
			$result = mysql_query($query) or die (minor_error(191, $fbme, $uid, $today, $query, mysql_error()));
			$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
			$content = $row1['comments'];
			
			$comments = json_decode($content);
			$comments = $comments->$ids->data;
			return $comments;
		}
		
		function dayDiff($date1, $date2 = null) {
			if(is_null($date2)) $date2 = time();
			
			$dateDiff = abs($date1 - $date2);
			$fullDays = floor($dateDiff / (60 * 60 * 24));
			
			return $fullDays;
		}
		function formatDate($date) {
			$dateFormat = "F j \a\\t g:ia";
			
			$date = strtotime($date);
			
			$daysBefore = $this->dayDiff($date);
			
			if($daysBefore > 6)
				$formatteddate = date($dateFormat, $date);
			else {
				switch ($daysBefore) {
					case 0:
						$day = "Today";
						break;
					case 1:
						$day = "Yesterday";
						break;
					default:
						$day = date("l", $date);
						break;
				}
				$formatteddate = "$day at " . date("g:ia", $date);
			}
			
			return $formatteddate;
		}
		
		function getComment($data) {
			$username = $data->from->name;
			$userid = $data->from->id;
			$messageid = $data->id;
			$message = $data->message;
			$date = $data->created_time;
			$formatteddate = $this->formatDate($date);
			
			$userpicture = "<fb:profile-pic uid=" . $userid . " size=\"square\"></fb:profile-pic>";
			$userlink = "http://www.facebook.com/profile.php?id=" . $userid;
			$comment = preg_replace("/@@([^@]+)@@/e", "$\\1", $this->pattern);
			
			return $comment;
		}
		
		function getComments($comments) {
			$html = "";
			
			foreach ($comments as $data) {
				$item = $this->getComment($data);
				$html .= $item;
				
				if($data->comments)
					$html .= $this->getComments($data->comments->data);
			}
			
			return $html;
		}
		
		function GetFBCommentsHTML($ids, $pattern) {
			$comments = $this->getFBComments($ids);
			$html = $this->getComments($comments);
			
			return $html;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta NAME="Description" CONTENT="A place for everyone to ask any questions related to <?php echo $category_name; ?> for <?php echo $location_name; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody: <?php echo $page_title; ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/ask/<?php echo $_GET['url']; ?>/"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/justAsk_logo1.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="A place for everyone to ask any questions related to <?php echo $category_name; ?> for <?php echo $location_name; ?>"/>
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php print($page_title) ?></title>

<!--CSS Begins-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/category.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<!--CSS Ends-->

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!-- Google Maps Javascript API -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=CA"></script>

<!--Javascript Begins-->
<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>

<!-- Facebook Comments notification -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/fbCommentsEN.js"></script>
<!-- end of Facebook Comments notification -->

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-qtip.js"></script>

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
				url: "<?php echo $working_directory; ?>loadmore-category.php?lastid=" + $(".pic_container:last").attr("id") + "&c=<?php echo $category_id; ?>",
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
	 //For Dropdown menu 
  function show_dropdown() {
	  $("div#dropdown_menu").show();
	  $("a#arrow_down").hide();
	  $("a#arrow_up").show();};
  function hide_dropdown() {
	  $("div#dropdown_menu").fadeOut(200);
	  $("a#arrow_up").hide();
	  $("a#arrow_down").show();};
  function show_header_popup(){
	  $("#header_popup").fadeIn(100);};
  function hide_header_popup(){
	  $("#header_popup").fadeOut(200);};
  function show_popup(){
	  $("#popup_sect").fadeIn(100);};
  function hide_popup(){
	  $("#popup_sect").fadeOut(200);};
   $('html').click(function() {
	  hide_dropdown();
	  hide_header_popup();
	  hide_popup();});
</script>
<!-- Google Analytics Social Button Tracking -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/ga_social_tracking.js"></script>
<!--Javascript Ends-->
</head>
<body onload="initialize();">
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

	<div id = 'top_sect'>
        <a id='justask_link' href='http://gohoody.com/ask/'> <img id='justask_logo' src="<?php echo $domain_secondary;?>attachements/justAsk_logo3.png" /> </a>
        <div id='category_banner'>
        	<div id='banner_sect' style="background:url(http://img.gohoody.com/service_pictures/<?php echo $_GET['url'];?>.jpg);">
            	<div id='banner_title'><?php echo $category_name ?></div>
            </div>
        </div>
        <img id='banner_shadow' src="<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png" alt="">
        
        
    </div> <!--end #top_sect-->
    
    
    
    
    <div id='right_sect'>
    
    	<div id='ask_title_sect'>
            <div class='section_title2a'> Got a question? <span class='strong'> Just Ask!</span> </div>
            <div class='g_plus_link'><g:plusone size="medium" annotation="none" href='http://gohoody.com/ask/<?php echo $_GET['url']; ?>/'></g:plusone></div>
            
            <div class='fb_link_sect'>
              <script type="text/javascript">_ga.trackFacebook();</script>
              <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Ffb.me%2Fgohoody&amp;send=false&amp;layout=button_count&amp;width=50&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=210437142339472" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:53px; height:21px;" allowTransparency="true"></iframe>	
            </div>    
        </div>
    
        <div id='ask_section'>
          <div class='extracted'>
          	<fb:comments-count href=http://dev.gohoody.com/ask/<?php echo $_GET['url']; ?>/></fb:comments-count> awesome comments 
		  	<?php	new SEO_FBComments; ?>
          </div>
      	  <div id='fbcomments_sect'>
			 <script type="text/javascript">_ga.trackFacebook();</script>	
         	 <fb:comments class='fbcomments' href="http://gohoody.com/ask/<?php echo $_GET['url']; ?>/"  num_posts="5" width="410" notify="true" title="Hoody: <?php echo $page_title; ?>"></fb:comments>
          </div>          
        </div>
    </div>  <!--end #left_sect-->
      
    <div id='left_sect'>
    
    	<div class='breadcrumbs'>
            <a class='breadcumb_link' href='<?php echo $working_directory; ?>'>Home</a> <span class='style1'> > </span> 
            <a class='breadcumb_link' href='<?php echo $working_directory; ?>service/'>Services</a> <span class='style1'> > </span>  
            <a class='breadcumb_link' href='' ><?php echo $category_name ?></a>
        </div> <!--end #breadcrumbs-->
    	
    	<div id = 'test1' ></div>
        <div id = 'test2' ></div>
        <div id = 'test3' ></div>
        <div id = 'test4' ></div>
        <div id = 'test5' ></div>
        <div id = 'test6' ></div>
        
       
        
        <div id="service_list">
        <table class="service_table" id="auto_load">
            <col width="16%">
            <col width="60%">
            <col width="24%">
		<?php while ($row= mysql_fetch_array($result)): // begin to show results set, now you can display the results returned ?>
        <?php
            extract($row);
            $location = "";
            $degree_separation = "";					
            if ($user)
            {	
                if ($distance == -1)
                    $location = "Buyer's home";
                else
                    $location = $city;
                if ($distance != -1 && ($user_area_code != NULL || $user_street != NULL))
                    $location = $location . "<p class='distance_text'>(" . $distance . "km away)</p>";
            }
            else
            {
                if ($city == "home")
                    $location = "Buyer's home";
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
                                    <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/hoodylogo2.jpg&h=65&w=65&zc=1"/></a>
                                    <?php else: ?>
                                    <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=75&w=75&zc=1"/></a>
                                    <?php endif; ?>
                                </div>
                               
                            </div>
                        </th>
                        <td class='info_cell' id='title_<?php echo $listing_id ?>'><p class='service_info'><a href="<?php echo $working_directory . "service/" . $listing_id . "/"; ?>"><?php echo $title; ?></a><p></td>
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
              <div id='controls'></div>
            </div>  <!--end of #service_list-->
            <div id="loadmoreajaxloader" style="display:none;"><center><img src="ajax-loader.gif" /></center></div>
            <button id="loadmorebutton">Load More</button>
    </div>	<!--end#right_sect-->	
</div> <!--end of #content-->

<?php include "html/footer.inc"; ?>
    
<script type="text/javascript">
	   $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('a[title]').qtip({
			   style: {
				  classes: 'ui-tooltip-rounded ui-tooltip-shadow'
			   },
			   position: {
				  my: 'top left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {
					  x: 10,  y: 10
				  }
			  },
			  hide: {
				  fixed: true // Helps to prevent the tooltip from hiding ocassionally when tracking!
			  },
			})
	  });
	  
	  //For fixed left column effect
	  var scrollY = $('.fixedElement').offset().top;
	  $(window).scroll(function(e){ 

		if ($(window).scrollTop() > scrollY ){ 
		  $('.fixedElement').css({'position': 'fixed', 'top': '0px'}); 
		}
		else {
			$('.fixedElement').css({'position': 'relative', 'top': ''});
		} 
	  });
</script>
<!-- Google +1 button - Google specified that this code need to implemented after the +1 button -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();  
</script>
</body>
</html>
    
