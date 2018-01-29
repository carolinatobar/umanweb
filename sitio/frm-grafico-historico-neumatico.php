<?php
 require 'autoload.php';

 @session_start();
 $acceso = new Acceso($_SESSION, session_id());
 if(!$acceso->Permitido()) exit();

 //  print_r($img_equipo);exit();

 $n = new Neumatico();
 $n = $n->listar();




 $TITULO    = $module_label; //'Gráfico de Presión y Temperatura';
 $SUBTITULO = '';
?>
<style>
  /* <?php include_once("assets/css/funky-radio.css") ?> */
  <?php include_once("assets/css/detalle-equipo.css") ?>
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

<!-- <script src="assets/js/moment.js"></script> -->

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">
  <!-- TÍTULO DE PÁGINA -->
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <!-- MENÚ DE PÁGINA -->
  <div class="filtro-contenido">
    <div class="<?=Core::col(4,4,12,12)?>">
      <div class="frm-group">
        <label>Neumáticos&nbsp;:&nbsp;</label><br/>
        <select id="neumatico" name="neumatico" data-style="btn-default" class="selectpicker" data-live-search="true">
          <?php
            foreach($n as $i){
              echo '<option data-content="<div class=\'neumatico_select icono-x24 pull-left\' style=\'\'></div>&nbsp;&nbsp;&nbsp;'.$i->NUMIDENTI.'" value="'.$i->ID_NEUMATICO.'">'.$i->NUMIDENTI."</option>";
            }
          ?>
        </select>
      </div>
    </div>

    <div class="<?=Core::col(5,5,12,12)?>" id="rango-fechas" style="display:none">
      <div class="frm-group">
        <label>Rango de fechas&nbsp;</label>
        <input type="text" class="dp form-control" name="fecha" id="fecha" placeholder="Fecha Inicio" >
      </div>
    </div>

    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" id="btn-ver">&nbsp;Ver&nbsp;&nbsp;</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <div id="loader" class="loader center-block" style="display:none"></div>
    <div id="contenedor-datos"></div>
  </div>
</div>

<script type="text/javascript">
  var sd;
 $(document).ready(function(){
  var checked = 'fecha';
  function cb(start, end) {
    $("#fecha").val(start.format("DD/MM/YYYY H:mm") + ' - ' + end.format("DD/MM/YYYY H:mm"));
    sd = 'Desde '+start.format("DD/MM/YYYY H:mm")+' hasta '+end.format("DD/MM/YYYY H:mm");
  }

  $("#fecha").daterangepicker({
    "timePicker": true,
    "timePicker24Hour": true,
    // "dateLimit": { "days": 7 },
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
  $("#neumatico").on('change', function(){
  	$("#rango-fechas").css('display','none');
  	});
  $("#btn-ver").on('click', function(){
    $("#contenedor-datos").html('');
    $("#loader").css('display','none');

    if($("#rango-fechas").css('display')=='none'){
    	$("#rango-fechas").css('display','block');
    	var neumatico = $("#neumatico").selectpicker('val');
    	var params = {neumatico: neumatico};
    	$.post('data-tabla-neumatico.php',params, function(data, status){
	      console.log(status);
	      $("#contenedor-datos").html(data);
	      $("#loader").css('display','none');
	    })
	    .fail(function(data) {
	      // console.log(data);
	      $("#contenedor-datos").html(
	        '<div class="alert alert-danger" role="alert">'+data.statusText+'</div>');
	      $("#loader").css('display','none');
	    });
    }else{
    	var fecha = $("#fecha").val();
	   
	    $("#loader").css('display','block');
	    var neumatico = $("#neumatico").selectpicker('val');
    	var params = {neumatico: neumatico, fecha: fecha};

	    $(".subtitulo-pagina").html('');
	    if(sd==undefined){
	      sd = 'Desde '+fecha.replace('-','hasta');
	    }
	        
	    $.post('data-grafico-historico-neumatico.php',params, function(data, status){
	      console.log(status);
	      $("#contenedor-datos").html(data);
	      $(".subtitulo-pagina").html((sd!=undefined?sd:''));
	      $("#loader").css('display','none');
	    })
	    .fail(function(data) {
	      // console.log(data);
	      $("#contenedor-datos").html(
	        '<div class="alert alert-danger" role="alert">'+data.statusText+'</div>');
	      $("#loader").css('display','none');
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
    if(checked == 'semana'){
      var p1 = new Date(d.getTime()-(3600000*24*7)).toLocaleString();
      $("#fecha").data('daterangepicker').setStartDate(p1);
      $("#fecha").data('daterangepicker').setEndDate(ahora);
    }
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