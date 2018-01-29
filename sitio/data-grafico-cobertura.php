<?php
  require 'autoload.php';

  $acc = new Acceso(true);
  $gen = new General(); 

  $map_api = $gen->getParamValue('mapapi', 'google');
  $zoom    = $gen->getParamValue('zoom', 13);

  $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : NULL;
  $maxContentHeight = isset($_POST['maxContentHeight']) ? $_POST['maxContentHeight'] : 335;

  $rango = isset($_POST['fecha']) ? $_POST['fecha'] : NULL;

  $params = 'formato=GeoJSON';
  if($tiempo != NULL) $params .= "&tiempo={$tiempo}";
  if($rango != NULL) $params .= "&fecha={$rango}";

  if( isset ( $_POST['fecha'] ) ) {
   // print '<center>'.$_POST['fecha'].'</center>';
   if(stripos($_POST['fecha'],' - ') > 0)
   {
    $fecha = explode(" - ", $_POST['fecha']);
    if($fecha[0] != $fecha[1])
    {
     $titulo = "<h4>DESDE <strong>{$fecha[0]}</strong> - HASTA <strong>{$fecha[1]}</strong></h4>";
     $fecha = "UNIX_TIMESTAMP(FECHA) 
     BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";

    }
    else
    {
      $titulo = "<h4><strong>{$fecha[0]}</strong></h4>";
      $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(FECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
    }
   }
   else
   {
    $fecha = $_POST['fecha'];
    $titulo = "<h4>DESDE <strong>{$fecha}</strong></h4>";
    $fecha = "UNIX_TIMESTAMP(FECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
   }
  } 
  else {
    $fecha2 = date('d/m/Y H:i:s', time());
    if($tiempo == NULL){
      // $fecha1 = date('d/m/Y H:i:s', mktime(0,0,0,date("m"),1,date("Y")));
      $fecha1 = date('d/m/Y 00:00:00', time());
      $fecha = "UNIX_TIMESTAMP(FECHA) 
      BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha1','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(NOW())";
    }
    else{
      $fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
      $fecha = "UNIX_TIMESTAMP(FECHA) 
      BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$tiempo} HOUR)) AND UNIX_TIMESTAMP(NOW())";
    }
   
   $titulo = "<h4>DESDE <strong>{$fecha1}</strong> - <strong>{$fecha2}</strong></h4>";
  }

  $db = DB::getInstance();

  $sql = "SELECT X, Y, COUNT(*) AS 'total' 
    FROM uman_cobertura 
    WHERE $fecha 
    GROUP BY X, Y";
  // echo $sql;
  $data = $db->query($sql);

  $mapData = array();
  // echo $data->count().'<br/>';
  $count1 = count($data);
  if($data->count() > 0){
    $data = $data->results();
    $mapData  = '';
    foreach($data as $l => $d){
      $mapData  .= 'new google.maps.LatLng('.floatval($d->X).', '.floatval($d->Y).'),';
    }
  }

  $count2 = count($data);
  $mapData = substr($mapData,0,strlen($mapData)-1);
  
  $dat=$db->query("select x,y FROM uman_ultimogps order by FECHAGPS desc limit 1");
  $dat = $dat->results();
 
  $dat = $dat[0];
  $center = '{lat:'.floatval($dat->x).', lng:'.floatval($dat->y).'}';
  $LAT = floatval($dat->x);
  $LNG = floatval($dat->y);

  $map_api = 'google';
?>
<style>
	#map-canvas { 
		/*width: 100%;*/
		/* height: 600px;  */
		position: relative;
		width: 100%;
		height: 720px;
	}
	.detalle-mini {
		min-width: 400px;
		min-height: 480px;
	}
	@media (max-width: 799px){
		.modal-dialog{
			width: 98% !important;
			left: 0 !important;
		}
		.real-time-data{
			height: 500px;
			overflow-y: auto;
		}
	}
	@media (min-width: 800px){
		.modal-dialog{
			width: 800px !important;
			max-width: 800px !important;
		}
		.real-time-data{
			margin-left: -10px;
			overflow-y: hidden;
		}
	}
</style>

<div id="map-canvas"></div>

<script type="text/javascript">
  var map, heatmap;

</script>

<?php if($map_api == 'here'){ ?>
<script type="text/javascript">

  (function () {
    var app_id = '<?=$GLOBALS['HERE']['ID']?>';
    var app_code = '<?=$GLOBALS['HERE']['CODE']?>';

    // initialize communication with the platform
    const platform = new H.service.Platform({
        app_id,
        app_code,
        useCIT: true,
        // useHTTPS: true
    });

    const pixelRatio = devicePixelRatio > 1 ? 2 : 1;
    let defaultLayers = platform.createDefaultLayers({
        tileSize: 256 * pixelRatio
    });
    const layers = platform.createDefaultLayers({
      tileSize: 256 * pixelRatio,
      ppi: pixelRatio > 1 ? 320 : 72
    });

    // initialize a map  - not specifying a location will give a whole world view.
    let map = new H.Map(
      document.getElementById('map-canvas'),
      defaultLayers.satellite.map,
      {
        pixelRatio, 
        center: new H.geo.Point(<?=$LAT?>, <?=$LNG?>),
        zoom: <?=$zoom?>
      }
    );

    // make the map interactive
    const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    let ui = H.ui.UI.createDefault(map, layers);
    ui.removeControl('mapsettings');

    // resize map on window resize
    window.addEventListener('resize', function() {
      map.getViewPort().resize();
    });

    let provider = new H.datalens.RawDataProvider({
      dataUrl: './ajax/mapa-2d/raw.cobertura.php?<?=$params?>',
      // dataToFeatures: (data, helpers) => {
      //   let parsed = helpers.parseCSV(data);
      //   let features = [];
      //   for (let i = 1; i < parsed.length; i++) {
      //     let row = parsed[i];
      //     features.push({
      //       "type": "Feature",
      //       "geometry": {
      //         "type": "Point",
      //         "coordinates": [Number(row[0]), Number(row[1])]
      //       },
      //       "properties": { "total": row[2] }
      //     });
      //   }
      //   return features;
      // },
      featuresToRows: (features) => {
        // console.log(features);
        let rows = [], feature;
        for (let i = 0, l = features.length; i < l; i++) {
          feature = features[i];
          rows.push([
            {
              lat: feature.geometry.coordinates[1],
              lng: feature.geometry.coordinates[0]
            },
            feature.properties.total
          ]);
        }
        return rows;
      }
    });

    console.log(provider);

    let countScale = d3.scaleLinear()
    .domain([0, 100])
    .range([25, 55])
    .clamp(true);

    //initialize object layer
    let layer = new H.datalens.ObjectLayer(
      provider,
      {
        clustering: {
          rowToDataPoint: function(row) {
            return new H.clustering.DataPoint(
              // row.latitude, row.longitude, row.count
              row[0], row[1], row[2]
            );
          },
          options: function(zoom) {
            return {
              strategy: H.clustering.Provider.Strategy.DYNAMICGRID,
              eps: 70,
              //after zoom 16 show do not cluster
              minWeight: zoom < 16 ? 2 : Infinity
            };
          }
        },
        rowToMapObject: function(cluster) {
          return new H.map.Marker(
            cluster.getPosition()
          );
        },
        rowToStyle: function(cluster) {
          let size = countScale(cluster.getWeight()) * pixelRatio;
          //Icon takes path and fit it icon size
          let icon = H.datalens.ObjectLayer.createIcon([
            'svg',
            {
              viewBox: [-size, -size, size * 2, size * 2]
            },
            ['path', {
              d: d3.arc()({ //fill
                startAngle: 0,
                endAngle: 360,
                outerRadius: size
              }),
              fill: '#0092b3',
              fillOpacity: 0.8
              }],
              ['text', {
                x: 0,
                y: 10 * pixelRatio,
                fontFamily: 'sans-serif',
                fontWeight: 100,
                fontSize: 20 * pixelRatio,
                textAnchor: 'middle',
                fill: 'white'
              },
              String(cluster.getWeight())]
            ], {size: size}
          );
          return {icon: icon};
        }
      }
    );

    // add layer to map
    map.addLayer(layer);

  }());
</script>
<?php }else{ ?>
<script type="text/javascript">
  map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: <?=$zoom?>,
    center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
    mapTypeId: 'satellite'
  });

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: getPoints(),
    map: map,
    // opacity: 1000,    
    // radius: 20,
  });

  changeOpacity();

  function toggleHeatmap() {
    heatmap.setMap(heatmap.getMap() ? null : map);
  }

  function changeGradient() {
    var gradient = [
      'rgba(0, 255, 255, 1)',
      'rgba(0, 255, 255, 1)',
      'rgba(0, 191, 255, 1)',
      'rgba(0, 127, 255, 1)',
      'rgba(0, 63, 255, 1)',
      'rgba(0, 0, 255, 1)',
      'rgba(0, 0, 223, 1)',
      'rgba(0, 0, 191, 1)',
      'rgba(0, 0, 159, 1)',
      'rgba(0, 0, 127, 1)',
      'rgba(63, 0, 91, 1)',
      'rgba(127, 0, 63, 1)',
      'rgba(191, 0, 31, 1)',
      'rgba(255, 0, 0, 1)'
    ]
    heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
  }

  function changeRadius() {
    heatmap.set('radius', heatmap.get('radius') ? null : 20);
  }

  function changeOpacity() {
    heatmap.set('opacity', heatmap.get('opacity') ? 1 : 1);
  }

  function getPoints() {
    return [<?=$mapData?>];
  }
</script>
<?php } ?>

<script type="text/javascript">
  $(document).ready(function () {
    var diff = Math.abs(parseInt($(".container-fluid").css("margin-top")));
    var h = <?= $maxContentHeight!=NULL?$maxContentHeight:'window.screen.availHeight'?> - diff;
    $(window).on('resize', function(){
      $("#map-canvas").css("height", (h)+'px');
    });
    $("#map-canvas").css("height", (h)+'px');
  });  
</script>