<?php
  require 'autoload.php';

  $acc = new Acceso();
  $gen = new General();
  $eqp = new Equipo();
  $his = new Historial();

  $equipos = $eqp->listar();
  $acciones = $his->listar_acciones();

  $nom = $gen->getNomenclaturas();

  $TITULO    = $module_label; //'Historial';
  $SUBTITULO = '';
?>
<link rel="stylesheet" href="assets/plugins/jQueryUI/jquery-ui.css">
<script src="assets/plugins/jQueryUI/jquery-ui.js"></script>
<script src="assets/js/i18n/datepicker-<?=strtolower($idioma)?>.js"></script>
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>
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
    <div class="<?=Core::col(10,10)?>"></div>
    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <!-- <button type="button" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Botón</button> -->
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table id="tabla-historial" class="table table-hover stripe hover">
      <thead>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Acción</th>
        <th>Equipo</th>
        <th width="20px">Posición</th>
        <th>Neumático</th>
        <th>Sensor</th>
        <th>Usuario</th>
      </thead>
      <tfoot>
        <tr>
          <td id="filtro_fecha"></td>
          <td></td>
          <td id="filtro_acciones">
            <select onchange="filtro_multiple(this)" data-index="2" class="selectpicker dropup" multiple>
              <option value="All">Todos</option>
              <?php
                foreach($acciones as $key => $value){
                  echo('<option value="'.$value->ACCION.'">'.$value->ACCION.'</option>'); 
                }
              ?>
            </select>
          </td>
          <td>
            <select onchange="filtro_multiple(this)" data-index="3" class="selectpicker dropup" multiple>
              <option value="All">Todos</option>
              <?php
                foreach($equipos as $key => $value){
                  echo('<option value="'.$value->NUMCAMION.'">'.$value->NUMCAMION.'</option>'); 
                }
              ?>
            </select>
          </td>
          <td>
            <select onchange="filtro_multiple(this)" data-index="4" class="selectpicker dropup" multiple>
              <option value="All">Todas</option>
              <?php
                foreach($nom as $key => $value){
                  if($value!=0) echo('<option value="'.$value.'">'.$value.'</option>'); 
                }
              ?>
            </select>
          </td>
          <td>
            <input type="text" oninput="filtro(this);" class="form-control foot-filter" data-index="5" placeholder="Neumático" />
          </td>
          <td>
            <input type="text" oninput="filtro(this);" class="form-control foot-filter" data-index="6" placeholder="Sensor" />
          </td>
          <td>
            <input type="text" oninput="filtro(this);" class="form-control foot-filter" data-index="7" placeholder="Usuario" />
          </td>
        </tr>
      </tfoot>
      <tbody>        
      </tbody>
    </table>
  </div>
</div>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  $(function(){
   
    tabla = $("#tabla-historial").DataTable({
      dom: 'Brtip',
      searching: true,
      ajax: {
        url: 'ajax/historial.php',
        type: 'POST',
      },
      className: 'dt-body-compact',
      columns: [
        { data: 'fecha', 'render': {'_': 'fecha1', 'filter': 'fecha2', 'display': 'fecha2'}},
        { data: 'hora' },
        { data: 'accion' },
        { data: 'num_cam' },
        { data: 'posicion', width: '20px' },
        { data: 'cod_neum' },
        { data: 'cod_sen' },
        { data: 'usuario' }
      ],
      columnDefs:[
        { width: "20px", target: 3 }
      ],
      order: [
        [ 0, 'desc' ],
      ],
      responsive: false,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de Historial - <?=date("d-m-Y")?>",
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
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de Historial - <?=date("d-m-Y")?>' }]);
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
            title: "Tabla de Historial - <?=date("d-m-Y")?>",
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

    yadcf.init(tabla,[
      {column_number : 0, filter_type: "range_date", filter_container_id: "filtro_fecha", date_format: "dd/mm/yyyy", filter_default_label: ["Desde","Hasta"]}
    ]);

    $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
      let _newValue = newValue? $(this)[0].children[clickedIndex].value : null;
      let _selected = $(this).val();

      if(_newValue == "All") $(this).selectpicker('val', 'All');
      else{
        if(_selected){
          if(_selected.join("|").includes("All")){
            let values = [];
            $.each(_selected, function(i,o){
              if(o != "All") values.push(o);
            });
            $(this).selectpicker('val', values);
          }
        }
      }
    });
  });

  function filtro(o){
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }
  function filtro_multiple(o){
    let values = $(o).selectpicker('val');
    let regex  = '';
    if(values) regex = values.join("|");
    if(regex.includes("All")) regex = '';

    tabla.columns($(o).data('index')).search( regex, true, false ).draw();
  }
</script>