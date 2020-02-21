/* global tinymce, wpCookies, autosaveL10n, switchEditors */
// Back-compat
window.opalestate_messages = function() {
	return true;
};

/**
 * @summary Adds autosave to the window object on dom ready.
 *
 * @since 3.9.0
 *
 * @param {jQuery} $ jQuery object.
 * @param {window} The window object.
 *
 */
 
( function( $, window ) {

	/**
	 * @summary Auto saves the post.
	 *
	 * @since 3.9.0
	 *
	 * @returns {Object}
	 * 	{{
	 * 		getPostData: getPostData,
	 * 		getCompareString: getCompareString,
	 * 		disableButtons: disableButtons,
	 * 		enableButtons: enableButtons,
	 * 		local: ({hasStorage, getSavedPostData, save, suspend, resume}|*),
	 * 		server: ({tempBlockSave, triggerSave, postChanged, suspend, resume}|*)}
	 * 	}
	 * 	The object with all functions for autosave.
	 */

	function opalestate_messages() {

		var  $document = $( document );
		var  $page 	   = $( '#page-importer' );

		/**
		 *
		 */
		function trigger_send_messages(){
			 $( ".opalestate-message-form" ).on('submit', function(){ 
			 	make_ajax( $( this ).serialize(), this );
			 	return false; 
			 } );
		}


	 	function toggle_submit_button ( submit ){
	 		var _this = $('button[type="submit"]', submit );
	        if( $(  _this ).attr('disabled') == "disabled" ){
	             $( _this ).removeAttr( 'disabled' );
	             $(_this).find('i').remove( );  
	        } else {
	             $( _this ).attr('disabled','disabled');
	             $(_this).append( '<i class="fa fa-spinner fa-spin"></i>' );   
	        }
	       
	    }; 

		function trigger_send_reply(){
			$( ".opalestate-form-reply" ).on( "submit" , function(){
				var message = $( 'textarea', this).val();
				if( message ) {
					make_ajax_reply( $( this ).serialize(), this );
				}
				
				return false; 
			} );
		}

		function make_ajax_reply( data, _this ){

			$( '.opalestate-message-notify', _this ).remove();
  			$.ajax({
                type     : 'POST',
                dataType : 'json',
                url		 : 	opalesateJS.ajaxurl,
                data     :  'action=send_email_contact_reply&' + data,
                success: function( response ) {
                    if( response ) {
                    	var _class = response.status ? 'success' : 'danger';
                    	// $( _this ).append('<p class="opalestate-message-notify msg-status-'+ _class +'"><span>'+ response.msg +'</span></p>');
                    	if( response.status ){
                			$( 'textarea', _this ).val( "" );
                    		var myToast = $.toast({
		                        heading: response.heading,
		                        text: response.msg,
		                        icon: 'success',
		                        position:  'bottom-right', 
		                        hideAfter: 3500, 
		                        showHideTransition: 'fade'
		                    });
                    		if ( response.data ){
                    			var html = '<div class="message-body">';
                    				html += '<div class="message-body"><div class="message-avatar">';
                    				html += '<img src="'+response.data.avatar+'">';
                    				html += '</div><div class="message-content">';
                    				html +=  '<div>'+response.data.created+'</div>' + response.data.message;
                    				html += '</div></div>';
                    				html += '</div>';

                    			$(".opalestate-read-message").append( html );	
                    		}
                    	} else {

                    	}
                    }
                }
            });
		}

		function load_message_reply(){

		}

  		function make_ajax ( data, _this ) { 
  			$( '.opalestate-message-notify', _this ).remove();
  			var action = $( _this ).data('action')? $( _this ).data('action') : 'send_email_contact';
  			toggle_submit_button( _this );
  			$.ajax({
                type     : 'POST',
                dataType : 'json',
                url		 : 	opalesateJS.ajaxurl,
                data     :  'action='+action+'&' + data,
                success: function( response ) {
                    if( response ) {
                    	var _class = response.status ? 'success' : 'danger';
                    	$( _this ).append('<p class="opalestate-message-notify msg-status-'+ _class +'"><span>'+ response.msg +'</span></p>');
                    	if( response.status ){
                    		$( 'textarea', _this ).val( "" );
                    	}
                    	toggle_submit_button( _this );
                    }
                },
				error: function( response ) {
					console.log(response)
				}
            });
  		} 

  		function trigger_print_property() {
  			$( '.js-print-property' ).on( 'click', function ( e ) {
		        e.preventDefault();

		        var id = $( this ).data( 'id' );
		        var newWindown = window.open( '', 'Print!', 'width=800 ,height=850' );

		        $.ajax( {
		            type: 'POST',
		            url: opalesateJS.ajaxurl,
		            data: {
		                'action': 'opalestate_ajax_create_property_print',
		                'id': id,
		            },
		            success: function ( data ) {
		                newWindown.document.write( data );
		                newWindown.document.close();
		                newWindown.focus();

		                setTimeout( function () {
		                    newWindown.print();
		                }, 1000 );
		            }
		        } );
		    } );
  		}

  		function trigger_toggle_featured() {
  			 /// ajax set featured
		    $( 'body' ).delegate( '.btn-toggle-featured', 'click', function () {
		        var $this = $( this );
		        $.ajax( {
		            type: 'POST',
		            url: opalesateJS.ajaxurl,
		            data: 'property_id=' + $( this ).data( 'property-id' ) + '&action=opalestate_toggle_featured_property',                                                                                                // elements.
		            dataType: 'json',
		            success: function ( data ) {
		                if ( data.status ) {
		                    $( '[data-id="property-toggle-featured-' + $this.data( 'property-id' ) + '"]' )
		                        .removeClass( 'hide' );
		                    $this.remove();
		                } else {
		                    alert( data.msg );
		                }
		            }
		        } );
		        return false;
		    } );
  		}

  		function trigger_view_gallery(){
  			$( 'body' ).delegate( '.opalestate-ajax-gallery', 'click', function () { 
  				var parent = $(this).parent();
  				var open_gallery = function ( parent ){
					$(parent ).magnificPopup({
						  type: 'image',
						  delegate: 'a.gallery-item',
						  gallery:{
						    	enabled:true
						  }
					});
					$( 'a.gallery-item', parent ).trigger('click'); 
  				} 
  				if( $(".gallery-item", parent ).length <= 0 ){
  					var items = [];

  					$.ajax( {
			            type: 'POST',
			            url: opalesateJS.ajaxurl,
			            data: 'property_id=' + $( this ).data( 'id' ) + '&action=opalestate_gallery_property',                                                                                                // elements.
			            dataType: 'json',
			            success: function ( data ) {
			             	if( data.gallery ){
			             		for( var image in data.gallery ){
			             			parent.append( '<a href="'+data.gallery[image]+'" class="gallery-item hide"></a>' );
			             		} 
			 
			             	} 
			             	open_gallery( parent );
			            }
			        } );
  				} else {
  					open_gallery( parent );
  				}

  				return false;
  			} );
  		}
		/**
		 * @summary Sets the autosave time out.
		 *
		 * Wait for TinyMCE to initialize plus 1 second. for any external css to finish loading,
		 * then save to the textarea before setting initialCompareString.
		 * This avoids any insignificant differences between the initial textarea content and the content
		 * extracted from the editor.
		 *
		 * @since 3.9.0
		 *
		 * @returns {void}
		 */
		$document.on( '.opalestate-message-form', function( event, editor ) {
 
		}).ready( function() {
			trigger_send_messages();	
			trigger_send_reply();	

			trigger_print_property();
			trigger_toggle_featured();

			trigger_view_gallery();
		});

		return {
			 
		};
	}

	/** @namespace wp */
	window.wp = window.wp || {};
	window.wp.opalestate_messages = opalestate_messages();

}( jQuery, window ));
