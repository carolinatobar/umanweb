<?php
 require '../autoload.php';
 
 error_reporting(E_ALL && ~ E_NOTICE);
 $ee = new EstadoEquipo();

 //  date_default_timezone_set("America/Santiago");

 $db = DB::getInstance();
 #region obtener parámetros
  $p = new General();
  $GLOBALS['pre_alarma']           = $p->getParamValue('pre_alarma', 1);
  $GLOBALS['mostrar_fecha_evento'] = $p->getParamValue('mostrar_fecha_evento', 0);
  $GLOBALS['unidad_temperatura']   = $p->getParamValue('unidad_temperatura', 'celsius');
  $GLOBALS['unidad_presion']       = $p->getParamValue('unidad_presion', 'psi');

  $nomenclatura                    = $p->getNomenclaturas();
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
 //print_r($_GET);
  
 @session_start();
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
  .bateria{
    height: 42px;
    width: 100%;
    font-size: 65% !important;
    text-align: center;
    margin-top: 28px; 
  }
  .pala_d{
    background: url(assets/img/pala_d.png);
    float: right;
    width: 50%;
  }
  .pala_i{
    background: url(assets/img/pala_i.png);
    float: left;
    width: 50%;
    background-position: bottom left;
  }
  .pala{
    height: 220px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom;
    margin-bottom: 35px;
  }
  .leyenda_d{
    background: url(assets/img/derecha.png);
    background-position: left;
    position: relative;
    width: 50%;
    float: right;
  }
  .leyenda_i{
    background: url(assets/img/izquierda.png);
    background-position: right;
    position: relative;
    width: 50%;
    float: left;
  }
  .leyenda{
    height: 35px;
    background-size: contain;
    background-repeat: no-repeat; 
    margin-top: 68px;   
  }
  .pala-pos1, .pala-pos2, .pala-pos3, .pala-pos4{
    position: relative;
    width: 132px;
  }
  .pala-pos1{
    display: block;
    left: -12%;
    top: 6%;
  }
  .pala-pos2{
    display: block;
    left: 5%;
    top: 6%;
  }
  .pala-pos3{
    display: block;
    left: 11%;
    top: 6%;
  }
  .pala-pos4{
    display: block;
    left: -5%;
    top: 6%;
  }
  .separator{
    height: 64px;
    /* background-color: red; */
  }
  /* sm */
  @media (min-width: 768px){
    .pala{
      height: 175px;
      margin-bottom: 12px;
    }
    .separator{
      height: 0;
    }
    .pala-pos1, .pala-pos2, .pala-pos3, .pala-pos4{
      -webkit-transform: scale(.65, .65);
      -ms-transform: scale(.65, .65);
      transform: scale(.65, .65);
    }
    .pala-pos1{
      display: block;
      left: 19%;
      top: 1%;
    }
    .pala-pos2{
      display: block;
      left: 16%;
      top: 10%
    }
    .pala-pos3{
      display: block;
      left: -19%;
      top: 1%;
    }
    .pala-pos4{
      display: block;
      left: -16%;
      top: 10%;
    }
  }
  /* md */
  @media (min-width: 992px){
    .pala{
      margin-bottom: 0;
    }
    .pala-pos1, .pala-pos2, .pala-pos3, .pala-pos4{
      -webkit-transform: scale(.7, .7);
      -ms-transform: scale(.7, .7);
      transform: scale(.7, .7);
    }
    .separator{
      height: 8px;
    }
    .pala-pos1{
      display: block;
      left: 17%;
      top: -5%;
    }
    .pala-pos2{
      display: block;
      left: 14%;
      top: 10%;
    }
    .pala-pos3{
      display: block;
      left: -17%;
      top: -5%;
    }
    .pala-pos4{
      display: block;
      left: -14%;
      top: 10%;
    }
  }
  /* lg */
  @media (min-width: 1200px){
    .pala{
      height: 320px;
      margin-bottom: 0;
    }
    .pala-pos1, .pala-pos2, .pala-pos3, .pala-pos4{
      -webkit-transform: scale(1, 1);
      -ms-transform: scale(1, 1);
      transform: scale(1, 1);
    }
    .separator{
      height: 0;
    }
    .pala-pos1{
      display: block;
      left: 19%;
      top: 16%;
    }
    .pala-pos2{
      display: block;
      left: 17%;
      top: 50%
    }
    .pala-pos3{
      display: block;
      left: -19%;
      top: 16%;
    }
    .pala-pos4{
      display: block;
      left: -16%;
      top: 50%;
    }
    #detalle_equipo{
      height: 410px !important;
    }
  }
  /* lg */
  @media (min-width: 2560px){
    .pala{
      height: 320px;
      margin-bottom: 0;
    }
    .pala-pos1, .pala-pos2, .pala-pos3, .pala-pos4{
      -webkit-transform: scale(1.2, 1.2);
      -ms-transform: scale(1.2, 1.2);
      transform: scale(1.2, 1.2);
    }
    .separator{
      height: 0;
    }
    .pala-pos1{
      display: block;
      left: 12%;
      top: 10%;
    }
    .pala-pos2{
      display: block;
      left: 10%;
      top: 53%
    }
    .pala-pos3{
      display: block;
      left: -12%;
      top: 10%;
    }
    .pala-pos4{
      display: block;
      left: -10%;
      top: 53%;
    }
    #detalle_equipo{
      height: 410px !important;
    }
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

  $isTimeout = false;
  $color = $ee->estatusPosiciones($id_camion,'green',$isTimeout);
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

      $data_plantilla         = $db->query("SELECT PIF, PRE_ALARMA FROM uman_plantilla WHERE ID_PLANTILLA='$id_plantilla'");
      $info_plantilla         = $data_plantilla->results()[0];

      $pif                    = $info_plantilla->PIF;
      $temp_k                 = $datos_eventos[$dnc->ID_POSICION]['eventotemperatura'] + 273.15;
      $ratio                  = $temp_k / ( 18 + 273.15 );
      $pif_[$dnc->ID_POSICION] = $pif;
      $datos_eventos[$dnc->ID_POSICION]['pre_alarma'] = $info_plantilla->PRE_ALARMA;
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
      if($datos_eventos[$j]['fecha_evento2'] == '00/00/0000 00:00'){
        $evt_temp = '-';
        $evt_fech = '';
        $evt_bate = '';
      }
      else{
        $evt_temp = $datos_eventos[$j]['eventotemperatura'];
        $evt_fech = $datos_eventos[$j]['fecha_evento2'];
        $evt_bate = $datos_eventos[$j]['eventobateria']; 
      }
      $ruedas[$i] = Core::dibuja_caja_temp($j, 
        $sensor[$j]['TIPO'],         
        $evt_temp, 
        $color[$j], 
        $evt_fech,
        $evt_bate);
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
  <div id="detalle_equipo" <?php if($numneumaticos==4) echo 'style="height:340px;"' ?>>   
    
    <!-- ESQUEMA EJES/NEUMÁTICOS -->
    <div class="<?=Core::col(6,6,6,12)?>">
      <div class="center-block hidden-xs">
       <?php

          $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
          $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
          $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
          $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

          $numneumaticos = $eje1 + $eje2 + $eje3 + $eje4;

          $attr = array();
          $attr['esquema']['style'] = 'margin-bottom: 0 !important;';
          if($eje1==2){
            $attr['neum'][1] = 'left: calc( 50% - 108px ) !important;';
            $attr['neum'][2] = 'left: calc( 50% + 60px ) !important;';

            $attr['popover'][1] = 'left: calc( 50% - 250px ) !important; margin: 0 !important;';
            $attr['popover'][2] = 'left: calc( 50% + 115px ) !important; margin: 0 !important;';
          }else{
            $attr['neum'][1] = 'left: calc( 50% - 134px ) !important;';
            $attr['neum'][2] = 'left: calc( 50% - 87px ) !important;';
            $attr['neum'][3] = 'left: calc( 50% + 44px ) !important;';
            $attr['neum'][4] = 'left: calc( 50% + 90px ) !important;';

            $attr['popover'][1] = 'left: calc( 50% - 397px ) !important; margin: 0 !important;';
            $attr['popover'][2] = 'left: calc( 50% - 266px ) !important; top: 225px; margin: 0 !important;';
            $attr['popover'][3] = 'left: calc( 50% + 134px ) !important; top: 225px; margin: 0 !important;';
            $attr['popover'][4] = 'left: calc( 50% + 266px ) !important; margin: 0 !important;';
          }            
          if($eje2==2){
            $attr['neum'][$eje1+1] = 'left: calc( 50% - 108px ) !important;';
            $attr['neum'][$eje1+2] = 'left: calc( 50% + 61px ) !important;';

            $attr['popover'][$eje1+1] = 'left: calc( 50% - 250px ) !important; margin: 0 !important;';
            $attr['popover'][$eje1+2] = 'left: calc( 50% + 115px ) !important; margin: 0 !important;';
          }else{
            $attr['neum'][$eje1+1] = 'left: calc( 50% - 134px ) !important;';
            $attr['neum'][$eje1+2] = 'left: calc( 50% - 87px ) !important;';
            $attr['neum'][$eje1+3] = 'left: calc( 50% + 43px ) !important;';
            $attr['neum'][$eje1+4] = 'left: calc( 50% + 90px ) !important;';

            $attr['popover'][$eje1+1] = 'left: calc( 50% - 275px ) !important; margin: 0 !important;';
            $attr['popover'][$eje1+2] = 'left: calc( 50% - 135px ) !important;';
            $attr['popover'][$eje1+3] = 'left: calc( 50% + 5px ) !important;';
            $attr['popover'][$eje1+4] = 'left: calc( 50% + 143px ) !important; margin: 0 !important;';
          }

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
                  class="scaleDown80" style="margin-left: -2px; top: 80px; left: auto; position: relative;">
                  <img src="./assets/img/btn_alarma1.png" />
                  <img src="./assets/img/btn_alarma2.png" />
                </div>';
            }
          }

          for($ix=0; $ix<count($colores); $ix++){
            $colores[$ix] = ["color"=>'none', "dato"=>null, "fecha"=>null];
          }
          echo Core::dibuja_esquema_equipo($neumaticos, $colores, $ruedas, $attr, $btn_rec_alarma);
       ?>
      </div>

      <div class="center-block visible-xs <?php echo $cls_lrm ?>">
       <?php
       $pops = '';
       array_shift($ruedas);
       foreach($ruedas as $r){
         if($r != '') $pops .= '<div class="'.Core::absolute_col_x(6).'">'.$r.'</div>';
       }

        echo $pops;
       ?>
      </div>
    </div>

    <!-- ESQUEMA PALA -->
    <div class="<?=Core::col(6,6,6,12)?> hidden-xs">
      <div class="separator"></div>
      <div class="row">
        <div class="pala pala_i">
          <div class="popover top pala-pos1">
            <div class="arrow"></div>
            <?=$ruedas[0]?>
          </div>

          <div class="popover bottom pala-pos2">
            <div class="arrow"></div>
            <?=$ruedas[4]?>
          </div>                 
        </div>
        <div class="pala pala_d">
          <div class="popover top pala-pos3">
            <div class="arrow"></div>
            <?=$ruedas[3]?>
          </div>

          <div class="popover bottom pala-pos4">
            <div class="arrow"></div>
            <?=$ruedas[7]?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="leyenda leyenda_i"></div>
        <div class="leyenda leyenda_d"></div>
      </div>
    </div>
  </div>
  
  <div id="detalle_alarmas" style="display:none"></div>

  <div id="detalle_recorrido" style="display:none"></div>

  <div id="detalle_grafico" style="display:none">
    <div id="container2" style="min-width: 310px; height: 100%; margin: 0 auto"></div>    
  </div>
</div>
<div class="modal-footer" id="footer-detalle">
  <div class="btn-group pull-left hidden-xs" role="group">
   <button type="button" class="btn btn-sm btn-success pull-left active" id="btnVerDetalle">Ver detalle equipo</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerAlarmas">Ver alarmas</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerRecorrido">Ver recorrido</button>
   <button type="button" class="btn btn-sm btn-success pull-left" id="btnVerGrafico">Grafico según posiciones</button>
  </div>
  <button type="button" class="btn btn-sm btn-primary pull-right" data-dismiss="modal" id="btn-cerrar-modal">Cerrar</button>
</div>

<?php
 $i=1;
 if(count($sensor)>0){
  for($i=1; $i<=16; $i++){
?>
<div id="detallesensor<?php echo $i ?>" style="display:none">
 <div class="row">
  <div class="<?=Core::col(12)?>">
   <div class="cc-divider">
     <center>
      <span class="t">POSICIÓN</span>
      <span class="s">: <?php echo $nomenclatura[$i]; ?></span><br/>
      <?php //if($GLOBALS['mostrar_fecha_evento']){ ?>
      <span style="font-size: 60%">Última recepción de datos: <?=$GLOBALS['datos_eventos'][$i]['fecha_evento2']?></span>
      <?php //} ?>
     </center>
   </div>
  </div>
  <div class="<?= Core::col(6,6,12,12) ?> <?=Core::offset(3,3)?>">
   <div class="cc-divider">SENSOR
     <div class="sensor Aceite pull-left"></div>
   </div>
   <h5>
    <span class="pull-left t">CÓDIGO</span>
    <span class="pull-left s">: <?php echo $sensor[$i]['COD']; ?></span>
   </h5>
   <h5>
    <span class="pull-left t">TIPO</span>
    <span class="pull-left s">: <?php echo $sensor[$i]['TIPO']; ?></span>
   </h5>   
   <h5>
    <span class="pull-left t">UMBRAL T&deg;</span>
    <span class="pull-left s">: <?php echo $datos_eventos[$i]['tempmax']; ?>&deg;C</span>
   </h5>
   <h5>
    <span class="pull-left t">PRE-ALARMA</span>
    <span class="pull-left s">: <?php echo $datos_eventos[$i]['pre_alarma']; ?>&deg;C</span>
   </h5>
   <h5>
    <span class="pull-left t">INST. SENSOR</span>
    <span class="pull-left s">: <?php echo $sensor[$i]['INST_SENSOR']; ?></span>
   </h5>
  </div>
 </div>
</div>
<?php
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

   $("#btnVerDetalle").removeClass('active');
   $("#btnVerRecorrido").removeClass('active');
   $("#btnVerGrafico").removeClass('active');
   $("#btnVerAlarmas").removeClass('active');

   if(obj == '#detalle_equipo')         $("#btnVerDetalle").addClass('active');
   else if(obj == '#detalle_recorrido') $("#btnVerRecorrido").addClass('active');
   else if(obj == '#detalle_grafico')   $("#btnVerGrafico").addClass('active');
   else if(obj == '#detalle_alarmas')   $("#btnVerAlarmas").addClass('active');
 }
 $(function(){
  
  $("#btnVerDetalle").click(function(){ mostrar("#detalle_equipo"); });
  
  $("#btnVerAlarmas").click(function(){
    $("#modal-loader").removeClass('hidden').addClass('show');
    $("#detalle_alarmas").load('data-reporte-alarmas.php?'+(Math.random()*1000),
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
    
    $("#detalle_recorrido").load('data-grafico-gps.php?'+(Math.random()*1000),
      {equipo:'<?php echo $id_camion ?>', maxContentHeight: maxContentHeight}, 
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
    $.post('data-grafico-presion.php?'+(Math.random()*1000),
      {equipo:'<?php echo $id_camion ?>', maxContentHeight: maxContentHeight},
      function(data){
        // console.log(data);
        mostrar("#detalle_grafico");
        $("#container2").html(data);

        var footer_width = $("#footer-detalle").css("width");
        var button_width = $("#btn-cerrar-modal").css("width");
        var button_height = $("#btn-cerrar-modal").css("height");
        $("#btn-cerrar-modal").css("margin","-"+button_height+" 0px auto calc( "+footer_width+" - "+button_width+")");
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
  });

  $("div.tire-box").on("click", function(){
   var swal_content = $(this).data("target");
   swal_content = $(swal_content).html();

   swal({
     title: 'Detalle de Sensor',
     text: swal_content,
     html: true,
     confirmButtonColor: '#337ab7',
     confirmButtonText: 'Cerrar'
   })
  });

 });
</script>
