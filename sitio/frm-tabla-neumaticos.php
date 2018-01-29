<?php
require 'autoload.php';

$acc = new Acceso();
$gen = new General();
$neu = new Neumatico();

$nomenclatura = $gen->getNomenclaturas();
$img_equipo   = $gen->getImagenesEquipo();
$neumsegun    = $gen->getParamValue('verneumaticosegun');
$db  = DB::getInstance();

$marcas     = $neu->obtenerMarcas();
$modelos    = $neu->obtenerModelos();
$compuestos = $neu->obtenerModelos();
$dimensiones= $neu->obtenerDimensiones();

$sql = "SELECT 
  f.NOMBRE AS Flota,
  c.ID_CAMION,
  c.NUMCAMION,
  te.NEUMATICOS AS 'configurados'  
  FROM uman_camion c INNER JOIN uman_flotas f ON c.NUMFLOTA=f.NUMFLOTAS 
  INNER JOIN uman_tipo_equipo te ON te.ID=c.tipo";

$camiones = $db->query($sql);
$equipo   = array();
$max_pos  = 0; //Almacena la mayor cantidad de posiciones que posee un camión para poder crear la tabla correctamente

foreach($camiones->results() as $c){
  $configurados = array_sum(explode(',',$c->configurados));

  $sql = "SELECT n.MARCA, n.DIMENSION, n.NUMIDENTI, n.NUMEROFUEGO, nc.ID_EQUIPO, nc.ID_POSICION, n.MODELO, 
    n.COMPUESTO, n.ID_PLANTILLA   
    FROM uman_neumatico_camion nc INNER JOIN uman_neumaticos n ON nc.ID_NEUMATICO=n.ID_NEUMATICO 
    WHERE nc.ID_EQUIPO=$c->ID_CAMION 
    ORDER BY nc.ID_EQUIPO ASC, nc.ID_POSICION ASC";

  $posiciones = $db->query($sql);
  $px = NULL;
  if($posiciones->count() > 0){
    if($posiciones->count() > $max_pos) $max_pos = $posiciones->count();
  }
  if($max_pos < $configurados) $max_pos = $configurados;
  foreach($posiciones->results() as $pos){
    if($neumsegun == 'fuego'){
      if($pos->NUMEROFUEGO != '' || $pos->NUMEROFUEGO != NULL) $cod = $pos->NUMEROFUEGO;
      else{
        if($pos->NUMIDENTI != '' || $pos->NUMIDENTI != NULL) $cod = $pos->NUMIDENTI;
        else $cod = $pos->NUMEROFUEGO;
      }
    }
    else{
      if($pos->NUMIDENTI != '' || $pos->NUMIDENTI != NULL) $cod = $pos->NUMIDENTI;
      else $cod = $pos->NUMEROFUEGO;
    }
    
    $px[$pos->ID_POSICION] = array(
      'FABRICANTE'=>$pos->MARCA,
      'DIMENSION'=>$pos->DIMENSION,
      'ID'=>$pos->NUMIDENTI,
      'CODIGO'=>$cod,
      'ID_EQUIPO'=>$pos->ID_EQUIPO,
      'POSICION'=>$pos->ID_POSICION,
      'MODELO'=>$pos->MODELO,
      'COMPUESTO'=>$pos->COMPUESTO,
      'PLANTILLA'=>$pos->ID_PLANTILLA == 0 ? '':$pos->ID_PLANTILLA
    );
  }

  $equipo[] = array(
    'ID'=>$c->ID_CAMION,
    'EQUIPO'=>$c->NUMCAMION,
    'FLOTA'=>$c->Flota,
    'POSICIONES'=>$px
  );
}

$TITULO    = $module_label; //'Tabla de Neumáticos en Equipos';
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
    for($i=5; $i<=11; $i++) $selector .= '.table>thead>tr:nth-child(1)>th:nth-child('.$i.'), ';
    echo substr($selector, 0, strlen($selector)-2);
  ?>
  {
    border-left: 1px solid #ddd;
  }
  .table>thead>tr:nth-child(1)>th:nth-child(11)
  {
    border-right: 1px solid #ddd;
  }

  <?php
    $selector = '';
    for($i=5; $i<=($max_pos*3)+3; $i++) $selector .= '.table>tbody>tr>td:nth-child('.$i.'), ';
    echo substr($selector, 0, strlen($selector)-2);
  ?>
  {
    border-left: 1px solid #ddd;
  }
  .table>tbody>tr>td:nth-child(11)
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
  <table class="table table-responsive" id="tabla-neumaticos">
    <thead>
      <tr>
        <th>#</th>
        <th>Equipo</th>
        <th>Pos</th>
        <th>Flota</th>        
        <th>N&deg; Fuego</th>
        <th>N&deg; Serie</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Dimensión</th>
        <th>Compuesto</th>
        <th>Plantilla</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td></td>
        <td><input type="text" style="width:100%" class="form-control" data-index="1" oninput="filtro(this);" ></td><!--Equipo-->
        <td><input type="text" style="width:100%" class="form-control" data-index="2" oninput="filtro(this);" ></td><!--Posición-->
        <td><input type="text" style="width:100%" class="form-control" data-index="3" oninput="filtro(this);" ></td><!--Flota--> 
        <td><input type="text" style="width:100%" class="form-control" data-index="4" oninput="filtro(this);" ></td><!--N° Fuego-->
        <td><input type="text" style="width:100%" class="form-control" data-index="5" oninput="filtro(this);" ></td><!--N° Serie-->
        <td>
          <select id="sMarca" class="selectpicker dropup" data-size="10" data-index="6" data-live-search="true" data-header="Selecciona una marca">
            <option value=""></option>
            <?php foreach($marcas as $marca){ 
              echo '<option value="'.$marca->nombre.'" data-tokens="'.$marca->nombre.'">'.$marca->nombre.'</option>'; 
            } ?>
          </select>
        </td><!--Marca-->
        <td>
          <select id="sModelo" class="selectpicker dropup" data-size="10" data-index="7" data-live-search="true" data-header="Selecciona un modelo">
            <option value=""></option>
            <?php foreach($modelos as $modelo){ 
              echo '<option value="'.$modelo->nombre.'" data-tokens="'.$modelo->nombre.'">'.$modelo->nombre.'</option>'; 
            } ?>
          </select>
        </td><!--Modelo-->
        <td>
          <select id="sDimension" class="selectpicker dropup" data-size="10" data-index="8" data-live-search="true" data-header="Selecciona una dimensión">
            <option value=""></option>
            <?php foreach($dimensiones as $dimension){ 
              echo '<option value="'.$dimension->dimension.'" data-tokens="'.$dimension->dimension.'">'.$dimension->dimension.'</option>'; 
            } ?>
          </select>
        </td><!--Dimensión--> 
        <td>
          <select id="sCompuesto" class="selectpicker dropup" data-size="10" data-index="9" data-live-search="true" data-header="Selecciona un compuesto">
            <option value=""></option>
            <?php foreach($compuestos as $compuesto){ 
              echo '<option value="'.$compuesto->nombre.'" data-tokens="'.$compuesto->nombre.'">'.$compuesto->nombre.'</option>'; 
            } ?>
          </select>
        </td><!--Compuesto-->
        <td><input type="text" style="width:100%" class="form-control" data-index="10" oninput="filtro(this);" ></td><!--Plantilla-->
      </tr>
    </tfoot>
    <tbody>
      <?php
        $i = 1;
        foreach($equipo as $e){
          for($x=1; $x<=$max_pos; $x++){
            if($e['POSICIONES'][$x]){
              echo '<tr>';
              echo '<td>'.$i++.'</td>';
              echo '<td><center><span style="float: center">'.$img_equipo[$e['ID']]['DIV36'].'</span><span style="float: center">'.$e['EQUIPO'].'</span></center></td>';
              echo '<td>'.$nomenclatura[$e['POSICIONES'][$x]['POSICION']].'</td>';
              echo '<td>'.$e['FLOTA'].'</td>';

              echo '<td>'.$e['POSICIONES'][$x]['CODIGO'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['ID'].'</td>';
              echo '<td><center>'.Core::imagen_marca_neumatico($e['POSICIONES'][$x]['FABRICANTE']).'<br/>'.$e['POSICIONES'][$x]['FABRICANTE'].'</center></td>';
              echo '<td>'.$e['POSICIONES'][$x]['MODELO'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['DIMENSION'].'</td>';              
              echo '<td>'.$e['POSICIONES'][$x]['COMPUESTO'].'</td>';
              echo '<td>'.$e['POSICIONES'][$x]['PLANTILLA'].'</td>';
              echo '</tr>';
            }
          }
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

    tabla = $("#tabla-neumaticos").DataTable({
      dom: 'Brtip',
      searching: true,
      // responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de Neumáticos - <?=date("d-m-Y")?>",
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
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de Neumáticos - <?=date("d-m-Y")?>' }]);
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
            title: "Tabla de Neumáticos - <?=date("d-m-Y")?>",
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


    $(".selectpicker").on("changed.bs.select", function(e){
      var v = $(e.currentTarget).selectpicker('val');
      var i = $(e.currentTarget).data("index");
      tabla.columns(i).search(v).draw();
    });

  });

  function filtro(o){
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }
</script>