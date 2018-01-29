<?php 
  require 'autoload.php';

  set_time_limit(300);
  // error_reporting(E_ERROR);
  $maxContentHeight = isset($_POST['maxContentHeight']) ? $_POST['maxContentHeight'] : 330;
  $tiempo           = isset($_POST['tiempo']) ? $_POST['tiempo'] : 24;
  $fecha            = isset($_POST['fecha']) ? $_POST['fecha'] : null;
  $gen              = new General();
  $maxvelocidad     = $gen->getParamValue('maxvelocidad');
  $nomenclatura     = $gen->getNomenclaturas();
  $utemp            = $gen->getParamValue('unidad_temperatura', 'celsius');
  $upres            = $gen->getParamvalue('unidad_presion', 'psi');
  $map_api          = $gen->getParamValue('mapapi', 'google');
  $zoom             = $gen->getParamValue('zoom-2d',15);
  $mostrar_coordenadasalarma = $gen->getParamValue('mostrar_coordenadasalarma', 0);
  $maxgps           = $gen->getParamValue('maxgps', 1);

  $tipo_alarma      = array(
    'timeout'=>8,
    'bateria_baja'=>16,
    'temperatura'=>32,
    'presion_baja'=>64,
    'presion_alta'=>128
    );
  $color            = array(
    'temperatura'=>array(
      'fill'=>'#DD2C00',
      'line'=>'#d50000'
    ),
    'presion_baja'=>array(
      'fill'=>'#FFFF00',
      'line'=>'#F9A825'
    ),
    'presion_alta'=>array(
      'fill'=>'#FFAB00',
      'line'=>'#FF6D00'
    ),
    'normal'=>array(
      'fill'=>'#33FFFF',
      'line'=>'#33FFFF'
    )
    );
  $i=0;
  $fp = array();
  $fix_x = 0; // 0.0001;
  $fix_y = 0; //-0.0001;

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
        $fecha = "UNIX_TIMESTAMP(ALARMAFECHA) 
        BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
      }
      else{
        $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
      }
    }
    else{
      $fecha = $_POST['fecha'];
      $fecha = "UNIX_TIMESTAMP(ALARMAFECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
    }
  } 
  else {
    $fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
    $fecha2 = date('d/m/Y H:i:s', time());
    $fecha = "UNIX_TIMESTAMP(ALARMAFECHA) 
    BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$tiempo} HOUR)) AND UNIX_TIMESTAMP(NOW())";
    
    $titulo = "<h4>DESDE <strong>{$fecha1}</strong>  HASTA <strong>{$fecha2}</strong></h4>";
  }

  $LAT  = 0;
  $LNG  = 0;
  $sql = "SELECT * FROM uman_gps 
    WHERE ".str_replace('(ALARMAFECHA)', '(FECHA)', $fecha)." AND EQUIPO={$equipo} LIMIT 1;";

  $row = $db->query($sql);
  if($row->count()>0){
    $row = $row->results()[0];
    $LAT  = $row->X;
    $LNG  = $row->Y;
  }
  else{
    echo '<div class="alert alert-warning" role="alert">El equipo '.$nomequipo.' no tiene eventos gps registrados en el intervalo seleccionado.</div>';
    exit();
  }

  $graph    = array();
  $additionals = array();
  $additionalsIds = array();
  $graphIds = array();


  $sql = sprintf("SELECT * FROM uman_alarmas 
    WHERE ALARMANUMCAMION=%d AND %s;", $equipo, $fecha);

  $alarmas = $db->query($sql);
  if($alarmas->count()>0){
    foreach($alarmas->results() as $al){
      $segs   = 30;
      $ciclos = 10; //Máximo de ciclos
      $gps    = NULL;

      #region obtener posición aproximada de alarma basada en fecha de la alarma
       // while($ciclos > 0){
          //$fecha1 = strtotime($al->ALARMAFECHA) - $segs;
          //$fecha2 = strtotime($al->ALARMAFECHA) + $segs;
          $maxdiff = $maxgps * 60;

          $fecha = strtotime($al->ALARMAFECHA);
          // se obtiene fecha hora de la siguiente posicion de gps despues de la alarma
          $sql1 = sprintf("SELECT *,UNIX_TIMESTAMP(FECHA) fecham FROM uman_gps 
            WHERE EQUIPO=%d AND FECHA >'%s' ORDER BY FECHA ASC LIMIT 1",
            $equipo, $al->ALARMAFECHA);
          $gps = $db->query($sql1);

          //se obtiene fecha hora la anterior posicion gps antes de la alarma
          $result1 = $gps->results();
          $sql2 = sprintf("SELECT *,UNIX_TIMESTAMP(FECHA) fecham FROM uman_gps 
            WHERE EQUIPO=%d AND FECHA <='%s' ORDER BY FECHA DESC LIMIT 1",
            $equipo, $al->ALARMAFECHA);
          $gps2 = $db->query($sql2);
          $result2=$gps2->results();

          //se verifica cual de las dos posiciones es la mas cercana a la alarma
          if(count($result1)>0){
            /*if($gps->count()>1){
              $gps = $gps->results()[intval($gps->count()/2)];
            }
            else $gps = $gps->results()[0];*/
            if(count($result2)>0){
              $fecha1=$result1[0]->fecham;
              $fecha2=$result2[0]->fecham;
              $diff1 = $fecha1-$fecha;
              $diff2 = $fecha-$fecha2;
              if($diff2>$diff1){
                  if($diff1<$maxdiff){
                    $gps = $result1[0];
                  }
              }else{
                  if($diff2<$maxdiff){
                    $gps = $result2[0];
                  }
              }
            }else{
                $fecha1=$result1[0]->fecham;
                $diff1 = $fecha1-$fecha;
                if($diff1<$maxdiff){
                  $gps = $result1[0];
                }
            }
            //$ciclos = 0;
          }
          else{
            //$segs += 15; //Se amplía el rango de tiempo para la búsqueda
            if(count($result2)>0){
                $fecha2=$result2[0]->fecham;
                $diff2 = $fecha-$fecha2;
                if($diff2<$maxdiff){
                  $gps = $result2[0];
                }
            }
          }
          //$ciclos--; //Se decrementa, para que cuando llegue a 0 se salga y no entre en un bucle infinito.
        //}

        if($mostrar_coordenadasalarma>0){
          //se deben mostrar x coordenadas previas y posteriores a la alarma
          $pos = array_search($gps->ID, $additionalsIds);
          if($pos!==false){
            //verifica si la coordenada gps que se esta dibujando no esta entre las coordenadas gps a dibujar, en caso afirmativo se remueven de las adicionales
            unset($additionalsIds[$pos]);
            unset($additionals[$pos]);
          }
          //x coordenadas previas y se verifica que no se haya dibujado o este en la lista de adicionales ya
          $sql = sprintf("SELECT * FROM uman_gps 
            WHERE EQUIPO=%d AND ID<%d ORDER BY FECHA DESC LIMIT %d",
            $equipo, $gps->ID,$mostrar_coordenadasalarma);
          $addit = $db->query($sql);
          if($addit->count()>0){
            foreach($addit->results() as $ad){
              if(array_search($ad->ID, $graphIds)===false){
                if(array_search($ad->ID, $additionalsIds)===false){
                  array_push($additionals, $ad);
                  array_push($additionalsIds, $ad->ID);
                }
              }
            }
          }
          //x coordenadas posteriores y se verifica que no se haya dibujado o este en la lista de adicionales ya
          $sql = sprintf("SELECT * FROM uman_gps 
            WHERE EQUIPO=%d AND ID>%d ORDER BY FECHA ASC LIMIT %d",
            $equipo, $gps->ID,$mostrar_coordenadasalarma);
          $addit = $db->query($sql);
          if($addit->count()>0){
            foreach($addit->results() as $ad){
              if(array_search($ad->ID, $graphIds)===false){
                if(array_search($ad->ID, $additionalsIds)===false){
                  array_push($additionals, $ad);
                  array_push($additionalsIds, $ad->ID);
                }
              }
            }
          }

        }

        $altura    = 0;
        $velocidad = 0;
        $latlng    = array('lat'=>NULL, 'lng'=>NULL);
        $angulo    = 0;
        $idGps     = 0;

        if($gps != NULL){
          $velocidad = $gps->rapidez;
          $altura    = $gps->ALTURA;
          $latlng    = array('lat'=>floatval($gps->X), 'lng'=>floatval($gps->Y));
          $angulo    = $gps->direccion;
          $LAT       = $gps->X;
          $LNG       = $gps->Y;
          $idGps     = $gps->ID;

        }
      #endregion

      #region obtener datos de umbrales desde eventos basado en fecha de la alarma
        $temp_max = NULL;
        $pres_min = NULL;
        $pres_max = NULL;
        $fecha1 = strtotime($al->ALARMAFECHA) - $segs;
        $fecha2 = strtotime($al->ALARMAFECHA) + $segs;

        $sql = sprintf("SELECT * FROM uman_eventos 
          WHERE EVENTONUMCAMION=%d AND UNIX_TIMESTAMP(EVENTOFECHA) BETWEEN %d AND %d LIMIT 1",
          $equipo, $fecha1, $fecha2);
        $evt = $db->query($sql);
        if($evt->count()>0){
          $evt = $evt->results()[0];
          $temp_max = $evt->TEMPMAX;
          $pres_max = $evt->PRESMAX;
          $pres_min = $evt->PRESMIN;
        }
        else $evt=NULL;
      #endregion

      $ta = '';
      switch($al->ALARMATIPO){
        case 8:   $ta = 'timeout'; break;
        case 16:  $ta = 'bateria_baja'; break;
        case 32:  $ta = 'temperatura'; break;
        case 64:  $ta = 'presion_baja'; break;
        case 128: $ta = 'presion_alta'; break;
      }
      if($latlng['lat'] != 0 && $latlng['lng'] != 0){
        $graph[] = array(
          'Tipo'=>$ta,
          'Temperatura'=>Core::tpConvert($al->EVENTOTEMPERATURA, $utemp),
          'Presion'=>Core::tpConvert($al->EVENTOPRESION, $upres),
          'MaxTemp'=>Core::tpConvert($temp_max, $utemp),
          'MaxPres'=>Core::tpConvert($pres_max, $upres),
          'MinPres'=>Core::tpConvert($pres_min, $upres),
          'Posicion'=>$nomenclatura[$al->ALARMAPOSICION],
          'Fecha'=>(new DateTime($al->ALARMAFECHA))->format('d/m/Y H:i:s'),
          'Relleno'=>$color[$ta]['fill'],
          'Linea'=>$color[$ta]['line'],

          'Velocidad'=>$velocidad,
          'Altura'=>$altura,
          'Pendiente'=>0,
          'Angulo'=>intval($angulo),
          'LatLng'=>$latlng
        );
        array_push($graphIds, $idGps);
      }
    }
  }
  else{
    echo '<div class="alert alert-warning" role="alert">El equipo '.$nomequipo.' no tiene eventos alarmas registradas en el intervalo seleccionado.</div>';
    exit();
  }
  //se dibujan los puntos adicionales
  if(count($additionals)>0 && count($graphIds)>0){
    foreach($additionals as $ad){
        $altura    = 0;
        $velocidad = 0;
        $latlng    = array('lat'=>NULL, 'lng'=>NULL);
        $angulo    = 0;
        $idGps     = 0;

        if($ad != NULL){
          $velocidad = $ad->rapidez;
          $altura    = $ad->ALTURA;
          $latlng    = array('lat'=>floatval($ad->X), 'lng'=>floatval($ad->Y));
          $angulo    = $ad->direccion;
          $LAT       = $ad->X;
          $LNG       = $ad->Y;
          $idGps     = $ad->ID;

        }
      #endregion
        $temp_max = NULL;
        $pres_min = NULL;
        $pres_max = NULL;
        $temp_ev = NULL;
        $pres_ev = NULL;
        $pos = 0;
        $fecha1 = strtotime($ad->FECHA) - $segs;
        $fecha2 = strtotime($ad->FECHA) + $segs;

        $sql = sprintf("SELECT * FROM uman_eventos 
          WHERE EVENTONUMCAMION=%d AND UNIX_TIMESTAMP(EVENTOFECHA) BETWEEN %d AND %d LIMIT 1",
          $equipo, $fecha1, $fecha2);
        $evt = $db->query($sql);
        if($evt->count()>0){
          $evt = $evt->results()[0];
          $temp_max = $evt->TEMPMAX;
          $pres_max = $evt->PRESMAX;
          $pres_min = $evt->PRESMIN;
          $temp_ev = $evt->EVENTOTEMPERATURA;
          $pres_ev = $evt->EVENTOPRESION;
          $pos = $evt->EVENTOPOSICION;
        }
        else $evt=NULL;
      
      $ta = 'evento';
      
      if($latlng['lat'] != 0 && $latlng['lng'] != 0){
        if($pos==0){
          $pos="";
        }else{
          $pos = $nomenclatura[$pos];
        }
        $graph[] = array(
          'Tipo'=>$ta,
          'Temperatura'=>Core::tpConvert($temp_ev, $utemp),
          'Presion'=>Core::tpConvert($pres_ev, $upres),
          'MaxTemp'=>Core::tpConvert($temp_max, $utemp),
          'MaxPres'=>Core::tpConvert($pres_max, $upres),
          'MinPres'=>Core::tpConvert($pres_min, $upres),
          'Fecha'=>(new DateTime($ad->FECHA))->format('d/m/Y H:i:s'),
          'Relleno'=>$color[$ta]['fill'],
          'Linea'=>$color[$ta]['line'],
          'Posicion'=>$pos,
          'Velocidad'=>$velocidad,
          'Altura'=>$altura,
          'Pendiente'=>0,
          'Angulo'=>intval($angulo),
          'LatLng'=>$latlng
        );
        array_push($graphIds, $idGps);
      }
    }
  }

  if(count($graph) == 0 && $alarmas->count() > 0){
    echo '<div class="alert alert-warning" role="alert">El equipo '.$nomequipo.' tiene alarmas registradas en el intervalo seleccionado, pero no se dispone de datos gps para ubicarlas en el mapa.</div>';
    exit();
  }

  $link = 'genera-documento.php?documento=velocidad_recorrido&equipo='.$equipo.'&fecha='.$rango.'&tipo=xlsx';
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
  #iw-container {
    margin-bottom: 10px;
    margin-top: -4px;
    color: black;
    width: 230px;
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
    overflow-y: hidden;
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
  .alerta_temperatura{
    background: <?php echo $color['temperatura']['fill'] ?>;
    color: white;
  }
  .alerta_presion_baja{
    background: <?php echo $color['presion_baja']['fill'] ?>;
    color: black;
  }
  .alerta_presion_alta{
    background: <?php echo $color['presion_alta']['fill'] ?>;
    color: black;
  }
  value{
    display: inline;
    padding: .2em .6em .3em;
    font-size: 100%;
    font-weight: bold;
    line-height: 1;
    color: #212121;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
  }
  div.label-temp value{
    background: #BF360C;
    color: #FFF;
  }
  div.label-presb value{
    background: #FFF176;
    color: #212121;
  }
  div.label-presa value{
    background: #FFC400;
    color: #212121;
  }
</style>

<script type="text/javascript">
  var rectangle, infoWindow, contentString, map, mk;
  var equipo = <?=$equipo?>;
</script>
<?php echo $titulo ?>
<div class="row hidden-xs">
  <div class="<?= Core::col(6) ?>">
    <a href="<?= $link ?>" class="btn btn-success btn-xs pull-left" style="min-width: 100px; width:25%; ">
      <i class="fa fa-file-excel-o" aria-hidden="true"></i>  Descargar datos
    </a>
  </div>
</div>

<div id="map" style="width: 100%; height: <?php echo $maxContentHeight ?>px"></div>

<script type="text/javascript">
  var markers = [];
  var map;
  var mapaCoords = <?=json_encode($graph)?>;
  var titulos    = {
    timeout : 'Alarma Timeout',
    bateria_baja : 'Alarma de batería baja',
    temperatura : 'Alarma de Temperatura',
    presion_baja : 'Alarma de presión baja',
    presion_alta : 'Alarma de presión alta',
    evento : 'Evento Presión/Temperatura',
  }
</script>
<script type="text/javascript">
  <?php if($map_api == 'here'){ ?>
  	var platform = new H.service.Platform({
			'app_id': '<?=$GLOBALS['HERE']['ID']?>', 
			'app_code': '<?=$GLOBALS['HERE']['CODE']?>'
		});

  	var defaultLayers = platform.createDefaultLayers();

    function addDomMarker(map, o){
      var fill = o.Relleno;
      // console.log(o);

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
        var titulo = 'Información de ubicación';        
        var className = 'alerta_'+o.Tipo;
        if(o.Tipo == 'temperatura'){
          titulo = 'Alerta de Temperatura';
        }
        else if(o.Tipo == 'presion_baja'){
          titulo = 'Alerta de Presión Baja';
        }
        else if(o.Tipo == 'presion_alta'){
          titulo = 'Alerta de Presión Alta';
        }

        var pendiente = '<img src="./assets/img/pendiente_'+(( data.Pendiente > 0 )?'pos':'neg')+'.png" style="width:36px; height:36px" />';
        var contenido = '<div class="vel">Fecha: <strong>' + data.Fecha + '</strong></div>' + 
          '<div class="vel">Posición: <strong>' + data.Posicion + '</strong></div>' + 
          '<div class="'+((data.Tipo=='temperatura')?'label-temp':'')+'">Temperatura: <value>' + data.Temperatura + '</value>'+(data.Tipo=='temperatura' ? '  Máx: ' + data.MaxTemp: '')+'</div>' +
          '<div class="'+((data.Tipo=='presion_alta')?'label-presa':(data.Tipo=='presion_baja')?'label-presb':'')+'">Presión: <value>' + data.Presion +'</value>'+(data.Tipo=='presion_alta'?'  Máx: '+data.MaxPres:(data.Tipo=='presion_baja'?'  Mín: '+data.MinPres:''))+'</div>' +
          '<div class="vel">Velocidad Registrada: <strong>' + data.Velocidad + '</strong> Km/h</div>' + 
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
    
      // Marker for Chicago Bears home
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
  <?php }else if($map_api == 'google'){ ?>
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
          '<div class="vel">Posición: <strong>' + data.Posicion + '</strong></div>' + 
          '<div class="'+((data.Tipo=='temperatura')?'label-temp':'')+'">Temperatura: <value>' + data.Temperatura + '</value>'+(data.Tipo=='temperatura' ? '  Máx: ' + data.MaxTemp: '')+'</div>' +
          '<div class="'+((data.Tipo=='presion_alta')?'label-presa':(data.Tipo=='presion_baja')?'label-presb':'')+'">Presión: <value>' + data.Presion +'</value>'+(data.Tipo=='presion_alta'?'  Máx: '+data.MaxPres:(data.Tipo=='presion_baja'?'  Mín: '+data.MinPres:''))+'</div>' +
          '<div class="vel">Velocidad Registrada: <strong>' + data.Velocidad + '</strong> Km/h</div>' + 
          '<div class="vel">Altitud: <strong>' + data.Altura + ' msnm</strong></div>' +
          '<div class="vel">Pendiente: <small><strong>' + data.Pendiente + '&deg; / ' + data.Porcentaje + '%</strong></small> ' + ( data.Pendiente == 0 ? '' : pendiente ) + '</div>';
      var panel = '<div id="iw-container">' +
        '<div class="iw-title alerta_'+className+'">'+titulo+'</div>' +
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

    $.each(mapaCoords, function(i, gps){
      // console.log(gps);
      gps.Pendiente = 0;
      gps.Porcentaje = 0;
      gps.DV = 0;
      gps.DH = 0;
      if(i>0){
        // console.log(gps[i].LatLng);
        var DH = google.maps.geometry.spherical.computeDistanceBetween(
          new google.maps.LatLng(gps.LatLng.lat, gps.LatLng.lng), 
          new google.maps.LatLng(mapaCoords[i-1].LatLng.lat, mapaCoords[i-1].LatLng.lng));
        var DV = (gps.Altura - mapaCoords[i-1].Altura);
        gps.Porcentaje = Math.abs(Math.round((DV/DH)*100));
        gps.Pendiente = Math.round( (Math.atan(DV/DH)*180)/Math.PI);
        gps.DV = Math.round(DV);
        gps.DH = Math.round(DH);
      }
      var panel = crearContenidoPopup(gps,titulos[gps.Tipo],gps.Tipo);

      marker.push(new google.maps.Marker({
        position: gps.LatLng,
        map: map,
        info: panel,
        icon: {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
          fillColor: gps.Relleno,
          strokeColor: gps.Linea,
          strokeWeight: 1,
          scale: 3,
          fillOpacity: 1,
          rotation: gps.Angulo,
        },
        zIndex: ( gps.Tipo == 'temperatura' ? 99 : 10 )
      }));

      marker[marker.length-1].addListener('click', function(){
        infoWindow.setContent(this.info);
        infoWindow.setPosition(this.getPosition());
        infoWindow.open(map);
      });
    });   
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

    $("#filtro").on("click", function(evt){
      $("#modalFiltro").modal('toggle');
    });
  });
</script>
