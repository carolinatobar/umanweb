<?php
require 'autoload.php';

$acc = new Acceso();
$gen = new General();

$nomenclatura = $gen->getNomenclaturas();
$img_equipo   = $gen->getImagenesEquipo();
$db  = DB::getInstance();

$sql = "SELECT 
  f.NOMBRE AS Flota,
  c.ID_CAMION,
  c.NUMCAMION
  FROM uman_camion c INNER JOIN uman_flotas f ON c.NUMFLOTA=f.NUMFLOTAS";

$camiones = $db->query($sql);
$equipo   = array();
$max_pos  = 0; //Almacena la mayor cantidad de posiciones que posee un camión para poder crear la tabla correctamente

foreach($camiones->results() as $c){
  $sql = "SELECT ID_POSICION, PRESMAX, PRESMIN, TEMPMAX, PIF   
    FROM uman_neumatico_camion nc INNER JOIN uman_neumaticos n ON nc.ID_NEUMATICO=n.ID_NEUMATICO
    INNER JOIN uman_plantilla p ON n.ID_PLANTILLA=p.ID_PLANTILLA 
    WHERE ID_EQUIPO=$c->ID_CAMION 
    ORDER BY ID_POSICION ASC";

  $posiciones = $db->query($sql);
  $px = NULL;
  if($posiciones->count() > 0){
    if($posiciones->count() > $max_pos) $max_pos = $posiciones->count();
    //Verificar que la cantidad obtenida no se menor a la última posición
    //Esto ocurre cuando no tiene configurado sensor en un neumático en cualquier posición
    //inferior a la última
    if($max_pos < $posiciones->results()[$posiciones->count()-1]->ID_POSICION) $max_pos = $posiciones->results()[$posiciones->count()-1]->ID_POSICION;
  }
  foreach($posiciones->results() as $pos){
    $px[$pos->ID_POSICION] = array(
      'AP'=>$pos->PRESMAX,
      'BP'=>$pos->PRESMIN,
      'T'=>$pos->TEMPMAX,
      'PIF'=>$pos->PIF,
      'POS'=>$pos->ID_POSICION
    );
  }

  $equipo[] = array(
    'ID'=>$c->ID_CAMION,
    'EQUIPO'=>$c->NUMCAMION,
    'MARCA'=>'',
    'FLOTA'=>$c->Flota,
    'POSICIONES'=>$px
  );
}

$TITULO    = $module_label; //'Tabla de Umbrales de Equipos';
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

  <?php
    $selector = '';
    for($i=2; $i<=$max_pos; $i++) $selector .= '.table>thead>tr:nth-child(1)>th:nth-child('.$i.'), ';
    echo substr($selector, 0, strlen($selector)-2);
  ?>
  {
    border-left: 1px solid #ddd;
  }
  .table>thead>tr:nth-child(1)>th:nth-child(<?=($i-1)?>)
  {
    border-right: 1px solid #ddd;
  }
  
  <?php
    $selector = '';
    for($i=5; $i<=($max_pos*4)+4; $i+=4) $selector .= '.table>thead>tr:nth-child(2)>th:nth-child('.$i.'), ';
    echo substr($selector, 0, strlen($selector)-2);
  ?>
  {
    border-left: 1px solid #ddd;
  }
  .table>thead>tr:nth-child(2)>th:nth-child(<?=($i-1)?>)
  {
    border-right: 1px solid #ddd;
  }

  <?php
    $selector = '';
    for($i=5; $i<=($max_pos*4)+4; $i+=4) $selector .= '.table>tbody>tr>td:nth-child('.$i.'), ';
    echo substr($selector, 0, strlen($selector)-2);
  ?>
  {
    border-left: 1px solid #ddd;
  }
  .table>tbody>tr>td:nth-child(<?=($i-1)?>)
  {
    border-right: 1px solid #ddd;
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
  <table class="table table-hover" id="tabla-umbral">
    <thead>
      <tr>
        <th colspan="4">&nbsp;</th>
        <?php
          for($i=1; $i<=$max_pos; $i++){
        ?>
        <th colspan="4" class="th-pos"><?=$nomenclatura[$i]?></th>
        <?php
          }
        ?>
      </tr>
      <tr>
        <th>#</th>
        <th>Equipo</th>
        <th>Flota</th>
        <th>Marca</th>
        <?php
          for($i=1; $i<=$max_pos; $i++){
        ?>
        <th data-toggle="tooltip" title="Alta Presión">AP</th>
        <th data-toggle="tooltip" title="Baja Presión">BP</th>
        <th data-toggle="tooltip" title="Temperatura">T&deg;</th>
        <th data-toggle="tooltip" title="Presión de Inflado en Frío">PIF</th>
        <?php
          }
        ?>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th></th>
        <th><input type="text" style="width:100%" data-index="1" oninput="filtro(this);" name=""></th>
        <th><input type="text" style="width:100%" data-index="2" oninput="filtro(this);" name=""></th>
        <th><input type="text" style="width:100%" data-index="3" oninput="filtro(this);" name=""></th>
        <?php
          $idx = 3;
          for($i=1; $i<=$max_pos; $i++){
        ?>
        <th><input type="text" style="width:100%" data-index="<?=($idx+1)?>" oninput="filtro(this);" name=""></th>
        <th><input type="text" style="width:100%" data-index="<?=($idx+2)?>" oninput="filtro(this);" name=""></th>
        <th><input type="text" style="width:100%" data-index="<?=($idx+3)?>" oninput="filtro(this);" name=""></th>
        <th><input type="text" style="width:100%" data-index="<?=($idx+4)?>" oninput="filtro(this);" name=""></th>
        <?php
          $idx += 4;
          }
        ?>
      </tr>
    </tfoot>
    <tbody>
      <?php
        $i = 1;
        foreach($equipo as $e){
          echo '<tr>';
          echo '<td>'.$i++.'</td>';
          echo '<td>'.$img_equipo[$e['ID']]['DIV'].'<div class="center-block">'.$e['EQUIPO'].'</td>';
          echo '<td>'.$e['FLOTA'].'</td>';
          echo '<td></td>';
          for($x=1; $x<=$max_pos; $x++){
            if($e['POSICIONES'][$x]){
              echo '<td>'.$e['POSICIONES'][$x]['AP'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['BP'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['T'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['PIF'].'</td>';
            }
            else{
              echo '<td>&nbsp;</td>';
              echo '<td>&nbsp;</td>';
              echo '<td>&nbsp;</td>';
              echo '<td>&nbsp;</td>';
            }
          }
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

    tabla = $("#tabla-umbral").DataTable({
      dom: 'Brtip',
      searching: true,
      responsive: false,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de Umbrales - <?=date("d-m-Y")?>",
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
              $('row c ', sheet).each(function() {
                var attr = $(this).attr('r');
                var pre = attr.substring(0, 1);
                var ind = parseInt(attr.substring(1, attr.length));
                ind = ind + downrows;
                $(this).attr("r", pre + ind);
              });

              function Addrow(index, data) {

                msg = '<row r="' + index + '">';
                for (var i = 0; i < data.length; i++) {
                  var key = data[i].k;
                  var value = data[i].v;
                  msg += '<c t="inlineStr" r="' + key + index + '">';
                  msg += '<is>';
                  msg += '<t>' + value + '</t>';
                  msg += '</is>';
                  msg += '</c>';
                }
                msg += '</row>';
                return msg;
              }

              //título
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de Umbrales - <?=date("d-m-Y")?>', s: '51' }]);
              var r1 = Addrow(2, [{ k: 'A', v: 'Fecha :' }, { k: 'B', v: '<?=date("d-m-Y")?>' }]);
              <?php
                $cols = array(' ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
                $pos = 1;
                $c = '';
                $merge = '';
                $cells = '';
                $heads = '';
                for($i=5; $i<($max_pos*3)+3; $i+=3){
                  $c .= "{ k: '{$cols[$i]}', v: '{$nomenclatura[$pos]}', s: '51' },";
                  $merge .= "<mergeCell ref=\"{$cols[$i]}4:{$cols[$i+2]}4\" />";
                  $cells .= "'c[r={$cols[$i]}4]',";
                  $pos++;
                }
                for($i=5; $i<($max_pos*3)+5; $i++){
                  $heads .= "'c[r={$cols[$i]}5]',";
                }
                $merge .= "<mergeCell ref=\"A1:{$cols[$i]}1\" />";
                $cells .= "'c[r=A1]'";
              ?>
              var r2 = Addrow(4, [<?=$c?>]);

              var cells = [<?=$cells?>];
              var heads = [<?=$heads?>];

              sheet.childNodes[0].childNodes[1].innerHTML = ti + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
              sheet.childNodes[0].childNodes[2].innerHTML = '<?=$merge?>';
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
            title: "Tabla de Umbrales - <?=date("d-m-Y")?>"
          }
        ]
      },
      columnDefs: [
        { "orderable": false, targets: -1 },
        { "orderable": false, "targets": 0 },
        { "orderable": false, "targets": 1 },
        { "orderable": false, "targets": 2 },
        <?php
          $t = 3;
          for($i=1; $i<=($max_pos*4)+1; $i++){
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
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }
</script>