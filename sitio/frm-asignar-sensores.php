<?php
  require 'autoload.php';

  $obj_neu  = new Neumatico();
  $arr_neu  = $obj_neu->get_master();

  $obj_sns  = new Sensor();
  $arr_sns  = $obj_sns->get_disponibles();

  $verNeumaticoPor = (new General())->getParamvalue('verneumaticosegun');

  $TITULO    = $module_label; //'Asignar Sensores';
  $SUBTITULO = '';
?>

<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/drag-n-drop.css") ?>
</style>
<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<style>
  .selected{
    background-color: #3c8dbc;
    color: white;
  }
  .sensor-cell{
    height: 100px;
    padding: 0 10px 10px 10px;
  }
  .sensor-cell-selector{
    height: 70px;
    width: 60px;
    margin: 5px;
    border: thin solid #999;
    cursor: pointer;
  }
  #sens_img{
    width: 100px;
    height: 100px;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
  }
  .sensor-icono-selector{
    width: 45px;
    height: 50px;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
  }
  #sens_cod, #sens_typ, #sens_finst{    
    font-size: 80%;
    font-weight: 800;
  }
  .sensor-codigo-selector, .sensor-tipo-selector{
    font-size: 14px;
    font-weight: 800;
    text-align: center;
    margin-top: -5px;
  }
  .sensor-tipo-selector{
    font-size: 50%;
  }
  #sens_cod::before{
    content: "CÓDIGO : ";
    font-weight: normal;
  }
  #sens_typ::before{
    content: "TIPO : ";
    font-weight: normal;
  }
  .rangepicker{
    width: 100% !important;
    height: 34px;
    padding: 6px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
  }
  .USO{
    background-color: rgba(128,216,255 ,1) !important;
  }
  .BAJA{
    background-color: rgba(255,158,128 ,1) !important;
    display: none;
  }
  .DISPONIBLE{
    background-color: rgba(204,255,144 ,1) !important;
  }
  #neum_id, #neum_num, #neum_com, #neum_sta, #neum_bra, #neum_mod{
    font-weight: 800;
    font-size: 80%;
  }
  #neum_img{
    width: 100px;
    height: 100px;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
  }
  #neum_id::before{
    content: 'ID NEUMÁTICO : ';
    font-weight: normal;
  }
  #neum_num::before{
    content: '<?= ($verNeumaticoPor=='fuego' ? 'NÚMERO FUEGO : ' : 'NÚM ID : ') ?>';
    font-weight: normal;
  }
  #neum_com::before{
    content: 'COMPUESTO : ';
    font-weight: normal;
  }
  #neum_sta::before{
    content: 'ESTADO : ';
    font-weight: normal;
  }
  #neum_bra::before{
    content: 'MARCA : ';
    font-weight: normal;
  }
  #neum_mod::before{
    content: 'MODELO : ';
    font-weight: normal;
  }
  #sensor-grid{
    max-height: 150px;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0;
    width: 100%;
  }
  .left-margin-5px{
    margin-left: -5px;
  }
  #seleccion_sensores{
    display: none;
  }
  #sensor_actual{
    display:block;
  }
  #seleccion_fecha_inst{
    display: none;
  }
  #seleccion_fecha_inst, #seleccion_fecha_dinst{
    padding: 10px;
  }
  #seleccion_fecha_inst>span{
    font-size: 80%;
    text-align: center;
    font-style: cursive;
  }
  #seleccion_fecha_dinst>span{
    font-size: 80%;
    text-align: center;
    font-style: cursive;
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
  <div class="filtro-contenido">
  <div class="<?=Core::col(8,8,null,null)?>"></div>
    <div class="<?=Core::col(4,4,12,12)?>">
      <div class="frm-group">
        <label><?php print $texto_sitio["Filtro"]; ?> <small>(Neumático / Sensor / Marca)</small></label>
        <input type="text" class="form-control" placeholder="Filtro" id="tire-filter">
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <input type="hidden" id="old_sensor_id" value="">
    <input type="hidden" id="new_sensor_id" value="">
    <input type="hidden" id="neum_id" value="">

    <div class="panel panel-default">
      <div class="panel-body">
        <div id="contenedor-neumaticos" class="<?=Core::col(12)?>" style="height: 90%; overflow-y: auto;">
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Ver/Eliminar Sensor -->
<div id="modal-view-sensor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content text-left">
      <div class="modal-header" style="padding: 10px 15px;">
        <?php print $texto_sitio["Sensor"]." - ".$texto_sitio["Neumatico"]; ?> <b></b>
      </div>
      <div class="modal-body" style="height: 200px">

        <div class="row">
          <!-- DATOS DEL NEUMÁTICO -->
          <div class="<?=Core::col(6,6,12,12)?>">
            <div class="<?=Core::col(5)?>">
              <div id="neum_img" style="background: url('./assets/img/neumaticos/acme.png');"></div>
            </div>
            <div class="<?=Core::col(7)?>">
              <div id="neum_num"></div>
              <div id="neum_com"></div>
              <div id="neum_bra"></div>
              <div id="neum_mod"></div>
              <div id="neum_sta"></div>
            </div>
          </div>

          <!-- DATOS DEL SENSOR -->
          <div class="<?=Core::col(6,6,12,12)?>">
            <!-- SENSOR ACTUAL SI TIENE UNO -->
            <div class="<?=Core::col(12)?>" id="sensor_actual" style="padding-left: 0; padding-right: 0;">
              <div class="<?=Core::col(5)?>" style="padding-left: 0;">
                <div class='pull-left' id="sens_img"></div>              
              </div>
              <div class="<?=Core::col(7)?>">
                <div id="sens_cod"></div>
                <div id="sens_typ"></div>                
                <br/>
                <button type="button" class="btn btn-xs btn-danger <?=Core::col(12)?>" id="btn_retirar_sensor">Retirar Sensor</button>
                <br/>
                <button type="button" class="btn btn-xs btn-danger <?=Core::col(12)?>" id="btn_cambiar_sensor">Cambiar Sensor</button>
              </div>
              <div class="<?=Core::col(12)?>">
                <div class="alert alert-warning text-center hidden" style="padding:2px;" id="mensaje-sensor2">
                  <small>Precaución, el neumático se encuentra en uso. <br/>Presione "Habilitar" si desea continuar. <br/>
                    <button type="button" id="habilitar_mod" class="btn btn-danger btn-xs">Habilitar</button>
                  </small>
                </div>

                <div class="alert alert-warning text-center" style="padding:2px;" id="mensaje-sensor">
                  <small>El neumático se encuentra en uso, para cambiar o quitar el sensor, primero tiene que desmontar el neumático<br/>
                  </small>
                </div>
              </div>
            </div>

            <!-- MOTIVOS DEL RETIRO DEL SENSOR -->
            <div class="<?=Core::col(10)?> <?=Core::offset(1)?> hidden" id="retiro_sensor">
              <div class="<?=Core::col(12)?>" id="seleccion_fecha_dinst">
                <span>Por favor seleccione la fecha real de retiro/baja del sensor.</span>
                <div id="sens_fdinst">
                  <input type="text" class="rangepicker" name="fecha_dinst_sensor" id="fecha_dinst_sensor" value="<?=date("d/m/Y H:i")?>" />
                </div>
              </div>
              <button type="button" class="btn btn-xs btn-danger <?=Core::col(12)?>" id="btn_stock_sensor">Dejar en stock</button>
              <br/>
              <button type="button" class="btn btn-xs btn-danger <?=Core::col(12)?>" id="btn_baja_sensor">Dar de baja</button>
              <br/>
              <div class="<?=Core::col(12)?> hidden" id="motivos_baja">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <div>
                      <input type="radio" name="motivo" value="1">
                      <label>Fin de la vida útil</label>
                    </div>

                    <div>
                      <input type="radio" name="motivo" value="2">
                      <label>Falla</label>
                    </div>

                    <div>
                      <input type="radio" name="motivo" value="3">
                      <label>Desprendimiento</label>
                    </div>

                    <div>
                      <input type="radio" name="motivo" value="5">
                      <label>Destruído</label>
                    </div>

                    <div>
                      <input type="radio" name="motivo" value="4">
                      <label>Sin información</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SENSORES DISPONIBLES -->
            <div class="<?=Core::col(12)?>" id="seleccion_sensores">
              <div class="input-group" style="max-width: 240px; margin: 0 auto;">
                <span class="input-group-addon" id="sizing-addon2"><?php print $texto_sitio["Filtro"]; ?></span>
                <input type="text" class="form-control" placeholder="<?php print $texto_sitio["Codigo Sensor"]; ?>" id="sensor-filter">
              </div>
              <div id="sensor-grid" class='row'>
                <?php
                  if(isset($arr_sns) && count($arr_sns) > 0){
                    foreach ($arr_sns as $sns) {
                      $sns_tipo = strtolower($sns->TIPO);
                ?>
                <div s-string="<?=$sns->CODSENSOR?>" class="<?=Core::col(3)?> left-margin-5px">
                  <div class="sensor-cell-selector center-block" s-id="<?=$sns->ID_SENSOR?>">
                    <div class="sensor-icono-selector center-block" style="background: url('assets/img/sensor_<?=strtolower($sns_tipo)?>.png');"></div>
                    <div class="sensor-codigo-selector center-block"><?=$sns->CODSENSOR?></div>
                    <div class="sensor-tipo-selector center-block"><?=$sns->TIPO?></div>
                  </div>
                </div>
                <?php
                    }
                  }
                ?>
              </div>              
            </div>

            <div class="<?=Core::col(12)?>" id="seleccion_fecha_inst">
              <span>Por favor seleccione la fecha real de instalación del sensor.</span>
              <div id="sens_finst">
                <input type="text" class="rangepicker" name="fecha_inst_sensor" id="fecha_inst_sensor" value="<?=date("d/m/Y H:i")?>" />
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer" style="padding: 8px 15px;">
        <button id="btn_guardar_cambios" type="button" class="btn btn-success btn-sm pull-right hidden">
          <?php print $texto_sitio["Guardar"]; ?>
        </button>
        
        <button id="btn_asignar_sensor" type="button" class="btn btn-primary btn-sm pull-right hidden" disabled>
          <?php print $texto_sitio["Asignar Sensor"]; ?>
        </button>

        <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">
          <?php print $texto_sitio["Cerrar"]; ?>
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  var old_sensor=0, new_sensor=0;
  var neum_list=undefined, sens_list=undefined;
  var fecha_dinst_sensor, fecha_inst_sensor;

  $(document).ready(function(){
    // $("input[name=motivo]").controlgroup();

    $("#contenedor-neumaticos").load('ajax/asignar-sensores/obtener.php', function(){
      neum_list = $("#contenedor-neumaticos > div.neum-big.neumatico");
    });

    $('.rangepicker').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      autoApply: true,
      startDate: '<?=date("d/m/Y H:i")?>',
      timePicker: true,
      timePicker24Hour: true,
      maxDate: new Date,
      locale: {
        "format": "DD/MM/YYYY H:mm",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Custom",
        "weekLabel": "S",
        "daysOfWeek": [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
        "monthNames": [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        "firstDay": 1
      }
    });

    $("#modal-view-sensor")
    .on("show.bs.modal", function(evt){
      $("#seleccion_sensores").css('display','none');
      $("#sensor_actual").css('display','block');
      $("#btn_retirar_sensor").show();
      $("#btn_cambiar_sensor").text('Cambiar Sensor');
      $("#sensor-filter").val('');
      sens_list.show();

      var objeto = $(evt.relatedTarget);

      var neumat_id  = objeto.attr("n-id");
      var sensor_id  = objeto.attr("s-id");

      $("#hid_modal_neu").val();
      $("#neum_num").text('');
      $("#neum_com").text('');
      $("#neum_bra").text('');
      $("#neum_mod").text('');
      $("#neum_sta").text('');
      $("#neum_img").css("background", "url('assets/img/neumaticos/acme.png')");

      $("#hid_modal_sns").val('');
      $("#sens_cod").text('');
      $("#sens_typ").text('');
      $("#sens_cod").show('fast');
      $("#sens_typ").show('fast');
      $("#sens_img").css("background", "url('assets/img/sensor_.png')");

      var modo = null;
      if(!isNaN(neumat_id) && !isNaN(sensor_id)) modo = null;
      else if(isNaN(sensor_id)) modo = 'neumatico';      

      $.post('ajax/asignar-sensores/obtener-info.php', {modo: modo, nid: neumat_id, sid: sensor_id}, function(data){
        if(data.type=='success'){
          if(data.neumatico){
            $("#neum_id").val(data.neumatico.ID_NEUMATICO);

            $("#neum_num").text(data.neumatico.<?= ($verNeumaticoPor=='fuego' ? 'NUMEROFUEGO' : 'NUMIDENTI') ?>);
            $("#neum_com").text(data.neumatico.COMPUESTO);
            $("#neum_bra").text(data.neumatico.MARCA);
            $("#neum_mod").text(data.neumatico.MODELO);
            $("#neum_sta").text(data.neumatico.ESTADO);

            var neum = 'acme';
            if(data.neumatico.MARCA!='' && data.neumatico.MODELO!='') neum = data.neumatico.MARCA + '-' + data.neumatico.MODELO;
            $("#neum_img").css("background", "url('assets/img/neumaticos/"+neum.toLowerCase()+".png')");
            $.get("assets/img/neumaticos/"+neum.toLowerCase()+".png")
            .fail(function(){
              $("#neum_img").css("background", "url('assets/img/neumaticos/acme.png')");
            });
          }

          $("#new_sensor_id").val(0);

          if(data.sensor){
            $("#old_sensor_id").val(data.sensor.ID_SENSOR);
            $("#sens_cod").text(data.sensor.CODSENSOR);
            $("#sens_typ").text(data.sensor.TIPO);
            $("#sens_img").css("background", "url('assets/img/sensor_"+data.sensor.TIPO.toLowerCase()+".png')"); 
            $("#btn_asignar_sensor").addClass('hidden');

            if(objeto.data("estado")=='USO'){
              if(data.sensor.TIPO == 'Interno'){
                $("#mensaje-sensor").show();
                $("#btn_retirar_sensor").hide();
                $("#btn_cambiar_sensor").hide();
              }
              else{
                $("#btn_retirar_sensor").show();
                $("#btn_cambiar_sensor").show();
                $("#mensaje-sensor").hide();
              }
            }
            else{
              $("#btn_retirar_sensor").show();
              $("#btn_cambiar_sensor").show();
              $("#mensaje-sensor").hide();
            }
          }
          else{
            $("#seleccion_sensores").css('display','block');
            $("#sensor_actual").css('display','none');
            $("#btn_retirar_sensor").hide();
            $("#btn_cambiar_sensor").text('Asignar Sensor');
            $("#btn_asignar_sensor").removeClass('hidden');
            $("#old_sensor_id").val(0);
          }
        }
        else{
          swal(data);
          $("#modal-view-sensor").modal('toggle');
          $("#new_sensor_id").val(0);
          $("#old_sensor_id").val(0);
          $("#neum_id").val(0);
        }
      });
    })
    .on("hidden.bs.modal",function(evt){
      $("#btn_guardar_cambios").addClass('hidden');

      //$("#fecha_dinst_sensor").val('');
      //$("#fecha_inst_sensor").val('');

      $("#hid_modal_neu").val();
      $("#neum_num").text('');
      $("#neum_com").text('');
      $("#neum_bra").text('');
      $("#neum_mod").text('');
      $("#neum_sta").text('');
      $("#neum_img").css("background", "url('assets/img/neumaticos/acme.png')");

      $("#hid_modal_sns").val('');
      $("#sens_cod").text('');
      $("#sens_typ").text('');
      $("#sens_cod").show('fast');
      $("#sens_typ").show('fast');
      $("#sens_img").css("background", "url('assets/img/sensor_.png')");

      $("#motivos_baja").addClass("hidden");
      $("#retiro_sensor").addClass('hidden');
      $("#sensor_actual").removeClass('hidden');
      $("#seleccion_fecha_inst").css('display','none');
      $("input:radio[name=motivo]:checked").prop("checked", false);
    });

    // Modal Actions
    $(".modal")
    .on("click", ".sensor-cell-selector", function(){
      var this_sns = $(this).attr("s-id");

      // $("#hid_sensor_id").val(this_sns);
      $("#btn_asignar_sensor").prop("disabled", false);
      $("#seleccion_fecha_inst").css('display','none');
      $(".sensor-cell-selector.selected").removeClass("selected");
      $(this).addClass("selected");
      new_sensor = this_sns;
      $("#new_sensor_id").val(this_sns);
    })
    .on("click", "#btn_asignar_sensor", function(){
      new_sensor = $("#new_sensor_id").val();

      $.post('ajax/asignar-sensores/obtener-info.php', { modo: 'sensor', id: new_sensor }, function(data){
        if(data.type=='success'){
          $("#hid_modal_sns").val(data.sensor.ID_SENSOR);
          $("#sens_cod").text(data.sensor.CODSENSOR);
          $("#sens_typ").text(data.sensor.TIPO);
          $("#sens_img").css("background", "url('assets/img/sensor_"+data.sensor.TIPO.toLowerCase()+".png')");

          $("#seleccion_sensores").css('display','none');
          // $("#sensor_actual").css('display','block');
          $("#seleccion_fecha_inst").css('display','block');
          $("#btn_asignar_sensor").addClass('hidden');
          $("#btn_guardar_cambios").removeClass('hidden');
          $("#mensaje-sensor").hide();

          $("#btn_retirar_sensor").show();
          $("#btn_cambiar_sensor").show();
          $("#btn_cambiar_sensor").text('Cambiar Sensor');

          $("#sens_cod").show('fast');
          $("#sens_typ").show('fast');
        }
        else swal(data);
      });
    })
    .on("click", "#btn_cambiar_sensor", function(){      
      $("#sensor_actual").slideUp('fast', function(){
        $("#seleccion_sensores").slideDown('fast', function(){
          $("#seleccion_sensores").show();
          $("#btn_asignar_sensor").removeClass('hidden');
          $("#btn_guardar_cambios").addClass('hidden');
        });
      });
    })
    .on("click", "#btn_retirar_sensor", function(){
      $("#hid_modal_sns").val('');
      $("#sens_cod").hide('fast');
      $("#sens_typ").hide('fast');
      $("#sens_img").css("background", "none");
      $("#btn_guardar_cambios").removeClass('hidden');
      // $("#btn_cambiar_sensor").text('Asignar Sensor');
      // $("#btn_retirar_sensor").hide();
      new_sensor = 0;
      $("#new_sensor_id").val(0);

      $("#retiro_sensor").removeClass('hidden');
      $("#sensor_actual").hide();
    })
    .on("click", "#btn_baja_sensor", function(){
      $("#motivos_baja").removeClass('hidden');
    })
    .on("click", "#btn_stock_sensor", function(){
      $("#motivos_baja").addClass("hidden");
    })
    .on("click", "#btn_guardar_cambios", function(){
      $("#seleccion_fecha_inst").css('display','none');
      new_sensor = $("#new_sensor_id").val();
      old_sensor = $("#old_sensor_id").val();
      let fecha_instalacion = moment($("#fecha_inst_sensor").val(), "DD/MM/YYYY H:mm").format("YYYY-MM-DD H:mm");
      let fecha_desinstalacion = moment($("#fecha_dinst_sensor").val(), "DD/MM/YYYY H:mm").format("YYYY-MM-DD H:mm");

      if(new_sensor==old_sensor){
        swal({
          title: 'No hay cambios que guardar.',
          text: 'No se han detectado cambios que guardar, por lo que no se realizará la operación.',
          type: 'warning'
        });
        $("#modal-view-sensor").modal('hide');        
      }
      else{
        var neum_id = $("#neum_id").val();
        if(!isNaN(neum_id)){          
          var params;
          if($("#motivos_baja").hasClass("hidden"))
            params = { old_sensor: old_sensor, new_sensor: new_sensor, neum_id: neum_id, finst: fecha_instalacion, fdinst: fecha_desinstalacion };
          else{
            //Si el panel de motivos de baja está visible se valida que haya seleccionado un valor y luego
            //agrega el valor seleccionado a los parámetros que serán enviados en la consulta
            var motivo_baja = $("input:radio[name=motivo]:checked").val();
            if(isNaN(motivo_baja)) swal('Faltan datos', 'Debe especificar un motivo de la baja.', 'error');
            else params = { old_sensor: old_sensor, new_sensor: new_sensor, neum_id: neum_id, motivo_baja: motivo_baja };
          }
          console.log(params);

          if(params){
            $.post('ajax/asignar-sensores/guardar.php', 
              params, function(data){
              swal(data);
              if(data.type=='success'){
                $("#modal-view-sensor").modal('hide');
                $("#seleccion_fecha_inst").css('display','none');
                //refrescar contenedor de neumáticos y sensores
                $("#contenedor-neumaticos").load('ajax/asignar-sensores/obtener.php', function(){
                  neum_list = $("#contenedor-neumaticos > div.neum-big.neumatico");
                });
                $("#sensor-grid").html('');
                $("#sensor-grid").load('ajax/asignar-sensores/obtener-disponibles.php', function(){
                  sens_list = $('#sensor-grid > div');
                });
              }
            });
          }
        }
      }

      if(params){
        $("#motivos_baja").addClass("hidden");
        $("#retiro_sensor").addClass('hidden');
        $("#sensor_actual").removeClass('hidden');
        $("input:radio[name=motivo]:checked").prop("checked", false);
      }
    });

    $("#habilitar_mod").click(function(){
      $("#btn_retirar_sensor").show();
      $("#btn_cambiar_sensor").show();
      $("#mensaje-sensor").slideUp('fast');
    });

    $("#tire-filter").keyup(function() {
      var this_val  = $.trim($(this).val()).toLowerCase();
        neum_list.show().filter(function() {
            var text = $(this).attr("n-string").replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(this_val);
        }).hide();
    });
    
    sens_list = $("#sensor-grid > div");
    $("#sensor-filter").keyup(function() {
      var this_sval  = $.trim($(this).val()).toLowerCase();
        sens_list.show().filter(function() {
            var text = $(this).attr("s-string").replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(this_sval);
        }).hide();
    });

    var h = <?= $maxContentHeight!=NULL?$maxContentHeight-128:'window.screen.availHeight - 290'?>;
    $(window).on('resize', function(){
      // $("#map").css("height", (h)+'px');
      $("#contenedor-neumaticos").css("height", (h)+'px');
    });
    // $("#map").css("height", (h)+'px');
    $("#contenedor-neumaticos").css("height", (h)+'px');
  });
</script>