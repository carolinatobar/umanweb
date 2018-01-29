<?php

include_once '../class/Core.php';
require_once '../class/Equipo.php';

$obj_eqp  = new Equipo();

//  v_equipo=92&hid_ipt_1_nc=&hid_ipt_1_n=&hid_ipt_2_nc=&hid_ipt_2_n=&hid_ipt_3_nc=&hid_ipt_3_n=&hid_ipt_4_nc=12&hid_ipt_4_n=17&hid_ipt_5_nc=&hid_ipt_5_n=&hid_ipt_6_nc=13&hid_ipt_6_n=18

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



if($equipo != ''){

  $return = array();

  foreach ($arr as $pos => $data) {

    $pos_id = $pos;
    $rel_id = $data['nc'];  //id de relacion neumatico camion
    $neu_id = $data['n'];   //id neumatico

    // relacion vacio - neumatico exite
    if($rel_id == '' && $neu_id != ''){

      //Insertar Neumatico
      $array_insert['ID_EQUIPO']    = $equipo;
      $array_insert['ID_POSICION']  = $pos_id;
      $array_insert['ID_NEUMATICO'] = $neu_id;

      $result   = $obj_eqp->instalar_neumatico($array_insert) ? 'OK' : 'ERROR';
      $return[] = $result;

    }
    // relacion existe - neumatico existe
    elseif($rel_id != '' && $neu_id != ''){

      //Actualizar Neumático
      $array_update['ID_NEUMATICO'] = $neu_id;

      $result   = $obj_eqp->cambiar_neumatico($rel_id, $array_update) ? 'OK' : 'ERROR';
      $return[] = $result;

    }
    // relacion existe - neumatico vacio
    elseif($rel_id != '' && $neu_id == ''){

      //Retirar Neumatico
      $result   = $obj_eqp->retirar_neumatico($rel_id) ? 'OK' : 'ERROR';
      $return[] = $result;

    }

  }

  echo "OK";


} else {
  echo "NO_POST";
}
