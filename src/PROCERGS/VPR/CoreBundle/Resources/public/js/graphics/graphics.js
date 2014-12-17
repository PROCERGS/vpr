var graphics =
{
  debug: true,

  init: function() {
    graphics.load();
  },

  load: function() {
    if ( typeof graphics_at_page != 'undefined' ) {
      if ( typeof graphics_at_page[ 'map' ] != 'undefined' ) {
        graphics.map.init( graphics_at_page[ 'map' ], 0 );
      }
    }
  },

  load_script: function( url, callback ) {
    $.getScript( url, callback );
  },

  load_json: function( url, callback ) {
    $.get( url, 'json', function(data) {
      callback( data );
    });
  },

  map:
  {
    url:             [],
    data:            [],
    config:          [],
    current:         0,
    title:           'title',
    current_zoom:    3,
    coredes:         true,
    location:        '#graphics',
    loader:           false,
    detailsCallback: function(){},
    options: function() {
      return {
        center:                 new google.maps.LatLng( -30.202114,-53.327637 ),
        zoom:                   6,
        mapTypeId:              google.maps.MapTypeId.ROADMAP,
        maxZoom:                10,
        minZoom:                6,
        draggable:              true,
        scrollwheel:            true,
        disableDoubleClickZoom: true,
        disableDefaultUI:       true
      };
    },

    styles: [
      {
        featureType: 'road',
        stylers: [ { visibility: 'off' } ]
      },
      {
        featureType: 'landscape',
        stylers: [ { visibility: 'off' } ]
      },
      {
        "featureType": "administrative.country",
        "elementType": "labels",
        "stylers": [ { "visibility": "off" } ]
      },
      {
        "featureType": "administrative.province",
        "elementType": "labels",
        "stylers": [ { "visibility": "off" } ]
      },
      {
        "featureType": "poi",
        "stylers": [ { "visibility": "off" } ]
      },
      {
        featureType: 'water',
        stylers: [ { color: '#FFFFFF' } ]
      }
    ],

    init: function( config, index ) {
      graphics.map.config          = config;
      graphics.map.coredes         = config[ index ].coredes;
      graphics.map.current         = index;
      graphics.map.current_zoom    = ( config[ index ].coredes ) ? 3 : 2;
      graphics.map.detailsCallback = config[ index ].detailsCallback;
      graphics.map.loader          = config[ index ].loader;
      graphics.map.url             = config[ index ].url;

      if (config[ index ].location != undefined) {
        graphics.map.location        = config[ index ].location;
      }

      if (graphics.map.loader) addLoader($(graphics.map.location).parent());

      graphics.map.load_data( config[ index ].url );
    },

    load_data: function( url ) {
      graphics.load_json( url, graphics.map.load );
    },

    load: function( data ) {
      graphics.map.data = data;
      graphics.load_script( 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDsEhrqfrI7ExYPFLUFypeVDBgJ7r6oYz8&sensor=false&callback=graphics.map.create' )
    },

    create: function() {

      if ( typeof google === 'undefined' ) {
        return false;
      }

      var min_value = null;
      var max_value = null;

      var cities = graphics.map.data;
      var pinNames = [];

      for ( var index in cities ) {
        pinNames[index] = {
          label: cities[index].name,
          link: (( !graphics.map.coredes ) ? cities[index].link : cities[index].link_corede)
        };

        if(min_value == null || min_value > cities[ index ].quantity) {
          min_value = parseInt(cities[ index ].quantity);
        }
        if(max_value == null || max_value < cities[ index ].quantity) {
          max_value = parseInt(cities[ index ].quantity);
        }
      }

      var html = '<div class="map">';
        html += '  <div class="header">';
        html += '    <div class="zoom">';
        html += '      <a href="#" class="plus">+</a>';
        html += '      <a href="#" class="minus">-</a>';
        html += '    </div>';
        html += '  </div>';
        html += '  <div class="graphic"></div>';
        html += '  <div class="legends">';
        html += '    <a href="#" class="closed">Legendas</a>';
        html += '    <p>' + formatShortNumericValue(max_value) + '</p>';
        html += '    <p class="min">' + formatShortNumericValue(min_value) + '</p>';
        html += '  </div>';
        html += '</div>';
      if (graphics.map.loader == true) {
        removeLoader($(graphics.map.location).parent());
      }

      $( graphics.map.location ).prepend( html );
      graphics.map.header.binds();
      graphics.map.legends.binds();
      graphics.map.graphic = new google.maps.Map( $( '#graphics .map .graphic' ).get( 0 ), graphics.map.options() );
      graphics.map.graphic.setOptions( { styles: graphics.map.styles } );
      graphics.map.binds();

      graphics.map.markers.mount();
      graphics.map.display_coredes();

      if ( !graphics.map.coredes ) {
        graphics.map.show_coredes( $( '#graphics .map .header .zoom a.corede' ) );
      }
    },

    binds: function() {
      // bounds of the desired area
      var allowedBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng( -33.6, -57.3 ),
        new google.maps.LatLng( -27.3, -49.9 )
      );

      var lastValidCenter = graphics.map.graphic.getCenter();

      google.maps.event.addListener(
        graphics.map.graphic,
        'center_changed',
        function() {
          if ( allowedBounds.contains( graphics.map.graphic.getCenter() ) ) {
            lastValidCenter = graphics.map.graphic.getCenter();
            return false;
          }

          graphics.map.graphic.panTo( lastValidCenter );
        }
      );
    },

    display_coredes: function() {
      graphics.map.coredes = new google.maps.KmlLayer( 'https://s3.amazonaws.com/mapatransparencia/coredes/coredes.kml' );
      graphics.map.coredes.set( 'preserveViewport', true );
      graphics.map.coredes.set( 'suppressInfoWindows', true );
      graphics.map.coredes.set( 'gradient', '#F60' );
      graphics.map.coredes.setMap( graphics.map.graphic );
    },

    markers: {
      mount: function() {
        var markers = graphics.map.data;

        var zIndex = 0;
        for ( var index in markers )
        {
          var marker_object = new google.maps.Circle(
          {
            strokeColor:    markers[ index ].color,
            strokeOpacity:  0,
            strokeWeight:   0,
            fillColor:      markers[ index ].color,
            fillOpacity:    0.7,
            map:            graphics.map.graphic,
            radius:         markers[ index ].size * 10000,
            center:         new google.maps.LatLng( markers[ index ].lat, markers[ index ].long ),
            zIndex:         zIndex
          });
          markers[ index ].zindex = zIndex;
          zIndex++;
          graphics.map.markers.bind( marker_object, markers[ index ] );
        }
      },

      bind: function( marker, marker_info ) {
        google.maps.event.addListener(marker, 'click', function() {
          graphics.map.details.show(marker_info);
        });
      },
    },

    header: {
      binds: function() {

        $( '#graphics .map .header .zoom a' ).bind('click', function() {
            var _this = $( this );

            if ( _this.hasClass( 'plus' ) ) {
              graphics.map.graphic.setZoom( graphics.map.graphic.getZoom() + 1 );
            } else if ( _this.hasClass( 'minus' ) ) {
              graphics.map.graphic.setZoom( graphics.map.graphic.getZoom() - 1 );
            }
            return false;
          }
        );
      },
    },
    legends:
    {
      closed: true,
      binds: function() {
        $( '#graphics .map .legends a' ).bind('click', function() {
            graphics.map.legends.toggle();
            return false;
          }
        );
      },

      toggle: function() {
        if ( graphics.map.legends.closed ) {
          $( '#graphics .map .legends p' ).show();
          $( '#graphics .map .legends' ).animate( { 'height': 94, 'top': '-=65' } ).find( 'a' ).toggleClass( 'closed' );
          graphics.map.legends.closed = false;
        } else {
          $( '#graphics .map .legends' ).animate( { 'height': 29, 'top': '+=65' },
            function() {
              $( '#graphics .map .legends p' ).hide()
            }
          ).find( 'a' ).toggleClass( 'closed' );
          graphics.map.legends.closed = true;
        }
      }
    },

    details: {
      show: function( pin ) {
        graphics.map.details.remove();
        var returnHover = graphics.map.detailsCallback(pin);
        $( '#graphics .map' ).append(returnHover);
        $( '#graphics .map .detail .close' ).bind('click', function() {
          graphics.map.details.remove();
        });
      },

      remove: function() {
        $( '#graphics .map .detail' ).remove();
      }
    }
  },
};

var remove_accents = function( word )
{
  var word      = word.split( '' );
  var result      = new Array();
  var accents     = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽž';
  var accents_out   = "AAAAAAaaaaaaOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSsYyyZz";

  for ( var y = 0; y < word.length; y++ )
  {
    if ( accents.indexOf( word[ y ]) != -1 )
    {
      result[ y ] = accents_out.substr( accents.indexOf( word[ y ] ), 1 );
    }
    else
    {
      result[ y ] = word[ y ];
    }
  }

  result = result.join( '' );

  return result;
};

jQuery.fn.extend({
  splitList: function(){
    var list = $(this);
    var listLen = $(this).length;
    var leftSize;

    if (listLen%2 == 1) { leftSize = (listLen/2)+1; }
    else { leftSize = listLen/2; }

    list.slice(0,leftSize).wrapAll('<div class="left" />');
    list.slice(leftSize,listLen).wrapAll('<div class="right" />');
  return $(this);
  }
});

function addLoader(loaderContainer) {
  loaderContainer.prepend('<div class="loader"><span class="loading"></span></div>');
}

function removeLoader(loaderContainer) {
  loaderContainer.find('.loader').remove();
}

function closeMoreSection() {
  graphics_at_page.more();
  $('#graphics .bar .more').unbind('click');
  return false;
}
function formatShortNumericValue(value) {
    if(value > 1000000000000)     return Math.round(value/1000000000000) + ' Tri';
    else if(value >= 1000000000)  return Math.round(value/1000000000) + ' Bi';
    else if(value >= 1000000)     return Math.round(value/1000000) + ' Mi';
    else if(value >= 1000)        return Math.round(value/1000) + ' mil';
    else return Math.round(value);
  }
Number.prototype.format = function(c, d, t){
  var n = this,
      c = isNaN(c = Math.abs(c)) ? 2 : c,
      d = d == undefined ? "." : d,
      t = t == undefined ? "," : t,
      s = n < 0 ? "-" : "",
      i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
      j = (j = i.length) > 3 ? j % 3 : 0;
     return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
   };

$( document ).ready
(
  function()
  {
    graphics.init();
  }
);
