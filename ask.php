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

	$page_title = "Hoody: Just Ask!";	
	
	//Link to Facebook PHP SDK
    include "php/fbmain.php";
	$config['baseurl']  =   $working_directory. "index.php";
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta property="og:title" content="<?php echo $page_title; ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/ask/"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/justAsk_logo1.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="Place for everyone to ask any questions"/>
          
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php print($page_title) ?></title>

<!--CSS Begins-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/ask.css" type="text/css" media="screen" />
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

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<!--Notifications-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.gritter.js"></script>

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
	<div id = 'top_sect'>    
        <img id='justask_logo' src="<?php echo $domain_secondary;?>attachements/justAsk_logo4.png" />
        <div id='q1'> "How do I get more traffic to my blog?" </div>
        <div id='q2'> "What are the differences between LED and LCD TVs?" </div>
        <div id='q3'> "What's the purpose of lens filters?" </div>
        <div id='q4'> "How much exercise does my dog need to stay healthy?" </div>
        <div id='q5'> "What are some natural beauty tips?" </div>
        <div id='q6'> "What does the rating on motor oils mean?"</div>
        <div id='page_title'> Looking for Help? <b> Just Ask!</b> </div>
    </div> <!--end #top_sect-->
    
    <div id='info_bar'>
    	<div id='info_text'> Ask questions in these topics: </div>
        <select id='location_dropdown'>
          <option>Greater Toronto Area</option>
          <option>more locations coming soon...</option>
        </select>
        <div id='location_text'> Location: </div>
    </div>
    
    <div id='sections'>
    	<div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/computer-and-electronics-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_computer.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/computer-and-electronics-gta/'> Computer and Electronics </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/graphic-design-and-web-development-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_web.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/graphic-design-and-web-development-gta/'> Graphic Design and Web Development </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/photography-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_photography.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/photography-gta/'> Photography </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/fashion-and-beauty-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_fashion.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/fashion-and-beauty-gta/'> Fashion and Beauty </a>
        </div> <!--end .section_box-->
        
        <div class='section_box box_right'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/transportation-and-auto-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_car.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/transportation-and-auto-gta/'> Transportation and Auto </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/personal-finance-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_finance.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/personal-finance-gta/'> Finance </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/pets-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_pet.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/pets-gta/'> Pets </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/academic-and-education-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_academic.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/academic-and-education-gta/'> Academic and Education </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/home-and-garden-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_home.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/home-and-garden-gta/'> Home and Garden </a>
        </div> <!--end .section_box-->
        
        <div class='section_box box_right'>
        	<div class='pic_wrapper box_right'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/health-and-fitness-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_health.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/health-and-fitness-gta/'> Health and Fitness </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/travel-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_travel.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/travel-gta/'> Travel </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/insurance-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_insurance.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/insurance-gta/'> Insurance </a>
        </div> <!--end .section_box-->
        
        <div class='section_box'>
        	<div class='pic_wrapper'>
            	<a class='pic_link' href='<?php echo $working_directory; ?>ask/real-estate-gta/'>
                	<img src="<?php echo $domain_secondary;?>attachements/section_real_estate.jpg" />
                </a>
            </div>
            <a class='section_title' href='<?php echo $working_directory; ?>ask/real-estate-gta/'> Real Estate </a>
        </div> <!--end .section_box-->
    </div> <!--end #sections-->
</div> <!--end of #content-->

<?php include "html/footer.inc"; ?>
<script type="text/javascript">
	   $(document).ready(function()
	  {
		  // Match all <A/> links with a title tag and use it as the content (default).
		  $('a[title]').qtip({
			   style: {
				  classes: 'ui-tooltip-rounded ui-tooltip-shadow'},
			   position: {
				  my: 'top left',
				  target: 'mouse',
				  viewport: $(window), // Keep it on-screen at all times if possible
				  adjust: {
					  x: 10,  y: 10}},
			  hide: { // Helps to prevent the tooltip from hiding ocassionally when tracking!
				  fixed: true },})
			$("#q1").hide().delay(1000).fadeIn();
  			$("#q2").hide().delay(3500).fadeIn();
			$("#q3").hide().delay(2000).fadeIn();
			$("#q4").hide().delay(3000).fadeIn();
			$("#q5").hide().delay(1500).fadeIn();
			$("#q6").hide().delay(2500).fadeIn();});
	  //For fixed left column effect
	  var scrollY = $('.fixedElement').offset().top;
	  $(window).scroll(function(e){ 
		if ($(window).scrollTop() > scrollY ){ 
		  $('.fixedElement').css({'position': 'fixed', 'top': '0px'});}
		else {
			$('.fixedElement').css({'position': 'relative', 'top': ''});}});

  //Google +1 button - Google specified that this code need to implemented after the +1 button
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();  
</script>
</body>
</html>
    
