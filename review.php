<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";			
	
	// check for a listing_id in the URL:
	$page_title = "Service Review";
	if ($user && isset($_GET['rid'])) 
	{
		//Typecast it to an integer:
		$rid = (int) $_GET['rid'];
		//An invalid $_GET['rid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($rid > 0) 
		{			
			//Get the information from the database for this review:
			$query = "SELECT * FROM Confirmed_Transactions WHERE transaction_id='$rid'&&fb_uid='$user'";
			$result = mysql_query($query) or die (fatal_error(133, $user, $user, $today, $query, mysql_error()));
			$num = mysql_num_rows($result);
			
			//service listing name not found
			if ($num == 0) 
				header("Location: " . $working_directory . "dashboard/");

			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			$database_rating = $rating;
			$database_review = $review;
			
			$query = "SELECT title,fb_uid,listing_description,price,pricing_model FROM Listing_Overview WHERE listing_id='$listing_id'";
			$result = mysql_query($query) or die (fatal_error(134, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			
			$query = "SELECT name,pic_big FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
			$result = mysql_query($query) or die (fatal_error(135, $user, $user, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			
			
			// Extract pictures for the listing
			$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
			$picture_result = mysql_query($picture_sql) or die (fatal_error(257, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
					
			$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id=" . $picture_row['picture_id_1'];
			$url_result = mysql_query($url_sql) or die (fatal_error(258, $user, $user, $today, $url_sql, mysql_error()));
			$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
			$picture_url = $url_row['URL'];
			
			
			
			if ($pricing_model == 0)
				$pricing_model_text = " per job";
			else if ($pricing_model == 1)
				$pricing_model_text = " per hour";
				
			if ($price == 0)
				$price_text = "Free";
			else 
				$price_text = "$ " . $price . $pricing_model_text;
							
							
							
						
			// Update title page
			$page_title = "Review - $title";

			// Update review
			// Updated 4/11
			// mysql_real_escape_string implementation to prevent SQL injection attack
			if ($_POST['rating'] != NULL)
			{
				$rating = $_POST['rating'];
				$review = $_POST['review'];
				$rating = mysql_real_escape_string($rating);
				$review = mysql_real_escape_string($review);
				$database_rating = (int)$rating;
				
				//if the transaction hasn't been confirmed
				if ($transaction_date == NULL)
				{			
					$query = "UPDATE Confirmed_Transactions SET review_status=1,review='$review',rating='$database_rating',transaction_date='$today' WHERE transaction_id='$rid'";
					$result = mysql_query($query) or die (fatal_error(136, $user, $user, $today, $address_sql, mysql_error()));
					$query = "UPDATE Contact_History SET transaction_status=1 WHERE contact_id='$rid'";
					$result_update = mysql_query($query) or die (fatal_error(137, $user, $user, $today, $address_sql, mysql_error()));
					
					// Updated 4/12
					// Incorperating popularity index
					$query = "UPDATE Listing_Overview SET popularity = (15 + popularity) WHERE listing_id = '$listing_id'";
					$result = mysql_query($query) or die (fatal_error(138, $user, $user, $today, $address_sql, mysql_error()));
					
					$review_status = 2;
					
					if($_POST['facebook'])
					{
						try 
						{						
							$wallpostpage = $facebook->api('/me/feed', 'post',
											array(
											  'message' 	=> 'My review of the ' . $title . ' service on Hoody' ,
											  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url,
											  'link'    	=> 'http://gohoody.com/service/' . $listing_id . '/',
											  'name'    	=> $title,
											  'caption' 	=> $price_text,
											  'description' => nl2br($review),
											  'source' 		=> '',
											  'cb'      	=> ''
											  )
							);
						} 
						catch (FacebookApiException $e) 
						{
							 print_r($o);
						}
					}					
				}
				else
				{
					
					if ($review_status == 0)
					{
						// Updated 4/12
						// Incorperating popularity index
						$query = "UPDATE Listing_Overview SET popularity = (5 + popularity) WHERE listing_id = '$listing_id'";
						$result = mysql_query($query) or die (fatal_error(139, $user, $user, $today, $query, mysql_error()));
					}
					
					$query = "UPDATE Confirmed_Transactions SET review_status=1,review='$review',rating='$database_rating' WHERE transaction_id='$rid'";
					$result = mysql_query($query) or die (fatal_error(140, $user, $user, $today, $query, mysql_error()));
					
					$review_status = 2;
					
					if($_POST['facebook'])
					{
						try 
						{					
							$wallpostpage = $facebook->api('/me/feed', 'post',
											array(
											  'message' 	=> 'My review of the ' . $title . ' service on Hoody' ,
											  'picture' 	=> 'http://img.gohoody.com/service_pictures/' . $picture_url,
											  'link'    	=> 'http://gohoody.com/service/' . $listing_id . '/',
											  'name'    	=> $title,
											  'caption' 	=> $price_text,
											  'description' => nl2br($review),
											  'source' 		=> '',
											  'cb'      	=> ''
											  )
							);
						} 
						catch (FacebookApiException $e) 
						{
							 print_r($o);
						}
					}
				}
			//	header("Location: user_dashboard.php");
			}
		} // End of if ($lid > 0)
	} // End of if ($user && isset($_GET['rid']))
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/logo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description" content="Hoody"/>
          
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png">
<title><?php print($page_title) ?></title>

<!--CSS Begins-->

<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/review.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/title_bar_new.css"  media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery-ui-1.8.16.custom.css" />

<!--CSS Ends-->

<!--Javascript Begins-->
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!--Warning msg script-->
<script type="text/javascript" charset="utf-8">
  function warning_fadein() {$("#warn_address").fadeTo(700, .75);}
  function warning_fadeout() {$("#warn_address").fadeOut(700);}
  
  $(function() {
	  $( "#radio_ui" ).buttonset();
	  
	  if ($("#radio1:checked").val() == '1') {
		  $(".box_unchecked_yes").hide();
		  $(".box_checked_yes").show();
		  
		  $(".box_checked_no").hide();
		  $(".box_unchecked_no").show();
			  
	  }
	  if ($("#radio2:checked").val() == '0') {
		  $(".box_checked_yes").hide();
		  $(".box_unchecked_yes").show();
		  
		  $(".box_unchecked_no").hide();
		  $(".box_checked_no").show();
	  };
	  
	  $("input.use_again").click(function(){
		  
		  
		  
	
		  if ($("#radio1:checked").val() == '1') {
			  $(".box_unchecked_yes").hide();
			  $(".box_checked_yes").show();
			  
			  $(".box_checked_no").hide();
			  $(".box_unchecked_no").show();
				  
		  }
		  else{
			  $(".box_checked_yes").hide();
			  $(".box_unchecked_yes").show();
			  
			  $(".box_unchecked_no").hide();
			  $(".box_checked_no").show();
		  };
	  
	  });
	  
  });
  
  
  
</script>



<!--Javascript Ends-->
</head>
<body>

	
	
	<?php 
        flush();
        include "html/title_bar_new2.inc";
    ?>
    <div id="content">	
        
        <div id = "service_block">
            
            <?php if($user): ?>
					
                  
                      
                <div id='page_title'>
                
                	<?php if ($review_status == 1): ?>
                   		<div class='title_1'>Update your review </div>
                    <?php elseif ($review_status == 2): ?>
                   		<div class='title_1'>Your review has been submitted </div>
                    <?php else: ?>
                		<div class='title_1'>Submit a review </div>
                    <?php endif; //if ($review_status == 1) ?>
                	
                    <div class='title_2'><span class='for_text'>For </span><a class='profile_link' href="<?php echo $working_directory . "service/" . $listing_id . "/"; ?>"><?php echo $title ?></a></div>
                    
                </div> <!--end #page_title-->  	
                    
                    
                
                
                <form method='POST' action="<?php echo $working_directory . "review/" . $rid . "/"; ?>">
                    <div class='title2'>Would you use this service again?</div>
                    
                    <div id="radio_ui">
                      <input type="radio" name="rating" value="1" class='use_again' id='radio1' <?php if($database_rating == 1) { echo "checked"; } ?> /> 
                      	<label for="radio1">&nbsp;&nbsp;&nbsp;&nbsp;Yes I would </label> 
                        <div id='checkbox_yes'>
                          <img class='box_checked_yes' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                          <img class='box_unchecked_yes' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
                        </div>
                      <input type="radio" name="rating" value="0" class='use_again' id='radio2' <?php if($database_rating == 0 && $review_status == 1) { echo "checked"; } ?>/> 					  	<label for="radio2">&nbsp;&nbsp;&nbsp;&nbsp;No I would not  </label>	
                      	<div id='checkbox_no'>
                        	<img class='box_checked_no' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                    		<img class='box_unchecked_no' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
                        </div>
                    </div>  	
                    
                    <div class='title2'>Additional Comments</div>
                    <div><textarea id='comments' name="review"><?php if($_POST['review'] == "") { echo "$database_review"; } ?><?php echo $_POST['review']; ?></textarea></div>
					<input type="checkbox" name="facebook" value="1" checked /> Post to my Facebook wall 
					<?php if ($review_status == 1): ?>
                   		<div id='input_sect'><input id='submit_button' type="submit" value="Modify"></div>
                    <?php else: ?>
                		<div id='input_sect'><input id='submit_button' type="submit" value="Submit"></div>
                    <?php endif; //if ($review_status == 1) ?>
                    
                    
                </form>
			<?php else: ?>
           		please log in first before continue
            <?php endif; //if($user) ?>
            
        </div><!--end #service_block-->
         
        <div id = "seller_block">
        	
            
            <div class='grey_box'>
              <div id="seller_name" class='grey_title'>		  	
                <div id='seller_name_text'> Service Seller </div>
              </div>
              <div id="seller_pic_sect">
                <div id="seller_pic">
                    <a href="http://www.facebook.com/profile.php?id=<?php echo $fb_uid; ?>"><img src="<?php echo $pic_big; ?>" width='100px' alt="" /></a>
                </div>
              </div>
              <div id='seller_right_sect'>  
                <p class='interest_content'><a class='profile_link' href="http://www.facebook.com/profile.php?id=<?php echo $fb_uid; ?>"><?php echo $name; ?></a></p>
               
            </div><!--end #seller_right_sect-->
          </div> <!--end .grey_box-->
            
        </div> <!--end #seller_block--> 
	
    </div> <!--end #content-->
    
<?php include "html/footer.inc"; ?>

</body>
</html>