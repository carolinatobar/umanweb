<?php
class General{

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

  public function listar_posiciones(){
    $data   = $this->_db->query('SELECT ID, POSICION, NOMENCLATURA FROM uman_posicion');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function update_position($pos_num, $fields = array()){
    //TABLA, CAMPOS, ID, INDEX
    $data   = $this->_db->update('uman_posicion', $fields, $pos_num, 'posicion');
    if(!$data->error()){
      return true;
    }
    else{
      return false;
    }
  }



  //----------------------------------------
  // Funciones Parametros Generales & Fallas
  //----------------------------------------


  public function listar_parametros(){
    $data   = $this->_db->query("SELECT TIEMPOFALLA, DESVIOTEMP, DESVIOPRESMAX, DESVIOPRESMIN, PRESIONMINIMA, PERIODOACTUALIZACION  FROM uman_parametros_fallas");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function get_atm(){
    $data   = $this->_db->query("SELECT VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO = 'atm'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }

  }

  public function get_sampleo(){
    $data   = $this->_db->query("SELECT VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO = 'sampleogps'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }

  }

  public function get_timeout(){
    $data   = $this->_db->query("SELECT VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO = 'timeout'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }

  }

  public function get_maxvelocidad(){
    $data   = $this->_db->query("SELECT VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO = 'maxvelocidad'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }

  }

  public function getParamValue($paramName, $default=0){
    $data   = $this->_db->query("SELECT VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO='$paramName'");

    if(!$data->error())
    {
      if( $data->count() > 0 ){
        $data = $data->results()[0]->VALOR1;
      }
      else{
        $data = $default;
        $this->_db->query("INSERT INTO uman_parametros (ID_PARAMETROS, NOMBREPARAMETRO, VALOR1) VALUES(NULL,'{$paramName}','{$default}');");
      }
      return $data;
    } 
    //Si no existe se creará con valor 0
    else{
      $this->_db->query("INSERT INTO uman_parametros (ID_PARAMETROS, NOMBREPARAMETRO, VALOR1) VALUES(NULL,'{$paramName}','{$default}');");
      return $default;
    }
  }

  public function getNomenclaturas(){
    $nomenclatura = array_fill(1,16,0);
    for($i=1; $i<=16; $i++) $$nomenclatura[$i] = $i;

    $db = $this->_db->query("SELECT * FROM uman_posicion");
    foreach( $db->results() as $datos_nom ) {
      $nomenclatura[$datos_nom->POSICION] = $datos_nom->NOMENCLATURA;
    }

    return $nomenclatura;
  }

  public function getImagenesEquipo($style='', $class='pull-left'){
    $img_equipo = array();
    $sql = "SELECT ID_CAMION, NEUMATICOS, CLASS_IMG, IMG_ESQUEMA, EJES 
		FROM uman_camion INNER JOIN uman_tipo_equipo ON tipo=ID 
		ORDER BY ID_CAMION ASC;";
    $camiones = $this->_db->query($sql);	
    if($camiones->count() > 0){
      foreach($camiones->results() as $c){
        $img_equipo[$c->ID_CAMION] = array(
          "NEUMATICOS"=>$c->NEUMATICOS,
          "CLASS_IMG"=>$c->CLASS_IMG,
          "IMG_ESQUEMA"=>$c->IMG_ESQUEMA,
          "EJES"=>$c->EJES,
          "DIV"=>'<div class="'.$c->CLASS_IMG.' icono-x24 '.$class.'" style="'.$style.'"></div>',
          "DIV16"=>'<div class="'.$c->CLASS_IMG.' icono-x16 '.$class.'" style="'.$style.'"></div>',
          "DIV24"=>'<div class="'.$c->CLASS_IMG.' icono-x24 '.$class.'" style="'.$style.'"></div>',
          "DIV36"=>'<div class="'.$c->CLASS_IMG.' icono-x36 '.$class.'" style="'.$style.'"></div>',
          "DIV48"=>'<div class="'.$c->CLASS_IMG.' icono-x48 '.$class.'" style="'.$style.'"></div>'
        );
      }
    }

    return $img_equipo;
  }


  public function update_parametro($table, $fields, $column_val, $column_name){
    //TABLA, CAMPOS, ID, INDEX
    $data   = $this->_db->update($table, $fields, $column_val, $column_name);
    if(!$data->error()){
      return true;
    }
    else{
      return false;
    }
  }




//end class
}
