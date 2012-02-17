<?php 
	// Program: footer.inc
	// Desc:	HTML based code for the footer bar of the site
	//

?>

<div id="footer_bg2">
  <div id='foot'>
	<div class='divider'> </div>
    <div id = "foot_left">
      <div id='foot_links'>
        <a class="footer" href="<?php echo $working_directory; ?>about-us/">About Us</a> 
        <a class="footer" href="javascript:UserVoice.showPopupWidget();">Contact Us</a>
        <a class="footer" href="<?php echo $working_directory; ?>FAQ">FAQ</a>
        <a class="footer" href="http://blog.gohoody.com/" target="_blank">Blog</a>
        <a class="footer" href="http://feedback.gohoody.com" target="_blank">Feedback</a>
      </div>   
      <div class="hoody_date">Hoody © 2011</div>
    </div> <!--end #left-->   
    
    <div id='foot_right'>
      <div class='twitter_section'>
         <a href='http://www.twitter.com/gohoody' target="_blank"><img class="social_icons" src="<?php echo $domain_secondary;?>attachements/twitter_icon_small.png" >
         </a>  
         <div class='twitter_wrapper'>
             <a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="GoHoody">Tweet</a>
            <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
         </div>
      </div>	
      
      <div class='gplus_section'>
      	<a id='gplus_fanpage' href="https://plus.google.com/b/105664755109508121432/?prsrc=3" style="text-decoration: none;"  target="_blank">
        	<img src="https://ssl.gstatic.com/images/icons/gplus-32.png" width="19" height="19" style="border: 0;"/>
        </a>
        <div id="google_plusone_footer"><g:plusone size="medium" href='http://gohoody.com'></g:plusone></div>     
      </div> 
          
      
      <div class='fb_link_section'>
        <a href='http://www.facebook.com/gohoody' target="_blank"><img class="social_icons fb_icon_small" src="<?php echo $domain_secondary;?>attachements/fb_icon_small.png" > 
        </a>
        <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fgohoody&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=210437142339472" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
            
      </div>
    </div> <!--end #right-->
  </div>
</div>

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
  <!-- Google +1 button - Google specified that this code need to implemented after the +1 button -->
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

