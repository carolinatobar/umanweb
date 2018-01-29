<?php
  require 'autoload.php';

  $acc = new Acceso();
  
  $gen = new General();
  $timeout     = $gen->getParamvalue('timeout');
  $img_equipo  = $gen->getImagenesEquipo();

  $db = DB::getInstance();
  $sql = "SELECT c.ID_CAMION, c.NUMCAMION, f.NOMBRE, te.NEUMATICOS
    FROM uman_camion c 
    INNER JOIN uman_flotas f ON c.NUMFLOTA=f.NUMFLOTAS
    INNER JOIN uman_tipo_equipo te ON te.ID=c.tipo";

  $equipos = $db->query($sql);

  $content = array();
  $i = 1;

  if($equipos->count() > 0){
    $equipos = $equipos->results();
    foreach($equipos as $e){
      $c = array();
      //CANTIDAD DE NEUMÁTICOS
      $neumaticos = array_sum(explode(',',$e->NEUMATICOS));
      
      //OBTENER LA CANTIDAD REAL DE SENSORES INSTALADOS
      $sql = "SELECT ID_POSICION AS posicion
        FROM uman_neumatico_camion nc INNER JOIN uman_neumaticos n ON nc.ID_NEUMATICO=n.ID_NEUMATICO
        WHERE n.ID_SENSOR > 0 AND n.ID_SENSOR != ''  AND n.ID_SENSOR IS NOT NULL AND nc.ID_EQUIPO={$e->ID_CAMION}
        ORDER BY ID_POSICION ASC";
      $sensores         = $db->query($sql);
      $c['sensores']    = $sensores->count();
      $sensores         = $sensores->results();

      $sql = "SELECT DISTINCTROW  posicion
        FROM uman_ultimoevento
        WHERE fecha_evento > DATE_SUB(NOW(), INTERVAL {$timeout} MINUTE) AND numequipo={$e->ID_CAMION}";
      $enLinea          = $db->query($sql);
      $c['emitiendo']   = $enLinea->count();
      $enLinea          = $enLinea->results();
            
      $c['correlativo'] = $i;
      $c['flota']       = $e->NOMBRE;
      $c['equipo']      = $img_equipo[$e->ID_CAMION]['DIV36'];
      $c['num_interno'] = $e->NUMCAMION;
      $c['neumaticos']  = $neumaticos;
      $c['sensor']      = array_fill(1,$neumaticos,0);
      $c['porcentaje']  = round( ($c['emitiendo']*100)/$c['sensores'], 1);

      foreach($sensores as $s){
        foreach($enLinea as $l){
          if($s->posicion == $l->posicion){
            $c['sensor'][$s->posicion] = 1;
            break;
          }
        }
      }
      $content[] = $c;
      $i++;
    }
  }

  $TITULO    = $module_label; //'Tabla de Timeout';
  $SUBTITULO = 'Timeout actual '.$timeout.' minutos';
?>
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  .fa-check{
    color: green;
  }
  .fa-times{
    color: red;
  }
  table {
    font-size: 75%;
  }
  td, th {
    text-align: center;
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
  <div class="filtro-contenido"></div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table class="table table-responsive" id="tabla-cobertura">
      <thead>
        <tr>
          <th>#</th>
          <th>Flota</th>
          <th>Equipo</th>
          <th>Número Interno</th>
          <th>S1</th>
          <th>S2</th>
          <th>S3</th>
          <th>S4</th>
          <th>S5</th>
          <th>S6</th>
          <th>Instalados</th>
          <th>% Cobertura</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if(count($content))
          {
            $i=1;
            foreach($content as $d){
              echo '<tr>';
              echo "<td>{$d['correlativo']}</td>";
              echo "<td>{$d['flota']}</td>";
              echo "<td>{$d['equipo']}</td>";
              echo "<td>{$d['num_interno']}</td>";
              echo '<td><span style="display:none">'.$d['sensor'][1].'</span><i class="fa fa-'.($d['sensor'][1]==1?'check':'times').'" aria-hidden="true"></i></td>';
              echo '<td><span style="display:none">'.$d['sensor'][2].'</span><i class="fa fa-'.($d['sensor'][2]==1?'check':'times').'" aria-hidden="true"></i></td>';
              echo '<td><span style="display:none">'.$d['sensor'][3].'</span><i class="fa fa-'.($d['sensor'][3]==1?'check':'times').'" aria-hidden="true"></i></td>';
              echo '<td><span style="display:none">'.$d['sensor'][4].'</span><i class="fa fa-'.($d['sensor'][4]==1?'check':'times').'" aria-hidden="true"></i></td>';
              if(isset($d['sensor'][5])){
                echo '<td><span style="display:none">'.$d['sensor'][5].'</span><i class="fa fa-'.($d['sensor'][5]==1?'check':'times').'" aria-hidden="true"></i></td>';
              }else{
                echo "<td></td>";
              }
              if(isset($d['sensor'][6])){
                echo '<td><span style="display:none">'.$d['sensor'][6].'</span><i class="fa fa-'.($d['sensor'][6]==1?'check':'times').'" aria-hidden="true"></i></td>';
              }else{
                echo "<td></td>";
              }
              echo "<td>{$d['sensores']}</td>";
              echo "<td>{$d['porcentaje']}%</td>";
              echo '</tr>';
              $i++;
            }
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
  var table;

  $(function(){
    table = $("#tabla-cobertura").DataTable({
      dom: 'Brtip',   
      searching: false,
      order: [0, 'asc'],
      paging: false,
      responsive: true,
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      },
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
            title: "<?=$TITULO?> - <?=date("d-m-Y")?>",
          }
        ]
      },
    })
    .on("init.dt", function(){
      var wt = parseInt($("#contenido").css("width"));
      var wb = 330;
      var x = wt - wb ;
      var top = -25;
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
</script>