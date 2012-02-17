<?php
	include "featured_listings.inc";
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
		
	$page_title = "Hoody! | Find trusted services near you.";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="msvalidate.01" content="F1F40E573047DCAB94BF6B1EC90A818A" />

<!--SEO-->
<meta name="Description" content="Promote your service on a trusted Network. Create a custom page for your business and connect with your customers." />
<meta name="Keywords" content="Hoody, GoHoody, Trusted Services, Social Integration, Post a service, Local classifieds, Social classifieds"  />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta property="fb:admins" content="28130239" />
<link rel="icon" 
      type="image/png" 
      href="<?php echo $domain_secondary; ?>attachements/favicon.png" />
<title><?php echo $page_title; ?></title>

<!--Facebook meta properties -->
    <meta property="og:title" content="Hoody"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="http://gohoody.com"/>
    <meta property="og:image" content="<?php echo $domain_secondary; ?>attachements/home_page/header/hoodylogo.png"/>
    <meta property="og:site_name" content="Hoody"/>
    <meta property="fb:app_id" content="<?php echo $facebook_app_id; ?>"/>
    <meta property="og:description"
          content="Trusted local services"/>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/style_new.css" type="text/css"/>

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/title_bar_new.css" type="text/css" media="screen" />

<link rel="stylesheet" href="<?php echo $working_directory; ?>css/style2.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo $working_directory; ?>css/slider.css" type="text/css" media="screen" />

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic" rel="stylesheet" type="text/css">

<?php
$cache_expire = 60*60*24*365;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
?>
<script src="http://connect.facebook.net/en_US/all.js"></script>

<div id="fb-root"></div>
<script src="<?php echo $working_directory; ?>javascript/facebook_js.inc" type="text/javascript"></script>

<![if !IE]>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.min.js" type="text/javascript"></script>
<![endif]>

<!--[if gte IE 6]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js" type="text/javascript" charset="utf-8"></script> 
<![endif]-->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/jquery.easing.1.2.js"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery.capSlide.js" type="text/javascript"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery.anythingslider.js" type="text/javascript" charset="utf-8"></script>

<!--Parameters for the slider-->
<script type="text/javascript">
  function formatText(index, panel) {return index + "";};
  $(function () {
      $('.anythingSlider').anythingSlider({
          easing: "easeInOutQuad",        // Anything other than "linear" or "swing" requires the easing plugin
          autoPlay: true,                 // This turns off the entire FUNCTIONALY, not just if it starts running or not.
          delay: 15000,                   // How long between slide transitions in AutoPlay mode
          startStopped: false,            // If autoPlay is on, this can force it to start stopped
          animationTime: 500,             // How long the slide transition takes
          hashTags: true,                 // Should links change the hashtag in the URL?
          buildNavigation: true,          // If true, builds and list of anchor links to link to each slide
          pauseOnHover: true,             // If true, and autoPlay is enabled, the show will pause on hover
          startText: "Go",                // Start text
          stopText: "Stop",               // Stop text
          navigationFormatter: formatText // Details at the top of the file on this use (advanced use)
      });
      $("#slide-jump").click(function(){$('.anythingSlider').anythingSlider(6);});});
  function playvideo(){
	  $('#what_is_hoody').hide();
	  $('#play_button_cont').hide();
	  $('#video_obj').show();};
function hide_explaination(){
	$("#dimmer").hide();
	$("#fb_explaination").fadeOut();}
function show_explaination(){
	$('#video_obj').hide();
	$('#what_is_hoody').show();
	$('#play_button_cont').show();
	$("#dimmer").show();
	$("#fb_explaination").show();}
</script>
<!-- Google Analytics Social Button Tracking -->
<script type="text/javascript" src="<?php echo $working_directory; ?>javascript/ga_social_tracking.js"></script>
<!--Javascript Ends-->
</head>
<body <?php if ($development_status) echo 'onLoad="javascript:pageTracker._setVar(\'hoody-notrack\')"'; ?>>
	<div id='dimmer'> </div>
	<div id='fb_explaination'>    	
        <div id='explain_title'> Why connect with Facebook? </div>
        <div class='explain_text'> <big> Fast & Easy: </big>one-click login - no additional password to remember!</div>
        <div class='explain_text'> <big> Control: </big> you decide what activities to post to Facebook.</div>
		<div class='explain_text'> <big> Security: </big> Facebook cannot see the address info you provide on Hoody. </div>
        <div class='explain_text'> <big> Privacy: </big> we don't use your Facebook information for any reason other than improving your experience on Hoody. </div>
		<div class='explain_text2'> We'll be adding more ways to join Hoody! (Email, LinkedIn, Twitter, etc.) </div>        
        <a onclick="hide_explaination();" id='explain_button'> Ok, fine </a>
    </div>
	<div id="broswer_warning">Hoody is optimized for the latest IE, Firefox, Chrome, Safari and Opera.</div> 
    <div id="gap"> </div>
<?php 
		flush();
		include "html/title_bar_new2.inc";
?>    
  	<div class="main_wrapper">   
       	<div id='panels'>
          <?php if ($user): ?>
              <div id='left_panel'>
                  <div id='headline3'>Hi <?php echo $facebook_first_name?>! </div>
                  <div id='headline4'>Create a Hoody page to promote your service now:</div>
                  
                  <div id='post_section'>
                  	<div id='placeholder_text'> Name of your service </div>
                  	<form id="creaet" action="<?php echo $working_directory; ?>create/" method="post">
                    	<input id="post_service_input" name="service_title" type="text" placeholder="Name of your service"/>
                        <button type='submit' id='post_service_button' name="submit">Create Page</button>
                    </form>
                  </div>
                  <div id='suggestion_sect'>
                  	<div id='popular_text'> Trending services: </div>
                    <div id='popular_links'>
                    	<a class='popular_link' href='<?php echo $working_directory; ?>search.php?q=tutoring' > Tutoring </a>
                        <a class='popular_link' href='<?php echo $working_directory; ?>search.php?q=graphic+design' > Graphic Design </a>
                        <a class='popular_link' href='<?php echo $working_directory; ?>search.php?q=photography' > Photography </a>
                        <a class='popular_link' href='<?php echo $working_directory; ?>search.php?q=trainer' > Personal Training </a> <br />
                        <a class='popular_link' id='last_link' href='<?php echo $working_directory; ?>search.php?q=hair' > Hair Dressing </a> 
                    </div>   
                  </div>
              </div>  
          <?php else: ?>
              <div id='left_panel'>
                  <div id='headline1'>Make money doing<b> what you love! </b> </div>
                  <div id='headline2'>Sell services based on your skills and expertise. Get hired and make some money!</div>
                    <div id='button_cont'>
                    	<a id='sign_up_button' onclick='facebookLogin(); return false;'><div class='fb_logo_button1' ><img src='<?php echo $domain_secondary; ?>attachements/fb_logo.png' width='13px'/></div>Sign in with Facebook</a>
                    </div>
                    <a onclick="show_explaination();" id='why_facebook' > Why Facebook? </a>
              </div>
          <?php endif; //if ($user) ?> 
          <div id = 'right_panel'>
              <div id='video_sect'>    
                  <div id='video_box'>
                      <div id='play_button_cont'>
                          <a id='play_button' onclick='playvideo();'><img class='play_button' src='<?php $domain_secondary;?>attachements/home_page/play_button.png' alt='' width='100px'/> </a>
                      </div>
                      <div id='what_is_hoody'> What is Hoody? </div>
                      <div id='video_obj'>
                        <object style="height: 274px; width: 487px">
                          <param name="movie" value="http://www.youtube.com/v/hOYfKkg5dQw?version=3&autohide=1&autoplay=1&fs=1&rel=0&showinfo=0&feature=player_embedded">
                          <param name="allowFullScreen" value="true">
                          <param name="allowScriptAccess" value="always">
                          <embed src="http://www.youtube.com/v/OY1TH4mu3kM?version=3&autohide=1&autoplay=1&fs=1&rel=0&showinfo=0&feature=player_embedded" 
                          type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="487" height="274">
                        </object>
                      </div>
                  </div>
                  <img class='box_shadow' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p.png' alt=''/>
              </div> <!--end #video_sect--> 
          </div> <!--end #right_panel-->
        </div><!--end #panels-->
      </div> <!--end .main_wrapper-->
		
      <?php if (!$user): ?>   
      	
        
      <div id="how_sect_bg">
          <div id='how_it_works_sect'>
          
          	<div id='how_title'>
            	<img class='how_it_works_img' src='<?php echo $domain_secondary;?>attachements/how_it_works_text.png' alt='How it works'  />
            </div> <!--end#how_title-->
          
            <img class='box_shadow_steps2' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p4.png' alt=''/> 
          	<div id='how_steps'>
            
            	<div class='single_step first_step'>
                	
                    <div class='step_text first_text'>
                    	Think of things you're good at
                    </div>
                    
                    <img class='step_img step_thinking' src="<?php echo $domain_secondary;?>attachements/graphic_thinking.png" alt='Think!' />
                    
                    
                </div> <!--end #single_step-->
                
                <div class='next_arrow'>
                	<img class='next_arrow_img' src="<?php echo $domain_secondary;?>attachements/next_arrow.png" alt='>' />
                </div>
                
                <div class='single_step'>
                	
                    <div class='step_text creating_text'>
                    	Create services around your skills and expertise
                    </div>
                    
                    <img class='step_img step_creating' src="<?php echo $domain_secondary;?>attachements/graphic_creating.png" alt='Create!' />
                    
                </div> <!--end #single_step-->
                
                <div class='next_arrow'>
                	<img class='next_arrow_img' src="<?php echo $domain_secondary;?>attachements/next_arrow.png" alt='>' />
                </div>
                
                <div class='single_step'>
                	
                    <div class='step_text'>
                    	Promote yourself across social networks with Hoody
                    </div>
                    
                    <img class='step_img step_social' src="<?php echo $domain_secondary;?>attachements/graphic_social.png" alt='Promote!' />
                    
                </div> <!--end #single_step-->
                
                <div class='next_arrow'>
                	<img class='next_arrow_img' src="<?php echo $domain_secondary;?>attachements/next_arrow.png" alt='>' />
                </div>
                
                <div class='single_step'>
                	
                    <div class='step_text last_text'>
                    	Get hired and make some money!
                    </div>
                    
                    <img class='step_img step_money' src="<?php echo $domain_secondary;?>attachements/graphic_coins.png" alt='Money!' />
                    
                </div> <!--end #single_step-->
            
            	
                        	
            </div> <!--end#how_steps-->
          	<img class='box_shadow_steps' src='<?php echo $domain_secondary;?>attachements/home_page/picture_tiles/pic_shadow_p2.png' alt=''/>
            
            <a id='create_service_button' href='<?php echo $domain_secondary;?>create'>Start Selling &nbsp;<em>></em></a>
            
          </div> <!--end #how_it_works_sect-->
          
               
                
      </div> <!--end .how_sect_bg-->
      <?php endif; //if ($user) ?>   
      <div class="main_wrapper">
       	 <!--featured sellers-->        		
         <div id='featured_sellers_title'> Check out what others are offering on Hoody </div>    
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
            	<div class ='seller_pic'><a href='<?php echo $working_directory; ?>profile/george/'><img src='<?php echo $domain_secondary;?>attachements/UofT/george.jpg' width='138px'/></a></div>
                <div class='seller_name'><a href='<?php echo $working_directory; ?>profile/george/'>George Gan</a></div>
                <div class='seller_info'>A guy with a dream and passion for entrepreneurship.</div>
                <div class='indv_service'>
                	<a title='Special offer on GTA new condo projects' href='<?php echo $working_directory; ?>service/573/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1314988015-studio_condo_building.jpg&h=67&w=67&zc=1' width='67px'/>
                    </a>
                </div>
                <div class='indv_service'>
                	<a title='Condo rental service (Rental/Listing)' href='<?php echo $working_directory; ?>service/574/'>
                    	<img src='<?php echo $domain_secondary;?>resizer.php?src=<?php echo $domain_secondary;?>service_pictures/1314989637-rentitfast_662_1.jpg&h=67&w=67&zc=1' width='67px'/>
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
        
        <div id='ask_banner'>
        	<div id='tile_title'> Got a question? Just ask away! </div>
            <a  href="http://gohoody.com/ask"><img id='justask_logo' src="<?php echo $domain_secondary;?>attachements/justAsk_logo4.png" /></a>
            <div id='ask_sections'>
            	<div id='ask_title'> Ask about these topic:</div>
                <div class='section_box'>
                    <div class='pic_wrapper'>
                        <a class='pic_link' href='http://gohoody.com/ask/computer-and-electronics-gta/'>
                            <img src="<?php echo $domain_secondary;?>attachements/section_computer.jpg" />
                        </a>
                    </div>
                    <a class='sect_link_title' href='http://gohoody.com/ask/computer-and-electronics-gta/'> Computer and Electronics </a>
                </div> <!--end .section_box-->
                
                <div class='section_box'>
                    <div class='pic_wrapper'>
                        <a class='pic_link' href='http://gohoody.com/ask/graphic-design-and-web-development-gta/'>
                            <img src="<?php echo $domain_secondary;?>attachements/section_web.jpg" />
                        </a>
                    </div>
                    <a class='sect_link_title' href='http://gohoody.com/ask/graphic-design-and-web-development-gta/'> Graphic Design and Web </a>
                </div> <!--end .section_box-->
                
                <div class='section_box'>
                    <div class='pic_wrapper'>
                        <a class='pic_link' href='http://gohoody.com/ask/photography-gta/'>
                            <img src="<?php echo $domain_secondary;?>attachements/section_photography.jpg" />
                        </a>
                    </div>
                    <a class='sect_link_title' href='http://gohoody.com/ask/photography-gta/'> Photography </a>
                </div> <!--end .section_box-->
                <div class='section_box box_right'>
                    <div class='pic_wrapper'>
                        <a class='pic_link' href='http://gohoody.com/ask/pets-gta/'>
                            <img src="<?php echo $domain_secondary;?>attachements/section_pet.jpg" />
                        </a>
                    </div>
                    <a class='sect_link_title' href='http://gohoody.com/ask/pets-gta/'> Pets </a>
                </div> <!--end .section_box-->
                <a id='more_link' href="http://gohoody.com/ask"> More topics </a>
            </div>
        </div>
        
      </div> <!--end .main_wrapper-->  
      <div class="main_wrapper"><div class="push"></div></div> 
<?php include "html/footer.inc";  ?>  
</body>  

<script type="text/javascript">
    $(function() {
        $(".ic_container").capslide({
            caption_color	: '#fff',
            caption_bgcolor	: '#000',
            overlay_bgcolor : 'black',
            border			: '',
            showcaption	    : true});
			
			
	});	
</script>
</html>