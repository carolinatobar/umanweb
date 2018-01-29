<?php
class Neumatico{

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



  //-----------------------------------
  // Funciones Nomenclaturas Posiciones
  //-----------------------------------

  public function listar(){
    $data   = $this->_db->query('SELECT * FROM uman_neumaticos ORDER BY MARCA ASC, NUMIDENTI ASC');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }


  public function listar_disponibles(){
    $data   = $this->_db->query("SELECT * FROM uman_neumaticos WHERE ESTADO = 'DISPONIBLE' ORDER BY MARCA ASC, NUMIDENTI ASC");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function listar_todos(){
    $data   = $this->_db->query("SELECT * FROM uman_neumaticos ORDER BY MARCA ASC, NUMIDENTI ASC");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function listar_con_sensor_y_disponibles(){
    $data   = $this->_db->query("SELECT
                                  n.ID_NEUMATICO,
                                  n.NUMIDENTI,
                                  n.ID_PLANTILLA,
                                  n.ESTADO,
                                  n.MARCA,
                                  n.MODELO,
                                  n.DIMENSION,
                                  n.COMPUESTO,
                                  n.NUMEROFUEGO,
                                  n.ID_SENSOR,
                                  s.CODSENSOR,
                                  s.TIPO
                                FROM
                                  uman_neumaticos AS n
                                  INNER JOIN uman_sensores AS s ON n.ID_SENSOR = s.ID_SENSOR
                                WHERE
                                  n.ID_NEUMATICO NOT IN ( SELECT nc.ID_NEUMATICO FROM uman_neumatico_camion as nc)
                                ORDER BY
                                  n.ID_NEUMATICO ASC,
                                  n.MARCA ASC,
                                  n.NUMIDENTI ASC");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }


  public function get_full($id){
    $data   = $this->_db->query("SELECT
                                  n.ID_NEUMATICO,
                                  n.NUMIDENTI,
                                  n.ID_PLANTILLA,
                                  n.ESTADO,
                                  n.MARCA,
                                  n.MODELO,
                                  n.DIMENSION,
                                  n.COMPUESTO,
                                  n.NUMEROFUEGO,
                                  n.ID_SENSOR,
                                  p.MARCA AS P_MARCA,
                                  p.MODELO AS P_MODELO,
                                  p.DIMENSION AS P_DIMENSION,
                                  p.TEMPMAX AS P_TEMPMAX,
                                  p.PRESMAX AS P_PRESMAX,
                                  p.PRESMIN AS P_PRESMIN,
                                  p.EJE AS P_EJE,
                                  p.COMPUESTO AS P_COMPUESTO,
                                  p.SENSOR AS P_SENSOR,
                                  s.CODSENSOR AS S_CODESENSOR,
                                  s.TIPO AS S_TIPO,
                                  s.ID_SENSOR AS S_ID_SENSOR 
                                  FROM
                                  uman_neumaticos AS n
                                  LEFT JOIN uman_plantilla AS p ON n.ID_PLANTILLA = p.ID_PLANTILLA
                                  LEFT JOIN uman_sensores AS s ON n.ID_SENSOR = s.ID_SENSOR
                                  WHERE n.ID_NEUMATICO = {$id}");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function get_master($id = null){

    $WHERE  = $id != null ? "WHERE n.ID_NEUMATICO = {$id}" : '';

    $data   = $this->_db->query("SELECT
                                  n.ID_NEUMATICO,
                                  n.NUMIDENTI,
                                  n.NUMEROFUEGO,
                                  n.ID_PLANTILLA,
                                  n.MARCA,
                                  n.ID_SENSOR,
                                  n.ESTADO,
                                  nc.ID_EQUIPO,
                                  nc.ID_POSICION,
                                  s.CODSENSOR,
                                  s.TIPO,
                                  c.NUMCAMION,
                                  p.NOMENCLATURA
                                FROM
                                  uman_neumaticos AS n
                                  LEFT JOIN uman_neumatico_camion AS nc ON  n.ID_NEUMATICO  = nc.ID_NEUMATICO
                                  LEFT JOIN uman_sensores         AS s ON   n.ID_SENSOR     = s.ID_SENSOR
                                  LEFT JOIN uman_camion           AS c ON   nc.ID_EQUIPO    = c.ID_CAMION
                                  LEFT JOIN uman_posicion         AS p ON   nc.ID_POSICION  = p.POSICION
                                {$WHERE}
                                ORDER BY n.ESTADO ASC, n.ID_SENSOR DESC");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }




  public function modificar($id, $fields = array()){
    $data   = $this->_db->update('uman_neumaticos', $fields, $id, 'ID_NEUMATICO');
    return !$data->error();
    // return $data;
  }

  public function eliminar($id){
    $data   = $this->_db->delete('uman_neumaticos', $id, 'ID_NEUMATICO');
    return !$data->error();
  }


  // public function insertar($fields = array()){
  //
  //   $data   = $this->_db->insertar('uman_cajauman', $fields, 'ID_CAJAUMAN');
  //   return !$data->error();
  // }



  public function check_template($eje, $marca, $modelo, $dimension, $compuesto, $sensor='Interno'){

    $alaprimera = false;
    $sql = " SELECT uman_plantilla.ID_PLANTILLA
      FROM uman_plantilla
      WHERE
        uman_plantilla.MARCA = '{$marca}' AND 
        uman_plantilla.MODELO = '{$modelo}' AND 
        uman_plantilla.DIMENSION = '{$dimension}' AND 
        uman_plantilla.SENSOR = '{$sensor}' AND 
        uman_plantilla.COMPUESTO = '{$compuesto}' AND 
        uman_plantilla.EJE = {$eje}";
    // echo "$sql \n";
    $data   = $this->_db->query($sql);

    $arr_plantillas    = $data->results();

    foreach ( $arr_plantillas as $arrax ) {
      $alaprimera=true;
    }

    $sql = "SELECT uman_plantilla.ID_PLANTILLA
      FROM uman_plantilla
      WHERE
        ( uman_plantilla.MARCA = '{$marca}' OR uman_plantilla.MARCA = '' ) AND 
        ( uman_plantilla.MODELO = '{$modelo}' OR uman_plantilla.MODELO = '' ) AND 
        ( uman_plantilla.DIMENSION = '{$dimension}' OR uman_plantilla.DIMENSION = '' ) AND 
        ( uman_plantilla.SENSOR = '{$sensor}' OR uman_plantilla.SENSOR = '' ) AND 
        ( uman_plantilla.COMPUESTO = '{$compuesto}' OR uman_plantilla.COMPUESTO = '' ) AND
        ( uman_plantilla.EJE = '{$eje}' OR uman_plantilla.EJE = '0' )";
    // echo "$sql \n";
    $data2   = $this->_db->query($sql);
    
    if ( $alaprimera ) {
      if(!$data->error()){
        return $data->results();
      }
      else{
        return false;
      }
    } else {
      if(!$data2->error()){
        return $data2->results();
      }
      else{
        return false;
      }
    }
  }

  public function obtenerMarcas($order='ASC'){
    if(!in_array(strtoupper($order), ['ASC','DESC'])) $order = 'ASC';

    $sql = sprintf("SELECT * FROM uman_marcaneumatico 
      WHERE estado=1 ORDER BY nombre %s;", $order);

    $data   = $this->_db->query($sql);

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function obtenerModelos($order='ASC'){
    if(!in_array(strtoupper($order), ['ASC','DESC'])) $order = 'ASC';

    $sql = sprintf("SELECT * FROM uman_modeloneumatico 
      WHERE estado=1 ORDER BY nombre %s;", $order);

    $data   = $this->_db->query($sql);

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function obtenerDimensiones($order='ASC'){
    if(!in_array(strtoupper($order), ['ASC','DESC'])) $order = 'ASC';

    $sql = sprintf("SELECT * FROM uman_dimensionneumatico 
      WHERE estado=1 ORDER BY dimension %s;", $order);

    $data   = $this->_db->query($sql);

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function obtenerCompuestos($order='ASC'){
    if(!in_array(strtoupper($order), ['ASC','DESC'])) $order = 'ASC';

    $sql = sprintf("SELECT * FROM uman_compuestoneumatico 
      WHERE estado=1 ORDER BY nombre %s;", $order);

    $data   = $this->_db->query($sql);

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }






}//end class
