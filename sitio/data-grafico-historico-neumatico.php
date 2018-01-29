<?php
 require 'autoload.php';
  // error_reporting(E_ALL);

  $db = DB::getInstance();
  $gn = new General();

  $nomenclatura = $gn->getNomenclaturas();
  $uPres = $gn->getParamValue('unidad_presion','psi');
  $uTemp = $gn->getParamValue('unidad_temperatura','celsius');

  // date_default_timezone_set("America/Santiago");
  $neumatico = isset($_POST['neumatico'])  && is_numeric($_POST['neumatico']) ? $_POST['neumatico'] : NULL;
  $fecha  = isset($_POST['fecha'])  ? $_POST['fecha']  : false;
  $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : 24;
  $maxContentHeight = isset($_POST['maxContentHeight']) ? $_POST['maxContentHeight'] : 500;

  //Default Time
  $df_time1  = "00:00:00";
  $df_time2  = "23:59:59";


  if(!isset($_POST['fecha'])){
    $ayer   = date("m/d/Y H:i:s",time() - ($tiempo*3600));
    $hoy    = date("m/d/Y H:i:s",time());
    $fecha  = "$ayer - $hoy";
  }
  
  // $link = 'doc-gen/presion-temperatura.php?equipo='.$equipo.'&fecha='.$fecha.'&tipo=';
  $link = 'genera-documento.php?documento=historico_neumatico&neumatico='.$neumatico.'&fecha='.$fecha.'&tipo=';

  $texto_fecha = $fecha;

  $titulo = '';
  $sql_fecha = '';

  if( isset ( $_POST['fecha'] ) ) {
    // print '<center>'.$_POST['fecha'].'</center>';
    if(stripos($_POST['fecha'],' - ') > 0)
    {
    $fecha = explode(" - ", $_POST['fecha']);
    }
  } 
   

  $max_presion  = 0;
  $array_data   = array();
  $presion_data = array();
  $temp_data    = array();
  $vel_data     = array();
  $posiciones   = array();
  $umbral_presion_baja = array();
  $umbral_presion_alta = array();
  $umbral_temperatura = array();

  

  $myDateTimeI = DateTime::createFromFormat('d/m/Y H:i', $fecha[0]);
  $fecha[0] = $myDateTimeI->format('Y-m-d H:i:s');

  $myDateTimeI = DateTime::createFromFormat('d/m/Y H:i', $fecha[1]);
  $fecha[1] = $myDateTimeI->format('Y-m-d H:i:s');

  $db = DB::getInstance();

  $final_json = NULL;
  if($neumatico){
    //tabla superior
    $sql   = "SELECT uh.*,uc.NUMCAMION,us.CODSENSOR FROM uman_historial uh INNER JOIN uman_camion uc ON uh.ID_CAMION=uc.ID_CAMION INNER JOIN uman_sensores us ON uh.ID_SENSOR=us.ID_SENSOR WHERE uh.ID_NEUMATICO ={$neumatico} AND (uh.ACCION LIKE  'Neumatico instalado en equipo' OR uh.ACCION LIKE  'Neumatico retirado de equipo') ORDER BY uh.FECHA ASC";
    // echo $sql;exit();
    $fetch = $db->query($sql);
    $fetch = ($fetch->count()>0) ? $fetch->results() : array();

    $titulo_grafico = "NO EXISTE INFORMACION DEL NEUMATICO SELECCIONADO";

    $ultimafecha = array_fill(0,17,0);
    $ultimafecha2 = 0;

    $tabla = array();
    $tabla2 = array();
    $tabla3 = array();

    for($i=0;$i<count($fetch);$i++){
      $titulo_grafico = '';
      $in = $fetch[$i];
      $i++;
      $tabla[$in->ID]= array('equipo'=>$in->NUMCAMION,'sensor'=>$in->CODSENSOR,'posicion'=>$in->ID_POSICION,'desde'=>$in->FECHA,'hasta'=>'','datos'=>0,'id_camion'=>$in->ID_CAMION,'id_sensor'=>$in->ID_SENSOR,'show_d'=>'','show_h'=>'');

      $desde = '-';
      $hasta = '-';
      
      $sql_datos = "SELECT COUNT( * ) AS tot
        FROM uman_eventos
        WHERE EVENTONUMCAMION ={$in->ID_CAMION}
        AND EVENTOPOSICION =$in->ID_POSICION
        AND EVENTOFECHA >=  '{$in->FECHA}'";
      if($i<count($fetch)){
        $out = $fetch[$i];
        $sql_fecha = "SELECT EVENTOFECHA
          FROM uman_eventos
          WHERE EVENTONUMCAMION ={$in->ID_CAMION}
          AND EVENTOPOSICION ={$in->ID_POSICION}
          AND EVENTOFECHA <=  '{$out->FECHA}'
          AND EVENTOFECHA >=  '{$in->FECHA}'
          ORDER BY EVENTOFECHA ASC";
        $hasta = '-';
        $datos_fecha = $db->query($sql_fecha);
        if($datos_fecha->count()>0){
          $datos_fecha2 = $datos_fecha->results()[0];
          $desde = $datos_fecha2->EVENTOFECHA;
          $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
          $hasta = $datos_fecha2->EVENTOFECHA;
        }
        $tabla[$in->ID]['hasta']=$hasta;
        $sql_datos .= " AND EVENTOFECHA <= '{$out->FECHA}'";
      }else{
        $sql_fecha = "SELECT EVENTOFECHA
          FROM uman_eventos
          WHERE EVENTONUMCAMION ={$in->ID_CAMION}
          AND EVENTOPOSICION ={$in->ID_POSICION}
          AND EVENTOFECHA >=  '{$in->FECHA}'
          AND EVENTOFECHA <=  NOW()
          ORDER BY EVENTOFECHA ASC";
        
        $datos_fecha = $db->query($sql_fecha);
        if($datos_fecha->count()>0){
          $datos_fecha2 = $datos_fecha->results()[0];
          $desde = $datos_fecha2->EVENTOFECHA;
          $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
          $hasta = $datos_fecha2->EVENTOFECHA;
        }
        
      }
      $tabla[$in->ID]['desde']=$desde;
      $tabla[$in->ID]['hasta']=$hasta;
      if($desde!='-'){
        $myDateTimeI = DateTime::createFromFormat('Y-m-d H:i:s', $desde);
        $desde = $myDateTimeI->format('d/m/Y H:i:s');
      }
      if($hasta!='-'){
        $myDateTimeI = DateTime::createFromFormat('Y-m-d H:i:s', $hasta);
        $hasta = $myDateTimeI->format('d/m/Y H:i:s');
      }
      $tabla[$in->ID]['show_d']=$desde;
      $tabla[$in->ID]['show_h']=$hasta;
      $datos = $db->query($sql_datos);
      $datos = $datos->results()[0];
      $tabla[$in->ID]['datos']=$datos->tot;
      
      if($tabla[$in->ID]['desde']!='-'){
        if(strtotime($tabla[$in->ID]['desde'])<=strtotime($fecha[0])){
          if($tabla[$in->ID]['hasta']!='-'){
            if(strtotime($tabla[$in->ID]['hasta'])>=strtotime($fecha[0])){
              $tabla2[$in->ID]=$tabla[$in->ID];
              if(strtotime($tabla[$in->ID]['desde'])<strtotime($fecha[0])){
                $tabla2[$in->ID]['desde']=$fecha[0];
              }
              if(strtotime($tabla[$in->ID]['hasta'])>strtotime($fecha[1])){
                $tabla2[$in->ID]['hasta']=$fecha[1];
              }
              $tabla2[$in->ID]['pmax']=0;
              $tabla2[$in->ID]['pmin']=999;
              $tabla2[$in->ID]['pmed']=0;
              $tabla2[$in->ID]['tmax']=0;
              $tabla2[$in->ID]['tmin']=999;
              $tabla2[$in->ID]['tmed']=0;
            }
          }else{
            $tabla2[$in->ID]=$tabla[$in->ID];
            if(strtotime($tabla[$in->ID]['desde'])<strtotime($fecha[0])){
              $tabla2[$in->ID]['desde']=$fecha[0];
            }
            $tabla2[$in->ID]['hasta']=$fecha[1];
            $tabla2[$in->ID]['pmax']=0;
            $tabla2[$in->ID]['pmin']=999;
            $tabla2[$in->ID]['pmed']=0;
            $tabla2[$in->ID]['tmax']=0;
            $tabla2[$in->ID]['tmin']=999;
            $tabla2[$in->ID]['tmed']=0;
          }
        }else{
          if(strtotime($tabla[$in->ID]['desde'])<=strtotime($fecha[1])){
            if($tabla[$in->ID]['hasta']!=''){
              if(strtotime($tabla[$in->ID]['hasta'])>=strtotime($fecha[1])){
                $tabla2[$in->ID]=$tabla[$in->ID];
                if(strtotime($tabla[$in->ID]['hasta'])>strtotime($fecha[1])){
                  $tabla2[$in->ID]['hasta']=$fecha[1];
                }         
              }else{
                $tabla2[$in->ID]=$tabla[$in->ID];
              }
              $tabla2[$in->ID]['pmax']=0;
              $tabla2[$in->ID]['pmin']=999;
              $tabla2[$in->ID]['pmed']=0;
              $tabla2[$in->ID]['tmax']=0;
              $tabla2[$in->ID]['tmin']=999;
              $tabla2[$in->ID]['tmed']=0;
            }else{
              $tabla2[$in->ID]=$tabla[$in->ID];
              $tabla2[$in->ID]['hasta']=$fecha[1];
              $tabla2[$in->ID]['pmax']=0;
              $tabla2[$in->ID]['pmin']=999;
              $tabla2[$in->ID]['pmed']=0;
              $tabla2[$in->ID]['tmax']=0;
              $tabla2[$in->ID]['tmin']=999;
              $tabla2[$in->ID]['tmed']=0;
            }
          }
        }
      }
    }

    //grafico

    foreach($tabla2 as $pos => $t){
      $sql = "SELECT e.EVENTOFECHA, e.EVENTOPRESION, e.EVENTOTEMPERATURA, e.EVENTOPOSICION, e.EVENTONUMCAMION,
     e.TEMPMAX, e.PRESMAX, e.PRESMIN  
        FROM uman_eventos AS e 
        WHERE EVENTONUMCAMION ={$t['id_camion']}
        AND EVENTOPOSICION ={$t['posicion']}
        AND EVENTOFECHA >=  '{$t['desde']}'
        AND EVENTOFECHA <=  '{$t['hasta']}'
        ORDER BY e.EVENTOFECHA ASC";


        $fetch = $db->query($sql);
        $fetch = ($fetch->count()>0) ? $fetch->results() : array();

        $max_temp = 0;
        $min_pres = 100;
        $max_pres = 0;
        $min_value = 100;
        $max_value = 100;

        foreach($fetch as $row)
        {
          if($row->TEMPMAX > $max_temp) $max_temp = $row->TEMPMAX;
          if($row->PRESMAX > $max_pres) $max_pres = $row->PRESMAX;
          if($row->PRESMIN < $min_pres) $min_pres = $row->PRESMIN;

          if($row->EVENTOPRESION > $t['pmax']) $t['pmax'] = $row->EVENTOPRESION;
          if($row->EVENTOPRESION < $t['pmin']) $t['pmin'] = $row->EVENTOPRESION;
          if($row->EVENTOTEMPERATURA > $t['tmax']) $t['tmax'] = $row->EVENTOTEMPERATURA;
          if($row->EVENTOTEMPERATURA < $t['tmin']) $t['tmin'] = $row->EVENTOTEMPERATURA;
          $t['pmed'] += $row->EVENTOPRESION;
          $t['tmed'] += $row->EVENTOTEMPERATURA;
        }
        $max_pres = intval($max_pres + 5);
        $max_temp = intval($max_temp + 5);
        $min_pres = intval($min_pres - 10);

        if($max_pres > $min_pres && $max_pres > $max_temp) $max_value = $max_pres;
        else if($min_pres > $max_temp) $max_value = $min_pres;
        else $max_value = $max_temp;

        if(!in_array($pos, $posiciones)) $posiciones[] = $pos;

        foreach($fetch as $row){
          $fecha        = $row->EVENTOFECHA;
          $posicion     = $row->EVENTOPOSICION;
          $presion      = $row->EVENTOPRESION;
          $temperatura  = $row->EVENTOTEMPERATURA;
          $max_value    = $presion > $max_value ? $presion : ($temperatura > $max_value ? $temperatura : $max_value);
          
          

          $ms           = ( strtotime($fecha) )* 1000; // correccion de -4 horas por offset del grafico y paso de segundos a milisegundos

          if ( $ms - $ultimafecha[$pos] > 720000 ) {
            $presion_data[$pos]['data'][]  = array($ms-1, NULL);
            $temp_data[$pos]['data'][]     = array($ms-1, NULL);
          }

          $presion_data[$pos]['data'][]  = array($ms, intval($presion));

          $temp_data[$pos]['data'][]  = array($ms, intval($temperatura));

          $umbral_presion_baja[$pos]['data'][] = array($ms, intval($row->PRESMIN));

          $umbral_presion_alta[$pos]['data'][] = array($ms, intval($row->PRESMAX));

          $umbral_temperatura[$pos]['data'][] = array($ms, intval($row->TEMPMAX));

          $ultimafecha[$pos]  = $ms;

          if($row->EVENTOPRESION < $row->EVENTOTEMPERATURA && $row->EVENTOPRESION < $min_value) $min_value = $row->EVENTOPRESION;
          else if($row->EVENTOTEMPERATURA < $min_value) $min_value = $row->EVENTOTEMPERATURA;
        }

        $tabla3[$pos]=$t;
    }
    // Mezclando array Presion + array Temperatura
    $final_array  = array_merge($presion_data, $temp_data);//, $umbral_presion_baja, $umbral_presion_alta, $umbral_temperatura);
    foreach ($array_data as $key => $array) {
      $final_array[]  = $array;
    }
    $final_json  = json_encode($final_array);
    
}
if($final_json != NULL){
    if($titulo!='') echo '<br/>';
?>
<style>
  #FiltroPopup{
    position: absolute; 
    top: 60px; 
    background-color: #ecf0f1; 
    width: 120px; 
    border: thin solid #CCC; 
    padding: 10px; 
    border-radius: 5px;
    z-index: 1055;
    display: none;
  }
  .btn span.glyphicon {         
    opacity: 0;       
  }
  .btn.active span.glyphicon {
    opacity: 1;       
  }
</style>
<script type="text/javascript">
  <?php 
    foreach($posiciones as $pos)
    {
      echo "\tvar presion{$pos}          = ".json_encode($presion_data[$pos]['data'])."\n";
      echo "\tvar temperatura{$pos}      = ".json_encode($temp_data[$pos]['data'])."\n";
      echo "\tvar umbral_temp{$pos}      = ".json_encode($umbral_temperatura[$pos]['data'])."\n";
      echo "\tvar umbral_pres_baja{$pos} = ".json_encode($umbral_presion_baja[$pos]['data'])."\n";
      echo "\tvar umbral_pres_alta{$pos} = ".json_encode($umbral_presion_alta[$pos]['data'])."\n";
      echo "\n";
    }
  ?>
</script>
<script type="text/javascript">
  Highcharts.setOptions({
    global:{
      useUTC: false,
      timezoneOffset: (7 * 60)
    },
    lang: {
      months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado']
    }
  });
  
  var chart = Highcharts.chart('graphContainer', {
    chart: {
      type: 'spline',
      zoomType: 'x',
      backgroundColor: '#f8f8f8',
      borderColor: '#335cad',
      height: <?=$maxContentHeight?>,
      resetZoomButton: {
        className: 'btn btn-info',
        position: {
          x: -130,
          y: 0
        }
      },
      events:{
        load: function(){
          var cWidth = $(".highcharts-container").css("width");
          console.log("cWidth: "+cWidth);
          <?php 
            if($titulo!=''){ 
          ?>
            $("#graphContainer").append(
              $('<div>')
                .css('position','relative').css('top','-<?=$maxContentHeight+30?>px').addClass("center-block")
                .html('<?php echo $titulo ?>')
            );
          <?php 
              $top = '-'.($maxContentHeight+30).'px';
            } 
            else{
              $top = '-'.($maxContentHeight-10).'px';
            }
          ?>
          $("#graphContainer").append(
            $('<button id="filtro" class="btn btn-info">')
            .html('<i class="fa fa-filter" aria-hidden="true"></i> Filtrar Gráfico')
            .css('position','relative').css('top','<?= $top ?>').css('width','120px').addClass("pull-right")
            <?php 
              if($titulo==''){ 
            ?>
            .attr('data-toggle','modal').attr('data-target','#modalFiltro'));
            <?php
              }
              else{
            ?>
            .bind('click', function(){
              var cWidth = $(".highcharts-container").css("width");
              $("#FiltroPopup").css("left","calc( " + cWidth + " - 105px )" );
              $("#FiltroPopup").toggle();
            })
          );
          <?php
            } //FIN ELSE 274 
          ?>        
        },
      }
    },
    title: { text: '' },
    xAxis: {
      type: 'datetime',
      labels: { overflow: 'justify' }
    },
    yAxis: {
      title: { text: '°C / PSI' },
      minorGridLineWidth: 1,
      gridLineWidth: 1,
      alternateGridColor: null,
      min: <?php echo $min_value ? $min_value : 0 ?>,
      max: <?php echo $max_value; ?>,
      tickInterval: 10,
      plotBands: []
    },
    tooltip: {
      formatter: function(){
        // console.log(this);

        var d = new Date(this.x);
        var name = this.series.name.split('.');
        var tipo =name[1];
        var data = '';
        if(tipo == 'P'){
          data += 'Fecha: ' + d.toLocaleDateString() + ' ' + d.toLocaleTimeString() + '<br/>';
          data += 'Posición: '+this.series.userOptions.data2.posicion+' <br/>';
          data += '<b>Presión: '+this.y + this.series.userOptions.data2.valueSuffix + '</b>';
        }
        else if(tipo == 'T'){
          data += 'Fecha: ' + d.toLocaleDateString() + ' ' + d.toLocaleTimeString() + '<br/>';
          data += 'Posición: '+this.series.userOptions.data2.posicion+' <br/>';
          data += '<b>Temperatura: '+this.y + this.series.userOptions.data2.valueSuffix + '</b>';
        }
        else if(tipo == 'UPA'){
          data += 'Umbral de Presión Alta <br/>';
          data += 'Posición: '+this.series.userOptions.data2.posicion+' <br/>';
          data += 'Presión Máxima: '+ this.y + this.series.userOptions.data2.valueSuffix;
        }
        else if(tipo == 'UPB'){
          data += 'Umbral de Presión Baja <br/>';
          data += 'Posición: '+this.series.userOptions.data2.posicion+' <br/>';
          data += 'Presión Máxima: '+ this.y + this.series.userOptions.data2.valueSuffix;
        }
        else if(tipo == 'UT'){
          data += 'Umbral de Temperatura <br/>';
          data += 'Posición: '+this.series.userOptions.data2.posicion+' <br/>';
          data += 'Temperatura Máxima: ' + this.y + this.series.userOptions.data2.valueSuffix;
        }
        return data;
      },
      // shared: true
    },
    credits: { enabled: false },
    plotOptions: {
      spline: {
        lineWidth: 1,
        states: {
          hover: {
            lineWidth: 2
          }
        },
        marker: {
          enabled: false
        },
        pointStart: 0
      }
    },
    series:[
      <?php
      foreach($posiciones as $pos)
      {
        if($_SESSION[session_id()]['faena'] != 'cerro_negro_norte'){
      ?>
      {
        name: '<?php echo "{$pos}.P" ?>',
        data: presion<?php echo $pos ?>,
        zIndex: 2,
        color: '<?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>',
        lineWidth: 2,
        marker: {
          fillColor: '<?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>',
          lineWidth: 2,
        },        
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      {
        name: '<?php echo "{$pos}.UPB" ?>',
        data: umbral_pres_baja<?php echo $pos ?>,
        lineWidth: 1,
        color: '#1A237E',
        zIndex: 1,
        marker: { enabled: false },
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      {
        name: '<?php echo "{$pos}.UPA" ?>',
        data: umbral_pres_alta<?php echo $pos ?>,
        lineWidth: 1,
        color: '#1A237E',
        zIndex: 0,
        marker: { enabled: false },
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      <?php } ?>
      {
        name: '<?php echo "{$pos}.T" ?>',
        data: temperatura<?php echo $pos ?>,
        dashStyle: "ShortDash",
        zIndex: 4,
        color: '<?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>',
        lineWidth: 2,
        marker: {
          fillColor: '<?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>',
          lineWidth: 2,
        },
        data2: {
          posicion: '<?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uTemp) ?>',
        },
        showInLegend: false,
      },
      {
        name: '<?php echo "{$pos}.UT" ?>',
        data: umbral_temp<?php echo $pos ?>,
        lineWidth: 1,
        color: '#b71c1c',
        zIndex: 3,
        showInLegend: false,
        marker: { enabled: false },
        data2: {
          posicion: '<?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uTemp) ?>',
        },
      },
      <?php } ?>

    ],
    navigation: {
      menuItemStyle: {
        fontSize: '10px'
      }
    }
  });

</script>
<?php 
}
else{
 echo('<h3>No hay datos en el rango de fecha seleccionado</h3>');
}
?>

<div id="FiltroPopup">
  <?php 
    foreach($posiciones as $pos){
  ?>
    <div class="checkboxes">
      <span class="pull-left"><?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?></span>
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-info active" style="background-color: <?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>">
          <input type="checkbox" autocomplete="off" class="posicion-popup" data-posicion="<?php echo $pos ?>" checked>
          <span class="glyphicon glyphicon-ok"></span>
        </label>
      </div>
    </div>
  <?php
    }
  ?>
  <br/>
</div>
<!-- TABLA RESUMEN -->
<div class="<?php Core::col(12) ?>" style="overflow-x: auto">
<?php
  if(count($tabla)>0)
  {
    echo '<table class="table table-responsive tabla-resumen" style="background:#f8f8f8">';
    echo '<thead>';
    echo '<th>Equipo</th><th>Posición</th><th>N&deg; Sensor</th><th>Desde</th><th>Hasta</th><th>Cantidad de Datos</th>';
    echo '</thead>';
    echo '<tbody>';
    foreach($tabla as $pos => $t)
    {
      echo '<tr>';
      echo '<td>'.(isset($t['equipo']) ? $t['equipo'] : '').'</td>';
      echo '<td>'.$nomenclatura[$t['posicion']].'</td>';
      echo '<td>'.(isset($t['sensor']) ? $t['sensor'] : '').'</td>';
      echo '<td>'.(isset($t['desde']) ? $t['show_d'] : '').'</td>';
      echo '<td>'.(isset($t['hasta']) ? $t['show_h'] : '').'</td>';
      echo '<td>'.(isset($t['datos']) ? $t['datos'] : '').'</td>';      
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
  }else{
echo('<h3>No hay datos en para el neumático seleccionado</h3>');
}
?>
</div>
<div class="<?php Core::col(12) ?>" style="overflow-x: auto">
<?php
  if(count($tabla3)>0)
  {
    echo '<table class="table table-responsive tabla-resumen" style="background:#f8f8f8">';
    echo '<thead>';
    echo '<th>Equipo</th><th>Posición</th><th>N&deg; Sensor</th><th>Presión Máx.</th><th>Presión Mín.</th><th>Media</th>';
    echo '<th>Temp. Máx.</th><th>Temp. Mín</th><th>Media</th><th>Cant. Datos</th>';
    echo '</thead>';
    echo '<tbody>';
    foreach($tabla3 as $pos => $t)
    {
      echo '<tr>';
      echo '<td><div class="marcador pull-left" style="background-color: '.Core::$colorLineaPosicion[$t['posicion']].'">&nbsp;</div>'.$t['equipo'].'</td>';
      echo '<td>'.$nomenclatura[$t['posicion']].'</td>';
      echo '<td>'.(isset($t['sensor']) ? $t['sensor'] : '').'</td>';
      echo '<td>'.(isset($t['pmax']) ? $t['pmax'] : '').'</td>';
      echo '<td>'.(isset($t['pmin']) ? $t['pmin'] : '').'</td>';
      echo '<td>'.(isset($t['datos']) && ($t['datos'] > 0) ? intval($t['pmed'] / $t['datos']) : '').'</td>';
      echo '<td>'.(isset($t['tmax']) ? $t['tmax'] : '').'</td>';
      echo '<td>'.(isset($t['tmin']) ? $t['tmin'] : '').'</td>';
      echo '<td>'.(isset($t['datos']) && ($t['datos'] > 0) ? intval($t['tmed'] / $t['datos']) : '').'</td>';
      echo '<td>'.(isset($t['datos']) ? $t['datos'] : '').'</td>';      
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
  }
?>
</div>
<div class="<?php Core::col(12) ?>" style="overflow-x: auto">
  <a href="<?php echo $link.'xls' ?>" class="btn btn-success center-block" style="min-width: 100px; width:25%; ">
    <i class="fa fa-file-excel-o" aria-hidden="true"></i>  Descargar
  </a>
</div>
<div id="graphContainer"></div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalFiltro">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Filtrar elementos del gráfico</h4>
      </div>
      <div class="modal-body">
        <table class="table table-responsive tabla-resumen" style="background:#f8f8f8" id="tabla-seleccion">
          <thead>
            <th>Posición</th>
            <th width="16%">T&deg;</th>
            <th width="16%">Umbral T&deg;</th>
            <th width="16%">P&deg;</th>
            <th width="16%">Umbral P&deg; Baja</th>
            <th width="16%">Umbral P&deg; Alta</th>
          </thead>
          <tbody>
            <?php 
              foreach($posiciones as $pos){
                $seriesName = "{$pos}";
            ?>
            <tr>
              <td>
                <span><?php echo $nomenclatura[$tabla3[$pos]['posicion']] ?>&nbsp;&nbsp;&nbsp;</span>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-info active" style="background-color: <?= Core::$colorLineaPosicion[$tabla3[$pos]['posicion']] ?>">
                    <input type="checkbox" autocomplete="off" class="posicion" data-posicion="<?php echo $pos ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                </div>
              </td>
              <td>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="checkbox" autocomplete="off" class="pos<?php echo $pos ?>" data-name="<?php echo "{$pos}T" ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                </div>

                <label class="btn fake-check" id="disabled_<?php echo $pos ?>T">
                  <span class="glyphicon glyphicon-remove"></span>
                </label>
              </td>
              <td>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="checkbox" autocomplete="off" class="pos<?php echo $pos ?>" data-name="<?php echo "{$pos}UT" ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                </div>

                <label class="btn fake-check" id="disabled_<?php echo $pos ?>UT">
                  <span class="glyphicon glyphicon-remove"></span>
                </label>
              </td>
              <td>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="checkbox" autocomplete="off" class="pos<?php echo $pos ?>" data-name="<?php echo "{$pos}P" ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                </div>

                <label class="btn fake-check" id="disabled_<?php echo $pos ?>P">
                  <span class="glyphicon glyphicon-remove"></span>
                </label>
              </td>
              <td>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="checkbox" autocomplete="off" class="pos<?php echo $pos ?>" data-name="<?php echo "{$pos}UPB" ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                </div>

                <label class="btn fake-check" id="disabled_<?php echo $pos ?>UPB">
                  <span class="glyphicon glyphicon-remove"></span>
                </label>
              </td>
              <td>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="checkbox" autocomplete="off" class="pos<?php echo $pos ?>" data-name="<?php echo "{$pos}UPA" ?>" checked>
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>

                  <label class="btn fake-check" id="disabled_<?php echo $pos ?>UPA">
                    <span class="glyphicon glyphicon-remove"></span>
                  </label>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <div id="loader-modal" class="loader center-block hidden"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="aplicarFiltro">Aplicar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script type="text/javascript">
  $(function(){
    $("input[type=checkbox].posicion").change(function(evt){
      // console.log(evt);
      if(evt.className != 'posicion popup'){
        var pos = $(evt.currentTarget).data('posicion');      
        var ischecked = false;
        ischecked = $(evt.currentTarget).prop('checked');
        console.log($("input[type=checkbox].pos"+pos));
        $("input[type=checkbox].pos"+pos).each(function(i,o){
          // console.log($(o)[0].parentElement);
          var name = $(o).data("name");
          if(ischecked){
            $(o).attr("checked",true).prop('checked', true).attr('disabled',false).prop('disabled',false);
            $($(o)[0].parentElement).addClass('active').attr('disabled',false).prop('disabled',false).show('fast');
            $("#disabled_"+name).hide('fast');
          }
          else{
            $(o).attr("checked",false).prop('checked', false).attr('disabled',true).prop('disabled',true);
            $($(o)[0].parentElement).removeClass('active').attr('disabled',true).prop('disabled',true).hide('fast');
            $("#disabled_"+name).show('fast');
          }
        });
      }
    });

    $("input[type=checkbox].posicion-popup").change(function(evt){
      console.log(evt.currentTarget);
      var pos = $(evt.currentTarget).data('posicion');      
      var isChecked = false;
      isChecked = $(evt.currentTarget).prop('checked');
      console.log(isChecked);

      var dp = $(evt.currentTarget).data('posicion');
      var dn = '#'+dp+'UPA'+dp+'UPB'+dp+'UT'+dp+'P'+dp+'T#';
      console.log(dn);

      for(i=0; i<chart.series.length; i++){
        var sn = chart.series[i].name;
        if(dn.indexOf(sn)>0){
          if(isChecked){
            if(!chart.series[i].visible) chart.series[i].setVisible(true, false);
          }
          else{
            if(chart.series[i].visible) chart.series[i].setVisible(false, false);
          }
        }
      }
    });

    function filtrar(){
      var chk = $("#modalFiltro div.modal-body").find("input[type=checkbox]");
      var d = new Date();
      var t1 = d.getTime();
      console.log(d.toLocaleTimeString());
      var mostrar = [];
      var ocultar = [];
      $.each(chk, function(i,o){
        if($(o).prop('checked'))
        {
          var dp = $(o).data('posicion');
          var dn = $(o).data('name');

          if(dn != undefined)
          {
            mostrar.push(dn);
          }
        }
        else
        {
          var dp = $(o).data('posicion');
          var dn = $(o).data('name');
          if(dn != undefined)
          {
            ocultar.push(dn);
          }
        }
      });

      console.log(mostrar);
      console.log(ocultar);

      for(i=0; i<chart.series.length; i++){
        chart.series[i].setVisible(false,false);

        for(j=0; j<mostrar.length; j++){
          if(chart.series[i].name == mostrar[j]){
            if(!chart.series[i].visible) chart.series[i].setVisible(true, false);
            mostrar.splice(j,1);
            break;
          }
        }
      }

      setTimeout(function(){
        $("#modalFiltro").modal('hide');
        $("#loader-modal").addClass("hidden");
        $("#tabla-seleccion").css('display', 'block');
        $("#modalFiltro div.modal-footer").css("display","block");
      }, 500);
    }

    $("#aplicarFiltro").click(function(){
      $("#loader-modal").removeClass("hidden");
      $("#tabla-seleccion").css('display','none');
      $("#modalFiltro div.modal-footer").css("display","none");

      setTimeout(filtrar, 500);
    });
  });
</script>