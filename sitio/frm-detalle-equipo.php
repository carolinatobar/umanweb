<?php
  require 'autoload.php';
  $f = new Flota();
  $f = $f->listar();
  $flota = array();
  foreach($f as $fx){ $flota[$fx->NUMFLOTAS] = $fx->NOMBRE; }

  $gen = new General();
  $img_equipo   = $gen->getImagenesEquipo();

  $e = new Equipo();
  $option = array(); 
  $equipos = $e->listar();
  foreach($equipos as $e){
    $option[$e->NUMFLOTA][] = '<option 
      data-tokens="'.$e->NUMCAMION.'" 
      data-content="'.str_replace('"','\'',$img_equipo[$e->ID_CAMION]['DIV']).'&nbsp;&nbsp;&nbsp;'.$e->NUMCAMION.'" 
      value="'.$e->ID_CAMION.'">'.$e->NUMCAMION.'</option>';
  }

  $TITULO    = $module_label; //'Detalle de Equipo';
  $SUBTITULO = 'Seleccione un equipo para ver su detalle.';
  ?>
<style>
 <?php include_once("assets/css/detalle-equipo.css") ?>
 <?php include_once("assets/css/drag-n-drop.css") ?>
 .posicion{
   color: rgba(33,33,33,1);
   font-size: 1.2em;
   font-weight: 800;
   padding-left: 5px;
   padding-top: 10px;
   height: 36px;
 }
 .posicion div{
   background-color: rgba(207,216,220,0.5);
   border-radius: 36px;
   position: absolute;
   top: 5px;
   width: 36px;
   height: 36px;
   text-align: center;
   padding-top: 4px;
 }
 #esquema_equipo_grande{
   margin-top: 20px;
 } 
 
  @media (min-width: 1440px){
    #esquema_equipo_grande{ 
      max-height: 500px; 
      /*max-width: 480px;  */
      min-width: 480px; 
      margin-top: 20px;
      -ms-transform: scale(1); /* IE 9 */
      -webkit-transform: scale(1); /* Safari */
      transform: scale(1);
    }
    .panel-body.detalle-conexion{
      font-size: 18px;
    }
    .panel-body.detalle-conexion > .input-group > .text-right{
    width: 200px !important;
    float: left;
    font-weight: bold;
    }
    .panel-body.detalle-conexion > .input-group > .text-left{
      float: left;
    }
  }
  @media (min-width: 767px){
    #esquema_equipo_grande{    
      margin-top: -35px;
      max-height: 500px; 
      /*max-width: 480px;  */
      min-width: 480px;    
      -ms-transform: scale(.8); /* IE 9 */
      -webkit-transform: scale(.8); /* Safari */
      transform: scale(.8);
    }
    .panel-body.detalle-conexion{
      font-size: 12px;
    }
    .panel-body.detalle-conexion > .input-group > .text-right{
      width: 100px !important;
      float: left;
      font-weight: bold;
    }
    .panel-body.detalle-conexion > .input-group > .text-left{
      float: left;
    }
  }
  @media (max-width: 991px) and (min-width: 768px){
    #contenido{
      margin-top: 50px !important;
    }
  }
  @media (max-width: 767px){
    #contenido{
      margin-top: 50px !important;
    }
  }
  @media (max-width: 425px){
    .panel-body.detalle-conexion{
      font-size: 10px;
    }
    .panel-body.detalle-conexion > .input-group > .text-right{
      width: 90px !important;
      float: left;
      font-weight: bold;
    }
    .panel-body.detalle-conexion > .input-group > .text-left{
      float: left;
    }
  }
  .popover{
    z-index: 1 !important;
  }
  .neumatico{
    margin-top: 0 !important;
  }
  .sensor-number{
    display:none;
  }
  .popover.left > .arrow:after{
    border-left-color: #3b3b3b !important;
  }
  .popover.top > .arrow:after{
    border-top-color: #3b3b3b !important;
  }
  .popover.right > .arrow:after{
    border-right-color: #3b3b3b !important;
  }
  .popover.bottom > .arrow:after{
    border-bottom-color: #3b3b3b !important;
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
    <div class="<?=Core::col(7,7)?>"></div>
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Equipos&nbsp;</label>
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

    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" id="btn-ver">Ver</button>
      </div>
    </div>

    <div class="<?=Core::col(12)?> visible-xs visible-sm">&nbsp;</div>

  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <div id="detalle-equipo" class="container-fluid"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $("#btn-ver").on('click', function(){
      var equipo = $("#equipo").selectpicker('val');
      $.post('data-detalle-equipo.php',{equipo:equipo}, function(data){
        $("#detalle-equipo").html(data);
      });
    });
  });
</script>