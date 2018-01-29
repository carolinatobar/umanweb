<?php
 require 'autoload.php';
  // error_reporting(E_ALL);

  $db = DB::getInstance();
  $gn = new General();

  $nomenclatura = $gn->getNomenclaturas();
  $uPres = $gn->getParamValue('unidad_presion','psi');
  $uTemp = $gn->getParamValue('unidad_temperatura','celsius');

  // date_default_timezone_set("America/Santiago");
  $equipo = isset($_POST['equipo'])  && is_numeric($_POST['equipo']) ? $_POST['equipo'] : NULL;
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
  $link = 'genera-documento.php?documento=presion_temperatura&equipo='.$equipo.'&fecha='.$fecha.'&tipo=';

  $texto_fecha = $fecha;

  $titulo = '';

  if( isset ( $_POST['fecha'] ) ) {
    // print '<center>'.$_POST['fecha'].'</center>';
    if(stripos($_POST['fecha'],' - ') > 0)
    {
    $fecha = explode(" - ", $_POST['fecha']);
    if($fecha[0] != $fecha[1])
    {
      $fecha = "UNIX_TIMESTAMP(e.EVENTOFECHA) 
      BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
    }
    else
    {
      $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(e.EVENTOFECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
    }
    }
    else
    {
    $fecha = $_POST['fecha'];
    $fecha = "UNIX_TIMESTAMP(e.EVENTOFECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
    }
  } 
  else {
    $fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
    $fecha2 = date('d/m/Y H:i:s');
    $fecha = "UNIX_TIMESTAMP(e.EVENTOFECHA) 
    BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$tiempo} HOUR)) AND UNIX_TIMESTAMP(NOW())";

    $titulo = "<h4>DESDE <strong>{$fecha1}</strong>  HASTA <strong>{$fecha2}</strong></h4>";
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

  $max_temp = 0;
  $min_pres = 100;
  $max_pres = 0;
  $min_value = 100;
  $max_value = 100;

  $db = DB::getInstance();

  $eq = new Equipo();
  $nom_equipo = $eq->obtener_nombre($equipo);
  $extra = $eq->listar_this($equipo);

  $final_json = NULL;
  if($equipo){
    $sql   = "SELECT e.EVENTOFECHA, e.EVENTOPRESION, e.EVENTOTEMPERATURA, e.EVENTOPOSICION, e.EVENTONUMCAMION,
     e.TEMPMAX, e.PRESMAX, e.PRESMIN  
    FROM uman_eventos AS e 
    WHERE e.EVENTONUMCAMION = {$equipo} AND ({$fecha}) 
    ORDER BY e.EVENTOPOSICION ASC, e.EVENTOFECHA ASC";
    // echo $sql;exit();
    $fetch = $db->query($sql);
    $fetch = ($fetch->count()>0) ? $fetch->results() : array();

    $titulo_grafico = "NO EXISTE INFORMACION EN LAS FECHAS SELECCIONADAS";

    $ultimafecha = array_fill(0,17,0);
    $ultimafecha2 = 0;

    $tabla = array();
    
    foreach($fetch as $row)
    {
      if($row->TEMPMAX > $max_temp) $max_temp = $row->TEMPMAX;
      if($row->PRESMAX > $max_pres) $max_pres = $row->PRESMAX;
      if($row->PRESMIN < $min_pres) $min_pres = $row->PRESMIN;

      if(!isset($tabla[$row->EVENTOPOSICION])) $tabla[$row->EVENTOPOSICION] = array('marca'=>'','sensor'=>'','pmax'=>0,'pmin'=>999,'pmed'=>0,'tmax'=>0,'tmin'=>999,'tmed'=>0,'datos'=>0);

      $tabla[$row->EVENTOPOSICION]['datos']++;
      if($row->EVENTOPRESION > $tabla[$row->EVENTOPOSICION]['pmax']) $tabla[$row->EVENTOPOSICION]['pmax'] = $row->EVENTOPRESION;
      if($row->EVENTOPRESION < $tabla[$row->EVENTOPOSICION]['pmin']) $tabla[$row->EVENTOPOSICION]['pmin'] = $row->EVENTOPRESION;
      if($row->EVENTOTEMPERATURA > $tabla[$row->EVENTOPOSICION]['tmax']) $tabla[$row->EVENTOPOSICION]['tmax'] = $row->EVENTOTEMPERATURA;
      if($row->EVENTOTEMPERATURA < $tabla[$row->EVENTOPOSICION]['tmin']) $tabla[$row->EVENTOPOSICION]['tmin'] = $row->EVENTOTEMPERATURA;
      $tabla[$row->EVENTOPOSICION]['pmed'] += $row->EVENTOPRESION;
      $tabla[$row->EVENTOPOSICION]['tmed'] += $row->EVENTOTEMPERATURA;
    }

    foreach($extra as $e)
    {
      $tabla[$e->ID_POSICION]['marca'] = $e->MARCA;
      $tabla[$e->ID_POSICION]['sensor'] = $e->CODSENSOR;
    }
    
    $max_pres = intval($max_pres + 5);
    $max_temp = intval($max_temp + 5);
    $min_pres = intval($min_pres - 10);

    if($max_pres > $min_pres && $max_pres > $max_temp) $max_value = $max_pres;
    else if($min_pres > $max_temp) $max_value = $min_pres;
    else $max_value = $max_temp;

    foreach($fetch as $row){
      $fecha        = $row->EVENTOFECHA;
      $posicion     = $row->EVENTOPOSICION;
      $presion      = $row->EVENTOPRESION;
      $temperatura  = $row->EVENTOTEMPERATURA;
      $max_value    = $presion > $max_value ? $presion : ($temperatura > $max_value ? $temperatura : $max_value);
      
      if(!in_array($row->EVENTOPOSICION, $posiciones)) $posiciones[] = $row->EVENTOPOSICION;

      $ms           = ( strtotime($fecha) )* 1000; // correccion de -4 horas por offset del grafico y paso de segundos a milisegundos

      if ( $ms - $ultimafecha[$posicion] > 720000 ) {
        $presion_data[$posicion]['data'][]  = array($ms-1, NULL);
        $temp_data[$posicion]['data'][]     = array($ms-1, NULL);
      }

      $presion_data[$posicion]['data'][]  = array($ms, intval($presion));

      $temp_data[$posicion]['data'][]  = array($ms, intval($temperatura));

      $umbral_presion_baja[$posicion]['data'][] = array($ms, intval($row->PRESMIN));

      $umbral_presion_alta[$posicion]['data'][] = array($ms, intval($row->PRESMAX));

      $umbral_temperatura[$posicion]['data'][] = array($ms, intval($row->TEMPMAX));

      $ultimafecha[$posicion]  = $ms;

      if($row->EVENTOPRESION < $row->EVENTOTEMPERATURA && $row->EVENTOPRESION < $min_value) $min_value = $row->EVENTOPRESION;
      else if($row->EVENTOTEMPERATURA < $min_value) $min_value = $row->EVENTOTEMPERATURA;
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
        var tipo = this.series.name.substring(1);
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
        name: '<?php echo "{$pos}P" ?>',
        data: presion<?php echo $pos ?>,
        zIndex: 2,
        color: '<?= Core::$colorLineaPosicion[$pos] ?>',
        lineWidth: 2,
        marker: {
          fillColor: '<?= Core::$colorLineaPosicion[$pos] ?>',
          lineWidth: 2,
        },        
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$pos] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      {
        name: '<?php echo "{$pos}UPB" ?>',
        data: umbral_pres_baja<?php echo $pos ?>,
        lineWidth: 1,
        color: '#1A237E',
        zIndex: 1,
        marker: { enabled: false },
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$pos] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      {
        name: '<?php echo "{$pos}UPA" ?>',
        data: umbral_pres_alta<?php echo $pos ?>,
        lineWidth: 1,
        color: '#1A237E',
        zIndex: 0,
        marker: { enabled: false },
        showInLegend: false,
        data2: {
          posicion: '<?php echo $nomenclatura[$pos] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uPres) ?>',
        },
      },
      <?php } ?>
      {
        name: '<?php echo "{$pos}T" ?>',
        data: temperatura<?php echo $pos ?>,
        dashStyle: "ShortDash",
        zIndex: 4,
        color: '<?= Core::$colorLineaPosicion[$pos] ?>',
        lineWidth: 2,
        marker: {
          fillColor: '<?= Core::$colorLineaPosicion[$pos] ?>',
          lineWidth: 2,
        },
        data2: {
          posicion: '<?php echo $nomenclatura[$pos] ?>',
          valueSuffix: ' <?php echo Core::obtener_simbolo_unidad($uTemp) ?>',
        },
        showInLegend: false,
      },
      {
        name: '<?php echo "{$pos}UT" ?>',
        data: umbral_temp<?php echo $pos ?>,
        lineWidth: 1,
        color: '#b71c1c',
        zIndex: 3,
        showInLegend: false,
        marker: { enabled: false },
        data2: {
          posicion: '<?php echo $nomenclatura[$pos] ?>',
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
      <span class="pull-left"><?php echo $nomenclatura[$pos] ?></span>
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-info active" style="background-color: <?= Core::$colorLineaPosicion[$pos] ?>">
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

<?php
  if($titulo==''){
?>
<!-- TABLA RESUMEN -->
<div class="<?php Core::col(12) ?>" style="overflow-x: auto">
<?php
  if(count($tabla)>0)
  {
    echo '<table class="table table-responsive tabla-resumen" style="background:#f8f8f8">';
    echo '<thead>';
    echo '<th>Posición</th><th>Marca</th><th>N&deg; Sensor</th><th>Presión Máx.</th><th>Presión Mín.</th><th>Media</th>';
    echo '<th>Temp. Máx.</th><th>Temp. Mín</th><th>Media</th><th>Cant. Datos</th>';
    echo '</thead>';
    echo '<tbody>';
    foreach($tabla as $pos => $t)
    {
      echo '<tr>';
      echo '<td><div class="marcador pull-left" style="background-color: '.Core::$colorLineaPosicion[$pos].'">&nbsp;</div>'.$nomenclatura[$pos].'</td>';
      echo '<td>'.(isset($t['marca']) ? $t['marca'] : '').'</td>';
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
<?php
  }
?>

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
                <span><?php echo $nomenclatura[$pos] ?>&nbsp;&nbsp;&nbsp;</span>
                <div class="btn-group" data-toggle="buttons">
                  <label class="btn btn-info active" style="background-color: <?= Core::$colorLineaPosicion[$pos] ?>">
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