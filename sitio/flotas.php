<?php
  require 'autoload.php';

  $acc = new Acceso();

  $obj_flt  = new Flota();
  $arr_pop  = $obj_flt->listar();
  $arr_flt  = $obj_flt->listar_full();
  $flota    = array();

  //Inicialización de arreglo de flotas con todas las flotas aunque no tengan equipos asignados.
  foreach($arr_pop as $fp){
    $flota[$fp->NUMFLOTAS]['ID']   = $fp->NUMFLOTAS;
    $flota[$fp->NUMFLOTAS]['NAME'] = $fp->NOMBRE;
  }

  if(isset($arr_flt) && count($arr_flt) > 0){
    foreach ($arr_flt as $flt) {
      $flota[$flt->NUMFLOTAS]['ID']   = $flt->NUMFLOTAS;
      $flota[$flt->NUMFLOTAS]['NAME'] = $flt->NOMBRE;

      if(isset($flt->ID_CAMION) && $flt->ID_CAMION > 0){
        $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]  = array( "ID"    =>  $flt->ID_CAMION,
                                                                      "CODE"  =>  $flt->NUMCAMION,
                                                                      "tipo"  =>  $flt->tipo,
                                                                      "NUM"   =>  $flt->NEUMATICOS,
                                                                      "CLASS_IMG" =>  $flt->CLASS_IMG);
      }
    }
  }

  $obj_eqp  = new Equipo();
  $arr_eqp  = $obj_eqp->listar_alone();
?>

<style>

  .ul-grid,
  .ul-grid-aside{
    list-style: none;
    text-align: left;
    padding: 4px;
    margin: 0;
  }
  .ul-grid-aside{
    text-align: center;
  }

  .truck,
  .truck-aside{
    display: inline-block;
    vertical-align: middle;
    text-align: center;
    width: 70px;
    margin: 4px;
  }

  .truck-add-text{
    margin-top: 45px;
    margin-bottom: 10px;
    font-size: 13px;
    color: #908d88;
  }
  .truck-window{
    background-color: white;
  }
  .truck-header,
  .truck-header-aside{
    background-color: #1079c5;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
  }
  .truck-header-aside{
    background-color: #92a3af;
  }


  .truck-body,
  .truck-body-aside{
    background-color: white;
    border: 1px solid #dedede;
    height: 85px;
    border-top: none;
  }
  .truck-body-aside{
    height: 70px;
  }

  .truck-body > a,
  .truck-body-aside > a{
    display: block;
    width: 100%;
    height: 100%;
  }
  .truck-body > a:hover,
  .truck-body-aside > a:hover{
    background-color: #dde5ea;
  }
  .truck-body > a > img{
    /* height: 88%; */
    width: inherit;
    margin: 5px auto 0 auto;
  }
  .truck-body-aside > a > img{
    height: 75%;
    margin: 5px auto 0 auto;
  }

  .popover-header-title{
    width: 200px;
    text-align: center;
    font-weight: 600;
  }
  #popover-flotas,
  #popover-remove{
    display: none;
  }

  /* bootstrap - overrides */
  button{
    outline: none !important;
  }
  .modal-dialog{
    max-width: 450px;
    font-size: 12px;
  }
  .panel {
    margin-bottom: 10px;
  }
  .panel-heading{
    font-weight: 600;
  }
  .panel-body{
    background-color: #fbfbfb;
    padding: 6px 10px;
  }
  .panel-body-aside{
    background-color: #e8e8e8;
    padding: 6px 4px;

  }
  table td{
    vertical-align: middle !important;
  }
</style>

<div class="container">
 	<div class="cc-divider">Flotas</div>
  <div class="row">
    <div class="section-equipos">
      <div class="panel panel-default">
        <div class="panel-heading"><b><?php print $texto_sitio["Equipos Disponibles"]; ?></b></div>
        <div class="panel-body-aside">
          <input type="hidden" id="hid_truck_id">
          <?php
            if(count($arr_eqp) > 0){

              echo "<ul class='ul-grid-aside'>";
              foreach ($arr_eqp as $equipo) {
                $eqp_id   = $equipo->ID_CAMION;
                $eqp_code = $equipo->NUMCAMION;
                $eqp_num  = $equipo->NUMNEUMATICOS;
                $eqp_type = $equipo->tipo;
                $eqp_box  = $equipo->ID_CAJAUMAN;
                $eqp_flt  = $equipo->NUMFLOTA;
                $eqp_img  = $equipo->CLASS_IMG.'.png';
                //$eqp_img  = $eqp_num == 6 ? 'truck_sm.png' : 'pickup_sm.png';

                echo "<li class='truck-aside truck-window'>
                        <div class='truck-header-aside'>{$eqp_code}</div>
                        <div class='truck-body-aside'>
                          <a u-truck='{$eqp_id}' tabindex='0' class='truck_mod a-truck' role='button' data-toggle='popover'><img src='assets/img/{$eqp_img}' alt=''></a>
                        </div>
                      </li>";
              }

              echo "</ul>";
            }
          ?>
        </div>
      </div>
      <!--    Popovers    -->
      <div id="popover-flotas">
        <?php
          if(count($arr_pop) > 0){
            foreach ($arr_pop as $pop) {
              $flt_id   = $pop->NUMFLOTAS;
              $flt_name = $pop->NOMBRE;
              echo "<button onclick='asignar_flota({$flt_id})' class='btn btn-primary btn-sm btn-block'>{$flt_name}</button>";
            }
          }
        ?>
      </div>
      <div id="popover-remove">
        <button onclick='asignar_flota(0)' class='btn btn-danger btn-sm btn-block'><?php print $texto_sitio["Quitar de Flota"]; ?></button>
      </div>
    </div>

    <button class="btn btn-md btn-success fleet_add" data-target="#g_fleet" data-toggle="modal">
      <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <?php print $texto_sitio["Crear Nueva Flota"]; ?>
    </button>

    <div class="section-flotas">
      <?php
        if(count($flota) > 0){
          foreach ($flota as $fl) {
            $flota_id   = $fl['ID'];
            $flota_name = $fl['NAME'];
      ?>
      <div class="panel panel-default">
        <div class="panel-heading text-left" style="padding: 6px 15px;">
          <div class="row" style="line-height: 30px;">
            <div class="<?= Core::col(6) ?>"><?php print $texto_sitio["FLOTA"]; ?> <b><?=$flota_name;?></b></div>
            <div class="<?= Core::col(6) ?>">
              <button class="btn btn-sm btn-default pull-right fleet_mod" u-id='<?=$flota_id;?>' u-name='<?=$flota_name;?>' data-target="#g_fleet" data-toggle="modal"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php print $texto_sitio["Editar Flota"]; ?></button>
            </div>
          </div>
        </div>
        <div class="panel-body">
        <?php
          if(isset($fl['EQUIPOS'])){
            echo "<ul class='ul-grid'>";
            foreach ($fl['EQUIPOS'] as $equipo) {
              $equipo_id   = $equipo['ID'];
              $equipo_code = $equipo['CODE'];
              $equipo_num  = $equipo['NUM'];
              $eqp_type    = $equipo['tipo'];
              $eqp_img     = $equipo['CLASS_IMG'].'.png';

              echo "<li class='truck truck-window'>
                      <div class='truck-header'>{$equipo_code}</div>
                      <div class='truck-body'>
                        <a u-truck='{$equipo_id}' class='truck_mod' href='#'><img src='assets/img/{$eqp_img}' alt=''></a>
                      </div>
                    </li>";
            }

            echo "</ul>";
          }
        ?>
        </div>
      </div>
      <?php
        }
        }
      ?>      
    </div>
  </div>
</div>

<!-- Small modal -->
<div id="g_fleet" class="modal fade" tabindex="-1" role="dialog" m-mode="I" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <input type="hidden" id="hid_flt_id">

      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><span id="modal_span_title"><?php print $texto_sitio["Nueva"]; ?></span> <?php print $texto_sitio["Flota"]; ?></h4>
      </div>

      <div class="modal-body">
        <input type="hidden" id="hid_eqp_id" value="">
        <table class="table table-bordered">
          <tr>
            <td class="active"><?php print $texto_sitio["Nombre"]; ?> <?php print $texto_sitio["Flota"]; ?></td>
            <td class="success">
                <input id="ipt_nombre_flota" type="text" class="form-control" placeholder="" value="">
            </td>
          </tr>
        </table>
      </div>

      <div class="modal-footer">
        <button id="btn_del_flota" class="btn btn-danger btn-sm pull-left"><?php print $texto_sitio["Borrar"]; ?></button>
        <button class="btn btn-default btn-sm" data-dismiss="modal"><?php print $texto_sitio["Cerrar"]; ?></button>
        <button id="btn_g_flota" class="btn btn-primary btn-sm"><span id="modal_span_button"><?php print $texto_sitio["Crear Nueva Flota"]; ?></span></button>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  $(document).ready(function(){


    $("#btn_g_flota").click(function(){

      var mode  = $(".modal").attr("m-mode");
      var id    = $("#hid_flt_id").val();
      var name  = $.trim($("#ipt_nombre_flota").val());
      var p_url = "name="+name;

      var file        = 'ajax_insertar_flota.php';
      var response    = 'Nueva Flota Ingresada';

      if(id != '' && mode == "U"){
        file        = 'ajax_modificar_flota.php';
        response    = 'Datos Actualizados';
        p_url       = "id="+id+"&name="+name;
      }


      if( (name != '' && mode == 'I') || (id != '' && name != '' && mode == 'U')  ){

        console.log(p_url);

        $.ajax({
          type		:	"POST",
          url			:	'./ajax/'+file,
          data		:	p_url,
          cache		:	false,
          error   : function(a,b,c){
                      console.log(a+"|"+b+"|"+c);
                    },
          success	:	function(data){

                      console.log(data);

                      if(data == 'OK'){
                        //alert(response);
                        location.reload();
                      } else {
                        //alert(data);
                        console.log(data);
                      }

                    }
        });

      } else {
        //alert("NOMBRE DE FLOTA ES OBLIGATORIO.");
      }

    });


    $("#btn_del_flota").click(function(){

      var id    = $("#hid_flt_id").val();

      if( id != '' ){

        if(confirm("Equipos asociados quedarán disponibles.")){
          $.ajax({
            type		:	"POST",
            url			:	'./ajax/ajax_eliminar_flota.php',
            data		:	'id='+id,
            cache		:	false,
            error   : function(a,b,c){
                        console.log(a+"|"+b+"|"+c);
                      },
            success	:	function(data){

                        console.log(data);

                        if(data == 'OK'){
                          //alert("Flota Eliminada");
                          location.reload();
                        } else {
                          //alert(data);
                          console.log(data);
                        }

                      }
          });
        }


      } else {
        //alert("NOMBRE DE FLOTA ES OBLIGATORIO.");
      }

    });


    $(".fleet_add").click(function(){
      set_modal("I");
    });

    $(".fleet_mod").click(function(){

      var id    = $(this).attr("u-id");
      var name  = $(this).attr("u-name");

      console.log(id, name);
      set_modal("U");

      $("#hid_flt_id").val(id);
      $("#ipt_nombre_flota").val(name);

    });

    $('.a-truck')
      .click(function(){
        var this_truck  = $(this).attr("u-truck");
        $("#hid_truck_id").val(this_truck);
      })
      .popover({
        placement : 'bottom',
        trigger: 'focus',
        html: true,
        title: '<div class="popover-header-title">Asignar a Flota</div>',
        content: $("#popover-flotas").html()
      });


    $('.truck_mod')
      .click(function(){
        var this_truck  = $(this).attr("u-truck");
        $("#hid_truck_id").val(this_truck);
      })
      .popover({
        placement : 'bottom',
        trigger: 'focus',
        html: true,
        title: '<div class="popover-header-title">Sacar de Flota</div>',
        content: $("#popover-remove").html()
      });
  });

  function asignar_flota(flt_id){
    var eqp_id  = $("#hid_truck_id").val();
    console.log("Asignar "+eqp_id+" a Flota: "+flt_id);

    if(eqp_id != '' && (flt_id != '' || flt_id == 0)){
      $.ajax({
          type		:	"POST",
          url			:	'./ajax/ajax_asignar_flota.php',
          data		:	'eqp_id='+eqp_id+"&flt_id="+flt_id,
          cache		:	false,
          error   : function(a,b,c){
            console.log(a+"|"+b+"|"+c);
          },
          success	:	function(data){
            console.log(data);

            if(data == 'OK'){
              location.reload();
            } else {
              //alert(data);
              console.log(data);
            }
          }
      });
    } else {
      //alert("Por favor, intente nuevamente.");
    }
  }

  function set_modal(mode){
    if(mode == "I"){
      $(".modal").attr("m-mode", "I");
      $("#modal_span_title").text("<?php print $texto_sitio["Nueva"]; ?>");
      $("#modal_span_button").text("<?php print $texto_sitio["Crear Nueva Flota"]; ?>");
      $("#hid_flt_id, #ipt_nombre_flota").val("");
      $("#btn_del_flota").hide("fast");
    } else {
      $(".modal").attr("m-mode", "U");
      $("#modal_span_title").text("<?php print $texto_sitio["Editar"]; ?>");
      $("#modal_span_button").text("<?php print $texto_sitio["Guardar"]; ?>");
      $("#btn_del_flota").show("fast");
    }
  }
</script>