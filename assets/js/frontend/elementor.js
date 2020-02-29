(function ($ ) {
    "use strict";

    var carouselSlick = function( $scope , $selector, elementorFrontend ) {

 
        var slidesToShow = 0;
        
        var elementSettings =  $scope.data('settings');

        if( elementSettings === undefined ){
            var   elementSettings =  $scope.find(".elementor-opal-slick-slider").data('settings');
        }
        //   console.log( elementSettings );
        if( elementSettings === undefined ){
            return true; 
        }
        
        var slidesToShow =+ elementSettings.slides_to_show || 3,
        isSingleSlide = 1 === slidesToShow,
        breakpoints = elementorFrontend.config.breakpoints;

        var slickOptions = {
            slidesToShow: slidesToShow,
            autoplay: 'yes' === elementSettings.autoplay,
            autoplaySpeed: elementSettings.autoplay_speed,
            infinite: 'yes' === elementSettings.infinite,
            pauseOnHover: 'yes' === elementSettings.pause_on_hover,
            speed: elementSettings.speed,
            arrows: -1 !== [ 'arrows', 'both' ].indexOf( elementSettings.navigation ),
            dots: -1 !== [ 'dots', 'both' ].indexOf( elementSettings.navigation ),
            rtl: 'rtl' === elementSettings.direction,
            responsive: [
                {
                    breakpoint: breakpoints.lg,
                    settings: {
                        slidesToShow: +elementSettings.slides_to_show_tablet || ( isSingleSlide ? 1 : 2 ),
                        slidesToScroll: 1,
                    },
                },
                {
                    breakpoint: breakpoints.md,
                    settings: {
                        slidesToShow: +elementSettings.slides_to_show_mobile || 1,
                        slidesToScroll: 1,
                    },
                },
            ],
        };

        if ( isSingleSlide ) {
            slickOptions.fade = 'fade' === elementSettings.effect;
        } else {
            slickOptions.slidesToScroll = +elementSettings.slides_to_scroll;
        }

        var $carousel = $scope.find( $selector );
       //  $carousel.removeClass('products');
        $carousel.slick( slickOptions );
       //  $carousel.addClass('products');
    }

    $(window).on('elementor/frontend/init', function(){
	
	  	elementorFrontend.hooks.addAction( 'frontend/element_ready/opalestate-agent-collection.default', function( $scope ) {
	  		if( $scope.find(".elementor-opal-slick-slider") ) {
				carouselSlick( $scope, '.elementor-slick-slider-row.row-items', elementorFrontend );
			}
		} );

        elementorFrontend.hooks.addAction( 'frontend/element_ready/opalestate-agency-collection.default', function( $scope ) {
            if( $scope.find(".elementor-opal-slick-slider") ) {
                carouselSlick( $scope, '.elementor-slick-slider-row.row-items', elementorFrontend );
            }
        } );

        elementorFrontend.hooks.addAction( 'frontend/element_ready/opalestate-property-collection.default', function( $scope ) {
            if( $scope.find(".elementor-opal-slick-slider") ) {  
                carouselSlick( $scope, '.elementor-slick-slider-row.row-items' , elementorFrontend );
            }
        } );

         elementorFrontend.hooks.addAction( 'frontend/element_ready/opalestate-category-list.default', function( $scope ) {
            if( $scope.find(".elementor-opal-slick-slider") ) {  
                carouselSlick( $scope, '.elementor-slick-slider-row.row-items' , elementorFrontend );
            }
        } );
	});
})( jQuery ); 
