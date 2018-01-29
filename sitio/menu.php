<?php
  $GLOBALS['useUpperCase']  = false;
  $GLOBALS['useUTF8Encode'] = true;
  require 'autoload.php';
  
  $mn_itm   = array(); //Items
  $mn_cnt   = array(); //Contenedor
  /*
   * Contiene los perfiles asignados al usuario, los cuales serán
   * listados en el menú lateral ofreciendo posibilidad de cambiar de perfil
   */
  // $perfiles = $_SESSION[session_id()]['perfiles'];
  $db2      = DB2::getInstance();
  $sql      = sprintf("SELECT m.*, me.etiqueta AS 'menu', me.icono AS 'icono_menu'  
    FROM uman_acceso_perfil ap INNER JOIN uman_modulo m ON ap.id_modulo=m.id
    INNER JOIN uman_menu me ON me.id=m.id_menu 
    WHERE ap.id_perfil=%d AND m.habilitado=1 
    ORDER BY ap.orden ASC", $perfilactivo->id);
  // die($sql);
  
  $modulo   = $db2->query($sql);
  if($modulo->count() > 0){
    foreach($modulo->results() as $m){
      $menu[$m->menu]['item'][$m->orden] = lnk('?s='.$m->link,$m->etiqueta,$m->icono);
      $menu[$m->menu]['icon']            = $m->icono_menu;
    }

    $root = array();
    foreach($menu as $key => $m){
        if($key == 'ROOT'){
          foreach($m['item'] as $itm){
            $root[] = $itm;
          }
        }
        else{
          $mn_cnt[] = drpdwn($key, $m['icon'], $m['item']);
        }
    }

    $mn_cnt = array_merge($mn_cnt, $root);
  }

  function lnk($url,$txt,$icn,$print=FALSE){
    if($GLOBALS['useUpperCase'])  $txt = strtoupper($txt);
    if($GLOBALS['useUTF8Encode']) $txt = utf8_encode($txt);
    $l  = '<li>';
    $l .= '<a href="'.$url.'" class="link-menu">';
    $l .= '<i class="fa '.$icn.'" aria-hidden="true"></i> <span>'.$txt.'</span>';
    $l .= '</a>';
    $l .= '</li>'."\n";

    if($print===TRUE) echo $l;
    else return $l;
  }

  function drpdwn($txt,$icn,$itm,$print=FALSE){
    if($GLOBALS['useUpperCase'])  $txt = strtoupper($txt);
    if($GLOBALS['useUTF8Encode']) $txt = utf8_encode($txt);
    $l  = '<li class="treeview">';
    $l .= ' <a href="#">';
    $l .= '  <i class="fa '.$icn.'"></i> <span>'.$txt.'</span>';
    $l .= '  <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
    $l .= ' </a>';
    $l .= ' <ul class="treeview-menu">';
    if(is_array($itm)){
      foreach($itm as $i){
        // $l .= lnk($i['url'],$i['txt'],'',FALSE);
        $l .= $i;
      }
    }
    else $l .= $itm;
    $l .= ' </ul>';
    $l .= '</li>';

    if($print===TRUE) echo $l;
    else return $l;
  }
?>
<!-- MENÚ LATERAL IZQUIERDO -->
<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php 
        foreach($mn_cnt as $m){
          echo $m;
        }
      ?>
    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>

<!-- MENÚ LATERAL DERECHO -->
<aside class="control-sidebar control-sidebar-dark">
  <div class="tab-content-menu">
    <!-- CAMBIO DE PERFIL -->  
      <?php
        if(count($perfiles) > 1)  {
          echo '<h3 class="control-sidebar-heading">Cambiar perfil</h3>';
          foreach($perfiles as $pa){
            if($perfilactivo->id == $pa->id)
              echo '<li>'.utf8_encode($pa->nombre).'</li>';
            else  
              echo '<li><a href="cambiar-perfil.php?perfil='.$pa->id.'">'.utf8_encode($pa->nombre).'</a></li>';
          }
        }
      ?>

    <!--//IDIOMAS//-->
      <h3 class="control-sidebar-heading">Seleccionar Idioma</h3>
      
      <li>
        <!-- <a class="dropdown__link" href="cambiar-idioma.php?idioma=es"> -->
          <img src="assets/img/flags/es.png"><span><?php print $texto_sitio["Espanol"]; ?></span>
        <!-- </a> -->
      </li>
      
      <li>
        <!-- <a class="dropdown__link" href="cambiar-idioma.php?idioma=po"> -->
          <img src="assets/img/flags/po.png"><span><?php print $texto_sitio["Portugues"]; ?></span>
        <!-- </a> -->
      </li>

      <li>
        <!-- <a class="dropdown__link" href="cambiar-idioma.php?idioma=en"> -->
          <img src="assets/img/flags/en.png"><span><?php print $texto_sitio["Ingles"]; ?></span>
        <!-- </a> -->
      </li>  

      <li>
        <!-- <a class="dropdown__link" href="cambiar-idioma.php?idioma=de"> -->
          <img src="assets/img/flags/de.png"><span><?php print $texto_sitio["Aleman"]; ?></span>
        <!-- </a> -->
      </li>

    <?php 
      if(count($faenas) > 1){
    ?>
    <!--//FAENAS//-->
    <h3 class="control-sidebar-heading">Cambiar Faena</h3>
    <?php
      $faenas = (new Usuario())->obtenerFaenas($_SESSION[session_id()]['id']);
      foreach($faenas as $f){
        if($f->nombre_db == $faena)
          echo "<li>{$f->nombre_faena}</li>\n";
        else
          echo "<li><a href='../faena-seleccionada.php?faena={$f->nombre_db}&withref'>{$f->nombre_faena}</a></li>\n";
      }
     } 
    ?>
    <h3 class="control-sidebar-heading">&nbsp;</h3>
  </div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
   immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
