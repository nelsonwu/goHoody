
// When the DOM is ready...
$(function(){
	
	// Hide stuff with the JavaScript. If JS is disabled, the form will still be useable.
	// NOTE:
	// Sometimes using the .hide(); function isn't as ideal as it uses display: none; 
	// which has problems with some screen readers. Applying a CSS class to kick it off the
	// screen is usually prefered, but since we will be UNhiding these as well, this works.
	
	<!--tabs script-->
	var show_tab1 = function show_tab1() {
	   
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
	
	$('#step_2 :input').attr('disabled', true);
	$('#step_3 :input').attr('disabled', true);
	$('#continue :input').attr('disabled', true);
	
	$("#back2").click(show_tab1);
	$("#back3").click(show_tab2);
	
	$.stepTwoComplete_one = "not complete";
	$.stepTwoComplete_two = "not complete"; 
	$.stepTwoComplete_three = "not complete"; 
	$.percentage_amt = 0;
	$.change_amt = 0;	
	
	if ($("#isFilled").val() == '1'){
		$("#display_address_wrap").slideDown();
	  	$("#display_address_wrap2").slideDown();
		$("#display_address_wrap3").slideDown();
		$("#display_address_wrap4").slideDown();
		  
			  
		$.stepTwoComplete_one = "complete";
		$.stepTwoComplete_two = "complete"; 
		$.stepTwoComplete_three = "complete";
		  
		
			
		$("#check1").fadeIn(500);
		
		$("#step_2").css({
			opacity: 1.0
		});
		$("#step_2 legend").css({
			opacity: 1.0 // For dumb Internet Explorer
		});
		$('#step_2 :input').attr('disabled', false);
		$('#step_3 :input').attr('disabled', false);
		$('#continue :input').attr('disabled', false);	
		
		
		
		
		stepTwoTest();
	  
		
	};
	
	
	//progress
	
	
	function check_title() {
		if ($("input#title").val() == '' ) {
		
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {	
			
			  if ( ($("textarea#textfield").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  
				  advance(5);
				  percent(0);
				 
			  }
			  else if ( !($("textarea#textfield").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 105 ) ) {
				  advance(70);
				  percent(40);
				  
				  /*$("#percentage_text").html($("#progress").width());*/
			  }
			  else if ( !($("textarea#textfield").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( ($("textarea#textfield").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  };
			};
		}
		else {
			
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {
			
			  if ( ($("textarea#textfield").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 5 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( !($("textarea#textfield").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 70 )) {
				  advance(105);
				  percent(60);
			  }
			  else if ( !($("textarea#textfield").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  }
			  else if ( ($("textarea#textfield").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  };
			};
			
		};
	
	};
	
	function check_textfield() {
		if ($("textarea#textfield").val() == '' ) {
		
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {	
			
			  if ( ($("input#title").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(5);
				  percent(0);
			  }
			  else if ( !($("input#title").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 105 ) ) {
				  advance(70);
				  percent(40);
			  }
			  else if ( !($("input#title").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( ($("input#title").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  };
			};
		}
		else {
			
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {
			
			  if ( ($("input#title").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 5 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( !($("input#title").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(105);
				  percent(60);
			  }
			  else if ( !($("input#title").val() == '')  && ($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  }
			  else if ( ($("input#title").val() == '')  && !($("input#price").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  };
			};
			
		};
	
	};
	
	function check_price() {
		if ($("input#price").val() == '' ) {
		
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {	
			
			  if ( ($("input#title").val() == '')  && ($("textarea#textfield").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(5);
				  percent(0);
			  }
			  else if ( !($("input#title").val() == '')  && !($("textarea#textfield").val() == '' ) && ($("#progress").width() == 105 ) ) {
				  advance(70);
				  percent(40);
			  }
			  else if ( !($("input#title").val() == '')  && ($("textarea#textfield").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( ($("input#title").val() == '')  && !($("textarea#textfield").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(35);
				  percent(20);
			  };
			};
		}
		else {
			
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && ($("input#file1").val() == '' ) ) {
			
			  if ( ($("input#title").val() == '')  && ($("textarea#textfield").val() == '' ) && ($("#progress").width() == 5 ) ) {
				  advance(35);
				  percent(20);
			  }
			  else if ( !($("input#title").val() == '')  && !($("textarea#textfield").val() == '' ) && ($("#progress").width() == 70 ) ) {
				  advance(105);
				  percent(60);
			  }
			  else if ( !($("input#title").val() == '')  && ($("textarea#textfield").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  }
			  else if ( ($("input#title").val() == '')  && !($("textarea#textfield").val() == '' ) && ($("#progress").width() == 35 ) ) {
				  advance(70);
				  percent(40);
			  };
			};
			
		};
	
	};
	
	
	$("input#title").keyup(function(){
		check_title();
	});
	
	$("textarea#textfield").keyup(function(){
		check_textfield();
	});
	
	$("input#price").keyup(function(){
		check_price();
	});
	
	$("input#title").click(function(){
		check_title();
	});
	
	$("textarea#textfield").click(function(){
		check_textfield();
	});
	
	$("input#price").click(function(){
		check_price();
	});
	
	
	
	//for tip box
	
	$("input#title").focus(function(){
		$("#tip_title").delay(105).fadeIn();
	});
	
	
	$("input#price").focus(function(){
		$("#tip_price").delay(105).fadeIn();
	});
	
	$("input#title").blur(function(){
		$("#tip_title").fadeOut(100);
	});
	
	
	$("input#price").blur(function(){
		$("#tip_price").fadeOut(100);
	});
	
		

	//Check if complete
	$(".desc_input").keyup(function(){
	
		var all_complete = true;
				
		$(".desc_input").each(function(){
			if ($(this).val() == '' ) {
				all_complete = false;
			};
			
		});

		
		if (all_complete) {
			
			$("#check1").fadeIn(500);
			
			$("#step_2").css({
				opacity: 1.0
			});
			$("#step_2 legend").css({
				opacity: 1.0 // For dumb Internet Explorer
			});
			$('#step_2 :input').attr('disabled', false);
			
			
			
			$("#next1").click(show_tab2);
			
			$('#tab1').click(show_tab1);
			$('#tab2').click(show_tab2);
			
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
				$('#tab3').click(show_tab3);
			};
			
			if ( (!(($.stepTwoComplete_one == "complete") && ($.stepTwoComplete_two == "complete") && ($.stepTwoComplete_three == "complete"))) && !($("input#file1").val() == '' ) ) {
				
				$('#tab3').css({
					opacity: 1.0
				});
				$('#tab3').click(show_tab3);
			};
				
		} else { // not complete
			
			$("#check1").fadeOut(500);
			
			$("#step_2").css({
				opacity: 0.3
			});
			$("#step_2 legend").css({
				opacity: 0.3 // For dumb Internet Explorer
			});
			$('#step_2 :input').attr('disabled', true);
			
			
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
			$("#check2").fadeIn(500);
			
			$("#step_3").css({
				opacity: 1.0
			});
			$("#step_3 legend").css({
				opacity: 1.0 // For dumb Internet Explorer
			});
			$("#continue").css({
				opacity: 1.0
			});
			$('#step_3 :input').attr('disabled', false);
			$('#continue :input').attr('disabled', false);
			
			$("#next2").click(show_tab3);
	
			$('#tab2').click(show_tab2);
			$('#tab3').click(show_tab3);
			
			$("#next2").css({
				opacity: 1.0
			});
			
			$('#tab3').css({
				opacity: 1.0
			});
			
			if ($("input#file1").val() == '' ) {
				advance(140);
				percent(80);
			};
			
			
		} else { // not complete
			$("#check2").fadeOut(500);
			
			$("#step_3").css({
				opacity: 0.3
			});
			$("#step_3 legend").css({
				opacity: 0.3 // For dumb Internet Explorer
			});
			$("#continue").css({
				opacity: 0.3
			});
			$('#step_3 :input').attr('disabled', true);
			$('#continue :input').attr('disabled', true);
			
			$('#next2').attr('onclick','').unbind('click');
			
			$('#tab3').attr('onclick','').unbind('click');
			
			$("#next2").css({
				opacity: 0.3
			});
			
			$('#tab3').css({
				opacity: 0.3
			});
			if ($("input#file1").val() == '' ) {
				advance(105);
				percent(60);
			};
		};
	};
	
	
	//check if picture file has been chosen
	
	$("input#file1").change(function(){
		if ($("input#file1").val() == '' ) {
			$("#next3").css({
				opacity: 0.3
			});
			advance(140);
			percent(80);
		}
		else{
			$("#next3").css({
				opacity: 1.0
			});	
			advance(175);
			percent(100);
		};
	});
	
	
	$("#step_2 input[name=location1]").click(function(){
		$.stepTwoComplete_one = "complete"; 
		if ($("#location_home:checked").val() == 'at_home') {
			$("#display_address_wrap").slideDown();
			$.stepTwoComplete_two = "complete";
			$.stepTwoComplete_three = "complete";
		} else {
			$("#display_address_wrap").slideUp();
		};
		if ($("#location_away:checked").val() == 'away_home') {
			$("#display_address_wrap2").slideDown();
			$.stepTwoComplete_three = "not complete";
			
			/*Check if range is filled*/
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
			
			/*check if another address is filled*/
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
			
			
		} else {
			$("#display_address_wrap2").slideUp();
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