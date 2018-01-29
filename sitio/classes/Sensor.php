<?php
class Sensor{

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
  //             Métodos
  //-----------------------------------


  // public function insertar($fields = array()){
  //   $data   = $this->_db->insertar('uman_camion', $fields, 'ID_CAMION');
  //   return !$data->error();
  // }


  // public function modificar($id, $fields = array()){
  //   $data   = $this->_db->update('uman_camion', $fields, $id, 'ID_CAMION');
  //   return !$data->error();
  // }


  // public function eliminar($id){
  //   $data   = $this->_db->delete('uman_camion', $id, 'ID_CAMION');
  //   return !$data->error();
  // }



  // extra

  function get_disponibles(){
    $data   = $this->_db->query("SELECT
                                  s.ID_SENSOR,
                                  s.CODSENSOR,
                                  s.TIPO
                                FROM
                                  uman_sensores as s
                                WHERE
                                  s.ID_SENSOR NOT IN ( SELECT n.ID_SENSOR FROM uman_neumaticos as n WHERE n.ID_SENSOR IS NOT NULL)
                                  AND ESTADO='DISPONIBLE' 
                                ORDER BY
                                  s.ID_SENSOR DESC");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  function get_sensor($id_neumatico){
    $data   = $this->_db->query("SELECT ID_SENSOR FROM uman_neumaticos WHERE ID_NEUMATICO='$id_neumatico'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  function get_sensor_full($id_sensor){
    $data   = $this->_db->query("SELECT * FROM uman_sensores WHERE ID_SENSOR='$id_sensor'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }
//end class
}
