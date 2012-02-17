<?php
	// Program: index.php
	// Desc:	landing page
	//
	//		4/11	Mike
	//				- Added CSS for layout
	//				- Added nav bar move-over effect 
	//				- Added the slider
	//				- New javascript files:
	//					jquery.easing.1.2.js
	//					jquery.anythingslider.js
	//					button_hover.js
	//				- New CSS files:
	//					slider.css
	//
	//
	//		4/12	Nelson
	//				- Add slider content
	//
	//		4/14 	Mike
	//				- Added the caption effect
	//				- New CSS file - 'style2.css'
	//				- New javascript file - jquery.capSlide.js
	//
	//		4/14	Nelson
	//				- Implemented the caption effect within the slider
	//
	//		4/15	Mike
	//				- Changed the layout of the caption:
	//					Files modified: 'style.css' 
	//				- Hide/unhide the user info depending on user login status
	//					Files modified: 'title_bar_no_search.inc'
	//									'facebook_js.inc'
	//									'button_hover.js'				
	//				- Used background img for the search bar + button instead of standard browser look
	//					Files modified: 'index.php'
	//									'style.css'
	//					Files added: 'attachements/search_bg.png'
	//		4/27	Nelson
	//				- Various bug fix
	//				- Add GetSatisfaction script
	//		4/28	Nelson
	//				- Update GetSatisfaction tab colour
	//				- Update Google Analytic Tracking Code
	//
			
	$page_title = "Landing Page";
	
	//Connect to @Hoody MySQL database
	include "php/misc.inc";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";
 
	include "php/fb_status.inc";	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta property="fb:admins" content="28130239" />
<title><?php print($page_title) ?></title>

<link rel="stylesheet" href="css/style.css" type="text/css"/>
<link rel="stylesheet" href="css/style2.css" type="text/css"/>
<link rel="stylesheet" href="css/slider.css" type="text/css" media="screen" />

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

<script type="text/javascript" src="javascript/jquery-1.5.js"></script>
<script type="text/javascript" src="javascript/jquery.easing.1.2.js"></script>
<script src="javascript/jquery.anythingslider.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="javascript/button_hover.js"></script>   

<!--Parameters for the slider-->
<script type="text/javascript">

  function formatText(index, panel) {
      return index + "";
  };

  $(function () {
  
      $('.anythingSlider').anythingSlider({
          easing: "easeInOutCubic",        // Anything other than "linear" or "swing" requires the easing plugin
          autoPlay: true,                 // This turns off the entire FUNCTIONALY, not just if it starts running or not.
          delay: 5000,                    // How long between slide transitions in AutoPlay mode
          startStopped: false,            // If autoPlay is on, this can force it to start stopped
          animationTime: 700,             // How long the slide transition takes
          hashTags: true,                 // Should links change the hashtag in the URL?
          buildNavigation: true,          // If true, builds and list of anchor links to link to each slide
          pauseOnHover: true,             // If true, and autoPlay is enabled, the show will pause on hover
          startText: "Go",                // Start text
          stopText: "Stop",               // Stop text
          navigationFormatter: formatText // Details at the top of the file on this use (advanced use)
      });
      
      $("#slide-jump").click(function(){
          $('.anythingSlider').anythingSlider(6);
      });
        
  });
</script>  
    

<script src="javascript/jquery.capSlide.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
       
        $(".ic_container").capslide({
            caption_color	: '#fff',
            caption_bgcolor	: '#000',
            overlay_bgcolor : 'black',
            border			: '',
            showcaption	    : true
        });
        
      

    });
</script>
    
<!--GetSatisfaction Script-->
<script type="text/javascript" charset="utf-8">
  var is_ssl = ("https:" == document.location.protocol);
  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>
</head>
<body>

<!--GetSatisfaction Script-->
<script type="text/javascript" charset="utf-8">
  var feedback_widget_options = {};

  feedback_widget_options.display = "overlay";  
  feedback_widget_options.company = "hoody";
  feedback_widget_options.placement = "left";
  feedback_widget_options.color = "#faaf3b";
  feedback_widget_options.style = "idea";
  
  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>

	<div id="header_bg">
    
    </div>
    
  	<div id="content">  
    
        <div id="navigation"> 
            <?php include "html/title_bar_no_search.inc"; ?> </div>
        <div id="search">
        
            <div id="sell_sect">
            	
                <div id="sell_question">
                	<img src="attachements/home_page/search_bar/sell_q.png" >
                </div>
                
                <div id="sell_button">
                	<a href = "create_listing.php"> <p id="submit_link">SUBMIT</p> </a>
                </div>
                
            
            </div>
            
            <div id="search_sect">
            
        		<div id="search_q">
                	<img src="attachements/home_page/search_bar/search_q.png" >
                </div>
                
                <div id="search_bar">
                
                  <form method="get" id="searchform" action="listing_page.php">
                        <fieldset class="search">
                            <input type="text" class="box" name="q" value="SEARCH FOR A SERVICE"
                            onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/>
                            <button class="btn" title="Submit Search">Search</button>
                        </fieldset>
                  </form>
                  
                </div> 
               
            </div>
               
        </div>
         <div id="picturetile">
           
          <div id="background">
          </div>
          <div id="bg_right">
            <img src="attachements/home_page/picture_tiles/bg_right.png" height="343px" >
          </div>	
          <div id="bg_bottom">
            <img src="attachements/home_page/picture_tiles/bg_bottom.png" width="916px" >
          </div>
          <div id="page-wrap">
              <div class="anythingSlider">
                <div class="wrapper">
                  <ul id = "slides">                
                    <li>
    <?php
                        $query = "SELECT title,price,listing_id,fb_uid FROM Listing_Overview WHERE status=1 ORDER BY Rand() LIMIT 4";
                        $result = mysql_query($query) or die("Couldn't execute query");
                       
                        for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) 
                        {
                            extract($row);
                    
                            // Extract pictures for the listing
                            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
                            $picture_result = mysql_query($picture_sql) or die ("Couldn't execute query. - extra picture id");
                            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
                            extract($picture_row);
                            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
                            $url_result = mysql_query($url_sql) or die ("Couldn't execute query. - extract picture URL");
                            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
                            extract($url_row);	
                            $name_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
                            $name_result = mysql_query($name_sql) or die ("Couldn't execute query. - extract picture URL");
                            $name_row = mysql_fetch_array($name_result,MYSQL_ASSOC);
                            extract($name_row);		
                            
                            switch ($counter) 
                            {
                                case 1:
                                    echo 	"<table width='890'>
                                                <tr>
                                                <td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=400&zc=1' width='370' height='259' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                            </a></div></td>";
                                    break;
                                case 2:
                                    echo 	"	<td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>								
                                            </a></div></td>";
                                    
                                    break;
                                case 3:
                                    echo "		<td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=270&zc=1' width='240' height='249' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>";
                                    break;
                                case 4:
                                    echo "		<tr>
                                                <td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>
                                            </table>";
                                    break;
                            }
                        }			
                            
    ?>
                    </li>
                    
                    <li>
    <?php
                        $query = "SELECT title,price,listing_id,fb_uid FROM Listing_Overview WHERE status=1 && (listing_id = 324 || listing_id = 319 || listing_id = 320 || listing_id = 321)";
                        $result = mysql_query($query) or die("Couldn't execute query");
                        for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) 
                        {
                            extract($row);
                    
                            // Extract pictures for the listing
                            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
                            $picture_result = mysql_query($picture_sql) or die ("Couldn't execute query. - extra picture id");
                            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
                            extract($picture_row);
                            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
                            $url_result = mysql_query($url_sql) or die ("Couldn't execute query. - extract picture URL");
                            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
                            extract($url_row);
                            $name_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
                            $name_result = mysql_query($name_sql) or die ("Couldn't execute query. - extract picture URL");
                            $name_row = mysql_fetch_array($name_result,MYSQL_ASSOC);
                            extract($name_row);	
                                                    
                            switch ($counter) 
                            {
                                case 1:
                                    echo 	"<table width='890'>
                                                <tr>
                                                <td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=400&zc=1' width='370' height='259' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                            </a></div></td>";
                                    break;
                                case 2:
                                    echo 	"	<td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>								
                                            </a></div></td>";
                                    
                                    break;
                                case 3:
                                    echo "		<td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=270&zc=1' width='240' height='249' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>";
                                    break;
                                case 4:
                                    echo "		<tr>
                                                <td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>
                                            </table>";
                                    break;
                            }
                        }		                
    ?>
                    </li>
                    <li>
    <?php
                        $query = "SELECT title,price,listing_id,fb_uid FROM Listing_Overview WHERE status=1 ORDER BY listed_time  DESC LIMIT 4";
                        $result = mysql_query($query) or die("Couldn't execute query");
                        for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) 
                        {
                            extract($row);
                    
                            // Extract pictures for the listing
                            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
                            $picture_result = mysql_query($picture_sql) or die ("Couldn't execute query. - extra picture id");
                            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
                            extract($picture_row);
                            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
                            $url_result = mysql_query($url_sql) or die ("Couldn't execute query. - extract picture URL");
                            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
                            extract($url_row);		
                            $name_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
                            $name_result = mysql_query($name_sql) or die ("Couldn't execute query. - extract picture URL");
                            $name_row = mysql_fetch_array($name_result,MYSQL_ASSOC);
                            extract($name_row);	
                                                    
                            switch ($counter) 
                            {
                                case 1:
                                    echo 	"<table width='890'>
                                                <tr>
                                                <td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=400&zc=1' width='370' height='259' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                            </a></div></td>";
                                    break;
                                case 2:
                                    echo 	"	<td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>								
                                            </a></div></td>";
                                    
                                    break;
                                case 3:
                                    echo "		<td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=270&zc=1' width='240' height='249' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>";
                                    break;
                                case 4:
                                    echo "		<tr>
                                                <td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>
                                            </table>";
                                    break;
                            }
                        }	                 
    ?>
                    </li>
                    <li>
    <?php
                        $query = "SELECT title,price,listing_id,fb_uid FROM Listing_Overview WHERE status=1 ORDER BY popularity DESC LIMIT 4";
                        $result = mysql_query($query) or die("Couldn't execute query");
                        for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) 
                        {
                            extract($row);
                    
                            // Extract pictures for the listing
                            $picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='$listing_id'";
                            $picture_result = mysql_query($picture_sql) or die ("Couldn't execute query. - extra picture id");
                            $picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
                            extract($picture_row);
                            $url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='$picture_id_1'";
                            $url_result = mysql_query($url_sql) or die ("Couldn't execute query. - extract picture URL");
                            $url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
                            extract($url_row);		
                            $name_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
                            $name_result = mysql_query($name_sql) or die ("Couldn't execute query. - extract picture URL");
                            $name_row = mysql_fetch_array($name_result,MYSQL_ASSOC);
                            extract($name_row);	
                                                    
                            switch ($counter) 
                            {
                                case 1:
                                    echo 	"<table width='890'>
                                                <tr>
                                                <td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=400&zc=1' width='370' height='259' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                            </a></div></td>";
                                    break;
                                case 2:
                                    echo 	"	<td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>								
                                            </a></div></td>";
                                    
                                    break;
                                case 3:
                                    echo "		<td rowspan='2'><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=280&w=270&zc=1' width='240' height='249' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>";
                                    break;
                                case 4:
                                    echo "		<tr>
                                                <td><div align='center'><a class='ic_link' href='service_description.php?lid=$listing_id'>
                                                <div class='demo'>
                                                    <div class='ic_container'>
                                                        <img src='http://www.athoody.com/resizer.php?src=http://www.athoody.com/service_pictures/$URL&h=140&w=250&zc=1' width='220' height='119' alt=''/>
                                                        <div class='overlay' style='display:none;'></div>
                                                        <div class='ic_caption'>
                                                            <h3 class='ic_title'>$title</h3>
                                                            <p class='ic_text'>Price: \$$price <br /> by $name</p>
                                                </div></div></div>
                                                </a></div></td></tr>
                                            </table>";
                                    break;
                            }
                        }		                 
    ?>
                    </li>
                  </ul>        
                </div>
              </div> <!-- END AnythingSlider -->
         
              
         
            
          </div> 
        </div> 
          
          
        <div id = "foot">
            <?php include "html/footer.inc";  ?>  </div>
        
      </div>
    </body>
</html>