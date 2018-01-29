<?php
 require 'autoload.php';

 // @session_start();
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

 $TITULO    = $module_label; //'Reporte de Alarmas';
 $SUBTITULO = '';
?>
<style>
 <?php include_once("assets/css/detalle-equipo.css") ?>
 <?php include_once("assets/css/funky-radio.css") ?>
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

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">
  <!-- TÍTULO DE PÁGINA -->
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <!-- MENÚ DE PÁGINA -->
  <div class="filtro-contenido">
    <div class="<?=Core::col(3,3,12,12)?>">
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
          <option value="semana">Última Semana</option>
          <option value="turno1">Último Turno [00:00 - 08:00]</option>
          <option value="turno2">Último Turno [08:00 - 16:00]</option>
          <option value="fechas" selected>A Medida</option>
        </select>
      </div>
    </div>

    <div class="<?=Core::col(4,4,12,12)?>" id="rango-fechas">
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
    <div id="contenedor-datos" class="container-fluid">
      <table id="tabla-alarmas" class="table table-hover compact">
        <thead>
          <tr>
            <th>Pos</th>
            <th>Sensor</th>
            <th>Tipo Alarma</th>
            <th>Valor</th>
            <th>Fecha Alarma</th>
            <th>Hora Alarma</th>
            <th>Fecha Reconocimiento Operador</th>
            <th>Hora Reconocimiento Operador</th>
            <th>Fecha Reconocimiento UmanWeb</th>
            <th>Hora Reconocimiento UmanWeb</th>
            <th>Comentario</th>
            <th>Usuario</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="0" oninput="filtro(this)" placeholder="Posición"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="1" oninput="filtro(this)" placeholder="Sensor"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="2" oninput="filtro(this)" placeholder="Tipo de Alarma"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="3" oninput="filtro(this)" placeholder="Valor"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="4" oninput="filtro(this)" placeholder="Fecha"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="5" oninput="filtro(this)" placeholder="Hora"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="6" oninput="filtro(this)" placeholder="Fecha"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="7" oninput="filtro(this)" placeholder="Hora"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="8" oninput="filtro(this)" placeholder="Fecha"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="9" oninput="filtro(this)" placeholder="Hora"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="10" oninput="filtro(this)" placeholder="Comentario"></td>
            <td><input type="text" class="foot-filter" style="width:100%" data-index="11" oninput="filtro(this)" placeholder="Usuario"></td>
          </tr>
        </tfoot>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>


<script type="text/javascript">
  var dt;
 $(document).ready(function(){
  var checked = 'fecha';
  var maxContentHeight = window.screen.availHeight - 242;
  console.log('maxContentHeight:'+maxContentHeight);

  function cb(start, end) {
    $("#fecha").val(start.format("DD/MM/YYYY H:mm") + ' - ' + end.format("DD/MM/YYYY H:mm"));
  }

  $("#fecha").daterangepicker({
    "timePicker": true,
    "timePicker24Hour": true,
    "dateLimit": { "days": 90 },
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
    //  $("#contenedor-datos").html('');
   $("#loader").css('display','none');
   
   var equipo = $("#equipo").selectpicker('val');
   var fecha = $("#fecha").val();
   var diff, f1;
   f1 = $("#fecha").data('daterangepicker');
   var diff = Math.round((f1.endDate._d.getTime() - f1.startDate._d.getTime()) / 86400000);
   var continua = false;
   var params = {equipo: equipo, fecha: fecha, maxContentHeight: maxContentHeight};
   var sd = 'Desde '+fecha.replace('-','hasta');

   if($.fn.DataTable.isDataTable($("#tabla-alarmas"))) dt.destroy();
   
   if(diff >= 30){
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
        cargarGrilla(params, sd);
      }
    });
   }
   else{
    cargarGrilla(params, sd);
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

 function filtro(o){
    dt.columns($(o).data('index')).search( o.value ).draw();
  }

 function cargarGrilla(params, sd){
  $(".subtitulo-pagina").html(sd);
  dt = $("#tabla-alarmas").DataTable({
    dom: 'Brtip',
    destroy: true,
    retrieve: true,
    searching: true,
    order: [4, 'desc'],
    responsive: false,
    buttons: {
      buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "<?=$TITULO?> - <?=date("d-m-Y")?>",
            customize: function(xlsx) {
              var sheet = xlsx.xl.worksheets['sheet1.xml'];
              var downrows = 3;
              var clRow = $('row', sheet);
              var msg;
              //update Row
              clRow.each(function() {
                var attr = $(this).attr('r');
                var ind = parseInt(attr);
                ind = ind + downrows;
                $(this).attr("r", ind);
              });

              // Update  row > c
              var total_rows = 0;
              $('row c ', sheet).each(function() {
                var attr = $(this).attr('r');
                var pre = attr.substring(0, 1);
                var ind = parseInt(attr.substring(1, attr.length));
                ind = ind + downrows;
                $(this).attr("r", pre + ind);
                total_rows = ind;
              });

              function Addrow(index, data) {

                msg = '<row r="' + index + '">';
                for (var i = 0; i < data.length; i++) {
                  var key = data[i].k;
                  var value = data[i].v;
                  var formula = data[i].f;
                  if(formula==undefined){
                    msg += '<c t="inlineStr" r="' + key + index + '">';
                    msg += '<is>';
                    msg += '<t>' + value + '</t>';
                    msg += '</is>';
                    msg += '</c>';
                  }
                  else{
                    msg += '<c r="' + key + index + '" s="3" t="str">';
                    msg += '<f>'+formula+'</f>';
                    msg += '<v></v>';
                    msg += '</c>';
                  }
                }
                msg += '</row>';
                return msg;
              }

              //título
              var ti = Addrow(1, [{ k: 'A', v: '<?=$TITULO?> - <?=date("d-m-Y")?>' }]);
              var r1 = Addrow(2, [{ k: 'A', v: ' Fecha    : ' }, { k: 'B', v: '<?=date("d-m-Y")?>' }]);
              <?php
                //                 1   2   3   4   5   6   7   8   9  10  11  12  13  14  15  16  17  18  19  20  21  22  23  24  25  26  27   28   29   30   31
                $cols = array(' ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE');
                $pos = 1;
                $c = '';
                $merge = '';
                $cells = '';
                $heads = '';
                for($i=4; $i<($max_pos*3)+4; $i+=3){
                  $c .= "{ k: '{$cols[$i]}', v: '{$nomenclatura[$pos]}' },";
                  $merge .= "<mergeCell ref=\"{$cols[$i]}4:{$cols[$i+2]}4\" />";
                  $cells .= "'c[r={$cols[$i]}4]',";
                  $pos++;
                }
                for($i=4; $i<($max_pos*3)+4; $i++){
                  $heads .= "'c[r={$cols[$i]}5]',";
                }
                $merge .= "<mergeCell ref=\"A1:{$cols[$i]}1\" />";
                $cells .= "'c[r=A1]'";
              ?>
              var r2 = Addrow(4, [<?=$c?>]);

              var cells = [<?=$cells?>];
              var heads = [<?=$heads?>];

              var merged = '<mergeCell ref="A'+(total_rows+1)+':B'+(total_rows+1)+'" />';
              var up = ''; va = ''; r3 = ''; r4 = ''; r5 = ''; r6 = '';
              // var up = Addrow(total_rows+1,[{ k: 'A', v: 'Universo Proyecto' }, { k: 'C', f: 'SUM(C6:C'+total_rows+')' }]);
              // total_rows += 2;
              // var va = Addrow(total_rows++, [{ k: 'A', v: '' }]);
              
              // var r3 = Addrow(total_rows, [{ k: 'A', v: 'TABLA TIPO SENSORES' }]);
              // merged += '<mergeCell ref="A'+(total_rows)+':D'+(total_rows)+'" />';

              // var r4 = Addrow(total_rows+1, [{ k: 'A', v: 'Interno' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor interno de presión y temperatura' }]);
              // merged += '<mergeCell ref="C'+(total_rows+1)+':D'+(total_rows+1)+'" />';
              
              // var r5 = Addrow(total_rows+2, [{ k: 'A', v: 'Externo' }, { k: 'B', v: 'TMS 24' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);
              // merged += '<mergeCell ref="C'+(total_rows+2)+':D'+(total_rows+2)+'" />';
              
              // var r6 = AddRow(total_rows, [{ k: 'A', v: 'Canister' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);


              sheet.childNodes[0].childNodes[0].childNodes[0].outerHTML = '<col min="1" max="1" width="9" customWidth="1"/>';
              sheet.childNodes[0].childNodes[1].innerHTML = ti + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML + up + va + r3 + r4 + r5;
              sheet.childNodes[0].childNodes[2].innerHTML = '<?=$merge?>'+merged;
              var rows = sheet.childNodes[0].childNodes[1].getElementsByTagName("row");

              
              $("c[r=A4] t", sheet).text('');

              $.each(cells, function(i,o){ $(o, sheet).attr('s', '51'); });
              // $.each(heads, function(i,o){ $(o, sheet).attr('s', '251'); });

              console.log(rows);
            },
          }, 
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "<?=$TITULO?> - <?=date("d-m-Y")?>",
          }
        ]
    },
    ajax:{
      url: "ajax/reporte-alarmas.php?"+(Math.random()*1000),
      type: "POST",
      data: params
    },
    autoWidth: false,
    columns: [
      { "data": "posicion" },
      { "data": "sensor" },
      { "data": "tipo_alarma" },
      { "data": "valor" },
      { "data": "fecha_alarma" },
      { "data": "hora_alarma" },
      { "data": "fecha_reconop" },
      { "data": "hora_reconop" },
      { "data": "fecha_reconuw" },
      { "data": "hora_reconuw" },
      { "data": "comentario", className: "dt-body-nowrap" },
      { "data": "usuario" },
    ],
    paging: true,
    info: false,
    pagingType: "full_numbers",
    language: {
      url: "assets/datatables-1.10.15/lang/Spanish.json",
      loadingRecords: '<div class="loader show"></div>'
    },
  })
  .on("xhr.dt", function(e, settings, json, xhr){
    if(json.type=='info' || json.type=='error'){
      swal(json);
    }
  })
  .on("draw.dt", function(){
    $('[data-toggle="popover"]').popover();
  });
 }
</script>