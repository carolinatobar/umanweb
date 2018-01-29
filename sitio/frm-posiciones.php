<?php
  require'autoload.php';

  $acc = new Acceso();

  $obj_gnr  = new General();
  $arr_pos  = $obj_gnr->listar_posiciones();

  $TITULO    = $module_label; //$texto_sitio["Nomenclatura Posiciones"];
  $SUBTITULO = '';

?>

<style>
  <?php include_once("assets/css/detalle-equipo.css") ?> 
  .img_axis{
    width: 100%;
    border: 3px solid #d6d6d6;
  }
  #esquema_equipo_grande{
  } 
  @media (min-width: 768px){
  #esquema_equipo_grande{    
    width: 100% !important;    
    margin-left: calc( calc( 100% - 480px ) / 2.4 );
    max-height: 500px; 
    max-width: 480px; 
    min-width: 480px;
  }
  }
  @media (min-width: 1024px){
   #esquema_equipo_grande{ 
    margin-left: calc( calc( 50% - 480px ) / 2.4 );
    max-height: 500px; 
    max-width: 50% !important; 
    min-width: 50% !important; 
   }
   #formulario_nomenclaturas{

   }
  }
  @media (min-width: 1440px){
   #esquema_equipo_grande{ 
    margin-top: 20px;
    margin-left: calc( calc( 50% - 480px ) / -2.4 );
    max-height: 500px; 
    max-width: 480px; 
    min-width: 480px; 
   }
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
    
    <div class="row">
      <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1">

        <div class="panel panel-default">
          <div class="panel-body">

            <div class="row">

              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5 hidden-xs"  id="esquema_equipo_grande">
                <?php
                  $neumaticos = array(1,0,0,1,1,1,1,1);
                  $colores    = array_fill(1,6,array('color'=>'black'));
                  $ruedas     = array_fill(1,6,'');
                  $attr['neum'][1] = 'position: absolute !important; left: calc( 50% - 95px) !important;';
                  $attr['neum'][2] = 'left: calc( 50% + 48px) !important;';
                  $attr['neum'][3] = 'left: calc( 50% - 133px) !important;';
                  $attr['neum'][4] = 'left: calc( 50% - 83px) !important;';
                  $attr['neum'][5] = 'left: calc( 50% + 40px) !important;';
                  $attr['neum'][6] = 'left: calc( 50% + 90px) !important;';
                  $attr['showPopover'] = false;
                  $attr['showNomenclatura'] = true;
                  $attr['nomenclatura']['class'] = 'bottom';
                  echo Core::dibuja_esquema_equipo($neumaticos, $colores, $ruedas, $attr);
                ?>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5" style="margin-top: 30px;" id="formulario_nomenclaturas">

                <form id="form-pos">
                  <div class="form-group">
                  <?php
                  if(isset($arr_pos) && is_array($arr_pos) && count($arr_pos) > 0){
                    foreach ($arr_pos as $pos) {
                      $pos_num  = $pos->POSICION;
                      $pos_val  = $pos->NOMENCLATURA;

                      echo '<div class="input-group">';
                      echo '<span class="input-group-addon" id="ipt_pos_'.$pos_num.'">'.$texto_sitio["Posicion"].' '.$pos_num.'</span>';
                      echo '<input type="text" name="'.$pos_num.'" class="form-control text-center" aria-describedby="ipt_pos_'.$pos_num.'" value="'.$pos_val.'"/>';
                      echo '</div>';
                    }
                  }
                  ?>
                  </div>

                  <button type="submit" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-saved" style="margin-right: 3px;" aria-hidden="true"></span>  <?php print $texto_sitio["Guardar"]; ?></button>
                </form>

              </div>

            </div>

          </div>

        </div>
        
      </div>
    </div>
  </div>

  </div>
</div>

<script>
  $(document).ready(function(){

    $("#form-pos").submit(function(event){

      event.preventDefault();

      swal({
        title: "Confirmación",
        text: "¿Está seguro(a) de que desea modificar las nomenclaturas?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        confirmButtonText: "Si"
      },
      function(){
        var p_url = $("#form-pos").serialize();

        $.ajax({
          type		:	"POST",
          url			:	'./ajax/ajax_actualizar_posiciones.php',
          data		:	p_url,
          cache		:	false,
          error   : function(a,b,c){ console.log(a+"|"+b+"|"+c); },
          success	:	function(data){
            console.log(data);

            if(data == 'OK'){
              location.reload();
            }
            else {
              swal('Error',data,'error');
              // console.log(data);
            }
          }
        });
      });
    });
  });
</script>
