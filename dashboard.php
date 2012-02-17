<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory;
 
	$page_title = $facebook_first_name . "'s Dashboard";	
	
	//go back to index.php if the user accessing this page hasn't already logged in 
    if (!$user)
		header("Location: ". $working_directory . "");
	else
	{
		// Part of the universal notification system. $update_status returns 1 if there's been an update to any data in the database
		$update_status_address = 0;
		$update_status_misc = 0;
	
		// Extract address info from User_Address table
		// Add lng/lat component
		$address_sql = "SELECT city,state,country,area_code,street FROM User_Address WHERE fb_uid='$user'";
		$result = mysql_query($address_sql) or die (fatal_error(54, $user, $user, $today, $address_sql, mysql_error()));
		$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
		$database_city = $row1['city'];
		$database_state = $row1['state'];
		$database_country = $row1['country'];
		$database_areacode = $row1['area_code'];
		$database_street = $row1['street'];
		
		// Extract service preference info from Basic_User_Information table
		$service_sql = "SELECT * FROM Basic_User_Information WHERE fb_uid='$user'";
		$result = mysql_query($service_sql) or die (fatal_error(55, $user, $user, $today, $service_sql, mysql_error()));
		$service_row = mysql_fetch_array($result,MYSQL_ASSOC);
		extract($service_row);
	
		if ($_POST[location1] == "street_address" && ($_POST[new_street] != "" || $_POST[new_state] != "" || $_POST[new_city] != "") && ($_POST[new_street] != $database_street || $_POST[new_state] != $database_state || $_POST[new_city] != $database_city))
		{	
			if ($_POST[new_street] != $database_street)
				$database_street = mysql_real_escape_string($_POST[new_street]);
			if ($_POST[new_state] != $database_state)
				$database_state = mysql_real_escape_string($_POST[new_state]);
			if ($_POST[new_city] != $database_city)
				$database_city = mysql_real_escape_string($_POST[new_city]);
			$database_areacode = '';
			$_POST[new_areacode] = '';
	
			// Use Street Address for geocoding
			if($database_street || (!$database_areacode && $database_state) || (!$database_areacode && $database_city))
				$lnglat = geocoding($database_street,$database_city,$database_state,$database_country,NULL);
		
			$lng = $lnglat["lng"];
			$lat = $lnglat["lat"];
		
			$address_sql = "UPDATE User_Address SET street='$database_street',state='$database_state'
			,city='$database_city',area_code='',lng='$lng',lat='$lat' WHERE fb_uid='$user'";
			$address_result = mysql_query($address_sql) or die (fatal_error(56, $user, $user, $today, $address_sql, mysql_error()));
			$update_status_address = 1;
		} //End of if
		
		//inactivate a service
		if (isset($_GET['inactivate'])) 
		{	
			//Typecast it to an integer:
			$inactivate = (int) $_GET['inactivate'];
			//An invalid $_GET['inactivate'] value would be typecast to 0
			
			//$inactivate must have a valid value
			if ($inactivate > 0) 
			{				
				//Get the information from the database for this service:
				$query = "SELECT * FROM Listing_Overview WHERE listing_id=$inactivate&&fb_uid=$user";
				$result = mysql_query($query) or die (fatal_error(57, $user, $user, $today, $query, mysql_error()));
				$num = mysql_num_rows($result);
				
				//service listing not found
				if ($num == 0) 
					header("Location: " . $working_directory . "dashboard/");
				
				//Proceed to inactivate the listing
				$query = "UPDATE Listing_Overview SET inactive_time='$today',status=0 WHERE listing_id='$inactivate'";
				$result = mysql_query($query) or die (fatal_error(58, $user, $user, $today, $query, mysql_error()));
				$update_status_misc = 1;
			} // End of if ($inactivate > 0)
		} // End of if (isset($_GET['inactivate']))	
		
		//delete a service from user's listed service list
		if (isset($_GET['delete'])) 
		{
			//Typecast it to an integer:
			$delete = (int) $_GET['delete'];
			//An invalid $_GET['delete'] value would be typecast to 0
			
			//$inactivate must have a valid value
			if ($delete > 0) 
			{				
				//Get the information from the database for this service:
				$query = "SELECT * FROM Listing_Overview WHERE listing_id=$delete&&fb_uid=$user";
				$result = mysql_query($query) or die (fatal_error(59, $user, $user, $today, $query, mysql_error()));
				$num = mysql_num_rows($result);
				
				//service listing not found
				if ($num == 0) 
					header("Location: " . $working_directory . "dashboard/");
				
				//Proceed to "delete" the listing, ie, set status to 2
				$query = "UPDATE Listing_Overview SET status=2 WHERE listing_id='$delete'";
				$result = mysql_query($query) or die (fatal_error(60, $user, $user, $today, $query, mysql_error()));
				$update_status_misc = 1;
			} // End of if ($delete > 0)
		} // End of if (isset($_GET['delete']))	
		
		//remove a service from user's interested service list
		if (isset($_GET['remove'])) 
		{	
			//Typecast it to an integer:
			$remove = (int) $_GET['remove'];
			//An invalid $_GET['delete'] value would be typecast to 0
			
			//$inactivate must have a valid value
			if ($remove > 0) {
							
				//Get the information from the database for this service:
				$query = "SELECT * FROM Contact_History WHERE contact_id=$remove&&fb_uid=$user";
				$result = mysql_query($query) or die (fatal_error(61, $user, $user, $today, $query, mysql_error()));
				$num = mysql_num_rows($result);
				
				//service listing not found
				if ($num == 0) 
					header("Location: " . $working_directory . "dashboard/");
				
				//Proceed to "delete" the listing, ie, set status to 2
				$query = "UPDATE Contact_History SET transaction_status=2 WHERE contact_id='$remove'";
				$result = mysql_query($query) or die (fatal_error(62, $user, $user, $today, $query, mysql_error()));
				$update_status_misc = 1;
			} // End of if ($delete > 0)
		} // End of if (isset($_GET['delete']))	
	
		// Check if the seller is a Facebook friend
		// Request for buyer's Facebook Friendlist
		$friendlist_sql = "SELECT * FROM Friendlist WHERE uid1='$user'";
		$result = mysql_query($friendlist_sql) or die (fatal_error(89, $user, $user, $today, $friendlist_sql, mysql_error()));
		$num = mysql_num_rows($result);
		for ($i=0; $i<$num; $i++)
		{
			$friend_uid=mysql_result($result,$i,"uid2");
			$buyer_fb_friendlist[] = $friend_uid;
		}
	
		// Query on top level data for all the tabs
		// Select all active listings from Listing_Overview table
		$active_listing_query = "SELECT * FROM Listing_Overview WHERE fb_uid=$user&&status=1 ORDER BY listing_id";
		$active_listing_result = mysql_query($active_listing_query) or die (fatal_error(63, $user, $user, $today, $active_listing_query, mysql_error()));
		$active_listing_num = mysql_num_rows($active_listing_result);	
	
		// Select all active listings from Listing_Overview table
		$inactive_listing_query = "SELECT * FROM Listing_Overview WHERE fb_uid = $user&&status=0 ORDER BY listing_id";
		$inactive_listing_result = mysql_query($inactive_listing_query) or die (fatal_error(64, $user, $user, $today, $inactive_listing_query, mysql_error()));
		$inactive_listing_num = mysql_num_rows($inactive_listing_result);	
		
		// Select all categories from Listing_Overview table
		$interested_service_query = "SELECT listing_id,contact_id,contact_time FROM Contact_History WHERE fb_uid='$user'&&transaction_status=0 ORDER BY contact_id";
		$interested_service_result = mysql_query($interested_service_query) or die (fatal_error(65, $user, $user, $today, $interested_service_query, mysql_error()));
		$interested_service_num = mysql_num_rows($interested_service_result);	
		
		// Select all categories from Listing_Overview table
		$bought_service_query = "SELECT listing_id,contact_id,contact_time FROM Contact_History WHERE fb_uid='$user'&&transaction_status=1 ORDER BY contact_id";
		$bought_service_result = mysql_query($bought_service_query) or die (fatal_error(66, $user, $user, $today, $bought_service_query, mysql_error()));	
		$bought_service_num = mysql_num_rows($bought_service_result);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody: Dashboard"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/dashboard"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/logo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="Hoody: Dashboard"/>
            
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php print($page_title) ?></title>

<!--CSS Begins-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/user_dashboard.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/slidingtabs_dashboard.css" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.lightbox.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip2.css" />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">
<!--CSS Ends-->

<!--Javascript Begins-->
<!-- Facebook Javascript API -->
<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!-- jQuery library -->
<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.easing.1.3.js"></script> 
<script src="<?php echo $working_directory; ?>javascript/animation.js" type="text/javascript"></script> 
    
<!-- qTip -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-qtip.js"></script>

<!--Javascript for popupbox-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.lightbox.js"></script>
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->
<!-- Jeditable -->
<script src="<?php echo $working_directory; ?>javascript/jquery.jeditable.js" type="text/javascript"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery.jeditable.autogrow.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.autogrow.js"></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.gritter.js"></script>
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
           minHeight  : 32
        }
    });
	
	$("#7_<?php echo $user; ?>").mouseover (function() {
		$(this).css("background-color", "#ffffd3")
	});
	$("#8_<?php echo $user; ?>").mouseover (function() {
		$(this).css("background-color", "#ffffd3")
	});
	$("#6_<?php echo $user; ?>").mouseover (function() {
		$(this).css("background-color", "#ffffd3")
	});
	$("#11_<?php echo $user; ?>").mouseover (function() {
		$(this).css("background-color", "#ffffd3")
	});
	$("#10_<?php echo $user; ?>").mouseover (function() {
		$(this).css("background-color", "#ffffd3")
	});	
	
	$("#7_<?php echo $user; ?>").mouseout (function() {
		$(this).css("background-color", "")
	});
	$("#8_<?php echo $user; ?>").mouseout (function() {
		$(this).css("background-color", "")
	});
	$("#6_<?php echo $user; ?>").mouseout(function() {
		$(this).css("background-color", "")
	});
	$("#10_<?php echo $user; ?>").mouseout(function() {
		$(this).css("background-color", "")
	});
	$("#11_<?php echo $user; ?>").mouseout(function() {
		$(this).css("background-color", "")
	});
	
	$("#7_<?php echo $user; ?>").focusout (function() {
		$(this).css("background-color", "")
	});
	$("#8_<?php echo $user; ?>").focusout (function() {
		$(this).css("background-color", "")
	});
	$("#6_<?php echo $user; ?>").focusout(function() {
		$(this).css("background-color", "")
	});
	$("#10_<?php echo $user; ?>").focusout(function() {
		$(this).css("background-color", "")
	});
	$("#11_<?php echo $user; ?>").focusout(function() {
		$(this).css("background-color", "")
	});
});
</script>

</head>
<body <?php if ($development_status) echo 'onLoad="javascript:pageTracker._setVar(\'hoody-notrack\')"'; ?>>
		
<?php 
	flush(); 
	include "html/title_bar_new2.inc";  
?>
  <div id='container'>
  
  	<div id='left_sect'>
  
        <ul id='tabs_nav'>
          <li class="list_title" id="sort_by">My Dashboard</li>	
          <li class="list_item"> <a id='tab1' class='tabs_link' onclick='show_tab1()'> Edit Account </a> </li>
    <?php if ($active_listing_num > 0): ?>     
            <li class="list_item"> <a id='tab2' class='tabs_link' onclick='show_tab2()'> Active Services </a> </li>
    <?php endif; ?>  
    <?php if ($inactive_listing_num > 0): ?>   
            <li class="list_item"> <a id='tab3' class='tabs_link' onclick='show_tab3()'> Inactive Services </a> </li>
    <?php endif; ?>
    <?php if ($interested_service_num > 0): ?>    
            <li class="list_item"> <a id='tab4' class='tabs_link' onclick='show_tab4()'> Watch List </a> </li>
    <?php endif; ?>
    <?php if ($bought_service_num > 0): ?>
            <li class="list_item"> <a id='tab5' class='tabs_link' onclick='show_tab5()'> Purchased Services </a> </li>
    <?php endif; ?>
        </ul>
    </div> <!--end #left_sect-->
    
    <div id='tab_container'>
      <!-- Start of Tab 1 -->
      <div id='tab1_content'>
      	<div id="user_pic_container"><div id="userinfo_pic"><img src="<?php echo $pic_big; ?>" alt="" /></div></div>
        <div id="right_col">
           <div id="userinfo_email">
            <div class="info_title" >Contact Email</div>
            	<div class='field_title'>Primary email <img title="This email address is associated with your Facebook account. Messages from other Hoody users will be sent here"  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  /></div>
                <div class='email_cont'> 
                	<div class='email_address'><?php echo $email; ?></div>
                    <img id="fb_icon" height="14px" alt="(Facebook)" src="<?php echo $domain_secondary;?>attachements/facebook.png">
                </div> 	
                
            	<div class='field_title'>
                	<div class='field_title_text'>Optional email <img title="You can enter an additional email address here. Once specified, you will be able to recieve messages from Hoody in this inbox as well as your primary email inbox."  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  /></div>
                    
                </div>
               
                <div class='email_cont'><p class="autogrow" id='7_<?php echo $user; ?>' style="width: 300px"><?php echo $optional_email; ?></p></div>
          </div> <!--end of #userinfo_email-->
         
          <!--Address Info-->
          <div id='address_section'>
            <div class="info_title2">Address Info 
            <img title="By providing your address information, we will tailor your Hoody experience based on your location. It is our top priority to make sure your personal information is safe and private."  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  /></div>
            
            
              <div id='country'>
                <div class='field_title' id='country_title'>Country: </div>
                <div class='country_field'><p class="autogrow" id='10_<?php echo $user; ?>' style="width: 100px"><?php echo $database_country; ?></p></div>
              </div>
            
            <form id="info_form" action='<?php echo $working_directory; ?>dashboard/' method='POST'>
              <div id='street'>
                <div id='radio_street'>
                	<input type="radio" id = "street_radio_button" name="location1" value="street_address" <?php 
																							if ($database_street || (!$database_areacode && $database_state) || (!$database_areacode && $database_city))
																								echo "checked";
																											?>> Street Address 
                	<img title="Street address will allow us to determine your EXACT location. This information is used for finding services near you and for posting services."  
                    class='qmark' src="<?php echo $working_directory; ?>attachements/question.png" width='13px' />
                </div>
                
                <div id="address_fields">
                  <div class='address_field_title'>Street: </div>
                  <div class='address_field'><input type="text" name="new_street" input="input" id="new_street" 
                  value="<?php if($_POST['new_street'] == "") {echo $database_street;} echo $_POST['new_street'] ?>" /></div>
                 
                  <div class='address_field_title'>City: </div>
                  <div class='address_field'><input name="new_city" type="text" input="input" id="new_city" 
                  value="<?php if($_POST['new_city'] == "") {echo $database_city;} echo $_POST['new_city'] ?>" /></div>
                  
                  <div class='address_field_title'>State/Province: </div>
                  <div class='address_field'><input name="new_state" type="text" input="input" id="new_state" 
                  value="<?php if($_POST['new_state'] == "") {echo $database_state;} echo $_POST['new_state'] ?>" /></div>
               </div>
              </div>
              
              <div id='postal'>
                <div id='radio_postal'>
                	<input type="radio" id = "postal_radio_button" name="location1" value="area_code_address" <?php
                													if (($database_areacode && !$database_street) || (!$database_state && !$database_city && !$database_street))
                    													echo "checked";
																												?>> Postal Code 
                	<img title="Postal code will only provide your APPROXIMATE location, This information is used for finding services near you and for posting services."  
                    class='qmark' src="<?php echo $working_directory; ?>attachements/question.png" width='13px' />
                </div>
                
                <div id='postal_fields'>
                  <div class='address_field_title'>Postal Code: </div>
                  <div class='address_field'><p class="autogrow" id='11_<?php echo $user; ?>' style="width: 300px"><?php echo $database_areacode; ?></p></div>
                </div>
              </div>
                  
              <div class='save_button'><button type="submit" name="button47" class='dashboard_button'> Save </button></div>
             </form>          
            </div> <!--end of #address_section-->
            
            <div class="info_title" id='linkedin_title'>Linkedin Profile <img title="A LinkedIn profile is great for showcasing your qualifications. Your potential customers can get to know you better and feel more comfortable buying your services. You can find your Linkedin Public Profile URL in your Linkedin Profile page."  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  /> </div>
              <div class='field_title' id='linkedin_profile'>Linkedin Public Profile URL </div>
              <div id='linkedin_input'>
             	 <p class="autogrow" id='8_<?php echo $user; ?>' style="width: 480px"><?php echo $linkedin_profile; ?></p> 
              </div>  
          
            <div class="info_title">About Me <img title="Tell people a little bit about yourself!"  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  /> </div>
			<div id='userinfo_about'>
              <p class="autogrow" id='6_<?php echo $user; ?>' style="width: 480px"><?php echo nl2br($about_me); ?></p>
            </div>
        </div> <!--end of #right_col-->  
      </div>
      <!-- End of Tab 1 -->
    
      <!-- Start of Tab 2 -->
      <div id='tab2_content'>
<?php if ($active_listing_num > 0): ?>
        <div id="active_listings_blcok">
<?php while ($active_listing_row = mysql_fetch_array($active_listing_result)): ?>
<?php	
	extract($active_listing_row);
	
	$URL = "";
	$picture_id_1 = "";
	
	// Query for confirmed buyers
	$confirmed_buyer_query = "SELECT contact_time,fb_uid FROM Contact_History WHERE listing_id='$listing_id'&&transaction_status=1 ORDER BY contact_id";
	$confirmed_buyer_result = mysql_query($confirmed_buyer_query) or die (minor_error(67, $user, $user, $today, $confirmed_buyer_query, mysql_error()));		
	$confirmed_buyer_num = mysql_num_rows($confirmed_buyer_result);
	
	// Query for interested buyers	
	$interested_buyer_query = "SELECT contact_time,fb_uid FROM Contact_History WHERE listing_id='$listing_id'&&transaction_status=0 ORDER BY contact_id";
	$interested_buyer_result = mysql_query($interested_buyer_query) or die (minor_error(68, $user, $user, $today, $interested_buyer_query, mysql_error()));	
	$interested_buyer_num = mysql_num_rows($interested_buyer_result);
	
	// Extract pictures for the listing
	$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (minor_error(69, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	extract($picture_row);
	$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
	$url_result = mysql_query($url_sql) or die (minor_error(70, $user, $user, $today, $url_sql, mysql_error()));
	$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
	extract($url_row);
	
	$listed_time = better_time($listed_time,$today); 
?>								
        <div class='indv_active_listing'>
<?php if($URL): ?>              
          <div class='listing_pic'>
            <div class='listing_pic_img'>
              <a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'>
              <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=100&w=100&zc=1"/></a>
            </div>
           
          </div>
<?php endif; ?>              
          <div class='listing_right_col'>
              <div class='listing_info'>
                  <div class='listing_name'><a href='<?php echo $working_directory . "service/" . $listing_id. "/"; ?>'><?php echo $title; ?></a></div>
                  <div class='listing_date'><p> Active Since: <?php echo $listed_time; ?></p></div>
                  <div class='buttons_and_date'> 
                    <div class='listing_buttons'>
                      <ul>
                          <li><a class='button1' href='<?php echo $working_directory . "create/" . $listing_id . "/"; ?>'>Modify</a></li>
                          <li class='button_1'><a href='<?php echo $working_directory . "flyer/" . $listing_id . "/"; ?>' target='_blank'> Print Flyers </a> </li>
                          <li class='Inactive_button'><a class='button2' href='<?php echo $working_directory . "dashboard/inactivate/" . $listing_id . "/"; ?>'>Make Inactive</a></li>
                          <li class='delete_button'><a class='button2' href='<?php echo $working_directory . "dashboard/delete/" . $listing_id . "/"; ?>'>Delete</a></li>
                      </ul>
                    </div>	
                  </div> <!--end .buttons_and_date--> 
              </div> <!--end .listing_info-->
              <div class='listing_customers'>
<?php if ($interested_buyer_num > 0): ?>
              <div class='interested_customers'>
                <div class='customer_sect_bar'>
                    <div class='customer_sect_title'><p> Interested Customers </p></div>
                    <div class = 'hide_show'><p> Show </p></div>
                </div> <!--end .customer_sect_bar-->
<?php while ($interested_buyer_row = mysql_fetch_array($interested_buyer_result)): // loop for interested buyers ?>
<?php
	extract($interested_buyer_row);
										
	// Extract interested buyer info from Basic_User_Information table
	$buyer_sql = "SELECT name,email,pic_square FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(71, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	
	$contact_time = better_time($contact_time,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
                <div class='customer'>
                    <div class='customer_pic'>
                      <div class='customer_pic_img'><img src='<?php echo $pic_square; ?>' /></div>
                      
                    </div>
                    
                    <div class='customer_info'>
                        <div class ='customer_name'><p><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name; ?></a></p></div>
                        <div class='customer_date'><p> Last Contact: <?php echo $contact_time; ?> </p></div>
                    </div> <!--end .customer_info-->
                    <div class='customer_buttons'>
                        <ul>
                            <li class='button3'> 
                           		<a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=2&lightbox[width]=475&lightbox[height]=545&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact</a> 
                            </li>
                            <li class='button3'>
                            	<a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=3&lightbox[width]=475&lightbox[height]=545&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'>Confirm Sale</a> </li> 
                        </ul>
                    </div>
                </div> <!--end .customer-->
<?php endwhile; //while ($interested_buyer_row = mysql_fetch_array($interested_buyer_result)) ?>
				</div> <!--end .interested_customers-->
<?php endif; //if ($interested_buyer_num > 0) ?>
<?php if ($confirmed_buyer_num > 0 ): ?>
             <div class='confirmed_customers'>
                <div class='customer_sect_bar'>
                    <div class='customer_sect_title'><p> Past Customers </p></div>
                    <div class = 'hide_show'><p> Show </p></div>
                </div> <!--end .customer_sect_bar-->
<?php while ($confirmed_buyer_row = mysql_fetch_array($confirmed_buyer_result)): // loop for confirmed buyers ?>
<?php
	extract($confirmed_buyer_row);	
	
	// Extract interested buyer info from Basic_User_Information table
	$buyer_sql = "SELECT name,pic_square FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(72, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	// Extract transaction detail info
	$buyer_sql = "SELECT transaction_date FROM Confirmed_Transactions WHERE fb_uid='$fb_uid'&&listing_id='$listing_id'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(73, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	
	$transaction_date = better_time($transaction_date,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
              <div class='customer'>
                  <div class='customer_pic'>
                    <div class='customer_pic_img'><img src='<?php echo $pic_square; ?>' /></div>
                    
                  </div>
                  
                  <div class='customer_info'>
                      <div class ='customer_name'><p><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name; ?></a></p></div>
                      <div class='customer_date'><p> Transaction Date: <?php echo $transaction_date; ?> </p></div>
                  </div> <!--end .customer_info-->
                  <div class='customer_buttons'>
                      <ul>
                          <li> <a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=2&lightbox[width]=475&lightbox[height]=545&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact</a> </li>
                      </ul>
                  </div>
              </div> <!--end .customer-->
<?php endwhile; //while ($confirmed_buyer_row = mysql_fetch_array($confirmed_buyer_result)) ?>
			</div> <!--end .confirmed_customers-->
<?php endif; //if ($confirmed_buyer_num > 0 ) ?>
			</div> <!--end .listing_customers-->
			</div> <!--end .listing_right_col-->
		  </div> <!--end .indv_active_listing--> 
<?php endwhile; //while ($active_listing_row = mysql_fetch_array($active_listing_result)) ?>
		</div> <!--end </div> <!--end #active_listing_block-->
<?php endif; //if ($active_listing_num > 0): ?>
      </div>
      <!-- End of Tab 2 -->
    
      <!-- Start of Tab 3 -->
      <div id='tab3_content'>
<?php if ($inactive_listing_num > 0): ?>
        <div id="active_listings_blcok">
<?php while ($inactive_listing_row = mysql_fetch_array($inactive_listing_result)): ?>
<?php
	extract($inactive_listing_row);
										
	// Query for confirmed buyers
	$confirmed_buyer_query = 	"SELECT contact_time,fb_uid FROM Contact_History WHERE listing_id='$listing_id'&&transaction_status=1 ORDER BY contact_id";
	$confirmed_buyer_result = mysql_query($confirmed_buyer_query) or die (minor_error(74, $user, $user, $today, $confirmed_buyer_query, mysql_error()));		
	$confirmed_buyer_num = mysql_num_rows($confirmed_buyer_result);
	
	// Query for interested buyers	
	$interested_buyer_query = 	"SELECT contact_time,fb_uid FROM Contact_History WHERE listing_id='$listing_id'&&transaction_status=0 ORDER BY contact_id";
	$interested_buyer_result = mysql_query($interested_buyer_query) or die (minor_error(75, $user, $user, $today, $interested_buyer_query, mysql_error()));	
	$interested_buyer_num = mysql_num_rows($interested_buyer_result);
	
	// Extract pictures for the listing
	$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (minor_error(76, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	extract($picture_row);
	$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
	$url_result = mysql_query($url_sql) or die (minor_error(77, $user, $user, $today, $url_sql, mysql_error()));
	$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
	extract($url_row);
	
	$inactive_time = better_time($inactive_time,$today); 
?>
          <div class='indv_active_listing'>
            <div class='listing_pic'>
              <div class='listing_pic_img'>
                <a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'>
                <img src= "<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=100&w=100&zc=1"/></a>
              </div>
              
            </div>
            <div class='listing_right_col'>
                <div class='listing_info'>
                  <div class='listing_name'><a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'><?php echo $title; ?></a></div>
                  <div class='listing_date'><p>Inactive Since: <?php echo $inactive_time; ?></p></div>
                </div> <!--end .listing_info--> 
                  
            <div class='listing_buttons'>
              <ul>
                  <li class='Modify_button'> <a class='button1' href='<?php echo $working_directory . "create/" . $listing_id . "/"; ?>'>Make Active</a> </li>									
                  <li class='delete_button'> <a class='button2' href='<?php echo $working_directory . "dashboard/delete/" . $listing_id . "/"; ?>'>Delete</a> </li>
              </ul>
            </div>
                
            <div class='listing_customers'>
<?php if ($interested_buyer_num > 0): ?>
           <div class='interested_customers'>
              <div class='customer_sect_bar'>
                  <div class='customer_sect_title'><p>Interested Customers</p></div>
                  <div class = 'hide_show'><p> Show </p></div>
              </div> <!--end .customer_sect_bar-->
<?php while ($interested_buyer_row = mysql_fetch_array($interested_buyer_result)): // loop for interested buyers ?>
<?php
	extract($interested_buyer_row);
										
	// Extract interested buyer info from Basic_User_Information table
	$buyer_sql = "SELECT name,email,pic_square FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(78, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	
	$contact_time = better_time($contact_time,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
              <div class='customer'>
                  <div class='customer_pic'>
                    <div class='customer_pic_img'><img src='<?php echo $pic_square; ?>' /></div>
                   
                  </div>
                  
                  <div class='customer_info'>
                      <div class ='customer_name'><p><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name; ?></a></p></div>
                      <div class='customer_date'><p> Last Contact: <?php echo $contact_time; ?> </p></div>
                  </div> <!--end .customer_info-->
                  <div class='customer_buttons'>
                      <ul>
                          <li class='button3'><a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=2&lightbox[width]=470&lightbox[height]=530&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact Customer </a> </li>
                          <li class='button3'><a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=3&lightbox[width]=470&lightbox[height]=530&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'>Send Thank You email</a> </li>
                      </ul>
                  </div>
              </div> <!--end .customer-->                                     
<?php endwhile; //while ($interested_buyer_row = mysql_fetch_array($interested_buyer_result)) ?>
			</div> <!--end .interested_customers-->
<?php endif; //if ($interested_buyer_num > 0) ?>
<?php if ($confirmed_buyer_num > 0 ): ?>
          <div class='confirmed_customers'>
              <div class='customer_sect_bar'>
                  <div class='customer_sect_title'><p> Customers bought this service </p></div>
                  <div class = 'hide_show'><p> Show </p></div>
              </div> <!--end .customer_sect_bar-->
<?php while ($confirmed_buyer_row = mysql_fetch_array($confirmed_buyer_result)): // loop for confirmed buyers ?>
<?php
	extract($confirmed_buyer_row);
								
	// Extract interested buyer info from Basic_User_Information table
	$buyer_sql = "SELECT name,pic_square FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(79, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	// Extract transaction detail info
	$buyer_sql = "SELECT transaction_date FROM Confirmed_Transactions WHERE fb_uid='$fb_uid'&&listing_id='$listing_id'";
	$result3 = mysql_query($buyer_sql) or die (minor_error(80, $user, $user, $today, $buyer_sql, mysql_error()));
	$row3 = mysql_fetch_array($result3,MYSQL_ASSOC);
	extract($row3);
	
	$transaction_date = better_time($transaction_date,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?>
            <div class='customer'>
                <div class='customer_pic'>
                  <div class='customer_pic_img'><img src='<?php echo $pic_square; ?>' /></div>
                 
                </div>
                  
                <div class='customer_info'>
                    <div class ='customer_name'><p><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name; ?></a></p></div>
                    <div class='customer_date'><p> Transaction Date: <?php echo $transaction_date; ?> </p></div>
                </div> <!--end .customer_info-->
                
                <div class='customer_buttons'>
                    <ul><li class='button3'> <a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=2&lightbox[width]=470&lightbox[height]=530&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact Customer</a> </li></ul>
                </div>
            </div> <!--end .customer-->
<?php endwhile; //while ($confirmed_buyer_row = mysql_fetch_array($confirmed_buyer_result)) ?>
            </div> <!--end .confirmed_customers-->
<?php endif; //if ($confirmed_buyer_num > 0 ) ?>					
            </div> <!--end .listing_customers-->
          </div> <!--end .listing_right_col-->
        </div> <!--end .indv_active_listing--> 
<?php endwhile; //while ($inactive_listing_row = mysql_fetch_array($inactive_listing_result)) ?>
		</div> <!--end </div> <!--end #active_listing_block-->
<?php endif; //if ($inactive_listing_num > 0) ?>
      </div>
      <!-- End of Tab 3 -->
    
      <!-- Start of Tab 4 -->
      <div id='tab4_content'>
<?php if ($interested_service_num > 0): ?>
		<div id=\"active_listings_blcok\">
<?php     
	$listing_status = "undefined";
	$degree_separation = "";
?>
<?php while($row = mysql_fetch_array($interested_service_result)): ?>
<?php
	extract($row);	
										
	$loop_query = "SELECT fb_uid,title,price,status FROM Listing_Overview WHERE listing_id='$listing_id'";
	$loop_result = mysql_query($loop_query) or die (minor_error(81, $user, $user, $today, $loop_query, mysql_error()));
	$loop_row = mysql_fetch_array($loop_result,MYSQL_ASSOC);
	extract($loop_row);
		  
	if ($status == 0)
	  $listing_status = "(This listing is no longer active)";
	else if ($status == 1)
	  $listing_status = "";
	
	// Check if the seller is a Facebook friend
	// #############Only ckeck for 1 degrees of separation (immediate friend) for now
	// Request for buyer's Facebook Friendlist	
	if ($fb_uid == $user)
	  $degree_separation = "- you are the seller";
	else
	{
	  foreach ($buyer_fb_friendlist as $key => $friend_fbuid)
	  {
		  if ($friend_fbuid == $fb_uid)
		  {
			  $degree_separation = "<img id=\"friend_icon\" src=\"http://img.gohoody.com/attachements/friends.png\"  alt=\"Facebook Friend\" />";
			  break;
		  }
	  }
	} // end of else
	
	$seller_name_query = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$seller_name_result = mysql_query($seller_name_query) or die (minor_error(82, $user, $user, $today, $seller_name_query, mysql_error()));
	$result_row = mysql_fetch_array($seller_name_result,MYSQL_ASSOC);
	$name = $result_row['name'];		
	$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (minor_error(83, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	$picture_id_1 = $picture_row['picture_id_1'];
	$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
	$url_result = mysql_query($url_sql) or die (minor_error(84, $user, $user, $today, $url_sql, mysql_error()));
	$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
	$URL = $url_row['URL'];			
	
	$contact_time = better_time($contact_time,$today);
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
?> 
          <div class='indv_active_listing'>
            <div class='listing_pic'>
              <div class='listing_pic_img'>
                <a href='" . $working_directory . "service/$listing_id/'>
                <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=100&w=100&zc=1"/></a>
              </div>
             
            </div>
            <div class='listing_right_col'>
                <div class='listing_info'>
                    <div class='listing_name'><a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'><?php echo $title; ?></a></div>
                    <div class='listing_date'>
                        <div class='ils'><?php echo $listing_status; ?></div>
                        <div class='idp'><b>Seller: </b><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name . " " . $degree_separation; ?></a></div>
                        <div class='ict'><b>Last contacted the seller: </b><?php echo $contact_time; ?></div>
                    </div>
                  </div> <!--end .listing_info--> 
                    
                  <div class='listing_buttons'>
                    <ul>
                        <li class='button_1'> <a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=1&lightbox[width]=475&lightbox[height]=545&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact Seller </a> </li>									
                        <li class='button_1'> <a href='<?php echo $working_directory . "review_popup.php?rid=" . $contact_id . "&user_uid=" . $user; ?>&lightbox[modal]=true&lightbox[iframe]=true&lightbox[width]=470&lightbox[height]=530' class='lightbox'>Write a Review</a> </li>
                        <li class='button_2'> <a href='<?php echo $working_directory . "dashboard/remove/" . $contact_id . "/"; ?>'>Remove</a> </li>
                    </ul>
                  </div>
              </div> <!--end .listing_right_col-->
            </div> <!--end .indv_active_listing--> 
<?php $degree_separation = ""; ?>
<?php endwhile; //while($row = mysql_fetch_array($interested_service_result)) ?>
			</div> <!--end #active_listing_block-->
<?php endif; //if ($interested_service_num > 0) ?>
      </div>
      <!-- End of Tab 4 -->
      
      <!-- Start of Tab 5 -->
      <div id='tab5_content'>
<?php if ($bought_service_num > 0): ?>
		<div id="active_listings_blcok">
<?php		  
	$listing_status = "undefined";
	$degree_separation = "";													
?>
<?php while ($row = mysql_fetch_array($bought_service_result)): ?>
<?php
	extract($row);
	$review_status = "";
	  
	$loop_query = "SELECT fb_uid,title,price,status FROM Listing_Overview WHERE listing_id='$listing_id'";
	$loop_result = mysql_query($loop_query) or die (minor_error(85, $user, $user, $today, $loop_query, mysql_error()));
	$loop_row = mysql_fetch_array($loop_result,MYSQL_ASSOC);
	$fb_uid = $loop_row['fb_uid'];
	$title = $loop_row['title'];
	$price = $loop_row['price'];
	$status = $loop_row['status'];
	
	$seller_name_query = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
	$seller_name_result = mysql_query($seller_name_query) or die (minor_error(86, $user, $user, $today, $seller_name_query, mysql_error()));
	$result_row = mysql_fetch_array($seller_name_result,MYSQL_ASSOC);
	$name = $result_row['name'];
	
	$transaction_date_query = "SELECT transaction_date,review_status FROM Confirmed_Transactions WHERE listing_id='$listing_id'";
	$transaction_date_result = mysql_query($transaction_date_query) or die (minor_error(87, $user, $user, $today, $transaction_date_query, mysql_error()));
	$transaction_date_row = mysql_fetch_array($transaction_date_result,MYSQL_ASSOC);
	$transaction_date = $transaction_date_row['transaction_date'];	
	$transaction_review_status = $transaction_date_row['review_status'];						
	
	// Extract pictures for the listing
	$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
	$picture_result = mysql_query($picture_sql) or die (minor_error(87, $user, $user, $today, $picture_sql, mysql_error()));
	$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
	$picture_id_1 = $picture_row['picture_id_1'];
	$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
	$url_result = mysql_query($url_sql) or die (minor_error(88, $user, $user, $today, $url_sql, mysql_error()));
	$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
	$URL = $url_row['URL'];	
	
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$fb_uid'";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $user, $user, $today, $user_lookup_sql, mysql_error()));
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_profile_identifier = $row['profile_name'];
	
	// Check if the seller is a Facebook friend
	// #############Only ckeck for 1 degrees of separation (immediate friend) for now
	// Request for buyer's Facebook Friendlist	
	
	if ($fb_uid == $user)
	  $degree_separation = "- you are the seller";
	else
	{
		foreach ($buyer_fb_friendlist as $key => $friend_fbuid)
		{
			if ($friend_fbuid[uid1] == $fb_uid)
			{
				$degree_separation = "<img id=\"friend_icon\" src=\"http://img.gohoody.com/attachements/friends.png\"  alt=\"Facebook Friend\" />";
				break;
			}
		}
	} // end of else
	
	if ($status == 0)
		$listing_status = "(This listing is no longer active)";
	else if ($status == 1)
		$listing_status = "";
	
	if (!$transaction_review_status)
		$review_status = " (Review Pending)";
	  
	$contact_time = better_time($contact_time,$today);
?>
          <div class='indv_active_listing'>
            <div class='listing_pic'>
              <div class='listing_pic_img'>
                <a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'>
                <img src="<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=100&w=100&zc=1"/></a>
              </div>
             
            </div>
            <div class='listing_right_col'>
                <div class='listing_info'>
                    <div class='listing_name'><a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'><?php echo $title; ?></a> <?php echo $review_status; ?></div>
                    <div class='listing_date'>
                        <div class ='bls'><?php echo $listing_status; ?></div>
                        <div class ='bdp'><b>Seller:</b><a class='customer_name_link' href='<?php echo $working_directory . "profile/" . $user_profile_identifier . "/"; ?>'><?php echo $name . " " . $degree_separation; ?></a></div>
                        <div class ='bct'><b>Last contacted seller:</b> <?php echo $contact_time?></div>
                    </div> 
                    <div class='listing_buttons'>
                      <ul>
                          <li class='button_1'> <a href='<?php echo $working_directory . "contact_user.php?lid=" . $listing_id . "&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=1&lightbox[width]=475&lightbox[height]=545&lightbox[modal]=true&lightbox[iframe]=true' class='lightbox'> Contact Seller </a> </li>									
                          <li class='button_1'> <a href='<?php echo $working_directory . "review_popup.php?rid=" . $contact_id . "&user_uid=" . $user; ?>&lightbox[modal]=true&lightbox[iframe]=true&lightbox[width]=470&lightbox[height]=530' class='lightbox'>Write a Review</a> </li>
                      </ul>  
                    </div>
                </div> <!--end .listing_info--> 
              </div> <!--end .listing_right_col-->
            </div> <!--end .indv_active_listing--> 							   
<?php endwhile; //while ($row = mysql_fetch_array($bought_service_result)) ?>
			</div> <!--end #active_listing_block-->
<?php endif; //if ($bought_service_num > 0) ?>
      </div>
      <!-- End of Tab 5 -->
   </div>       
  </div> <!--end of #container--> 
  <div id="foot_sect"><?php include "html/footer.inc"; ?></div>
   
  <script type="text/javascript">
      jQuery(document).ready(function($){
      $('.lightbox').lightbox();});
	  $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('a[title]').qtip({
			   style: {classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'bottom left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {x: 5,  y: -10}},
			  hide: {// Helps to prevent the tooltip from hiding ocassionally when tracking!
				  fixed: true},})
		  $('img[title]').qtip({
			   style: {classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'bottom left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {x: 5,  y: -10}},
			  hide: {// Helps to prevent the tooltip from hiding ocassionally when tracking!
				  fixed: true},})});
  </script>
</body>
</html>