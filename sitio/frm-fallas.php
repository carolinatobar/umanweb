<?php
  require 'autoload.php';

  $acc = new Acceso();

  $TITULO    = $module_label; //'Fallas';
  $SUBTITULO = 'Seleccione las opciones de los filtro para cargar los datos';
?>

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
    <div class="<?=Core::col(2,2)?>"></div>
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Periodo&nbsp;:&nbsp;</label>
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

    <div class="<?=Core::col(5,5,12,12)?>">
      <div class="frm-group">
        <label>Rango de fechas&nbsp;</label>
        <input type="text" class="dp form-control" name="fecha" id="fecha" placeholder="Fecha Inicio" >
      </div>
    </div>

    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" id="btn-ver"> Ver</button>
      </div>
    </div>

  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    
    <table id="tabla-fallas" class="table table-hover">
      <thead>
        <tr>
          <th>N&deg;</th>
          <th>Equipo</th>
          <th>Pos</th>
          <th>Tipo</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th>Duración <br/>(segundos)</th>
          <th>Umbral</th>
          <th>Dato Extremo</th>
          <th>&nbsp;</th>
      </thead>
      <tbody>
      </tbody>
    </table>

  </div>
</div>

<?php Core::createModal('modalDetalle', array(
  'includeContent'=>true,
  'content'=>'<div id="tabla"></div>',
  'title'=>'',
  'style'=>'width: 90%;',
  'includeLoader'=>true,
)) ?>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  var checked = 'fecha';
  var dParams = {equipo: 0, posicion: 0, fecha: ''};

  $(function(){
    function cb(start, end) {
      $("#fecha").val(start.format("DD/MM/YYYY H:mm") + ' - ' + end.format("DD/MM/YYYY H:mm"));
    }

    $("#fecha").daterangepicker({
      "timePicker": true,
      "timePicker24Hour": true,
      "dateLimit": { "days": 5 },
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


    tabla = $("#tabla-fallas").DataTable({
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
            filename: "Fallas",
            title: "Fallas"
          }, 
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "Fallas"
          }
        ]
      },
      columnDefs: [
        { "orderable": false, "targets": 0 },
      ],
      ajax: {
        "url": "ajax/fallas/obtener-tabla.php",
        "type": "POST",
      },
      fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
        if(aData.tipo.toLowerCase() == 'presión baja') $(nRow).attr("style","background-color: yellow");
        if(aData.tipo.toLowerCase() == 'presión alta') $(nRow).attr("style","background-color: orange");
        if(aData.tipo.toLowerCase() == 'temperatura') $(nRow).attr("style","background-color: red; color: white");
      },
      autoWidth: false,
      columns: [
        { "data": "num" },
        { "data": "equipo" },
        { "data": "posicion" },
        { "data": "tipo" },
        { "data": "inicio" },
        { "data": "fin" },
        { "data": "duracion" },
        { "data": "umbral" },
        { "data": "extremo" },
        { "data": "acciones", "width": '80px' }
      ],
      paging: true,
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    })
    .on('xhr.dt', function(e, settings, json, xhr){
      $("subtitulo-pagina").html('');
      if(json == null){
        swal({
          title: 'Error',
          text: 'No fue posible obtener la información debido al siguiente error: <br/>'+xhr.responseText,
          html: true,
          type: 'error'
        });
        return true;
      }
      if(json.titulo){
        $(".subtitulo-pagina").html(json.titulo);
      }
    });
  
    $("#btn-ver").click(function(){
      tabla.destroy();
      tabla = $("#tabla-fallas").DataTable({
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
              filename: "Fallas",
              title: "Fallas"
            }, 
            {
              extend: 'print',
              text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
              key:{
                key: 'p',
                altKey: true
              },
              className: 'btn btn-info',
              title: "Fallas"
            }
          ]
        },
        columnDefs: [
          { "orderable": false, "targets": 0 },
        ],
        ajax: {
          url: "ajax/fallas/obtener-tabla.php",
          type: "POST",
          data: { fecha: $("#fecha").val() }
        },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
          if(aData.tipo.toLowerCase() == 'presión baja') $(nRow).attr("style","background-color: yellow");
          if(aData.tipo.toLowerCase() == 'presión alta') $(nRow).attr("style","background-color: orange");
          if(aData.tipo.toLowerCase() == 'temperatura') $(nRow).attr("style","background-color: red; color: white");
        },
        autoWidth: false,
        columns: [
          { "data": "num" },
          { "data": "equipo" },
          { "data": "posicion" },
          { "data": "tipo" },
          { "data": "inicio" },
          { "data": "fin" },
          { "data": "duracion" },
          { "data": "umbral" },
          { "data": "extremo" },
          { "data": "acciones", "width": '80px' }
        ],
        paging: true,
        info: false,
        language: {
          url: "assets/datatables-1.10.15/lang/Spanish.json",
          loadingRecords: '<div class="loader show"></div>'
        }
      });
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

    $("#modalDetalle").on("show.bs.modal", function(evt){      
      $("#tabla").hide();
      $("#loader").show();
      if(dParams.equipo > 0 && dParams.posicion > 0 && dParams.fecha != ''){
        if(dParams.modo == 'eventos'){
          $("#modalDetalle h4.modal-title").html('Eventos Equipo '+dParams.equipo);
          $.post('ajax/fallas/obtener-eventos.php', dParams, function(data){
            $("#tabla").html(data);
            $("#tabla").show();
            $("#loader").hide();
          });
        }
        else if(dParams.modo == 'alarmas'){
          $("#modalDetalle h4.modal-title").html('Alarmas Equipo '+dParams.equipo);
          $.post('ajax/fallas/obtener-alarmas.php', dParams, function(data){
            $("#tabla").html(data);
            $("#tabla").show();
            $("#loader").hide();
          });
        }
        else if(dParams.modo == 'grafico'){
          $("#modalDetalle h4.modal-title").html('Gráfico Equipo '+dParams.equipo);
          $.post('data-grafico-presion.php', dParams, function(data){
            $("#tabla").html(data);
            $("#tabla").show();
            $("#loader").hide();
          });
        }
      }
    });
  });

  function detalle(eq, po, fe, mo, ta = undefined){
    dParams.equipo = eq;
    dParams.posicion = po;
    dParams.fecha = fe;
    dParams.modo = mo;
    dParams.tipo = ta;
    $("#modalDetalle").modal('show');
  }
</script>