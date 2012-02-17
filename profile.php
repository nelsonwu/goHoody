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
	
	if (isset($_GET['name'])) {
		// Extract user info from Basic_User_Information table
		$profile_name = $_GET['name'];
		$name_lookup_sql = "SELECT fb_uid FROM User_Lookup WHERE profile_name='" . $profile_name . "'";
		$result = mysql_query($name_lookup_sql) or die (fatal_error(1, $user, $user, $today, $name_lookup_sql, mysql_error()));
		if (!mysql_num_rows($result))
			header("Location: " . $working_directory . "lost/");
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$fb_uid = $row['fb_uid'];
		
		$service_sql = "SELECT name,first_name,linkedin_profile,about_me,pic_big FROM Basic_User_Information WHERE fb_uid='" . $fb_uid . "'";
		$result = mysql_query($service_sql) or die (fatal_error(2, $user, $user, $today, $service_sql, mysql_error()));
		$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
		$user_name = $row3['name'];
		$user_first_name = $row3['first_name'];		
		$user_pic2 = $row3['pic_big'];
		$linkedin_profile = $row3['linkedin_profile'];
		$about_me = nl2br($row3['about_me']);
		$about_me = str_replace('   ','&nbsp;&nbsp;&nbsp;&nbsp;',$about_me);
		$about_me = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$about_me);							
	} // End of else if (isset($_GET['name']))	
	
	//If the name variable is not set
	else
		header("Location: " . $working_directory . "lost/");
	
	$page_title = $user_name . "'s Hoody profile page";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/profile.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.lightbox.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!--Facebook meta properties -->
    <meta property="og:title" content="<?php echo $page_title; ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="<?php echo $domain_primary . "profile/" . $user . "/"; ?>"/>
    <meta property="og:image" content=""/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="<?php echo $page_title; ?>"/>

<!-- Facebook Javascript API -->
<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!-- jQuery library -->
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>

<!--Javascript for popupbox-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.lightbox.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function($){$('.lightbox').lightbox();});
</script>

<!-- qTip -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-qtip.js"></script>

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
           minHeight  : 32
        }
    });
	$("#6_<?php echo $fb_uid; ?>").mouseover (function() {
		$("#6_<?php echo $fb_uid; ?>").css("background-color", "#ffffd3")
	});
	$("#6_<?php echo $fb_uid; ?>").mouseout(function() {
		$("#6_<?php echo $fb_uid; ?>").css("background-color", "")
	});
	$("#6_<?php echo $fb_uid; ?>").focusout(function() {
		$("#6_<?php echo $fb_uid; ?>").css("background-color", "")
	});
});
</script> 
</head>
<body <?php if ($development_status) echo 'onLoad="javascript:pageTracker._setVar(\'hoody-notrack\')"'; ?>>

<?php 
  flush();
  include "html/title_bar_new2.inc";
?>   
  <div id='content'> 
  	<div id='profile_section'>
        <div id='user_section'>
            <img id='user_profile_pic' src="<?php echo $user_pic2; ?>" alt="" />
            <div id='badge_section'>
            
            <?php if($fb_uid > 0): ?>
            	<img id='verified_badge' src='<?php echo $domain_secondary;?>attachements/verified_badge.png'  />
            <?php endif; ?>
            </div>
            
            
            <div id='user_name'> <?php echo $user_name;?>  </div>
            <div id='user_info_right'>
              <div id='contact_button'>
                <?php if ($user): ?>
                    <a href='<?php echo $working_directory . "contact_user.php?lid=0&fb_uid=" . $fb_uid . "&user_uid=" . $user; ?>&action=1&lightbox[iframe]=true&lightbox[width]=475&lightbox[height]=545' 
                     class='lightbox' id='contact_link'> 
                      <img class="contact_icon" src="<?php echo $domain_secondary;?>attachements/mail_icon.png"> Contact
                    </a>
                <?php else: ?>
                    <a id='contact_link' class='disabled' title='Please log in to contact <?php echo $user_first_name; ?>'> 
                      <img class="contact_icon" src="<?php echo $domain_secondary;?>attachements/mail_icon.png"> Contact
                    </a>
                <?php endif; //if ($user) ?>	            
              </div> <!--end #contact_button-->
              <div id='connections_section'> 
                  <?php if($fb_uid > 0): ?>
                  <div class='indv_connection'>
                      <a class="connection_link" href="http://www.facebook.com/profile.php?id=<?php echo $fb_uid; ?>" target="_blank">
                        <img id="fb_icon" src="<?php echo $domain_secondary;?>attachements/facebook_med.png" height='20px' alt="Facebook" />
                        <div class='connection_text'> Facebook Profile </div>
                      </a>
                  </div>
                  <?php endif; ?>
                  <?php if ($linkedin_profile): ?>
                  <div class='indv_connection'>
                      <a class="connection_link" href="<?php echo $linkedin_profile; ?>" target="_blank">
                        <img id="in_icon" src="<?php echo $domain_secondary;?>attachements/linkedin_med.png" height='20px' alt="Linkedin" />
                        <div class='connection_text'> LinkedIn Profile </div>
                      </a>
                  </div>
                  <?php endif; ?>   
              </div> <!--end #connections_section-->  
            </div> <!--end #user_info_right-->
            
            
        </div> <!--end #user_section-->
        <div id='about_user' class='grey_box'>
            <div class='grey_title'>About <?php echo $user_first_name;?> </div>
            <div class='sect_content'>
            <?php if ( ($fb_uid == $user || in_array($user, $admin) ) && $user): ?>
                <p class="autogrow" id='6_<?php echo $fb_uid; ?>' ><?php echo $about_me; ?></p>
            <?php else: ?>			  
                <p><?php echo $about_me; ?></p> 
            <?php endif; //if ($fb_uid == $user && $user) ?>              
            </div>
        </div> <!--end #about_user-->
        <?php
			if ($user)
			{
				// 1 -> activities, 2 - > interests, 3 -> music, 4 -> tv, 5 -> movies, 6 -> books
				$common_activities_list = common_check($fb_uid, $user, 1);
				$common_interests_list = common_check($fb_uid, $user, 2);
				$common_music_list = common_check($fb_uid, $user, 3);
				$common_tv_list = common_check($fb_uid, $user, 4);
				$common_movies_list = common_check($fb_uid, $user, 5);
				$common_books_list = common_check($fb_uid, $user, 6);
			}
		?>
		<?php if ($fb_uid != $user && $user && (count($common_activities_list) > 0 || count($common_interests_list) > 0 || count($common_music_list) > 0 || count($common_tv_list) > 0 || count($common_movies_list) > 0 || count($common_books_list) > 0)): // Common Interests ?>
        <div id='common_interests_sect' class='grey_box'>
            <div class='grey_title'>Common Interests</div> 
            <div class='sect_content2'>  
              <ul id='common_interests'>
		<?php if (count($common_activities_list) > 0): //common activities ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_hand.png' />
							<p class='interest_content'><?php echo implode(", ", $common_activities_list); ?></p>
						</li>
		<?php endif; ?>
		<?php if (count($common_interests_list) > 0): //common interests ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_hand.png' />
							<p class='interest_content'><?php echo implode(", ", $common_interests_list); ?></p>
						</li>
		<?php endif; ?>
		<?php if (count($common_music_list) > 0): //common music ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_music.png' />
							<p class='interest_content'><?php echo implode(", ", $common_music_list); ?></p>
						</li>
		<?php endif; ?>
		<?php if (count($common_tv_list) > 0): //common tv ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_tv.png' />
							<p class='interest_content'><?php echo implode(", ", $common_tv_list); ?></p>
						</li>
		<?php endif; ?>
		<?php if (count($common_movies_list) > 0): //common movies ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_movie.png' />
							<p class='interest_content'><?php echo implode(", ", $common_movies_list); ?></p>
						</li>
		<?php endif; ?>
		<?php if (count($common_books_list) > 0): //common books ?>
						<li class='common_interest'>
							<img class='interest_icon' src='<?php echo $domain_secondary;?>attachements/fb_book.png' />
							<p class='interest_content'><?php echo implode(", ", $common_books_list); ?></p>
						</li>
		<?php endif; ?>
					</ul>
			</div> <!--end .sect_content2-->
        </div> <!--end #common_interests-->
		<?php endif; //if ($fb_uid != $user && $user) ?>	
        <?php 
				if ($user)
					$common_friend_list = common_check($fb_uid, $user, 9); 
		?>
		<?php if ($fb_uid != $user && $user && count($common_friend_list) > 0): // Common Friends ?>
		<?php
			if(count($common_friend_list) > 0)		
			{
				// code for updating user's friendslist into the database
				$fql = "SELECT name,uid,pic_square from user where ";
				$i=0;
				foreach ($common_friend_list as $key)
				{		
					if ($i == 0)
						$fql .= "uid=".$key;
					else
						$fql .= " OR uid=".$key;
					$i++;
				}
								
				$param  =   array(
						'method'    => 'fql.query',
						'query'     => $fql,
						'callback'  => ''
						);
				$fqlResult1 = $facebook->api($param);
			}
		?>
			<div id='user_friends' class='grey_box'>
    
            	<div class='grey_title'><?php echo count($common_friend_list); ?> Friends in Common 
                </div>
            	 
                <div class='sect_content2'> 
					
					  <ul id='common_friends'> 				
		<?php if(count($common_friend_list) > 0): ?>
		<?php for($i=0; $i<15 && $i<count($common_friend_list); $i++): ?>
        				<li class='common_friend_pic'>
							<a title="<?php echo $fqlResult1[$i][name]; ?>" id='tab3' class='tabs'>
							<img class='common_friend_icon' alt'<?php echo $fqlResult1[$i][name]; ?>' src='<?php echo $fqlResult1[$i][pic_square]; ?>' height="50" width="50" /></a>
						</li>        
        <?php endfor; ?>
		<?php endif; //if(count($common_friend_list) > 0) ?>
					  </ul>
					  <div class='see_more'><a class='see_more_link' href='http://www.facebook.com/profile.php?id=<?php echo $seller_uid; ?>'>See more</a></div>
				  </div>
        </div> <!--end #user_friends-->
		<?php endif; //if ($fb_uid != $user && $user) ?>   
    </div> <!--end #profile_section-->
    <div id='service_section'>
    	<?php
			// Get data for seller's other listings
			$query = "SELECT listing_id,title,price,pricing_model FROM Listing_Overview WHERE fb_uid='$fb_uid'&&status=1 ORDER BY listing_id";
			$other_listing_result = mysql_query($query) or die (minor_error(102, $user, $user, $today, $query, mysql_error()));
			$other_listing_num = mysql_num_rows($other_listing_result);
			
			$URL = "";
		?>
		<?php if ($other_listing_num > 0): ?>
				 <div id='user_services' >	
						<div class='sect_title'><?php echo $user_first_name; ?>'s Services </div>
		<?php while($other_listing_row = mysql_fetch_array($other_listing_result)): ?>
		<?php
			extract($other_listing_row);
			
			// Add a thumbnail picture for each service
			// Extract pictures for the listing
			$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
			$picture_result = mysql_query($picture_sql) or die (minor_error(3, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			extract($picture_row);
			$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
			$url_result = mysql_query($url_sql) or die (minor_error(4, $user, $user, $today, $url_sql, mysql_error()));
			$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
			extract($url_row);
		?>    
						<div class='indv_service'>
							<a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>'>
									
                            	<?php if($URL == "hoodylogo.jpg"): ?>
                                    <img class='service_pic' src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/hoodylogo2.jpg&h=80&w=80&zc=1'  alt='' />
                        		<?php else: ?>
                            		<img class='service_pic' src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/<?php echo $URL; ?>&h=80&w=80&zc=1'  alt='' />
                        		<?php endif; ?>
                            </a>
							<div class = 'service_title'><a href='<?php echo $working_directory . "service/" . $listing_id . "/"; ?>' class = 'service_title_link'><?php echo $title; ?></a></div>
							<div class='service_price'><?php  
																if ($price==0)
																	echo "Free"; 
																else if (!$pricing_model)
																	echo "$".$price." Per Job";
																else if ($pricing_model)
																	echo "$".$price." Per Hour";
														?></div>
						</div>
		<?php endwhile; //while($other_listing_row = mysql_fetch_array($other_listing_result)) ?>
					  
				  </div> <!--end #user_service-->
		<?php else: //if ($other_listing_num > 0) ?>
        		  <div id='user_services' >	
						<div class='sect_title'><?php echo $user_first_name; ?>'s Services </div>	
        			    <div class='indv_service' id='no_service'>
                        	<div class = 'service_title'><?php echo $user_first_name; ?> has not posted a service yet.</div>
                        </div>
                        
                  </div> <!--end #user_service-->
        
        <?php endif; // if ($other_listing_num > 0)?>
    	
    	
    </div> <!--end #service_section-->
  

   </div><!--end #content-->
   
<?php	include "html/footer.inc"; ?> 

<!-- Google +1 button - Google specified that this code need to implemented after the +1 button -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();  
    $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('a[title]').qtip({
			   style: {classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'top left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {x: 10,  y: 10}},
			  hide: {// Helps to prevent the tooltip from hiding ocassionally when tracking!
				  fixed: true},})
		  $('img[title]').qtip({
			   style: {classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'top left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {x: 10,  y: 10}},
			  hide: {// Helps to prevent the tooltip from hiding ocassionally when tracking!
				  fixed: true },})});
</script>
</body>
</html>