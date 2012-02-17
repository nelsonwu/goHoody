<?php
	// Program: service_description.php
	//
	// Error Code Range: 1000 - 1099	
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");

	//Link to Facebook PHP SDK
    include "php/fbmain.php";
	include "php/fb_status.inc";
	
	if ($user != 28130239 && $user != 28119534)
		header("Location: index.php");

	// Save updated address info back to database
	// mysql_real_escape_string implementation to prevent SQL injection attack
	// Also convert the address into lng/lat geocode, and save it back to the databse
	if ($_POST[delete] != "")
	{
		$delete_sql = "UPDATE Listing_Overview SET status=2 WHERE listing_id='$_POST[delete]'";
		$delete_result = mysql_query($delete_sql) or die (error_page(1000));
	} //End of if

	$query = "SELECT * FROM Basic_User_Information";
	$result = mysql_query($query) or die (error_page(1001));
	$num_users = mysql_num_rows($result);	
	
	$query = "SELECT * FROM Listing_Overview";
	$result = mysql_query($query) or die (error_page(1002));
	$num_listing = mysql_num_rows($result);
	
	$query = "SELECT * FROM Listing_Overview WHERE status=0";
	$result = mysql_query($query) or die (error_page(1003));
	$num_inactive_listing = mysql_num_rows($result);	
	
	$query = "SELECT * FROM Listing_Overview WHERE status=1";
	$result = mysql_query($query) or die (error_page(1004));
	$num_active_listing = mysql_num_rows($result);	
	
	$query = "SELECT * FROM Listing_Overview WHERE status=2";
	$result = mysql_query($query) or die (error_page(1005));
	$num_deleted_listing = mysql_num_rows($result);	
	
	$query = "SELECT * FROM Listing_Overview WHERE status=3";
	$result = mysql_query($query) or die (error_page(1006));
	$num_notsubmitted_listing = mysql_num_rows($result);
	
	$query = "SELECT SUM(price) AS total_price FROM Listing_Overview WHERE status=1";
	$result = mysql_query($query) or die (error_page(1007));
	$total_price_stack =mysql_fetch_assoc($result);
	$total_price = $total_price_stack['total_price'];  
	$average_price = $total_price / $num_active_listing;
	
	$query = "SELECT * FROM Pictures_Lookup";
	$result = mysql_query($query) or die (error_page(1008));
	$num_pics = mysql_num_rows($result);
	
	$query = "SELECT * FROM Confirmed_Transactions WHERE transaction_date != 'NULL'";
	$result = mysql_query($query) or die (error_page(1009));
	$num_transactions = mysql_num_rows($result);		
	
	$query = "SELECT * FROM Confirmed_Transactions WHERE review_status = 1";
	$result = mysql_query($query) or die (error_page(1010));
	$num_completed_review = mysql_num_rows($result);		
	
	$query = "SELECT * FROM Confirmed_Transactions WHERE rating = 1";
	$result = mysql_query($query) or die (error_page(1011));
	$num_positive_ratings = mysql_num_rows($result);
	
	$query = "SELECT * FROM Confirmed_Transactions WHERE rating = 0";
	$result = mysql_query($query) or die (error_page(1012));
	$num_negative_ratings = mysql_num_rows($result);		
	
	$query = "SELECT * FROM Confirmed_Transactions WHERE review != 'NULL'";
	$result = mysql_query($query) or die (error_page(1013));
	$num_written_reviews = mysql_num_rows($result);		
	
	$query = "SELECT * FROM Contact_History";
	$result = mysql_query($query) or die (error_page(1014));
	$num_contacts = mysql_num_rows($result);

	$average_pic_per_listing = $num_pics / $num_listing;	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="icon" 
      type="image/png" 
      href="http://www.athoody.com/attachements/favicon.png">
<title>Vault</title>

<link rel="stylesheet" href="css/service_description.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/title_bar.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/slidingtabs-vertical.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" /><![endif]-->

<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=true&region=CA">
</script>
<div id="fb-root"></div>
<script src="javascript/facebook_js.inc" type="text/javascript"></script>

<script src="javascript/jquery-1.5.min.js" type="text/javascript"></script>
<script src="javascript/animation.js" type="text/javascript"></script>

<!--Javascript for tabs START-->

<script type="text/javascript" src="javascript/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="javascript/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="javascript/jquery.slidingtabs.pack.js"></script>    
<script type="text/javascript">  
$(document).ready(function() {  				  		
	
	// Vertical Sliding Tabs demo
	$('div#st_vertical').slideTabs({
		// Options  			
		orientation: 'vertical',  			
		slideLength: 300, // Height of the div.st_v_tabs_container element -minus the directional button's height (37px)			
		contentAnim: 'slideH',			
		contentEasing: 'easeInOutExpo',
		tabsAnimTime: 300,
		contentAnimTime: 600
	});		

});
</script>   

<!--Javascript for tabs END--> 

<!--Javascript for picturebox-->
<script type="text/javascript" src="javascript/jquery.lightbox.js"></script>

</head>
<body>

<?php 
	flush(); 
	include "html/title_bar_new2.inc"; 
?>
    
<?php
		
		
		echo	"<p>&nbsp;</p><p>&nbsp;</p>
				<h2 align=\"center\">Numbers of users: $num_users</h2>
				<br />
				<h2 align=\"center\">Numbers of All the Listings: $num_listing</h2>
				<h4 align=\"center\"><p>Inactive Listings: $num_inactive_listing</p>
				<p>Active Listings: $num_active_listing</p>
				<p>Deleted Listings: $num_deleted_listing</p>
				<p>Not Submitted Listings: $num_notsubmitted_listing</p>
				<p>Average Selling Price (Active Listings): \$$average_price</p>
				<p><img src=\"https://chart.googleapis.com/chart?cht=p3&chd=t:
				$num_inactive_listing,$num_active_listing,$num_deleted_listing,$num_notsubmitted_listing
				&chs=800x200&chl=Inactive Listing|Active Listing|Deleted Listing|Not Submmitted Listing&chf=bg,s,f5f5f5\" /></p></h4>
				<br />
				<h2 align=\"center\">Numbers of Pictures: $num_pics</h2>
				<h4 align=\"center\">Average Pictures Per Listing: $average_pic_per_listing</h4>
				<br />
				<h2 align=\"center\">Numbers of Confirmed Transactions: $num_transactions</h2>
				<h4 align=\"center\"><p>Completed Reviews: $num_completed_review</p>
				<p>Positive Ratings: $num_positive_ratings</p>
				<p>Negative Ratings: $num_negative_ratings</p>
				<p>Written Reviews: $num_written_reviews</p>
				<p><img src=\"https://chart.googleapis.com/chart?cht=p3&chd=t:
				$num_positive_ratings,$num_negative_ratings
				&chs=800x200&chl=Positive Ratings|Negative Ratings&chf=bg,s,f5f5f5\" /></p></h4>
				<br />
				<h2 align=\"center\">Numbers of Contacts: $num_contacts</h2>
				<br />";
?>    


<p>&nbsp;</p>
<table width="410" height="113" border="1">
  <tr><td width="400" height="27"><h2>Delete Listing</h2></td></tr>
  <tr><td>
  	<form action='vault.php' method='POST'>
		<p>Listing ID: 
		  <input type="text" name="delete" input="input" />
		  <input type="submit" value="delete">
		</p>
</form>
  </td></tr>
</table>

  <div id="foot">
<?php include "html/footer.inc"; ?>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.lightbox').lightbox();
  });
</script>

</body>
</html>