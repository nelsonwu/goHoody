<?php
	// Program: review_popup.php
	//
			
	//Connect to @Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	$user_uid = (int) $_GET['user_uid'];
	
	if(empty($user_uid))
		die("please make sure you are logged into Hoody");
	
	$today = date("Y-m-d H:i:s"); 
	
	if (!empty($user_uid) && isset($_GET['rid'])) 
	{
		//Typecast it to an integer:
		$rid = (int) $_GET['rid'];
		//An invalid $_GET['rid'] value would be typecast to 0
		
		//$lid must have a valid value
		if ($rid > 0) 
		{			
			//Get the information from the database for this review:
			$query = "SELECT * FROM Confirmed_Transactions WHERE transaction_id='$rid'&&fb_uid='$user_uid'";
			$result = mysql_query($query) or die (fatal_error(130, $user, $user_uid, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			$database_rating = $rating;
			$database_review = $review;
			
			$query = "SELECT title,fb_uid,listing_description FROM Listing_Overview WHERE listing_id='$listing_id'";
			$result = mysql_query($query) or die (fatal_error(131, $user, $user_uid, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
			
			$query = "SELECT name,pic_big FROM Basic_User_Information WHERE fb_uid='$user_uid'";
			$result = mysql_query($query) or die (fatal_error(132, $user, $user_uid, $today, $query, mysql_error()));
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			extract($row);
								
		} // End of if ($lid > 0)
	} // End of if ($user && isset($_GET['rid']))
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Service Review</title>
<link rel="stylesheet" href="css/general.css" type="text/css" media="screen" />
<style type="text/css">
body {
	
	
	font-size: 12px;
	
	margin-top:10px;
	margin-left: 10px;
	margin-right: 0px;
	font-family: 'Open Sans', Arial, san-serif;
	
	z-index:-998;
}

.title{
	font-family: 'Open Sans', Arial, san-serif;
}


	
	
#page_title{
		font-family: 'Open Sans', Arial, Helvetica, sans-serif;
		font-size: 30px;
		font-weight: bold;
		padding-bottom: 15px;
		color: #666666;
		border-bottom: #EEEEEE 1px solid;
		margin-bottom: 20px;
		clear: both;
		overflow:hidden;
}
	.title_2{
		color:#f05b28;
		margin-top: 5px;
		font-size: 19px;
		
	}
	
	.for_text{
		color: #666666;
		font-size: 20px;
	}
	.title_1{
		
		
	}
		
	.profile_link{
		text-decoration:none;
		color:#f05b28;
		-webkit-transition: all 300ms ; 
		-moz-transition: all 300ms; 
		-ms-transition: all 300ms; 
		-o-transition: all 300ms; 
		transition: all 300ms; /* custom */
	}
	
		.profile_link:hover{
			text-decoration:none;
			color:#ff8933;
			-webkit-transition: all 200ms ; 
			-moz-transition: all 200ms; 
			-ms-transition: all 200ms; 
			-o-transition: all 200ms; 
			transition: all 200ms; /* custom */
		}
		.profile_link:active{
			text-decoration:none;	
		}
	

	
	#radio_ui{
		margin-bottom:25px;
	}
	label{
		font-size:12px;
	}
	
	#checkbox_yes{
		pointer-events: none;
		position: absolute;
		margin: -22px auto auto 10px;
		
	}
		.box_checked_yes{
			display: none;
		}
	#checkbox_no{
		pointer-events: none;
		position: absolute;
		margin: -22px auto auto 135px;
		
	}
	
	
		.box_checked_no{
			display: none;
		}
	
	
	
	
	.title2{
		font-weight:bold;
		margin-bottom: 10px;
		margin-top: 10px;
	}
	
	#comments{
		
		float:left;
		width: 390px;
		height: 180px;
		font-family: 'Open Sans', Arial, Helvetica, sans-serif;
		font-size: 14px;
		padding: 10px;
		border: #CCCCCC 1px solid;
		
		resize: none;
		-webkit-transition: all 300ms ; 
		-moz-transition: all 300ms; 
		-ms-transition: all 300ms; 
		-o-transition: all 300ms; 
		transition: all 300ms; /* custom */}
	
		#comments:focus{
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
		
	#input_sect{
		width: 100%;
		margin-top: 15px;
		float:left;
	}
	
	#post_to_fb{
		width: 80%;
		margin-top: 15px;
		float:left;
	}
	
	#submit_button{
		
		float:left;
	
		
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
		#submit_button:hover{
			border-bottom-width: 4px;}
		#submit_button:active{
			position:relative;
			top: 2px;
			border-bottom-width: 2px;}
	  
		
</style>
<link rel="stylesheet" type="text/css" href="<?php echo $working_directory; ?>css/jquery-ui-1.8.16.custom.css" />


<div id="fb-root"></div>
<script src="javascript/facebook_js.inc" type="text/javascript"></script>
<script src="javascript/jquery-1.5.min.js" type="text/javascript"></script>
<script src="javascript/animation.js" type="text/javascript"></script>
<script src="<?php echo $working_directory; ?>javascript/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">

  $(function() {
	  $( "#radio_ui" ).buttonset();
	  
	  if ($("#radio1:checked").val() == '1') {
		  $(".box_unchecked_yes").hide();
		  $(".box_checked_yes").show();
		  
		  $(".box_checked_no").hide();
		  $(".box_unchecked_no").show();
			  
	  }
	  if ($("#radio2:checked").val() == '0') {
		  $(".box_checked_yes").hide();
		  $(".box_unchecked_yes").show();
		  
		  $(".box_unchecked_no").hide();
		  $(".box_checked_no").show();
	  };
	  
	  $("input.use_again").click(function(){
	
	
		  if ($("#radio1:checked").val() == '1') {
			  $(".box_unchecked_yes").hide();
			  $(".box_checked_yes").show();
			  
			  $(".box_checked_no").hide();
			  $(".box_unchecked_no").show();
				  
		  }
		  else{
			  $(".box_checked_yes").hide();
			  $(".box_unchecked_yes").show();
			  
			  $(".box_unchecked_no").hide();
			  $(".box_checked_no").show();
		  };
	  
	  });
	  
  });
  
  
  
</script>
</head>
<body>

<?php 
	flush();
?>

	<div id='page_title'>
                
	
   					 <?php if ($review_status == 1): ?>
                   		<div class='title_1'>Update your review </div>
                    <?php elseif ($review_status == 2): ?>
                   		<div class='title_1'>Your review has been submitted </div>
                    <?php else: ?>
                		<div class='title_1'>Submit a review </div>
                    <?php endif; //if ($review_status == 1) ?>
    
    
    

        <div class='title_2'><a class='profile_link' href="<?php echo $working_directory . "service/" . $listing_id . "/"; ?>"><?php echo $title ?></a></div>
        
    </div> <!--end #page_title-->  	
        
    
    
    <form method='POST' action="popup_actions.php?action=6&rid=<?php echo $rid . "&user_uid=" . $user_uid ?>">
        <div class='title2'>Would you use this service again?</div>
        
        <div id="radio_ui">
          <input type="radio" name="rating" value="1" class='use_again' id='radio1' <?php if($database_rating == 1) { echo "checked"; } ?> /> 
            <label for="radio1">&nbsp;&nbsp;&nbsp;&nbsp;Yes I would </label> 
            <div id='checkbox_yes'>
              <img class='box_checked_yes' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
              <img class='box_unchecked_yes' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
            </div>
          <input type="radio" name="rating" value="0" class='use_again' id='radio2' <?php if($database_rating == 0 && $review_status == 1) { echo "checked"; } ?>/> 					  	<label for="radio2">&nbsp;&nbsp;&nbsp;&nbsp;No I would not  </label>	
            <div id='checkbox_no'>
                <img class='box_checked_no' src="<?php echo $domain_secondary;?>attachements/radio_check.png" />
                <img class='box_unchecked_no' src="<?php echo $domain_secondary;?>attachements/box_uncheck.png" />
            </div>
        </div>  	
        
        <div class='title2'>Additional Comments</div>
        <div><textarea id='comments' name="review"><?php if($_POST['review'] == "") { echo "$database_review"; } ?><?php echo $_POST['review']; ?></textarea></div>        
		<div id='post_to_fb'><input type="checkbox" name="facebook" value="1" checked /> Post to my Facebook wall </div>
		<?php if ($review_status == 1): ?>
            <div id='input_sect'><input id='submit_button' type="submit" value="Modify"></div>
        <?php else: ?>
            <div id='input_sect'><input id='submit_button' type="submit" value="Submit"></div>
        <?php endif; //if ($review_status == 1) ?>
        
        
    </form>



</body>
</html>