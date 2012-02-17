<?php
	//Connect to Hoody MySQL database
	include "php/misc.inc";	
	include "php/hoody_functions.php";	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	$popup_title = '';
	$lid = (int) $_GET['lid'];
	$action = (int) $_GET['action'];
	$fb_uid= (int) $_GET['fb_uid'];
	$user_uid = (int) $_GET['user_uid'];
	
	if(empty($user_uid))
		$popup_title = "Oops! Not so fast! <br /> <div id='contact_service_name'> Please sign in to contact the seller. </div>";
		/*die("Oops! Not so fast! <br /> <div id='contact_service_name'> Please sign in to contact seller. </div>");*/
	
	$today = date("Y-m-d H:i:s"); 
	
	$user_sql = "SELECT name,pic_square FROM Basic_User_Information WHERE fb_uid='$user_uid'";
	$result3 = mysql_query($user_sql) or die (fatal_error(253, $user, $user, $today, $user_sql, mysql_error()));
	$row = mysql_fetch_array($result3,MYSQL_ASSOC);
	$user_pic_square = $row['pic_square'];
	$user_name = $row['name'];

		
	// Action:
	// 1 --> contact seller (Service Description + User Dashboard)
	// 2 --> contact customer (User Dashboard)
	// 3 --> Thank you email (User Dashboard)
	// 4 --> report listing abuse (Service Description + User Dashboard)
	// 5 --> report user abuse (User Dashboard)
	// 7 --> Name my own price
		
	if ($action == 1 && !empty($user_uid))
	{
		$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
		$result = mysql_query($query) or die (fatal_error(47, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$popup_title = "Contact seller <br /> <div id='contact_service_name'> " . $row['title'] . "</div>";
	}
	else if ($action == 2 && !empty($user_uid))
	{
		$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
		$result = mysql_query($query) or die (fatal_error(48, $user, $user, $today, $query, mysql_error()));
		$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
		$buyer_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
		$result3 = mysql_query($buyer_sql) or die (fatal_error(49, $user, $user, $today, $buyer_sql, mysql_error()));
		$row2 = mysql_fetch_array($result3,MYSQL_ASSOC);
		$popup_title = "Contact " . $row2['name'] . " <br /> <div id='contact_service_name'> " . $row1['title']. "</div>" ;
	}
	else if ($action == 3 && !empty($user_uid))
	{
		$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
		$result = mysql_query($query) or die (fatal_error(50, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$buyer_sql = "SELECT name FROM Basic_User_Information WHERE fb_uid='$fb_uid'";
		$result2 = mysql_query($buyer_sql) or die (fatal_error(51, $user, $user, $today, $buyer_sql, mysql_error()));
		$row2 = mysql_fetch_array($result2,MYSQL_ASSOC);
		$popup_title = 'Thanks for using my service!';
		
		// Get Review ID (same as contact_id)
		$query = "SELECT contact_id FROM Contact_History WHERE fb_uid='$fb_uid'&&listing_id='$lid'";
		$result3 = mysql_query($query) or die (fatal_error(52, $user, $user, $today, $query, mysql_error()));
		$review_id_row = mysql_fetch_array($result3,MYSQL_ASSOC);
		$review_id = $review_id_row['contact_id'];
	}
	else if (($action == 4 || $action == 5) && !empty($user_uid))
		header("Location: popup_actions.php?uid=$user&lid=$lid&action=$action&fb_uid=$fb_uid");
	else if ($action == 7 && !empty($user_uid))
	{
		$query = "SELECT title FROM Listing_Overview WHERE listing_id=$lid";
		$result = mysql_query($query) or die (fatal_error(47, $user, $user, $today, $query, mysql_error()));
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$popup_title = "Name My Own Price <br /> <div id='contact_service_name'> " . $row['title'] . "</div>";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Contact</title>
<style type="text/css">
	#contact_pu_title{
		font-family: 'Open Sans', Arial, Helvetica, sans-serif;
		font-size: 30px;
		font-weight: bold;
		padding-bottom: 15px;
		color: #666666;
		border-bottom: #EEEEEE 1px solid;
		margin-bottom: 20px;
		clear: both;
		overflow:hidden;}
	#contact_service_name{
		font-family: Arial, Helvetica, sans-serif;
		font-size: 18px;
		font-weight: bold;
		color:#f05b28;
		margin: 5px auto auto 3px;}
	#contact_pu_sender{
		width: 70px;
		float:left;}
		.pu_user_pic{
			width: 60px;
			padding: 5px;
			background-color:#FFF;
			border:#CCCCCC 1px solid;
			-moz-box-shadow: 2px 2px 7px #c9c4bd;
			-webkit-box-shadow: 2px 2px 7px #c9c4bd;
			box-shadow: 2px 2px 7px #c9c4bd;
			border-radius:5px;
			-moz-border-radius:5px;
			-webkit-border-radius:5px;}
		.pu_user_name{
			font-family: 'Open Sans', Arial, Helvetica, sans-serif;
			font-size: 14px;
			padding-top: 5px;
			text-align: center;}
		#pu_point{
			position: absolute;
			margin: -75px auto auto 85px;}
			
	#regarding{
		float:left;
		padding-top: 4px;
		margin-left: 0px;
		margin-right: 10px;
		font-family: 'Open Sans', Arial, Helvetica, sans-serif;
		font-size: 12px;
		color:#f05b28;
	}
	
	#topic_selector{
		float:left;
	}
	
	
	
	#contact_pu_textarea{
		width: 340px;
		height: 350px;
		font-family: 'Open Sans', Arial, Helvetica, sans-serif;
		font-size: 14px;
		padding: 10px;
		border: #CCCCCC 1px solid;
		float: right;
		resize: none;
		-webkit-transition: all 300ms ; 
		-moz-transition: all 300ms; 
		-ms-transition: all 300ms; 
		-o-transition: all 300ms; 
		transition: all 300ms; /* custom */}
	
		#contact_pu_textarea:focus{
			outline:none;
			-moz-box-shadow: 2px 2px 7px #c9c4bd;
			-webkit-box-shadow: 2px 2px 7px #c9c4bd;
			box-shadow: 2px 2px 7px #c9c4bd;
			-webkit-transition: all 300ms ; 
			-moz-transition: all 200ms; 
			-ms-transition: all 200ms; 
			-o-transition: all 200ms; 
			transition: all 200ms; /* custom */
		}
		
	#button47{
		float:right;
		margin: 0;
		margin-top: 10px;
		margin-left:90px;
		border:0;
		border-radius:5px;
		-moz-border-radius:5px;
		-webkit-border-radius:5px;
		background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #43be55), color-stop(1, #13a74e) );
		background:-moz-linear-gradient( center top, #43be55 5%, #13a74e 100% );	
		background-image:      -o-linear-gradient(top, #43be55, #13a74e);
		background-color:#43be55;
		-moz-box-shadow: 1px 1px 2px 0px #c9c4bd;
		-webkit-box-shadow: 1px 1px 2px 0px #c9c4bd;
		box-shadow: 1px 1px 2px 0px #c9c4bd;
		border-bottom:2px solid #6f7072;
		display:inline-block;
		color:#FFFFFF;
		font-family: Arial, san-serif;
		font-size:15px;
		font-weight:bold;
		padding: 7px 20px;
		text-decoration:none;
		cursor:pointer;}
		#button47:hover{
			border-bottom-width: 4px;}
		#button47:active{
			position:relative;
			top: 2px;
			border-bottom-width: 2px;}
	#spinner_img {
		display: none;
		position: absolute;
		margin: 384px auto auto 195px;}
		
		#dollar_sign{
			width: 15px;
			float:left;
			font-family: 'Open Sans', Arial, Helvetica, sans-serif;
			font-size: 20px;
			font-weight: bold;
			padding-bottom: 15px;
			color: #666666;
			
			margin-top:17px;
			
		}
		.desc_input{
			padding: 5px;
			width: 100px;
			margin-left: 5px;
			margin-top: 15px;
			float:left;
			font-family: 'Open Sans', Arial, Helvetica, sans-serif;
			font-size:13px;
		}
		
</style>
<script src="<?php echo $working_directory; ?>javascript/jquery-1.5.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>

<?php flush(); ?>

<div id="popupWindow">
<form name = "popupFormAction" action='popup_actions.php?uid=<?php echo $user . "&lid=" . $lid . "&action=" . $action . "&fb_uid=" . $fb_uid . "&user_uid=" . $user_uid; ?>' method='POST'>
	<div id='contact_pu_title'> 
		
		<?php echo $popup_title; ?>  
    	
        <!--contact seller in general-->
        <?php if ($action == 1 && $lid==0): ?>
    	
            <div id='regarding'> Regarding: </div>
            <select id='topic_selector' name="lid">
                
                <option value="0">General Question</option>
        <?php
            // Get data for seller's other listings
            $query = "SELECT listing_id,title FROM Listing_Overview WHERE fb_uid=$fb_uid&&status=1 ORDER BY listing_id";
            $other_listing_result = mysql_query($query) or die (minor_error(53, $user, $user, $today, $name_lookup_sql, mysql_error()));
            $other_listing_num = mysql_num_rows($other_listing_result);
        ?>
        <?php if ($other_listing_num > 0): ?>
        <?php while($other_listing_row = mysql_fetch_array($other_listing_result)): ?>
                <option value="<?php echo $other_listing_row[listing_id]; ?>" ><?php echo $other_listing_row[title]; ?></option>
        <?php endwhile; //while($other_listing_row = mysql_fetch_array($other_listing_result)) ?>
        <?php endif; //if ($other_listing_num > 0) ?>
            </select>
        <?php endif; //if ($action == 1 && $lid==0) ?>
    
    
    </div> <!--end #contact_pu_title -->
    
    
    
<?php if(empty($user_uid))
		die();
?>    
    
<?php if ($action == 7): // Name my own price?>
    	<p id="dollar_sign">$</p>  
        	<input type="text" id="price" name="price" class="desc_input"  maxlength="8" />
            <div id='pu_submit'>
            	<input type="submit" name="contact" id="button47" value="Submit" >
            	<img id='spinner_img' src='<?php echo $working_directory; ?>css/images/loading.gif' /> 
            </div> 
<?php else: ?>    

   
    
    <div id='contact_pu_sender'>
    	<img class='pu_user_pic' src='<?php echo $user_pic_square; ?>' /> 
    	<div class='pu_user_name'> <?php echo $user_name; ?> </div>
        <img id='pu_point' src='<?php echo $domain_secondary;?>attachements/triangle_left.png' />
    </div>
    
    
    
    <textarea  name="contact_seller" id="contact_pu_textarea"><?php if ($action==3): ?><?php echo $row2['name']; ?>, I want to thank you for using my service on Hoody. Please kindly review my service by going to: http://gohoody.com/review/<?php echo $review_id; ?>                Thank you! <?php endif; //if ($action==3) ?></textarea>
	<div id='pu_submit'>
    	<input type="submit" name="contact" id="button47" value="Send message" onclick="$('#spinner_img').fadeIn();">
    	<img id='spinner_img' src='<?php echo $working_directory; ?>css/images/loading.gif' /> 
    </div> 
    
<?php endif; // if ($action == 7) ?>    
    
  </form>
</div>

</body>
</html>