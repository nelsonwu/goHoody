<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

    //Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";

	$page_title = "Hoody - About Us";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta property="fb:admins" content="28130239" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary;?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<!--SEO-->
<meta name="Description" content="Hoody - About Us" />
<meta name="Keywords" content="Hoody, GoHoody, About Us, Trusted Local Services, Social Integration, Post a service, Local classifieds, Social classifieds"  />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!--Facebook meta properties -->
    <meta property="og:title" content="Hoody"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/about-us/"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/home_page/header/hoodylogo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="It's the easiest way to look for and offer local services!"/>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/about_us.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js"></script>
<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>
</head>
<body>
<?php 
	flush();
	include "html/title_bar_new2.inc";
?>  
<div id="content">  
      <h1 class="about_title">What is Hoody?</h1>
      <div id='about_hoody'>
      
        <div class='content_title'>A powerful platform for promoting your services.</div>
        <p>If you have a service you're looking to sell, whether it's tutoring, web development, or something obscure, Hoody can help you create a webpage to promote yourself, for free!</p>
        <p>Hoody quickly creates a professional-looking, permanently-hosted webpage on a network of verified users. A community with trusted buyer feedback and no evil trolls souring seller ratings.</p>
        <p>Once your page is up, it integrates seamlessly with a range of social networks, for great viral
        promotion. You can easily manage your service in the seller Dashboard and watch it grow! </p>
    
        <div class='content_title'>A friendly marketplace to find trusted services.</div>
        <p> Hoody makes it easy to find trusted services near you. By integrating with social networks, Hoody puts a face on each service. You can get to know the sellers before deciding to buy their services. </p>
        <p> With Hoody, you can see what services your friends are using and what they have to say about it. If none of your friends have used the service, you can always check out reviews written by other verified users. </p>
        <p> If you like a service, you can easily share it with a friend using one of the social sharing plug-ins and let others enjoy this trusted service.  </p>
      </div>
      <img id='aboout_pic' src="<?php echo $domain_secondary;?>attachements/hoody-service-page.png"  />
      <h1 class="about_title">Who are we?</h1> 
      <div id='about_us'>    
        <p> We are a group of young entrepreneurs located in Toronto who are passionate about making the world better connected. Feel free to give us a shout!</p>
        
          <div class='profile1'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/nelson'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/nelson.png' />
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/nelson'> Nelson </a> </div>
            <div class='profile_email'> nelson.wu@gohoody.com </div>
          </div>
        
          <div class='profile1'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/mike'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/mike.png' /></a>
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/mike'> Mike </a> </div>
            <div class='profile_email'> mike.tang@gohoody.com </div>
          </div>
           
          <div class='profile1'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/althea'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/althea.png' /></a>
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/althea'> Althea </a> </div>
            <div class='profile_email'> althea.manasan@gohoody.com </div>
          </div>
       
          <div id='right'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/shawn'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/shawn.png' /></a>
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/shawn'> Shawn </a> </div>
            <div class='profile_email'> shawn.wang@gohoody.com </div>
          </div>
          
          <div class='profile1'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/ivan'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/ivan.png' /></a>
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/ivan'> Ivan </a> </div>
            <div class='profile_email'> ivan.kostynyk@gohoody.com </div>
          </div>
          
          <div class='profile1'>
            <img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/hyman.png' />
            <div class='profile_name'>Hyman  </div>
            <div class='profile_email'> hyman.chan@gohoody.com </div>
          </div>
          
          <div class='profile1'>
            <a class='profile_link' href='<?php echo $working_directory;?>profile/annie'><img class='prifile_pic' src='<?php echo $domain_secondary; ?>attachements/annie.png' /></a>
            <div class='profile_name'><a class='profile_link' href='<?php echo $working_directory;?>profile/annie'> Annie </a> </div>
            <div class='profile_email'> annie.chou@gohoody.com </div>
          </div>        
      </div> <!--end #about_us-->    
    </div>    
    <?php include "html/footer.inc";  ?> 
    </body>   	
</html>