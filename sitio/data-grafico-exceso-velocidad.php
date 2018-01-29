<?php 
 require 'autoload.php';
 ini_set("memory_limit", "128M");
 
 $maxContentHeight = isset($_POST['maxContentHeight']) ? $_POST['maxContentHeight'] : 330;
 $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : 6;
 $gen  = new General();
 $maxvelocidad = $gen->getParamValue('maxvelocidad', 35);
 $map_api      = $gen->getParamValue('mapapi', 'google');

 //  date_default_timezone_set("Chile/Continental");

 $db = DB::getInstance();

 $cuenta_equipos=0;
 $titulo = '';

 $datos_equipos  = $db->query("SELECT * FROM uman_camion WHERE NUMFLOTA!='0'");
 
 foreach($datos_equipos->results() as $info_equipos) {
  $equipos[$cuenta_equipos] = $info_equipos;
  $cuenta_equipos++;
 }

 if ( !isset ( $_POST['equipo'] ) ) {
  echo '<div class="alert alert-warning" role="alert">Debe seleccionar un Equipo.</div>';
  exit();
 } 
 else {
  $equipo = $_POST['equipo'];
  $nomequipo = $db->query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=$equipo;");
	$nomequipo = ($nomequipo->count()>0)?$nomequipo->results()[0]->NUMCAMION:$equipo;
 }

 $rango = $_POST['fecha'];
 if( isset ( $_POST['fecha'] ) ) {
  // print '<center>'.$_POST['fecha'].'</center>';
  if(stripos($_POST['fecha'],' - ') > 0){
   $fecha = explode(" - ", $_POST['fecha']);
   if($fecha[0] != $fecha[1]){
    $fecha = "UNIX_TIMESTAMP(FECHA) 
    BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
   }
   else{
     $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(FECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
   }
  }
  else{
   $fecha = $_POST['fecha'];
   $fecha = "UNIX_TIMESTAMP(FECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
  }
 } 
  else {
    $fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
    $fecha2 = date('d/m/Y H:i:s', time());
    $fecha = "UNIX_TIMESTAMP(FECHA) 
    BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$tiempo} HOUR)) AND UNIX_TIMESTAMP(NOW())";
    
    $titulo = "<h4>DESDE <strong>{$fecha1}</strong>  HASTA <strong>{$fecha2}</strong></h4>";
  }
 //print date("Y-m-d H:i:s",$fecha1)." - ".date("Y-m-d H:i:s",$fecha2);
 // print $fecha;
 $datos_gps = NULL;
 if ( !isset( $_POST['n'] ) ) {
  $sql = "SELECT FECHA, X, Y, rapidez, ALTURA, direccion,DATE_FORMAT(FECHA, '%d/%m/%Y %H:%i:%s') AS FECHA2    
   FROM uman_gps 
   WHERE EQUIPO=$equipo AND $fecha AND rapidez > {$maxvelocidad} 
   ORDER BY FECHA ASC;";
  // echo "52: $sql \n";
  // $datos_gps 	= $db->query($sql);
  $set = 0;
 } 
 else { 
  $n   = $_POST['n'];
  $sur = $_POST['sur'];
  $e   = $_POST['e'];
  $o   = $_POST['o'];
  $sql = "SELECT FECHA,X,Y,rapidez,ALTURA,direccion, DATE_FORMAT(FECHA, '%d/%m/%Y %H:%i:%s') AS FECHA2   
   FROM uman_gps 
   WHERE EQUIPO=$equipo AND X > '$n' AND X < '$sur' AND Y > '$e' AND Y < '$o' AND $fecha AND rapidez > {$maxvelocidad} 
   ORDER BY FECHA ASC;";
  // echo "65: $sql \n";
  // $datos_gps 	= $db->query($sql); 
  $set = 1;
 }
 $db = DB::getInstance();
 $datos_gps   = $db->query($sql); 
 // var_dump($datos_gps);
  // echo "$sql \n";exit();
 // echo 'pasa';
 $cuenta_gps=0;
 
 foreach($datos_gps->results() as $info_gps){
  // $info_gps->FECHA  = date('Y-m-d H:i:s',(strtotime($info_gps->FECHA)));
  //   var_dump($info_gps);
  //print $info_gps["FECHA"]."<br>";
  $gps[$cuenta_gps]   = $info_gps;
  $cuenta_gps++;
 }
 if(!$set && isset($gps)) {
  $n   = $gps[0]->X-0.002;
  $sur = $gps[0]->X+0.002;
  $e   = $gps[0]->Y+0.002;
  $o   = $gps[0]->Y-0.002;
 }

  if($datos_gps->count()<=0){
    echo '<div class="alert alert-warning" role="alert">El equipo '.$nomequipo.' no cuenta con datos GPS recientes.</div>';
    exit();
  }

  $mapaCoordsPL = '';
  $mapaCoordsMaxVelPL = '';

  $mapaCoordsM = array();
  $mapaCoordsMaxVelM = array();

  $i=0;
  $fp = array();
  $id_tramo = 0;
  $fix_x = 0;
  $fix_y = 0;//0.0004;

  $g = array_shift($gps);  
  $inicio = array(
    'Velocidad'=>intval($g->rapidez),
    'Altura'=>intval($g->ALTURA),
    'Pendiente'=>0,
    'Porcentaje'=>0,
    'Porcentaje2'=>0,
    'Recorrido'=>0,
    'DH'=>0,
    'DV'=>0,
    'Exceso'=>false,
    'Fecha'=>$g->FECHA2,
    'Angulo'=>intval($g->direccion),
    'LatLng'=>array('lat'=>floatval($g->X+$fix_x), 'lng'=>floatval($g->Y+$fix_y)),
  );
  $g = array_pop($gps);
  $fin = array(
    'Velocidad'=>intval($g->rapidez),
    'Altura'=>intval($g->ALTURA),
    'Pendiente'=>0,
    'Porcentaje'=>0,
    'Porcentaje2'=>0,
    'DH'=>0,
    'DV'=>0,
    'Exceso'=>false,
    'Fecha'=>$g->FECHA2,
    'Angulo'=>intval($g->direccion),
    'LatLng'=>array('lat'=>floatval($g->X+$fix_x), 'lng'=>floatval($g->Y+$fix_y)),
  );
  
  $agregar = true;
  for($i=0; $i<count($gps); $i++)
  {
    $tramo = array(
      'Velocidad'=>intval($gps[$i]->rapidez),
      'Altura'=>intval($gps[$i]->ALTURA),
      'Pendiente'=>0,
      'Porcentaje'=>0,
      'Porcentaje2'=>0,
      'DH'=>0,
      'DV'=>0,
      'Exceso'=>true,
      'Fecha'=>$gps[$i]->FECHA2,
      'Angulo'=>intval($gps[$i]->direccion),
      'LatLng'=>array('lat'=>floatval($gps[$i]->X+$fix_x), 'lng'=>floatval($gps[$i]->Y+$fix_y)),
    );

    $fp['f'.strtotime($gps[$i]->FECHA).'000'] = $tramo;
    $alt .= '['.strtotime($gps[$i]->FECHA).'000'.', '.intval($gps[$i]->ALTURA).'],';
    $tramos[] = $tramo;
  }

  $LAT  = $gps[0]->X + $fix_x;
  $LNG  = $gps[0]->Y + $fix_y;
  $zoom = $gen->getParamValue('zoom-2d',15);

  $link = 'genera-documento.php?documento=velocidad_recorrido&equipo='.$equipo.'&fecha='.$rango.'&tipo=xlsx';

  $gData = array();
  foreach ($gps as $key => $g) {
    $gData[] = array(strtotime($g->FECHA), intval($g->rapidez));
    $altura = ($g->ALTURA/$maxvelocidad) % 100;
    $aData[] = array(strtotime($g->FECHA), intval($altura));
  }
?>

<style type="text/css">
  .chart {
    min-width: 400px;
    max-width: 520px;
    width: 100%;
    margin: 0 auto;
  }
  .gm-style-iw {
    width: 230px !important;
    top: 15px !important;
    left: 0px !important;
    background-color: #fff;
    box-shadow: 0 1px 6px #424242;
    border: 1px solid #263238;
    border-radius: 7px;
  }
  .gm-style-iw > div:first-child{
    width: 230px;
  }
  #iw-container {
    margin-bottom: 10px;
    margin-top: -4px;
    color: black;
  }
  #iw-container .iw-title {
    font-family: 'Open Sans Condensed', sans-serif;
    font-size: 18px;
    font-weight: 400;
    padding: 10px;
    color: #263238;
    margin: 0 !important;
    border-radius: 5px 5px 0 0;
  }
  .H_ib_body{
    background: white !important;
    padding: 0 !important;
    width: 230px;
  }
  .H_ib_content{
    padding: 0 !important;
  }
  .H_ib_tail>svg>g>path{
    fill: white !important;
  }
  .alerta{
    background-color: rgba(255,165,0,0.7);
    color: white !important;
  }
  .info{
    background-color: #2196F3;
    color: white !important;
  }
  .inicio{
    background-color: rgba(236,239,241 ,0.7);
    color: black !important;
  }
  .fin{
    background-color: rgba(33,33,33 ,0.7);
    color: white !important;
  }
  #iw-container .iw-content {
    font-size: 13px;
    line-height: 18px;
    font-weight: 400;
    margin-right: 1px;
    padding: 5px 5px 20px 15px;
    max-height: 140px;
    overflow-y: auto;
    overflow-x: hidden;
  }
  .iw-content img {
    float: right;
    margin: 0 5px 5px 10px; 
  }
  .iw-subTitle {
    font-weight: 700;
  }
  .iw-bottom-gradient {
    position: absolute;
    width: 326px;
    height: 25px;
    bottom: 10px;
    right: 18px;
    background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    background: -ms-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
  }

  .icon {
    display: inline-block;
    width: 1em;
    height: 1em;
    stroke-width: 0;
    stroke: currentColor;
    fill: currentColor;
  }
  .icon-location-arrow {
    width: 0.7861328125em;
  }
  .btn-descargar::after{
    content: ' Descargar datos';
  }
  @media (max-width: 1024px){
    .btn-descargar::after{
      content: '--';
    }
  }
</style>

<script type="text/javascript">
  var rectangle, infoWindow, contentString, map, mk;
  var fp = <?=json_encode($fp)?>;
  var equipo = <?=$equipo?>;
</script>
<?php echo $titulo ?>
<div class="row hidden-xs">
  <div class="<?= Core::col(6) ?>">
    <a href="<?= $link ?>" class="btn btn-success btn-xs pull-left btn-descargar" style="min-width: 100px; width:25%; ">
      <i class="fa fa-file-excel-o" aria-hidden="true"></i>
    </a>
  </div>
</div>
<div class="row">
 <div class="<?=Core::col(6,6,12,12)?>">
  <div id="grafico"></div> 
  <!-- Highcharts -->
  <script type="text/javascript">
    var marker, i, marker_z;
    Highcharts.setOptions({
      global: { 
        useUTC: false,
        timezoneOffset: (7 * 60)
      },
      lang: {
        months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
      }
    });
    Highcharts.chart('grafico', {
      chart: {
        type: 'areaspline',
        height: <?php echo $maxContentHeight ?>,
        zoomType: 'x',
        resetZoomButton: {
          position: {
            x: 0,
            y: -10
          }
        }
      },
      title: {
        text: ''
      },
      subtitle: {
        text: ''
      },
      xAxis: {
        type: 'datetime',
        labels: {
          formatter: function () {
            // console.log(this);
            var d = new Date(this.value*1000);
            return d.toLocaleDateString() + '<br/>' + d.toLocaleTimeString();
          },
          overflow: 'justify'
        },
      },
      yAxis: {
        title: {
          text: 'Velocidad'
        },
        minorGridLineWidth: 0,
        gridLineWidth: 0,
        alternateGridColor: null,
        plotBands: [{
          from: <?=$maxvelocidad?>,
          to: <?=$maxvelocidad+30?>,
          color: 'rgba(255, 165, 0, 0.4)',
          label: {
            text: 'Exceso de velocidad',
            style: { color: '#606060' }
          }
        }]
      },
      tooltip: {
        formatter: function(){
          var d = new Date(this.x*1000);
          var fpx = fp['f'+this.x*1000];
          var data = '';
          if(fpx){
            data = 'Velocidad : ' + this.y + 'Km/h. <br/>' +
            'Coordenadas: ' + fpx.LatLng.lat + ', ' + fpx.LatLng.lng +'<br/>' +
            'Altitud: ' + fpx.Altura + 'msnm. <br/>' +
            'Fecha: ' + fpx.Fecha;
          }
          else{
            data = 'Velocidad : ' + this.y + 'Km/h. <br/>' +
            'Fecha: ' + d.toDateString();
          }
          return data;
        },
        shared: true
      },
      credits: { enabled: false },
      plotOptions: {
        area: {
          pointStart: <?=strtotime($gps[0]->FECHA)*1000?>,
          marker: {
            enabled: false,
            symbol: 'circle',
            radius: 2,
            states: {
              hover: {
                enabled: true
              }
            }
          }
        },
        series: {
       cursor: 'pointer',
         point: {
          events: {
           click: function () {
            var s = fp['f'+this.x*1000];


            var titulo = s.Exceso ? 'Exceso de velocidad' : 'Información de ubicación';
            var className = s.Exceso ? 'alerta' : 'info';
            var pendiente = '<img src="./assets/img/pendiente_'+(( s.Pendiente > 0 )?'pos':'neg')+'.png" style="width:36px; height:36px" />';
            var contenido = '<div class="vel">Fecha: <strong>' + s.Fecha + '</strong></div>' + 
                '<div class="maxvel">Velocidad Máxima: <strong><?php echo($maxvelocidad) ?> Km/h</strong></div>' +
                '<div class="vel">Velocidad Registrada: <strong>' + s.Velocidad + ' Km/h</strong></div>' + 
                '<div class="vel">Altitud: <strong>' + s.Altura + ' msnm</strong></div>' +
                '<div class="vel">Pendiente: <small><strong>' + s.Pendiente + '&deg; / ' + s.Porcentaje + '%</strong></small> ' + ( s.Pendiente == 0 ? '' : pendiente ) + '</div>';
            var html = '<div id="iw-container">' +
              '<div class="iw-title '+className+'">'+titulo+'</div>' +
              '<div class="iw-content">' +
              contenido +
              '</div>' +
              '</div>';

            <?php if($map_api == 'google'){ ?>
            if(infoWindow!=undefined) infoWindow.close();
            var panel = crearContenidoPopup(s,titulo,className);

            infoWindow = new google.maps.InfoWindow({content:panel, maxWidth: 230, position: s.LatLng,});

            // Event that closes the Info Window with a click on the map
            google.maps.event.addListener(map, 'click', function() {
              infoWindow.close();
            });

            google.maps.event.addListener(infoWindow, 'domready', function() {
              estilizarInfoWindow();
            });

            infoWindow.open(map);
            
            map.setCenter(s.LatLng);
            map.setZoom(18);
            <?php }else if($map_api == 'here'){ ?>         
            if(ui.getBubbles().length == 0){
              bubble = new H.ui.InfoBubble(s.LatLng, {
                content: html
              });
              ui.addBubble(bubble);
            }
            else{
              // console.log(ui.getBubbles());
              bubble.open();
              bubble.setContent(html);
              bubble.setPosition(s.LatLng);
            }
            map.setCenter(s.LatLng);
            <?php } ?>
           }
          }
         }
        }
      },
      series: [
        // {
        //   name: 'Altitud',
        //   data: <?php//json_encode($aData)?>,
        //   fillColor: '#BDBDBD',
        //   color: '#9E9E9E',
        // },
        {
          name: 'Velocidad Equipo <?=$nomequipo?>',
          data: <?=json_encode($gData)?>,
          fillColor: '#18FFFF',
          color: '#00E5FF',
          lineWidth: 1,
        }        
      ],
      navigation: {
        menuItemStyle: { fontSize: '10px' }
      },
      responsive: {
        rules: [{
          condition: { maxWidth: 500 },
          chartOptions: {
            chart: { height: <?php echo $maxContentHeight ?> },
            subtitle: { text: null },
            navigator: { enabled: false }
          }
        }]
      }
    });
  </script>
 </div>

 <div class="hidden-lg hidden-md col-sm-12 col-xs-12">
   <br/><br/><br/><br/><br/><br/><br/><br/>
 </div>

 <div class="<?=Core::col(6,6,12,12)?>">
  <div id="map" style="width: 100%; height: <?php echo $maxContentHeight ?>px"></div>
 </div>
</div>

<script type="text/javascript">
  var map;
  var markers = [];

  <?php if($map_api == 'here'){ ?>
  var platform = new H.service.Platform({
    'app_id': '<?=$GLOBALS['HERE']['ID']?>', 
    'app_code': '<?=$GLOBALS['HERE']['CODE']?>'
  });

	var defaultLayers = platform.createDefaultLayers();

  function addDomMarker(map, o){
    var fill = o.Exceso ? '#FFA500' : '#18FFFF';

    var outerElement = document.createElement('div'), innerElement = document.createElement('div');
  
    outerElement.style.userSelect = 'none';
    outerElement.style.webkitUserSelect = 'none';
    outerElement.style.msUserSelect = 'none';
    outerElement.style.mozUserSelect = 'none';
    outerElement.style.cursor = 'default';
  
    innerElement.style.color = fill;
    innerElement.style.backgroundColor = 'transparent';
    innerElement.style.border = 'none';
    innerElement.style.font = 'normal 20px arial';
    innerElement.style.lineHeight = '20px'    
  
    innerElement.style.paddingTop = '2px';
    innerElement.style.paddingLeft = '4px';
    innerElement.style.width = '24px';
    innerElement.style.height = '24px';
  
    // add negative margin to inner element
    // to move the anchor to center of the div
    innerElement.style.marginTop = '-12px';
    innerElement.style.marginLeft = '-12px';
  
    outerElement.appendChild(innerElement);
  
    // Add text to the DOM element
    var rotation = 'rotate('+(o.Angulo-45)+'deg);';
    var style = '-webkit-transform: '+rotation+
      '-moz-transform: '+rotation+
      '-ms-transform: '+rotation+
      '-o-transform: '+rotation+
      'transform: '+rotation;
    innerElement.innerHTML = '<i class="fa fa-location-arrow" aria-hidden="true" style="'+style+'"></i>';
  
    function changeOpacity(evt) {
      evt.target.style.opacity = 0.6;
      // evt.target.style.cursor = 'pointer';
    };
  
    function changeOpacityToOne(evt) {
      evt.target.style.opacity = 1;
    };

    function mostrarInfo(evt){
      // console.log(evt);
      var data = evt.target.getData();
      var className = data.Exceso ? 'alerta' : 'info';
      var titulo = data.Exceso ? 'Exceso de velocidad' : 'Información de ubicación';
      var pendiente = '<img src="./assets/img/pendiente_'+(( data.Pendiente > 0 )?'pos':'neg')+'.png" style="width:36px; height:36px" />';
      var contenido = '<div class="vel">Fecha: <strong>' + data.Fecha + '</strong></div>' + 
          '<div class="maxvel">Velocidad Máxima: <strong><?php echo($maxvelocidad) ?> Km/h</strong></div>' +
          '<div class="vel">Velocidad Registrada: <strong>' + data.Velocidad + ' Km/h</strong></div>' + 
          '<div class="vel">Altitud: <strong>' + data.Altura + ' msnm</strong></div>' +
          '<div class="vel">Pendiente: <small><strong>' + data.Pendiente + '&deg; / ' + data.Porcentaje + '%</strong></small> ' + ( data.Pendiente == 0 ? '' : pendiente ) + '</div>';
      var html = '<div id="iw-container">' +
        '<div class="iw-title '+className+'">'+titulo+'</div>' +
        '<div class="iw-content">' +
        contenido +
        '</div>' +
        '</div>';
      //
      
      if(ui.getBubbles().length == 0){
        bubble = new H.ui.InfoBubble(data.LatLng, {
          content: html
        });
        ui.addBubble(bubble);
      }
      else{
        // console.log(ui.getBubbles());
        bubble.open();
        bubble.setContent(html);
        bubble.setPosition(data.LatLng);
      }
      // console.log(bubble);
      map.setCenter(data.LatLng);
    }
  
    //create dom icon and add/remove opacity listeners
    var domIcon = new H.map.DomIcon(outerElement, {
      // the function is called every time marker enters the viewport
      onAttach: function(clonedElement, domIcon, domMarker) {
        clonedElement.addEventListener('mouseover', changeOpacity);
        clonedElement.addEventListener('mouseout', changeOpacityToOne);
        // clonedElement.addEventListener('click', mostrarInfo);
      },
      // the function is called every time marker leaves the viewport
      onDetach: function(clonedElement, domIcon, domMarker) {
        clonedElement.removeEventListener('mouseover', changeOpacity);
        clonedElement.removeEventListener('mouseout', changeOpacityToOne);
        // clonedElement.removeEventListener('click', mostrarInfo);
      }
    });
  
    
    var bearsMarker = new H.map.DomMarker({lat: o.LatLng.lat, lng: o.LatLng.lng}, {
      icon: domIcon,
      zIndex: o.Exceso ? 100 : 0,
      data: o,
    });
    bearsMarker.addEventListener('tap', function(evt){
      mostrarInfo(evt);
    });
    map.addObject(bearsMarker);
  }

	map = new H.Map(
  	document.getElementById('map'),
		defaultLayers.satellite.map,
		{
			zoom: <?=$zoom?>,
			center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
			fixedCenter: false,
		});

	var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

	var ui = H.ui.UI.createDefault(map, defaultLayers);

  var mapaCoords = <?php echo json_encode($tramos) ?>;

  function changeOpacity(evt) {
    evt.target.style.opacity = 0.8;
  };

  markers = [];
  $.each(mapaCoords, function(i,o){
    mapaCoords[i].Pendiente = 0;
    mapaCoords[i].Porcentaje = 0;
    mapaCoords[i].DV = 0;
    mapaCoords[i].DH = 0;
    if(i>0){
      var DH = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(mapaCoords[i].LatLng.lat, mapaCoords[i].LatLng.lng), 
        new google.maps.LatLng(mapaCoords[i-1].LatLng.lat, mapaCoords[i-1].LatLng.lng));
      var DV = (mapaCoords[i].Altura - mapaCoords[i-1].Altura);
      mapaCoords[i].Porcentaje = Math.abs(Math.round((DV/DH)*100));
      mapaCoords[i].Pendiente = Math.round( (Math.atan(DV/DH)*180)/Math.PI);
      mapaCoords[i].DV = Math.round(DV);
      mapaCoords[i].DH = Math.round(DH);
    }
    var tmpmrkr = addDomMarker(map, o);
    markers.push(tmpmrkr);
  });

  // Create an info bubble object at a specific geographic location:
  var bubble;

  <?php 
    }
    else if($map_api == 'google'){
  ?>
  function estilizarInfoWindow(){
    var iwOuter = $('.gm-style-iw');

    /* Since this div is in a position prior to .gm-div style-iw.
     * We use jQuery and create a iwBackground variable,
     * and took advantage of the existing reference .gm-style-iw for the previous div with .prev().
    */
    var iwBackground = iwOuter.prev();

    // Removes background shadow DIV
    iwBackground.children(':nth-child(2)').css({'display' : 'none'});

    // Removes white background DIV
    iwBackground.children(':nth-child(4)').css({'display' : 'none'});

    // Moves the infowindow 115px to the right.
    iwOuter.parent().parent().css({left: '55px'});

    // Moves the shadow of the arrow 76px to the left margin.
    iwBackground.children(':nth-child(1)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

    // Moves the arrow 76px to the left margin.
    iwBackground.children(':nth-child(3)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

    // Changes the desired tail shadow color.
    iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px', 'z-index' : '1'});

    // Reference to the div that groups the close button elements.
    var iwCloseBtn = iwOuter.next();

    // Apply the desired effect to the close button
    iwCloseBtn.css({opacity: '1', right: '55px', top: '17px', border: '1px solid #263238', 'border-radius': '13px', width: '15px', height: '15px'});

    // If the content of infowindow not exceed the set maximum height, then the gradient is removed.
    if($('.iw-content').height() < 140){
      $('.iw-bottom-gradient').css({display: 'none'});
    }

    // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
    iwCloseBtn.mouseout(function(){
      $(this).css({opacity: '1'});
    });
  }

  function crearContenidoPopup(data, titulo, className){
    var pendiente = '<img src="./assets/img/pendiente_'+(( data.Pendiente > 0 )?'pos':'neg')+'.png" style="width:36px; height:36px" />';
    var contenido = '<div class="vel">Fecha: <strong>' + data.Fecha + '</strong></div>' + 
        '<div class="maxvel">Velocidad Máxima: <strong><?php echo($maxvelocidad) ?> Km/h</strong></div>' +
        '<div class="vel">Velocidad Registrada: <strong>' + data.Velocidad + ' Km/h</strong></div>' + 
        '<div class="vel">Altitud: <strong>' + data.Altura + ' msnm</strong></div>' +
        '<div class="vel">Pendiente: <small><strong>' + data.Pendiente + '&deg; / ' + data.Porcentaje + '%</strong></small> ' + ( data.Pendiente == 0 ? '' : pendiente ) + '</div>';
    var panel = '<div id="iw-container">' +
      '<div class="iw-title '+className+'">'+titulo+'</div>' +
      '<div class="iw-content">' +
      contenido +
      '</div>' +
      '<div class="iw-bottom-gradient"></div>' +
      '</div>';

    return panel;
  }

  
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: <?=$zoom?>,
    center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
    mapTypeId: "satellite",
    fullscreenControl: true
  });

  var marker = [];
  infoWindow = new google.maps.InfoWindow({content:'', maxWidth: 230});

  // Event that closes the Info Window with a click on the map
  google.maps.event.addListener(map, 'click', function() {
    infoWindow.close();
  });

  google.maps.event.addListener(infoWindow, 'domready', function() {
    estilizarInfoWindow();
  });

  var mapaCoords = [<?php echo json_encode($tramos) ?>];

  $.each(mapaCoords, function(i, gps){
    for(i=0; i<gps.length-1; i++){
      var titulo = gps[i].Exceso ? 'Exceso de velocidad' : 'Información de ubicación';
      var className = gps[i].Exceso ? 'alerta' : 'info';
      gps[i].Pendiente = 0;
      gps[i].Porcentaje = 0;
      gps[i].DV = 0;
      gps[i].DH = 0;
      if(i>0){
        // console.log(gps[i].LatLng);
        var DH = google.maps.geometry.spherical.computeDistanceBetween(
          new google.maps.LatLng(gps[i].LatLng.lat, gps[i].LatLng.lng), 
          new google.maps.LatLng(gps[i-1].LatLng.lat, gps[i-1].LatLng.lng));
        var DV = (gps[i].Altura - gps[i-1].Altura);
        gps[i].Porcentaje = Math.abs(Math.round((DV/DH)*100));
        gps[i].Pendiente = Math.round( (Math.atan(DV/DH)*180)/Math.PI);
        gps[i].DV = Math.round(DV);
        gps[i].DH = Math.round(DH);
      }
      var panel = crearContenidoPopup(gps[i],titulo,className);

      marker.push(new google.maps.Marker({
        position: gps[i].LatLng,
        map: map,
        info: panel,
        exceso: gps[i].Exceso,
        icon: {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
          fillColor: gps[i].Exceso ? '#FFA500' : '#18FFFF',
          // strokeColor: gps[i].Exceso ? '#2196F3' : '#2196F3',
          strokeColor: gps[i].Exceso ? '#2196F3' : '#18FFFF',
          strokeWeight: 1,
          scale: 3,
          // fillOpacity: gps[i].Exceso ? 1.0 : 0,
          fillOpacity: 1,
          rotation: gps[i].Angulo,
        },
        zIndex: ( gps[i].Exceso ? 99 : 10 )
      }));

      marker[marker.length-1].addListener('click', function(){
        infoWindow.setContent(this.info);
        infoWindow.setPosition(this.getPosition());
        infoWindow.open(map);
      });
    }
  });

  var inicio = <?php echo json_encode($inicio) ?>;
  var fin = <?php echo json_encode($fin) ?>;
  marker.push(new google.maps.Marker({
    position: inicio.LatLng,
    map: map,
    info: crearContenidoPopup(inicio,'Inicio de ruta','inicio'),
    icon: {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: '#FFFFFF',
        strokeColor: '#FFFFFF',
        strokeWeight: 1,
        scale: 6,
        fillOpacity: 1.0,
      },
    zIndex: 200
  }));

  marker[marker.length-1].addListener('click', function(){
    infoWindow.setContent(this.info);
    infoWindow.setPosition(this.getPosition());
    infoWindow.open(map);
  });

  marker.push(new google.maps.Marker({
    position: fin.LatLng,
    map: map,
    info: crearContenidoPopup(fin,'Finalización de ruta','fin'),
    icon: {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: '#663300',
        strokeColor: '#663300',
        strokeWeight: 1,
        scale: 6,
        fillOpacity: 1.0,
      },
    zIndex: 200
  }));

  marker[marker.length-1].addListener('click', function(){
    infoWindow.setContent(this.info);
    infoWindow.setPosition(this.getPosition());
    infoWindow.open(map);
  });
  console.log("listo");
  
  <?php } ?>

</script>

<script type="text/javascript">
  $(document).ready(function () {
    var h = <?= $maxContentHeight!=NULL?$maxContentHeight-128:'window.screen.availHeight - 128'?>;
    $(window).on('resize', function(){
      // $("#map").css("height", (h)+'px');
      $("#grafico").css("height", (h)+'px');
    });
    // $("#map").css("height", (h)+'px');
    $("#grafico").css("height", (h)+'px');
  });
</script>
<!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw&libraries=geometry"></script> -->
