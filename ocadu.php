<?php
	include "featured_listings.inc";
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
    $config['baseurl']  =   $working_directory. "index.php";

	$page_title = "Hoody for OCADU | Hone your skills, build your portfolio, connect with companies";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<!--SEO-->
<meta name="Description" content="Hoody - Services for students, by students." />
<meta name="Keywords" content="Hoody, GoHoody, Student Services, OCADU, OCAD, Trusted Services, Social Integration, Post a service, Local classifieds, Social classifieds, Hone your skills, build your portfolio, connect with companies"  />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta property="fb:admins" content="28130239" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary; ?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<!--Facebook meta properties -->
    <meta property="og:title" content="Hoody - OCADU"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?php echo $domain_primary; ?>"/>
    <meta property="og:image" content="<?php echo $domain_secondary; ?>attachements/home_page/header/hoodylogo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="<?php echo $facebook_app_id; ?>"/>
    <meta property="og:description"
          content="Hone your skills, build your portfolio, connect with companies"/>

<![if !IE]>
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/ocad.css" type="text/css"/>
<![endif]>
<!--[if gte IE 6]>
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/ocad_ie.css" type="text/css"/>
<![endif]-->
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/countdown.css" />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js"></script>

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->

<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.easing.1.2.js"></script>
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.countdown.js"></script>
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>

<script type="text/javascript">
  function playvideo(){
	  $('#what_is_hoody').hide();
	  $('#play_button_cont').hide();
	  $('#video_obj').show();};
</script>  
</head>
<body>
	<div id="broswer_warning">Uh oh! Your browser is out of date! You may experience problems with this site. Please update your browser.</div> 
    <div id="gap"> </div>	
	<?php 
		flush(); 
		include "html/title_bar_new2.inc";
	?>    
  	<div class='content'>     
    	<div id='panels'>
          <?php if ($user): ?>
              <div id='left_panel'>
                  <div id='headline3'>Hi <?php echo $facebook_first_name?>! </div>
                  <div id='headline4'>Create a Hoody page to promote your skills now:</div>
                  <div id='post_section'>
                  	<div id='placeholder_text'> What skill are you promoting? </div>
                  	<form id="create" action="<?php echo $working_directory; ?>create/" method="post">
                    	<input id="post_service_input" name="service_title" type="text" placeholder="What skill are you promoting?"/>
                        <button type='submit' id='post_service_button' name="submit">Create Page</button>
                    </form>
                  </div>
                  <div id='suggestion_sect'>
                  	<div class='panel_text1'>Connect with companies</div>
             		<div class='panel_text2'>Start-ups are always looking for fresh talent. Show them what you have to offer. </div>   
                  </div>
              </div>  
          <?php else: ?>
        
          <div id='left_panel'>
              <div id='headline1'>Hone your skills, <br />become a professional!</div>
              <div id='headline2'>Create a Hoody page to promote your skills, gain valuable experience and make money doing what you love.</div>
                <a id='sign_up_button' onclick='facebookLogin(); return false;'><div class='fb_logo_button_new' ><img src='<?php echo $domain_secondary; ?>attachements/fb_logo.png' width='13px'/></div>Free! Get started now &nbsp;<em>></em></a>
          </div>
          
          <?php endif; //if ($user) ?>
          
          <div id='right_panel'>
            <div id='panel1'>
              <div class='panel_img'>
              	<img src="<?php echo $domain_secondary;?>attachements/skills3.jpg" />
              </div>
              <img class='box_shadow' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' alt=''/>
              <div class='panel_text1'>Hone your skills</div>
              <div class='panel_text2'>Build your portfolio, gain valuable experience and feedback to improve your skills. </div>
            </div>
        </div>
        </div> <!--end #panels-->         
      </div> <!--end .content-->
      <div class='divider'></div>
      <div class="content">        
        <div id='title_section'><div class='title_text'> What other students are promoting on Hoody: </div></div> <!--end #title_section-->      
                
        <div id='featured_sect'>
        	<div class='featured featured_left' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/nelson/'><img src='<?php echo $domain_secondary;?>attachements/UofT/nelson.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/nelson/'>Nelson Wu</a></div>
                <div class='seller_info'>Crazy About Apple, Motor Racing, and Polar Bears</div>
                <div class='indv_service'>
                	<a title='Computer Tune-up' href='<?php echo $working_directory; ?>service/385/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305759031-3446271885_b3ee5cb8ae_z.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a  title='Digital SLR Photography Course for Beginners' href='<?php echo $working_directory; ?>service/387/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305761668-2164996776_1f03516e69_z.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Computer Networking Setup' href='<?php echo $working_directory; ?>service/384/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305758479-3449116183_758f523471_z.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            <div class='featured' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/mike/'><img src='<?php echo $domain_secondary;?>attachements/UofT/mike.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/mike/'>Mike Tang</a></div>
                <div class='seller_info'>Tech-geek, entrepreneur, risk-taker, and drinker of tea.</div>
                <div class='indv_service'>
                	<a title='Smartphone/Tablet Setup' href='<?php echo $working_directory; ?>service/398/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1306082589-photo%201.JPG&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Editing service for documents and reports' href='<?php echo $working_directory; ?>service/600/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315212701-trial-hero-20090106.png&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Singing Buddy' href='<?php echo $working_directory; ?>service/388/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1305778479-Mic.JPG&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            
           <div class='featured featured_left' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/althea/'><img src='<?php echo $domain_secondary;?>attachements/UofT/althea.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/althea/'>Althea Manasan</a></div>
                <div class='seller_info'>Master student in Journalism.</div>
                <div class='indv_service'>
                	<a title='Copy editing' href='<?php echo $working_directory; ?>service/572/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1314980547-copyediting.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Nail Art' href='<?php echo $working_directory; ?>service/576/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315002588-IMG_2385.JPG&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            
            <div class='featured' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/ivan/'><img src='<?php echo $domain_secondary;?>attachements/UofT/ivan.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/ivan/'>Ivan Kostynyk</a></div>
                <div class='seller_info'>Graphic design student at OCADU. Passtionate about Graphic Design and Tech</div>
                <div class='indv_service'>
                	<a title='Graphic Design/Typography' href='<?php echo $working_directory; ?>service/472/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1308020348-ON-typeface.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                 </div>
                <div class='indv_service'>
                	<a title='Concept Industrial Design' href='<?php echo $working_directory; ?>service/571/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1314934349-258303_10150336858742837_634717836_9979495_3990650_o.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                 </div>
                <div class='indv_service'>
                	<a title='Custom Typography'  href='<?php echo $working_directory; ?>service/583/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315099254-IMG_2303.JPG&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
            <div class='featured featured_left' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/annie/'><img src='<?php echo $domain_secondary;?>attachements/UofT/annie.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/annie/'>Annie Chou</a></div>
                <div class='seller_info'>I am a graphic designer based in Toronto.</div>
                <div class='indv_service'>
                	<a title='Graphic Design' href='<?php echo $working_directory; ?>service/404/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1306178772-Screen%20shot%202011-03-21%20at%2012.06.15%20AM.png&w=67&h=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
                        
             <div class='featured' >
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/annayip/'><img src='<?php echo $domain_secondary;?>attachements/UofT/anna.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/annayip/'>Anna Yip</a></div>
                <div class='seller_info'>I'm a student in marketing</div>
                <div class='indv_service'>
                	<a title='Print media design' href='<?php echo $working_directory; ?>service/568/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315107542-die-cut-brochure-design.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Personal Shopper ;)' href='<?php echo $working_directory; ?>service/570/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1315108768-rome-personal-shopper.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
            </div> <!--end .featured-->
        </div> <!--end #featured_sect-->
        <div id='featured_services_sect'>
        	<div class='title_text'>Interested in buying a service? Check these out:</div> <!--end #featured_title-->
        	<?php if (empty($var)): ?>
        	<?php 
				$query = "SELECT title,price,pricing_model,listing_id,fb_uid FROM Listing_Overview 
				WHERE listing_id = '$featured_uoft_listings_1' || listing_id = '$featured_uoft_listings_2' || listing_id = '$featured_uoft_listings_3' || listing_id = '$featured_uoft_listings_4' || listing_id = '$featured_uoft_listings_5'";
				$result1 = mysql_query($query) or die(minor_error(216, $user, $user, $today, $query, mysql_error()));
			?>
				
					
			<?php for ($counter = 1 ; $row= mysql_fetch_array($result1) ; $counter++): ?>
			<?php
				// Extract pictures for the listing
				$picture_sql = "SELECT picture_id_1 FROM Listing_Pictures WHERE listing_id='" . $row[listing_id] . "'";
				$picture_result = mysql_query($picture_sql) or die (minor_error(217, $user, $user, $today, $picture_sql, mysql_error()));
				$picture_row = mysql_fetch_array($picture_result,MYSQL_ASSOC);
				$url_sql = "SELECT URL FROM Pictures_Lookup WHERE pictures_id='" . $picture_row[picture_id_1] . "'";
				$url_result = mysql_query($url_sql) or die (minor_error(218, $user, $user, $today, $url_sql, mysql_error()));
				$url_row = mysql_fetch_array($url_result,MYSQL_ASSOC);
			?>  
			
				<?php if ($counter == 5): ?>      
					<div id='featured_last'>
						<div class='featured_box'>
							<div class='service_pic2'>
								<a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
								<img src='<?php echo $domain_secondary . "resizer.php?src=". $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=158&w=158&zc=1"; ?>' height='158px' width='158px' alt=''/></a>
							</div>
							<div class='featured_service_info'>
								<div class='service_name'><a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'><?php echo $row[title]; ?></a></div>
							</div>
						</div> <!--end #featured_box-->
						<div class='box_shadow2'>
							<div class='landscape_shadow'><img src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' width='176px' height='10px' alt=''/></div>
						</div>
					</div><!--end #featured-->
					
				<?php else:?>
					<div class='featured2'>
						<div class='featured_box'>
							<div class='service_pic2'>
								<a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'>
								<img src='<?php echo $domain_secondary . "resizer.php?src=". $domain_secondary . "service_pictures/" . $url_row[URL] . "&h=158&w=158&zc=1"; ?>' height='158px' width='158px' alt=''/></a>
							</div>
							<div class='featured_service_info'>
								<div class='service_name'><a href='<?php echo $working_directory . "service/" . $row[listing_id] . "/"; ?>'><?php echo $row[title]; ?></a></div>
							</div>
						</div> <!--end #featured_box-->
						<div class='box_shadow2'>
							<div class='landscape_shadow'><img src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' width='176px' height='10px' alt=''/></div>
						</div>
					</div><!--end #featured-->
				
				<?php endif; //if ($counter == 5) ?>				
			<?php endfor; //for ($counter = 1 ; $row= mysql_fetch_array($result) ; $counter++) ?>
			<?php endif; //if (empty($var)): ?>
        </div> <!--end #featured_services_sect-->  
        <a id='see_more_link' href="<?php echo $working_directory;?>search.php"> See More </a>                
    </div> <!--end .content-->
	<?php include "html/footer.inc";  ?>  
</body>   	
</html>