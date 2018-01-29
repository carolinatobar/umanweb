<?php
class Historial{

  private $_db,
          $_pdo,
          $_schema,
          $_data,
          $_id,
          $_code,
          $_desc,
          $_stt;

  public function __construct($id = null){
    $this->_db      = DB::getInstance();
    $this->_pdo     = $this->_db->getPDO();
  }


  public function insertar($fields = array()){
    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }


#region sensores
  public function creacion_sensor ( $sensor, $fecha=null) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ACCION']         = "Sensor creado en sistema";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function instalacion_sensor ( $sensor , $neumatico, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ID_NEUMATICO']   = $neumatico;
    $fields['ACCION']         = "Sensor instalado en neumatico";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function modificacion_sensor ( $sensor, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ACCION']         = "Sensor modificado";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function eliminacion_sensor ( $sensor, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ACCION']         = "Sensor eliminado";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function retiro_sensor ( $sensor , $neumatico, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ID_NEUMATICO']   = $neumatico;
    $fields['ACCION']         = "Sensor retirado de neumatico";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function baja_sensor ( $sensor, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ACCION']         = "Sensor dado de baja";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }
#endregion

#region neumáticos
  public function creacion_neumatico ( $neumatico, $fecha= null ) {

    $fields['ID_NEUMATICO']   = $neumatico;
    $fields['ACCION']         = "Neumatico creado en sistema";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function eliminacion_neumatico ( $neumatico, $fecha= null ) {
    
        $fields['ID_NEUMATICO']   = $neumatico;
        $fields['ACCION']         = "Neumatico eliminado del sistema";
        $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
        if($fecha)
          $fields['FECHA']        = $fecha; 
    
        $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
        return !$data->error();
      }

  public function modificacion_neumatico ( $neumatico, $fecha= null ) {
        $fields['ID_NEUMATICO']   = $neumatico;
        $fields['ACCION']         = "Neumatico modificado en sistema";
        $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
        if($fecha)
          $fields['FECHA']        = $fecha; 
    
        $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
        return !$data->error();
      }

  public function instalacion_neumatico ( $neumatico , $equipo , $posicion, $fecha=null ) {
    $data_sens                = $this->_db->query("SELECT ID_SENSOR FROM uman_neumaticos WHERE ID_NEUMATICO='$neumatico' LIMIT 1");
    $arr_sens                 = $data_sens->results();
    $id_sens                  = $arr_sens[0]->ID_SENSOR;

    $fields['ID_SENSOR']      = $id_sens;
    $fields['ID_NEUMATICO']   = $neumatico;
    $fields['ID_CAMION']      = $equipo;
    $fields['ID_POSICION']    = $posicion;
    $fields['ACCION']         = "Neumatico instalado en equipo";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function retiro_neumatico ( $sensor , $neumatico , $posicion, $fecha=null ) {

    $fields['ID_SENSOR']      = $sensor;
    $fields['ID_NEUMATICO']   = $neumatico;
    $fields['ID_CAMION']      = $equipo;
    $fields['ACCION']         = "Neumatico retirado de equipo";
    $fields['ID_USUARIO']     = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha)
      $fields['FECHA']        = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }
#endregion

#region plantillas
  public function crea_plantilla ( $fecha=null ) {
    // $fields['ID_NEUMATICO']   = '';
    // $fields['ID_SENSOR']      = '';
    // $fields['ID_CAMION']      = '';
    // $fields['ID_POSICION']    = '';
    $fields['ACCION']           = "Plantilla creada";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha) $fields['FECHA'] = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  } 

  public function borra_plantilla ( $id_plantilla, $fecha=null ) {
    // $fields['ID_NEUMATICO']   = '';
    // $fields['ID_SENSOR']      = '';
    // $fields['ID_CAMION']      = '';
    // $fields['ID_POSICION']    = '';
    $fields['ACCION']           = "Plantilla ID:{$id_plantilla} eliminada";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha) $fields['FECHA'] = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  } 

  public function modifica_plantilla ( $id_plantilla, $fecha=null ) {
    // $fields['ID_NEUMATICO']   = '';
    // $fields['ID_SENSOR']      = '';
    // $fields['ID_CAMION']      = '';
    // $fields['ID_POSICION']    = '';
    $fields['ACCION']           = "Plantilla ID:{$id_plantilla} modificada";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;
    if($fecha) $fields['FECHA'] = $fecha; 

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }
#endregion

#region usuarios
  public function creacion_usuario($id_usuario){
    $fields['ACCION']           = "Usuario creado";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function modificacion_usuario($id_usuario){
    $fields['ACCION']           = "Usuario modificado";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }

  public function cambio_clave($id_usuario){
    $fields['ACCION']           = "Cambio de clave de Usuario";
    $fields['ID_USUARIO']       = isset($_SESSION[session_id()]['id']) ? $_SESSION[session_id()]['id'] : -1;

    $data   = $this->_db->insertar('uman_historial', $fields, 'ID');
    return !$data->error();
  }
#endregion

function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

  public function listar_this($id,$tipo){
    
    if ( $tipo == "neumatico" ) {
      $where = "WHERE id_neumatico='{$id}'";
    } else if ( $tipo == "sensor" ) {
      $where = "WHERE id_sensor='{$id}'";
    } else if ( $tipo == "posicion" ) {
      $where = "WHERE posicion='{$id}'";
    } else if ( $tipo == "camion" ) {
      $where = "WHERE id_camion='{$id}'";
    } else if ( $tipo == NULL || $tipo = "" ) {
      $where = "";
    }

    $data   = $this->_db->query("SELECT * FROM uman_historial{$where}");


    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function listar_acciones(){
    $data = $this->_db->query("SELECT DISTINCT ACCION FROM uman_historial;");
    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }
//end class
}
