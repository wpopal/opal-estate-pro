;( function ( $, settings ) {
    'use strict';

    if ( window.Opalestate === undefined ) {
        window.Opalestate = {};
    }

    /**
     * GooglemapSearch
     */
    var GooglemapSingle = Opalestate.GooglemapSingle = function ( data, id ) {
        /**
         * Create Google Map In Single Property Only
         */
        var initializePropertyMap = function ( data, id ) {

            var propertyMarkerInfo = data;
            var enable = true;
            var url = propertyMarkerInfo.icon;
            var size = new google.maps.Size( 42, 57 );

            var allMarkers = [];

            var setMapOnAll = function ( markers, map ) {
                for ( var i = 0; i < markers.length; i++ ) {
                    markers[ i ].setMap( map );
                }
            };
            // retina
            if ( window.devicePixelRatio > 1.5 ) {
                if ( propertyMarkerInfo.retinaIcon ) {
                    url = propertyMarkerInfo.retinaIcon;
                    size = new google.maps.Size( 83, 113 );
                }
            }

            var propertyLocation = new google.maps.LatLng( propertyMarkerInfo.latitude, propertyMarkerInfo.longitude );
            var propertyMapOptions = {
                center: propertyLocation,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false
            };

            if ( ( typeof opalestateGmap !== 'undefined' ) ) {
                switch ( opalestateGmap.style ) {
                    case 'standard':
                        propertyMapOptions.styles = GoogleMapStyles.standard;
                        break;
                    case 'silver':
                        propertyMapOptions.styles = GoogleMapStyles.silver;
                        break;
                    case 'retro':
                        propertyMapOptions.styles = GoogleMapStyles.retro;
                        break;
                    case 'dark':
                        propertyMapOptions.styles = GoogleMapStyles.dark;
                        break;
                    case 'night':
                        propertyMapOptions.styles = GoogleMapStyles.night;
                        break;
                    case 'aubergine':
                        propertyMapOptions.styles = GoogleMapStyles.aubergine;
                        break;
                    case 'custom':
                        if ( opalestateGmap.custom_style != '' ) {
                            propertyMapOptions.styles = $.parseJSON( opalestateGmap.custom_style );
                        }
                        break;
                }
            }

            var propertyMap = new google.maps.Map( document.getElementById( id ), propertyMapOptions );

            var createMarker = function ( position, icon ) {

                var image = {
                    url: icon,
                    size: size,
                    scaledSize: new google.maps.Size( 32, 57 ),
                    origin: new google.maps.Point( 0, 0 ),
                    anchor: new google.maps.Point( 21, 56 )
                };

                var _marker = new google.maps.Marker( {
                    map: propertyMap,
                    position: position,
                    icon: image
                } );
                return _marker;
            };

            var infowindow = new google.maps.InfoWindow();

            createMarker( propertyLocation, url );

            /**
             *  Places near with actived types
             */
            if ( enable ) {
                var $navs = $( '#' + id ).parent().find( '.property-search-places' );
                $( ' .btn-map-search', $navs ).unbind( 'click' ).bind( 'click', function () {
                    var service = new google.maps.places.PlacesService( propertyMap );
                    var type = $( this ).data( 'type' );
                    var $this = $( this ).parent();

                    var icon = {
                        url: opalesateJS.mapiconurl + $( this ).data( 'icon' ),
                        scaledSize: new google.maps.Size( 28, 28 ),
                        anchor: new google.maps.Point( 21, 16 ),
                        origin: new google.maps.Point( 0, 0 )
                    };

                    if ( !allMarkers[ type ] || allMarkers[ type ].length <= 0 ) {
                        var markers = [];
                        var bounds = propertyMap.getBounds();

                        var $this = $( this );

                        service.nearbySearch( {
                            location: propertyLocation,
                            radius: 2000,
                            bounds: bounds,
                            type: type
                        }, callbackNearBy );

                        function callbackNearBy( results, status ) {
                            if ( status === google.maps.places.PlacesServiceStatus.OK ) {
                                for ( var i = 0; i < results.length; i++ ) {
                                    createMarkerNearBy( results[ i ] );
                                }

                                $( '.nearby-counter', $this ).remove();
                                $( 'span', $this )
                                    .append( $( '<em class="nearby-counter">' + markers.length + '</em>' ) );
                                allMarkers[ type ] = markers;
                            }
                        }

                        function abc() {
                            if ( status === google.maps.places.PlacesServiceStatus.OK ) {
                                for ( var i = 0; i < results.length; i++ ) {
                                    var place = results[ i ];
                                    var marker = new google.maps.Marker( {
                                        map: propertyMap,
                                        position: place.geometry.location,
                                        icon: icon,
                                        visible: true
                                    } );

                                    marker.setMap( propertyMap );

                                    google.maps.event.addListener( marker, 'click', function () {

                                        infowindow.setContent( place.name );

                                        infowindow.open( propertyMap, this );
                                    } );

                                    markers.push( marker );
                                }
                                $( '.nearby-counter', $this ).remove();
                                $( 'span', $this )
                                    .append( $( '<em class="nearby-counter">' + markers.length + '</em>' ) );
                                allMarkers[ type ] = markers;
                            }
                        }

                        function createMarkerNearBy( place ) {
                            var placeLoc = place.geometry.location;
                            var marker = new google.maps.Marker( {
                                map: propertyMap,
                                position: place.geometry.location,
                                icon: icon,
                                visible: true
                            } );

                            marker.setMap( propertyMap );

                            google.maps.event.addListener( marker, 'click', function () {
                                infowindow.setContent( place.name );
                                infowindow.open( propertyMap, this );
                            } );

                            markers.push( marker );
                        }
                    } else {
                        for ( var i = 0; i < allMarkers[ type ].length; i++ ) {
                            allMarkers[ type ][ i ].setMap( null );
                        }
                        allMarkers[ type ] = [];
                    }

                    $( this ).toggleClass( 'active' );
                } );
            }
        };
        initializePropertyMap( data, id );
    };

    var GoogleMapSearch = Opalestate.GooglemapSingle = function ( data ) {
        var initializePropertiesMap = function ( properties ) {
            // Properties Array
            var mapOptions = {
                zoom: 12,
                maxZoom: 16,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                panControl: false,
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: true,
                overviewMapControl: false,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL,
                    position: google.maps.ControlPosition.RIGHT_TOP
                },
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                }
            };

            if ( ( typeof opalestateGmap !== 'undefined' ) ) {
                switch ( opalestateGmap.style ) {
                    case 'standard':
                        mapOptions.styles = GoogleMapStyles.standard;
                        break;
                    case 'silver':
                        mapOptions.styles = GoogleMapStyles.silver;
                        break;
                    case 'retro':
                        mapOptions.styles = GoogleMapStyles.retro;
                        break;
                    case 'dark':
                        mapOptions.styles = GoogleMapStyles.dark;
                        break;
                    case 'night':
                        mapOptions.styles = GoogleMapStyles.night;
                        break;
                    case 'aubergine':
                        mapOptions.styles = GoogleMapStyles.aubergine;
                        break;
                    case 'custom':
                        if ( opalestateGmap.custom_style != '' ) {
                            mapOptions.styles = $.parseJSON( opalestateGmap.custom_style );
                        }
                        break;
                }
            }

            var map = new google.maps.Map( document.getElementById( 'opalestate-map-preview' ), mapOptions );

            var bounds = new google.maps.LatLngBounds();

            // Loop to generate marker and infowindow based on properties array
            var markers = new Array();

            for ( var i = 0; i < properties.length; i++ ) {

                var url = properties[ i ].icon;
                var size = new google.maps.Size( 42, 57 );
                if ( window.devicePixelRatio > 1.5 ) {
                    if ( properties[ i ].retinaIcon ) {
                        url = properties[ i ].retinaIcon;
                        size = new google.maps.Size( 83, 113 );
                    }
                }

                var image = {
                    url: url,
                    size: size,
                    scaledSize: new google.maps.Size( 30, 51 ),
                    origin: new google.maps.Point( 0, 0 ),
                    anchor: new google.maps.Point( 21, 56 )
                };

                markers[ i ] = new google.maps.Marker( {
                    position: new google.maps.LatLng( properties[ i ].lat, properties[ i ].lng ),
                    map: map,
                    icon: image,
                    title: properties[ i ].title,
                    animation: google.maps.Animation.DROP,
                    visible: true
                } );

                bounds.extend( markers[ i ].getPosition() );

                var boxText = document.createElement( 'div' );
                var pricelabel = '';

                if ( properties[ i ].pricelabel ) {
                    pricelabel = ' / ' + properties[ i ].pricelabel;
                }

                boxText.className = 'map-info-preview media';

                function opalestate_get_property_icon( $key ) {
                    var $icon = $key;
                    switch ( $key ) {
                        case 'builtyear':
                            $icon = 'fas fa-calendar';
                            break;
                        case 'parking':
                            $icon = 'fas fa-car';
                            break;
                        case 'bedrooms':
                            $icon = 'fas fa-bed';
                            break;
                        case 'bathrooms':
                            $icon = 'fas fa-bath';
                            break;
                        case 'plotsize':
                            $icon = 'fas fa-map';
                            break;
                        case 'areasize':
                            $icon = 'fas fa-arrows-alt';
                            break;
                        case 'orientation':
                            $icon = 'fas fa-compass';
                            break;
                        case 'livingrooms':
                            $icon = 'fas fa-tv';
                            break;
                        case 'kitchens':
                            $icon = 'fas fa-utensils';
                            break;
                        case 'amountrooms':
                            $icon = 'fas fa-building';
                            break;
                        default:
                            $icon = $key;
                            break;
                    }

                    return $icon;
                }

                var meta = '<ul class="list-inline property-meta-list">';
                if ( properties[ i ].metas ) {
                    for ( var x in properties[ i ].metas ) {
                        var m = properties[ i ].metas[ x ];
                        meta += '<li><i class="icon-property-' + x + ' ' + opalestate_get_property_icon( x ) +
                            '"></i>' + m.value + '<span' +
                            ' class="label-property">' + m.label + '</span></li>';
                    }
                }
                meta += '</ul>';

                boxText.innerHTML = '<div class="media-top"><a class="thumb-link" href="' + properties[ i ].url + '">' +
                    '<img class="prop-thumb" src="' + properties[ i ].thumb + '" alt="' + properties[ i ].title +
                    '"/>' +
                    '</a>' + properties[ i ].status + '</div>' +
                    '<div class="info-container media-body">' +
                    '<h5 class="prop-title"><a class="title-link" href="' + properties[ i ].url + '">' +
                    properties[ i ].title +
                    '</a></h5><p class="prop-address"><em>' + properties[ i ].address +
                    '</em></p><p><span class="price text-primary">' + properties[ i ].pricehtml + pricelabel +
                    '</span></p>' + meta + '</div>' + '<div class="arrow-down"></div>';

                var myOptions = {
                    content: boxText,
                    disableAutoPan: true,
                    maxWidth: 0,
                    alignBottom: true,
                    pixelOffset: new google.maps.Size( -122, -48 ),
                    zIndex: null,
                    closeBoxMargin: '0 0 -16px -16px',
                    closeBoxURL: opalesateJS.mapiconurl + 'close.png',
                    infoBoxClearance: new google.maps.Size( 1, 1 ),
                    isHidden: false,
                    pane: 'floatPane',
                    enableEventPropagation: false
                };

                var ib = new InfoBox( myOptions );

                attachInfoBoxToMarker( map, markers[ i ], ib, i );
            }

            var last = null;

            $( 'body' ).delegate( '[data-related="map"]', 'mouseenter', function () {
                if ( $( this ).hasClass( 'map-active' ) ) {
                    return true;
                }

                var i = $( this ).data( 'id' );
                $( '[data-related="map"]' ).removeClass( 'map-active' );
                $( this ).addClass( 'active' );
                map.setZoom( 65536 );//  alert( scale );

                if ( markers[ i ] ) {
                    var marker = markers[ i ];
                    google.maps.event.trigger( markers[ i ], 'click' );

                    var scale = Math.pow( 2, map.getZoom() );
                    var offsety = ( ( 100 / scale ) || 0 );
                    var projection = map.getProjection();
                    var markerPosition = marker.getPosition();
                    var markerScreenPosition = projection.fromLatLngToPoint( markerPosition );
                    var pointHalfScreenAbove = new google.maps.Point( markerScreenPosition.x,
                        markerScreenPosition.y - offsety );
                    var aboveMarkerLatLng = projection.fromPointToLatLng( pointHalfScreenAbove );
                    map.setZoom( scale );
                    map.setCenter( aboveMarkerLatLng );

                }
                return false;
            } );

            map.fitBounds( bounds );

            /* Marker Clusters */
            var markerClustererOptions = {
                ignoreHidden: true,
                maxZoom: 14,
                styles: [
                    {
                        textColor: '#000000',
                        url: opalesateJS.mapiconurl + 'cluster-icon.png',
                        height: 51,
                        width: 30
                    } ]
            };

            var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );

            function attachInfoBoxToMarker( map, marker, infoBox, i ) {

                google.maps.event.addListener( marker, 'click', function () {

                    if ( $( '[data-related="map"]' ).filter( '[data-id="' + i + '"]' ).length > 0 ) {
                        var $m = $( '[data-related="map"]' ).filter( '[data-id="' + i + '"]' );
                        $( '[data-related="map"]' ).removeClass( 'map-active' );
                        $m.addClass( 'map-active' );
                    }

                    if ( last != null ) {
                        last.close();
                    }

                    var scale = Math.pow( 2, map.getZoom() );
                    var offsety = ( ( 100 / scale ) || 0 );
                    var projection = map.getProjection();
                    var markerPosition = marker.getPosition();
                    var markerScreenPosition = projection.fromLatLngToPoint( markerPosition );
                    var pointHalfScreenAbove = new google.maps.Point( markerScreenPosition.x,
                        markerScreenPosition.y - offsety );
                    var aboveMarkerLatLng = projection.fromPointToLatLng( pointHalfScreenAbove );
                    map.setCenter( aboveMarkerLatLng );
                    infoBox.open( map, marker );
                    last = infoBox;
                } );
            }
        };
        initializePropertiesMap( data );
    };

    var GoogleMapStyles = {
        standard: [],
        silver: [
            {
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#f5f5f5'
                    }
                ]
            },
            {
                'elementType': 'labels.icon',
                'stylers': [
                    {
                        'visibility': 'off'
                    }
                ]
            },
            {
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#616161'
                    }
                ]
            },
            {
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#f5f5f5'
                    }
                ]
            },
            {
                'featureType': 'administrative.land_parcel',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#bdbdbd'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#eeeeee'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#e5e5e5'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#9e9e9e'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#ffffff'
                    }
                ]
            },
            {
                'featureType': 'road.arterial',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#dadada'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#616161'
                    }
                ]
            },
            {
                'featureType': 'road.local',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#9e9e9e'
                    }
                ]
            },
            {
                'featureType': 'transit.line',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#e5e5e5'
                    }
                ]
            },
            {
                'featureType': 'transit.station',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#eeeeee'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#c9c9c9'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#9e9e9e'
                    }
                ]
            }
        ],
        retro: [
            {
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#ebe3cd'
                    }
                ]
            },
            {
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#523735'
                    }
                ]
            },
            {
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#f5f1e6'
                    }
                ]
            },
            {
                'featureType': 'administrative',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#c9b2a6'
                    }
                ]
            },
            {
                'featureType': 'administrative.land_parcel',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#dcd2be'
                    }
                ]
            },
            {
                'featureType': 'administrative.land_parcel',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#ae9e90'
                    }
                ]
            },
            {
                'featureType': 'landscape.natural',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#dfd2ae'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#dfd2ae'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#93817c'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'geometry.fill',
                'stylers': [
                    {
                        'color': '#a5b076'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#447530'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#f5f1e6'
                    }
                ]
            },
            {
                'featureType': 'road.arterial',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#fdfcf8'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#f8c967'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#e9bc62'
                    }
                ]
            },
            {
                'featureType': 'road.highway.controlled_access',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#e98d58'
                    }
                ]
            },
            {
                'featureType': 'road.highway.controlled_access',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#db8555'
                    }
                ]
            },
            {
                'featureType': 'road.local',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#806b63'
                    }
                ]
            },
            {
                'featureType': 'transit.line',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#dfd2ae'
                    }
                ]
            },
            {
                'featureType': 'transit.line',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#8f7d77'
                    }
                ]
            },
            {
                'featureType': 'transit.line',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#ebe3cd'
                    }
                ]
            },
            {
                'featureType': 'transit.station',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#dfd2ae'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'geometry.fill',
                'stylers': [
                    {
                        'color': '#b9d3c2'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#92998d'
                    }
                ]
            }
        ],
        dark: [
            {
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#212121'
                    }
                ]
            },
            {
                'elementType': 'labels.icon',
                'stylers': [
                    {
                        'visibility': 'off'
                    }
                ]
            },
            {
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#212121'
                    }
                ]
            },
            {
                'featureType': 'administrative',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'featureType': 'administrative.country',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#9e9e9e'
                    }
                ]
            },
            {
                'featureType': 'administrative.land_parcel',
                'stylers': [
                    {
                        'visibility': 'off'
                    }
                ]
            },
            {
                'featureType': 'administrative.locality',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#bdbdbd'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#181818'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#616161'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#1b1b1b'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry.fill',
                'stylers': [
                    {
                        'color': '#2c2c2c'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#8a8a8a'
                    }
                ]
            },
            {
                'featureType': 'road.arterial',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#373737'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#3c3c3c'
                    }
                ]
            },
            {
                'featureType': 'road.highway.controlled_access',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#4e4e4e'
                    }
                ]
            },
            {
                'featureType': 'road.local',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#616161'
                    }
                ]
            },
            {
                'featureType': 'transit',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#757575'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#000000'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#3d3d3d'
                    }
                ]
            }
        ],
        night: [
            {
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#242f3e'
                    }
                ]
            },
            {
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#746855'
                    }
                ]
            },
            {
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#242f3e'
                    }
                ]
            },
            {
                'featureType': 'administrative.locality',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#d59563'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#d59563'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#263c3f'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#6b9a76'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#38414e'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#212a37'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#9ca5b3'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#746855'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#1f2835'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#f3d19c'
                    }
                ]
            },
            {
                'featureType': 'transit',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#2f3948'
                    }
                ]
            },
            {
                'featureType': 'transit.station',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#d59563'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#17263c'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#515c6d'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#17263c'
                    }
                ]
            }
        ],
        aubergine: [
            {
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#1d2c4d'
                    }
                ]
            },
            {
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#8ec3b9'
                    }
                ]
            },
            {
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#1a3646'
                    }
                ]
            },
            {
                'featureType': 'administrative.country',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#4b6878'
                    }
                ]
            },
            {
                'featureType': 'administrative.land_parcel',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#64779e'
                    }
                ]
            },
            {
                'featureType': 'administrative.province',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#4b6878'
                    }
                ]
            },
            {
                'featureType': 'landscape.man_made',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#334e87'
                    }
                ]
            },
            {
                'featureType': 'landscape.natural',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#023e58'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#283d6a'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#6f9ba5'
                    }
                ]
            },
            {
                'featureType': 'poi',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#1d2c4d'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'geometry.fill',
                'stylers': [
                    {
                        'color': '#023e58'
                    }
                ]
            },
            {
                'featureType': 'poi.park',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#3C7680'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#304a7d'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#98a5be'
                    }
                ]
            },
            {
                'featureType': 'road',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#1d2c4d'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#2c6675'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'geometry.stroke',
                'stylers': [
                    {
                        'color': '#255763'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#b0d5ce'
                    }
                ]
            },
            {
                'featureType': 'road.highway',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#023e58'
                    }
                ]
            },
            {
                'featureType': 'transit',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#98a5be'
                    }
                ]
            },
            {
                'featureType': 'transit',
                'elementType': 'labels.text.stroke',
                'stylers': [
                    {
                        'color': '#1d2c4d'
                    }
                ]
            },
            {
                'featureType': 'transit.line',
                'elementType': 'geometry.fill',
                'stylers': [
                    {
                        'color': '#283d6a'
                    }
                ]
            },
            {
                'featureType': 'transit.station',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#3a4762'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'geometry',
                'stylers': [
                    {
                        'color': '#0e1626'
                    }
                ]
            },
            {
                'featureType': 'water',
                'elementType': 'labels.text.fill',
                'stylers': [
                    {
                        'color': '#4e6d70'
                    }
                ]
            }
        ]
    };

    /////
    $( document ).ready( function () {

        function initialize_property_street_view( data, id ) {

            var propertyMarkerInfo = data;

            var propertyLocation = new google.maps.LatLng( propertyMarkerInfo.latitude, propertyMarkerInfo.longitude );

            /**
             * Street View
             */
            var panoramaOptions = {
                position: propertyLocation,
                pov: {
                    heading: 34,
                    pitch: 10
                }
            };
            var panorama = new google.maps.StreetViewPanorama( document.getElementById( id ), panoramaOptions );
            google.maps.event.trigger( panorama, 'resize' );
        }

        $( '.property-preview-map' ).each( function () {
            new GooglemapSingle( $( this ).data(), $( this ).attr( 'id' ) );
        } );

        $( '.tab-google-street-view-btn' ).click( function () {
            $( '.property-preview-street-map' ).hide();
            $( '.property-preview-street-map' ).each( function () {

                var d = $( this ).data();
                var i = $( this ).attr( 'id' );

                initialize_property_street_view( d, i );
            } );
            $( '.property-preview-street-map' ).show( 100 );
        } );
        ///
        // auto set height for split google map
        $( '.split-maps-container' ).each( function () {
            $( '#opalestate-map-preview ' ).height( $( window ).height() );
        } );
    } );

    $( document ).ready( function () {
        // search
        // show google maps
        // update google maps
        var updatePreviewGoogleMap = function ( url ) {
            if ( $( '#opalestate-map-preview' ).length > 0 ) {
                $.ajax( {
                    type: 'GET',
                    dataType: 'json',
                    url: opalesateJS.ajaxurl,
                    data: url,
                    success: function ( data ) {
                        new GoogleMapSearch( data );
                    }
                } );
            }
        };
        if ( $( '#opalestate-map-preview' ).length > 0 || $( '.opalesate-properties-results' ).length > 0 ) {
            var currentLocation = location.search.substr( 1 ) + '&action=opalestate_ajx_get_properties&paged=' +
                $( '#opalestate-map-preview' ).data( 'page' );
            updatePreviewGoogleMap( currentLocation );
        }

        // update results
        function updatePropertiesResults( data ) {
            $( '.opalesate-properties-results' ).append( $( '<div class="opalestate-loading"></div>' ) );
            $.ajax( {
                type: 'GET',
                url: opalesateJS.ajaxurl,
                data: data + '&action=opalestate_render_get_properties',
                success: function ( response ) {
                    if ( response ) {

                        $( '.opalesate-properties-results' ).html( response );
                    }
                    $( '.opalesate-properties-results .opalestate-loading' ).remove();
                    $( '.opalestate-sortable select' ).select2( {
                        width: '100%',
                        // minimumResultsForSearch: 20
                    } );
                }
            } );
        }

        function updatePropertiesByParseringHtml( newurl ) {
            $( '.opalesate-properties-results .opalesate-archive-bottom' )
                .append( $( '<div class="opalestate-loading"></div>' ) );
            $.ajax( {
                type: 'GET',
                url: newurl,
                dataType: 'html',
                cache: false,
                success: function ( data ) {
                    if ( data ) {
                        $( '.opalesate-properties-results' )
                            .html( $( data ).find( '.opalesate-properties-results' ).html() );
                        $( '.opalestate-sortable select' ).select2( {
                            width: '100%',
                            // minimumResultsForSearch: 20
                        } );
                    }
                    //  $( '.opalesate-properties-results .opalestate-loading').remove();
                }
            } );
        }

        $( 'form.opalestate-search-form' ).submit( function () {
            if ( $( '#opalestate-map-preview' ).length > 0 ) {
                if ( $( '.opalesate-properties-results' ) && $( '.opalesate-properties-results' ).data( 'mode' ) ==
                    'html' ) {
                    var $form = $( this );
                    if ( history.pushState ) {
                        var ps = $form.serialize();
                        var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname +
                            '?' + ps;
                        window.history.pushState( { path: newurl }, '', newurl );
                        updatePropertiesByParseringHtml( newurl );
                    }

                } else {
                    updatePropertiesResults( $( this ).serialize() );
                }

                return false;
            }
            return true;
        } );

        $( '.ajax-search-form form.opalestate-search-form' ).each( function () {
            var $form = $( this );
            $( '.ajax-change select', this ).change( function () {
                if ( history.pushState ) {
                    var ps = $form.serialize();
                    var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname +
                        '?' + ps;
                    window.history.pushState( { path: newurl }, '', newurl );
                }
                $form.submit();
                return false;
            } );
        } );

        // // Sortable Change // //
        $( 'body' ).delegate( '#opalestate-sortable-form select', 'change', function () {
            var ps = '';
            if ( $( 'form.opalestate-search-form' ).length > 0 ) {
                var $form = $( 'form.opalestate-search-form' );
                if ( $( 'body' ).hasClass( 'archive' ) ) {
                    ps = 'opalsortable=' + $( this ).val() + '&display=' +
                        $( '.display-mode a.active' ).data( 'mode' );
                } else {
                    ps = $form.serialize() + '&opalsortable=' + $( this ).val() + '&display=' +
                        $( '.display-mode a.active' ).data( 'mode' );
                }
            }

            if ( $( '.opalesate-properties-results' ).length > 0 && ps ) {
                if ( history.pushState ) {
                    var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname +
                        '?' + ps;
                    window.history.pushState( { path: newurl }, '', newurl );
                    updatePropertiesByParseringHtml( newurl );
                }
            } else {
                if ( history.pushState && $( 'body' ).hasClass( 'archive' ) ) {
                    var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname +
                        '?' + ps;
                    window.history.pushState( { path: newurl }, '', newurl );
                }

                $( '#opalestate-sortable-form' ).submit();
            }
        } );

        // display mode
        $( 'body' ).delegate( '.display-mode a', 'click', function () {
            if ( $( '.opalesate-properties-results' ).length > 0 ) {
                var newurl = $( this ).attr( 'href' );
                window.history.pushState( { path: newurl }, '', newurl );
                updatePropertiesByParseringHtml( newurl );
                return false;
            }
        } );

        if ( $( '#opalestate-map-preview' ).length > 0 ) {
            $( 'body' ).delegate( 'form.opalestate-search-form select', 'change', function () {
                var params = $( 'form.opalestate-search-form' ).serialize();
                var url = 'action=opalestate_ajx_get_properties&' + params;

                updatePreviewGoogleMap( url );
                $( 'form.opalestate-search-form' ).submit();
                return true;
            } );

            $( 'body' ).delegate( 'form.opalestate-search-form input', 'change', function () {

                if ( $( this ).hasClass( 'ranger-geo_radius' ) ) {
                    return false;
                }

                var params = $( 'form.opalestate-search-form' ).serialize();
                var url = 'action=opalestate_ajx_get_properties&' + params;
                updatePreviewGoogleMap( url );
                $( 'form.opalestate-search-form' ).submit();
            } );
        }
    } );
} )( jQuery );

( function ( $ ) {
    'use strict';

    $( document ).ready( function () {
        $( '.opalestate-search-opal-map' ).each( function () {
            initializeMapAdressSearch( $( this ) );
        } );
    } );

    function initializeMapAdressSearch( mapInstance ) {
        var searchInput = mapInstance.find( '.opal-map-search' );

        // Search
        var autocomplete = new google.maps.places.Autocomplete( searchInput[ 0 ] );
        // autocomplete.bindTo( 'bounds', map );
        var latitude = mapInstance.find( '.opal-map-latitude' );
        var longitude = mapInstance.find( '.opal-map-longitude' );

        if ( ( typeof opalestateGmap !== 'undefined' ) && opalestateGmap.autocomplete_restrictions ) {
            autocomplete.setComponentRestrictions( { 'country': JSON.parse(opalestateGmap.autocomplete_restrictions) } );
        }

        google.maps.event.addListener( autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            if ( !place.geometry ) {
                return;
            }

            if ( place.geometry.location.lat() ) {
                $( mapInstance ).addClass( 'active' );
            } else {
                $( mapInstance ).removeClass( 'active' );
            }

            latitude.val( place.geometry.location.lat() );
            longitude.val( place.geometry.location.lng() );
        } );

        $( '.map-remove', mapInstance ).click( function () {
            latitude.val( '' );
            longitude.val( '' );
            searchInput.val( '' );
            latitude.change();
        } );

        $( searchInput ).keypress( function ( event ) {
            if ( 13 === event.keyCode ) {
                event.preventDefault();
            }
        } );
    }
} )( jQuery );
