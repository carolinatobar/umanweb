<?php

require '../autoload.php';


$obj_eqp  = new Equipo();
$obj_neu  = new Neumatico();
$obj_hist = new Historial();


$equipo = isset($_POST['v_equipo']) && $_POST['v_equipo'] != '' ? $_POST['v_equipo'] : '';
$post   = $_POST;
$arr    = array();

array_shift($post);

if(isset($post) && count($post) > 0){
  //rearm array
  foreach ($post as $key => $value) {
    $aux  = explode("_", $key);
    $pos  = $aux[2];
    $data = $aux[3];
    $arr[$pos][$data] = $value;
  }
}



if ( $equipo != '' ) {

  $return = array();

  foreach ($arr as $pos => $data) {

    $pos_id = $pos;
    $rel_id = $data['nc'];  //id de relacion neumatico camion
    $neu_id = $data['n'];   //id neumatico
    $tpl_id = $data['t'];   //id plantilla
    $sen_id = $data['s'];

    $array_hist_insert['ID_CAMION']    = $equipo;
    $array_hist_insert['ID_POSICION']  = $pos_id;
    $array_hist_insert['ID_NEUMATICO'] = $neu_id;
    $array_hist_insert['ID_SENSOR']    = $sen_id;

    if ( $array_hist_insert['ID_NEUMATICO'] != '' && $array_hist_insert['ID_NEUMATICO'] != 0 ) {
      $array_hist_insert['ACCION'] = "ASIG_NEUM";
      //$obj_hist->insertar($array_hist_insert);
    } else {
      $array_hist_insert['ACCION'] = "ASIG_NEUM";
      //$obj_hist->insertar($array_hist_insert);
    }
    

    // relacion vacio - neumatico exite
    if ( $rel_id == '' && $neu_id != '' ) {

      //Insertar Neumatico
      $array_insert['ID_EQUIPO']    = $equipo;
      $array_insert['ID_POSICION']  = $pos_id;
      $array_insert['ID_NEUMATICO'] = $neu_id;

      $result   = $obj_eqp->instalar_neumatico($array_insert) ? 'OK' : 'ERROR';
      $return[] = $result;

      //Asignar Plantilla (ademas)
      $obj_neu->modificar($neu_id, array("ID_PLANTILLA" => $tpl_id, "ESTADO" => 'USO'));


    }
    // relacion existe - neumatico existe
    else if ( $rel_id != '' && $neu_id != '' ) {

      //Actualizar Neumático
      $array_update['ID_NEUMATICO'] = $neu_id;

      $result   = $obj_eqp->cambiar_neumatico($rel_id, $array_update) ? 'OK' : 'ERROR';
      $return[] = $result;

      //Asignar Plantilla (ademas)
      $obj_neu->modificar($neu_id, array("ID_PLANTILLA" => $tpl_id, "ESTADO" => 'USO'));

    }
    // relacion existe - neumatico vacio
    elseif($rel_id != '' && $neu_id == ''){

      //Retirar Neumatico
      $result   = $obj_eqp->retirar_neumatico($rel_id) ? 'OK' : 'ERROR';
      $return[] = $result;

      //Eliminar Plantilla (ademas)
      $obj_neu->modificar($neu_id, array( "ID_PLANTILLA" => 0 , "ESTADO" => 'DISPONIBLE' ));

    }

  }

  include_once("ajax_actualizar_umanblue.php");

  echo "OK";


} else {
  echo "NO_POST";
}
