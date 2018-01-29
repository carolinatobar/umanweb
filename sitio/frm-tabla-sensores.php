<?php
  require 'autoload.php';

  $acc = new Acceso();
  $gen = new General();

  $nomenclatura = $gen->getNomenclaturas();
  $img_equipo   = $gen->getImagenesEquipo('','center-block');
  $db           = DB::getInstance();

  $sql = "SELECT s.*, nc.ID_POSICION, c.NUMCAMION, c.ID_CAMION 
    FROM uman_neumatico_camion nc 
      INNER JOIN uman_neumaticos n ON nc.ID_NEUMATICO=n.ID_NEUMATICO
      INNER JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR 
      INNER JOIN uman_camion c ON c.ID_CAMION=nc.ID_EQUIPO 
    ORDER BY c.NUMCAMION ASC;";

  $camiones = $db->query($sql);
  $equipo   = array();

  foreach($camiones->results() as $c){

    $equipo[] = array(
      'ID'=>$c->ID_CAMION,
      'EQUIPO'=>$c->NUMCAMION,
      'POSICION'=>$c->ID_POSICION,
      'CODSENSOR'=>$c->CODSENSOR,
      'TIPO'=>$c->TIPO
    );
  }

  $TITULO    = $module_label; //'Tabla de Sensores en Equipos';
  $SUBTITULO = '';
?>

<style>
  <?php include_once('assets/css/detalle-equipo.css') ?>
</style>

<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<style>
  .th-pos{
    text-align: center;
  }
  .table>thead>tr>th{
    border-bottom-style: none;
    border-top-style: none;
    text-align: center;
  }

 
</style>

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
  <table class="table table-responsive" id="tabla-sensores">
    <thead>
      <tr>
        <th>#</th>
        <th>Equipo</th>
        <th>Posición</th>
        <th>Tipo</th>
        <th>Código Sensor</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td></td>
        <td><input type="text" data-index="1" class="form-control" style="width:100%" oninput="filtro(this);"></td>
        <td><input type="text" data-index="2" class="form-control" style="width:100%" oninput="filtro(this);"></td>
        <td><input type="text" data-index="3" class="form-control" style="width:100%" oninput="filtro(this);"></td>
        <td><input type="text" data-index="4" class="form-control" style="width:100%" oninput="filtro(this);"></td>
      </tr>
    </tfoot>
    <tbody>
      <?php
        $i=1;
        foreach($equipo as $e){
          echo '<tr>';
          echo '<td>'.$i++.'</td>';
          echo '<td><center>'.$img_equipo[$e['ID']]['DIV36'].'<br/><b>'.$e['EQUIPO'].'</b></center></td>'; 
          echo '<td>'.$nomenclatura[$e['POSICION']].'</td>';           
          echo '<td>'.Core::imagen_sensor($e['TIPO'],36,'margin: 0 auto -10px auto !important;').'<br/>'.$e['TIPO'].'</td>';
          echo '<td>'.$e['CODSENSOR'].'</td>';
          echo '</tr>';
        }
      ?>
    </tbody>
  </table>
</div>
</div>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  $(function(){

    tabla = $("#tabla-sensores").DataTable({
      dom: 'Brtip',
      searching: true,
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de Sensores - <?=date("d-m-Y")?>",
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
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de Sensores - <?=date("d-m-Y")?>' }]);
              var r1 = Addrow(2, [{ k: 'A', v: ' Fecha    : ' }, { k: 'B', v: '<?=date("d-m-Y")?>' }]);
              <?php
                //                 1   2   3   4   5   6   7   8   9  10  11  12  13  14  15  16  17  18  19  20  21  22  23  24  25  26  27   28   29   30   31
                $cols = array(' ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE');
                $pos = 1;
                $c = '';
                $merge = '';
                $cells = '';
                $heads = '';
                for($i=6; $i<($max_pos*2)+6; $i+=2){
                  $c .= "{ k: '{$cols[$i]}', v: '{$nomenclatura[$pos]}' },";
                  $merge .= "<mergeCell ref=\"{$cols[$i]}4:{$cols[$i+1]}4\" />";
                  $cells .= "'c[r={$cols[$i]}4]',";
                  $pos++;
                }
                for($i=6; $i<($max_pos*2)+6; $i++){
                  $heads .= "'c[r={$cols[$i]}5]',";
                }
                $merge .= "<mergeCell ref=\"A1:{$cols[$i]}1\" />";
                $cells .= "'c[r=A1]'";
              ?>
              var r2 = Addrow(4, [<?=$c?>]);

              var cells = [<?=$cells?>];
              var heads = [<?=$heads?>];

              var merged = '<mergeCell ref="A'+(total_rows+1)+':B'+(total_rows+1)+'" />';
              var up = Addrow(total_rows+1,[{ k: 'A', v: 'Universo Proyecto' }, { k: 'C', f: 'SUM(C6:C'+total_rows+')' }]);
              total_rows += 2;
              var va = Addrow(total_rows++, [{ k: 'A', v: '' }]);
              
              var r3 = Addrow(total_rows, [{ k: 'A', v: 'TABLA TIPO SENSORES' }]);
              merged += '<mergeCell ref="A'+(total_rows)+':D'+(total_rows)+'" />';

              var r4 = Addrow(total_rows+1, [{ k: 'A', v: 'Interno' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor interno de presión y temperatura' }]);
              merged += '<mergeCell ref="C'+(total_rows+1)+':D'+(total_rows+1)+'" />';
              
              var r5 = Addrow(total_rows+2, [{ k: 'A', v: 'Externo' }, { k: 'B', v: 'TMS 24' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);
              merged += '<mergeCell ref="C'+(total_rows+2)+':D'+(total_rows+2)+'" />';
              
              // var r6 = AddRow(total_rows, [{ k: 'A', v: 'Canister' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);


              sheet.childNodes[0].childNodes[0].childNodes[0].outerHTML = '<col min="1" max="1" width="10" customWidth="1"/>';
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
            title: "Tabla de Sensores - <?=date("d-m-Y")?>",
          }
        ]
      },
      columnDefs: [
        { "orderable": false, "targets": 0 },
        { "orderable": false, "targets": 1 },
        { "orderable": false, "targets": 2 },
        <?php
          $t = 3;
          for($i=1; $i<=($max_pos*2)+2; $i++){
            echo '{ "orderable": false, "targets": '.$t++.' },';
          }
        ?>
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
  });
  function filtro(o){
    tabla.column($(o).data("index")).search($(o).val()).draw();
  }
</script>