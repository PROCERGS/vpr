var graphics =
{
  debug: true,

  init: function() {
    //graphics.load_scripts();
    graphics.load();
  },

  load: function() {
    if ( typeof graphics_at_page != 'undefined' ) {
      if ( typeof graphics_at_page[ 'map' ] != 'undefined' ) {
        graphics.map.init( graphics_at_page[ 'map' ], 0 );
      }

      if ( typeof graphics_at_page[ 'treemap' ] != 'undefined' ) {
        graphics.treemap.init( graphics_at_page[ 'treemap' ] );
      }

      if ( typeof graphics_at_page[ 'bar' ] != 'undefined' ) {
        graphics.bar.init( graphics_at_page[ 'bar' ], 0 );
      }

      if ( typeof graphics_at_page[ 'line' ] != 'undefined' ) {
        graphics.line.init( graphics_at_page[ 'line' ], 0 );
      }

      if ( typeof graphics_at_page[ 'donut' ] != 'undefined' ) {
        var donut = new Donut();
        donut.init( graphics_at_page[ 'donut' ] );
      }
    }
  },

  load_script: function( url, callback ) {
    $.getScript( url, callback );
  },

  // load_scripts: function() {
  //   $.getScript( 'js/raphael.min.js' );
  //   $.getScript( 'js/morris.min.js' );
  // },

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
        scrollwheel:            false,
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
        html += '    <a href="#" class="title closed">' + ( ( !graphics.map.coredes ) ? 'Municípios' : 'Coredes' ) + '</a>';
        html += '    <div class="city-search"><input type="text" id="city-input" name="city" placeholder="Busca rápida"></div>';
        html += '    <div class="zoom">';
        html += '      <a href="#" class="plus">+</a>';
        html += '      <a href="#" class="city">Cidade <i></i></a>';
        html += '      <a href="#" class="corede">Corede <i></i></a>';
        html += '      <a href="#" class="state' + ( ( graphics.map.coredes ) ? ' current' : '' ) + ' ">Estado <i></i></a>';
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
      $("#city-input").autocomplete({source: pinNames }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li>" ).append( '<a href="' + item.link + '">' + item.label + '</a>' ).appendTo( ul );
      };

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

          // not valid anymore => return to last valid position
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
        $( '#graphics .map .header .title' ).bind('click', function() {
            $( '#graphics .map .header .city-search' ).slideToggle();
            $( this ).toggleClass( 'closed' );
            return false;
          }
        );

        $( '#graphics .map .header .zoom a' ).bind('click', function() {
            var _this = $( this );

            if ( _this.hasClass( 'plus' ) ) {
              if ( graphics.map.current_zoom == 3 ) {
                graphics.map.show_coredes( _this );
              } else if ( graphics.map.current_zoom == 2 ) {
                graphics.map.show_cities( _this );
              }
            } else if ( _this.hasClass( 'minus' ) ) {
              if ( graphics.map.current_zoom == 1 ) {
                graphics.map.show_coredes( _this );
              } else if ( graphics.map.current_zoom == 2 ) {
                graphics.map.show_state( _this );
              }
            }

            if ( _this.hasClass( 'city' ) ) {
              graphics.map.show_cities( _this );
            } else if ( _this.hasClass( 'corede' ) ) {
              graphics.map.show_coredes( _this );
            } else if ( _this.hasClass( 'state' ) ) {
              graphics.map.show_state( _this );
            }

            return false;
          }
        );
      },
    },

    show_cities: function( link ) {
      graphics.map.current_zoom = 1;

      graphics.map.graphic.setZoom( 10 );

      link.parent().find( 'a' ).removeClass( 'current' );
      link.parent().find( '.city' ).addClass( 'current' );
    },

    show_coredes: function( link ) {
      graphics.map.current_zoom = 2;

      graphics.map.graphic.setZoom( 8 );

      link.parent().find( 'a' ).removeClass( 'current' );
      link.parent().find( '.corede' ).addClass( 'current' );
    },

    show_state: function( link ) {
      graphics.map.current_zoom = 3;

      var center = new google.maps.LatLng( -30.202114,-53.327637 );

      graphics.map.graphic.setCenter( center );
      graphics.map.graphic.setZoom( 6 );

      link.parent().find( 'a' ).removeClass( 'current' );
      link.parent().find( '.state' ).addClass( 'current' );
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

  bar:
  {
    url:    [],
    data:     [],
    config:   [],
    started:  false,
    current:  0,
    title:    'title',

    init: function( config, index ) {
      graphics.bar.started  = false;
      graphics.bar.config   = config;
      graphics.bar.current  = index;
      graphics.bar.url      = config[ index ].url;
      graphics.bar.hasMore  = (typeof(config[ index ].hasMore) === 'undefined') ? true : config[ index ].hasMore;

      graphics.bar.load_data( graphics.bar.url );
      addLoader($( '#graphics .bar' ));
    },

    load_data: function( url ) {
      graphics.load_json( url, graphics.bar.load );
    },

    load: function( data ) {
      graphics.bar.data = data;
      graphics.bar.create();
    },

    create: function() {
      if ( graphics.bar.started ) {
        return false;
      } else {
        graphics.bar.started = true;
      }

      $( '#graphics .bar' ).children().remove();

      var history_title = (graphics_at_page['history_title'] == null) ? 'Histórico de gastos' : graphics_at_page['history_title'];

      var html =  '<div class="header">' +
            ' <div class="title">' + history_title + '</div>' +
            ' <ul class="period">' +
            '   <li class="current">' +
            '     <a href="javascript:void(0)">' + graphics.bar.config[ graphics.bar.current ].title + '</a>' +
            '   </li>';

        for ( var index in graphics.bar.config ){
          if(typeof graphics.bar.config[index].title !== 'undefined')
            html += '   <li>' +
                '     <a href="javascript:void(0)">' + graphics.bar.config[ index ].title + '</a>' +
                '   </li>';
        }

        html += ' </ul>' +
            '</div>' +
            '<div id="graphic-bar"></div>' +
            '<div class="legends">' +
            ' <p class="label">Legendas Bi = Bilhões(R$) Mi = Milhões(R$)</p>' +
            '</div>';

        if(graphics.bar.hasMore) html += '<span class="more"><em class="icon"></em>Veja outros gráficos</span>';

      removeLoader($( '#graphics .bar' ));
      $( '#graphics .bar' ).html( html );

      Morris.Bar({
        element:    'graphic-bar',
        data:       graphics.bar.data,
        xkey:           'year',
        ykeys:          [ 'label' ],
        labels:         [ 'Valor total' ],
        barRatio:       0.4,
        xLabelMargin:   1,
        hideHover:      'auto',
        barColors:      [ '#E0DFD9' ],
        yLabelFormat:   function(y) {
          if(y >= 1000000000000) return y/1000000000000 + ' Tri';
          else if(y>=1000000000) return y/1000000000 + ' Bi';
          else if(y>=1000000) return y/1000000 + ' Mi';
          else if(y>=1000) return y/1000 + ' mil';
          return y;
        },
        hoverCallback:  function( index, options, content )
        {
          $( '#graphics .bar #graphic-bar .morris-hover' ).html
          (
            '<strong>Valor total:</strong>' +
            '<span>' + options.data[ index ].amount + '</span>'
          );
        }
      });

      graphics.bar.header.binds();

      $( '#graphics .bar .more' ).bind('click', closeMoreSection);
    },

    header:
    {
      binds: function() {
        $( '#graphics .bar .header li a' ).bind( 'click',
        function() {
          $( '#graphics .bar .header li:not( .current )' ).toggle();
          var currentValue = $('#graphics .bar .header li.current a').html();

          if($(this).parent().index() > 0 && $(this).html() != currentValue){
            graphics.bar.init(graphics.bar.config, $(this).parent().index()-1);
          }
          return false;
        });
      }
    }
  },

  line:
  {
    url:      [],
    data:     [],
    started:  false,
    title:    'title',
    location: '',
    ykeys:    [],

    init: function( config ) {
      graphics.line.title     = config.title;
      graphics.line.url       = config.url;
      graphics.line.location  = config.location;
      graphics.line.load_data( graphics.line.url );
      graphics.line.ykeys     = config.ykeys;

      if(!graphics.line.started) { addLoader($(graphics.line.location)); }
    },

    load_data: function( url ) {
      graphics.load_json( url, graphics.line.load );
    },

    load: function( data ) {
      graphics.line.data = data;
      graphics.line.create();
    },

    create: function() {
      if ( graphics.line.started ) {
        return false;
      } else {
        graphics.line.started = true;
      }


      var html = '<div class="line">';

      if(graphics.line.title !== undefined)
        html += '    <div class="header">' +
          '        <div class="title">' + graphics.line.title + '</div>' +
          '    </div>';

      html += '    <div id="graphic-line"></div>' +
        '    <ul class="legends">';

      for(var index in graphics.line.ykeys) {
        if(typeof graphics.line.ykeys[index] !== 'function')
          html += '<li class="label-' + (parseInt(index)+1) + '"><p><span></span> ' + graphics.line.ykeys[index] + '</p></li>';
      }

      html += '    </ul></div>';
      
      removeLoader($(graphics.line.location));
      $('#graphics ' + graphics.line.location).html(html);

      Morris.Line(
      {
        element:        'graphic-line',
        data:           graphics.line.data,
        xkey:           'year',
        ykeys:          graphics.line.ykeys,
        labels:         ['Series A', 'Series B'],
        lineColors:     ['#1A7146','#339967','#67FFCE','#FFFFFF'],
        pointStrokeColors: ['#1A7146','#339967','#67FFCE','#FFFFFF'],
        hideHover:      'auto',
        smooth:         false,
        lineWidth:      2,
        pointSize:      3,
        xLabelMargin:   1,
        yLabelFormat:   function(y) {
          if(y >= 1000000000000) return y/1000000000000 + ' Tri';
          else if(y>=1000000000) return y/1000000000 + ' Bi';
          else if(y>=1000000) return y/1000000 + ' Mi';
          else if(y>=1000) return y/1000 + ' mil';
          return y;
        },
        hoverCallback:  function( index, options, content ) {
          var hoverCallbackHtml = '';

          for(var key in options.ykeys) {
            if(typeof options.ykeys[key] !== 'function')
              hoverCallbackHtml += '<div><strong>' + options.ykeys[key] + ':</strong> <span>' +
                options.data[index]['formatted_' + options.ykeys[key]] + '</span></div>';
          }
          $( '#graphics .line #graphic-line .morris-hover' ).html(hoverCallbackHtml);
        }
      });
    },
  },

  hbar:
  {
    url:    [],
    data:     [],
    started:  false,
    title:    '',
    location:   '',

    init: function( config )
    {
      graphics.hbar.title     = config.title;
      graphics.hbar.url       = config.url;
      graphics.hbar.location  = config.location;
      graphics.hbar.load_data( graphics.hbar.url );
    },

    load_data: function( url )
    {
      graphics.load_json( url, graphics.hbar.load );
    },

    load: function( data )
    {
      graphics.hbar.data = data;
      
      $( '<link/>',
        {
          rel:  'stylesheet',
          type:   'text/css',
          href:   'css/jquery.jqplot.min.css'
        }
      ).appendTo( 'head' );

      if ( typeof $.jqplot != 'undefined' && typeof $.jqplot.BarRenderer != 'undefined' && typeof $.jqplot.categoryAxisRenderer != 'undefined' && typeof $.jqplot.pointLabels != 'undefined' )
      {
        graphics.hbar.create();
      }
      else
      {
        if ( typeof $.jqplot === 'undefined' )
        {
          graphics.load_script( 'js/jqplot.min.js', graphics.hbar.load_files );
        }
      }
    },

    load_files: function()
    {
      if ( typeof $.jqplot.barRenderer === 'undefined' )
      {
        graphics.load_script( 'js/jqplot.barRenderer.min.js', graphics.hbar.create );
      }

      if ( typeof $.jqplot.CategoryAxisRenderer === 'undefined' )
      {
        graphics.load_script( 'js/jqplot.categoryAxisRenderer.min.js', graphics.hbar.create );
      }

      if ( typeof $.jqplot.PointLabels === 'undefined' )
      {
        graphics.load_script( 'js/jqplot.pointLabels.min.js', graphics.hbar.create );
      }
    },

    create: function()
    {
      if ( typeof $.jqplot === 'undefined' || typeof $.jqplot.BarRenderer === 'undefined' || typeof $.jqplot.CategoryAxisRenderer === 'undefined' || typeof $.jqplot.PointLabels === 'undefined' || graphics.hbar.started )
      {
        return false;
      }
      else
      {
        graphics.hbar.started = true;
      }

      $( '#graphics .second-container ' + graphics.hbar.location ).append
      (
        '<div class="hbar">' +
        '    <div class="header">' +
        '        <div class="title">' + graphics.hbar.title + '</div>' +
        '    </div>' +
        '    <div id="graphic-hbar"></div>' +
        /*'    <ul class="legends">' +
        '        <li class="label-1"><p><span></span> Pagamento1</p></li>' +
        '        <li class="label-2"><p><span></span> Liquidação2</p></li>' +
        '        <li class="label-3"><p><span></span> Empenho3</p></li>' +
        '        <li class="label-4"><p><span></span> Retenção4</p></li>' +
        '    </ul>' +*/
        '</div>'
      );

      $.jqplot
      (
        'graphic-hbar',
        graphics.hbar.data.info,
        {
          seriesColors: [ '#4DAF7C', '#2B734E' ],
          grid:
          {
            drawGridLines:  false,
            shadow:     false,
            background:   '#FFF',
            borderWidth:  0
          },
          seriesDefaults:
          {
            shadow:       false,
            pointLabels: { 
              show: true, 
              location: 'e', 
              edgeTolerance: -205 ,
              formatString: function() {return 'R$ %s';}()
            },
            renderer:       $.jqplot.BarRenderer,
            rendererOptions:
            {
              barPadding:   1,
              barMargin:    2,
              barDirection:   'horizontal'
            }
          },
          axes:
          {
            yaxis:
            {
              show:     false,
              renderer:   $.jqplot.CategoryAxisRenderer,
              ticks:    graphics.hbar.data.cities,
              tickOptions:
              {
                showGridline: false
              }
            },
            xaxis:
            {
              tickOptions:
              {
                showLabel: false
              }
            }
          }
        }
      );
    },
  },

  treemap:
  {
    url:    [],
    data:     [],
    started:  false,
    title:    'title',

    init: function( url ) {
      graphics.treemap.url = url;
      graphics.treemap.load();
    },

    load: function() {
      graphics.treemap.create();
    },

    create: function() {
      if ( graphics.treemap.started ) {
        return false;
      } else {
        graphics.treemap.started = true;
      }

      $( '#graphics .treemap' ).prepend( '<div id="graphic-treemap"></div>' );

      addLoader($('#graphics .treemap'));
      var treemap_div = $('#graphics .treemap');

      graphics.treemap.graphic  = d3.layout.treemap().size([treemap_div.width(), treemap_div.height()]).sort(function(a,b) { return a.size - b.size; }).sticky(true).value( function( d ){ return d.size; });
      graphics.treemap.div    = d3.select( '#graphics .treemap #graphic-treemap' );


      d3.json (
        graphics.treemap.url[ 0 ],
        function( error, root ) {
            var node = graphics.treemap.div.datum( root ).selectAll( '.node' )
              .data( graphics.treemap.graphic.nodes )
              .enter().append( 'div' )
              .attr( 'class', 'node' )
              .call( graphics.treemap.position )
              .style( 'background', function( d ) { return d.color; } )
              .html( function( d ) {
                if(typeof(d.name) !== 'undefined')
                  return '<a href="' + d.link + '"><span class="name">' + d.name + '</span>' +
                    '<strong class="value" style="' + ( graphics.treemap.adjustFontSize(d.size) ) + '">' + d.amount + '</strong>' +
                    '<span class="more-info" style="border:8px solid '+ d.color +';">' +
                      '<span class="name-inside" style="color:'+ d.color +';">' + d.name + '</span>' +
                      '<span class="year-and-amount">' + d.totalAmount + '</span>' +
                      '<span class="percent">' + d.percent + ' valor total do orçamento' + '</span>' +
                    '</span>' +
                  '</a>';
              })
              .call( graphics.treemap.adjustTreemapText )
              .call( graphics.treemap.adjustHoverPosition );

            removeLoader($('#graphics .treemap'));
        }
      );
    },

    position: function() {
      this.style( 'left', function(d) { return d.x + "px"; })
          .style( 'top', function(d) { return d.y + "px"; })
          .style( 'width', function(d) { return d.dx + "px"; })
          .style( 'height', function(d) { return d.dy + "px"; });
    },

    adjustFontSize: function(size){
      if(size < 1){ return ' display:none;'; }
      else if(size>=1 && size <=12){ return ' font-size:24px;'; }
      else if(size> 12 && size <=50) { return ' font-size:'+ size*2 +'px;'; }
      else if(size> 50 && size <=100) { return ' font-size:'+ size*1.5 +'px;'; }
      else return ' font-size:150px;';
    },

    adjustTreemapText: function(){
      $('.treemap .node').each(function(){
        if($(this).width() == 0) { $(this).attr('style','display:none;');  }
      });

      $('.treemap .node a .name, .treemap .node a .value').each(function(){
        var thisHeight = $(this).height()+20;
        var thisParentHeight = $(this).parents('a').innerHeight();
        if(thisHeight >= thisParentHeight){ $(this).attr('style','display:none;'); }

        var text = $(this).text();
        $(this).append('<em class="temporary" style="display:inline;">'+text+'<em/>');

        var thisParentWidth = $(this).parents('a').innerWidth();
        if(thisParentWidth <= 100){
          var temporaryWidth = $(this).find('.temporary').width()+10;
          if(temporaryWidth >= thisParentWidth){ $(this).attr('style','display:none;'); }
        }

        $(this).find('.temporary').remove();
      });
    },

    adjustHoverPosition: function(){
      var height = $('.treemap').height();
      var width = $('.treemap').width();

      $('.treemap .node').each(function(index){
        if(index > 0){
          var leftStr = $(this).css('left');
          var left = parseInt(leftStr.replace('px',''));

          var topStr = $(this).css('top');
          var top = parseInt(topStr.replace('px',''));

          $(this).find('.more-info').css({position:'absolute'});

          if(top > height / 2) $(this).find('.more-info').css({bottom:'20%'});
          else $(this).find('.more-info').css({top:'20%'});

          if(left > width / 4) $(this).find('.more-info').css({right:'15%'});
          else $(this).find('.more-info').css({left:'15%'});
        }
      });
    }
  }
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

function Donut() {
  this.url =    [];
  this.data =     [];
  this.started =  false;
  this.title =    'title';
  this.location =   '';
  this.element =  'graphic-donut';

  this.init = function( config ) {
    this.title    = config.title;
    this.url      = config.url;
    this.location = config.location;
    this.element  = config.element;
    this.load_data( this.url );
    
     $('#graphics ' + this.location).html("");
     addLoader($(this.location));
  };

  this.load_data = function( url ) {
    var self = this;
    $.get (
      url,
      'json',
      function(data) {
        self.load( data );
      }
    );
  };

  this.load = function( data ) {
    this.data = data;
    this.create();
  };

  this.create = function() {

    var donutContainer = '<div class="donut">';

    if(typeof this.title != 'undefined')
      donutContainer += '    <div class="header">' +
        '        <div class="description">' + this.title + '</div>' +
        '    </div>';

    donutContainer += '    <div id="' + this.element + '"></div>';
    donutContainer += '    <ul class="legends">';

    var array_colors = [];
    if(this.data != null) {
      for(var i = 0; i < this.data.length; i++) {
        array_colors[i] = this.data[i].color;
        donutContainer += '<li class="label-'+i+'"><p><span style="background-color:'+this.data[i].color+'"></span> '+this.data[i].label+' </p></li>';
      }
      donutContainer += '</ul></div>';
    }

    $('#graphics ' + this.location).html(donutContainer);
    removeLoader($(this.location));

    Morris.Donut( {
      element:    this.element,
      data:       this.data,
      colors:   array_colors,
      labelColor:   '#444444',
      formatter:    function ( y, data ) {
        return y + '%';
      }
    });

  };

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
Number.prototype.formatMoney = function(c, d, t){
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
