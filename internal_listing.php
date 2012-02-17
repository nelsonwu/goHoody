<?php
	// Program: create_listing.php
	//								
		
	$page_title = NULL;
	
	// set a max file size for the html upload form
	$max_file_size = 5000000; // size in bytes
	
	//Connect to @Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	include "php/admin.inc";

	//Link to Facebook PHP SDK
	include "php/fbmain.php";
    $config['baseurl'] = $working_directory. "index.php";
			
	//variable for indicating if the lid is a valid listing that is already in the database. created to pass lid information to upload_process.php
	$listing_exist = 0;
		
	//only user logged into Facebook has access to create listing
	if (!in_array($user, $admin))
		header("Location: index.php");
	
	//if the create listing page is access with lid variable attached to its URL
	if (isset($_GET['lid'])) 
	{
		//Typecast it to an integer:
		$lid = (int) $_GET['lid'];
		//An invalid $_GET['lid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($lid > 0) 
		{
			//Get the information from the database for this service:
			$query = "SELECT * FROM Listing_Overview WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(279, $user, $user, $today, $query, mysql_error()));
			$num = mysql_num_rows($result);
			
			//service listing name not found
			if ($num == 0) 
				echo "Listing not found";
			
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			$seller_uid = $fb_uid;
			
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT pic_square,name,first_name FROM Basic_User_Information WHERE fb_uid='$seller_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(280, $user, $user, $today, $service_sql, mysql_error()));
			$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
			$seller_pic3 = $row3['pic_square'];
			$seller_name = $row3['name'];
			$seller_first_name = $row3['first_name'];
			
			$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$seller_uid'";
			$result = mysql_query($user_lookup_sql) or die (fatal_error(281, $user, $user, $today, $user_lookup_sql, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			$user_profile_identifier = $row['profile_name'];
			
			//verified listing exists in the database
			$listing_exist = 1;
			
			//Get the information from database for this service
			$query = "SELECT * FROM Listing_Location WHERE listing_id=$lid";
			$result = mysql_query($query) or die (fatal_error(282, $user, $user, $today, $query, mysql_error()));
			$row2 = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row2);
			
			// Extract seller info from Basic_User_Information table
			$service_sql = "SELECT name,pic_big,email FROM Basic_User_Information WHERE fb_uid='$seller_uid'";
			$result = mysql_query($service_sql) or die (fatal_error(283, $user, $user, $today, $service_sql, mysql_error()));
			$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row3);
			
			// Extract pictures for the listing
			$picture_sql = "SELECT * FROM Listing_Pictures WHERE listing_id='$lid'";
			$picture_result = mysql_query($picture_sql) or die (fatal_error(284, $user, $user, $today, $picture_sql, mysql_error()));
			$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
			extract($picture_row);
			$picture_id = array($picture_id_1, $picture_id_2, $picture_id_3, $picture_id_4, $picture_id_5);
			$picture_url = array();
			
			for ($counter=0 ; $counter < $picture_count ; $counter++)
			{
				$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id[$counter]'";
				$url_result = mysql_query($url_sql) or die (fatal_error(285, $user, $user, $today, $url_sql, mysql_error()));
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
            
  <link rel="icon" 
      type="image/png" 
      href="http://img.gohoody.com/attachements/favicon.png" />
  <title><?php echo $page_title; ?></title>
  
<div id="fb-root"></div>
<script src="javascript/facebook_js.inc" type="text/javascript"></script>

<!--CSS-->
<link href="css/uploadify.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/create_listing.css" media="screen" />
<link rel="stylesheet" href="css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox.css" />
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/qtip2.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.gritter.css" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.16.custom.css" />	

<script src="http://www.gohoody.com/javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>

<script src="javascript/jquery.alphanumeric.js" type="text/javascript" charset="utf-8"></script>

<script src="javascript/jquery.easing.1.3.js" type="text/javascript" charset="utf-8"></script>  
  
<!-- qTip -->
<script type="text/javascript" src="javascript/jquery-qtip.js"></script>
  
<!--Javascript for popupbox-->
<script type="text/javascript" src="javascript/jquery.lightbox.js"></script>
  
<!--Notifications-->
<script type="text/javascript" src="javascript/jquery.gritter.js"></script>
<script type="text/javascript">
	function loading() {
	  $('#continue_text').fadeOut(100);
	  $('#continue_text2').delay(150).fadeIn();
	}
	
	//For Dropdown menu 
	function show_dropdown() {
		$("div#dropdown_menu").show();
		$("a#arrow_down").hide();
		$("a#arrow_up").show();
		
	};
	function hide_dropdown() {
		$("div#dropdown_menu").fadeOut(200);
		$("a#arrow_up").hide();
		$("a#arrow_down").show();
		
	};
	
	function show_header_popup(){
		$("#header_popup").fadeIn(100);
	};
	
	function hide_header_popup(){
		$("#header_popup").fadeOut(200);
	};
	
	function show_popup(){
		$("#popup_sect").fadeIn(100);
	};
	
	function hide_popup(){
		$("#popup_sect").fadeOut(200);
	};
	
	 $('html').click(function() {
		hide_dropdown();
		hide_header_popup();
		hide_popup();
	 });
	
	//For Radio buttons
	$(function() {
		$( "#radio_ui" ).buttonset();
		$("#radio_address").buttonset();
		$("#input_display_address").buttonset();
		$("#away_from_home_options").buttonset();
	}); 
	
	//For showing basic uploader
	function show_basic_upload(){
		$("#upload_section").slideUp();
		$("#basic_uploader").slideDown();
		$("#trouble_msg").hide();
		$("#show_fancy").show();
	};
	
	function show_fancy_upload(){
		$("#upload_section").slideDown();
		$("#basic_uploader").slideUp();
		$("#show_fancy").hide();
		$("#trouble_msg").show();
		
	};	   
</script>
<script src="javascript/form-fun.jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- Uploadify -->
<script type="text/javascript" src="javascript/swfobject.js"></script>
<script type="text/javascript" src="javascript/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#file_upload').uploadify({
			'uploader'  : 'javascript/uploadify.swf',
			'script'    : 'php/uploadify.php',
			'cancelImg' : 'http://gohoody.com/attachements/cancel.png',
			'folder'    : '/service_pictures',
			'auto'      : true,
			'multi'		: true,
			'fileExt'     : '*.jpg;*.jpeg;*.gif;*.png;*.JPG;*.JPEG;*.GIF;*.PNG;',
			'fileDesc'    : 'Image Files',
			'sizeLimit'   : 2100000,
			'removeCompleted': false,
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
				$('#uploaded_files').val($('#uploaded_files').val()+'<br/>'+response);
			}	
		});
	});
</script>
<!-- End of Uploadify -->
</head>
<body>

	<script type="text/javascript">
		
	  if (BrowserDetect.browser == 'Explorer') {
		  if (BrowserDetect.version	< 9) {
			   document.write('<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> <div id="gap"> </div>');
		  }
	  }
	  if (BrowserDetect.browser == 'Firefox') {
		  if (BrowserDetect.version	< 6) {
			   document.write('<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> <div id="gap"> </div>');
		  }
	  }
	  if (BrowserDetect.browser == 'Opera') {
		  if (BrowserDetect.version	< 11) {
			   document.write('<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> <div id="gap"> </div>');
		  }
	  }
	  if (BrowserDetect.browser == 'Safari') {
		  if (BrowserDetect.version	< 5) {
			   document.write('<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> <div id="gap"> </div>');
		  }
	  }
	  if (BrowserDetect.browser == 'Chrome') {
		  if (BrowserDetect.version	< 11) {
			   document.write('<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> <div id="gap"> </div>');
		  }
	  }
	  if ((BrowserDetect.browser != 'Chrome') &&  (BrowserDetect.browser != 'Firefox') && (BrowserDetect.browser != 'Opera') && (BrowserDetect.browser != 'Safari') && (BrowserDetect.browser != 'Explorer')) {
		  if (BrowserDetect.version	< 11) {
			   document.write('<div id="broswer_warning">Sorry, your broswer is not supported by Hoody. Please use the latest IE, Firefox, Chrome, Safari, or Opera.</div> <div id="gap"> </div>');
		  }
	  }
    </script>
<?php 
	flush();
	include "html/title_bar_new2.inc";
?>
  <div id="content">
    <div id = "isFilledWrap">
      <input name = "isFilled" id="isFilled" value="<?php echo "$listing_exist"; ?>" />
      <div id="testfield"> </div>
    </div>
    
    <div id='page_title'>Post a Service</div>
    <div id = "tabs_block">
    <ul id='tabs_nav'>
      <li> <a id='tab1' class='tabs'> 1. Description </a> </li>
      <li> <a id='tab2' class='tabs' > 2. Location </a> </li>
      <li> <a id='tab3' class='tabs'> 3. Pictures  </a> </li>
    </ul>
    
    <div id='tab_container'>
      <form id="Upload" action="<?php if($listing_exist) {echo($working_directory.'internal_upload_listing.php?lid='.$lid);} else{echo($working_directory.'internal_upload_listing.php');} ?>" 
      enctype="multipart/form-data" method="post" onsubmit="beginUpload();">
      
      <div id='tab1_content' class='step1_info'>
      	<fieldset class="listing_form" id="step_1">
        <label class="step1_label" for="title">Service Title</label>        
        <input name="title" type="text" id="title" class="desc_input" value="<?php if($_POST['title'] == "") {print($title);} echo $_POST['title'] ?>" input="input" />
        
        <p>Seller Name: <?php echo $seller_name ;?></p>
        <label class="step1_label" for="title">User ID</label>        
        <input name="seller_uid" type="text" id="seller_uid" class="desc_input" value="<?php if($_POST['seller_uid'] == "") {print($seller_uid);} echo $_POST['seller_uid'] ?>" input="input" />
        
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
                	<img class='box_checked_job' src="http://img.gohoody.com/attachements/radio_check.png" />
                    <img class='box_unchecked_job' src="http://img.gohoody.com/attachements/box_uncheck.png" />
                </div>
             
            	<input type="radio" class = "pricing_model" id="radio2" name="pricing_model" value="1"
                <?php if($pricing_model) echo "checked"; ?>> <label for="radio2">&nbsp;&nbsp;&nbsp;&nbsp;Per Hour</label>
                
                <div id='checkbox_hour'>
                	<img class='box_checked_hour' src="http://img.gohoody.com/attachements/radio_check.png" />
                    <img class='box_unchecked_hour' src="http://img.gohoody.com/attachements/box_uncheck.png" />
                </div>
            </div> 
        </div>
    	</fieldset>  
        
        <div class='tab_right_side'>
        	<div class='side_box' id='tip_title'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>Don't try to do everything in one service. Split into multiple services if needed.</div>
            </div>
            
            <div class='side_box' id='tip_description'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>Describe your service in detail so others will know what you're offering.</div>
            </div>
            
            <div class='side_box' id='tip_price'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>If price varies based on the job, put an amount that best represents the price range.</div>
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
          <label for="location_home">&nbsp;&nbsp;&nbsp;&nbsp;At my home </label>
          <div id='checkbox_home'>
              <img class='box_checked_home' src="http://img.gohoody.com/attachements/radio_check.png" />
              <img class='box_unchecked_home' src="http://img.gohoody.com/attachements/box_uncheck.png" />
          </div>
          
          <input type="radio" class='location_options' id = "location_away" name="location1" value="away_home" <?php if($listing_location != 0) {echo "checked";} ?>>
          <label for="location_away">&nbsp;&nbsp;&nbsp;&nbsp;Away from my home</label>
          <div id='checkbox_away'>
              <img class='box_checked_away' src="http://img.gohoody.com/attachements/radio_check.png" />
              <img class='box_unchecked_away' src="http://img.gohoody.com/attachements/box_uncheck.png" />
          </div>
        </div>
        
        <div id="display_address_wrap">
           <img id='arrow_up1' src="http://img.gohoody.com/attachements/arrow_up_create.png"  />
           <div id="input_display_address">
           	<input type="checkbox" id="display_address" name="show" value="1" checked="checked" >
            <label for="display_address"> &nbsp;&nbsp;&nbsp;&nbsp;Display address in the listing </label>
            <div id='checkbox_home'>
                <img class='box_checked_addy' src="http://img.gohoody.com/attachements/box_check.png" />
                <img class='box_unchecked_addy' src="http://img.gohoody.com/attachements/box_uncheck.png" />
            </div>
           </div>
        </div>       
        
        <div id="display_address_wrap2">
          <img id='arrow_up2' src="http://img.gohoody.com/attachements/arrow_up_create.png"  /> 
          <div id="away_from_home_options">
          	<input type="radio" class='away_options' id = "buyer_home" name="location2" value="buyer_home" <?php if($listing_location == 1) {echo "checked";} ?>>
            <label for="buyer_home"> &nbsp;&nbsp;&nbsp;&nbsp; Customer's home </label>
            <div id='checkbox_home'>
                <img class='radio_checked_buyer' src="http://img.gohoody.com/attachements/radio_check.png" />
                <img class='radio_unchecked_buyer' src="http://img.gohoody.com/attachements/box_uncheck.png" />
            </div>
          
          	<input type="radio" class='away_options' id = "other" name="location2" value="other" <?php if($listing_location == 2) {echo "checked";} ?>>
            <label for="other"> &nbsp;&nbsp;&nbsp;&nbsp; Another location </label>  
            <div id='checkbox_another'>
                <img class='radio_checked_another' src="http://img.gohoody.com/attachements/radio_check.png" />
                <img class='radio_unchecked_another' src="http://img.gohoody.com/attachements/box_uncheck.png" />
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
        	<div class='side_box' id='tip_home'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Note </div>
                <div class='side_box_content'>Display the address as it appears in your dashboard.</div>
            </div>
            
            <div class='side_box' id='tip_range'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Note </div>
                <div class='side_box_content'>This defines the range for service.</div>
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
            
            <div id='restrictions'>
                <div><b>File types supported:</b> .jpg .png .gif</div>
                <div><b>Maximum file size:</b> 1.5MB per picture</div>
            </div>
        </div> 
       </fieldset>
       
       <div class='tab_right_side'>
       		<div class='side_box' id='tip_pictures'>
            	<img class='tip_arrow' src="http://img.gohoody.com/attachements/tip_arrow.png"  />
            	<div class='side_box_title'> Tip </div>
                <div class='side_box_content'>A picture can make your service look more attractive.</div>
            </div>
       </div> <!--end .tab_right_side--> 
		
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
   <div id="page-wrap"></div> <!--end #page-wrap-->
  </div> <!--end of #content-->
      
  <?php include "html/footer.inc"; ?>
  
  <script type="text/javascript">
    jQuery(document).ready(function($){
      $('.lightbox').lightbox();
	  
    });
	
	$('#price').numeric();
	$('#range').numeric();
	$('#street').alphanumeric({allow:"-#():., "});
	$('#city').alphanumeric({allow:"-#():., "});
 
    $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('img[title]').qtip({
			   
			   style: {
				  classes: 'ui-tooltip-rounded ui-tooltip-shadow'
				  
			   },
			   position: {
				  my: 'bottom left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {
					  x: 5,  y: -10
				  }
			  },
			  hide: {
				  fixed: true // Helps to prevent the tooltip from hiding ocassionally when tracking!
			  },
			})	
	  });
  </script>
</body>
</html>