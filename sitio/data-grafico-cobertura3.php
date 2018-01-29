<?php
  require 'autoload.php';

  $acc = new Acceso(true);
  $gen = new General(); 

  $map_api = $gen->getParamValue('mapapi', 'google');
  $zoom    = $gen->getParamValue('zoom', 13);

  $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : NULL;
  $maxContentHeight = isset($_POST['maxContentHeight']) ? $_POST['maxContentHeight'] : 335;

  $rango = isset($_POST['fecha']) ? $_POST['fecha'] : NULL;
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

  $sql = "SELECT X, Y 
    FROM uman_cobertura 
    WHERE $fecha 
    ORDER BY X ASC, Y ASC";
  $data = $db->query($sql);

  $mapData = array();
  // echo $data->count().'<br/>';
  $count1 = count($data);
  if($data->count() > 0){
    $data = $data->results();
    $mapData = '';
    foreach($data as $l => $d){
      $mapData .= 'new google.maps.LatLng('.floatval($d->X).', '.floatval($d->Y).'),';
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
    'use strict';

    var queries = {"query":{"fileName":"./query.json","dataset":"561122bec90a46778e08c366ce201402","id":"e3c7831cd8834133b2432a0ff99c62f3"}};

    const platform = new H.service.Platform({
      'app_id': 'tPpShACnHW0ovnpwQ6IJ',
      'app_code': 'vPYtvr3FruOUO-GbflHuQg',
      // useCIT: true,
      // useHTTPS: true
    });
    const {query} = queries;

    // Initialize a map
    const pixelRatio = devicePixelRatio > 1 ? 2 : 1;
    const defaultLayers = platform.createDefaultLayers({tileSize: 256 * pixelRatio});
    const map = new H.Map(
        document.getElementById('map-canvas'),
        defaultLayers.satellite.map,
        {
          pixelRatio,
          zoom: <?=$zoom?>,
          center: new H.geo.Point(<?=$LAT?>, <?=$LNG?>),
          fixedCenter: false,
          style: 'default'
        }
    );
  
    
    window.addEventListener('resize', function() {
      map.getViewPort().resize();
    });

    //make the map interactive
    new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    let ui = H.ui.UI.createDefault(map, defaultLayers);
    ui.removeControl('mapsettings');

    //instantiate Geovisualization service
    var service = platform.configure(new H.datalens.Service());

    var dBColors = [
        'rgb(158, 1, 66)',
        'rgb(238, 100, 69)',
        'rgb(250, 177, 88)',
        'rgb(243, 250, 173)',
        'rgb(199, 250, 173)',
        'rgb(152, 213, 163)',
        'rgb(92, 183, 169)'
    ];

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