jQuery(document).ready(function ($) {
    $('#show-user-sidebar-btn').click(function () {
        $('body').toggleClass('active');
    });

    $('.more-options-label, .form-item--types .group-item, .cmb2-checkbox-list.cmb2-list li').each(function () {
        $(this).append('<span class="custom-checkbox-label"></span>');
    });

    $('.opalestate-tooltip').each(function () {
        if ($(this).tooltipster) {
            $(this).tooltipster({
                side: ['bottom', 'top', 'right', 'left']
            });
        }
    });

    /***/

    // Social login.
    $('.js-opal-google-login').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST', url: opalesateJS.ajaxurl, data: {
                'action': 'opalestate_ajax_redirect_google_login_link'
            }, success: function (results) {
                window.location.href = results.data;
            }, error: function (errorThrown) {
                // TODO:
            }
        });
    });

    $('.js-opal-facebook-login').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST', url: opalesateJS.ajaxurl, data: {
                'action': 'opalestate_ajax_redirect_facebook_login_link'
            }, success: function (results) {
                window.location.href = results.data;
            }, error: function (errorThrown) {
                // TODO:
            }
        });
    });

    if ($('.opalestate-swiper-play').length > 0) {
        var play_swiper_sliders = function () {
            if ($('.opalestate-swiper-play').length > 0) {
                $('.opalestate-swiper-play').each(function () {
                    var option = $(this).data('swiper');

                    if (option) {
                        option = $.extend({
                            navigation: {
                                nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev'
                            }, slidesPerView: 3, spaceBetween: 30, loop: true, pagination: {
                                el: '.swiper-pagination', clickable: true,
                            }, breakpoints: {
                                1024: {
                                    slidesPerView: 2, spaceBetween: 30
                                }, 768: {
                                    slidesPerView: 1, spaceBetween: 10
                                }, 640: {
                                    slidesPerView: 1, spaceBetween: 10
                                }, 320: {
                                    slidesPerView: 1, spaceBetween: 10
                                }
                            }
                        }, option);

                        if (option.thumbnails_nav) {
                            var ioption = $(option.thumbnails_nav).data('swiper');

                            ioption.breakpoint = {
                                1024: {
                                    slidesPerView: 2, spaceBetween: 30
                                }, 768: {
                                    slidesPerView: 1, spaceBetween: 10
                                }, 640: {
                                    slidesPerView: 1, spaceBetween: 10
                                }, 320: {
                                    slidesPerView: 1, spaceBetween: 10
                                }
                            };

                            var iswiper = new Swiper(option.thumbnails_nav, ioption);

                            option.thumbs = {
                                swiper: iswiper
                            };

                        }

                        var swiper = new Swiper(this, option);
                    }
                });
            }
        };
        play_swiper_sliders();
        $(document).ajaxComplete(function () {
            play_swiper_sliders();
        });
    }

    ////////
    $('.opalestate-scroll-elements a').on('click', function (e) {
        e.preventDefault();
        if ($($(this).attr('href')).length) {
            $('.opalestate-scroll-elements a').removeClass('active');
            $(this).addClass('active');

            $('html, body').animate({
                scrollTop: $($(this).attr('href')).offset().top - 80,
            }, 500, 'linear');
        }
    });
    var header = $('.keep-top-bars');
    if (header.length > 0) {
        var sticky = header.offset().top;
        $(window).scroll(function () {
            var scroll = $(window).scrollTop();
            if (scroll >= sticky) {
                header.addClass('floating-keep-top');
            } else {
                header.removeClass('floating-keep-top');
            }
        });
    }

    ////
    $('.opalestate-gallery').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image', gallery: {
                enabled: true
            }
        });
        var $_this = this;
        $('.show-first-photo').click(function () {
            $('a', $_this).first().click();
        });
        $('.show-last-photo').click(function () {
            $('a', $_this).last().click();
        });
    });

    //////
    $('.opalestate_rating').each(function () {
        $(this)
            .hide()
            .before('<p class="opalestate-stars stars">\
                  <span>\
                    <a class="star-1" href="#">1</a>\
                    <a class="star-2" href="#">2</a>\
                    <a class="star-3" href="#">3</a>\
                    <a class="star-4" href="#">4</a>\
                    <a class="star-5" href="#">5</a>\
                  </span>\
                </p>');
    });

    $('body')
        // Star ratings for comments
        .on('click', '.comment-form-rating p.opalestate-stars a', function () {
            var $star = $(this), $rating = $(this).closest('.comment-form-rating').find('.opalestate_rating'),
                $container = $(this).closest('.stars');

            $rating.val($star.text());
            $star.siblings('a').removeClass('active');
            $star.addClass('active');
            $container.addClass('selected');

            return false;
        })
        .on('click', '#respond #submit', function () {
            var $rating = $(this).closest('#respond').find('.opalestate_rating');
            var rating = $rating.val();

            if ($rating.length > 0 && !rating) {
                window.alert('Require rating!');

                return false;
            }
        });

    // sticky ////
    // $( '.opalestate-sticky-column' ).stick_in_parent();

    // var window_width = $( window ).width();
    //
    // if ( window_width < 768 ) {
    //     $( '.opalestate-sticky-column' ).trigger( 'sticky_kit:detach' );
    // } else {
    //     make_sticky();
    // }
    //
    // $( window ).resize( function () {
    //
    //     window_width = $( window ).width();
    //
    //     if ( window_width < 768 ) {
    //         $( '.opalestate-sticky-column' ).trigger( 'sticky_kit:detach' );
    //     } else {
    //         make_sticky();
    //     }
    //
    // } );
    //
    // function make_sticky() {
    //     $( '.opalestate-sticky-column' ).stick_in_parent();
    // }

    ////
    $('.input-group-number').each(function () {
        var _input = $('input', this);
        if (parseInt(_input.val()) < 0) {
            _input.val(0);
        }
        $('.btn-actions > span', this).click(function () {

            var _check = function () {
                return parseInt(_input.val()) < 0 ? 0 : parseInt(_input.val());
            };

            if ($(this).hasClass('btn-plus')) {
                _val = _check() + 1;
            } else {
                if (_check() === 0) {
                    _val = _check();
                } else {
                    _val = _check() - 1;
                }
            }

            _input.val(_val);
            _input.change();
        });

    });

    $('select.form-control , .cmb2-wrap select, .form-row select').select2({
        width: '100%', // minimumResultsForSearch: 20
    });

    function opalCollapse() {
        $('.opal-collapse-button').on('click', function () {
            var $el = $(this), data = $el.data('collapse'), $el_data = $(data), speed = 250;

            if ($el.data('speed') && $el.data('speed') > 0) {
                speed = $el.data('speed');
            }

            if ($el_data.is(':visible')) {
                $el_data.slideUp(speed);
                $el.removeClass('show');
                $el_data.removeClass('show');
            } else {
                $el.addClass('show');
                $el_data.addClass('show');
                $el_data.slideDown(speed);
            }

            return false;
        });
    }

    opalCollapse();

    /************************/

    $('.opalestate-tab .tab-item').click(function (event) {
        event.preventDefault();
        $(this).parent().find(' .tab-item').removeClass('active');
        $(this).addClass('active');

        $($(this).attr('href')).parent().children('.opalestate-tab-content').removeClass('active');
        $($(this).attr('href')).addClass('active');
    });

    $('.opalestate-tab').each(function () {
        $(this).find('.tab-item').first().click();
    });

    /**
     * Click to show body popup
     */
    $('.popup-head').on('click', function (e) {
        var $popup = $(this).closest('.opalestate-popup');
        $popup.toggleClass('active');
    });

    $('.popup-close').on('click', function (e) {
        var $popup = $(this).closest('.opalestate-popup');
        $popup.removeClass('active');
    });

    /**
     * Login form
     **/
    $('form.opalestate-login-form').on('submit', function () {
        var $form = $(this);
        $.ajax({
            type: 'POST',
            url: opalesateJS.ajaxurl,
            dataType: 'json',
            data: $(this).serialize() + '&ajax=1&action=opalestate_login_form', // serializes the form's elements.
            success: function (data) {
                if (data.status == true) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }
                if (data.message) {
                    $form.find('.opalestate-notice').remove();
                    $form.prepend(data.message);
                }
            }
        });
        return false;
    });

    /**
     * Register form
     **/
    $('form.opalestate-register-form').on('submit', function () {
        var $form = $(this);
        $.ajax({
            type: 'POST',
            url: opalesateJS.ajaxurl,
            dataType: 'json',
            data: $(this).serialize() + '&ajax=1&action=opalestate_register_form', // serializes the form's elements.
            success: function (data) {
                if (data.status == true) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }
                if (data.message) {
                    $form.find('.opalestate-notice').remove();
                    $form.prepend(data.message);
                }
            }
        });
        return false;
    });

    /**
     * AJAX ACTION
     */

    $('#opalestate_user_frontchangepass').submit(function (e) {
        var $this = $(this);
        $('.alert', $this).remove();
        $.ajax({
            type: 'POST',
            url: opalesateJS.ajaxurl,
            dataType: 'json',
            data: $(this).serialize() + '&action=opalestate_save_changepass', // serializes the form's elements.
            success: function (data) {
                if (data.status == false) {
                    $this.find('.form-table')
                        .prepend($('<p class="alert alert-danger">' + data.message + '</p>'));
                } else {
                    $this.find('.form-table').prepend($('<p class="alert alert-info">' + data.message + '</p>'));
                    $('input[type="text"]', $this).val('');
                    setTimeout(function () {
                        $('.alert', $this).remove();
                    }, 1000);
                }
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $('body').delegate('.opalestate-popup-button', 'click', function () {
        var $target = $(this).data('target');
        $.magnificPopup.open({
            items: {
                src: $target
            }
        });
        return false;
    });

    // open login form
    $(document).on('opalestate:login', function () {
        if ($('#opalestate-user-form-popup')) {
            $.magnificPopup.open({
                items: {
                    src: '#opalestate-user-form-popup'
                }, mainClass: 'mfp-with-zoom', // this class is for CSS animation below
                zoom: {
                    enabled: true
                }
            });
        }
    });

    $('body').delegate('.opalestate-need-login', 'click', function () {
        $(document).trigger('opalestate:login', [true]);
        return false;
    });
    //// ajax favorite
    $('body').delegate('.property-toggle-favorite', 'click', function () {
        var $this = $(this);
        if ($(this).hasClass('opalestate-need-login')) {
            return;
        }
        $.ajax({
            type: 'POST',
            url: opalesateJS.ajaxurl,
            data: 'property_id=' + $(this).data('property-id') + '&action=opalestate_toggle_status', // serializes
                                                                                                     // the form's
                                                                                                     // elements.
            success: function (data) {
                if (data) {
                    $this.replaceWith($(data));
                }
            }
        });
    });

    if ($('.opalestate-datepicker').length > 0) {
        $('.opalestate-datepicker').datepicker({minDate: 0});
    }

    $('.list-property-status li').click(function () {
        $('.opalestate-search-form [name=status]').val($(this).data('id'));
        $('.list-property-status li').removeClass('active');
        $(this).addClass('active');
    });
    if ($('.opalestate-search-form [name=status]').val() > 0) {
        var id = $('.opalestate-search-form [name=status]').val();
        $('.list-property-status li').removeClass('active');
        $('.list-property-status [data-id=' + id + ']').addClass('active');
    }

    /*-----------------------------------------------------------------------------------*/
    $('.opal-slide-ranger').each(function () {
        var _this = this;
        var unit = $(this).data('unit');
        var decimals = $(this).data('decimals');
        var min = $('.slide-ranger-bar', this).data('min');
        var max = $('.slide-ranger-bar', this).data('max');
        var mode = $('.slide-ranger-bar', this).data('mode');
        var start = $('.slide-ranger-bar', this).data('start');

        var imin = $('.slide-ranger-min-input', this).val();
        var imax = $('.slide-ranger-max-input', this).val();
        var slider = $('.slide-ranger-bar', this).get(0);
        var unit_pos = $(this).data('unitpos');
        var unit_thousand = $(this).data('thousand');
        var step = $(this).data('step');
        var format = $(this).data('format');
        step = step ? step : 1;
        format = format ? format : 1;

        var config_format = {
            decimals: decimals, thousand: unit_thousand, step: step,
        };

        if (unit_pos == 'prefix') {
            config_format.prefix = ' ' + unit + ' ';
        } else {
            config_format.postfix = ' ' + unit + ' ';
        }

        var nummm = wNumb(config_format);

        var istart = [imin, imax];
        if (mode && mode == 1 && (start || start == 0)) {
            istart = [start];
        }

        var options = {
            range: {
                'min': [min], 'max': [max]
            }, step: step, connect: true, start: istart, direction: opalesateJS.rtl == 'true' ? 'rtl' : 'ltr',
        };

        if (format) {
            options.format = nummm;
        }

        noUiSlider.create(slider, options);

        slider.noUiSlider.on('update', function (values, handle) {
            var val = values[handle];
            if (handle == 0) {
                $('.slide-ranger-min-label', _this).text(val);
                $('.slide-ranger-min-input', _this).val(nummm.from(val));
            } else {
                $('.slide-ranger-max-label', _this).text(val);
                $('.slide-ranger-max-input', _this).val(nummm.from(val));
            }
        });

        slider.noUiSlider.on('end', function (values, handle) {
            var val = values[handle];
            if (handle == 0) {
                $('.slide-ranger-min-input', _this).change();
            } else {
                $('.slide-ranger-max-input', _this).change();
            }
        });
    });

    $('input[name="geo_radius"]').on('change', function (e) {
        var val = $(this).val();
        $(this).closest('.opalestate-popup').find('.radius-status__number').html(val);
    });

    //////***Search Search **/
    $('body').delegate('#opalestate-save-search-form', 'submit', function () {
        var params = window.location.search.substring(1);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: opalesateJS.ajaxurl,
            data: 'params=' + encodeURIComponent(params) + '&' + $(this).serialize() + '&action=opalestate_ajx_save_search',
            success: function (data) {
                $('#opalestate-save-search-form .alert').remove();
                $('#opalestate-save-search-form input').val('');
                $('#opalestate-save-search-form')
                    .append('<div class="opalestate-message-notify msg-status-success" style="margin-top:20px">' + data.message + '</div>');
                $('#opalestate-save-search-form .alert').delay(5000).queue(function () {
                    $('#opalestate-save-search-form .alert').remove();
                });
            }
        });
        return false;
    });

    $('.ajax-load-properties').delegate('.pagination li', 'click', function () {
        var $content = $(this).parents('.ajax-load-properties');
        var type = $(this).parents('.ajax-load-properties').data('type');
        if (type === 'agent') {
            $.ajax({
                type: 'POST',
                url: opalesateJS.ajaxurl,
                data: location.search.substr(1) + '&action=get_agent_property&paged=' + $(this).data('paged') + '&id=' + $content.data('id'),
                success: function (data) {
                    if (data) {
                        $content.html(data);
                        $('html, body').animate({
                            scrollTop: $('.ajax-load-properties').offset().top - 100
                        }, 500);
                    }
                }
            });
        } else if (type === 'agency') {
            $.ajax({
                type: 'POST',
                url: opalesateJS.ajaxurl,
                data: location.search.substr(1) + '&action=get_agency_property&paged=' + $(this).data('paged') + '&id=' + $content.data('id'),
                success: function (data) {
                    if (data) {
                        $content.html(data);
                        $('html, body').animate({
                            scrollTop: $('.ajax-load-properties').offset().top - 100
                        }, 500);
                    }
                }
            });
        }

        return false;
    });

    if ($('.opalestate-sticky').length > 0) {
        $('.opalestate-sticky').each(function () {
            $(this).stick_in_parent($(this).data());
        });
    }
});
