 <?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";			
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";
	
	$page_title = "Hoody Page Not Found";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary; ?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/lost.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/title_bar_new.css"  media="screen" />

<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!-- Google Maps Javascript API -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=CA"></script>

<!-- Facebook Javascript API -->
<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!-- jQuery library -->
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>

</head>
<?php	include "html/title_bar_new2.inc"; ?>
<div id="content">

	<div id='title_text'>Oops! The page you're looking for doesn't exist.</div>
    <div id='title_text2'>Since you're here, why no try out the 'Just Ask!' page?</div>
    <a href='<?php echo $working_directory; ?>ask/'> <img id='just_ask_logo' src="<?php echo $domain_secondary; ?>attachements/justAsk_logo4.png"  /> </a>
</div>
<div id="foot_sect"><?php include "html/footer.inc"; ?></div>
</body>
</html>