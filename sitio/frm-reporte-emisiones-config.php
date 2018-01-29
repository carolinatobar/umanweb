<?php
  require 'autoload.php';

  $acc = new Acceso();

  $db = DB::getInstance();

  $sql = "SELECT * FROM
    (SELECT DISTINCTROW
      c.NUMCAMION,
        po.NOMENCLATURA,
        s.CODSENSOR,
        s.TIPO,
        n.NUMIDENTI,
        n.MODELO,
        CONCAT(p.MARCA,' ',p.MODELO,' ',p.DIMENSION) AS PLANTILLA,
        p.PIF, 
        u.tempmax,
        u.presmax,
        u.presmin,
        u.eventotemperatura,
        u.eventopresion,
        u.eventobateria, 
        DATE_FORMAT(u.fecha_evento,'%d/%m/%Y %H:%i:%s') as fecha_evento
        
    FROM uman_camion c 
    LEFT JOIN uman_neumatico_camion nc ON c.ID_CAMION=nc.ID_EQUIPO
    LEFT JOIN uman_neumaticos n ON n.ID_NEUMATICO=nc.ID_NEUMATICO
    LEFT JOIN uman_ultimoevento u ON u.posicion=nc.ID_POSICION
    LEFT JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR
    LEFT JOIN uman_plantilla p ON p.ID_PLANTILLA=n.ID_PLANTILLA
    LEFT JOIN uman_posicion po ON po.POSICION=nc.ID_POSICION
    WHERE fecha_evento NOT LIKE '0000-00-00%' AND fecha_evento <= DATE_SUB(NOW(), INTERVAL 12 MINUTE)
    ORDER BY u.fecha_evento DESC) AS x

    ORDER BY NOMENCLATURA ASC, fecha_evento DESC";
  $emisiones = $db->query($sql);

  $TITULO    = $module_label; //'Reporte de estado de emisiones y configuración';
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
  .tabla-resumen, .tabla-pif{
    background: #fff;
  }
  .tabla-resumen > thead :first-child > :nth-child(2),
  .tabla-resumen > thead :first-child > :nth-child(3),
  .tabla-resumen > thead :first-child > :nth-child(4),
  .tabla-resumen > thead :nth-child(2) > :nth-child(1),
  .tabla-resumen > thead :nth-child(2) > :nth-child(2),
  .tabla-resumen > thead :nth-child(2) > :nth-child(11),
  .tabla-resumen > thead :nth-child(2) > :nth-child(12),
  .tabla-resumen > thead :nth-child(2) > :nth-child(13) {
    border: thin solid #ECEFF1;
    border-bottom: thin solid #ECEFF1;
  }
  .tabla-resumen > thead :nth-child(2) > :nth-child(3),
  .tabla-resumen > thead :nth-child(2) > :nth-child(5),
  .tabla-resumen > thead :nth-child(2) > :nth-child(8) {
    border-left: thin solid #ECEFF1;
    border-bottom: thin solid #ECEFF1;
  }
  .tabla-resumen > thead :nth-child(2) > :nth-child(4),
  .tabla-resumen > thead :nth-child(2) > :nth-child(6),
  .tabla-resumen > thead :nth-child(2) > :nth-child(7),
  .tabla-resumen > thead :nth-child(2) > :nth-child(9),
  .tabla-resumen > thead :nth-child(2) > :nth-child(10) {
    border-bottom: thin solid #ECEFF1;
  }
  .tabla-resumen > tbody > tr > td{
    border-bottom: 1px solid white;
  }
  .tabla-resumen > tbody > tr > :nth-child(3),
  .tabla-resumen > tbody > tr > :nth-child(5),
  .tabla-resumen > tbody > tr > :nth-child(8) {
    border-left: thin solid #ECEFF1;
  }
  .tabla-resumen > thead, .tabla-pif > thead {
    border: thin solid #ECEFF1;
  }
  .tabla-resumen td, .tabla-pif td{
    text-align: center;
  }
  .tabla-resumen td span {
    font-size: 18px; 
    font-weight: 800;
  }
  .tabla-resumen th, .tabla-pif th{
    text-align: center;
    font-weight: 800;
    font-size: 16px;
  }
  .tabla-resumen td, .tabla-pif td{
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
<script src="assets/js/moment.js"></script>

<div class="container">
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <div class="row">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#emisiones" aria-controls="emisiones" role="tab" data-toggle="tab">Emisiones</a></li>
      <li role="presentation"><a href="#pif" aria-controls="pif" role="tab" data-toggle="tab">PIF</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade in active" id="emisiones">
        <table class="table table-responsive tabla-resumen">
          <thead>
            <tr>
              <th colspan=2></th>
              <th colspan=2>Sensor</th>
              <th colspan=3>Neumático</th>
              <th colspan=4>Umbrales</th>
              <th colspan=4></th>
            </tr>
            <tr>
              <th>Equipo</th>
              <th>Posición</th>
              <th>ID</th>
              <th>Tipo</th>
              <th>ID</th>
              <th>Modelo</th>
              <th>Plantilla</th>
              <th>T&deg; Máx.</th>
              <th>P&deg; Máx.</th>
              <th>P&deg; Mín.</th>
              <th>PIF</th>
              <th>Presión</th>
              <th>Temperatura</th>
              <th>Batería</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if($emisiones->count() > 0){
                foreach($emisiones->results() as $e){
                  echo '<tr>';
                  echo "<td>{$e->NUMCAMION}</td>";
                  echo "<td>{$e->NOMENCLATURA}</td>";
                  echo "<td>{$e->CODSENSOR}</td>";
                  echo "<td>{$e->TIPO}</td>";
                  echo "<td>{$e->NUMIDENTI}</td>";
                  echo "<td>{$e->MODELO}</td>";
                  echo "<td>{$e->PLANTILLA}</td>";
                  echo "<td>{$e->tempmax}</td>";
                  echo "<td>{$e->presmax}</td>";
                  echo "<td>{$e->presmin}</td>";
                  echo "<td>{$e->PIF}</td>";
                  echo "<td>{$e->eventotemperatura}</td>";
                  echo "<td>{$e->eventopresion}</td>";
                  echo "<td>{$e->eventobateria}%</td>";
                  echo "<td class='bg-danger'>{$e->fecha_evento}</td>";
                  echo '</tr>';
                }
              }
            ?>
          </tbody>
        </table>
      </div>
      <div role="tabpanel" class="tab-pane fade" id="pif">
        <table class="table table-responsive tabla-pif">
          <thead>
            <tr>
              <th>Equipo</th>
              <th>Posición</th>
              <th>PIF Planificada</th>
              <th>PIF Real</th>
              <th>Desviación</th>
              <th>Plantilla</th>
            </tr>
          </thead>
          <tbody>
            <?php
              
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>