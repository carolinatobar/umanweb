<?php
class Caja{

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
    $data   = $this->_db->query('SELECT ID_CAJAUMAN, IPUMAN, CODIGOCAJA FROM uman_cajauman ORDER BY ID_CAJAUMAN DESC');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function insertar($fields = array()){

    $data   = $this->_db->insertar('uman_cajauman', $fields, 'ID_CAJAUMAN');
    return !$data->error();
  }







//end class
}
