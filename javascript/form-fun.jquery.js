
// When the DOM is ready...
$(function(){
	
	// Hide stuff with the JavaScript. If JS is disabled, the form will still be useable.
	// NOTE:
	// Sometimes using the .hide(); function isn't as ideal as it uses display: none; 
	// which has problems with some screen readers. Applying a CSS class to kick it off the
	// screen is usually prefered, but since we will be UNhiding these as well, this works.
	
	<!--tabs script-->
	var show_tab1 = function show_tab1() {
	   
	    $('html, body').delay(100).animate({ scrollTop: 0 }, 'slow');
	   
		$('#tab1_content').fadeIn(); 
		$('#tab2_content').hide();
		$('#tab3_content').hide();
		$('#tab4_content').hide();		  
			
		$('#tab1').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	var show_tab2 = function show_tab2() {
	   
	    $('html, body').delay(100).animate({ scrollTop: 0 }, 'slow');
	    
		$('#tab2_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab3_content').hide();
		$('#tab4_content').hide();		  
		
		$('#tab2').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	var show_tab3 = function show_tab3() {
		
		$('html, body').delay(100).animate({ scrollTop: 0 }, 'slow');
		
		$('#tab3_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab2_content').hide();
		$('#tab4_content').hide();		  
		
		$('#tab3').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	
	<!--tabs script-->
	var show_tab1b = function show_tab1b() {
	   
	   
		$('#tab1_content').fadeIn(); 
		$('#tab2_content').hide();
		$('#tab3_content').hide();
		$('#tab4_content').hide();		  
			
		$('#tab1').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	var show_tab2b = function show_tab2b() {
	   
	    
		$('#tab2_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab3_content').hide();
		$('#tab4_content').hide();		  
		
		$('#tab2').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab3').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	var show_tab3b = function show_tab3b() {

		
		$('#tab3_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab2_content').hide();
		$('#tab4_content').hide();		  
		
		$('#tab3').css({"margin-top": "1px", "background-color":"#F5F5F5"});
		$('#tab1').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab2').css({"margin-top": "0px", "background-color":"#FFFFFF"});
		$('#tab4').css({"margin-top": "0px", "background-color":"#FFFFFF"}); 
		
		
	}
	
	
	
	$("#display_address_wrap").hide();
	$("#display_address_wrap2").hide();
	$("#display_address_wrap3").hide();
	$("#display_address_wrap4").hide();
	
	
	$("#back2").click(show_tab1);
	$("#back3").click(show_tab2);
	
	$.stepTwoComplete_one = "not complete";
	$.stepTwoComplete_two = "not complete"; 
	$.stepTwoComplete_three = "not complete"; 
	$.percentage_amt = 0;
	$.change_amt = 0;	
	
	$.isTitleComplete = 0;
	$.isTextfieldComplete = 0;
	$.isPriceComplete = 0;
	$.isLocationComplete = 0;
	$.isPictureComplete = 0;
	$.completeness = 0;
	
	if ($("#isFilled").val() == '1'){
	
		//check for step2
		 
		if ($("#location_home:checked").val() == 'at_home') {
			$("#display_address_wrap").show();
		} else {
			$("#display_address_wrap").hide();
		};
		if ($("#location_away:checked").val() == 'away_home') {
			$("#display_address_wrap2").show();
			
			if ($("#buyer_home:checked").val() == 'buyer_home') {
			  $("#display_address_wrap3").show();
	
			} else {
				$("#display_address_wrap3").hide();
			};
			
			/*check if another address is filled*/
			if ($("#other:checked").val() == 'other') {
				$("#display_address_wrap4").show();
			
			} else {
				$("#display_address_wrap4").hide();
			};	
			
		} else {
			$("#display_address_wrap2").hide();
		};
		
		//check for radio and checkbox states
	
		if ($("#radio1:checked").val() == '0') {
			$(".box_unchecked_job").hide();
			$(".box_checked_job").show();
			
			$(".box_checked_hour").hide();
			$(".box_unchecked_hour").show();
				
		}
		else{
			$(".box_checked_job").hide();
			$(".box_unchecked_job").show();
			
			$(".box_unchecked_hour").hide();
			$(".box_checked_hour").show();
		};
	
	
	
		if ($("#location_home:checked").val() == 'at_home') {
			$(".box_unchecked_home").hide();
			$(".box_checked_home").show();
			
			$(".box_checked_away").hide();
			$(".box_unchecked_away").show();
			
			$(".box_checked_virtual").hide();
			$(".box_unchecked_virtual").show();
				
		};
		if ($("#location_away:checked").val() == 'away_home'){
			$(".box_checked_home").hide();
			$(".box_unchecked_home").show();
	
			
			$(".box_unchecked_away").hide();
			$(".box_checked_away").show();
			
			$(".box_checked_virtual").hide();
			$(".box_unchecked_virtual").show();
		};
		if ($("#location_virtual:checked").val() == 'virtual') {
			$(".box_checked_home").hide();
			$(".box_unchecked_home").show();
			
			$(".box_checked_away").hide();
			$(".box_unchecked_away").show();
			
			$(".box_checked_virtual").show();
			$(".box_unchecked_virtual").hide();
			
		};
	
	
	
		if ($("#display_address:checked").val() == '1') {
			$(".box_unchecked_addy").hide();
			$(".box_checked_addy").show();
				
		}
		else{
			$(".box_checked_addy").hide();
			$(".box_unchecked_addy").show();
			
			
		};
	
	
		if ($("#buyer_home:checked").val() == 'buyer_home') {
			$(".radio_unchecked_buyer").hide();
			$(".radio_checked_buyer").show();
			
			$(".radio_checked_another").hide();
			$(".radio_unchecked_another").show();
				
		}
		else{
			$(".radio_checked_buyer").hide();
			$(".radio_unchecked_buyer").show();
			
			$(".radio_unchecked_another").hide();
			$(".radio_checked_another").show();
		};
	
	    $("#next1").click(show_tab2);
		$("#next2").click(show_tab3);
		$("#next3").show();	
		$('#tab1').click(show_tab1b);
		$('#tab2').click(show_tab2b);
		$('#tab3').click(show_tab3b);
		
		  
		$("#next1").css({
			opacity: 1.0
		});
		$("#next2").css({
			opacity: 1.0
		});
		
		$('#tab1').css({
			opacity: 1.0
		});
		$('#tab2').css({
			opacity: 1.0
		});
		$('#tab3').css({
			opacity: 1.0
		});
		
		$.stepTwoComplete_one = "complete";
		$.stepTwoComplete_two = "complete"; 
		$.stepTwoComplete_three = "complete"; 
		$.isPictureComplete = 1;
	  	
		check_progress();
		
	};
	
	
	//progress
	
		
	
		
	
	function check_progress(){
		
		
		$.isTitleComplete = !($("input#title").val() == '' );
		$.isTextfieldComplete = !($("textarea#textfield").val() == '' );
		$.isPriceComplete = !($("input#price").val() == '' );
		$.isLocationComplete = (($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete")) ;
		
		
		
		if ($.completeness != $.isTitleComplete + $.isTextfieldComplete + $.isPriceComplete + $.isLocationComplete + $.isPictureComplete ) {
		
			$.completeness = $.isTitleComplete + $.isTextfieldComplete + $.isPriceComplete + $.isLocationComplete + $.isPictureComplete;
			
			if ($.completeness == 0){
				advance(5);
				percent(0);
			}
			else {
				advance($.completeness * 35);
				percent($.completeness * 20);
			};
	
		};
	};

	
	//for tip box
	
	$("input#title").focus(function(){
		$("#tip_title").delay(105).fadeIn();
	});
	
	$("textarea#textfield").focus(function(){
		$("#tip_description").delay(105).fadeIn();
	});
	
	$("input#price").focus(function(){
		$("#tip_price").delay(105).fadeIn();
	});
	
	
	$("input#title").blur(function(){
		$("#tip_title").fadeOut(100);
	});
	
	$("textarea#textfield").blur(function(){
		$("#tip_description").fadeOut(100);
	});
	
	
	$("input#price").blur(function(){
		$("#tip_price").fadeOut(100);
	});
	
	$("input#location_home").blur(function(){
		$("#tip_home").fadeOut(100);
	});
	
	$("input#location_home").focus(function(){
		$("#tip_location").fadeOut(100);
	});
	
	$("input#location_away").focus(function(){
		$("#tip_location").fadeOut(100);
	});
	
	
	
		

	//Check if complete
	$(".desc_input").keyup(function(){
	
		check_progress();
		
		var all_complete = true;
				
		$(".desc_input").each(function(){
			if ($(this).val() == '' ) {
				all_complete = false;
			};
			
		});

		
		if (all_complete) {
			
		
			
			$("#next1").click(show_tab2);
			
			$('#tab1').click(show_tab1b);
			$('#tab2').click(show_tab2b);
			
			$("#next1").css({
				opacity: 1.0
			});
			
			$('#tab2').css({
				opacity: 1.0
			});
			
			if (($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete")) {
				$('#tab3').css({
					opacity: 1.0
				});
				$('#tab3').click(show_tab3b);
			};
			
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && 
			($.isPictureComplete == 1 ) ) {
				
				$('#tab3').css({
					opacity: 1.0
				});
				$('#tab3').click(show_tab3b);
			};
				
		} else { // not complete
			
			
			$('#next1').attr('onclick','').unbind('click');
			$('#tab1').attr('onclick','').unbind('click');
			$('#tab2').attr('onclick','').unbind('click');
			$('#tab3').attr('onclick','').unbind('click');
			
			$("#next1").css({
				opacity: 0.3
			});
			
			$('#tab2').css({
				opacity: 0.3
			});
			$('#tab3').css({
				opacity: 0.3
			});
			
			

		};
	});
	
	$("input.pricing_model").click(function(){
	
		if ($("#radio1:checked").val() == '0') {
			$(".box_unchecked_job").hide();
			$(".box_checked_job").show();
			
			$(".box_checked_hour").hide();
			$(".box_unchecked_hour").show();
				
		}
		else{
			$(".box_checked_job").hide();
			$(".box_unchecked_job").show();
			
			$(".box_unchecked_hour").hide();
			$(".box_checked_hour").show();
		};
	
	});
	
	
	$("input.location_options").click(function(){
	
		if ($("#location_home:checked").val() == 'at_home') {
			$(".box_unchecked_home").hide();
			$(".box_checked_home").show();
			$("#tip_range").fadeOut(100);
			$("#tip_location").fadeOut(100);
			$("#tip_home").delay(105).fadeIn();
			
			$(".box_checked_away").hide();
			$(".box_unchecked_away").show();
			
			$(".box_checked_virtual").hide();
			$(".box_unchecked_virtual").show();
				
		}
		else if ($("#location_away:checked").val() == 'away_home') {
			$(".box_checked_home").hide();
			$(".box_unchecked_home").show();
			$("#tip_home").fadeOut(100);
			$("#tip_location").fadeOut(100);
			$(".box_unchecked_away").hide();
			$(".box_checked_away").show();
			
			$(".box_checked_virtual").hide();
			$(".box_unchecked_virtual").show();
			
		}
		else if ($("#location_virtual:checked").val() == 'virtual') {
			$(".box_checked_home").hide();
			$(".box_unchecked_home").show();
			$("#tip_home").fadeOut(100);
			$("#tip_range").fadeOut(100);
			$("#tip_location").delay(105).fadeIn();
			$(".box_unchecked_away").show();
			$(".box_checked_away").hide();
			
			$(".box_checked_virtual").show();
			$(".box_unchecked_virtual").hide();
			
		};
	
	});
	
	$("input#display_address").click(function(){
	
		if ($("#display_address:checked").val() == '1') {
			$(".box_unchecked_addy").hide();
			$(".box_checked_addy").show();
				
		}
		else{
			$(".box_checked_addy").hide();
			$(".box_unchecked_addy").show();
			
			
		};
	
	});
	
	$("input.away_options").click(function(){
	
		if ($("#buyer_home:checked").val() == 'buyer_home') {
			$(".radio_unchecked_buyer").hide();
			$(".radio_checked_buyer").show();
			
			$("#tip_range").delay(105).fadeIn();
			
			$(".radio_checked_another").hide();
			$(".radio_unchecked_another").show();
				
		}
		else{
			$(".radio_checked_buyer").hide();
			$(".radio_unchecked_buyer").show();
			
			$("#tip_range").fadeOut(100);
			
			$(".radio_unchecked_another").hide();
			$(".radio_checked_another").show();
		};
	
	});
	
	
	function advance($amt) {  
	  	
	  $('#progress').animate({
		width: $amt
		}, {
		  duration: 1000,
		  specialEasing: {
		   width: 'easeOutBounce'
		  },
	  }); 
	};
	
	
	function countdown() {           
	   setTimeout(function () {   
		          
		  $.percentage_amt--;  
		  $('#percentage').html($.percentage_amt);  
		                  
		  if ($.percentage_amt > $.change_amt) {        
			 countdown();            
		  }                       
	   }, 25)
	}
	
	function countup() {           
	   setTimeout(function () {   
		           
		  $.percentage_amt++;   
		  $('#percentage').html($.percentage_amt);
		                  
		  if ($.percentage_amt < $.change_amt) {        
			 countup();            
		  }                       
	   }, 25)
	}


	function percent($amt) {  
	  	
	  $.change_amt = $amt;	
		
	  if ($.percentage_amt > $amt) {        
		 countdown();            
	  }   	
	  if ($.percentage_amt < $amt) {        
		 countup();            
	  }	
	  
	
		
	 
	};
	
	
	
	
	function stepTwoTest() {
		if (($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete")) {
	
			
			$("#next2").click(show_tab3);
	
			$('#tab2').click(show_tab2b);
			$('#tab3').click(show_tab3b);
			
			$("#next2").css({
				opacity: 1.0
			});
			
			$('#tab3').css({
				opacity: 1.0
			});
			
			check_progress();
			
			
		} else { // not complete
			
			
			$('#next2').attr('onclick','').unbind('click');
			
			$('#tab3').attr('onclick','').unbind('click');
			
			$("#next2").css({
				opacity: 0.3
			});
			
			$('#tab3').css({
				opacity: 0.3
			});
			check_progress();
		};
	};
	
	
	//check if picture file has been chosen
	
	/*$("input#file1").change(function(){
		
		check_progress();
		if ($("input#file1").val() == '' ) {
			$("#next3").css({
				opacity: 0.3
			});
	
		}
		else{
			$("#next3").css({
				opacity: 1.0
			});	
		};
	});*/
	
	
	$("#location_home").change(function(){
		$.stepTwoComplete_one = "complete"; 
		if ($("#location_home:checked").val() == 'at_home') {
			$("#display_address_wrap").delay(55).fadeIn()
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		} else {
			$("#display_address_wrap").fadeOut(50);
		};
		if ($("#location_away:checked").val() == 'away_home') {
			$("#display_address_wrap2").delay(55).fadeIn()
			$.stepTwoComplete_three = "not complete";
	
		} else {
			$("#display_address_wrap2").fadeOut(50);
		};
		
		if ($("#location_virtual:checked").val() == 'virtual') {
			$("#display_address_wrap").fadeOut(50);
			$("#display_address_wrap2").fadeOut(50);
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		};
		
		stepTwoTest();
	});
	
	$("#location_away").change(function(){
		$.stepTwoComplete_one = "complete"; 
		if ($("#location_home:checked").val() == 'at_home') {
			$("#display_address_wrap").delay(55).fadeIn();
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		} else {
			$("#display_address_wrap").fadeOut(50);
		};
		if ($("#location_away:checked").val() == 'away_home') {
			$("#display_address_wrap2").delay(55).fadeIn();
			$.stepTwoComplete_three = "not complete";
			
			
		} else {
			$("#display_address_wrap2").fadeOut(50);
		};
		
		if ($("#location_virtual:checked").val() == 'virtual') {
			$("#display_address_wrap").fadeOut(50);
			$("#display_address_wrap2").fadeOut(50);
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		};
		
		stepTwoTest();
	});
	
	$("#location_virtual").change(function(){
		$.stepTwoComplete_one = "complete"; 
		if ($("#location_home:checked").val() == 'at_home') {
			$("#display_address_wrap").delay(55).fadeIn();
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		} else {
			$("#display_address_wrap").fadeOut(50);
		};
		if ($("#location_away:checked").val() == 'away_home') {
			$("#display_address_wrap2").delay(55).fadeIn();
			$.stepTwoComplete_three = "not complete";
			
			
		} else {
			$("#display_address_wrap2").fadeOut(50);
		};
		
		if ($("#location_virtual:checked").val() == 'virtual') {
			$("#display_address_wrap").fadeOut(50);
			$("#display_address_wrap2").fadeOut(50);
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		};
		
		stepTwoTest();
	});
	
	
	$("#step_2 input[name=location2]").click(function(){
		$.stepTwoComplete_two = "complete"; 
		if ($("#buyer_home:checked").val() == 'buyer_home') {
			$("#display_address_wrap3").slideDown();
			
			if ( !$(".desc_input2").val() == '' ) {
				$.stepTwoComplete_three = "complete";
			}
			else {
				$.stepTwoComplete_three = "not complete";
			};
	
		} else {
			$("#display_address_wrap3").slideUp();
		};
		if ($("#other:checked").val() == 'other') {
			$("#display_address_wrap4").slideDown();
			
			var all_complete2 = true;
				
			$(".desc_input3").each(function(){
				if ($(this).val() == '' ) {
					all_complete2 = false;
				};
			});
			
			if (all_complete2) {
				$.stepTwoComplete_three = "complete";
			} else {
				$.stepTwoComplete_three = "not complete";
			}
				
		} else {
			$("#display_address_wrap4").slideUp();
		};
			
		stepTwoTest();
	});
	
	$(".desc_input2").keyup(function(){
		
		$.stepTwoComplete_three = "not complete";
		
		$(".desc_input2").each(function(){
			if ( !$(this).val() == '' ) {
				$.stepTwoComplete_three = "complete";
			};
		});
		
		stepTwoTest();
	});
	
	$(".desc_input3").keyup(function(){
		
		var all_complete2 = true;
				
		$(".desc_input3").each(function(){
			if ($(this).val() == '' ) {
				all_complete2 = false;
			};
		});
		
		if (all_complete2) {
			$.stepTwoComplete_three = "complete";
		} else {
			$.stepTwoComplete_three = "not complete";
		};
		
		
		stepTwoTest();
	});
	
	
	jQuery('#range').keyup(function () { 
		
		if (this.value == '0') {
		
		  $.stepTwoComplete_three = "not complete";
		  stepTwoTest();
		  this.value = this.value.replace(/^0/g,'');
		};
	});

	
	
	
	
});