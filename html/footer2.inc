<?php 
	// Program: footer.inc
	// Desc:	HTML based code for the footer bar of the site
	//

?>



<div id="footer_bg2">

  <div id='foot'>
     
    <a href='<?php echo $working_directory; ?>'><img id="logo_footer" src="<?php echo $domain_secondary;?>attachements/logo_border.png" > </a>
    <div class="hoody_date">Hoody © 2011</div>    
      
    
    <div id='foot_right'>
    
     
          
          <div class='twitter_section'>
          	 <a href='http://www.twitter.com/gohoody' target="_blank"><img class="social_icons" src="<?php echo $domain_secondary;?>attachements/twitter_icon_small.png" >
             </a>  
             <div class='twitter_wrapper'>
            	 <a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="GoHoody">Tweet</a>
			 	<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
             </div>
          </div>	
          <div class='fb_link_section'>
           	<a href='http://www.facebook.com/gohoody' target="_blank"><img class="social_icons fb_icon_small" src="<?php echo $domain_secondary;?>attachements/fb_icon_small.png" > 
            </a>
            <div class="fb-like" data-href="http://www.facebook.com/gohoody" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="lucida grande"></div>
            	
          </div>
      
      
      
    </div> <!--end #right-->
    
    <div id = "foot_left">
      <div id='foot_links'>
        <a class="footer" href="<?php echo $working_directory; ?>about-us/">About Us</a> 
        <a class="footer" href="javascript:UserVoice.showPopupWidget();">Contact Us</a>
        <a class="footer" href="<?php echo $working_directory; ?>FAQ">FAQ</a>
        <a class="footer" href="http://blog.gohoody.com/" target="_blank">Blog</a>
        <a class="footer" href="http://feedback.gohoody.com" target="_blank">Feedback</a>
      </div>   
      
    </div> <!--end #left--> 
    
  </div> <!--end #foot-->
</div> <!--end #footer_bg2-->


<?php if($update_status_address == 1): ?>
<script type='text/javascript'>
	jQuery(document).ready(function($){
		msg_address();
	});
</script>
<?php endif; ?>
<?php if($update_status_misc == 1): ?>
<script type='text/javascript'>
	jQuery(document).ready(function($){
		msg_info();
	});
</script>
<?php endif;?>
<?php if($notification_imcomplete_address == 1): ?>
<script type='text/javascript'>
	jQuery(document).ready(function($){
		msg_incomplete_address();
	});
</script>
<?php endif; ?>
<?php if($notification_outstanding_review == 1): ?>
<script type='text/javascript'>
	jQuery(document).ready(function($){
		msg_review();
	});
</script>
<?php endif; ?>

<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/Ve1ABV1QkaUqDwjRVusw.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>