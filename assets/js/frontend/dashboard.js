;(function ($, settings) {
    "use strict";

    if (window.Opalestate === undefined) {
        window.Opalestate = {};
    }

    /**
     * GooglemapSearch
     */
    var AgencyUpdateProfile = Opalestate.AgencyUpdateProfile = function ( form  ) {

            /**
             * Create Google Map In Single Property Only
             */
            function getFormData () {

                var formData = new FormData();

                    formData.append('section', 'general');
                    $(".cmb2-uploader-files").each( function(){
                        var file_btn      = $( 'input.select-file', this ); 
                        
                        var files =  $(".uploader-item-preview", this );

                        var name = $(this).data( 'name' );
                        var issingle = $( this ).data('single'); 
                        $(files).each( function( i , element ){ 
                            var file = $(this).prop( 'file');
                            if( file ) {
                                if( issingle ){
                                    formData.append( name, file ); 
                                } else {
                                    formData.append( name+"["+i+"]", file ); 
                                }
                            }
                        } );
                });

                return formData;    
            }


           function toggleSubmit ( _this ){
                if( $( _this ).attr('disabled') == "disabled" ){
                     $( _this ).removeAttr( 'disabled' );
                     $(_this).find('i').remove( );  
                } else {
                     $( _this ).attr('disabled','disabled');
                     $(_this).append( ' <i class="fa fa-spinner fa-spin"></i> ' );   
                }
               
            }; 

            function makeAjax( formData, $submit_btn ) {
                $.ajax({
                    url : opalesateJS.ajaxurl,
                    data : formData,
                    type : 'POST',
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success : function( response ){
                        if( response.status == true ){
                            if( response.redirect ){
                                window.location.href = response.redirect;
                            }

                            var myToast = $.toast({
                                heading: response.heading,
                                text: response.message,
                                icon: 'success',
                                position:  'bottom-right', 
                                hideAfter: 5000, 
                                showHideTransition: 'fade',
                            });
                        } else {
                            toggleSubmit( $submit_btn );
                            var myToast = $.toast({
                                heading: response.heading,
                                text: response.message,
                                icon: 'error',
                                position:  'bottom-right', 
                                 hideAfter: 5000, 
                                 showHideTransition: 'fade'
                            });
                        }
                    }
                }); 
            }

            var init = function ( form ){
               $( form ).on( "submit", function(){  
                    
                    if( typeof(tinyMCE) != "undefined" ) { 
                        tinyMCE.triggerSave();
                    }

                    toggleSubmit(  $("button:submit" , form ) ) ;
                    
                    var formData = getFormData();
                    var dataSubmit =  $( form ).serializeArray();

                    $.each( dataSubmit, function ( key, input ) {
                        formData.append( input.name, input.value ); 
                    });

                    makeAjax( formData, $("button:submit" , form )  )
                    return false; 
               } );
            }
            init( form );  
    }

   
    /////
    $(document).ready(function () {
        /// update  agency profile
        if(  $("#opalestate_ofe_front").length  > 0 ){
             new AgencyUpdateProfile( $("#opalestate_ofe_front") );
        }
        // update  agent profile
        if(  $("#opalestate_agt_front").length  > 0 ){  
             new AgencyUpdateProfile( $("#opalestate_agt_front") );
        }

        if(  $("#opalestate_user_front").length  > 0 ){   
             new AgencyUpdateProfile( $("#opalestate_user_front") );
        }

        if( $("#opalestate-add-team-form").length > 0 ){
             function formatRepo (repo) {
                if ( repo.loading ) {
                    return repo.text;
                }
                var markup = "<div class='select2-search-member clearfix'>" +
                "<div class='avatar'><img width=\"50\" src='" + repo.avatar_url + "' /></div>" +
                "<div class='member-meta'>" +
                  "<div class='member-title'>" + repo.full_name + "</div>";
                markup +=  "</div></div>";
                return markup;
            }

            function formatRepoSelection (repo) {
              return repo.full_name || repo.text;
            }
            function load_select2_member ( id, action ) {
                $( id ).select2({
                    width: '100%',
                    ajax: {
                        url: opalesateJS.ajaxurl+"?action="+action,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                          return {
                                q: params.term, // search term
                                page: params.page
                          };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                               return {
                                        results: data.items,
                                        pagination: {
                                            more: (params.page * 30) < data.total_count
                                        }
                                   };
                            },
                            cache: true
                         },
                        placeholder: 'Search for a repository',
                        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                        minimumInputLength: 1,
                        templateResult: formatRepo,
                        templateSelection: formatRepoSelection
                });
            }

            load_select2_member( ".opalesate-find-user", 'opalestate_search_property_users');
        }

    } ); 

})(jQuery);
  