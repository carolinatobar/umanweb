<?php

  require 'autoload.php';
  session_start();
  $sess_id = session_id();
  $acc = new Acceso(false, false);
  if(!$acc->Permitido()){    
    if($acc->CodigoError() == 'acceso_denegado'){
      $_SESSION[session_id()]['ERROR'] = array(
        'title'=>$acc->MensajeError(),
        'text'=>'<h3><center>Para acceder al sitio correctamente, Ud. debe ingresar su nombre de usuario y contraseña.</center></h3><br/><a class="btn btn-info center-block" href="../">Ir al Login</a>',
      );
      header("Location: errors/");
    }
  } 

  //rescatar menu
  $link = isset($_GET['s']) ? $_GET['s'] : NULL;
  if( $link == NULL ){
    // $_SERVER['REQUEST_URI'] .= '?s='.$_SESSION[session_id()]['predefinido'];
    // echo $_SERVER['REQUEST_URI'];
    $link = $_SESSION[session_id()]['predefinido'];
    $complete_link =  $_SESSION[session_id()]['predefinido'];
  }

  //Verificar si el link recibido existe, de lo contrario se cargará el predefinido o se deplegará un mensaje de error
  $include_file = '';
  $module_label = '';
  $db2 = DB2::getInstance();
  $sql = sprintf("SELECT * FROM uman_modulo WHERE link='%s'", $link);
  $res = $db2->query($sql);
  if($res->count() == 0){
    $link = 'error';
  }
  else{
    $include_file = $res->results()[0]->pagina;
    $module_label = utf8_encode($res->results()[0]->etiqueta);
  }

  if($link == 'logout'){
    header("Location: ../cerrar-sesion.php");
  }

  $idioma        = $_SESSION[session_id()]['lang'];
  $faena         = $_SESSION[session_id()]['faena'];
  $nombrefaena   = $_SESSION[session_id()]['nombrefaena'];
  $nombreusuario = $_SESSION[session_id()]['nombre'];
  // $tipodeusuario = $_SESSION[session_id()]['tipodeusuario'];
  $empresa       = $_SESSION[session_id()]['empresa'];
  $perfilactivo  = $_SESSION[session_id()]['perfilactivo'];
  $perfiles      = $_SESSION[session_id()]['perfiles'];
  $csrf_token    = $_SESSION[session_id()]['csrf_token'];
  $faenas        = $_SESSION[session_id()]['faenas'];

  $stringFaenas = '';
  $comboFaenas = '';

  foreach($faenas as $f){  
    $stringFaenas .= "<li><a href='../faena-seleccionada.php?faena={$f->nombre_db}&withref'>{$f->nombre_faena}</a></li>\n";
  }

  $sql = "SELECT * FROM uman_faenas";
  $res = $db2->query($sql);
  foreach($res->results() as $f){
    $comboFaenas .= '<option value="'.$f->id.'">'.utf8_encode($f->nombre_faena).'</option>';
  }

  include("idiomas/cargar_idioma.php");

  //Estilos Wheels
  $wheels_style = ($s == 'asignar-neumaticos') ? 'styles_n.css' : 'styles.css';

  $gen     = new General();
  $map_api = $gen->getParamValue('mapapi', 'google');
?>

<!DOCTYPE html>
<html lang="<?=$idioma?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

  <link rel="shortcut icon" type="image/png" href="favicon.png"/>
  
  <!-- jQuery 2.2.3 -->
  <script src="assets/plugins/jQuery/jquery-2.2.3.min.js"></script>  

  <!-- HighCharts -->
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/highcharts-more.js"></script>
  
  <link href="https://fonts.googleapis.com/css?family=Muli|Libre+Franklin" rel="stylesheet">
  <!-- Reset -->
  <link rel="stylesheet" href="assets/css/reset.css">
  <!-- Custom Index -->
  <link rel="stylesheet" href="assets/css/index.css">
  <!-- Wheels Styles -->
  <link rel="stylesheet" href="assets/wheels/css/<?php echo $wheels_style; ?>">
  <!-- Wheels Icons -->
  <link rel="stylesheet" href="assets/wheels/css/wheels_icon.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/material-icons.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/css/AdminLTE.css">
  <link rel="stylesheet" href="assets/css/skins/skin-blue.min.css">
  
  <!--// DataTables //-->
  <link rel="stylesheet" type="text/css" href="assets/datatables-1.10.15/datatables.min.css"/>
  <script type="text/javascript" src="assets/datatables-1.10.15/datatables.min.js"></script>

  <!--// YADCF //-->
  <link rel="stylesheet" type="text/css" href="assets/yadcf-0.9.1/jquery.dataTables.yadcf.css"/>
  <script type="text/javascript" src="assets/yadcf-0.9.1/jquery.dataTables.yadcf.js"></script>
  
  <!--// SweetAlert //-->
  <link rel="stylesheet" href="assets/sweetalert/sweetalert.css">
  <link rel="stylesheet" href="assets/sweetalert/themes/twitter.css">
  <!--<link rel="stylesheet" href="dist/themes/facebook.css">-->
  <!--<link rel="stylesheet" href="dist/themes/google.css">-->
  <script src="assets/sweetalert/sweetalert.min.js"></script>
  
  <!--// BootstrapSelect //-->
  <link rel="stylesheet" href="assets/bootstrap-select-1.12.4/css/bootstrap-select.min.css" />
  <script src="assets/bootstrap-select-1.12.4/js/bootstrap-select.min.js"></script>
  <script src="assets/bootstrap-select-1.12.4/js/i18n/defaults-es_CL.min.js"></script>
  
  <!--// MommentJS //-->
  <script type="text/javascript" src="assets/js/moment.js"></script>
  
  <!--// Bootstrap 3.3.7 //-->
  <link rel="stylesheet" href="assets/bootstrap-3.3.7-dist/css/bootstrap.css">
  <script src="assets/bootstrap-3.3.7-dist/js/bootstrap.js"></script>

  <!--// AdminLTE //-->
  <script src="assets/js/app.js"></script> 
  
  <!--// Range DatePicker //-->
  <link rel="stylesheet" href="assets/daterangepicker/daterangepicker.css" />
  <script src="assets/daterangepicker/daterangepicker.js"></script>


  <!-- HERE API -->
  <link rel="stylesheet" type="text/css" href="https://js.cit.api.here.com/v3/3.0/mapsjs-ui.css" />
  <script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-core.js"></script>
  <script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-service.js"></script>
  <script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-ui.js"></script>
  <script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>
  <script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-clustering.js"></script>
  <script type="text/javascript" src="https://js.cit.datalens.api.here.com/latest/mapsjs-datalens.js"></script>
  <script type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>

  <!-- GOOGLE API -->
  <!-- <script async defer src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw&libraries=visualization,geometry"></script> -->
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=
  <?=$GLOBALS['GOOGLE']['KEY']?>&libraries=<?=$GLOBALS['GOOGLE']['LIBRARY']?>"></script>
  <?php if($map_api == 'google'){ ?>
  <!-- <script type="text/javascript" src="assets/js/markerwithlabel.js"></script> -->
  <?php } ?>
  

  <script type="text/javascript">
    window.csrf = { csrf_token: '<?php echo $csrf_token ?>' };
    $.ajaxSetup({ data: window.csrf });
  </script>
  <title>Uman Web</title>
  <style>
   @media (max-height: 670px){
    .modal-dialog.modal-lg{
      -ms-transform: scale(.7, .7) !important;
      -webkit-transform: scale(.7, .7) !important;
      transform: scale(.7, .7) !important;
    }
   }
   .container { width: 100%; }
   .neumico{
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    width: 15px;
    height: 35px;
    margin-right: 20px;  
   }
   .icon-neum-none, .icon-neum-gray, .icon-neum-orange, .icon-neum-yellow, .icon-neum-red, .icon-neum-black, .icon-neum-lilac{
    background-size: 15px 35px !important;
    background-repeat: no-repeat !important;
    background-position: center;
   }
   .icon-neum-none  { background: url('assets/img/rueda_fantasma.png'); }
   .icon-neum-gray  { background: url('assets/img/rueda_gris.png'); }
   .icon-neum-orange{ background: url('assets/img/rueda_naranja.png'); }
   .icon-neum-yellow{ background: url('assets/img/rueda_amarilla.png'); }
   .icon-neum-red   { background: url('assets/img/rueda_roja.png'); }
   .icon-neum-black { background: url('assets/img/rueda.png'); }
   .icon-neum-lilac { background: url('assets/img/rueda_ambar.png'); }

   .menu, .expanded{
     max-height: 400px !important;
     background: white;
     z-index: 9999;
   }
   .content-wrapper{
     min-height: calc( 100% - 100px ) !important;
     height: calc( 100% - 100px ) !important;
     overflow-y: auto !important;
   }
   .main-footer{
     height: 50px;
     width: calc( 100% - 50px ) !important;
     position: absolute;
     padding-left: 60px;
     z-index: 900 !important;
     top: calc( 100% - 50px);
   }
   .treeview-menu{
     margin-top: -1px !important;
     z-index: 99999;
   }

    ul.treeview-menu::-webkit-scrollbar-track
    {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      /* background-color: #F5F5F5; */
      background-color: #222d32;
      border-radius: 7px;
    }

    ul.treeview-menu::-webkit-scrollbar
    {
      width: 10px;
      background-color: #222d32;
    }

    ul.treeview-menu::-webkit-scrollbar-thumb
    {
      border-radius: 7px;
      background-image: -webkit-gradient(linear,
                        left bottom,
                        left top,
                        color-stop(0.44, #3c8dbc),
                        color-stop(0.86, #3c8dbc));
    }

    div.content-wrapper::-webkit-scrollbar-track, 
    div.tab-content-menu::-webkit-scrollbar-track,
    #contenedor-neumaticos::-webkit-scrollbar-track,
    #sensor-grid::-webkit-scrollbar-track
    {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      background-color: #e8e8e8;
      border-radius: 7px;
      z-index: 1049;
    }

    div.content-wrapper::-webkit-scrollbar, 
    div.tab-content-menu::-webkit-scrollbar,
    #contenedor-neumaticos::-webkit-scrollbar,
    #sensor-grid::-webkit-scrollbar
    {
      width: 10px;
      background-color: #e8e8e8;
      z-index: 1049;
    }

    div.tab-content-menu::-webkit-scrollbar-track, 
    div.tab-content-menu::-webkit-scrollbar
    {
      background-color: #222d32;
    }

    #contenedor-neumaticos::-webkit-scrollbar,
    #contenedor-neumaticos::-webkit-scrollbar-track,
    #sensor-grid::-webkit-scrollbar-track,
    #sensor-grid::-webkit-scrollbar
    {
      background-color: #ccc;
    }

    div.content-wrapper::-webkit-scrollbar-thumb, 
    div.tab-content-menu::-webkit-scrollbar-thumb,
    #contenedor-neumaticos::-webkit-scrollbar-thumb,
    #sensor-grid::-webkit-scrollbar-thumb
    {
      border-radius: 4px;
      background-image: -webkit-gradient(linear,
                        left bottom,
                        left top,
                        color-stop(0.44, #3c8dbc),
                        color-stop(0.86, #3c8dbc));
      z-index: 1049;
    }
    .tab-content-menu{
      margin: 0 0 0 20px;
      padding: 0;
      border: 0;
      font-size: 100%;
      font: inherit;
      vertical-align: baseline;
    }
    html{
      overflow: hidden;
      /* background: #ecf0f5; */
    } 
    .ndcsa{
      width: 60px;
      height: 79px;
      background: url(assets/img/disponibilidad.png) 60px 0;
    }
    .ndssa{
      width: 60px;
      height: 79px;
      background: url(assets/img/disponibilidad.png) 120px 0;
    }
    .nie{
      width: 60px;
      height: 79px;
      background: url(assets/img/disponibilidad.png) 0 0;
    }
    @-webkit-keyframes blinker {
      from {opacity: 1.0;}
      to {opacity: 0.3;}
    }
    .blink-alarmado{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 0.3s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
  </style>
</head>

<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
 <div class="wrapper" style="overflow-y:hidden">
  <?php include_once("header.php") ?>
  <?php include_once("menu.php") ?>
  <div class="content-wrapper">
   <section class="content" id="content">
     <?php
      // var_dump($acc->Permitido()); echo $link;
      if( $acc->Permitido() || $link == 'error'){
        if(file_exists($include_file)){
          //Parche para cerro negro norte para que muestre esquema diferente
          if($include_file == 'monitor.php' && $faena == 'cerro_negro_norte') $include_file = 'monitor.aceite.php';
          
          include_once($include_file);
        }
        else{
          Render::make_block(
            '¡Ups!',
            '<h3><center>La página a la que está intentando acceder no existe o de momento no está habilitado su acceso.</center></h3>',
            'warning'
          );
        }
      }
      else{
        Render::make_block(
          $GLOBALS['_ERROR'][$acc->CodigoError()]->title,
          $GLOBALS['_ERROR'][$acc->CodigoError()]->text           
        );
      }
     ?>
   </section>
  </div>
 </div>

 <?php include_once("footer.php"); ?>

  <section name="SCRIPTS">
    <script type="text/javascript">
      function activarFullscreen(){
        if (!document.mozFullScreen && !document.webkitFullScreen) {
          if (document.mozRequestFullScreen) {
            document.body.mozRequestFullScreen();
          } else {
            document.body.webkitRequestFullScreen();
          }
        }
      }

      //INICIALIZAR .selectpicker
      $(document).ready(function () {
        $('select').selectpicker({dropupAuto:false});
      });


      $(function(){ 
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
          console.log('mobile');
        }
        else{
          console.log('desktop');
        }
        
        $("button").click(function(){
          // activarFullscreen();
        });

        var sHeight = window.screen.availHeight - 115;
        var tvmHeight = window.screen.availHeight;
        
        $("div.tab-content-menu").css("max-height", (tvmHeight-150)+"px").css("overflow-y","auto");
        $.each($("li.treeview"), function(i, o){
          var a = o.children[0].text.trim();
          var mHeight = (o.children[1].children.length + 1) * 30;

          if(a == 'Consultas'){            
            var attr = "height: "+(tvmHeight-250)+"px !important;";
            if((tvmHeight-250)<mHeight) attr = attr + "overflow-y: scroll !important;";
            $(o.children[1]).attr("style",attr);
          }else if(a == 'Configuraciones'){
            var attr = "height: "+(tvmHeight-295)+"px !important;";
            if((tvmHeight-295)<mHeight) attr = attr + "overflow-y: scroll !important;";
            $(o.children[1]).attr("style",attr);
          }
        });
        
        // $(".container").css("height", sHeight+"px").attr("style","height:"+sHeight+"px").append('<p>&nbsp;</p><br/>');
      });

      // EVITAR goBack() EN BROWSER
      (function (global) {
        if(typeof (global) === "undefined"){
          throw new Error("window is undefined");
        }

        var _hash = "!";
        var noBackPlease = function () {
          <?php
            if(isset($complete_link)){
              echo 'global.location.href += "'.$complete_link.'";';
            }
          ?>
          global.location.href += "#";
          // making sure we have the fruit available for juice....
          // 50 milliseconds for just once do not cost much (^__^)
          global.setTimeout(function () { global.location.href += "!"; }, 50);
        };
      
        // Earlier we had setInerval here....
        global.onhashchange = function () {
          if (global.location.hash !== _hash) {
            global.location.hash = _hash;
          }
        };

        global.onload = function () {
          noBackPlease();
          // disables backspace on page except on input fields and textarea..
          document.body.onkeydown = function (e) {
            var elm = e.target.nodeName.toLowerCase();
            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
              e.preventDefault();
            }
            // stopping event bubbling up the DOM tree..
            e.stopPropagation();
          };
        };
      })(window);

      document.addEventListener('DOMContentLoaded', function () {
        if (Notification.permission !== "granted")
          Notification.requestPermission();
      });

      function crearNotificacion(t,i,b,u=undefined) {
        if (!Notification) {
          // alert('Desktop notifications not available in your browser. Try Chromium.'); 
          return;
        }

        if (Notification.permission !== "granted")
          Notification.requestPermission();
        else {
          var notification = new Notification(t, { icon: i, body: b});

          notification.onclick = function () { 
            if(u!=undefined){
              if(u == 'focus') window.focus();
              else if(u.startsWith('url:')) window.open(u.replace('url:',''));
            }

            notification.close();
          }          
        }
      }
    </script>
  </section>  
</body>
</html>
