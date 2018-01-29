<?php 
    require '../autoload.php';

    $acc = new Acceso(true);

    $gen = new General();
    $nomenclatura = $gen->getNomenclaturas();

    // var_dump($_POST);exit();

    $equipo  = isset($_POST['equipo']) ? $_POST['equipo'] : null;
    $rem     = isset($_POST['rem']) ? $_POST['rem'] : null;
    $put     = isset($_POST['put']) ? $_POST['put'] : null;

    // print_r(['EQUIPO'=>$equipo,'REM'=>$rem,'PUT'=>$put]);exit();

    $data = array(
        'title'=>'Asignar Neumáticos',
        'text'=>'',
        'type'=>'error',
        'html'=>true,
    );

    if($equipo != null){
        $error = false;
        $mensaje = '';
        if(is_numeric($equipo)){
            $status = array();
            $db = DB::getInstance();

            if($rem != null){
                foreach($rem as $x){
                    $sql = sprintf("DELETE FROM uman_neumatico_camion WHERE ID_NEUMATICO=%d;", $x['id']);
                    // echo "$sql\n";
                    $rem1 = $db->query($sql);
        
                    $sql = sprintf("UPDATE uman_neumaticos SET ESTADO='DISPONIBLE', ID_PLANTILLA=0 WHERE ID_NEUMATICO=%d",$x['id']);
                    // echo "$sql\n";
                    $rem2 = $db->query($sql);

                    $delete = ($rem1 && $rem2);
                    $status[] = array('DELETE'=>$delete, 'STATUS'=>($delete ? 'OK' : $db->getPDO()->errorInfo[2]));
                    // 18_01_2018 CT Se repara siguiente error, al retirar 2 o más neumáticos y guardar, sólo saca un neumático y no los neumáticos seleccionados.
		    (new Historial())->retiro_neumatico($x['id'], $equipo, $x['fecha']);
                }
            }

            if($put != null){
                foreach($put as $x){
                    $sql = sprintf("SELECT * 
                    FROM uman_neumatico_camion 
                    WHERE ID_EQUIPO=%d AND ID_POSICION=%d",
                    $equipo, $x['posicion']);
                    // echo "$sql\n";
                    $res = $db->query($sql);
       
                    if($res->count()>0){
                        $mensaje .= '<br/>El equipo ya posee un neumático asociado en la posición '.$nomenclatura[$x['posicion']]."\n";
                    }
                    else{
                        $obj_eqp  = new Equipo();
                        $obj_neu  = new Neumatico();

                        //Buscar y asignar Plantilla
                        $mtr_neu   = $obj_neu->get_full($x['id']);
                        $plantilla = 0;

                        if(isset($mtr_neu)){
                            $marca     = isset($mtr_neu[0]->MARCA)     && $mtr_neu[0]->MARCA     != '' ? $mtr_neu[0]->MARCA      : '';
                            $modelo    = isset($mtr_neu[0]->MODELO)    && $mtr_neu[0]->MODELO    != '' ? $mtr_neu[0]->MODELO     : '';
                            $dimension = isset($mtr_neu[0]->DIMENSION) && $mtr_neu[0]->DIMENSION != '' ? $mtr_neu[0]->DIMENSION  : '';
                            $compuesto = isset($mtr_neu[0]->COMPUESTO) && $mtr_neu[0]->COMPUESTO != '' ? $mtr_neu[0]->COMPUESTO  : '';
                            $sensor    = isset($mtr_neu[0]->S_TIPO)    && $mtr_neu[0]->S_TIPO    != '' ? $mtr_neu[0]->S_TIPO     : '';

                            // var_dump([$x['eje'], $marca, $modelo, $dimension, $compuesto, $sensor]);
                            $template = $obj_neu->check_template($x['eje'], $marca, $modelo, $dimension, $compuesto, $sensor);

                            // var_dump($template);

                            if(isset($template[0]->ID_PLANTILLA) && $template[0]->ID_PLANTILLA != ''){
                                $plantilla = $template[0]->ID_PLANTILLA;
                            }
                        }

                        if($plantilla == 0) $mensaje = '<br/>No existe plantilla para este neumático, por favor asegúrese de crear una y asignarla posteriormente.';

                        //Verificar si el nuevo neumático está asignado a otra posición,
                        //si lo está, lo asigna a la nueva.
                        $sql = sprintf("SELECT * 
                        FROM uman_neumatico_camion 
                        WHERE ID_EQUIPO=%d AND ID_NEUMATICO=%d", $equipo, $x['id']);
                        // echo "$sql\n";
                        $res = $db->query($sql);
                        // var_dump($res->count());

                        if($res->count() > 0){
                            //Sólo se mueve de posición
                            $sql = sprintf("UPDATE uman_neumatico_camion 
                            SET ID_POSICION=%d WHERE ID_EQUIPO=%d AND ID_NEUMATICO=%d;", 
                            $x['posicion'], $equipo, $x['id']);
                            // echo "$sql\n";
                            $put1 = $db->query($sql);
                            $status[] = array('UPDATE'=>$put1, 'STATUS'=>($put1 ? 'OK' : $db->getPDO()->errorInfo[2]));
                        }
                        else{
                            $sql = sprintf("INSERT INTO uman_neumatico_camion 
                                (ID, ID_EQUIPO, ID_POSICION, ID_NEUMATICO, FECHA) 
                                VALUES (null, %d, %d, %d, NOW());", 
                                $equipo, $x['posicion'], $x['id']);
                            // echo "$sql\n";
                            $put1 = $db->query($sql);
                            $status[] = array('INSERT'=>$insert, 'STATUS'=>($put1 ? 'OK' : $db->getPDO()->errorInfo[2]));
                        }

                        $res = $obj_neu->modificar($x['id'], array("ID_PLANTILLA" => $plantilla, "ESTADO" => 'USO'));
                        $res = $obj_eqp->modificar($equipo, array("NEUM".$x['posicion'] => $x['id']));
                        $res = $obj_neu->modificar($x['id'], array("ESTADO" => 'USO')); 
                        (new Historial())->instalacion_neumatico($x['id'], $equipo, $x['posicion'], $x['fecha']);
                    }
                }
            }

            $obj_eqp_n  	= new Equipo();
            $arr_camion   	= $obj_eqp_n->listar();
            
            Core::actualizar_umanblue();

            foreach($status as $s){
                if($s['STATUS'] != 'OK'){
                    $mensaje .= $s['STATUS']."<br/>";
                    $error = true;
                }
            }

            header("Content-type: text/json");
            if($error){
                $data['type'] = 'error';
                $data['text'] = 'Han ocurrido algunos errores. <br/>'.$mensaje;
            }
            else{ 
                $data['type'] = 'success';
                $data['text'] = 'Todas las operaciones se han realizado correctamente. <br/>'.$mensaje;
            }

            echo json_encode($data);
        }
    }

?>