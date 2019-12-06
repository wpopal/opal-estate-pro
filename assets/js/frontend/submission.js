jQuery( document ).ready( function ( $ ) {
    'use strict';

    var toggleSubmit = function ( _this ) {
        if ( $( _this ).attr( 'disabled' ) == 'disabled' ) {
            $( _this ).removeAttr( 'disabled' );
            $( _this ).find( 'i' ).remove();
        } else {
            $( _this ).attr( 'disabled', 'disabled' );
            $( _this ).append( '<i class="fa fa-spinner fa-spin"></i>' );
        }
    };

    $( '.opalestate-submission-tab' ).each( function () {
        var $submission_tab = $( this );
        var $submit_btn = $submission_tab.find( '[name=submit-cmb]' );
        var $next_btn = $( '.submission-next-btn' );
        var $back_btn = $( '.submission-back-btn' );
        var $tab_content = $submission_tab.find( '.opalestate-tab-content' );

        $submission_tab.find( '.tab-item' ).first().addClass( 'active' );
        $tab_content.first().addClass( 'active' );
        if ( $tab_content.length != 1 ) {
            $submit_btn.hide();
        } else {
            $next_btn.hide();
        }

        $submit_btn.on( 'click', function ( e ) {
            e.preventDefault();
            var empty_required_inputs = opalestate_get_empty_required_inputs( $submission_tab );
            if ( empty_required_inputs.length === 0 ) {
                $submit_btn.parents( 'form' ).submit();
            }
        } );

        /*
         $next_btn.click( function(){
         //    $submit_btn.click();

         return false;
         });
         */
        var submitFormFiles = function ( name, files ) {
            if( typeof(tinyMCE) != "undefined" ) {
                tinyMCE.triggerSave();
            }

            var formData = new FormData();
            formData.append( 'section', 'general' );

            $( '.cmb2-uploader-files' ).each( function () {
                var file_btn = $( 'input.select-file', this );

                var files = $( '.uploader-item-preview', this );

                var name = $( this ).data( 'name' );
                var issingle = $( this ).data( 'single' );
                $( files ).each( function ( i, element ) {
                    var file = $( this ).prop( 'file' );
                    if ( file ) {
                        if ( issingle ) {
                            formData.append( name, file );
                        } else {
                            formData.append( name + '[' + i + ']', file );
                        }
                    }
                } );
            } );

            var dataSubmit = $submit_btn.parents( 'form' ).serializeArray();

            $.each( dataSubmit, function ( key, input ) {
                formData.append( input.name, input.value );
            } );

            formData.append( 'action', 'opalestate_save_agency_data' );

            toggleSubmit( $submit_btn );
            $.ajax( {
                url: opalesateJS.ajaxurl,
                data: formData,
                type: 'POST',
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function ( response ) {
                    if ( response.status == true ) {
                        if ( response.redirect ) {
                            window.location.href = response.redirect;
                        }

                        var myToast = $.toast( {
                            heading: response.heading,
                            text: response.message,
                            icon: 'success',
                            position: 'bottom-right',
                            hideAfter: 5000,
                            showHideTransition: 'fade',
                        } );
                    } else {
                        toggleSubmit( $submit_btn );
                        var myToast = $.toast( {
                            heading: response.heading,
                            text: response.message,
                            icon: 'error',
                            position: 'bottom-right',
                            hideAfter: 5000,
                            showHideTransition: 'fade'
                        } );
                    }
                }
            } );
        };

        $submit_btn.parents( 'form' ).on( 'submit', function (e) {
            submitFormFiles();
            return false;
        } );

        // Clicking Next button
        $next_btn.on( 'click', function ( e ) {
            e.preventDefault();
            var $tab_content = $( this ).parents( '.opalestate-tab-content' );
            var empty_required_inputs = opalestate_get_empty_required_inputs( $tab_content );

            if ( empty_required_inputs.length === 0 ) {
                var $next_tab_content = $tab_content.next();
                if ( $next_tab_content.length != 0 ) {
                    $submission_tab.find( '.opalestate-tab-content' ).removeClass( 'active' );
                    $submission_tab.find( '.tab-item.active' )
                                   .removeClass( 'active' )
                                   .addClass( 'validated' )
                                   .addClass( 'passed' )
                                   .next()
                                   .addClass( 'active' );
                    $tab_content.addClass( 'validated' ).addClass( 'passed' );
                    $next_tab_content.addClass( 'active' );

                    $( 'html, body' ).animate( {
                        scrollTop: $next_tab_content.offset().top - 100
                    }, 500 );

                    // Show Save button if is last tab.
                    if ( $next_tab_content.is( ':last-child' ) ) {
                        $next_btn.hide();
                        $submit_btn.show();
                    }
                }
            }
        } );

        // Clicking Back button
        $back_btn.on( 'click', function ( e ) {
            e.preventDefault();
            var $tab_content = $( this ).parents( '.opalestate-tab-content' );

            $submission_tab.find( '.opalestate-tab-content' ).removeClass( 'active' );
            $submission_tab.find( '.tab-item.active' )
                           .removeClass( 'active' )
                           .removeClass( 'passed' )
                           .prev()
                           .addClass( 'active' );
            $tab_content.removeClass( 'active' );

            var $prev_tab_content = $tab_content.prev();

            if ( $prev_tab_content.length != 0 ) {
                $prev_tab_content.addClass( 'active' ).removeClass( 'passed' );
                $( 'html, body' ).animate( {
                    scrollTop: $prev_tab_content.offset().top - 100
                }, 500 );
            }

            $submit_btn.hide();
            $next_btn.show();
        } );

        $( '.tab-item' ).on( 'click', function ( e ) {
            e.preventDefault();
            var $el = $( this );
            var $prev_tab_item = $el.prev();
            if ( $el.hasClass( 'validated' ) ||
                ( $prev_tab_item.length != 0 && $prev_tab_item.hasClass( 'validated' ) &&
                    $prev_tab_item.hasClass( 'passed' ) ) ) {
                $submission_tab.find( '.opalestate-tab-content' ).removeClass( 'active' );
                $submission_tab.find( '.tab-item.active' ).removeClass( 'active' );
                var $tab_id = $el.attr( 'href' );
                var $prev_tab_content = $( $tab_id ).prev();
                var $next_tab_content = $( $tab_id ).next();

                if ( $prev_tab_content.length != 0 ) {
                    $back_btn.show();
                } else {
                    $back_btn.hide();
                }

                if ( $next_tab_content.length != 0 ) {
                    $next_btn.show();
                    $submit_btn.hide();
                } else {
                    $next_btn.hide();
                    $submit_btn.show();
                }

                $el.addClass( 'active' );
                $( $tab_id ).addClass( 'active' );
            }
        } );
    } );

    function opalestate_get_empty_required_inputs( el_wrapper ) {
        var empty_required_inputs = [];
        el_wrapper.find( 'input' ).each( function () {
            $( this ).removeClass( 'required' );
            $( this ).blur();

            if ( $( this ).prop( 'required' ) ) {
                if ( $( this ).val() == '' ) {
                    $( this ).addClass( 'required' );
                    $( this ).focus();
                    empty_required_inputs.push( $( this ) );
                }
            }
        } );

        return empty_required_inputs;
    }

    $( '.opalestate-tab-content input' ).each( function ( e ) {
        if ( $( this ).prop( 'required' ) ) {
            $( this ).on( 'input', function () {
                if ( $( this ).val() == '' ) {
                    $( this ).addClass( 'required' );
                    $( this ).focus();
                } else {
                    $( this ).removeClass( 'required' );
                }
            } );
        }
    } );
} );
