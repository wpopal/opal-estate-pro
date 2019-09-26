(function( $ ) {
	'use strict';
	$( document ).ready( function() {
 

		$( ".adduser-team" ).delegate( ".remove-user", "click", function() {
			if( confirm( $(this).data('alert') ) ){
				$(this).parents( '.user-team' ).remove();
			}
		});

		$( '.opalestate-add-user-field' ).each( function() {
			var $this = $(this);
			$('.button', this).click( function () {
 

				var user_search = $( '.opalestate-adduser-search', $(this).parent().parent() ).val();
 

		        $('.opalestate-ajax').show();

		        var data = {
		            action: 'opalestate_ajax_search_username',
		            user_name: user_search,
		           
		        };

 

		        $.ajax({
		            type: "POST",
		            data: data,
		            dataType: "json",
		            url: ajaxurl,
		            success: function ( response ) {
		           		if( response.status == true ){ 
		           			var template = wp.template( 'adduser-team-template' );  
							$('.adduser-team', $this  ).append( template(  response.user   ) );
		           		}else {
		           			alert( response.message );
		           		}
		            }
		        });

			} );
		} );	
	} );
		
})( jQuery );	