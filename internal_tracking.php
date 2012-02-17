<?php
	// Program: about.php
	//
	// Desc:	About Us
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	require "php/admin.inc";
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
      href="http://www.athoody.com/attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<!--SEO-->
<meta name="Description" content="Hoody - Frequently Asked Questions" />
<meta name="Keywords" content="Hoody, GoHoody, Frequently Asked Questions, FAQ, Trusted Local Services, Social Integration, Post a service, Local classifieds, Social classifieds"  />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<!--Facebook meta properties -->
    <meta property="og:title" content="Hoody"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="http://gohoody.com/about-us/"/>
    <meta property="og:image" content="http://www.athoody.com/attachements/home_page/header/hoodylogo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="192823134073322"/>
    <meta property="og:description"
          content="It's the easiest way to look for and offer local services!"/>

<link rel="stylesheet" href="css/faq.css" type="text/css"/>
<link rel="stylesheet" href="css/title_bar_new.css" type="text/css" media="screen" />

<script type="text/javascript" src="javascript/jquery-1.5.min.js"></script>
<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<!--Google Analytic Tracking Code-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23022233-1']);
  _gaq.push(['_setDomainName', '.gohoody.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<div id="fb-root"></div>
<script src="javascript/facebook_js.inc" type="text/javascript"></script>
    
<!--GetSatisfaction Script-->
<script type="text/javascript" charset="utf-8">
  var is_ssl = ("https:" == document.location.protocol);
  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
//browser_detection	

var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera",
			versionSearch: "Version"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();

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


</script>

</head>

<?php flush(); ?>

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

<?php	include "html/title_bar_new2.inc"; ?>
  	<div id="content">  
<?php
	$query = "SELECT * FROM QR_Code_Tracking ORDER BY id";
	$result = mysql_query($query) or die (minor_error(102, $user, $user, $today, $query, mysql_error()));
	$result_num = mysql_num_rows($result);
?>
<?php	if ($result_num > 0): ?>
	<table width="940" border="1">
        <tr>
            <th width=40% scope="col"><div align="center">Description</div></th>
            <th width=30% scope="col"><div align="center">Destination</div></th>
            <th width=15% scope="col"><div align="center">Medium</div></th>
            <th width=15% scope="col"><div align="center">Visit</div></th>
        </tr>
<?php 	while($result_row = mysql_fetch_array($result)): ?>
<?php	
		extract($result_row); 
		
		if ($medium == 1)
			$medium = "Bookmarks";
		else if ($medium == 2)
			$medium = "Flyers";
		else if ($medium ==3)
			$medium = "Emails";
?>    
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th scope="row"><div align="center"><?php echo $description; ?></div></th>
            <td><div align="center"><a href="http://<?php echo $link; ?>"><?php echo $destination; ?></a></div></td>
            <td><div align="center"><?php echo $medium; ?></div></td>
            <td><div align="center"><?php echo $visits; ?></div></td>
        </tr>
<?php endwhile; ?>
	</table>
<?php endif; // if ($other_listing_num > 0)?>  
    </div>
    <?php include "html/footer.inc";  ?> 
    </body>   	
</html>