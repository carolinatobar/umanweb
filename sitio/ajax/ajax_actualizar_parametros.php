<?php

require '../autoload.php';

$obj_gnr  = new General();

//  POST  "item="+form+"&value="+input;
// $item   = $_POST['item'];
// $value  = $_POST['value'];
$data = array();
if(isset($_POST)){
  $db = DB::getInstance();

  foreach($_POST as $key => $value){
    $param = $db->query(sprintf("SELECT * FROM uman_parametros 
      WHERE NOMBREPARAMETRO='%s';", $key));

    if($param->count() <= 0){
      //No existe parámetro, por lo tanto se debe insertar
      $updated = $db->query(sprintf("INSERT INTO uman_parametros 
        VALUES(null, '%s', '%s');", $key, $value));
    }
    else{ 
      //Como ya existe, sólo se actualiza el valor correspondiente
      $updated = $db->query(sprintf("UPDATE uman_parametros 
        SET VALOR1='%s' 
        WHERE NOMBREPARAMETRO='%s';", $value, $key));
    }

    $data = array(
      'title'=>'Parámetros actualizados',
      'text'=>'Los parámetros se han actualizado correctamente.',
      'type'=>'success'
    );
  }
  Core::actualizar_umanblue();
}
else {
  $data = array(
    'title'=>'Error',
    'text'=>'No se ha podido realizar el cambio porque ha enviado una consulta vacía.',
    'type'=>'error'
  );
}

//REALIZANDO LOOP DE GUARDADO
//--------------------------
// if(isset($item) && $item != ''){

//   $array_update = array();
//   $table        = 'uman_parametros_fallas';
//   $column_name  = 'ID';
//   $column_val   = 1;

//   switch($item){

//     case 'atm':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'atm'";
//       break;
//     case 'maxvelocidad':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'maxvelocidad'";
//       break;
//     case 'sampleogps':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'sampleogps'";
//       break;
//     case 'timeout':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'timeout'";
//       break;
//     case 'verneumaticosegun':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'verneumaticosegun'";
//       break;
//     case 'tipoesquemamonitoreo':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'tipoesquemamonitoreo'";
//       break;
//     case 'refresco':        
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'refresco'";
//       break;
//     case 'mapapi':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'mapapi'";
//       break;
//     case 'zoom':
//       $array_update = array("VALOR1" => $value);
//       $table        = 'uman_parametros';
//       $column_name  = 'NOMBREPARAMETRO';
//       $column_val   = "'zoom'";
//       break;
//     case 'tiempo_falla':    $array_update = array("TIEMPOFALLA" => $value);           break;
//     case 'temperatura':     $array_update = array("DESVIOTEMP" => $value);            break;
//     case 'presion_alta':    $array_update = array("DESVIOPRESMAX" => $value);         break;
//     case 'presion_baja':    $array_update = array("DESVIOPRESMIN" => $value);         break;
//     case 'presion_minima':  $array_update = array("PRESIONMINIMA" => $value);         break;
//   }


//   $data = array();

//   if(count($array_update) > 0){

//     try{

//       //Verificar si existe el parámetro, de lo contrario insertar predefinido o con valor recibido en request.
//       $db = DB::getInstance();
//       $param = $db->query("SELECT * FROM uman_parametros WHERE NOMBREPARAMETRO='$item';");

//       if($param->count() <= 0){
//         //No existe parámetro, por lo tanto se debe insertar
//         $updated = $db->query("INSERT INTO uman_parametros VALUES(null, '$item', '$value');");
//       }
//       else{ 
//         //Como ya existe, sólo se actualiza el valor correspondiente
//         $updated  = $obj_gnr->update_parametro($table, $array_update, $column_val, $column_name);
//       }

//       Core::actualizar_umanblue();

//       $data = array(
//         'title'=>'Parámetro actualizado',
//         'text'=>'El parámetro se actualizado correctamente.',
//         'type'=>'success',
//         'insert'=>"INSERT INTO uman_parametros VALUES(null, '$item', '$value');",
//         'select'=>"SELECT * FROM uman_parametros WHERE NOMBREPARAMETRO='$item';"
//       );
//     } catch (Exception $e){
//       $data = array(
//         'title'=>'Error',
//         'text'=>$e->getMessage(),
//         'type'=>'error'
//       );
//     }

//   } else {
//     $data = array(
//       'title'=>'Datos no permitidos',
//       'text'=>'No se ha podido realizar el cambio porque los datos enviado no fueron admitidos.',
//       'type'=>'error'
//     );
//   }


// } else {
//   $data = array(
//     'title'=>'Error',
//     'text'=>'No se ha podido realizar el cambio porque ha enviado una consulta vacía.',
//     'type'=>'error'
//   );
// }

header("Content-type: application/json");
echo json_encode($data);
