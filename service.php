 <?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";			
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	include "php/admin.inc";

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";
	
	// check for a listing_id in the URL:
	$page_title = NULL;
	if (isset($_GET['lid'])) {
		
		//Typecast it to an integer:
		$lid = (int) $_GET['lid'];
		//An invalid $_GET['lid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($lid > 0) {			
			//Get the information from the database for this service:
			//Do not show deleted listings!!!!
			$query = "SELECT * FROM Listing_Overview WHERE listing_id=$lid&&(status=1||status=0||status=3)";
			$result = mysql_query($query) or die (fatal_error(255, $user, $user, $today, $query, mysql_error()));
			$num = mysql_num_rows($result);
			
			//service listing name not found
			if ($num == 0) 
				header("Location: " . $working_directory);
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			$seller_uid = $fb_uid;
			// Takes care of the textarea linebreaks and tabs
			$listing_description = nl2br($listing_description);
			$listing_description = str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$listing_description);
			$listing_description = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$listing_description);
			$listing_description = make_clickable($listing_description);

			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT pic_square,name,first_name FROM Basic_User_Information WHERE fb_uid='$seller_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(256, $user, $user, $today, $service_sql, mysql_error()));
			$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_pic3 = $row3['pic_square'];
			$seller_name = $row3['name'];
			$seller_first_name = $row3['first_name'];
						
			// Extract pictures for the listing
			$picture_sql = "SELECT * FROM Listing_Pictures WHERE listing_id='$lid'";
			$picture_result = mysql_query($picture_sql) or die (fatal_error(257, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			$picture_id = array($picture_row['picture_id_1'], $picture_row['picture_id_2'], $picture_row['picture_id_3'], $picture_row['picture_id_4'], $picture_row['picture_id_5']);
			$picture_url = array();
			$picture_count = $picture_row['picture_count'];
			for ($counter = 0 ; $counter < $picture_count ; $counter++)
			{
				$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id[$counter]'";
				$url_result = mysql_query($url_sql) or die (fatal_error(258, $user, $user, $today, $url_sql, mysql_error()));
				$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
				$picture_url[$counter] = $url_row['URL'];
			}

			// Tweaks for the listing address
			// Add lng/lat component
			$sql = "SELECT * FROM Listing_Location WHERE listing_id='$lid'";
			$result = mysql_query($sql) or die (fatal_error(259, $user, $user, $today, $sql, mysql_error()));
			$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($service_row);	
			
			// if the service takes place at seller's home
			if ($listing_location == 0)
			{
				$sql = "SELECT * FROM User_Address WHERE fb_uid='$seller_uid'";
				$result = mysql_query($sql) or die (fatal_error(260, $user, $user, $today, $sql, mysql_error()));
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
				$result = mysql_query($sql) or die (fatal_error(261, $user, $user, $today, $sql, mysql_error()));
				$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
				extract($service_row);
				// Google Maps takes $database_* variable to generate the map	
				$database_lng = $lng;
				$database_lat = $lat;
			}			
			else if ($listing_location == 3)
			{
				
			}
			
			//if the user is logged in
			//enable address/distance comparison feature
			//enable emailing
			if ($user && $seller_uid > 0)
			{
				$address_sql = "SELECT area_code,street,city,state,country,lng,lat FROM User_Address WHERE fb_uid='$user'";
				$result = mysql_query($address_sql) or die (fatal_error(262, $user, $user, $today, $address_sql, mysql_error()));
				$row = mysql_fetch_array($result,MYSQL_ASSOC);
				$buyer_city = $row['city'];
				$buyer_state = $row['state'];
				$buyer_country = $row['country'];
				$buyer_areacode = $row['area_code'];
				$buyer_street = $row['street'];	
				$buyer_lng = $row['lng'];	
				$buyer_lat = $row['lat'];	
							
				// actual friend checking
				if ($seller_uid == $user)
					$degree_separation = 0;
				else
				{
					// Check if the seller is a Facebook friend
					// Request for buyer's Facebook Friendlist
					$degree_separation = 9;
					$fql    =   "SELECT uid1, uid2 FROM friend WHERE uid1 = " . $user . " AND uid2 = " . $seller_uid;
					$param  =   array(
						'method'    => 'fql.query',
						'query'     => $fql,
						'callback'  => ''
					);
					$buyer_fb_friendlist   =   $facebook->api($param);
					if ($buyer_fb_friendlist)
						$degree_separation = 1;
				} // end of else
				
				if (isset($_GET['facebook'])) 
				{		
					//Typecast it to an integer:
					$post_to_facebook = (int) $_GET['facebook'];
					
					if ($post_to_facebook == 1)
					{
						try 
						{					
							if ($pricing_model == 0)
								$pricing_model_text = " per job";
							else if ($pricing_model == 1)
								$pricing_model_text = " per hour";
								
							if ($price == 0)
								$price_text = "Free";
							else 
								$price_text = "$ " . $price . $pricing_model_text;
								
							$wallpostpage = $facebook->api('/me/feed', 'post',
											array(
											  'message' 	=> 'I am offering ' . $title . ' service on Hoody' ,
											  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url[0],
											  'link'    	=> 'http://gohoody.com/service/' . $lid . '/',
											  'name'    	=> $title,
											  'caption' 	=> $price_text,
											  'description' => $title. " by " . $facebook_first_name,
											  'source' 		=> '',
											  'cb'      	=> ''
											  )
							);
						} 
						catch (FacebookApiException $e) 
						{
							 print_r($o);
						}
						header("Location: " . $working_directory . "service/" . $lid . "/");
					}
				} // End of if (isset($_GET['facebook']))	
			} // End of if ($user && $seller_uid > 0)

			if (isset($_GET['convert'])) 
				{		
					$query = "UPDATE Listing_Overview SET status='1',fb_uid='$user' WHERE listing_id='$lid'";
					$result = mysql_query($query) or die (fatal_error(118, $user, $user, $today, $query, mysql_error()));
					
					try 
					{					
						if ($pricing_model == 0)
							$pricing_model_text = " per job";
						else if ($pricing_model == 1)
							$pricing_model_text = " per hour";
							
						if ($price == 0)
							$price_text = "Free";
						else 
							$price_text = "$ " . $price . $pricing_model_text;
							
						$wallpostpage = $facebook->api('/me/feed', 'post',
										array(
										  'message' 	=> 'I am offering ' . $title . ' service on Hoody' ,
										  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url[0],
										  'link'    	=> 'http://gohoody.com/service/' . $lid . '/',
										  'name'    	=> $title,
										  'caption' 	=> $price_text,
										  'description' => $title. " by " . $facebook_first_name,
										  'source' 		=> '',
										  'cb'      	=> ''
										  )
						);
					} 
					catch (FacebookApiException $e) 
					{
						 print_r($o);
					}
					header("Location: " . $working_directory . "service/" . $lid . "/");
				} // End of if (isset($_GET['convert']))	
		} // End of if ($lid > 0)
		
		// Get data from Confirmed_Transactions table
		// column needed: "rating" "fb_uid" "review" "transaction_date"
		$query = "SELECT rating,fb_uid,review,transaction_date FROM Confirmed_Transactions WHERE listing_id='$lid'&&review_status=1 ORDER BY transaction_date";
		$rating_result = mysql_query($query) or die (fatal_error(263, $user, $user, $today, $query, mysql_error()));
		$rating_num = mysql_num_rows($rating_result);
		
		$query = "SELECT review,review_status FROM Confirmed_Transactions WHERE listing_id='$lid'&&review_status=1&&review!='' ORDER BY transaction_date";
		$review_num_result = mysql_query($query) or die (fatal_error(264, $user, $user, $today, $query, mysql_error()));
		$review_num = mysql_num_rows($review_num_result);
	
		// Get data for past customers
		$query = "SELECT fb_uid FROM Confirmed_Transactions WHERE listing_id='$lid'&&transaction_date!='NULL' ORDER BY transaction_date";
		$past_customers_result = mysql_query($query) or die (fatal_error(265, $user, $user, $today, $query, mysql_error()));
		$past_customers_num = mysql_num_rows($past_customers_result);
	
		// Get data for seller's other listings
		$query = "SELECT listing_id,title,price,pricing_model FROM Listing_Overview WHERE fb_uid = '$seller_uid' && status=1 ORDER BY listing_id";
		$other_listing_result = mysql_query($query) or die (fatal_error(266, $user, $user, $today, $query, mysql_error()));
		$other_listing_num = mysql_num_rows($other_listing_result);
	} // End of if (isset($_GET['lid']))
	
	else
		header("Location: " . $working_directory);
	
	// update page title	
	if($title)
		$page_title = $title;
	else
		$page_title = "Hoody Service";	
		
	// Extract Facebook comments for SEO
	$STD_PATTERN = "@@userpicture@@<a href='@@userlink@@'>@@username@@</a> <BR> @@message@@ <BR> @@formatteddate@@<HR>";
	
	/* PLEASE DON'T MODIFY THE CODE BELOW THIS COMMENT */
	class SEO_FBComments {
		const GRAPH_COMMENTS_URL = "https://graph.facebook.com/comments/?ids=";
		private $pattern;
		private $pageUrl;
	
		// @param string $pattern
		// @param bool $debug
		 
		public function __construct($pattern = null, $debug = null) {
			$this->pageUrl = "http://gohoody.com/service/" . $_GET['lid'] . "/";
			$this->pattern = $this->getPattern($pattern);
			
			if(is_null($debug)) $debug = ($_REQUEST["debug"] == "1");
			
			$this->echoComments();
		}
		
		function echoComments() {
			$oldTimezone = ini_get("date.timezone");
			ini_set("date.timezone", "UTC");
			
			$comments = $this->GetFBCommentsHTML($this->pageUrl, $this->pattern);
			$comments = "<div class='fb_comments_extracted'>$comments</div>";
			
			ini_set("date.timezone", $oldTimezone);
			
			echo $comments;
		}
		
		function getPattern($pattern) {
			global $STD_PATTERN;
			
			if(is_null($pattern)) $pattern = $_REQUEST["pattern"];
			if(!$pattern) $pattern = $STD_PATTERN;
			
			return $pattern;
		}
		
		 // Retrieves a list of Facebook comments 
		 // from the Comments Plugin
		 // @param string $ids
		 // @return array
		 
		function GetFBComments($ids) {		
			// Extract category info from Category_Lookup table
			$query = "SELECT comments FROM Comments WHERE category_id='" . -$_GET['lid'] . 	"'";
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
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$seller_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(271, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row4['profile_name'];
	
	
	
	
	$review_query = "SELECT contact_id FROM Contact_History WHERE fb_uid='$user'&&listing_id='$lid' ORDER BY contact_id";
	$review_result = mysql_query($review_query) or die (fatal_error(65, $user, $user, $today, $interested_service_query, mysql_error()));
	$review_row = mysql_fetch_array($review_result,MYSQL_ASSOC);
	$contact_id = $review_row['contact_id'];
	$query = "SELECT review_status FROM Confirmed_Transactions WHERE listing_id='$lid'&&fb_uid='$user'";
	$review_num_result = mysql_query($query) or die (fatal_error(264, $user, $user, $today, $query, mysql_error()));
	$review_row2 = mysql_fetch_array($review_num_result,MYSQL_ASSOC);
	$review_status = $review_row2['review_status'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html itemscope itemtype="http://schema.org/Product" xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<!-- schema.org metal properties -->
<meta itemprop="name" content="<?php echo $page_title; ?>">
<meta itemprop="description" content="<?php echo $title. " Service Offered by " . $seller_name . ", Hosted By Hoody"; ?>">
<meta itemprop="image" content="<?php echo $domain_secondary . "service_pictures/" . $picture_url[0]; ?>">
<meta itemprop="url" content="<?php echo $domain_primary . "service/" . $lid . "/"; ?>">

<!--Facebook meta properties -->
    <meta property="og:title" content="<?php echo $page_title; ?>"/>
    <meta property="og:type" content="product"/>
    <meta property="og:url" content="<?php echo $domain_primary . "service/" . $lid . "/"; ?>"/>
    <meta property="og:image" content="<?php echo $domain_secondary . "service_pictures/" . $picture_url[0]; ?>"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="<?php echo $title. " Service Offered by " . $seller_name . ", Hosted By Hoody"; ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/service_description.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/title_bar_new.css"  media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/slidingtabs-vertical.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.lightbox.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!-- Google Maps Javascript API -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=CA"></script>

<!-- Facebook Javascript API -->
<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!-- Linkedin API -->
<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>

<!-- jQuery library -->
<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>
<!--[if gte IE 6]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.3.2.min.js" type="text/javascript"></script></script> 
<![endif]-->

<!-- Facebook Comments notification -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/fbCommentsEN.js"></script>

<!-- qTip -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.qtip-1.0.0-rc3.min.js"></script>

<!--notification-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.gritter.js"></script>

<!--Javascript for tabs START-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.slidingtabs.pack.js"></script>    
<script type="text/javascript">  
$(document).ready(function() {  				  		
	$('div#st_vertical').slideTabs({
		// Options  			
		orientation: 'vertical',  			
		slideLength: 300, // Height of the div.st_v_tabs_container element -minus the directional button's height (37px)			
		contentAnim: 'slideH',			
		contentEasing: 'easeInOutExpo',
		tabsAnimTime: 300,
		contentAnimTime: 600});});
</script>   

<!-- Twitter button -->
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>

<!--Javascript for popupbox-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.lightbox.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($){
      $('.lightbox').lightbox();});
</script>
 
<script type="text/javascript" charset="utf-8"> 
  <!--Warning msg script-->	
  function warning_fadein() {
	  $("#warn_address").fadeTo(700, .75).delay(5000).fadeOut(700);}
  function close_confirm() {
	  $("#confirm_content").fadeOut(500);
	  $("#confirm_post").delay(500).slideUp();}
  <!--tabs script-->
  function show_tab1() {
	  $('#tab1_content').fadeIn(); 
	  $('#tab2_content').hide();
	  $('#tab3_content').hide();
	  $('#tab4_content').hide();		  
	  $('#tab1').css({"margin-top": "1px", "background-color":"#F5F5F5"});
	  $('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"});}
  function show_tab2() {
	  $('#tab2_content').fadeIn();
	  $('#tab1_content').hide();
	  $('#tab3_content').hide();
	  $('#tab4_content').hide();		  
	  $('#tab2').css({"margin-top": "1px", "background-color":"#F5F5F5"});
	  $('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"});}
  function show_tab3() {
	  $('#tab3_content').fadeIn();
	  $('#tab1_content').hide();
	  $('#tab2_content').hide();
	  $('#tab4_content').hide();		  
	  $('#tab3').css({"margin-top": "1px", "background-color":"#F5F5F5"});
	  $('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
	  $('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"});}
</script>

<!-- Jeditable -->
<script src="<?php echo $working_directory; ?>javascript/jquery.jeditable.js" type="text/javascript"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery.jeditable.autogrow.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.autogrow.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $(".autogrow").editable("<?php echo $working_directory; ?>php/edit.php", { 
        indicator : "<img src='<?php echo $working_directory; ?>css/images/loading.gif'>",
        type      : "autogrow",
        submit    : 'OK',
        cancel    : 'cancel',
        tooltip   : "Click to edit",
        onblur    : "ignore",
        autogrow : {
           lineHeight : 16,
           minHeight  : 32}});
	$(".editable_select").editable("<?php echo $working_directory; ?>php/edit.php", { 
		indicator : '<img src="<?php echo $working_directory; ?>css/images/loading.gif">',
		data   : "{'per job':'per job','per hour':'per hour','free':'free'}",
		type   : "select",
		submit : "OK",
		style  : "inherit",});
	$("#1_<?php echo $lid; ?>").mouseover (function() {
		$("#1_<?php echo $lid; ?>").css("background-color", "#ffffd3")});
	$("#2_<?php echo $lid; ?>").mouseover (function() {
		$("#2_<?php echo $lid; ?>").css("background-color", "#ffffd3")});
	$("#1_<?php echo $lid; ?>").mouseout (function() {
		$("#1_<?php echo $lid; ?>").css("background-color", "")});
	$("#2_<?php echo $lid; ?>").mouseout (function() {
		$("#2_<?php echo $lid; ?>").css("background-color", "")});
	$("#1_<?php echo $lid; ?>").focusout (function() {
		$("#1_<?php echo $lid; ?>").css("background-color", "")});
	$("#2_<?php echo $lid; ?>").focusout (function() {
		$("#2_<?php echo $lid; ?>").css("background-color", "")});});

//For pictures section
function show_picture(pic_num) {
	$("#pic1_img").hide();
	$("#pic2_img").hide();
	$("#pic3_img").hide();
	$("#pic4_img").hide();
	$("#pic5_img").hide();
	switch (pic_num){
		case 1:
			$("#pic1_img").fadeIn();
			break
		case 2:
			$("#pic2_img").fadeIn();
			break
		case 3:
			$("#pic3_img").fadeIn();
			break
		case 4:
			$("#pic4_img").fadeIn();
			break
		case 5:
			$("#pic5_img").fadeIn();
			break
		case 6:
			$("#pic1_img").fadeIn();
			break}}
</script> 

<!--Google Maps-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $database_lat; ?>, <?php echo $database_lng; ?>);
    var myOptions = {
      zoom: 13,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
	  mapTypeControl: false,
	  scrollwheel: false,
	  streetViewControl: false,}
	var image = new google.maps.MarkerImage('<?php echo $domain_secondary;?>attachements/map_marker.png',
		new google.maps.Size(40, 66),
		new google.maps.Point(0,0),
		new google.maps.Point(20, 66));
	var shadow = new google.maps.MarkerImage('<?php echo $domain_secondary;?>attachements/map_marker_shadow.png',
		new google.maps.Size(59, 29),
		new google.maps.Point(0,0),
		new google.maps.Point(0, 29));
	var shape = {
		coord: [1, 1, 1, 66, 40, 66, 40 , 1],
		type: 'poly'};
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var contentString = '<p><strong>Service Location:</strong></p><p><?php 
                // if the service takes place at seller's home
                if ($listing_location == 0) 
                {	
                      // Generate the HTML code for the address field box
                      if ($show_address == 1) 
					  {
						  if ($database_areacode)
						  	echo $database_areacode;
						  else
						  	echo "$database_street, $database_city, $database_state";
					  }
                      else
                          echo "$database_city, $database_state";
                }
                // if the service takes place at another location
                else if ($listing_location == 2)
                      echo "$database_street, $database_city, $database_state";
  ?></p>';
        
    var infowindow = new google.maps.InfoWindow({
        content: contentString});
    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'Hoody Service',
        icon: image,
		shape: shape,
		shadow: shadow,
		animation: google.maps.Animation.DROP});
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);});}
</script>

<!-- Google Analytics Social Button Tracking -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/ga_social_tracking.js"></script>

<!-- Load Twitter JS-API asynchronously -->
<script>
(function(){
var twitterWidgets = document.createElement('script');
twitterWidgets.type = 'text/javascript';
twitterWidgets.async = true;
twitterWidgets.src = 'http://platform.twitter.com/widgets.js';
// Setup a callback to track once the script loads.
twitterWidgets.onload = _ga.trackTwitter;
document.getElementsByTagName('head')[0].appendChild(twitterWidgets);
})();
</script>
<!-- end of Javascript -->
</head>
<body onload="initialize()">

<?php if (!$user && $seller_uid < 0 && $status == 3):?>
	<div id='ui_blocker'></div>
<?php endif; ?>  
<?php 
	flush();
	include "html/title_bar_new2.inc"; 
?>  


  

<div id="content">	

<div class='breadcrumbs'>
	<a class='breadcumb_link' href='<?php echo $working_directory; ?>'>Home</a> <span class='style1'> > </span> 
	<a class='breadcumb_link' href='<?php echo $working_directory; ?>service/'>Services</a> <span class='style1'> > </span>  
	<a class='breadcumb_link' href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" ><?php echo $seller_name ;?></a> <span class='style1'> > </span> 
    <a class='breadcumb_link' href=''><?php echo $title; ?></a> 
</div> <!--end #breadcrumbs-->

<div id ='popup_sect'>
	<img class='popup_arrow' src="<?php echo $domain_secondary;?>attachements/popup_arrow_right.png"  />
	<div class='pu_title'> Please login to contact <br /> <?php echo $seller_first_name; ?> </div>
    <div class='pu_content'>
        <div id ='login_button' class='pu_button'>
            <a class='login_button_link' onclick='facebookLogin(); return false;'>
            	<img class='fb_logo_button2' src='<?php echo $domain_secondary;?>attachements/fb_logo.png' width='11px'/>Login
            </a>
        </div>
    </div>
</div> <!--end #popup_sect-->
<?php if ( $seller_uid == $user && $user): ?>
  <div id = 'confirm_post'>
    <div id='confirm_content'>
    	<a id='dashboard_button' href='<?php echo $working_directory; ?>dashboard/'>Dashboard</a>
        <div id='text_and_links'>
            <div id='confirm_text'> Your posting is live. </div>
            <div id='confirm_text2'> You can manage it in your Dashboard. </div>
        </div>
        <div id ='confirm_links'>
          <a class='option_link' id='modify_link' href='<?php echo $working_directory . "create/" . $listing_id . "/"; ?>'>Modify</a>
          <a class='option_link' href='<?php echo $working_directory . "flyer/" . $lid . "/"; ?>' id='qr_link' target='_blank'> Print Flyer </a> 
          <!--<a class='option_link' id='facebook_button' href='<?php echo $working_directory . "service/" . $lid . "/post-to-facebook/"; ?>'>Share on Facebook</a> -->
        </div>
        <a id='hide_link' onclick='close_confirm()'></a> 
    </div>
  </div>
<?php endif; //if ($seller_uid == $user && $user) ?>   
 
<?php if (!$user && $seller_uid < 0 && $status == 3):?>
	<div id='final_step'>	
    	<div id='final_text1'> Almost done! </div>
        <div id='final_text2'> Please sign in using Facebook to post your service. </div>
    	<a id='final_login' href="<?php echo $loginUrl_create; ?>"> <img class="fb_logo_button_final" src="http://dev.gohoody.com/attachements/fb_logo.png"> Sign in to post service</a>
    </div> <!--end #final_step-->
    <script type="text/javascript">
		$('.login_button_link').attr('onclick', '');
		$('.login_button_link').attr('href', '<?php echo $loginUrl_create; ?>');
	</script>
<?php endif; ?> 
  
  <!--Container for the Service Description Block-->
  <div id = "service_block">
<?php       
	if ($price == 0)
		echo "<div class='price_box_free'>&nbsp;Free&nbsp;</div>";
	else if ($pricing_model==0)
		echo "<div class='price_box'> $". $price . " / job</div>";
	else 
		echo "<div class='price_box'>$". $price. " / hr</div>";
?>	 
    <div id ="service_title" class='title'>
<?php if ( ($seller_uid == $user || in_array($user, $admin) ) && $user): ?>
	  <b class="autogrow" id='1_<?php echo $lid; ?>' style="width: 500px"><?php echo $title; ?></b> 
      <a title='Click on service title to enable quick edit'><img src="<?php echo $domain_secondary;?>attachements/pencil.png" alt='edit'/></a>
<?php else: ?>
	  <p><?php echo $title; ?></p>
<?php endif; //if ($seller_uid == $user && $user) ?>
    </div>
    
      <div id='service_info'>
        <div id="service_pic_container">  
          <div id = "service_pictures">
          	<div id="pic_left">
<?php for ($counter1=1, $counter2=0, $picture_url_temp="" ; $counter1 <= $picture_count ; $counter1++, $counter2++): // Limits max picture dimension to 800x700 ?>
<?php
	$picture_url_temp = "http://img.gohoody.com/service_pictures/$picture_url[$counter2]";
	$picture_url_temp = str_replace(" ", "%20", $picture_url_temp);	
?>		

<?php if ($picture_count==1): //if only 1 picture?> 
			  <a onclick='show_picture(<?php if ($counter1+1 <= $picture_count) {echo $counter1+1;} else {echo "1";}  ?>);' >
              	<img id="only_pic<?php echo $counter1; ?>_img" src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $picture_url[$counter2]; ?>&w=594&zc=1" alt='' />
              </a>
<?php else: ?>              	
              <a onclick='show_picture(<?php if ($counter1+1 <= $picture_count) {echo $counter1+1;} else {echo "1";}  ?>);' >
              	<img id="pic<?php echo $counter1; ?>_img" src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $picture_url[$counter2]; ?>&w=533&zc=1" alt='' /> 
              </a>
<?php endif; //if ($picture_count==1) ?>

<?php endfor; //for ($counter1=1, $counter2=0, $picture_url_temp="" ; $counter1 <= $picture_count ; $counter1++, $counter2++) ?>
		  	</div><!--end #pic_left-->
           
           	<div id='pic_right'>	
<?php if ($picture_count>1): //if more than 1 picture?> 
<?php for ($counter1=11, $counter2=0, $picture_url_temp="" ; $counter2 < $picture_count ; $counter1++, $counter2++): // Limits max picture dimension to 800x700 ?>
<?php
        $picture_url_temp = "http://img.gohoody.com/service_pictures/$picture_url[$counter2]";
        $picture_url_temp = str_replace(" ", "%20", $picture_url_temp);	
?>
                  <a onclick='show_picture(<?php echo $counter1-10; ?>);' > 
                  	<img id="pic<?php echo $counter1; ?>_img" 
                    src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $picture_url[$counter2]; ?>&w=50&h=50&zc=1" alt='' />
                  </a> 
<?php endfor; //for ($counter1=1, $counter2=0, $picture_url_temp="" ; $counter1 <= $picture_count ; $counter1++, $counter2++) ?>	
<?php endif; //if ($picture_count>1) ?>  
		   	</div><!--end #pic_right-->
          </div> <!--end of #service_pictures-->
          <div class='landscape_shadow'><img src='<?php echo $domain_secondary; ?>attachements/home_page/picture_tiles/pic_shadow_p2.png' width='618px' alt=''/></div>
        </div>
                
        <div id="service_description">          
          <div id="description_info">
<?php if ( ($seller_uid == $user || in_array($user, $admin) ) && $user): ?>
			<p class="autogrow" id='2_<?php echo $lid; ?>' style="width: 588px"><?php echo $listing_description; ?></p>
<?php else: ?>
			<p><?php echo $listing_description; ?></p>
<?php endif; //if ($seller_uid == $user && $user) ?> 
          </div>
        <!--  <div class='sub_border'> </div>-->

      </div> <!--end #service_description-->
      
      
      
    </div> <!--end #service_info-->  
    
    
    <div id='contact_section'>
      
      <div class="contact_button2">
            
        <?php if ($user): ?>
                    <a href='<?php 
							echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $seller_uid . "&user_uid=" . $user; 
					?>&action=1&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=545' 
            class='lightbox contact_link2'><img class="contact_icon3" src="<?php echo $domain_secondary;?>attachements/mail_icon.png">Contact <?php echo $seller_first_name; ?></a>
        <?php else: ?>
                    <a href='<?php 
							echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $seller_uid . "&user_uid=" . $user; 
					?>&action=1&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=220' 
            class='lightbox contact_link2'>
                    <img class="contact_icon3" src="<?php echo $domain_secondary;?>attachements/mail_icon.png">Contact <?php echo $seller_first_name; ?></a>
        <?php endif; //if ($user) ?>                         	
       </div> <!--end #contact_button-->
       
      
      <div class='price_cont'>
		  <?php       
              if ($price == 0)
                  echo "<div class='price_box_free2'>&nbsp;Free&nbsp;</div>";
              else if ($pricing_model==0)
                  echo "<div class='price_box2'> $". $price . " / job</div>";
              else 
                  echo "<div class='price_box2'>$". $price. " / hr</div>";
          ?>
          
          <?php if ($price != 0): ?>		
          <div id='bargin'>
         	<a href='<?php 
                                    echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $seller_uid . "&user_uid=" . $user; 
                            ?>&action=7&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=220' 
                    class='lightbox bargin_link'> Don't like the price? </a>
          </div>
          <?php endif; //if ($price != 0) ?>
      </div> 	
      
      <!--Paypal-->
      <!--<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="QD74L7NZ7LHS6">
        <input type="hidden" name="lc" value="CA">
        <input type="hidden" name="item_name" value="<?php echo $title; ?>">
        <input type="hidden" name="amount" value="<?php echo $price; ?> " >
        <input type="hidden" name="currency_code" value="CAD">
        <input type="hidden" name="button_subtype" value="services">
        <input type="hidden" name="tax_rate" value="0.000">
        <input type="hidden" name="shipping" value="0.00">
        <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
      </form>-->
 
       
       
   
   </div>
    
    
    <div id="extra_buttons">
        <div id="fb_like">
			<script type="text/javascript">_ga.trackFacebook();</script>
            <fb:like href="http://gohoody.com/service/<?php echo $lid; ?>/" show_faces="false" font="arial" width="320" send="true"></fb:like>
        </div>
        <div id="linkedin_share"><script type="IN/Share" data-url="http://gohoody.com/service/<?php echo $lid; ?>/" data-counter="right"></script></div>
        <div id="twitter">	<a	href="http://twitter.com/share" class="twitter-share-button"
                                data-url="http://gohoody.com/service/<?php echo $lid; ?>/"
                                data-via="gohoody"
                                data-text="Checking out this <?php echo $title; ?> service on Hoody"
                                data-count="horizontal">Tweet</a></div>
        <div id="google_plusone"><g:plusone size="medium" href='http://gohoody.com/service/<?php echo $lid; ?>/'></g:plusone></div>                
    </div>
    <div id = "tabs_block">
    <ul id='tabs_nav'>
      <li> <a id='tab1' class='tabs' onclick='show_tab1()'> Comments </a> </li>
 	  <li> <a id='tab2' class='tabs' onclick='show_tab2()'> Reviews (<?php echo $review_num; ?>)</a> </li>
	  <li> <a id='tab3' class='tabs' onclick='show_tab3()'> Past Customers (<?php echo $past_customers_num; ?>)</a> </li>
    </ul>
   
    <div id='tab_container'>
      <div id='tab1_content' class='fb_comments'>
	  	<script type="text/javascript">_ga.trackFacebook();</script>
        <fb:comments class='fbcomments' href="http://gohoody.com/service/<?php echo $lid; ?>/"  num_posts="5" width="578" notify="true" title="Hoody: <?php echo $page_title; ?>"></fb:comments>
		<?php	new SEO_FBComments; ?>
      </div>
      <div id='tab2_content' class='reviews'>
        
        <?php if (!($rating_num > 0)): ?>        
        	<div class='indv_review'>
            <div class='review_left_section'>
              <img class='customer_pic' src='<?php echo $domain_secondary;?>attachements/hoody_review.png' width='60px' alt='' /> 
              <div class='customer_name'>Hoody the Groundhog</div>
            </div> <!--end of #left_section-->
            <div class='review_right_section'>
              <div class='point'><img src='<?php echo $domain_secondary;?>attachements/triangle_left.png' /></div>	
              <div class='review_sect'>
				<div id='review_content'>
					<div class='no_content'> Hmmmm... It seems that no one has left a review yet.  </div>
           			<div class='no_content2'> Be the first to write a review! </div>
                </div>
              </div>
            </div>
          </div>	
        <?php endif; //if (!($rating_num > 0) || ($review == NULL)) ?>        
<?php if ($rating_num > 0): ?>     
<?php $base_rating = 0; ?>
<?php while($rating_row = mysql_fetch_array($rating_result)): ?>
<?php
	extract($rating_row);	
	$base_rating = $base_rating + $rating;
	$loop_query = "SELECT pic_square,name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$loop_result = mysql_query($loop_query) or die (fatal_error(267, $user, $user, $today, $loop_query, mysql_error()));
	$loop_row = mysql_fetch_array($loop_result,MYSQL_ASSOC);
	extract($loop_row);
	$negative_rating = $rating_num - $base_rating;
	$relationship = "";
	// actual friend checking
	// Check for second degree of separation
	// facebook doesn't allow us to extract seller's friendlist. 
	// need to find out more about our authorized level of access
	// before we can proceed
	// Request for seller's Facebook Friendlist
	if ($fb_uid == $user)
		$relationship = " - that's you!";
	else
	{
		foreach ($buyer_fb_friendlist as $key => $friend_fbuid)
		{
			if ($friend_fbuid[uid1] == $fb_uid)
			{
				$relationship = " <img id=\"fb_icon2\" src=\"http://img.gohoody.com/attachements/friends.png\" alt=\"Facebook Friend\" /> ";
				break;
			}
		}
	} // end of else
	
	$transaction_time = better_time($transaction_date,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
<?php if ($review != NULL): ?>
          <div class='indv_review'>
            <div class='review_left_section'>
              <a href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" >
              	<img class='customer_pic' src='<?php echo $pic_square; ?>' width='60px' alt='' /> 
              </a>
              <div class='customer_name'>
              	<a href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" ><?php echo $name; ?></a>
				<span class='transaction_time'><?php echo $relationship; ?></span>
              </div>
            </div> <!--end of #left_section-->
              
            <div class='review_right_section'>
              <div class='point'><img src='<?php echo $domain_secondary;?>attachements/triangle_left.png' /></div>	
              <div class='review_sect'>
			  	<div class='transaction_time'><?php echo $transaction_time; ?></div>
				<div id='review_content'><?php echo nl2br($review); ?></div>
              </div>
            </div>
          </div>	

<?php endif; //if ($review != NULL) ?>
<?php endwhile; //while($rating_row = mysql_fetch_array($rating_result)) ?>  
<?php $percentage_rating = ($base_rating / $rating_num) * 100; ?>
<?php endif; //if ($rating_num > 0) ?>

			<?php if ($contact_id): ?>
 	<a href=	'<?php 
					echo	$working_directory . "review_popup.php?rid=" . $contact_id . "&user_uid=" . $user; 
				?>&lightbox[modal]=true&lightbox[iframe]=true&lightbox[width]=470&lightbox[height]=530' 
            	class='lightbox contact_link2 review_link'>
				<?php if ($review_status == 1): ?>Update your review
                <?php else: ?>Submit a review
                <?php endif; //if ($review_status == 1) ?></a>
<?php endif; //if ($user) ?> 

    	</div>
        <div id='tab3_content' class='past_customers'>
        
        <?php if (!($past_customers_num > 0) || ($past_customers_num == NULL)): ?>
        	<div class='indv_review'>
            <div class='review_left_section'>
              <img class='customer_pic' src='<?php echo $domain_secondary;?>attachements/hoody_review.png' width='60px' alt='' /> 
              <div class='customer_name'>Hoody the Groundhog</div>
            </div> <!--end of #left_section-->
              
            <div class='review_right_section'>
              <div class='point'><img src='<?php echo $domain_secondary;?>attachements/triangle_left.png' /></div>	
              <div class='review_sect'>
			  	
				<div id='review_content'>
					<div class='no_content'> Yikes !  You're telling me that no one has used this service !?  </div>
           			<div class='no_content2'> Show some love, try out this service today! </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; //if (!($rating_num > 0) || ($review == NULL)) ?>
        
<?php if ($past_customers_num > 0): ?>  
<?php while($past_customers_row = mysql_fetch_array($past_customers_result)): ?>
<?php
	extract($past_customers_row);	
	$loop_query = "SELECT pic_square,name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$loop_result = mysql_query($loop_query) or die (fatal_error(269, $user, $user, $today, $loop_query, mysql_error()));
	$loop_row = mysql_fetch_array($loop_result,MYSQL_ASSOC);
	extract($loop_row);
	
	$relationship = "";
	// actual friend checking
	if ($fb_uid == $user)
		$relationship = "";
	else
	{
		foreach ($buyer_fb_friendlist as $key => $friend_fbuid)
		{
			if ($friend_fbuid[uid1] == $fb_uid)
			{
				$relationship = " <img id=\"fb_icon3\" src=\"http://img.gohoody.com/attachements/friends.png\" alt=\"Facebook Friend\" /> ";
				break;
			}							
		}
	} // end of else
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(270, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
            <div class='review_left_section'>
              <a href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" >
              	<img class='customer_pic' src='<?php echo $pic_square; ?>' width='60px'  alt='' />
              </a>
              <div class='customer_name'>
              	<a href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" ><?php echo $name; ?></a><?php echo $relationship; ?>
              </div>
            </div> 	
<?php endwhile; //while($past_customers_row = mysql_fetch_array($past_customers_result)) ?>
<?php endif; //if ($past_customers_num > 0) ?>
        </div>          
      </div> <!--end #tab_container-->
    </div> <!--end of #tabs_block--> 
  </div> <!--end #service_block-->
  
  <div id = "seller_block">
     <div class="contact_button">
        	
<?php if ($user): ?>
			<a href='<?php 
							echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $seller_uid . "&user_uid=" . $user; 
					?>&action=1&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=545' 
            class='lightbox contact_link'><img class="contact_icon2" src="<?php echo $domain_secondary;?>attachements/mail_icon.png">Contact <?php echo $seller_first_name; ?></a>
<?php else: ?>
			<a href='<?php 
							echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $seller_uid . "&user_uid=" . $user; 
					?>&action=1&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=220' 
            class='lightbox contact_link'>
            <img class="contact_icon2" src="<?php echo $domain_secondary;?>attachements/mail_icon.png">Contact <?php echo $seller_first_name; ?>  </a>
<?php endif; //if ($user) ?>                         	
     </div> <!--end #contact_button-->
  
<?php
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$seller_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(271, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row4['profile_name'];
?>  
     <div class='grey_box'>
          <div id="seller_name" class='grey_title'>		  	
			<div id='seller_name_text'> <?php echo $seller_name ;?> </div>
          </div>
          <div id="seller_pic_sect">
            <div id="seller_pic">
            	<a href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" ><img src="<?php echo $seller_pic3; ?>" width='60px' alt="" /></a>
            </div>
          </div>
          <div id='seller_right_sect'>  
   		  <div class='common_interest'>
		  <?php if($seller_uid > 0): ?>
            <img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/verified_icon.png' />
            <p class='interest_content'>Verified Seller</p>
          <?php else: ?>
            <img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/unverified_icon2.png' />
            <p class='interest_content'>Unverified Seller</p>
          <?php endif; ?>
          </div>       
         	 
<?php if ($user && $seller_uid != $user): ?>          
<?php 
	$common_interests 	= count(common_check($seller_uid, $user, 1)) 
						+ count(common_check($seller_uid, $user, 2)) 
						+ count(common_check($seller_uid, $user, 3)) 
						+ count(common_check($seller_uid, $user, 4)) 
						+ count(common_check($seller_uid, $user, 5)) 
						+ count(common_check($seller_uid, $user, 6));
	$common_friends = count(common_check($seller_uid, $user, 9));
?>
<?php if($common_friends): ?>  
          <div class='common_interest'>
              <img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_friend.png' />
              <p class='interest_content'><?php echo $common_friends; ?> friends in common</p>
          </div>   
<?php endif; //if($common_friends) ?>
<?php if($common_interests): ?>
          <div class='common_interest'>
              <img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_hand.png' />
              <p class='interest_content'><?php echo $common_interests; ?> interests in common</p>
          </div>  	
<?php endif; //if($common_interests) ?>
<?php endif; //if ($user && $seller_uid != $user) ?>
         <div id='see_more'><a id='see_more_link' href="<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>" >See Profile</a></div> 
    	</div><!--end #seller_right_sect-->
      </div> <!--end .grey_box-->
<?php if ($other_listing_num > 1): ?>
      <div class='grey_box'>
        <div class='grey_title'><?php echo $seller_first_name ;?>'s Services</div>
<?php $URL = ""; ?>
<?php while($other_listing_row = mysql_fetch_array($other_listing_result)): ?>
<?php
	extract($other_listing_row);
	if ($listing_id == $lid)
		continue;
	
	// Numbers for confirmed buyers
	$confirmed_buyer_query = "SELECT fb_uid FROM Contact_History WHERE listing_id='$listing_id'&&transaction_status=1 ORDER BY contact_id";
	$confirmed_buyer_result = mysql_query($confirmed_buyer_query) or die (fatal_error(272, $user, $user, $today, $confirmed_buyer_query, mysql_error()));		
	$confirmed_buyer_num = mysql_num_rows($confirmed_buyer_result);	
	
	// Add a thumbnail picture for each service
	// Extract pictures for the listing
	$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (fatal_error(273, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	extract($picture_row);
	$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
	$url_result = mysql_query($url_sql) or die (fatal_error(274, $user, $user, $today, $url_sql, mysql_error()));
	$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
	extract($url_row);
?>
                    <div class='other_listings'>
                       <a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'> 
                       	<?php if($URL == "hoodylogo.jpg"): ?>
                        	<img class='customer_pic' src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/hoodylogo2.jpg&h=70&w=70&zc=1'  alt='' />
                        <?php else: ?>
                            <img class='customer_pic' src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=70&w=70&zc=1'  alt='' />
                        <?php endif; ?>
                       </a>
                       <div class = 'item_name'><a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'><?php echo $title; ?></a> </div>
                       <div class='list_price'><?php 
													  if ($price==0)
														echo "FREE"; 
													  else if (!$pricing_model)
														echo "$".$price." Per Job";
													  else if ($pricing_model)
														echo "$".$price." Per Hour";
												?></div> <!--end .list_price-->
                    </div> <!--end #other_listings-->
<?php endwhile; //while($other_listing_row = mysql_fetch_array($other_listing_result))  ?>
     </div> <!--end .grey_box-->
<?php endif; //if ($other_listing_num > 1) ?>  
     
     <div class='grey_box'>
     	<div class='grey_title'>Service Location</div>
        
        
        


<?php if ($listing_location ==1): ?>
			<div id='home_icon'><img src="<?php echo $domain_secondary;?>attachements/house.png"  /></div>
            <div id='home_msg'>This service takes place at the buyer's home</div>
<?php elseif ($listing_location ==3): ?>
			<div id='virtual_icon'><img src="<?php echo $domain_secondary;?>attachements/virtual.png"  /></div>
            <div id='home_msg'>This service takes place virtually (anywhere)</div>
<?php else: //if ($listing_location ==1) ?> 
			<div id='map_display'><div id='map_canvas' style='width: 248px; height: 172px;'> </div></div>
<?php endif; //if ($listing_location ==1) ?>  




     </div> <!--end .grey_box-->    
  </div> <!--end #seller_block-->
  </div> <!--end #content-->	
<div id="foot_sect"><?php include "html/footer.inc"; ?></div>


</body>
</html>