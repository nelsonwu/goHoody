function show_tab1() {
	  $('#tab1_content').fadeIn(); 
	  $('#tab2_content').hide();
	  $('#tab3_content').hide();
	  $('#tab4_content').hide();
	  $('#tab5_content').hide();		  
	  	   
	
	  $('#tab1').css('font-weight', 'bold');
	  $('#tab2').css('font-weight', 'normal');
	  $('#tab3').css('font-weight', 'normal');
	  $('#tab4').css('font-weight', 'normal');
	  $('#tab5').css('font-weight', 'normal');
	  
	}
	
	function show_tab2() {
		$('#tab2_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab3_content').hide();
		$('#tab4_content').hide();		  
		$('#tab5_content').hide();
		
	
		$('#tab2').css('font-weight', 'bold');
		$('#tab1').css('font-weight', 'normal');
		$('#tab3').css('font-weight', 'normal');
		$('#tab4').css('font-weight', 'normal');
		$('#tab5').css('font-weight', 'normal');
		
	}
	function show_tab3() {
		$('#tab3_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab2_content').hide();
		$('#tab4_content').hide();	
		$('#tab5_content').hide();	  
	
		
		$('#tab3').css('font-weight', 'bold');
		$('#tab1').css('font-weight', 'normal');
		$('#tab2').css('font-weight', 'normal');
		$('#tab4').css('font-weight', 'normal');
		$('#tab5').css('font-weight', 'normal');
	}
	function show_tab4() {
		$('#tab4_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab3_content').hide();
		$('#tab2_content').hide();	
		$('#tab5_content').hide();	  
	
		$('#tab4').css('font-weight', 'bold');
		$('#tab1').css('font-weight', 'normal');
		$('#tab3').css('font-weight', 'normal');
		$('#tab2').css('font-weight', 'normal');
		$('#tab5').css('font-weight', 'normal');
		
	}
	function show_tab5() {
		$('#tab5_content').fadeIn();
		$('#tab1_content').hide();
		$('#tab3_content').hide();
		$('#tab2_content').hide();
		$('#tab4_content').hide();		  
	
		$('#tab5').css('font-weight', 'bold');
		$('#tab1').css('font-weight', 'normal');
		$('#tab3').css('font-weight', 'normal');
		$('#tab4').css('font-weight', 'normal');
		$('#tab2').css('font-weight', 'normal');
	}


  


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
		
	if ($("#postal_radio_button:checked").val() == 'area_code_address') {
		 $("#address_fields").css({
				  opacity: 0.3 // For dumb Internet Explorer
		  });
		   $("#postal_fields").css({
				  opacity: 1 // For dumb Internet Explorer
		  });
		  $(".dashboard_button").hide();
	}
	
	if ($("#street_radio_button:checked").val() == 'street_address') {
		  $("#postal_fields").css({
				  opacity: 0.3 // For dumb Internet Explorer
		  });
		  $("#address_fields").css({
				  opacity: 1 // For dumb Internet Explorer
		  });
		  $(".dashboard_button").show();
		  
	}
		
	
	$("#radio_postal input[name=location1]").click(function(){	
	
		  $("#address_fields").css({
				  opacity: 0.3 // For dumb Internet Explorer
		  });
		   $("#postal_fields").css({
				  opacity: 1 // For dumb Internet Explorer
		  });
		  $(".dashboard_button").hide();
		  
	});
	$("#radio_street input[name=location1]").click(function(){	
	
		  $("#postal_fields").css({
				  opacity: 0.3 // For dumb Internet Explorer
		  });
		  $("#address_fields").css({
				  opacity: 1 // For dumb Internet Explorer
		  });
		  $(".dashboard_button").show();
	});
	
	
	
	

});

