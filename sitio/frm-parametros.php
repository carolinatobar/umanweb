<?php
  require 'autoload.php';
  
  $acc = new Acceso();

  $obj_gnr  = new General();
  // $arr_par  = $obj_gnr->listar_parametros();

  $gen = new General();
  $verNeumaticoPor   = $gen->getParamValue('verneumaticosegun', 'id');
  $esquema_monitoreo = $gen->getParamValue('tipoesquemamonitoreo', 'universal');
  $atm               = $gen->getParamValue('atm', 14);
  $refresco          = $gen->getParamValue('refresco', 30);
  $sampleogps        = $gen->getParamValue('sampleogps', 6);
  $timeout           = $gen->getParamValue('timeout', 30);
  $maxvelocidad      = $gen->getParamValue('maxvelocidad', 35);
  $map_api           = $gen->getParamValue('mapapi', 'google');
  $zoom              = $gen->getParamValue('zoom', 13);
  $pre_alarma        = $gen->getParamValue('pre_alarma', 1);
  $mostrar_con_fecha = $gen->getParamValue('mostrar_fecha_evento', 1);
  $oeem2dceet        = $gen->getParamValue('oeem2dceet', 0); //Ocultar Equipos En Mapa 2D Cuando Están En Timeout
  $mostrar_coordenadasalarma = $gen->getParamValue('mostrar_coordenadasalarma', 0);
  $maxgps = $gen->getParamValue('maxgps', 1);
  // $tiempo_falla      = $arr_par[0]->TIEMPOFALLA;
  // $temperatura       = $arr_par[0]->DESVIOTEMP;
  // $presion_alta      = $arr_par[0]->DESVIOPRESMAX;
  // $presion_baja      = $arr_par[0]->DESVIOPRESMIN;
  // $presion_minima    = $arr_par[0]->PRESIONMINIMA;

  //Obtener coordenadas de la faena
  $sql = "SELECT * FROM uman_ultimogps LIMIT 1;";
  $db  = DB::getInstance();

  $res = $db->query($sql);
  $LAT = 0;
  $LNG = 0;
  if($res->count() > 0){
    $res = $res->results()[0];
    $LAT = $res->X;
    $LNG = $res->Y;
  }

  $TITULO    = $module_label; //$texto_sitio["Nomenclatura Posiciones"];
  $SUBTITULO = '';
?>

<link rel="stylesheet" href="assets/css/uman/frm-parametros.css">

<div class="cc-divider">
  <span class="titulo-pagina"><?=$TITULO?></span>
  <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
</div>

<form>
  <div class="row">
    <!-- PRESIÓN ATMOSFÉRICA -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b><?php print $texto_sitio["Presion Atmosferica Actual"]; ?></b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon2"><?php print $texto_sitio["ATM Actual"]; ?></span>
              <input type="number" name="atm" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$atm;?>" autocomplete="off">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- REFRESCO MONITOREO -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b><?php print $texto_sitio["Refresco de pantalla de monitoreo"]; ?></b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" name="refresco" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$refresco;?>" autocomplete="off" min="10" max="300">
              <span class="input-group-addon" id="basic-addon2"><?php print $texto_sitio["Segundos"]; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- REFRESCO GPS -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Sampleo GPS</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" class="form-control text-center" aria-describedby="basic-addon2" name="sampleogps" value="<?=$sampleogps;?>" autocomplete="off" min="0" max="60">
              <span class="input-group-addon" id="basic-addon2"><?php print $texto_sitio["Segundos"]; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TIMEOUT SENSORES -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Timeout sensores</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" name="timeout" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$timeout;?>" autocomplete="off" min="1" max="360">
              <span class="input-group-addon" id="basic-addon2"><?php print $texto_sitio["Minutos"]; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- VELOCIDAD MÁXIMA -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Velocidad máxima</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" name="maxvelocidad" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$maxvelocidad;?>" autocomplete="off" min="1" max="150">
              <span class="input-group-addon" id="basic-addon2">KM/H</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- VER NEUMÁTICO POR ID O NÚMERO DE FUEGO -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Ver neumático según</b></div>
        <div class="panel-body">
          <div class="form-group">
            <select class="form-control selectpicker" name="verneumaticosegun" data-width="100%">
              <option value="id" <?=($verNeumaticoPor=='id'?'selected':'')?>>Número de Serie</option>
              <option value="fuego" <?=($verNeumaticoPor=='fuego'?'selected':'')?>>Número de Fuego</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- VISUALIZAIÓN PRE-ALARMA A.K.A ALARMA ÁMBAR -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Visualización de Pre Alarma</b></div>
        <div class="panel-body">
          <div class="form-group">
            <select class="form-control selectpicker" name="pre_alarma" data-width="100%">
              <option value="1" <?=($pre_alarma=='1'?'selected':'')?>>Mostrar</option>
              <option value="0" <?=($pre_alarma=='0'?'selected':'')?>>Ocultar</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- VISUALIZAIÓN DE EQUIPOS EN MAPA 2D CUANTO ESTÁN EN TIMEOUT -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Ocultar equipos en mapa 2D cuando están sin conexión</b></div>
        <div class="panel-body">
          <div class="form-group">
            <select class="form-control selectpicker" name="oeem2dceet" data-width="100%">
              <option value="1" <?=($oeem2dceet=='1'?'selected':'')?>>Ocultar</option>
              <option value="0" <?=($oeem2dceet=='0'?'selected':'')?>>Mostrar</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ESQUEMA DE MONITOREO -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Esquema en monitoreo</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-default <?=($esquema_monitoreo=='universal' ? 'active':'')?> universal">
                <input type="radio" name="tipoesquemamonitoreo" id="option2" autocomplete="off" <?=($esquema_monitoreo=='universal' ? 'checked':'')?> value="universal">
                <span class="glyphicon glyphicon-ok"></span>
              </label>

              <label class="btn btn-default <?=($esquema_monitoreo=='con-equipo' ? 'active':'')?> con-equipo">
                <input type="radio" name="tipoesquemamonitoreo" id="option1" autocomplete="off" <?=($esquema_monitoreo=='con-equipo' ? 'checked':'')?> value="con-equipo">
                <span class="glyphicon glyphicon-ok"></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ESQUEMA DE INFORMACIÓN DE TEMPERATURA PRESIÓN -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Cuadro de Temperatura Presión</b></div>
        <div class="panel-body">
          <div class="form-group center-block">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-default <?=($mostrar_con_fecha=='1' ? 'active':'')?> con_fecha">
                <input type="radio" name="mostrar_fecha_evento" id="option2" autocomplete="off" <?=($mostrar_con_fecha=='1' ? 'checked':'')?> value="1">
                <span class="glyphicon glyphicon-ok"></span>
              </label>

              <label class="btn btn-default <?=($mostrar_con_fecha=='0' ? 'active':'')?> sin_fecha">
                <input type="radio" name="mostrar_fecha_evento" id="option1" autocomplete="off" <?=($mostrar_con_fecha=='0' ? 'checked':'')?> value="0">
                <span class="glyphicon glyphicon-ok"></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- API DE VISUALIZACIÓN DE MAPAS -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Visuaización de mapas 2D</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-default <?=($map_api=='google' ? 'active':'')?> google-api" data-title="Google" data-toggle="tooltip">
                <input type="radio" name="mapapi" id="option1" autocomplete="off" <?=($map_api=='google' ? 'checked':'')?> value="google">
                <span class="glyphicon glyphicon-ok"></span>
              </label>

              <label class="btn btn-default <?=($map_api=='here' ? 'active':'')?> here-api" data-title="Here" data-toggle="tooltip">
                <input type="radio" name="mapapi" id="option2" autocomplete="off" <?=($map_api=='here' ? 'checked':'')?> value="here">
                <span class="glyphicon glyphicon-ok"></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ZOOM DE MAPAS -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Zoom mapas 2D</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon2">Zoom</span>
              <input type="number" name="zoom" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$zoom;?>" oninput="cambiar_zoom(this)" autocomplete="off" min="10" max="19">
            </div>
            <div id="zoom_map" class="<?=Core::col(12)?>" style="height:150px"></div>
            <script type="text/javascript">
              <?php if($map_api == 'here'){ ?>
                var platform = new H.service.Platform({
                  'app_id': '<?=$GLOBALS['HERE']['ID']?>',
                  'app_code': '<?=$GLOBALS['HERE']['CODE']?>'
                });
                var defaultLayers = platform.createDefaultLayers();
                var map = new H.Map(
                  document.getElementById('zoom_map'),
                  defaultLayers.satellite.map, {
                    zoom: <?=$zoom?>,
                    center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
                    fixedCenter: false,
                  });
                var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));                

                var ui = H.ui.UI.createDefault(map, defaultLayers);
                ui.removeControl('zoom');
                ui.removeControl('mapsettings');
                ui.removeControl('panorama');
                ui.removeControl('scalebar');

              <?php } else if($map_api == 'google'){ ?>
                var map = new google.maps.Map(document.getElementById('zoom_map'), {
                  zoom: <?=$zoom?>,
                  center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
                  mapTypeId: "satellite",
                  fullscreenControl: false
                });
              <?php } ?>
            </script>
          </div>
        </div>
      </div>
    </div>

    <!-- MOSTRAR X POSICIONES DE GPS PREVIOS Y POSTERIOR A LA ALARMA -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Coordenadas previas y posteriores a la alarma </b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" name="mostrar_coordenadasalarma" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$mostrar_coordenadasalarma;?>" autocomplete="off" min="1" max="150">
              <span class="input-group-addon" id="basic-addon2">Coordenadas</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TIEMPO MAXIMO ENTRE ALERTA Y POSICION DE GPS -->
    <div class="<?=Core::col(4,4,4,12)?>">
      <div class="panel panel-default">
        <div class="panel-heading"><b>Tiempo máximo de diferencia entre alerta y coordenadas</b></div>
        <div class="panel-body">
          <div class="form-group">
            <div class="input-group">
              <input type="number" name="maxgps" class="form-control text-center" aria-describedby="basic-addon2" value="<?=$maxgps;?>" autocomplete="off" min="1" max="150">
              <span class="input-group-addon" id="basic-addon2">Minutos</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
  <button class="btn btn-primary" type="submit"><i class="fa fa-save" aria-hidden="true"></i> Guardar</button>
</form>

<script>
  $(document).ready(function(){

    $("form").submit(function(event){
      event.preventDefault();
      // var form  = $(this).attr("name");
      // var input = $(this).find("input");
      var params = $("form").serializeArray();


      // if(input.length==0){
      //   input = $(this).find("select").val();
      // }
      // else{
      //   if(input[0].type=='radio') input = $(this).find(":checked").val();
      //   else input = $(this).find("input").val();
      // }    

      $.post('./ajax/ajax_actualizar_parametros.php', params, function(data){
        swal(data);
      });
    });

  });

  function cambiar_zoom(o){
    map.setZoom(parseInt(o.value));
  }
</script>
