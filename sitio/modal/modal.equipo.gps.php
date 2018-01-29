<?php
 require '../autoload.php';

 $ee = new EstadoEquipo();

 $db = DB::getInstance();
 #region obtener parámetros
  $p = new General();
  $GLOBALS['pre_alarma']           = $p->getParamValue('pre_alarma');
  $GLOBALS['mostrar_fecha_evento'] = $p->getParamValue('mostrar_fecha_evento');
  $GLOBALS['unidad_temperatura']   = $p->getParamValue('unidad_temperatura', 'celsius');
  $GLOBALS['unidad_presion']       = $p->getParamValue('unidad_presion', 'psi');
  // print_r($GLOBALS);
 #end region

  $sql = "SELECT * FROM uman_tipo_reconocimiento";
 $reconocimiento = $db->query($sql);
 $opt_reconocimiento = '<option>Seleccione una opción</option>';
 if($reconocimiento->count() > 0){
   foreach($reconocimiento->results() as $r){
    $opt_reconocimiento .= '<option value='.$r->id.'>'.utf8_encode($r->descripcion).'</option>';
   }
 }
  
 $equipo = $_REQUEST['equipo'];
 //  print_r($_GET);
  
 session_start();
 $sess_id = session_id();
 $acceso = new Acceso($_SESSION, session_id());
 if(!$acceso->Permitido()) exit();

 //Rescatar el Menu
 $s = isset( $_GET['s'] )? $_GET['s'] : NULL;

 $perfil       = $_SESSION[$sess_id]['perfil'];
 $perfilactivo = $_SESSION[$sess_id]['perfilactivo'];
 $idioma       = $_SESSION[$sess_id]['lang'];
 $faena        = $_SESSION[$sess_id]['faena'];
 $nombrefaena  = $_SESSION[$sess_id]['nombrefaena'];

 //  error_reporting(0);
 //  date_default_timezone_set("Chile/Continental");
?>
<style media="screen">
  <?php include_once("../assets/css/detalle-equipo.css") ?> 
  span.t{
    width:60%;
    text-align: left;
  }
  span.s{
    width:40%;
    text-align:left;
    font-weight:bold;
  }
  .sweet-alert{
    width:50% !important;
    left:45% !important;
  }
  .rosa{
    background-color: #337ab7;
    background: url(assets/img/ubicar.png);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
  }
</style>

<?php
  $sql = "SELECT * 
  FROM uman_camion INNER JOIN uman_tipo_equipo ON ID=tipo 
  WHERE ID_CAMION='$equipo' LIMIT 1";
  // echo $sql;
  $consulta       = $db->query($sql);
  $datos          = ($consulta->count()>0)?$consulta->results()[0]:new ArrayObject();
  // var_dump($datos);

  $id_camion      = $datos->ID_CAMION;
  $numcamion      = $datos->NUMCAMION;
  $neumaticos     = explode(',',$datos->NEUMATICOS);
  $numneumaticos  = 0;
  $id_cajauman    = $datos->ID_CAJAUMAN;
  $tipo           = $datos->tipo;
  $esquema        = $datos->IMG_ESQUEMA;
  $img_class      = $datos->CLASS_IMG;

  foreach($neumaticos as $ne) $numneumaticos+=$ne;

  $color = $ee->estatusPosiciones($id_camion,'green');
  $colores = $ee->estatusPosiciones($id_camion);
  $datos_eventos = $ee->datosEventos();
  $GLOBALS['datos_eventos'] = $datos_eventos;
  // var_dump($color);

  $sql = "SELECT * 
  FROM uman_neumatico_camion 
  WHERE ID_EQUIPO='$id_camion'";
  // echo $sql;

  $rel_neu_cam = $db->query($sql);
  // print_r($rel_neu_cam->results());

  foreach ( $rel_neu_cam->results() as $dnc ) {
    $datos_neum       = $db->query("SELECT * FROM uman_neumaticos WHERE ID_NEUMATICO='$dnc->ID_NEUMATICO'");
    $info_sensores        = $datos_neum->results();
    // print_r($datos_neum->results());

    if ( $datos_neum->count()>0 ) {
      $info_neum = $datos_neum->results()[0];
      $data_sensores        = $db->query("SELECT * FROM uman_sensores WHERE ID_SENSOR='$info_neum->ID_SENSOR'");
      $infox_sensores       = $data_sensores->results()[0];
      $sensor[$dnc->ID_POSICION]['SENSOR']        = $info_neum->ID_SENSOR;
      $sensor[$dnc->ID_POSICION]['COD']           = $infox_sensores->CODSENSOR;
      $sensor[$dnc->ID_POSICION]['TIPO']          = $infox_sensores->TIPO;
      $sensor[$dnc->ID_POSICION]['INST_SENSOR']   = date('d/m/Y',strtotime($infox_sensores->FECHA_INGRESO));
      $sensor[$dnc->ID_POSICION]['INST_NEU']      = date('d/m/Y',strtotime($dnc->FECHA));
      $sensor[$dnc->ID_POSICION]['MARCA']         = $info_neum->MARCA;
      $sensor[$dnc->ID_POSICION]['NUMIDENTI']     = $info_neum->NUMIDENTI;
      $sensor[$dnc->ID_POSICION]['NUMEROFUEGO']   = ($info_neum->NUMEROFUEGO!='')?$info_neum->NUMEROFUEGO:'&nbsp;';
      $sensor[$dnc->ID_POSICION]['MODELO']        = $info_neum->MODELO;
      $sensor[$dnc->ID_POSICION]['COMPUESTO']     = $info_neum->COMPUESTO;

      $id_plantilla           = $info_neum->ID_PLANTILLA;

      $data_plantilla         = $db->query("SELECT PIF FROM uman_plantilla WHERE ID_PLANTILLA='$id_plantilla'");
      $info_plantilla         = $data_plantilla->results()[0];

      $pif                    = $info_plantilla->PIF;
      $temp_k                 = $datos_eventos[$dnc->ID_POSICION]['eventotemperatura'] + 273.15;
      $ratio                  = $temp_k / ( 18 + 273.15 );
      $pif_[$dnc->ID_POSICION] = $pif;
      $recomendada[$dnc->ID_POSICION] = round( $pif * $ratio );

    }
  }

  $consulta_nomen = $db->query("SELECT * FROM uman_posicion");
  foreach ( $consulta_nomen->results() as $datos_nom ) {
    $valor_rueda[$datos_nom->POSICION] = $datos_nom->NOMENCLATURA;
  }

  $ruedas = array_fill(0, 17, "");

  $j = 1;
  for ( $i = 1 ; $i <= 16 ; $i++ ) {
    if ( $neumaticos[$i-1] == "1" ) {
      $ruedas[$i] = Core::dibuja_caja($j, $sensor[$j]['TIPO'], $datos_eventos[$j]['eventotemperatura'], $datos_eventos[$j]['eventopresion'], $recomendada[$j], $color[$j], $datos_eventos[$j][0]);
      $j++;
    }
  }
  // print_r($ruedas);
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <div class="modal-title" id="title-detalle">
    <div class="modal-title-i icono-x48 <?php echo $img_class ?> pull-left"></div>
    <div class="modal-title-1">EQUIPO <strong><?php echo $numcamion; ?></strong></div>
    <div class="modal-title-2">Faena <?php print $nombrefaena; ?></div>
  </div>
</div>
<div class="modal-body" id="body-detalle">
  <div id="modal-loader" class="loader hidden" style="top: calc( 50% - 60px );"></div>
  <div id="detalle_equipo" >
   <div class="row">
    
    <!-- ESQUEMA -->
    <div class="<?php echo Core::absolute_col_x(5) ?> hidden-xs" style="max-height:480px;">
      <?php
        $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
        $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
        $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
        $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

        $attr = array(); $attr2 = array();
        if($eje1==2){
          $attr['neum'][1] = 'left: 133px !important;';
          $attr['neum'][2] = 'left: 299px !important;';
          
          $attr2[1]['style'] = 'left: -58px; top: -380px;';
          $attr2[2]['style'] = 'left: 74px; top: -431px;';
        }else{
          $attr['neum'][1] = 'left: 133px !important;';
          $attr['neum'][2] = 'left: 171px !important;';
          $attr['neum'][3] = 'left: 299px !important;';
          $attr['neum'][4] = 'left: 345px !important;';

          $attr2[3]['style'] = 'left: -80px; top: -293px;';
          $attr2[4]['style'] = 'left: -41px; top: -342px;';
          $attr2[5]['style'] = 'left: 57px; top: -392px;';
          $attr2[6]['style'] = 'left: 98px; top: -442px;';
        }

        if($eje2==2){
          $attr['neum'][3] = 'left: 133px !important;';
          $attr['neum'][4] = 'left: 299px !important;';
          
          $attr2[3]['style'] = 'left: -58px; top: -294px;';
          $attr2[4]['style'] = 'left: 74px; top: -344px;';
        }else{
          $attr['neum'][3] = 'left: 106px !important;';
          $attr['neum'][4] = 'left: 156px !important;';
          $attr['neum'][5] = 'left: 280px !important;';
          $attr['neum'][6] = 'left: 330px !important;';

          $attr2[3]['style'] = 'left: -80px; top: -293px;';
          $attr2[4]['style'] = 'left: -41px; top: -342px;';
          $attr2[5]['style'] = 'left: 57px; top: -392px;';
          $attr2[6]['style'] = 'left: 98px; top: -442px;';
        }
        
        $attr['showPopover'] = false;
        $attr['neum-drag-n-drop'] = true;
        $attr['container']['class'] = 'scaleDown80';
        $attr['container']['style'] = 'min-width: 480px; max-width:480px; margin-left: -84px !important;';
        $attr['showNomenclatura'] = true;
        $attr['nomenclatura']['class'] = 'bottom';

        $btn_rec_alarma = '';
        $sql = "SELECT * 
        FROM uman_alarmas 
        WHERE ALARMAESTADO='0' AND ALARMATIPO!='16' AND ALARMATIPO!='8' AND ALARMANUMCAMION=$id_camion;";
        $data_alarmas = $db->query($sql);
        $info_alarmas = $data_alarmas->results();
        if ( $data_alarmas->count()>0) {
          switch($_SESSION[session_id()]['perfilactivo']->id){
            case 3:
            case 7:
              $mostrar = true;
              break;
          }
          if($mostrar === true){
            $btn_rec_alarma = '
              <div id="boton-reconocer-alarma" data-toggle="tooltip" 
                title="Reconocer Alarma" 
                class="scaleDown80" style="margin-left: 40px; top: 80px; left: auto; position: relative;">
                <img src="./assets/img/btn_alarma1.png" />
                <img src="./assets/img/btn_alarma2.png" />
              </div>';
          }
        }

        echo Core::dibuja_esquema_equipo($neumaticos, $colores, $ruedas, $attr, $btn_rec_alarma);
        // echo Core::dibuja_posiciones($numneumaticos,$attr2);
        ?>
    </div>

    <!-- DATOS EN TIEMPO REAL -->
    <div class="<?php echo Core::absolute_col_x(7,7,12,12) ?> real-time-data">
     <div class="row">
      
      <div class="<?php echo Core::absolute_col_x(12) ?> bg-primary" style="">
        <center><span id="fecha-gps"></span></center>
      </div>

      <!-- ORIENTACIÓN -->
      <div class="<?php echo Core::absolute_col_x(4) ?> bg-primary" style="height:150px;">
        <h4 class="rosa">
          <center>
             <!-- <span> Orientación </span> -->
             <br/> 
            <img id="orientacion" src="assets/img/gps/blank.png" style="width:100px; height:100px;" class="center-block" />
          </center>
        </h4>
      </div>

      <!-- RAPIDEZ -->
      <div class="<?php echo Core::absolute_col_x(4) ?> bg-primary" style="height:150px;">
        <h4>
          <center>
            <span> Velocidad </span><br/>
            <strong>
              <span id="rapidez" class="value">0</span>
              <span> Km/h.</span>
            </strong>
          </center>
        </h4>
         <br/> 
        <h4>
          <center>
            <span> Coordenadas </span><br/>
            <strong>
              <span id="latitud" class="value">0</span>
              <span id="longitud" class="value">0</span>
            </strong>
          </center>
        </h4>
      </div>

      <!-- ALTURA -->
      <div class="<?php echo Core::absolute_col_x(4) ?> bg-primary" style="height:150px;">
        <h4>
          <center>
            <span> Altura </span><br/>
            <strong>
              <span id="altura" class="value">0</span>
              <span> msnm.</span>
            </strong>
          </center>
        </h4>
        <br/>
        <h4>
          <center>
            <span> Pendiente </span><br/>
            <strong>
              <span id="pendiente" class="value">0</span>
            </strong>
          </center>
        </h4>
      </div>

      <!-- ESPACIADOR -->
      <div class="<?php echo Core::absolute_col_x(12) ?>">&nbsp;</div>
      <div class="clearfix visible-xs-block"></div>

      <!-- TABLA DATOS -->
      <div class="<?php echo Core::absolute_col_x(12) ?>" style="padding: 0 !important;">
        <table class="table" id="datos">
          <thead>
            <tr class="bg-primary">
              <th style="vertical-align: middle;"><center>Sensor</center></th>
              <th style="vertical-align: middle;"><center>Pos</center></th>
              <th style="width: 60px; vertical-align: middle;"><center>T&deg;</center></th>
              <th style="vertical-align: middle;"><center>P&deg; Actual / P&deg; Recomend.</center></th>
              <th style="vertical-align: middle;"><center>Fecha</center></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
       
     </div>
    </div>
   </div>
  </div>
  
  <div id="detalle_alarmas" style="display:none"></div>

  <div id="detalle_recorrido" style="display:none"></div>

  <div id="detalle_grafico" style="display:none">
    <div id="container" style="min-width: 310px; height: 100%; margin: 0 auto"></div>    
  </div>
</div>
<div class="modal-footer" id="footer-detalle">
  <div class="btn-group pull-left hidden-xs" role="group" aria-label="...">
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerDetalle">Ver detalle equipo</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerAlarmas">Ver alarmas</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerRecorrido">Ver recorrido</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerGrafico">Grafico según posiciones</button>
  </div>
  <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Cerrar</button>
</div>

<?php
 $i=1;
 if(count($sensor)>0){
  foreach($sensor as $s){  
?>
<div id="detalleneum<?php echo $i ?>" style="display:none">
 <div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
   <h4 class="center-block">
    <span class="t">POSICIÓN</span>
    <span class="s">: <?php echo $valor_rueda[$i]; ?></span>
   </h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6">
    <h5>
     <span class="pull-left t">ID NEUMÁTICO</span>
     <span class="pull-left s">: <?php echo $s['NUMIDENTI']; ?></span>
    </h5>
    <h5>
     <span class="pull-left t">NÚMERO DE FUEGO</span>
     <span class="pull-left s">: <?php echo $s['NUMEROFUEGO']; ?></span>
    </h5>
    <h5>
     <span class="pull-left t">MARCA</span>
     <span class="pull-left s">: <?php echo $s['MARCA']; ?></span>
    </h5>
    <h5>
     <span class="pull-left t">MODELO</span>
     <span class="pull-left s">: <?php echo $s['MODELO']; ?></span>
    </h5>
    <h5>
     <span class="pull-left t">COMPUESTO</span>
     <span class="pull-left s">: <?php echo $s['COMPUESTO']; ?></span>
    </h5>
    <h5>
     <span class="pull-left t">INSTALACIÓN NEUMÁTICO</span>
     <span class="pull-left s">: <?php echo $s['INST_NEU']; ?></span>
    </h5>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6">
   <h5>
    <span class="pull-left t">SENSOR</span>
    <span class="pull-left s">: <?php echo $s['COD']; ?></span>
   </h5>
   <h5>
    <span class="pull-left t">PIF</span>
    <span class="pull-left s">: <?php echo $pif_[$i]; ?> PSI</span>
   </h5>
   <h5>
    <span class="pull-left t">UMBRAL DE TEMPERATURA</span>
    <span class="pull-left s">: <?php echo $datos_eventos[$i]['tempmax']; ?>&deg;C</span>
   </h5>
   <h5>
    <span class="pull-left t">UMBRAL DE PRESIÓN MÁX.</span>
    <span class="pull-left s">: <?php echo $datos_eventos[$i]['presmax']; ?> PSI</span>
   </h5>
   <h5>
    <span class="pull-left t">UMBRAL DE PRESIÓN MÍN.</span>
    <span class="pull-left s">: <?php echo $datos_eventos[$i]['presmin']; ?> PSI</span>
   </h5>
   <h5>
    <span class="pull-left t">INSTALACIÓN SENSOR</span>
    <span class="pull-left s">: <?php echo $s['INST_SENSOR']; ?></span>
   </h5>
  </div>
</div>
<?php
   $i++;
  }
 }
?>

<script type="text/javascript">
  // let maxContentHeight = (parseInt($('#body-detalle').css("height").replace('px',''))-62);
 function mostrar(obj){
   $("#detalle_equipo").css("display", (obj == "#detalle_equipo")?'block':'none');
   $("#detalle_recorrido").css("display", (obj == "#detalle_recorrido")?'block':'none');
   $("#detalle_grafico").css("display", (obj == "#detalle_grafico")?'block':'none');
   $("#detalle_alarmas").css("display", (obj == "#detalle_alarmas")?'block':'none');
 }
 $(function(){
  // $('.popover').popover('show');
  $("#btnVerDetalle").click(function(){ mostrar("#detalle_equipo"); });
  
  $("#btnVerAlarmas").click(function(){
    $("#modal-loader").removeClass('hidden').addClass('show');
   $("#detalle_alarmas").load('data-reporte-alarmas.php',
    {equipo:'<?php echo $id_camion ?>',modo:'modal'}, 
    function(response, status, xhr){
      if ( status == "error" ) {
        mostrar("#detalle_equipo");
        swal('error','error','error');
      }
      else mostrar("#detalle_alarmas");
      $("#modal-loader").removeClass('show').addClass('hidden');
   });
  });

  $("#btnVerRecorrido").click(function(){
    $("#modal-loader").removeClass('hidden').addClass('show');
    
    $("#detalle_recorrido").load('data-grafico-gps.php',
      {equipo:'<?php echo $id_camion ?>', tiempo: 24, modo: 'modal', maxContentHeight: maxContentHeight}, 
      function(response, status, xhr){
        if ( status == "error" ) {
          mostrar("#detalle_equipo");
          swal('error','error','error');
        }
        else mostrar("#detalle_recorrido");
        $("#modal-loader").removeClass('show').addClass('hidden');
    });
  });

  $("#btnVerGrafico").click(function(){
    $("#modal-loader").removeClass('hidden').addClass('show');
   $.post('data-grafico-presion.php',
    {equipo:'<?php echo $id_camion ?>', maxContentHeight: maxContentHeight},
    function(data){
      mostrar("#detalle_grafico");
      $("#container").html(data);
      $("#modal-loader").removeClass('show').addClass('hidden');
   });
  });

  $("#boton-reconocer-alarma").click(function(){
    swal({
      title: "Reconocimiento de alarma",
      text: "Acción Tomada: <select class='form-control' id='opciones'><?= $opt_reconocimiento ?></select>",
      html: true,
      showCancelButton: true,
      closeOnConfirm: false,
      cancelButtonText: "Cancelar",
      confirmButtonText: "Reconocer",
      animation: "slide-from-top",
    },
    function(isConfirm){
      var selected = $("#opciones").val();      
      if(!isNaN(selected)){
        $.post("reconocer-alarmas.php", {equipo:'<?php echo $equipo; ?>', comentario: selected}, function(data){
          if(data.success){
            $("#boton-reconocer-alarma").hide();
            swal({
              title: 'Alarma Reconocida',
              text: data.msg,
              type: 'success'
            },
            function(){ $("#modal_equipo").modal('hide'); }
            );
          }
          else{
            swal('Alarma NO Reconocida',data.msg,'error');
          }
          
        });
      }
      else{
        swal.showInputError("Selecciona una opción");
      }
    });
  })

  $("div.tire-box").on("click", function(){
   var swal_content = $(this).data("target");
   swal_content = $(swal_content).html();

   swal({
     title: '<div class="bg-primary">Detalle de Neumático</div>',
     text: swal_content,
     html: true,
     confirmButtonColor: '#337ab7',
     confirmButtonText: 'Cerrar'
   })
  });

 });
</script>
