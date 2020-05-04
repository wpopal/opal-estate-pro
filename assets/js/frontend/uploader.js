/* global tinymce, wpCookies, autosaveL10n, switchEditors */
// Back-compat
window.opalestate_uploader = function() {
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
     *  The object with all functions for autosave.
     */

    function opalestate_uploader() {

        $document = $( document );

        function is_image_file( file ) {
            const acceptedImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
            return file && acceptedImageTypes.includes(file['type'])
        }

        function check_number_files( file , i   ) {
            if( is_image_file(file) ) {
                if( i+1 > opalesateJS.mfile_image ){
                    return false;
                }
            } else {
                if( i+1 > opalesateJS.mfile_other ){
                    return false;
                }
            }
            return true;
        }

        function check_filesize (  file , i  ) {

            if( is_image_file(file) ) {
                if( file.size > opalesateJS.size_image ){
                    var myToast = $.toast({
                        heading: file.name,
                        text: opalesateJS.error_upload_size,
                        icon: 'error',
                        position:  'bottom-right',
                         hideAfter: 3500,
                         showHideTransition: 'fade'
                    });
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }

        }
        /**
         *
         */
        function trigger_button_upload(){

            var handleUpload = function ( _container ){

                var file_btn      = $( 'input.select-file', _container  );
                var allow_files   = [];
              //  var all_selected = [];
                var name      = $(this).data( 'name' );
                var issingle  = $(_container).data('single');
                var show_icon = $(_container).data( 'show-icon' );

                var on_select_files = function ( files, _container ) {

                    if ( window.File && window.FileList && window.FileReader ) {
                        $(files).each( function( i, file ){

                            if( check_number_files( file, i+$(".uploader-item-preview",_container).length ) == false ){
                                return ;
                            }
                            if( check_filesize( file, i )  ) {
                                var picReader = new FileReader();
                                picReader.addEventListener("load", function ( event ) {
                                    var input = '<div class="uploader-item-preview">';
                                    var picFile = event.target;
                                    if ( picFile.result ) {
                                        if( show_icon == 1 ) {
                                            input += '<div class="inner preview-icon"><span class="btn-close fa fa-close"></span><i class="fas fa-paperclip"></i> '+ file.name +' </div>';
                                        } else {
                                            input += '<div class="inner preview-image"><span class="btn-close' +
                                                ' fa fa-close"></span><img src="'+picFile.result+'"></div>';
                                        }

                                    }
                                    input += '</div>';
                                    var a = $(input) ;
                                    if( issingle ){
                                        $( ".uploader-item-preview", _container ).remove();
                                        all_selected = [];
                                    }
                                    $(  _container ).prepend( a );
                                    a.prop( 'file', file  );
                                } );
                                picReader.readAsDataURL( file );
                            }
                        } );
                    }
               };

               file_btn.on("change", function( event ){
                    on_select_files( event.target.files, _container, allow_files );

                } );

                $( _container ).on( "click", ".btn-close", function(){
                    if( confirm(opalesateJS.confirmed ) ){
                        if( $("input",  $(this).parent().parent()).length  ){
                            var rinput = $("<input type=\"hidden\" name=\"remove_image_id[]\" value=\""+ $("input",  $(this).parent().parent()).val() +"\">");
                            $(_container).append( rinput );
                        }

                        $(this).parent().parent().remove();
                    }
                } );

                $( ".button-placehold", _container ).click( function(){
                    file_btn.trigger("click");
                } );
            }

            $(".cmb2-uploader-files").each( function(){
                handleUpload( this )
            } );

            // fix for submittion form
            window.CMB2 = window.CMB2 || {};
            window.CMB2.metabox().find('.cmb-repeatable-group').on( 'cmb2_add_row', function(i, row ) {
                var _container = $( row );
                if(  $(".cmb2-uploader-files", _container ).length ) {
                    $( ".uploader-item-preview", _container ).remove();
                    $(".cmb2-uploader-files", _container ).each( function(){
                        var name = $( 'input', this ).attr('name');
                        $( this ).attr('data-name', name );
                        $(this).data( 'name', name );
                        handleUpload( this );
                    });
                }
            } );
        }

        function upload_attachments( name, files ) {

            alert( name );
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
        $document.on( 'body', function( event, editor ) {

        }).ready( function() {
            trigger_button_upload();
        });

        return {

        };
    }

    /** @namespace wp */
    window.wp = window.wp || {};
    window.wp.opalestate_uploader = opalestate_uploader();

}( jQuery, window ));
