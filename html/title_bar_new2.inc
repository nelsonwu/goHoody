<?php
	$address_sql = "SELECT area_code,street,lng,lat FROM User_Address WHERE fb_uid='$user'";
	$result = mysql_query($address_sql) or die ("Couldn't execute query - Error Code: A02");
	$lnglat_row = mysql_fetch_array($result,MYSQL_ASSOC);
	$user_area_code = $lnglat_row['area_code'];
	$user_street = $lnglat_row['street'];  
	
	$review_status_sql = "SELECT transaction_date FROM Confirmed_Transactions WHERE fb_uid='$user'&&transaction_date!='NULL'&&review_status='0'";
	$review_status_result = mysql_query($review_status_sql) or die ("Couldn't execute query - Error Code: A01");
    $imcomplete_reviews =mysql_num_rows ($review_status_result);

	
	$notification_outstanding_review = 0;
	$notification_imcomplete_address = 0;
	if ($user_area_code == NULL && $user_street == NULL && $user)
		$notification_imcomplete_address = 1;
	if ($imcomplete_reviews != 0)
		$notification_outstanding_review = 1;
?>
<link href="https://plus.google.com/b/105664755109508121432/" rel="publisher" />


<script type='text/javascript'>
   (function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) {return;}
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=210437142339472";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));	

  function msg_address() {
			  $.gritter.add({
				  title: 'Saved',
				  text: 'Your address info has been updated.',
				  image: '<?php echo $domain_secondary;?>attachements/check.png',
				  sticky: false,
				  time: '12000',
				  class_name: 'my-sticky-class'});}
  function msg_info() {
			  $.gritter.add({
				  title: 'Saved',
				  text: 'Your user info has been updated.',
				  image: '<?php echo $domain_secondary;?>attachements/check.png',
				  sticky: false,
				  time: '12000',
				  class_name: 'my-sticky-class'});}	
   function msg_incomplete_address() {
			  $.gritter.add({
				  title: 'Address info not completed',
				  text: 'Please fill in your address info in the <a class="note_link" href="http://gohoody.com/dashboard/">Dashboard</a> to post and find services in your area.',
				  image: '<?php echo $domain_secondary;?>attachements/warning2.png',
				  sticky: false,
				  time: '12000',
				  class_name: 'my-sticky-class'});}   
   function msg_review() {
			  $.gritter.add({
				  title: 'Review Pending',
				  text: 'You have a pending review. Please go to the <a class="note_link" href="http://gohoody.com/dashboard/">Dashboard</a> to leave a review',
				  image: '<?php echo $domain_secondary;?>attachements/warning2.png',
				  sticky: false,
				  time: '12000',
				  class_name: 'my-sticky-class'});} 
     
	 //browser_detection	
	var BrowserDetect = {
		init: function () {
			this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
			this.version = this.searchVersion(navigator.userAgent)
				|| this.searchVersion(navigator.appVersion)
				|| "an unknown version";
			this.OS = this.searchString(this.dataOS) || "an unknown OS";},
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
					return data[i].identity;}},
		searchVersion: function (dataString) {
			var index = dataString.indexOf(this.versionSearchString);
			if (index == -1) return;
			return parseFloat(dataString.substring(index+this.versionSearchString.length+1));},
		dataBrowser: [{
				string: navigator.userAgent,
				subString: "Chrome",
				identity: "Chrome"},{ 	
				string: navigator.userAgent,
				subString: "OmniWeb",
				versionSearch: "OmniWeb/",
				identity: "OmniWeb"},{
				string: navigator.vendor,
				subString: "Apple",
				identity: "Safari",
				versionSearch: "Version"},{
				prop: window.opera,
				identity: "Opera",
				versionSearch: "Version"},{
				string: navigator.vendor,
				subString: "iCab",
				identity: "iCab"},{
				string: navigator.vendor,
				subString: "KDE",
				identity: "Konqueror"},{
				string: navigator.userAgent,
				subString: "Firefox",
				identity: "Firefox"},{
				string: navigator.vendor,
				subString: "Camino",
				identity: "Camino"},{		
				// for newer Netscapes (6+)
				string: navigator.userAgent,
				subString: "Netscape",
				identity: "Netscape"},{
				string: navigator.userAgent,
				subString: "MSIE",
				identity: "Explorer",
				versionSearch: "MSIE"},{
				string: navigator.userAgent,
				subString: "Gecko",
				identity: "Mozilla",
				versionSearch: "rv"},{ 		
				// for older Netscapes (4-)
				string: navigator.userAgent,
				subString: "Mozilla",
				identity: "Netscape",
				versionSearch: "Mozilla"}],
		dataOS : [{
				string: navigator.platform,
				subString: "Win",
				identity: "Windows"},{
				string: navigator.platform,
				subString: "Mac",
				identity: "Mac"},{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"},{
				string: navigator.platform,
				subString: "Linux",
				identity: "Linux"}]};
	BrowserDetect.init();  
	  
	  //display browser warning
      if (BrowserDetect.browser == 'Explorer') {
		  if (BrowserDetect.version	>= 9 ) {
			  $('#broswer_warning').hide();
	 		  $('#gap').hide();}}
	  if (BrowserDetect.browser == 'Explorer') {
		  if (BrowserDetect.version	<= 8 ) {
			  $('#broswer_warning').hide();
			  document.write('<div id="broswer_warning2">Sorry, Hoody is not fully supported on this version of IE. Please use IE9 or the latest Firefox, Chrome, Safari, or Opera.</div> <div id="gap"> </div>');}}
	  if (BrowserDetect.browser == 'Firefox') {
		  if (BrowserDetect.version	>= 6) {
			  $('#broswer_warning').hide();
	 		  $('#gap').hide();}}
	  if (BrowserDetect.browser == 'Opera') {
		  if (BrowserDetect.version	>= 11) {
			  $('#broswer_warning').hide();
	 		  $('#gap').hide();}}
	  if (BrowserDetect.browser == 'Safari') {
		  if (BrowserDetect.version	>= 5) {
			  $('#broswer_warning').hide();
	 		  $('#gap').hide();}}
	  if (BrowserDetect.browser == 'Chrome') {
		  if (BrowserDetect.version	>= 11) {
			  $('#broswer_warning').hide();
	 		  $('#gap').hide();}}
	  if ((BrowserDetect.browser!='Chrome')&&(BrowserDetect.browser!='Firefox')&&(BrowserDetect.browser!='Opera')&&(BrowserDetect.browser!='Safari')&&(BrowserDetect.browser!='Explorer')){
		  if (BrowserDetect.version	< 11) {
			  $('#broswer_warning').hide();
			  document.write('<div id="broswer_warning2">Sorry, your broswer is not supported by Hoody. Please use the latest IE, Firefox, Chrome, Safari, or Opera.</div> <div id="gap"> </div>');}}
	  function expand_search() {
		 $("input.box").animate({
			width: '+=60',
		  }, {
			duration: 300,
			specialEasing: {
			  width: 'swing',},});}
	  function shrink_search() {
		 $("input.box").animate({
			width: '-=60',
		  }, {
			duration: 300,
			specialEasing: {
			  width: 'swing',},});}

 //Google Analytic Tracking Code
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23022233-1']);
  _gaq.push(['_setDomainName', '.gohoody.com']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();
	
 //Dropdown menu
  function show_dropdown() {
	  $("div#dropdown_menu").show();
	  $("a#arrow_down").hide();
	  $("a#arrow_up").show();};
  function hide_dropdown() {
	  $("div#dropdown_menu").fadeOut(200);
	  $("a#arrow_up").hide();
	  $("a#arrow_down").show();};
  function show_header_popup(){
	  $("#header_popup").fadeIn(100);};
  function hide_header_popup(){
	  $("#header_popup").fadeOut(200);};
  function show_popup(){
	  $("#popup_sect").fadeIn(100);};
  function hide_popup(){
	  $("#popup_sect").fadeOut(200);};
   $('html').click(function() {
	  hide_dropdown();
	  hide_header_popup();
	  hide_popup();});
</script>

<?php if ($development_status): ?>
<!--Info Bar to indicate if working in development-->
<div id='infobar'> dev.gohoody.com | Current version: <?php echo $version_number; ?> </div>
<?php endif; ?>

<!--Background width 100%-->
<div id="header_bg">
<div id="header">  
    <div id ="logo"><a href="<?php echo $working_directory;?>"><img id="logo_img" src="<?php echo $domain_secondary;?>attachements/hoody_logo_white.png" ></a></div>
   	<nav>
      <ul> 
          <li id = "create_listing_button">
            <a class="navlinks <?php if ($_SERVER[REQUEST_URI] == "/create/") echo 'button_active';?> " href='<?php echo $working_directory; ?>create/'> <img class='button_icon' src='<?php echo $domain_secondary;?>attachements/post_icon.png' id='post_icon'/>Sell a Service
            </a>            
          </li> 
          
          <li id = "view_listing_button" >
            <a class="navlinks <?php if (!strncmp($_SERVER[REQUEST_URI], "/service/", 8) && strlen($_SERVER[REQUEST_URI]) < 10) echo 'button_active';?>" href="<?php echo $working_directory; ?>service/">
            	<img class='button_icon' src='<?php echo $domain_secondary;?>attachements/browse_icon.png' id='browse_icon'/>
                Browse Services
            </a>
          </li>
          <li id = "just_ask_button" >
            <a class="navlinks <?php if (!strncmp($_SERVER[REQUEST_URI], "/ask/", 5) && strlen($_SERVER[REQUEST_URI]) < 6) echo 'button_active';?>" href="<?php echo $working_directory; ?>ask/">
            	<img class='button_icon' src='<?php echo $domain_secondary;?>attachements/question_icon.png' id='question_icon'/>
                Just Ask
            </a>
          </li>
         
          <li id = "search_bar">
                                     
                  <form method="get" id="searchform" action="<?php echo $working_directory; ?>search.php">
                    <fieldset>
                      <div class='search_box'>
                        <input id="search_field" type="text" class="box" name="q" onfocus='event.stopPropagation(); expand_search();' onblur='event.stopPropagation(); shrink_search();' value="<?php echo $var; ?>" placeholder="Search services"/>     
                        <div id='search_icon_box' ><img src='<?php echo $domain_secondary;?>attachements/search_icon.png' id='search_icon'/></div>
                      </div>  
                     </fieldset>
                  </form>               
          </li> 
      </ul>    
      
	  <?php if ($user): ?>
        <div id = 'user_sect'> 
            <div id='user_pic_box'><img id='user_pic_img' src='<?php echo $facebook_pic_square; ?>' alt='' /></div>
            <a onclick='event.stopPropagation(); show_dropdown();' class='dropdown_arrow' id = 'arrow_down'>
                <img class='drop_down' src='<?php echo $domain_secondary;?>attachements/triangledown2.png'>
            </a>
            <a onclick='event.stopPropagation(); hide_dropdown();' class='dropdown_arrow' id = 'arrow_up'>
                <img class='go_up' src='<?php echo $domain_secondary;?>attachements/triangleup2.png'>
            </a>
            
            <?php
				//Loop up the user profile name of the sender
				$user_lookup_sql = "SELECT profile_name FROM User_Lookup WHERE fb_uid='$user'";
				$result = mysql_query($user_lookup_sql) or die (error_page(1201));
				$row4 = mysql_fetch_array($result,MYSQL_ASSOC);
				$user_profile_name = $row4['profile_name'];
			?>      
			<div id='dropdown_menu'>
            	<div class='arrowup'><img class='arrowup_img' src='<?php echo $domain_secondary;?>attachements/arrowup.png'></div>
				<div class='dropdown_item' id='dropdown_name'><?php echo $facebook_first_name; ?></div>
				<a class='dropdown_item_link' id='dropdown_dashboard' href='<?php echo $working_directory . "dashboard/" ?>'> My Dashboard </a>
				<a class='dropdown_item_link' id='dropdown_profile' 
				href='<?php echo $working_directory . "profile/" . $user_profile_name . "/"; ?>'>My Public Profile </a>
				<a class='dropdown_item_link' id='logout_link' onclick='facebookLogout(); return false;' > Logout</a>	
			</div>
        </div>
    	
    <?php else: ?>
        <div id ='login_button'>
            <a class='login_button_link' onclick='facebookLogin(); return false;'  >
            	<img class='fb_logo_button' src='<?php echo $domain_secondary;?>attachements/fb_logo.png' width='11px'/>Sign in
            </a>
        </div>
    <?php endif; //if ($user) ?>	
    
  </nav>
  
  <div id='header_popup'>
    <img class='header_popup_arrow' src="<?php echo $domain_secondary;?>attachements/popup_arrow_up.png"  />
	<div class='pu_title' id='header_pu_title'> Please login to sell a service </div>
    <div class='pu_content'>
        <div class='pu_button'>
            <a class='login_button_link pu_link' onclick='facebookLogin(); return false;'>
            	<img class='fb_logo_button' src='<?php echo $domain_secondary;?>attachements/fb_logo.png' width='11px'/>Login
            </a>
        </div>
    </div>
  </div> <!--end #header_popup-->
</div> <!--end of #header-->
</div> <!--end of #header_bg-->
