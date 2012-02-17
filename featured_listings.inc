<?php
	// listing_id for the listings to be featured on the front page
	$raw_featured_1  = 387; //Digital SLR Photography Course for Beginners
	$raw_featured_2  = 385; //Computer Tune-up
	$raw_featured_3  = 384; //Computer Networking Setup
	$raw_featured_4  = 422; //Automotive Photography
	$raw_featured_5  = 418; //Airport Taxi service
	$raw_featured_6  = 417; //Mobile Car wash
	$raw_featured_7  = 416; //Java Web Application Development
	$raw_featured_8  = 414; //Apple iPad (both iPad 1 and iPad 2) Jailbreak Service
	$raw_featured_9  = 413; //Apple iPhone / iPod Touch Jailbreak
	$raw_featured_10 = 404; //Graphic Design
	$raw_featured_11 = 714; //Ruby on Rails Lessons
	$raw_featured_12 = 583; //Custom Typography
	$raw_featured_13 = 607; //Computer Science Tutor
	$raw_featured_14 = 583; //Custom Typography
	$raw_featured_15 = 582; //Search Engine Optimization
	$raw_featured_16 = 572; //Copy editing
	$raw_featured_17 = 571; //Concept Industrial Design
	$raw_featured_18 = 570; //Personal Shopper ;)
	$raw_featured_19 = 574; //Condo rental service (Rental/Listing)
	$raw_featured_20 = 568; //Print media design
	$raw_featured_21 = 498; //Personal/Small Business website design
	$raw_featured_22 = 493; //Providing MTT Coaching at Micro and Low Stakes
	$raw_featured_23 = 479; //Graphic Design
	$raw_featured_24 = 472; //Graphic Design / Typography
	$raw_featured_25 = 469; //taxi service to airport
	$raw_featured_26 = 467; //Illüm Photography
	$raw_featured_27 = 466; //Commercial and Editorial Photography
	$raw_featured_28 = 415; //Flash Website Developer
	$raw_featured_29 = 398; //Smartphone/Tablet Setup
	$raw_featured_29 = 710; //Personal Training
	$raw_featured_30 = 746; //Personal Training
		
	
	// listing_id for the listings to be featured on the UofT page
	$featured_uoft_1 = 405;
	$featured_uoft_2 = 576;
	$featured_uoft_3 = 413;
	$featured_uoft_4 = 569;
	$featured_uoft_5 = 570;
	$featured_uoft_6 = 418;
	$featured_uoft_7 = 554;
	$featured_uoft_8 = 572;
	$featured_uoft_9 = 574;
	$featured_uoft_10 = 385;
	
	$featured_uoft_listings = array($featured_uoft_1,$featured_uoft_2,$featured_uoft_3,$featured_uoft_4,$featured_uoft_5,$featured_uoft_6,$featured_uoft_7,$featured_uoft_8,$featured_uoft_9,$featured_uoft_10);
	$featured_listings = array($raw_featured_1,$raw_featured_2,$raw_featured_3,$raw_featured_4,$raw_featured_5,$raw_featured_6,$raw_featured_7,$raw_featured_8,$raw_featured_9,$raw_featured_10);
	
	// select some random elements from the array
	$random_indices = array_rand($featured_listings, 5);
	$random_uoft_indices = array_rand($featured_uoft_listings, 5);

	// output the random elements
	$featured_listings_1 = $featured_listings[$random_indices[0]];
	$featured_listings_2 = $featured_listings[$random_indices[1]];
	$featured_listings_3 = $featured_listings[$random_indices[2]];
	$featured_listings_4 = $featured_listings[$random_indices[3]];
	$featured_listings_5 = $featured_listings[$random_indices[4]];	
	
	// output the random elements
	$featured_uoft_listings_1 = $featured_uoft_listings[$random_uoft_indices[0]];
	$featured_uoft_listings_2 = $featured_uoft_listings[$random_uoft_indices[1]];
	$featured_uoft_listings_3 = $featured_uoft_listings[$random_uoft_indices[2]];
	$featured_uoft_listings_4 = $featured_uoft_listings[$random_uoft_indices[3]];
	$featured_uoft_listings_5 = $featured_uoft_listings[$random_uoft_indices[4]];		
?>