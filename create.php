<?php
	$page_title = NULL;
	
	// set a max file size for the html upload form
	$max_file_size = 5000000; // size in bytes
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
	include "php/fbmain.php";
    $config['baseurl'] = $working_directory. "index.php";
		
	//variable for indicating if the lid is a valid listing that is already in the database. created to pass lid information to upload_process.php
	$listing_exist = 0;
	
	//only user logged into Facebook has access to create listing
//	if (!$user)
//		header("Location: " . $working_directory);


	if (!$user)
	{
		$basic_query = "SELECT fb_uid FROM Basic_User_Information WHERE fb_uid = (SELECT MIN(fb_uid) FROM Basic_User_Information)";
		$result = mysql_query($basic_query) or die (fatal_error(275, $user, $user, $today, $basic_query, mysql_error()));
		$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
		if ($row3['fb_uid'] > 0)
			$user_id = -1;
		else
			$user_id = $row3['fb_uid'] - 1;
	}
	
	else
	{
		$user_id = $user;
		$address_sql = "SELECT area_code,street,lng,lat FROM User_Address WHERE fb_uid='$user'";
		$result = mysql_query($address_sql) or die (fatal_error(90, $user, $user, $today, $address_sql, mysql_error()));
		$lnglat_row = mysql_fetch_array($result,MYSQL_ASSOC);
		$user_area_code = $lnglat_row['area_code'];
		$user_street = $lnglat_row['street'];
		if ($user_area_code == NULL && $user_street == NULL)
			$address_insufficient = "<div id='add_warn'>*Please complete your address info in the <a class='link' href='" 
									. $working_directory . "dashboard.php'> Dashboard</a> before choosing this option</div>";
	}
	
	//if the create listing page is access with lid variable attached to its URL
	if (isset($_GET['lid']) && $user) 
	{
		//Typecast it to an integer:
		$lid = (int) $_GET['lid'];
		//An invalid $_GET['lid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($lid > 0) 
		{	
			//Get the information from the database for this service:
			$query = "SELECT * FROM Listing_Overview WHERE listing_id=$lid&&fb_uid=$user";
			$result = mysql_query($query) or die (fatal_error(103, $user, $user, $today, $query, mysql_error()));
			$num = mysql_num_rows($result);
			
			//service listing name not found
			if ($num == 0) 
				header("Location: " . $working_directory . "lost/");
			
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			
			//verified listing exists in the database
			$listing_exist = 1;
			
			//Get the information from database for this service
			$query = "SELECT * FROM Listing_Location WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(91, $user, $user, $today, $query, mysql_error()));
			$row2 = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row2);
			
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT name,pic_big,email FROM Basic_User_Information WHERE fb_uid='$user'";
			$result = mysql_query($service_sql) or die (fatal_error(92, $user, $user, $today, $service_sql, mysql_error()));
			$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row3);
			
			// Extract pictures for the listing
			$picture_sql = "SELECT * FROM Listing_Pictures WHERE listing_id='$lid'";
			$picture_result = mysql_query($picture_sql) or die (fatal_error(93, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			extract($picture_row);
			$picture_id = array($picture_id_1, $picture_id_2, $picture_id_3, $picture_id_4, $picture_id_5);
			$picture_url = array();
			
			for ($counter=0 ; $counter < $picture_count ; $counter++)
			{
				$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id[$counter]'";
				$url_result = mysql_query($url_sql) or die (fatal_error(94, $user, $user, $today, $url_sql, mysql_error()));
				$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
				extract($url_row);
				$picture_url[$counter] = $URL;
			}	
		} // End of if ($lid > 0)
	} // End of if (isset($_GET['lid']))
		
	if($title)
		$page_title = "Modify Lisiting - ". $title;
	else
		$page_title = "Create Listing";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="Hoody: Create Listing"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/create/"/>
    <meta property="og:image" content="<?php echo $domain_secondary; ?>attachements/logo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="Hoody: Create Listing"/>
  <link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary; ?>attachements/favicon.png" />
  <title><?php echo $page_title; ?></title>

<!--CSS-->
<link href="<?php echo $working_directory; ?>css/uploadify.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/create_listing.css" media="screen" />
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.lightbox.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.lightbox.ie6.css" /><![endif]-->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/qtip2.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery.gritter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery-ui-1.8.16.custom.css" />	

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<script src="<?php echo $working_directory; ?>javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo $working_directory; ?>javascript/jquery.alphanumeric.js" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo $working_directory; ?>javascript/jquery.easing.1.3.js" type="text/javascript" charset="utf-8"></script>  
  
<!-- qTip -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-qtip.js"></script>
  
<!--Javascript for popupbox-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.lightbox.js"></script>
  
<!--Notifications-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.gritter.js"></script>
<script type="text/javascript">
	function loading() {
	  $('#continue_text').fadeOut(100);
	  $('#continue_text2').delay(150).fadeIn();}
	
	//For Radio buttons
	$(function() {
		$( "#radio_ui" ).buttonset();
		$("#radio_address").buttonset();
		$("#input_display_address").buttonset();
		$("#away_from_home_options").buttonset();}); 
	
	//For showing basic uploader
	function show_basic_upload(){
		$("#upload_section").slideUp();
		$("#basic_uploader").slideDown();
		$("#trouble_msg").hide();
		$("#show_fancy").show();};
	
	function show_fancy_upload(){
		$("#upload_section").slideDown();
		$("#basic_uploader").slideUp();
		$("#show_fancy").hide();
		$("#trouble_msg").show();};
</script>
<script src="<?php echo $working_directory; ?>javascript/form-fun.jquery.js" type="text/javascript" charset="utf-8"></script>
  
<!-- Uploadify -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/swfobject.js"></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		
		$.numOfPics = 0;
		
		$('#file_upload').uploadify({
			'uploader'  : '<?php echo $working_directory; ?>javascript/uploadify.swf',
			'script'    : '<?php echo $working_directory; ?>php/uploadify.php',
			'cancelImg' : '<?php echo $working_directory;?>attachements/cancel.png',
			'folder'    : '/service_pictures',
			'auto'      : true,
			'multi'		: true,
			'fileExt'     : '*.jpg;*.jpeg;*.gif;*.png;*.JPG;*.JPEG;*.GIF;*.PNG;',
			'fileDesc'    : 'Image Files',
			'sizeLimit'   : 2100000,
			'removeCompleted': true,
			'queueSizeLimit' : 5,		
			'onAllComplete'  : function(event, queueID, fileObj, response, data) 
			{
				$('#next3').show();
				$.isPictureComplete = 1;
				$('#progress').animate({
				  width: '175px'
				  }, {
					duration: 1000,
					specialEasing: {
					 width: 'easeOutBounce'
					},
				});
				
				$('#percentage').html(100);
			  
				$('#status-message').text(data.filesUploaded + ' files uploaded, ' + data.errors + ' errors.');
			},
			'onComplete': function(event, queueID, fileObj, response, data) 
			{
					$.numOfPics++;
					var json = eval('(' + response + ')');

					$('#uploaded_files').val($('#uploaded_files').val()+'<br/>'+json.picture_id);
					
					$('#preview_title').fadeIn();
					
					if ($.numOfPics > 5){
						$(".preview_pic:first-child").remove();
						$("#uploaded_preview").append("<li class='preview_pic' id='" +json.picture_id + "'><img src=\'<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/" +json.url + "&w=100&h=100&zc=1\' width=\'100px\' height=\'100px\' /></li>");
						$(".preview_pic:last-child").hide().fadeIn(1000);
					} else {
						$("#uploaded_preview").append("<li class='preview_pic' id='" +json.picture_id + "'><img src=\'<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/" +json.url + "&w=100&h=100&zc=1\' width=\'100px\' height=\'100px\' /></li>");
						$(".preview_pic:last-child").hide().fadeIn(1000);
					};
	
			}	
	
		});
	});
	
	
	
	
	
	
	
	function clear_uploaded(){
		$(".preview_pic").fadeOut();
		$("#preview_title").delay(800).fadeOut();
		$('#uploaded_files').val('');
		$('#progress').animate({
		  width: '140px'
		  }, {
			duration: 1000,
			specialEasing: {
			 width: 'easeOutBounce'
			},
		});
		
		$('#percentage').html(80);
	};
	
</script>
<!-- End of Uploadify -->

<!-- Sortable -->
<script>
var imgOrder = '';
$(function() {
  $("#uploaded_preview").sortable({
    update: function(event, ui) {
      imgOrder = $("#uploaded_preview").sortable('toArray').toString();
	  $("input#picture_orders").val(imgOrder);}});
  $("#uploaded_preview").disableSelection();});

</script>

</head>
<body <?php if ($development_status) echo 'onLoad="javascript:pageTracker._setVar(\'hoody-notrack\')"'; ?>>
	
<?php 
	flush();
	include "html/title_bar_new2.inc";
?>

      

  <div id="content">
    <div id = "isFilledWrap">
      <input name = "isFilled" id="isFilled" value="<?php echo "$listing_exist"; ?>" />
      <div id="testfield"> </div>
    </div>
    <div id='page_title'>Create a Service</div>
   <div id = "tabs_block">
    <ul id='tabs_nav'>
      <li> <a id='tab1' class='tabs'> 1. Description </a> </li>
      <li> <a id='tab2' class='tabs' > 2. Location </a> </li>
      <li> <a id='tab3' class='tabs'> 3. Pictures  </a> </li>
    </ul> 
    <div id='tab_container'>
      <form name="create_form" id="Upload" action="<?php if($listing_exist) {echo($working_directory.'upload_process.php?lid='.$lid);} else{echo($working_directory.'upload_process.php');} ?>" 
      enctype="multipart/form-data" method="post" onsubmit="beginUpload();">
      <div id='tab1_content' class='step1_info'>
      	<fieldset class="listing_form" id="step_1">
    	<input type=hidden name="uid" value="<?php echo $user_id; ?>">
        <label class="step1_label" for="title">Service Title</label>        
        <input name="title" type="text" id="title" class="desc_input" value="<?php 
																					if ($_POST['service_title'] != "")
																						echo $_POST['service_title'];
																					else if($_POST['title'] == "") 
																						echo $title;
																					echo $_POST['title']; 
																					?>" input="input" />
        <label class="step1_label" for="listing_description">Service Description</label>
        <textarea name="listing_description" id="textfield" class="desc_input"><?php if($_POST['listing_description'] == ""){print($listing_description);} echo $_POST['listing_description'] ?></textarea>
        <label class="step1_label" for="asking_price">Price </label>
        <div id="asking_price">
           <p id="dollar_sign">$</p>  
            <input type="text" id="price" name="price" class="desc_input" value="<?php if($_POST['price'] == "") {echo($price);} echo $_POST['price'] ?>"  maxlength="8" />  
         	<div id="radio_ui" class='price_option'>    
            	<input type="radio" class = "pricing_model" id="radio1" name="pricing_model"  value="0" 
				<?php if(!$pricing_model && $price!=0) echo "checked"; if($pricing_model) {echo "";} else {echo"checked";} ?>> 
                <label for="radio1"> &nbsp;&nbsp;&nbsp;&nbsp;Per Job</label> 
                <div id='checkbox_job'>
                	<img class='box_checked_job' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                    <img class='box_unchecked_job' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
                </div>
            	<input type="radio" class = "pricing_model" id="radio2" name="pricing_model" value="1"
                <?php if($pricing_model) echo "checked"; ?>> <label for="radio2">&nbsp;&nbsp;&nbsp;&nbsp;Per Hour</label>
                <div id='checkbox_hour'>
                	<img class='box_checked_hour' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                    <img class='box_unchecked_hour' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
                </div>
            </div> 
        </div>
    	</fieldset>  
        <div class='tab_right_side'>
        	<div class='side_box' id='tip_title'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>
                	Don't try to do everything in one service. Split into multiple services if needed.
                </div>
            </div>
            
            <div class='side_box' id='tip_description'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>
                	Describe your service in detail so others will know what you're offering.
                </div>
                
            </div>
            
            <div class='side_box' id='tip_price'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>
                	If price varies based on the job, put an amount that best represents the price range.
                </div>
            </div>
        </div> <!--end .tab_right_side-->
      
      	<div class='control_sect'>
        	<a class='control_button' id='back1'> Back </a>
            <div class='control_page'> <div class='page_num'>1/3</div> </div>
            <a class='control_button' id='next1'> Next </a>
        </div> <!--end .control_sect-->
      </div>
      
      <div id='tab2_content' class='Step_2_location'>
		<fieldset class="listing_form" id="step_2">
        <p class="step2_label">Where does this service take place? </p>
        <div id='radio_address'>
          <input type="radio" class='location_options' id = "location_home" name="location1" value="at_home" <?php if($listing_exist == 1 && $listing_location == 0) {echo "checked";} ?>>
          <label for="location_home">At my home</label>
          
          
          <input type="radio" class='location_options' id = "location_away" name="location1" value="away_home" <?php if($listing_location == 2) {echo "checked";} ?>>
          <label for="location_away">Away from my home</label>
         
          
          <input type="radio" class='location_options' id = "location_virtual" name="location1" value="virtual" <?php if($listing_location == 3) {echo "checked";} ?>>
          <label for="location_virtual">Virtually </label>
          
          
        </div>
        
        <div id="display_address_wrap">
           <img id='arrow_up1' src="<?php echo $domain_secondary;?>attachements/arrow_up_create.png"  />
           <div id="input_display_address">
           	<input type="checkbox" id="display_address" name="show" value="1" checked="checked" >
            <label for="display_address"> &nbsp;&nbsp;&nbsp;&nbsp;Display address in the listing </label>
            <div id='checkbox_home'>
                <img class='box_checked_addy' src="<?php echo $domain_secondary;?>attachements/box_check.png" />
                <img class='box_unchecked_addy' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
            </div>
           </div>
        </div>
        
        <div id="display_address_wrap2">
          <img id='arrow_up2' src="<?php echo $domain_secondary;?>attachements/arrow_up_create.png"  /> 
          <div id="away_from_home_options">
          	<input type="radio" class='away_options' id = "buyer_home" name="location2" value="buyer_home" <?php if($listing_location == 1) {echo "checked";} ?>>
            <label for="buyer_home"> &nbsp;&nbsp;&nbsp;&nbsp; Customer's home </label>
            <div id='checkbox_home'>
                <img class='radio_checked_buyer' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                <img class='radio_unchecked_buyer' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
            </div>
          
          	<input type="radio" class='away_options' id = "other" name="location2" value="other" <?php if($listing_location == 2) {echo "checked";} ?>>
            <label for="other"> &nbsp;&nbsp;&nbsp;&nbsp; Another location </label>  
            <div id='checkbox_another'>
                <img class='radio_checked_another' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                <img class='radio_unchecked_another' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
            </div>
          </div>    
                   
          <div id="display_address_wrap3">     
              <div id="radius_caption"> How far are you willing to go? </div>
              <div id="input_radius"> <input type="text" name="range" id='range' class="desc_input2" 
              value="<?php if($_POST['range'] == "" && $listing_location == 1) {echo($listing_range);} echo $_POST['range'] ?>" size="2" maxlength="4"> km 
              <img title='Anyone living outside of this range will not see your service.'  class='qmark' src="<?php echo $working_directory; ?>attachements/question.png"  width='15px'/></div>
          </div>
              
          <div id="display_address_wrap4">
              <div id="street_caption">Street:</div>
              <div id="input_street"> <input type="text" name="street" id='street' class="desc_input3"  
              value="<?php if($_POST['street'] == "" && $listing_location == 2) {echo($street);} echo $_POST['street'] ?>" size="20" maxlength="255" /></div>
              <div id="city_caption">City:</div>
              <div id="input_city"> <input type="text" name="city" id='city' class="desc_input3" value="<?php if($_POST['city'] == "" && $listing_location == 2) {echo($city);} 
              echo $_POST['city'] ?>" size="20" maxlength="255" /></div>
          </div>          
        </div>	
        </fieldset>
		
        <div class='tab_right_side'>
        
        	<div class='side_box' id='tip_location'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Note </div>
                <div class='side_box_content'><br />
                	<span class='strong'>At my home </span> <br/>
                    The customer will come to you <br /><br />
                    <span class='strong'>Away from my home </span> <br/>
                    You'll go meet with the customer <br /><br />
                    <span class='strong'>Virtually </span> <br/>
                    Service is done remotely, no need to meet with the customer<br /> <span class='weak'>(i.e. web development)</span> <br /><br /> 
                </div>
            </div>
        
        	<div class='side_box' id='tip_home'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Note </div>
                <div class='side_box_content'>
                	Display the address as it appears in your dashboard.
                </div>
            </div>
            
            <div class='side_box' id='tip_range'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Note </div>
                <div class='side_box_content'>
                	This defines the visibility range for this service.
                </div>
            </div>
        </div> <!--end .tab_right_side-->
        
        <div class='control_sect'>
        	<a class='control_button' id='back2'> Back </a>
            <div class='control_page'> <div class='page_num'>2/3</div> </div>
            <a class='control_button' id='next2' > Next </a>
        </div> <!--end .control_sect-->
      </div> <!--end #tab2_content-->
        
      <div id='tab3_content' class='step3_pictures'>
		<fieldset class="listing_form" id="step_3">
        <div class="step3_label">Upload pictures (up to 5)</div>
       
<?php if ($listing_exist != 0): ?>        
        <p><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size ?>"></p>
<?php
      // Add picture attachment controls for modify listing
      // Select all active listings from Listing_Overview table
     
	 if($counter != 0)
	 {
		echo "
			<div id='step3_left2'>
			<div id='pictures'>";
		for($counter = 0 ; $counter < $picture_count ; $counter++)
		{
			echo "<div id='img_container$counter'><a href='http://img.gohoody.com/service_pictures/$picture_url[$counter]' class='lightbox' rel='group1'>
			<img id='img$counter' src='http://img.gohoody.com/resizer.php?src=http://img.gohoody.com/service_pictures/$picture_url[$counter]&w=220&zc=1'/></a></div>";
		}
		echo "</div></div>";
	 }
      // additional picture attachement control
      // count how many additional pictures the user is allow to attach out of their quota of 5 max
      $left_to_upload = 5 - $picture_count;
      
      echo "<div id='upload'>";
      if ($picture_count != 0)
      {
          echo 	"<p><input type='radio' name='keep_pictures' value='". $picture_count ."' checked/> Keep previous pictures and add " . $left_to_upload . " more:</p><br />
                  ";
  
          for ($counter2 = 1, $file_name = "" ; $counter2 <= $left_to_upload ; $counter2++)
          {
              $file_name = "file" . $counter2;
              echo " <input class='update_input' id='$file_name' type='file' name='file[]'></p>";
          }
          echo "<br /><p><input type='radio' name='keep_pictures' value='' /> Remove previous pictures and add new ones:</p><br />";
          for ($counter2 = 1 ; $counter2 <= 5 ; $counter2++)
          {
              $file_name = "file" . $counter2;
              echo "<input id='$file_name' class='input_picture' type='file' name='file[]'></p>";
          }
      }
      else
      {
          for ($counter2 = 1 ; $counter2 <= 5 ; $counter2++)
          {
              $file_name = "file" . $counter2;
              echo "<label for='$file_name' class='picture_number'>Picture $counter2:</label> <br /><input id='$file_name' class='input_picture' type='file' name='file[]'></p>";
          }
      }
      echo "</div>";
?>
       </fieldset>
       <div class='control_sect'>
        	<a class='control_button' id='back3'> Back </a>
            <div class='control_page'> <div class='page_num'>3/3</div> </div>
            <button type='submit' class='control_button' id='next3' name="submit" onclick='loading()'>Update</button>
<?php else: ?>
		
        
       	<div id ='step3_left'>	
            <div id='upload_section'>
            <input name="uploaded_files" id="uploaded_files" class="inputbox" type="hidden" />
                <input type="file" id="file_upload" name="file_upload" />
            </div>    
         	
            <div id='trouble_msg'> 
            	Having trouble?
                <a id='show_basic' class='show_hide_uploader' onclick='show_basic_upload();'> Try the basic uploader</a>
            </div>
            <a id='show_fancy' class='show_hide_uploader' onclick='show_fancy_upload();'> Use fancy uploader</a>
            <div id='basic_uploader'>
<?php
				for ($counter2 = 1 ; $counter2 <= 5 ; $counter2++)
				{
					$file_name = "file" . $counter2;
					echo "<label for='$file_name' class='picture_number'>Picture $counter2:</label> <br /><input id='$file_name' class='input_picture' type='file' name='file[]'></p>";
				}
			
?>
            </div>	
            
            
           <!-- <a href='#' onclick="removeFirst();"> Remove First Image </a>-->
            
        </div> 
       </fieldset>
       
       <div class='tab_right_side step3_right'>
       		<div class='side_box' id='tip_pictures'>
            	<img class='tip_arrow' src="<?php echo $domain_secondary;?>attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>
                	A picture can make your service look more attractive.
                </div>
            </div>
            
            <div id='restrictions'>
                <div><b>File types supported:</b> .jpg .png .gif</div>
                <div><b>Max file size:</b> 1.5MB per picture</div>
                <div><b>Max image dimension:</b> 2500x2500 </div>
            </div>
            
       </div> <!--end .tab_right_side--> 
       
       
    
    
    
    
    
       
       
       
       
       <div id='preview_sect'>
       	 <div id='preview_title' class='step3_label'>Preview (<a id='clear_uploaded' class='show_hide_uploader' onclick='clear_uploaded();'> Clear all </a>)</div>
      	 <ul id="uploaded_preview"></ul>
         <input type=hidden id='picture_orders' name="picture_orders" value="">

         
       </div>	
       <div class='control_sect'>
        	<a class='control_button' id='back3'> Back </a>
            <div class='control_page'> <div class='page_num'>3/3</div> </div>
            <button type='submit' class='control_button' id='next3' name="submit" onclick='loading()'>Post Service</button>
<?php endif; // if ($listing_exist != 0): ?>
        </div> <!--end .control_sect--> 
      </div>  <!--end #tab3_content-->        
      </form> 	
    </div> <!--end #tab_container-->
   </div> <!--end of #tabs_block--> 
    
   <div id = 'progress_bar'>
   	  <div class='grey_box'>
         <div class='grey_title'>Your Progress</div>
		 <div id='progress_content'>
         	<div id='bar_wrapper'><div id ='progress'></div></div> <!--end #bar_wrapper-->
            
            <div id='percentage_complete'>
            	<div id='percentage'> 0&nbsp; </div>
                <div id='percentage_text'>% Complete</div> 
            </div> <!--end #percentage_complete-->
         </div> <!--#progress_content-->
      </div>  <!--end #grey_box-->
   </div> <!--end #progress_bar-->
    <div id="page-wrap"></div> <!--end #page-wrap-->
    <br /><br />
  </div> <!--end of #content-->
      
  <?php include "html/footer.inc"; ?>
  
  <script type="text/javascript">
    jQuery(document).ready(function($){
      $('.lightbox').lightbox();});
	$('#price').numeric();
	$('#range').numeric();
	$('#street').alphanumeric({allow:"-#():., "});
	$('#city').alphanumeric({allow:"-#():., "});
    $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
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