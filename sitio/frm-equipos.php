<?php
  require 'autoload.php';

  $acc = new Acceso();

  $TITULO    = $module_label; //'Equipos';
  $SUBTITULO = '';

  $obj_eqp  = new Equipo();
  $arr_eqp  = $obj_eqp->listar();

  $obj_box  = new Caja();
  $arr_box  = $obj_box->listar();

  $obj_flt  = new Flota();
  $arr_flt  = $obj_flt->listar();

  $db = DB::getInstance();

?>

<style>

  .panel-heading{
    font-weight: 600;
  }
  .panel-body{
    background-color: #fbfbfb;
  }
  .ul-grid{
    list-style: none;
    text-align: left;
    padding: 4px;
  }
  .truck{
    display: inline-block;
    vertical-align: middle;
    text-align: center;
    width: 110px;
    height: 150px;
    margin: 4px;
    background-color: green;
  }
  .truck-add{
    background-color: whitesmoke;
    border: 1px solid #dadada;
    border-radius: 3px;
    margin-right: 0px;
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
  .truck-header{
    background-color: #1079c5;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
    height: 30px;
    line-height: 30px;
    font-weight: 500;
    text-align: center;
    color: white;
    font-weight: 600;
  }
  .truck-body{
    background-color: white;
    border: 1px solid #dedede;
    height: 120px;
    border-top: none;
  }
  .truck-body > a{
    display: block;
    width: 100%;
    height: 100%;
  }
  .truck-body > a:hover{
    background-color: #dde5ea;
  }
  .truck-body > a > img{

    height: 91%;
    margin: 5px auto;
  }

  /* bootstrap - overrides */
  button{
    outline: none !important;
  }
  .panel-body {
    padding: 15px 10px;
  }
  .modal-dialog{
    max-width: 450px;
    font-size: 12px;
  }
  table td{
    vertical-align: middle !important;
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
    <div class="<?=Core::col(10,10)?>"></div>
    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary truck_add" data-toggle="modal" data-target="#g_truck"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <div class="panel-body">

      <ul class="ul-grid">
        <?php
        foreach ($arr_eqp as $eqp) {
          $eqp_id   = $eqp->ID_CAMION;
          $eqp_code = $eqp->NUMCAMION;
          $eqp_num  = $eqp->NUMNEUMATICOS;
          $eqp_type = $eqp->tipo;
          $eqp_flt  = $eqp->NUMFLOTA;
          $eqp_img  = $eqp->CLASS_IMG.'.png';

          echo "<li class='truck truck-window'>
            <div class='truck-header'>{$eqp_code}</div>
            <div class='truck-body'>
              <a class='truck_mod' href='#' u-id='{$eqp_id}' u-code='{$eqp_code}' u-wheels='{$eqp_num}' u-type='{$eqp_type}' u-fleet='{$eqp_flt}' onclick='editar(this);'><img src='assets/img/{$eqp_img}' alt='' style='width:108px; height: auto;'></a>
            </div>
          </li>";
        }
        ?>
      </ul>

    </div>
  </div>
</div>


<!-- Small modal -->
<div id="g_truck" class="modal fade" tabindex="-1" role="dialog" m-mode="I" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><span id="modal_span_title">Nuevo</span> Equipo</h4>
      </div>
      <div class="modal-body">
        <form id="frm-equipo">
          <input type="hidden" id="id" name="id" value="">
          <input type="hidden" name="modo" id="modo" value="">

          <table class="table table-bordered">
            <tr>
              <td class="active obligatorio">CÓDIGO</td>
              <td class="success">
                  <input id="num" name="num" type="text" class="form-control" placeholder="Ej: 10005" aria-describedby="basic-addon2" value="ads">
              </td>
            </tr>
            <tr>
              <td class="active obligatorio">TIPO</td>
              <td class="success">
                <select class="form-control selectpicker" id="tipo" name="tipo">
                  <option>Seleccione una opción</option>
                  <?php
                    $info_tipo = $db->query("SELECT * FROM uman_tipo_equipo");
                    foreach( $info_tipo->results() as $data_tipo) {
                      print "<option value='".$data_tipo->ID."'>".utf8_encode($data_tipo->TIPO_EQUIPO)."</option>\n";
                    }
                  ?>
                </select>
              </td>
            </tr>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button id="Eliminar" class="btn btn-danger btn-sm pull-left">Eliminar</button>
        <!-- 10_01_2018 CT - Se agrega id al botón cancelar para limpiar los textbox al salir  -->
	<button id="Cancelar" type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
        <button id="Guardar" type="button" class="btn btn-primary btn-sm">Ingresar Equipo</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
  $(document).ready(function(){

    $("#Guardar").click(function(){
      var mode        = $(".modal").attr("m-mode");      
      var params = $("#frm-equipo").serializeArray();
      params.push({ name: 'tipo', value: $("#tipo").selectpicker('val')});
      
      $.post('ajax/equipos/crud.php', params, function(json){
        if(json){
          swal(json);
          actualiza_equipos();
	  //10_01_2018 CT - Se limpia select tipo al guardar
          $("#tipo").selectpicker('val', "");
          $("#g_truck").modal('hide');
        }
      });
    });

    $("#Eliminar").click(function(){
      $("#modo").val('eliminar');
      var params = $("#frm-equipo").serializeArray();
      
      $.post('ajax/equipos/crud.php', params, function(json){
        if(json){
          swal(json);
          $("#g_truck").modal('hide');
          actualiza_equipos();
	  //10_01_2018 CT - Se limpia select tipo al eliminar
          $("#tipo").selectpicker('val', "");
        }
      });
    });

    $(".truck_add").click(function(){
      $(".modal").attr("m-mode", "I");
      $("#modo").val('nuevo');
      $("#modal_span_title").text("Nuevo");
      $("#Guardar").text("Ingresar Equipo");
      $("#id, #num, #sel_neumaticos, #tipo, #sel_flota").val("");
      $("#Eliminar").hide("fast");
    });
  });

  function actualiza_equipos(){
    $.post('ajax/equipos/equipos.php', function(data){
      $("ul.ul-grid").html(data);
    });
  }

  function editar(o){
    $(".modal").attr("m-mode", "U");
    $("#modo").val('editar');
    $("#modal_span_title").text("Editar");
    $("#Guardar").text("Actualizar Datos");
    $("#Eliminar").show("fast");

    var id      = $(o).attr("u-id");
    var code    = $(o).attr("u-code");
    var type    = $(o).attr("u-type");

    $("#id").val(id);
    $("#num").val(code);
    $("#tipo").selectpicker('val',type);
    $("#g_truck").modal('show');
    // 10_01_2018 CT - Se limpia select tipo y num al cancelar  
    $("#Cancelar").click(function(){
        $("#tipo").selectpicker('val', "");
        $("#num").val("");
    });
  }

</script>
