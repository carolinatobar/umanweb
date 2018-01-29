<?php
  require 'autoload.php';

  $acc = new Acceso();

  $db = DB::getInstance();

  $TITULO    = $module_label; //'Actualizar Firmware UMAN Blue';
  $SUBTITULO = '';
?>

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
    <div class="<?=Core::col(9,9)?>"></div>
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal">Actualizar TODAS</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <?php
      $sql = "SELECT c.CODIGOCAJA AS 'codigo', c.ID_CAJAUMAN AS 'id', 
        (SELECT DATE_FORMAT(fecha,'%d/%m/%Y %H:%i:%s') FROM uman_firmware_updates WHERE id_cajauman=c.ID_CAJAUMAN AND dispositivo=\"UMANBLUE\") AS 'fecha_umanblue', 
        (SELECT DATE_FORMAT(fecha,'%d/%m/%Y %H:%i:%s') FROM uman_firmware_updates WHERE id_cajauman=c.ID_CAJAUMAN AND dispositivo=\"TABLET\") AS 'fecha_tablet',
        eu.ACTUALIZAR_UMANBLUE AS 'actualizar_umanblue', eu.ACTUALIZAR_TABLET AS 'actualizar_tablet' 
        FROM uman_cajauman AS c LEFT JOIN uman_estado_umanblue AS eu ON c.CODIGOCAJA=eu.UMAN_BLUE
        WHERE ESTADO='1'";
      $datos_equipos    = $db->query($sql);

      foreach($datos_equipos->results() as $de) {
    ?>
      <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">

        <div class="panel panel-default">
          <div class="panel-heading">
            <b><?php print $de->codigo; ?></b>
          </div>
          <div class="panel-body">
            <b>Firmware UMAN Blue</b><br/>
            <?php
              if($de->fecha_umanblue != '') print "&nbsp;&nbsp;<small>Última actualización: &nbsp;{$de->fecha_umanblue}</small>";
              else print "&nbsp;&nbsp;<small>&nbsp;</small>";
            ?>
            <form action="procesar-guardado.php" method="POST">
              <input type="hidden" name="modo" value="firmware">
              <input type="hidden" name="dispositivo" value="UMANBLUE">
              <input type="hidden" name="id" value="<?=$de->id?>">
              <input type="hidden" name="codigocaja" value="<?=$de->codigo?>">
              <?php
                if( $de->actualizar_umanblue == 1 ){
              ?>
                <a class="btn btn-primary btn-sm disabled <?=Core::col(8)?> <?=Core::offset(2)?>">
                  <span class="glyphicon glyphicon-time" style="margin-right: 3px;" aria-hidden="true"></span>Esperando conexión de UMAN Blue
                </a>
                <br/>
              <?php
                }
                else {
              ?>
                <button type="submit" class="btn btn-primary btn-sm <?=Core::col(8)?> <?=Core::offset(2)?>">
                  <span class="glyphicon glyphicon-upload" style="margin-right: 3px;" aria-hidden="true"></span>Actualizar UMAN Blue
                </button>
                <br/>
              <?php
                }
              ?>
            </form>
            <hr>
            <b>Firmware Tablet</b><br/>
            <?php
              if($de->fecha_tablet != '') print "&nbsp;&nbsp;<small>Última actualización: &nbsp;{$de->fecha_tablet}</small>";
              else print "&nbsp;&nbsp;<small>&nbsp;</small>";
            ?>
            <form action="procesar-guardado.php" method="POST">
              <input type="hidden" name="modo" value="firmware">
              <input type="hidden" name="dispositivo" value="TABLET">
              <input type="hidden" name="id" value="<?=$de->id?>">
              <input type="hidden" name="codigocaja" value="<?=$de->codigo?>">
              <?php
                if($de->actualizar_tablet == 1){
              ?>
                <a class="btn btn-primary btn-sm disabled <?=Core::col(8)?> <?=Core::offset(2)?>">
                  <span class="glyphicon glyphicon-time" style="margin-right: 3px;" aria-hidden="true"></span>Esperando conexión de UMAN Blue
                </a>
                <br/>
              <?php
                }
                else {
              ?>
                <button type="submit" class="btn btn-primary btn-sm <?=Core::col(8)?> <?=Core::offset(2)?>">
                  <span class="glyphicon glyphicon-upload" style="margin-right: 3px;" aria-hidden="true"></span>Actualizar Tablet
                </button>
                <br/>
              <?php
                }
              ?>
            </form>
            <p>&nbsp;</p>
          </div>
        </div>

      </div>
    <?php
      }
    ?>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title danger" id="exampleModalLabel">Actualizar TODAS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="<?=Core::col(6)?>">
            <form action="procesar-guardado.php" method="POST">
              <input type="hidden" name="modo" value="firmware_todas">
              <input type="hidden" name="dispositivo" value="UMANBLUE">
              <button type="submit" class="btn btn-danger btn-sm center-block">
                <span class="glyphicon glyphicon-upload" style="margin-right: 3px;" aria-hidden="true"></span>Actualizar TODAS las UMAN Blue
              </button>
            </form>
          </div>
          <div class="<?=Core::col(6)?>">
            <form action="procesar-guardado.php" method="POST">
              <input type="hidden" name="modo" value="firmware_todas">
              <input type="hidden" name="dispositivo" value="TABLET">
              <button type="submit" class="btn btn-danger btn-sm center-block" >
                <span class="glyphicon glyphicon-upload" style="margin-right: 3px;" aria-hidden="true"></span>Actualizar TODAS las Tablets
              </button>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>