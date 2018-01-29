<?php
  require_once 'autoload.php';

  $acc = new Acceso(true);

  include("idiomas/cargar_idioma.php");

  // date_default_timezone_set("America/Santiago");
  // echo 'monitor.php';
  //inicialización
  $db          = DB::getInstance(); 
  $flota       = array();
  $ctrlFlota   = new Flota();
  $ctrlEquipo  = new Equipo();
  $gen         = new General();

  $timeout                       = $gen->getParamValue('timeout', 30);
  $timeoutOL                     = $gen->getParamValue('timeoutOL', 5);
  $GLOBALS['esquema']            = $gen->getParamValue('tipoesquemamonitoreo');
  
  #region determinar si se debe mostrar la alarma ámbar
  $GLOBALS['pre_alarma']       = $gen->getParamvalue('pre_alarma', 1);
  #end region
  
  #region obtener tiempo de refresco de página de monitoreo
  $GLOBALS['refresco']           = $gen->getParamValue('refresco', 30);
  #end region
  $GLOBALS['unidad_temperatura'] = $gen->getParamValue('unidad_temperatura', 'celsius');
  $GLOBALS['unidad_presion']     = $gen->getParamValue('unidad_presion', 'psi');
  
  $hora_actual 		               = date('d/m/Y H:i:s', strtotime('now') - ($timeout*60));

  $arr_flt  = $ctrlFlota->listar_full();

  if(isset($arr_flt) && count($arr_flt) > 0){
    foreach ($arr_flt as $flt){

      for($i=1; $i<=16; $i++) $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['R'.$i] = 0;

      if($ctrlEquipo->obtener_neumaticos($flt->ID_CAMION,$neumaticos))
      {
        foreach($neumaticos as $n)
        {
          $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['R'.$n->ID_POSICION] = $n->ID_NEUMATICO;
        }
      }
      
      $flota[$flt->NUMFLOTAS]['ID']   = $flt->NUMFLOTAS;
      $flota[$flt->NUMFLOTAS]['NAME'] = $flt->NOMBRE;
      $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['ID'] = $flt->ID_CAMION;
      $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['CODE'] = $flt->NUMCAMION;
      $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['NUM'] = count($neumaticos);
      $flota[$flt->NUMFLOTAS]['EQUIPOS'][$flt->ID_CAMION]['tipo'] = $flt->tipo;
    }
  }

  // print_r($flota);
?>

<style media="screen">
  /*eje1 neum-normal neum-11 pad*/
  .neum{     
    width: 14px;
    height: 34px;
    position: absolute;

    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
  }
  .neum-1112{ left: 12px; }
  .neum-1314{ left: 73px; }
  .neum-11, .neum-21{ left: 6px; }
  .neum-12, .neum-22{ left: 22px; }
  .neum-13, .neum-23{ left: 64px; }
  .neum-14, .neum-24{ left: 81px; }
  .neum-2122{ left: 12px; }
  .neum-2324{ left: 73px; }
  .eje1{ margin-top:-3px; }
  .eje2{ margin-top:70px; }
  .neum-none, .neum-gray, .neum-orange, .neum-yellow, .neum-red, .neum-black, .neum-lilac{
    background-size: 14px 34px !important;
    background-repeat: no-repeat !important;
    background-position: center;
  }
  .neum-none  { background: url('assets/img/rueda_fantasma.png'); }
  .neum-gray  { background: url('assets/img/rueda_gris.png'); }
  .neum-orange{ background: url('assets/img/rueda_naranja.png'); }
  .neum-yellow{ background: url('assets/img/rueda_amarilla.png'); }
  .neum-red   { background: url('assets/img/rueda_roja.png'); }
  .neum-black { background: url('assets/img/rueda.png'); }
  .neum-lilac { background: url('assets/img/rueda_ambar.png'); }
  .num-equipo{
    font-size:18px;
    font-weight:bold;
  }
  .equipo-online{ color:#1565C0; }
  .equipo-offline{ color:#90A4AE; }
  .box{
    cursor: pointer;
  }
  .box-header-equipo{
    padding: 0 0 10px 10px;
    height: 30px;
  }
  .btn_modal_equipo{
    min-width:99px;
    margin-right:5px;
  }
	.blc-article_wheel.fixed{
		width: 90px;
	}
 .monitor-grid{
  	list-style: none;
  	text-align: left;
  	padding: 1px;
  	margin: 0px;
 }
 .monitor-grid > li{
  	display: inline-block;
  	vertical-align: middle;
  	text-align: center;
  	width: 90px;
  	margin: 3px;
 }
 .monitor-grid .monitor-header{
 	background-color: #0e55b1;
 	border: 1px solid #0b3973;
 	height: 22px;
 }
 .monitor-grid .monitor-title{
 	float: left;
 	width: 60px;
 	height: 20px;
 	line-height: 22px;
 	border-right: 0px solid #0b3973;
 	color: white;
 	font-size: 12px;
 	font-weight: 600;
 }
 .monitor-grid .monitor-signal{
 	float: right;
 	width: 20px;
 	height: 20px;
 	line-height: 20px;
 	background-image: url('assets/img/sprite_signal.png');
 	background-size: 20px;
 	background-position: bottom;
 }
 .monitor-grid .monitor-signal.on{
 	background-position: top;
 }
 .monitor-grid a{
 	text-decoration: none;
 }
 .monitor-grid a:hover article{
 	background-color: #dedede;
 }
 .monitor-grid article{
 	border: 1px solid #d0d0d0;
 }
 #modal_equipo_content{
  text-align: -webkit-center;
 }
  @media (max-width: 799px){
    .modal-dialog{
      width: 98% !important;
      left: 0 !important;
    }
    .real-time-data{
      height: 500px;
      overflow-y: auto;
    }
  }
  @media (min-width: 800px){
    .modal-dialog{
      width: 90% !important;
      max-width: 90% !important; 
    }
    .real-time-data{
      margin-left: -10px;
      overflow-y: hidden;
    }
  }
</style>

<div class="container">
	<?php
	 if(isset($flota) && count($flota) > 0){
		foreach ($flota as $flt) {
			$flt_id		=	$flt['ID'];				// => 3
			$flt_name	=	$flt['NAME'];			// => Camionetas
			$flt_arr	=	$flt['EQUIPOS'];	// => Array
	?>
 <div class="cc-divider"><b><?php print header_flota($texto_sitio['Flota'].' '.$flt_name) ?></b></div>
  <div class="row">
   <?php
	  if(isset($flt_arr) && count($flt_arr) > 0){
		 echo "<div class=''>";
		 echo " <ul class='monitor-grid'>";

		 foreach ($flt_arr as $eqp) {
      $eqp_id		=	$eqp['ID'];		// => 4
      $eqp_code	=	$eqp['CODE'];	// => 10000
      $eqp_num	=	$eqp['NUM'];	// => 6
      $eqp_type = $eqp['tipo'];
      $r1				=	$eqp['R1'];		// => 683
      $r2				=	$eqp['R2'];		// =>
      $r3				=	$eqp['R3'];		// => 332
      $r4				=	$eqp['R4'];		// =>
      $r5				=	$eqp['R5'];		// =>
      $r6				=	$eqp['R6'];		// =>
      $cuenta 	= 0;

      $enLinea['caja'] 	 = FALSE;
      $enLinea['equipo'] = FALSE;

      #region determinar estado de recepción de eventos
     //    $q = "SELECT * 
     //     FROM uman_ultimoevento 
     //     WHERE numequipo='$eqp_id' AND fecha_evento > date_sub(now(), interval {$timeout} minute) 
     //     LIMIT 1";
     //    $cuenta = $db->query($q)->count();
     //    // echo $q;

  			// if ( $cuenta == 0 ) {
     //     $q = "SELECT * 
     //      FROM uman_ultimogps 
     //      WHERE ID_EQUIPO='$eqp_id' AND FECHAGPS > date_sub(now(), interval {$timeout} minute) 
     //      LIMIT 1";
  			//  $cuenta = $db->query($q)->count();
     //     // echo $q;
  			// }

     //    $enLinea['equipo'] = ($cuenta > 0)?TRUE:FALSE;
      #end region

      #region determinar si el equipo está en cobertura
        $q = "SELECT * 
         FROM uman_ultimoevento 
         WHERE numequipo='$eqp_id' AND fecha_evento > date_sub(now(), interval {$timeoutOL} minute) 
         LIMIT 1";
        $cuenta = $db->query($q)->count();
        // echo $q;

        if ( $cuenta == 0 ) {
         $q = "SELECT * 
          FROM uman_ultimogps 
          WHERE ID_EQUIPO='$eqp_id' AND FECHAGPS > date_sub(now(), interval {$timeoutOL} minute) 
          LIMIT 1";
         $cuenta = $db->query($q)->count();
         // echo $q;
        }

        if ( $cuenta == 0 ) {
         $q = "SELECT * 
          FROM uman_reportes_id ri INNER JOIN uman_cajauman cu ON ri.id_caja=cu.CODIGOCAJA
          INNER JOIN uman_camion c ON c.ID_CAJAUMAN=cu.ID_CAJAUMAN
          WHERE c.ID_CAMION={$eqp_id} AND ri.fecha > date_sub(now(), interval {$timeoutOL} minute) 
          LIMIT 1";
         $cuenta = $db->query($q)->count();
         // echo $q;
        }
        $enLinea['caja'] = ($cuenta > 0)?TRUE:FALSE;
      #en region

			
      $_GLOBALS['enLinea'] = $enLinea;
      $isTimeout = false;
      // var_dump($enLinea); echo '#####';

      $data = $db->query(sprintf("SELECT NEUMATICOS FROM uman_tipo_equipo WHERE ID='%s'",$eqp_type));
      $info = $data->results()[0];

      $neumaticos = explode( "," , $info->NEUMATICOS );

      $ee = new EstadoEquipo();
      $colores = $ee->estatusPosiciones($eqp_id,'black',$isTimeout);

      $class_alarmado = '';
      $sql = "SELECT * 
       FROM uman_alarmas 
       WHERE ALARMAESTADO='0' AND ALARMATIPO!='16' AND ALARMATIPO!='8' AND ALARMANUMCAMION=$eqp_id;";
      // echo $sql;
      $data_alarmas = $db->query($sql);
      $info_alarmas = $data_alarmas->results();
      if($data_alarmas->count() > 0) {
        if(!$isTimeout){ $class_alarmado = 'blink-alarmado'; }
      }
      echo "<li class='btn_modal_equipo $class_alarmado' codigo-equipo='".$eqp_id."'>";
      // echo dibuja_caja_equipo($neumaticos, colores( $eqp_id ) , $enLinea , $eqp_code);
      echo dibuja_caja_equipo($neumaticos, $colores , $enLinea['caja'] , $eqp_code);
      echo "</li>";
     }

     echo " </ul>";
     echo "</div>";
    }
   ?>
	</div>
<?php
		}
	}
?>
 </div>

<!-- Modal Vista Equipo -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="modal_equipo">
  <div class="modal-dialog">
    <div class="modal-content" id="modal_equipo_content">
      <!-- Aqui se carga la información del equipo, via ajax -->
    </div>
  </div>
</div>

<script type="text/javascript">
 var equipo;
 var tt;
 $(document).ready(function(){
   // $("#modal_equipo").modal();
    // Click de un Equipo en el monitoreo
    var datax = undefined;
    $(".btn_modal_equipo").click(function(){
      equipo  = $(this).attr("codigo-equipo");
      $("#modal_equipo_content").load("./modal/modal.equipo.php?equipo="+equipo);
      $("#modal_equipo").modal('show');
    });

    $("#modal_equipo").on('shown.bs.modal', function () {
      let minHeight = window.screen.availHeight-200;
      // 12_01_2018 CT - Se comenta impresiones por consola 
      //console.log("window.screen.availHeight: "+window.screen.availHeight);
      $("div.modal-body").css("min-height", minHeight+'px');
      let actHeight = parseInt($('#body-detalle').css("height").replace('px',''));
      maxContentHeight = (minHeight > actHeight ? minHeight : actHeight) - 62;
      // 12_01_2018 CT - Se comenta impresiones por consola 
      //console.log(maxContentHeight);
      var top = $("#modal_equipo").css('height').replace('px','');
      if(top<762){
       top = (top==0 || top==NaN) ? 1 : (top/4);
       document.getElementById("modal_equipo").scrollTop = top;
      }
      clearTimeout(tt);
    });

    $("#modal_equipo").on('hidden.bs.modal', function () {
      // location.reload();
      $("#content").load('monitor.php');
    });    
  });

 tt = setTimeout(
  function() { 
    $("#content").load('monitor.php');
  }, '<?php echo($GLOBALS['refresco']*1000); ?>');
</script>

<?php
function header_flota($nom_flota){
  $html = '<div class="text-info"><center>'.$nom_flota.'</center></div>';

  return $html;
}

function crearTooltip($data){
  $tooltip = '';
  if($data['dato']!=''){
    $tooltip = 'title="'.$data['dato'].'" data-placement="bottom" data-toggle="tooltip" data-html="true"';
  }
  else if($data['fecha']!=''){
    $tooltip = 'title="ÚLTIMA EMISIÓN: <BR/> '.$data['fecha'].'" data-placement="bottom" data-toggle="tooltip" data-html="true"';
  }
  return $tooltip;
}

function dibuja_caja_equipo($neumaticos , $colores , $enLinea , $eqp_code, $info=NULL){
 $neums = ''; $script = ''; $img = '';

 $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
 $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
 if(count($neumaticos)>=11)
  $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
 if(count($neumaticos)>=15)
  $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

 foreach($neumaticos as $ne) $img .= $ne;
 if($GLOBALS['esquema'] == 'universal'){
  $img = 'universal';
 }


 $en_linea_fa = '<i class="fa fa-wifi fa-2x" aria-hidden="true"></i>';
 $en_linea_mi = '<i class="material-icons equipo-online ">signal_wifi_4_bar</i>';
 $en_linea = $en_linea_fa;

 $tt_status = ($enLinea===TRUE) ? 'En línea':'Sin cobertura';
 $enLinea   = ($enLinea===TRUE) ? $en_linea : '<i class="material-icons equipo-offline ">signal_wifi_off</i>';

 // CAJA APAGADA
  // $status_caja = '<span class="fa-stack fa-lg">
  // <i class="material-icons equipo-offline fa-stack-1x">signal_wifi_off</i>
  // <i class="fa fa-ban fa-stack-2x text-danger"></i>
  // </span>';

 if($eje1==2){
   $neums .= '<div class="eje1 neum neum-'.($colores[1]['color']).' neum-1112" 
    '.(crearTooltip($colores[1])).'></div>';
   $neums .= '<div class="eje1 neum neum-'.($colores[2]['color']).' neum-1314" 
    '.(crearTooltip($colores[2])).'></div>';
 }
 elseif($eje1==4){
   $neums .= '<div class="ccc eje1 neum neum-'.$colores[1]['color'].' neum-11" 
    '.(crearTooltip($colores[1])).'></div>';
   $neums .= '<div class="eje1 neum neum-'.$colores[2]['color'].' neum-12" 
    '.(crearTooltip($colores[2])).'></div>';
   $neums .= '<div class="eje1 neum neum-'.$colores[3]['color'].' neum-13" 
    '.(crearTooltip($colores[3])).'></div>';
   $neums .= '<div class="eje1 neum neum-'.$colores[4]['color'].' neum-14" 
    '.(crearTooltip($colores[4])).'></div>';
 }

 if($eje2==2){
  $neums .= '<div class="eje2 neum neum-'.$colores[3]['color'].' neum-2122" 
   '.(crearTooltip($colores[3])).'></div>';
  $neums .= '<div class="eje2 neum neum-'.$colores[4]['color'].' neum-2324" 
   '.(crearTooltip($colores[4])).'></div>';
 }
 elseif($eje2==4){
  $neums .= '<div class="eje2 neum neum-'.$colores[3]['color'].' neum-21" 
   '.(crearTooltip($colores[3])).'></div>';
  $neums .= '<div class="eje2 neum neum-'.$colores[4]['color'].' neum-22" 
   '.(crearTooltip($colores[4])).'></div>';
  $neums .= '<div class="eje2 neum neum-'.$colores[5]['color'].' neum-23" 
   '.(crearTooltip($colores[5])).'></div>';
  $neums .= '<div class="eje2 neum neum-'.$colores[6]['color'].' neum-24" 
   '.(crearTooltip($colores[6])).'></div>';
 }

 $nombre = $eqp_code;
 if(strlen($eqp_code)>6) $nombre = substr($eqp_code, 0, 5).'..';
 $html = '
   <div class="box" style="width:99px; background: #dbdcdd">
    <div class="box-header-equipo with-border">
     <span class="num-equipo pull-left" style="height: 10px" title="'.$eqp_code.'" data-toggle="tooltip">'.$nombre.'</span>
     <div class="box-tools pull-right">
      <span title="'.$tt_status.'" style="color:#3c8dbc" data-toggle="tooltip">
       '.$enLinea.'
      </span>
     </div>
    </div>
    <div class="box-body" style="background-image: url(assets/img/esquemas/'.$img.'.png); width: 99px; height: 119px">
    '.$neums.'
    </div>   
   </div>'
   .$script;

 return $html;
}


?>