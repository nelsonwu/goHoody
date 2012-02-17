<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

    //Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";

	$page_title = "Hoody - Frequently Asked Questions";
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
<meta name="Description" content="Hoody - Frequently Asked Questions" />
<meta name="Keywords" content="Hoody, GoHoody, Frequently Asked Questions, FAQ, Trusted Local Services, Social Integration, Post a service, Local classifieds, Social classifieds"  />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!--Facebook meta properties -->
    <meta property="og:title" content="Hoody"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/FAQ/"/>
    <meta property="og:image" content="<?php echo $domain_secondary;?>attachements/home_page/header/hoodylogo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="It's the easiest way to look for and offer local services!"/>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/faq.css" type="text/css"/>
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
      <h1 class="about_title">Frequently Asked Questions</h1>
      <div id='left_section'>
        <div class='content_title'>What is Hoody?</div>
        <p>Hoody is a friendly marketplace for finding trusted local services. You can find out what services your friends are using and read up on reviews left by past customers.  <br /><br />If you want to sell a service, Hoody can help you create a professional-looking page to promote your service, completely free of charge. Integrated with social networks, Hoody can create viral marketing through online word-of-mouth. Handcrafted by graphic designers and computer engineers, Hoody presents a premium and professional look that's sure to please.</p>
      
        <div class='content_title'>Why Hoody?</div>
        <p> Hoody emphasize on trust and transparency. We believe that it's hard to build trust on an anonymous network. When identities are hidden, it's easy for scams and frauds to take place. At Hoody, everyone's identity is verified through Facebook, making it a safe and trusted place. By knowing who is selling a service and reading reviews left by friends and other customers, Hoody offers transparency - you know exactly what the service can offer before deciding to buy it.<br /><br />
         Hoody is a powerful tool for anyone to promote their services. You can easily create a Hoody page for your service in minutes. Once your page is up, it is deeply integrated with the most popular social networks such as Facebook, Twitter, LinkedIn and Google+ to help sellers build their reputation and engage with potential and past buyers easily. Furthermore, the Hoody Dashboard can track all the important and useful stats about seller's Hoody listings. </p>
       
       <div class='content_title'>How much does Hoody cost?</div>
        <p> Hoody is absolutely free of charge. </p>
        
        <div class='content_title'>I'm good at something, but not sure if I can sell it as a service?</div>
        <p> No problem! Use Hoody as a training ground to hone your skills. Say you're good at graphic deisgn or programming or even teaching. Post a service around that skill and get hired! Don't need to charge a lot (could even be free!). There are lots of people offering free services for the sole purpose of improving their skills and meeting new people. Besides, start-up companies are always looking for fresh talent, you can get experience, build up your portfolio and build connections. </p>
        
        <div class='content_title'>Can I use Hoody if I don't have Facebook?</div>
        <p> Not at the moment, all Hoody Profiles must be linked with a Facebook account. The purpose of this is to increase the transparency of the service providers and customer feedback. This is fundamental towards creating a trusted network and helpful towards those who aim to build a good reputation. Please sign up for a Facebook account at http://www.facebook.com. </p>       
      </div>
      <img id='aboout_pic' src="<?php echo $domain_secondary;?>attachements/hoody-service-page.png"  />
    </div>
    <?php include "html/footer.inc";  ?> 
    </body>   	
</html>