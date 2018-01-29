<?php
  require 'autoload.php';

  $acc = new Acceso();

  $gen = new General();

  $verneumaticosegun  = $gen->getParamValue('verneumaticosegun');
  $unidad_presion     = $gen->getParamValue('unidad_presion');
  $unidad_temperatura = $gen->getParamvalue('unidad_temperatura');
  $img_equipo         = $gen->getImagenesEquipo();

  $db = DB::getInstance();
  
  $sql = "SELECT * FROM (
    SELECT DISTINCTROW
      c.ID_CAMION,
      c.tipo AS TIPOEQUIPO,
      c.NUMCAMION,
      po.NOMENCLATURA,
      s.CODSENSOR,
      s.TIPO,
      n.NUMIDENTI,
      n.NUMEROFUEGO,
      n.MODELO,
      CONCAT(p.MARCA,' ',p.MODELO,' ',p.DIMENSION) AS PLANTILLA,
      u.tempmax,
      u.presmax,
      u.presmin,
      u.eventotemperatura,
      u.eventopresion,
      u.eventobateria, 
      u.fecha_evento,
      p.PIF 
            
      FROM uman_camion c 
      LEFT JOIN uman_neumatico_camion nc ON c.ID_CAMION=nc.ID_EQUIPO
      LEFT JOIN uman_neumaticos n ON n.ID_NEUMATICO=nc.ID_NEUMATICO
      LEFT JOIN uman_ultimoevento u ON u.posicion=nc.ID_POSICION
      LEFT JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR
      LEFT JOIN uman_plantilla p ON p.ID_PLANTILLA=n.ID_PLANTILLA
      LEFT JOIN uman_posicion po ON po.POSICION=nc.ID_POSICION
      WHERE u.fecha_evento NOT LIKE '0000-00-00%' 
      ORDER BY u.fecha_evento DESC, nomenclatura ASC, c.ID_CAMION ASC) as X
    GROUP BY ID_CAMION, NOMENCLATURA";
  $emisiones = $db->query($sql);

  $link_emisiones = 'genera-documento.php?documento=informe_diario_emisiones&tipo=xlsx';
  $link_pif = 'genera-documento.php?documento=informe_diario_pif&tipo=xlsx';

  $TITULO    = $module_label; //'Reporte de estado de emisiones y configuración';
  $SUBTITULO = '';
?>
<style>
 <?php include_once("assets/css/detalle-equipo.css") ?>
 <?php include_once("assets/css/uman/tabla.css") ?>
 
  .col-xs-12{
    margin: 0;
    padding: 0;
  }
  .modal-dialog{
    width: 60%;
  }
  .tab-content{
    margin-top: -5px;
    background: white;
    border: thin solid #ddd;
    padding: 2px;
    border-radius: 4px;
  }  
  .tabla-resumen, .tabla-pif{
    background: #fff;
  }
  .tabla-resumen > thead :first-child > :nth-child(2),
  .tabla-resumen > thead :first-child > :nth-child(3),
  .tabla-resumen > thead :first-child > :nth-child(4),
  .tabla-resumen > thead :nth-child(2) > :nth-child(1),
  .tabla-resumen > thead :nth-child(2) > :nth-child(2),
  .tabla-resumen > thead :nth-child(2) > :nth-child(12),
  .tabla-resumen > thead :nth-child(2) > :nth-child(13),
  .tabla-resumen > thead :nth-child(2) > :nth-child(14) {
    border: thin solid #ECEFF1;
    /*border-bottom: thin solid #ECEFF1;*/
  }
  .tabla-resumen > thead :nth-child(2) > :nth-child(3),
  .tabla-resumen > thead :nth-child(2) > :nth-child(5),
  .tabla-resumen > thead :nth-child(2) > :nth-child(9) {
    border-left: thin solid #ECEFF1;
    /*border-bottom: thin solid #ECEFF1;*/
  }
  .tabla-resumen > thead :nth-child(1) > :nth-child(n+1),
  .tabla-resumen > thead :nth-child(2) > :nth-child(n+1)
  {
    border-bottom: 2px solid #ECEFF1;
  }
  .tabla-resumen > tbody > tr > td{
    border-bottom: 1px solid white;
  }
  .tabla-resumen > tbody > tr > :nth-child(2),
  .tabla-resumen > tbody > tr > :nth-child(3),
  .tabla-resumen > tbody > tr > :nth-child(5),
  .tabla-resumen > tbody > tr > :nth-child(9),
  .tabla-resumen > tbody > tr > :nth-child(13),
  .tabla-resumen > tbody > tr > :nth-child(14),
  .tabla-resumen > tbody > tr > :nth-child(15),
  .tabla-resumen > tbody > tr > :nth-child(16) {
    border-left: thin solid #ECEFF1;
  }
  .tabla-resumen > thead, .tabla-pif > thead {
    border: thin solid #ECEFF1;
  }
  .tabla-resumen td, .tabla-pif td{
    text-align: center;
  }
  .tabla-resumen td span {
    /* font-size: 18px;  */
    font-weight: 800;
  }
  .tabla-resumen th, .tabla-pif th{
    text-align: center;
    font-weight: 800;
    /* font-size: 16px; */
  }
  .tabla-resumen td, .tabla-pif td{
    padding: 2px !important;
  }
  .btn span.glyphicon {    			
	  opacity: 0;				
  }
  .btn.active span.glyphicon {				
    opacity: 1;				
  }
  .chart {
      min-width: 320px;
      max-width: 98%;
      height: 100%;
      margin: 0 auto;
  }
  .fake-check{
    width: 40px;
    height: 34px;
    /* background-color:#E0E0E0; */
    margin-left:-3px;
    border-radius: 3px;
    display: none;
  }
  .fake-check span.glyphicon{
    opacity: 1;
    color: #37474F;
  }
  @media (max-width: 425px){
    #contenedor-datos{
      margin: auto -20px;
      padding: 0;
    }
    .tabla-resumen{
      margin: 0;
    }
    .modal-dialog{
      width: 98%;
    }
    .modal-body{
      -ms-transform: scale(0.9, 0.9); /* IE 9 */
      -webkit-transform: scale(0.9, 0.9); /* Safari */
      transform: scale(0.9, 0.9);
    }
    .modal-body table.tabla-resumen{
      margin: 0 0 0 -25px;
    }
    .tabla-resumen td span{
      /* font-size: 14px;  */
      font-weight: 800;
      text-align: center;
    }
  }
</style>

<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<script src="assets/js/moment.js"></script>

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">
  <!-- TÍTULO DE PÁGINA -->
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <!-- MENÚ DE PÁGINA -->
  <div class="filtro-contenido"></div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#emisiones" aria-controls="emisiones" role="tab" data-toggle="tab">Emisiones</a></li>
      <li role="presentation"><a href="#pif" aria-controls="pif" role="tab" data-toggle="tab">PIF</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade in active" id="emisiones">
        <table class="table table-responsive tabla-resumen" id="tabla-emisiones">
          <thead>
            <tr>
              <th colspan=2>&nbsp;</th>
              <th colspan=2>Sensor</th>
              <th colspan=4>Neumático</th>
              <th colspan=4>Umbrales</th>
              <th colspan=4>&nbsp;</th>
            </tr>
            <tr>
              <th>Equipo</th>
              <th>Posición</th>
              <th>Código</th>
              <th>Tipo</th>
              <th>N&deg; Serie</th>
              <th>N&deg; Fuego</th>
              <th>Modelo</th>
              <th>Plantilla</th>
              <th>T&deg; Máx.</th>
              <th>P&deg; Máx.</th>
              <th>P&deg; Mín.</th>
              <th>P.I.F</th>
              <th>Temperatura</th>
              <th>Presión</th>
              <th>Batería</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th><input type="text" class="foot-filter emisiones" data-index="0" placeholder="Equipo" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="1" placeholder="Posición" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="2" placeholder="Código" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="3" placeholder="Tipo Sensor" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="4" placeholder="N&deg; Serie" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="5" placeholder="N&deg; Fuego" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="6" placeholder="Modelo" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="7" placeholder="Plantilla Neumático" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="8" placeholder="T° Máxima" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="9" placeholder="P° Máxima" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="10" placeholder="P° Mínima" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="11" placeholder="PIF" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="12" placeholder="T°" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="13" placeholder="P°" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="14" placeholder="Batería" style="width:100%" /></th>
              <th><input type="text" class="foot-filter emisiones" data-index="15" placeholder="Fecha" style="width:100%"/></th>
            </tr>
          </tfoot>
          <tbody>
            <?php
              if($emisiones->count() > 0){
                foreach($emisiones->results() as $e){
                  // $cod_neum = $verneumaticosegun == 'fuego' ? ($e->NUMEROFUEGO != '' ? $e->NUMEROFUEGO : $e->NUMIDENTI) : ($e->NUMIDENTI != '' ? $e->NUMIDENTI : $e->NUMEROFUEGO);
                  echo '<tr>';
                  echo '<td>'.$img_equipo[$e->ID_CAMION]['DIV36'].'<br/>'.$e->NUMCAMION.'</td>';
                  echo "<td>{$e->NOMENCLATURA}</td>";
                  echo "<td>{$e->CODSENSOR}</td>";
                  echo '<td>'.Core::imagen_sensor($e->TIPO,24,'margin:0 auto -18px auto !important').'<br/>'.$e->TIPO.'</td>';
                  echo "<td>{$e->NUMIDENTI}</td>";
                  echo "<td>{$e->NUMEROFUEGO}</td>";
                  echo "<td>{$e->MODELO}</td>";
                  echo "<td>{$e->PLANTILLA}</td>";
                  echo '<td>'.Core::tpConvert($e->tempmax, $unidad_temperatura, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->presmax, $unidad_presion, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->presmin, $unidad_presion, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->PIF, $unidad_presion, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->eventotemperatura, $unidad_temperatura, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->eventopresion, $unidad_presion, true).'</td>';
                  echo '<td>'.$e->eventobateria.'%</td>';
                  $style = '""';
                  $dif = date_diff(new DateTime($e->fecha_evento), new DateTime(date('Y-m-d H:i:s')));
                  if($dif->format('%a') == 1) $style = '"background: yellow;"';
                  else if($dif->format('%a') > 1) $style = '"background: red; color: white;"';
                  echo "<td style={$style}>".(new DateTime($e->fecha_evento))->format('d/m/Y H:i:s').'</td>';
                  echo '</tr>';
                }
              }
            ?>
          </tbody>
        </table>
      </div>
      <div role="tabpanel" class="tab-pane fade" id="pif">
        <table class="table table-responsive tabla-pif" id="tabla-pif">
          <thead>
            <tr>
              <th>Equipo</th>
              <th>Posición</th>
              <th>PIC</th>
              <th>PIF Planificada</th>
              <th>PIF Real</th>
              <th>Desviación</th>
              <th>Plantilla</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th><input type="text" class="foot-filter pif" data-index="0" placeholder="Equipo" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="1" placeholder="Posición" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="2" placeholder="PIC" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="3" placeholder="PIF Planificada" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="4" placeholder="PIF Real" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="5" placeholder="Desviación" style="width:100%" /></th>
              <th><input type="text" class="foot-filter pif" data-index="6" placeholder="Plantilla" style="width:100%" /></th>
            </tr>
            </tfoot>
          <tbody>
            <?php
              if($emisiones->count() > 0){
                foreach($emisiones->results() as $e){
                  $pif_real='';
                  $desviacion='';
                  $t='';	

                  if( (new DateTime($e->fecha_evento))->format('d/m') == date('d/m') ){
                    $pif_real = round(($e->eventopresion * 291.5) / ($e->eventotemperatura + 273.15));
                    $pic      = round((($e->eventotemperatura + 273.15)*$e->PIF)/291.5);
                    $desviacion = round( (($pif_real * 100) / $e->PIF) - 100 );
                  }

                  echo '<tr>';
                  echo '<td>'.$img_equipo[$e->ID_CAMION]['DIV36'].'<br/>'.$e->NUMCAMION.'</td>';
                  echo "<td>{$e->NOMENCLATURA}</td>";
                  echo '<td>'.Core::tpConvert($pic, $unidad_presion, true).'</td>';
                  echo '<td>'.Core::tpConvert($e->PIF, $unidad_presion, true).'</td>';
                  echo '<td>'.Core::tpConvert($pif_real, $unidad_presion, true).'</td>';
                  echo '<td>'.$desviacion.' %</td>';
                  echo "<td>{$e->PLANTILLA}</td>";
                  echo '</tr>';
                }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var tabla_emisiones; 
  var tabla_pif;
  $(function(){
    tabla_emisiones = $("#tabla-emisiones").DataTable({
      dom: 'Brtip',
      searching: true,
      order: [0, 'asc'],
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Emisiones - <?=date("d-m-Y")?>",
            title: "Emisiones - <?=date("d-m-Y")?>"
          }, 
          {
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Completo</span>',
            className: 'btn btn-info',
            action: function(e, dt, node, config){
              window.open('<?=$link_emisiones?>');
            }
          },
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "Emisiones - <?=date("d-m-Y")?>"
          }
        ]
      },
      columnDefs: [
        { "orderable": false, "targets": 0 },
      ],
      autoWidth: false,
      paging: true,
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    })
    .on("init.dt", function(){
      var wt = parseInt($("#contenido").css("width"));
      var wb = 330;
      var x = wt - wb ;
      var top = 25;
      var left = 3;
      if(wt <= 768){
        wb = 120;
        x = wt - wb;
        if(wt <= 490){
          top = -50;
          left = -1;
        }
      }
      $("div.dt-buttons.btn-group")
        .css("margin", "auto "+x+"px auto "+left+"px")
        .css("width",wb+"px");
    });
    $( ".foot-filter.emisiones" ).on( 'keyup change', function () {
      tabla_emisiones.columns($(this).data('index')).search( this.value ).draw();
    });

    tabla_pif = $("#tabla-pif").DataTable({
      dom: 'Brtip',
      searching: true,
      order: [0, 'asc'],
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "PIF - <?=date("d-m-Y")?>",
            title: "PIF - <?=date("d-m-Y")?>"
          }, 
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "PIF - <?=date("d-m-Y")?>"
          }
        ]
      },
      autoWidth: false,
      paging: true,
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    })
    .on("init.dt", function(){
      var wt = parseInt($("#contenido").css("width"));
      var wb = 330;
      var x = wt - wb ;
      var top = 25;
      var left = 3;
      if(wt <= 768){
        wb = 120;
        x = wt - wb;
        if(wt <= 490){
          top = -50;
          left = -1;
        }
      }
      $("div.dt-buttons.btn-group")
        .css("margin", "auto "+x+"px auto "+left+"px")
        .css("width",wb+"px");
    });
    $( ".foot-filter.pif" ).on( 'keyup change', function () {
      tabla_pif.columns($(this).data('index')).search( this.value ).draw();
    });
  });
</script>