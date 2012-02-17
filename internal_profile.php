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
		
	//only user logged into Facebook has access to create listing
	if (!in_array($user, $admin))
		header("Location: index.php");
	
	$page_title = "Create Temporary User";
			
	$basic_query = "SELECT fb_uid FROM Basic_User_Information WHERE fb_uid = (SELECT MIN(fb_uid) FROM Basic_User_Information)";
	$result = mysql_query($basic_query) or die (fatal_error(275, $user, $user, $today, $basic_query, mysql_error()));
	$row3 = mysql_fetch_array($result,MYSQL_ASSOC);
	if ($row3['fb_uid'] > 0)
		$user_id = -1;
	else
		$user_id = $row3['fb_uid'] - 1;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
            
  <link rel="icon" 
      type="image/png" 
      href="http://www.athoody.com/attachements/favicon.png" />
  <title><?php echo $page_title; ?></title>
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
            
  <link rel="icon" 
      type="image/png" 
      href="http://www.athoody.com/attachements/favicon.png" />
  <title><?php echo $page_title; ?></title>
  
<div id="fb-root"></div>
<script src="javascript/facebook_js.inc" type="text/javascript"></script>

<!--CSS-->
<link href="css/uploadify.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/create_listing.css" media="screen" />
<link rel="stylesheet" href="css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/qtip2.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.gritter.css" />
<link rel="stylesheet" type="text/css" href="http://www.gohoody.com/css/jquery-ui-1.8.16.custom.css" />	

<script src="javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>
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
    <div id='page_title'>Add a temporary user</div>
   <div id = "tabs_block">
    <div id='tab_container'>
      <form action="<?php echo 'internal_upload_profile.php'; ?>" enctype="multipart/form-data" method="post">            	
        <p>User Name <input name="user_name" type="text" input="input" /></p>
        <p>User First Name <input name="user_first_name" type="text" input="input" /></p>
        <p>User ID <input name="user_id" type="text" input="input" value="<?php echo $user_id; ?>" /></p>
        <p>Email <input name="email" type="text" input="input" /></p>        
        <p>About User <textarea name="about_user"></textarea></p>
        <p>Country <input name="country" type="text" input="input" /></p>
        <p>Street <input type="text" name="street" input="input" /></p>
        <p>City <input name="city" type="text" input="input" /></p>
        <p>State/Province <input name="state" type="text" input="input" /></p>
        <p>Postal Code <input name="postal_code" type="text" input="input" /></p>
    	</fieldset>  
	  <button type='submit' name="submit">Post Service</button>
      </form> 	
    </div> <!--end #tab_container-->
   </div> <!--end of #tabs_block--> 
   <div id="page-wrap">
   </div> <!--end #page-wrap-->
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