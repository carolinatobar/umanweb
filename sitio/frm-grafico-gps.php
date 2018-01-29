<?php
 require 'autoload.php';

 @session_start();
 $acceso = new Acceso($_SESSION, session_id());
 if(!$acceso->Permitido()) exit();

 $gen = new General();
 $img_equipo   = $gen->getImagenesEquipo();

 $f = new Flota();
 $f = $f->listar();
 $flota = array();
 foreach($f as $fx){ $flota[$fx->NUMFLOTAS] = $fx->NOMBRE; }

 $e = new Equipo();
 $option = array(); 
 $equipos = $e->listar();
 foreach($equipos as $e){
  $option[$e->NUMFLOTA][] = '<option 
    data-tokens="'.$e->NUMCAMION.'" 
    data-content="'.str_replace('"','\'',$img_equipo[$e->ID_CAMION]['DIV']).'&nbsp;&nbsp;&nbsp;'.$e->NUMCAMION.'" 
    value="'.$e->ID_CAMION.'">'.$e->NUMCAMION.'</option>';
 }

 $TITULO    = $module_label; //'Gráfico de Velocidad y Recorrido';
 $SUBTITULO = '';
?>
<style>
 <?php include_once("assets/css/detalle-equipo.css") ?>
 /* <?php include_once("assets/css/funky-radio.css") ?> */
 .marcador{
    width: 24px !important;
    height: 24px !important;
    border: thin solid #ccc;
    background: #03A9F4;
    border-radius: 5px;
  }
  .col-xs-12{
    margin: 0;
    padding: 0;
  }
  .modal-dialog{
    width: 60%;
  }
  .tabla-resumen td{
    text-align: center;
  }
  .tabla-resumen td span{
    font-size: 18px; 
    font-weight: 800;
  }
  .tabla-resumen th{
    text-align: center;
    font-weight: 800;
    font-size: 16px;
  }.tabla-resumen td{
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
      font-size: 14px; 
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
  <div class="filtro-contenido">
    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <label>Equipo&nbsp;:&nbsp;</label><br/>
        <select id="equipo" name="equipo" data-style="btn-default" class="selectpicker" data-live-search="true">
          <?php
            foreach($flota as $i => $f){
              echo('<optgroup label="'.$f.'">');
              foreach($option[$i] as $o){ echo($o); }
              echo('</optgroup>');
            }
          ?>
        </select>
      </div>
    </div>

    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Periodo&nbsp;:&nbsp;</label><br/>
        <select id="periodos" name="periodos" data-style="btn-default" class="selectpicker">
          <option value="1hrs">Última Hora</option>
          <option value="5hrs">Últimas 5 Horas</option>
          <option value="12hrs">Últimas 12 Horas</option>
          <option value="24hrs">Últimas 24 Horas</option>
          <!-- <option value="semana">Última Semana</option> -->
          <option value="turno1">Último Turno [00:00 - 08:00]</option>
          <option value="turno2">Último Turno [08:00 - 16:00]</option>
          <option value="fechas" selected>A Medida</option>
        </select>
      </div>
    </div>

    <div class="<?=Core::col(5,5,12,12)?>" id="rango-fechas">
      <div class="frm-group">
        <label>Rango de fechas&nbsp;</label>
        <input type="text" class="dp form-control" name="fecha" id="fecha" placeholder="Fecha Inicio" >
      </div>
    </div>

    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" id="btn-ver">Ver</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <div id="loader" class="loader center-block" style="display:none"></div>
    <div id="contenedor-datos" class="container-fluid"></div>
  </div>
</div>

<script type="text/javascript">
  var intGPS = 0;
  var isPlaying = false;
 $(document).ready(function(){
  var checked = 'fecha';
  var maxContentHeight = window.screen.availHeight - 242;
  $("#container").data("maxContentHeight", maxContentHeight);
  console.log('maxContentHeight:'+maxContentHeight);
  function cb(start, end) {
    $("#fecha").val(start.format("DD/MM/YYYY H:mm") + ' - ' + end.format("DD/MM/YYYY H:mm"));
  }

  $("#fecha").daterangepicker({
    "timePicker": true,
    "timePicker24Hour": true,
    "dateLimit": { "days": 1 },
    "opens": "left",
    "locale": {
      "format": "DD/MM/YYYY H:mm",
      "separator": " - ",
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
      "fromLabel": "Desde",
      "toLabel": "Hasta",
      "customRangeLabel": "Custom",
      "weekLabel": "S",
      "daysOfWeek": [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
      "monthNames": [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
      "firstDay": 1
    },
  }, cb);

  $("#btn-ver").on('click', function(){
   $("#contenedor-datos").html('');
   $("#loader").css('display','none');
   
   var equipo = $("#equipo").selectpicker('val');
   var fecha = $("#fecha").val();
   var diff, f1;
   f1 = $("#fecha").data('daterangepicker');
   var diff = Math.round((f1.endDate._d.getTime() - f1.startDate._d.getTime()) / 86400000);
   var continua = false;

   var sd = 'Desde '+fecha.replace('-','hasta');

   var params = {equipo: equipo, fecha: fecha, maxContentHeight: maxContentHeight};
   
   if(diff > 1 )
   {
    swal({
      title: 'Precaución', 
      text: 'Si desea consultar periodos de tiempo muy extensos, ' +
      'estos pueden contener demasiados datos por lo que podría experimentar ' +
      'problemas en su navegador.\n\n ¿Desea continuar?', 
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Si, Continuar",
      cancelButtonText: "No, Cancelar",
      closeOnConfirm: true
    },
    function(isConfirm){
      if(isConfirm){
        $("#loader").css('display','block');        
            
        $.post('data-grafico-gps.php',params, function(data){
          $("#contenedor-datos").html(data);
          $(".subtitulo-pagina").html(sd);
          $("#loader").css('display','none');
          
          isPlaying = false;
          if(typeof intGPS !== 'undefined') clearInterval(intGPS);
          if(typeof stopGPSTrip === 'function') stopGPSTrip();
        });
      }
    });
   }
   else
   {
    continua = true;
   }

   if(continua){
    $("#loader").css('display','block');
        
    $.post('data-grafico-gps.php',params, function(data){
      $("#contenedor-datos").html(data);
      $(".subtitulo-pagina").html(sd);
      $("#loader").css('display','none');

      isPlaying = false;
      if(typeof intGPS !== 'undefined') clearInterval(intGPS);
      if(typeof stopGPSTrip === 'function') stopGPSTrip();
    });
   }
  });

  $("#periodos").on('change', function(evt){
    checked = $(evt.target).val();
    
    var d = new Date();
    var ahora = new Date(d.getTime()).toLocaleString();
    
    if(checked == 'fechas'){
      
    }
    if(checked == '1hrs'){
      var p1 = new Date(d.getTime()-3600000).toLocaleString();
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(ahora);
    }
    if(checked == '5hrs'){
      var p1 = new Date(d.getTime()-(3600000*5)).toLocaleString();
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(ahora);
    }
    if(checked == '12hrs'){
      var p1 = new Date(d.getTime()-(3600000*12)).toLocaleString();
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(ahora);
    }
    if(checked == '24hrs'){
      var p1 = new Date(d.getTime()-(3600000*24)).toLocaleString();
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(ahora);
    }
    // if(checked == 'semana'){
    //   var p1 = new Date(d.getTime()-(3600000*24*7)).toLocaleString();
    //   $("#fecha").data('daterangepicker').setStartDate(p1);
    //   $("#fecha").data('daterangepicker').setEndDate(ahora);
    // }
    if(checked == 'turno1'){
      var p1 = new Date(d.getFullYear(),d.getMonth(),d.getDate(),0,0,0);
      var p2 = new Date(d.getFullYear(),d.getMonth(),d.getDate(),7,59,0);
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(p2);
    }
    if(checked == 'turno2'){
      var p1 = new Date(d.getFullYear(),d.getMonth(),d.getDate(),8,0,0);
      var p2 = new Date(d.getFullYear(),d.getMonth(),d.getDate(),15,59,0);
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(p2);
    }
  });
 });
</script>
<!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw&callback=initMap"></script>  -->
<!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw&libraries=geometry"></script> -->