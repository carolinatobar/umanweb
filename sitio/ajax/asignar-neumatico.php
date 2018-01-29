<?php
    require '../autoload.php';

    $id_neum   = isset($_POST['id_neumatico']) ? $_POST['id_neumatico'] : null;
    $id_equipo = isset($_POST['id_equipo'])    ? $_POST['id_equipo']    : null;
    $posicion  = isset($_POST['posicion'])     ? $_POST['posicion']     : null;

    if($id_neum != null && $id_equipo != null && $posicion != null){
        if(is_numeric($id_neum) && is_numeric($id_equipo) && is_numeric($posicion)){
            $db = DB::getInstance();

            $sql = sprintf("SELECT * 
             FROM uman_neumatico_camion 
             WHERE ID_EQUIPO=%d AND ID_POSICION=%d",
             $id_equipo, $posicion);
            $res = $db->query($sql);

            if($res->count()>0){
                header("Content-type: text/json");
                echo json_encode(array('response'=>'error', 'data'=>'El equipo ya posee un neumático asociado en la posición '.$posicion));
                exit();
            }

            $sql = sprintf("INSERT INTO uman_neumatico_camion 
             (ID, ID_EQUIPO, ID_POSICION, ID_NEUMATICO, FECHA) 
             VALUES (null, %d, %d, %d, NOW());", $id_equipo, $posicion, $id_neum);
            $res = $db->query($sql);

            $sql = sprintf("UPDATE uman_neumaticos SET ESTADO='USO' WHERE ID_NEUMATICO=%d",$id_neum);
            $res2 = $db->query($sql);

            if($res && $res2){
                include_once("ajax_actualizar_umanblue.php");
                header("Content-type: text/json");
                echo json_encode(array('response'=>'success', 'data'=>'El neumático se ha asociado al equipo.'));
            }            
            else{
                $error = $db->getPDO()->errorInfo;
                $error = $error[2];
                header("Content-type: text/json");
                echo json_encode(array('response'=>'error', 'data'=>$error));
            }
        }
    }

?>