<?php
  require 'autoload.php';

  $acc = new Acceso();

  $gen        = new General();
  $utemp      = $gen->getParamValue('unidad_temperatura');
  $upres      = $gen->getParamValue('unidad_presion');
  $img_equipo = $gen->getImagenesEquipo();

  $sql = "SELECT
    c.NUMCAMION AS EQUIPO,
    c.ID_CAMION,
    po.NOMENCLATURA AS POSICION,
    p.MARCA,
    n.MODELO,
    s.CODSENSOR AS SENSOR,
    s.TIPO,
    n.NUMIDENTI AS NEUMATICO,
    u.tempmax AS TEMPMAX,
    u.presmax AS PRESMAX,
    u.presmin AS PRESMIN,
    u.eventopresion AS PRESION,
    u.eventotemperatura AS TEMPERATURA,
    u.eventobateria AS BATERIA,
    u.fecha_evento AS FECHA_EVENTO,
    u.fecha_descarga AS FECHA_DESCARGA,
    p.PIF,
    ROUND((p.PIF * (u.eventotemperatura + 273.15) / 291.15)) AS PIC
    FROM uman_camion c 
      LEFT JOIN uman_neumatico_camion nc ON c.ID_CAMION=nc.ID_EQUIPO
      LEFT JOIN uman_neumaticos n ON n.ID_NEUMATICO=nc.ID_NEUMATICO
      LEFT JOIN uman_ultimoevento u ON u.posicion=nc.ID_POSICION
      LEFT JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR
      LEFT JOIN uman_plantilla p ON p.ID_PLANTILLA=n.ID_PLANTILLA
      LEFT JOIN uman_posicion po ON po.POSICION=nc.ID_POSICION
      WHERE fecha_evento NOT LIKE '0000-00-00%'";        
  $db = DB::getInstance();
    
  $data = $db->query($sql);

  $TITULO    = $module_label; //'Datos UmanWeb';
  $SUBTITULO = '';
?>

<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
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
  <table class="table table-hover" id="tabla-umanweb">  
      <thead>
        <tr>
          <th>ID</th>
          <th>Equipo</th>
          <th>Pos.</th>
          <th>Marca</th>
          <th>Modelo</th>
          <th>Sensor</th>
          <th>Serie Neumático</th>
          <th>T&deg; Máx.</th>
          <th>P&deg; Máx.</th>
          <th>P&deg; Mín.</th>
          <th>Presión</th>
          <th> T° </th>
          <th>Batería</th>
          <th>Fecha</th>
          <th>Última Actualización</th>
          <th>PIF</th>
          <th>PIC</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if($data->count() > 0)
          {
            $i=1;
            foreach($data->results() as $d){
              echo '<tr>';
              echo "<td>{$i}</td>";
              echo '<td>'.$img_equipo[$d->ID_CAMION]['DIV36'].'<br/>'.$d->EQUIPO.'</td>';
              echo "<td>{$d->POSICION}</td>";
              echo "<td>{$d->MARCA}</td>";
              echo "<td>{$d->MODELO}</td>";
              echo '<td>'.Core::imagen_sensor($d->TIPO,24,'margin:0 auto -18px auto !important').'<br/>'.$d->SENSOR.'</td>';
              echo "<td>{$d->NEUMATICO}</td>";
              echo '<td>'.Core::tpConvert($d->TEMPMAX,$utemp,true).'</td>';
              echo '<td>'.Core::tpConvert($d->PRESMAX,$upres,true).'</td>';
              echo '<td>'.Core::tpConvert($d->PRESMIN,$upres,true).'</td>';
              echo '<td>'.Core::tpConvert($d->PRESION,$upres,true).'</td>';
              echo '<td>'.Core::tpConvert($d->TEMPERATURA,$utemp,true).'</td>';
              echo "<td>{$d->BATERIA}%</td>";
              echo "<td>{$d->FECHA_EVENTO}</td>";
              echo "<td>{$d->FECHA_DESCARGA}</td>";
              echo "<td>{$d->PIF}</td>";
              echo "<td>{$d->PIC}</td>";
              echo '</tr>';
              $i++;
            }
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var table;

  $(function(){
    table = $("#tabla-umanweb").DataTable({
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
            filename: "Datos UmanWeb",
            title: "Datos UmanWeb"
          }, 
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "Datos UmanWeb"
          }
        ]
      },
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    });
  });
</script>