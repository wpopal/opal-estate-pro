(function ($) {
    "use strict";

    var carouselSlick = function ($scope, $selector, elementorFrontend) {


        var slidesToShow = 0;

        var elementSettings = $scope.data('settings');

        if (elementSettings === undefined) {
            var elementSettings = $scope.find(".elementor-opal-slick-slider").data('settings');
        }

        if (elementSettings === undefined) {
            return true;
        }

        var slidesToShow = +elementSettings.slides_to_show || 3, isSingleSlide = 1 === slidesToShow,
            breakpoints = elementorFrontend.config.breakpoints;

        var slickOptions = {
            slidesToShow: slidesToShow,
            autoplay: 'yes' === elementSettings.autoplay,
            autoplaySpeed: elementSettings.autoplay_speed,
            infinite: 'yes' === elementSettings.infinite,
            pauseOnHover: 'yes' === elementSettings.pause_on_hover,
            speed: elementSettings.speed,
            arrows: -1 !== ['arrows', 'both'].indexOf(elementSettings.navigation),
            dots: -1 !== ['dots', 'both'].indexOf(elementSettings.navigation),
            rtl: 'rtl' === elementSettings.direction,
        };
        const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

        slickOptions.responsive = [];
        Object.keys(elementorBreakpoints).reverse().forEach(breakpointName => {
            slickOptions.responsive.push({
                breakpoint: elementorBreakpoints[breakpointName].value,
                settings: {
                    slidesToShow: +elementSettings['slides_to_show_' + breakpointName],
                }
            });
        });

        if (isSingleSlide) {
            slickOptions.fade = 'fade' === elementSettings.effect;
        } else {
            slickOptions.slidesToScroll = +elementSettings.slides_to_scroll;
        }

        var $carousel = $scope.find($selector);
        $carousel.slick(slickOptions);
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/opalestate-agent-collection.default', function ($scope) {
            if ($scope.find(".elementor-opal-slick-slider")) {
                carouselSlick($scope, '.elementor-slick-slider-row.row-items', elementorFrontend);
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/opalestate-agency-collection.default', function ($scope) {
            if ($scope.find(".elementor-opal-slick-slider")) {
                carouselSlick($scope, '.elementor-slick-slider-row.row-items', elementorFrontend);
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/opalestate-property-collection.default', function ($scope) {
            if ($scope.find(".elementor-opal-slick-slider")) {
                carouselSlick($scope, '.elementor-slick-slider-row.row-items', elementorFrontend);
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/opalestate-category-list.default', function ($scope) {
            if ($scope.find(".elementor-opal-slick-slider")) {
                carouselSlick($scope, '.elementor-slick-slider-row.row-items', elementorFrontend);
            }
        });
    });
})(jQuery);
