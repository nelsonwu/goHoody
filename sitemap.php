<?php

	header("Content-Type: text/xml;charset=iso-8859-1");  
	
	//Connect to Hoody MySQL database
	include "php/misc.inc";
	include "php/hoody_functions.php";
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	    
	//this is the normal header applied to any Google sitemap.xml file  
	echo	'<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
			
			<url>
  				<loc>http://gohoody.com/</loc>
  				<changefreq>always</changefreq>
				<priority>1</priority>
			</url>
			<url>
  				<loc>http://gohoody.com/uoft/</loc>
  				<changefreq>daily</changefreq>
				<priority>0.8</priority>
			</url>
			<url>
  				<loc>http://gohoody.com/about-us/</loc>
  				<changefreq>weekly</changefreq>
				<priority>1</priority>
			</url>
			<url>
  				<loc>http://gohoody.com/search.php</loc>
  				<changefreq>always</changefreq>
			</url>
			<url>
  				<loc>http://gohoody.com/service/</loc>
  				<changefreq>always</changefreq>
			</url>';


	
	//Services
	$service_query = "SELECT listing_id FROM Listing_Overview WHERE status=1";
	$result = mysql_query($service_query) or die(minor_error(215, $fbme, $uid, $today, $query, mysql_error()));
	$num_rows = mysql_num_rows($result);
	
	//loop through the entire resultset  
	for($i=0;$i<$num_rows; $i++)  
	{  
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
				 
		//you can assign whatever changefreq and priority you like 
		echo  
		'  
			<url>  
			<loc>http://gohoody.com/service/' . $row['listing_id'] . '/</loc>  
			<changefreq>daily</changefreq>  
			<priority>0.8</priority>  
			</url>  
		';  	  
	}  
	
	//Profiles
	$user_lookup_sql = "SELECT profile_name FROM User_Lookup";
	$result = mysql_query($user_lookup_sql) or die (fatal_error(268, $fbme, $uid, $today, $user_lookup_sql, mysql_error()));
	$num_rows = mysql_num_rows($result);
	
	
	//loop through the entire resultset  
	for($i=0;$i<$num_rows; $i++)  
	{  	
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
		//you can assign whatever changefreq and priority you like 
		echo  
		'  
			<url>  
			<loc>http://gohoody.com/profile/' . $row['profile_name'] . '/</loc>  
			<changefreq>daily</changefreq>  
			<priority>0.8</priority>  
			</url>  
		';  	  
	}  
	
	
	//Category
	$category_lookup_sql = "SELECT category_url FROM Category_Lookup";
	$result = mysql_query($category_lookup_sql) or die (fatal_error(268, $fbme, $uid, $today, $user_lookup_sql, mysql_error()));
	$num_rows = mysql_num_rows($result);
	
	
	//loop through the entire resultset  
	for($i=0;$i<$num_rows; $i++)  
	{  	
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
		//you can assign whatever changefreq and priority you like 
		echo  
		'  
			<url>  
			<loc>http://gohoody.com/ask/' . $row['category_url'] . '/</loc>  
			<changefreq>daily</changefreq>  
			<priority>0.8</priority>  
			</url>  
		';  	  
	} 


	//Main ask page
	echo  
		'  
			<url>  
			<loc>http://gohoody.com/ask/</loc>  
			<changefreq>daily</changefreq>  
			<priority>0.8</priority>  
			</url>  
		'; 
		
		
	mysql_close(); //close connection  
	  
	//close the XML attribute that we opened in #3  
	echo  
	'</urlset>';  





?>