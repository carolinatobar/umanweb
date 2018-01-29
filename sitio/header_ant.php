<!-- Main Header -->
<header class="main-header">
  <link rel="stylesheet" href="assets/css/header.css" />
  <!-- Logo -->
  <a href="#" class="logo">
    <!-- logo for regular state and mobile devices -->
    <div class="logo_uman"></div>
  </a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle hidden-xs" data-toggle="offcanvas" role="button">
      <span class="sr-only">Navegación Tabla</span>
    </a>

    <!-- <a href="#" class="hidden-md hidden-lg" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a> -->
    
    <!-- Navbar Right Menu -->
    <div class="navbar-left">
      <ul class="nav navbar-nav">
        <!--//LOGO-EMPRESA//-->
        <a href="http://www.bailac.cl" class="logo-empresa">
          <img src="assets/img/logo.png" alt="" />
        </a>        
        
        <ul class="nav navbar-nav navbar-right">
         <li>Empresa: <b><?=utf8_encode($empresa)?></b></li>
         <li>Faena: <b><?= $nombrefaena ?></b></li>
         <li>Usuario: <b><?= $nombreusuario ?></b></li>
         <li>Perfil: <b><?= utf8_encode($perfilactivo->nombre) ?></b></li>
         <li class="hidden tiempo-sesion"><small>Sesión expira en <span id="tiempo_sesion"></span></small></li>
      </ul>
    </div>
    <div class="navbar-right">
      <ul class="nav navbar-nav">
          <li class="dropdown notifications-menu" id="ayuda">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-info-circle"></i>
            </a>
            <ul class="dropdown-menu">
              <?php if($link=='monitoreo' || $link=='gps-2d'){ ?>
                <!--// Nomenclatura de colores //-->
                <li class="header"><b><center>Nomenclatura de colores</center></b></li>
                <li>
                  <!-- inner menu: contains the actual data -->
                  <ul class="menu expanded">
                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Alcanza o excede el umbral definido">
                      <div class="neumico icon-neum-red pull-left">&nbsp;</div>
                      <h5>Alarma de Temperatura</h5>
                      </a>
                    </li>
                      
                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="La T&deg; está 5% por debajo del umbral de T&deg;">
                      <div class="neumico icon-neum-lilac pull-left">&nbsp;</div>
                      <h5>Alerta de Temperatura</h5>
                      </a>
                    </li>
                      
                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Alcanza o excede el umbral definido">
                      <div class="neumico icon-neum-orange pull-left">&nbsp;</div>
                      <h5>Alerta de Presión Alta</h5>
                      </a>
                    </li>
                      
                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Alcanza o excede el umbral definido">
                      <div class="neumico icon-neum-yellow pull-left">&nbsp;</div>
                      <h5>Alerta de Presión Baja</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="El sensor emitiendo de manera normal">
                      <div class="neumico icon-neum-black pull-left">&nbsp;</div>
                      <h5>Sensor Emitiendo</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="El sensor no emite desde al menos una hora">
                      <div class="neumico icon-neum-gray pull-left">&nbsp;</div>
                      <h5>Sensor no emitiendo</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="No tiene sensor configurado">
                      <div class="neumico icon-neum-none pull-left">&nbsp;</div>
                      <h5>Sin Sensor</h5>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } else if($link=='grafico-velocidad'){ ?>
                <!--// Nomenclatura gps //-->
                <li class="header"><b><center>Nomenclatura GPS</center></b></li>
                <li>
                  <ul class="menu expanded">

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Inicio del recorrido">
                      <div class="gpsico icon-inicio pull-left">&nbsp;</div>
                      <h5>Inicio del recorrido</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Su velocidad no sobrepasa el límite establecido.">
                      <div class="gpsico icon-arrow-normal pull-left">&nbsp;</div>
                      <h5>Movimiento normal</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Su velocidad sobrepasa el límite establecido.">
                      <div class="gpsico icon-arrow-exceso pull-left">&nbsp;</div>
                      <h5>Exceso de velocidad</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Fin del recorrido">
                      <div class="gpsico icon-fin pull-left">&nbsp;</div>
                      <h5>Finalización del recorrido</h5>
                      </a>
                    </li>

                  </ul>
                </li>
              <?php } else if($link=='asignar-sensores'){ ?>
                <li class="header"><b><center>Nomenclatura Asignación de sensores</center></b></li>
                <li>
                  <ul class="menu expanded">

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Neumático disponible sin sensor asignado">
                      <div class="ndssa pull-left">&nbsp;</div>
                      <h5 style="white-space:normal; padding-left:65px">Neumático disponible sin sensor asignado</h5>
                      </a>
                    </li>

                    <li>
                      <a href="#" data-toggle="tooltip" data-placement="bottom" title="Neumático instalado en equipo">
                      <div class="nie pull-left">&nbsp;</div>
                      <h5 style="white-space:normal; padding-left:65px">Neumático disponible con sensor asignado</h5>
                      </a>
                    </li>

                  </ul>
                </li>
              <?php } else{ ?>
                <script type="text/javascript">
                  $("#ayuda").hide();
                </script>
              <?php } ?>
            </ul>
          </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
        <li role="separator">&nbsp;&nbsp;&nbsp;&nbsp;</li>
      </ul>
    </div>
  </nav>
</header>

<script type="text/javascript">
 var fecha = '';
 var tsesion = '';
 var h=0; var m=0; var s=0;
 var th=0; tm=0; ts=0; interval1=0; interval2=0;

  function sync_hora(){
    $.get('reloj.php', function(data){
      if(data){
        fecha = data.fecha;
        h = data.hora;
        m = data.min;
        s = data.seg;

        $("#reloj").html(
          '<span class="fecha">' + fecha + '</span><br/>'+
          '<span class="hora">' + h + ':' + m + ':' + s + ' hrs</span>'
        );

        <?php if($_SESSION[session_id()]['expira']>0){ ?>
          th=data.sesion.hor;
          tm=data.sesion.min;
          ts=data.sesion.seg;

          if(th==tm && tm==ts && th==0){$("#tiempo_sesion").html(''); window.location = '<?=$GLOBALS['LOGIN']?>';}
        <?php } ?>

        if(data.notificaciones.length>0){
          $.each(data.notificaciones, function(i,o){
            crearNotificacion(o.t, o.i, o.b);
          });
        }
      }
    });
  }

  function actualizar_fecha_hora(){
    s++;
    if(s>59){ m++; s=0; if(m>59){ m=0; h++; if(h>24){ h=1;} } }
    $("#reloj").html(
      '<span class="fecha">' + fecha + '</span><br/>'+
      '<span class="hora">' + (parseInt(h)<10?'0'+parseInt(h):h) + ':' + (parseInt(m)<10?'0'+parseInt(m):m) + ':' + (parseInt(s)<10?'0'+parseInt(s):s) + ' hrs</span>'
    );

    <?php if($_SESSION[session_id()]['expira']>0){ ?>
      if(th==tm && tm==ts && th==0){$("#tiempo_sesion").html(''); window.location = '<?=$GLOBALS['SESSION_TIMEOUT']?>';}
      else{
        ts--;
        if(th==tm && tm==ts && th==0){$("#tiempo_sesion").html(''); window.location = '<?=$GLOBALS['SESSION_TIMEOUT']?>';}
        if(ts<0){tm--;if(tm>=0)ts=59;if(tm<=0){if(th>0) tm=59;th--;if(th<=24){th=0;}}}

        var tiempo = '';
        if(th>0){tiempo  = (th < 10 ? '0' + th : th) + '<small>h</small> ';}
        if(tm>0){tiempo += (tm < 10 ? '0' + tm : tm) + '<small>m</small> ';}
        tiempo += (ts < 10 ? '0' + ts : ts) + '<small>s</small>';

        $("#tiempo_sesion").html('<span>' + tiempo + '</span>'); 

        var segs = (th*3600)+(tm*60)+ts;
        if(segs<0) segs = 0;
        // console.log(segs + '///' + <?=$GLOBALS['SESSION_WARNING']?>);

        if($("#contador").length>0){
          $("#contador").text(segs+' segundos');
        }

        if(segs == <?=$GLOBALS['SESSION_WARNING']?>){
          $("li.tiempo-sesion").removeClass("hidden");
          crearNotificacion(
            'Su sesión se cerrará',
            '<?=$GLOBALS['ASSETS']?>img/reloj.png',
            'Su sesión se cerrará automáticamente dentro de '+segs+' segundos.',
            'focus');
          swal({
            title:'Su sesión se cerrará',
            text:'<i class="fa fa-clock-o fa-5x" aria-hidden="true"></i><br/><br/>'+
            'Su sesión se cerrará automáticamente dentro de <span id="contador">'+segs+' segundos</span>, si desea extender el tiempo por favor presione el botón \'Extender\' para poder continuar sin re-loguear',
            html: true,
            type:'',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: 'Extender',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false,
          }, function(isConfirm){
            // console.log(isConfirm);
            if(isConfirm){
              clearInterval(interval1);
              $.post('extender-sesion.php', function(data){
                $("li.tiempo-sesion").addClass("hidden"); 
                swal(data);
                if(data.type == 'success'){
                  crearNotificacion(
                    data.title,
                    '<?=$GLOBALS['ASSETS']?>img/check.png',
                    data.text);
                }

                //Vuelve a reactivar el contador y cargar el timer interno
                sync_hora();
                interval1 = setInterval(actualizar_fecha_hora, 1000);
              });
            }
          });
        }
      }
    <?php } ?>
  }

 sync_hora();
 interval1 = setInterval(actualizar_fecha_hora, 1000);
 setInterval(sync_hora, 60000);
</script>