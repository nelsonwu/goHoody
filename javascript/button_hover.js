//basically what this function do is to hide in start span.hidden and when the user is placed on one of the options appears with a fade effect
$(document).ready(function(){
			$("#userinfo").hide();
			
			/*$('#view_image').hide();
			$('#view_image').css('display','inline');
			$('#create_image').hide();
			$('#create_image').css('display','inline');
			$('#help_image').hide();
			$('#help_image').css('display','inline');
			*/
            $('#view_listing_button').hover( function (){
                $('#view_image').fadeIn('slow');
            }, function (){
                $('#view_image').fadeOut('slow');
            });
			
			
            $('#create_listing_button').hover( function (){
                $('#create_image').fadeIn('slow');
            }, function (){
                $('#create_image').fadeOut('slow');
            });
			
			
            $('#help_button').hover( function (){
                $('#help_image').fadeIn('slow');
            }, function (){
                $('#help_image').fadeOut('slow');
            });
});