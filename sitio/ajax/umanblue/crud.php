<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	
	$db  = DB::getInstance();

	$id      = isset($_POST['id'])      ? $_POST['id']      : NULL;
	$modo    = isset($_POST['modo'])    ? $_POST['modo']    : NULL;
	$codigo  = isset($_POST['codigo'])  ? $_POST['codigo']  : NULL;
	$ip_wifi = isset($_POST['ip_wifi']) ? $_POST['ip_wifi'] : NULL;
	$ip_lan  = isset($_POST['ip_lan'])  ? $_POST['ip_lan']  : NULL;
	$sim     = isset($_POST['sim'])     ? $_POST['sim']     : NULL;
	$equipo  = isset($_POST['equipo'])  ? $_POST['equipo']  : NULL;
	$cam     = isset($_POST['cam'])     ? $_POST['cam']     : NULL;
	$old_eq  = isset($_POST['old_eq'])  ? $_POST['old_eq']  : NULL;
	$old_ub  = isset($_POST['old_ub'])  ? $_POST['old_ub']  : NULL;

	$data = array(
		'type'=>'warning',
		'title'=>'NOT DEVELOPED YET',
		'text'=>''
	);

	if($modo == 'nueva'){
		$error = '';
		if($codigo == NULL) $error .= 'Debe ingresar el código de la UMAN Blue. <br/>';
		else if(!is_numeric($codigo)) 
			$error .= 'El campo código UMAN contiene un dato no válido. Asegúrese de ingresar sólo valores numéricos en este.<br/>';
		else{
			$sql = sprintf("SELECT * FROM uman_cajauman WHERE CODIGOCAJA=%d;", $codigo);
			$res = $db->query($sql);
			// echo "$sql\n";
			if($res->count() > 0) $error .= 'Ya existe uma caja con el código ingresado.<br/>';
		}
		
		if($equipo == NULL) $error .= 'Debe seleccionar un equipo disponible al que asignarle la caja. <br/>';
		else if(!is_numeric($equipo)) $error .= 'El campo equipo contiene un dato no válido. Asegúrese de haber selecciona un equipo disponible.<br/>';
		else{
			$sql = sprintf("SELECT * FROM uman_estado_umanblue WHERE ID_CAMION=%d;", $equipo);
			$res = $db->query($sql);
			if($res->count() > 0) $error .= 'Ya existe un equipo con caja asignada. <br/>';
		}

		if($error == ''){
			$transmision 		= "GPRS/WIFI/LAN";
			$sql = sprintf("INSERT INTO uman_cajauman VALUES (NULL,NULL,10000,%d,60,1,1,'%s','%s','%s','%s')", 
				$codigo, $transmision, $ip_wifi, $ip_lan, $sim);

			$res = $db->query($sql);

			if($res){
				$id_caja = $db->getPDO()->lastInsertId();

				$sql = sprintf("INSERT INTO uman_estado_umanblue VALUES (
					'',%d,%d,'','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','','','',
					'','','','','','','','','')", $codigo, $equipo);
				$db->query($sql); // 97 CAMPOS

				$sql = sprintf("UPDATE uman_camion SET ID_CAJAUMAN=%d WHERE ID_CAMION=%d;", $id_caja, $equipo);
				$db->query($sql);

				$sql = sprintf("UPDATE uman_sim SET ESTADO='En uso' WHERE ID=%d;", $sim);
				$db->query($sql);

				Core::actualizar_umanblue();

				$data = array(
					'title'=>'Nueva UMAN Blue',
					'text'=>'Se ha creado el registro con éxito',
					'type'=>'success',
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>$db->getPDO()->errorInfo[1],
					'text'=>$db->getPDO()->errorInfo[2],
				);
			}
		}
		else {
			$data = array(
				'title'=>'Faltan campos',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'editar'){
		$error = '';

		if($codigo == NULL) $error .= 'Debe ingresar el código de la UMAN Blue. <br/>';
		else if(!is_numeric($codigo)) 
			$error .= 'El campo código UMAN contiene un dato no válido. Asegúrese de ingresar sólo valores numéricos en este.<br/>';
		else{
			$sql = sprintf("SELECT * FROM uman_cajauman WHERE CODIGOCAJA=%d && ID_CAJAUMAN!=%d;", $codigo, $id);
			$res = $db->query($sql);
			if($res->count() > 0) $error .= 'No puede asignar el código "'.$codigo.'", ya que pertenece a otra caja.<br/>';
		}
		
		if($equipo == NULL) $error .= 'Debe seleccionar un equipo disponible al que asignarle la caja. <br/>';
		else if(!is_numeric($equipo)) 
			$error .= 'El campo equipo contiene un dato no válido. Asegúrese de haber seleccionado un equipo disponible.<br/>';

		if(!is_numeric($id)) $error .= 'El Id de la caja no contiene un dato válido, por favor verifique la información.<br/>';

		if($error == ''){
			$sql = sprintf("UPDATE uman_cajauman SET IP_WIFI='%s', IP_LAN='%s', ID_SIM=%d, CODIGOCAJA=%d WHERE ID_CAJAUMAN=%d;", 
				$ip_wifi, $ip_lan, $sim, $codigo, $id);
			$res = $db->query($sql);

			if($old_eq){
				$sql  = sprintf("UPDATE uman_camion SET ID_CAJAUMAN=0 WHERE NUMCAMION='%s';", $old_eq);
				$res3 = $db->query($sql);
			}
			$sql  = sprintf("UPDATE uman_camion SET ID_CAJAUMAN=%d WHERE ID_CAMION=%d;", $id, $equipo);
			$res3 = $db->query($sql);

			$sql  = sprintf("UPDATE uman_estado_umanblue SET UMAN_BLUE=%d WHERE ID_CAMION=%d AND UMAN_BLUE=%d;", $codigo, $equipo, $old_ub);
			$res3 = $db->query($sql);

			if($res){
				Core::actualizar_umanblue();
				$data = array(
					'type'=>'success',
					'title'=>'Modificar UMAN Blue',
					'text'=>'El registro se ha modificado con éxito',
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>$db->getPDO()->errorInfo[1],
					'text'=>$db->getPDO()->errorInfo[2],
				);
			}
		}
		else{
			$data = array(
				'title'=>'Faltan campos',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'eliminar'){
		$error = '';
		if(!is_numeric($id)) $error .= 'El Id de la caja no contiene un dato válido, por favor verifique la información.<br/>';

		if($error == ''){
			$sql = sprintf("SELECT ID_SIM FROM uman_cajauman WHERE ID_CAJAUMAN=%d;",$id);
			$res = $db->query($sql);
			$id_sim  = $res->count() > 0 ? $res->results()[0]->ID_SIM : -1;

			$sql  = sprintf("DELETE FROM uman_cajauman WHERE ID_CAJAUMAN=%d;", $id);
			$res  = $db->query($sql);
			$sql  = sprintf("UPDATE uman_sim SET ESTADO='Disponible' WHERE ID=%d;", $id_sim);
			$res2 = $db->query($sql);
			$sql  = sprintf("UPDATE uman_camion SET ID_CAJAUMAN=0 WHERE ID_CAJAUMAN=%d;", $id);
			$res3 = $db->query($sql);
			$sql  = sprintf("DELETE FROM uman_estado_umanblue WHERE UMAN_BLUE=%d;", $codigo);
			$res4 = $db->query($sql);

			if($res && $res2 && $res3 && $res4){
				Core::actualizar_umanblue();
				$data = array(
					'title'=>'Eliminar UMAN Blue',
					'text'=>'El registro ha sido eliminado con éxito',
					'type'=>'success',
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>$db->getPDO()->errorInfo[1],
					'text'=>$db->getPDO()->errorInfo[2],
				);	
			}
		}
		else{
			$data = array(
				'title'=>'Error de validación',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'obtener'){

	}
	else if($modo == 'equipos-disponibles'){
		$numcamion = $cam;
		$cam = ($cam != NULL) ? "OR NUMCAMION='{$cam}'" : '';
		$sql = "SELECT * FROM uman_camion WHERE ID_CAJAUMAN=0 $cam ORDER BY NUMCAMION";

		$res = $db->query($sql);

		echo '<option value="">Sin Equipo</option>';
		if($res->count() > 0){
			foreach($res->results() as $r){
				$selected = ($r->NUMCAMION == $numcamion) ? 'selected="selected"' : '';
				echo '<option value="'.$r->ID_CAMION.'" '.$selected.'>'.$r->NUMCAMION.'</option>';
			}
		}
		exit();
	}
	else if($modo == 'sim-disponibles'){
		$numsim = $sim;
		$sim = ($sim != NULL) ? "OR TELEFONO='{$sim}'" : '';
		$sql = "SELECT * FROM uman_sim WHERE ESTADO='Disponible' {$sim} ORDER BY TELEFONO, COMPANIA ASC;";

		$res = $db->query($sql);

		echo '<option value="">Sin SIM</option>';
		if($res->count() > 0){
			foreach($res->results() as $r){
				$selected = ($r->TELEFONO == $numsim) ? 'selected="selected"' : '';
				echo '<option value="'.$r->ID.'" '.$selected.'>'.$r->TELEFONO.'</option>';
			}
		}
		exit();
	}

	header("Content-Type: application/json");
	echo json_encode($data);

?>