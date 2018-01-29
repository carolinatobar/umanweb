<?php
class Flota{

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
    $data   = $this->_db->query('SELECT NUMFLOTAS, NOMBRE FROM uman_flotas ORDER BY NOMBRE ASC');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function obtenerNombre($num_flota){
    $data   = $this->_db->query('SELECT NOMBRE FROM uman_flotas WHERE NUMFLOTAS='.$num_flota);

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }


  public function listar_full()
  {
    $sql = 'SELECT
      uf.NUMFLOTAS,
      uf.NOMBRE,
      uc.ID_CAMION,
      uc.NUMCAMION,
      uc.ID_CAJAUMAN,
      uc.tipo,
      ute.NEUMATICOS,
      ute.CLASS_IMG 
      FROM
      uman_flotas uf LEFT JOIN uman_camion uc ON uf.NUMFLOTAS = uc.NUMFLOTA
      INNER JOIN uman_tipo_equipo ute ON uc.tipo=ute.ID
      ORDER BY uf.NUMFLOTAS ASC, uc.NUMCAMION ASC';
    $data   = $this->_db->query($sql);

    if(!$data->error())
    {
      return $data->results();
    }
    else
    {
      return false;
    }
  }



  public function insertar($fields = array()){

    $data   = $this->_db->insertar('uman_flotas', $fields, 'NUMFLOTAS');
    return !$data->error();
  }


  public function modificar($id, $fields = array()){

    //TABLA, CAMPOS, ID, INDEX
    $data   = $this->_db->update('uman_flotas', $fields, $id, 'NUMFLOTAS');
    return !$data->error();

  }


  public function eliminar($id){

    $data   = $this->_db->delete('uman_flotas', $id, 'NUMFLOTAS');
    return !$data->error();

  }







//end class
}
