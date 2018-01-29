<?php

class DB2 {

  private static $_instance = null;
  public  $_pdo  = null;

  private $_query,
          $_error = false,
          $_results,
          $_count = 0;

  private function __construct(){
      try{

        $host           = Config::getHost();
        $db             = Config::getDBLogin();
        $user           = Config::getUserName();
        $password       = Config::getPassword();

        $this->_pdo     = new PDO("mysql:host=$host;dbname=$db;",$user,$password);

      }
      catch(PDOException $e){
        die($e->getMessage());
        // Error::lanzar($e->getMessage());
      }
  }

  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new DB2();  //si no hay instancia, creela.
    }
    return self::$_instance;
  }

  public function getPDO(){
    return $this->_pdo;
  }
  
  public function closeCursor(){
    $this->_query->closeCursor();
  }

  public function insertar($table, $fields = array(), $index = ''){

    $this->_error = true;

    if(count($fields) > 0){

      $keys = '';
      $vals = '';

      foreach($fields as $key => $val){
        $keys .=  "`".$key."`, ";
        $vals .=  "'".$val."', ";
      }

      $keys = trim($keys, ', ');  //quitar comma final
      $vals = trim($vals, ', ');  //quitar comma final

      $sql    = "INSERT INTO `{$table}` (".$keys.") VALUES (".$vals.")";

      //  INSERT INTO `uman_camion` (`NUMCAMION`, `NUMNEUMATICOS`, `ID_CAJAUMAN`, `NUMFLOTA`, `ORDEN`) VALUES ('123123', '6', '7', '1', '12')

      if($this->_query = $this->_pdo->prepare($sql)){
        //var_dump($this->_query );

        if($this->_query->execute()){
          //$this->_query->execute();
          $this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
          $this->_count = $this->_query->rowCount();
          $this->_error = false;
        }
        else{
          $this->_error = true;
        }
      }
      return $this;
    }
  }

  public function update($table, $fields = array(), $id, $index){

    $this->_error = true;

    if(count($fields) > 0){

      $values = '';

      foreach($fields as $key => $val){
        if($val == '' || $val == null){
          $values .=  "`".$key."` = '', ";
        } else {
          $values .=  "`".$key."` = '".$val."', ";
        }

      }

      $values = trim($values, ', ');  //quitar comma final

      $sql    = "UPDATE `{$table}` SET {$values} WHERE (`{$index}` = {$id})";

      //echo $sql;

      if($this->_query = $this->_pdo->prepare($sql)){

        if($this->_query->execute()){
          $this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
          $this->_count = $this->_query->rowCount();
          $this->_error = false;
        }
        else{
          $this->_error = true;
        }

      }
      return $this;
    }
  }

  public function delete($table, $id, $index){

    $this->_error = false;

    //DELETE FROM `uman_flotas` WHERE (`NUMFLOTAS`='13')
    $sql    = "DELETE FROM `{$table}` WHERE (`{$index}` = '{$id}')";

    if($this->_query = $this->_pdo->prepare($sql)){

      if(!$this->_query->execute()){
        $this->_error = true;
      }
      return $this;
    }

  }

  public function query($sql){

    $this->_error = false;
    if($this->_query = $this->_pdo->prepare($sql)){

      if($this->_query->execute()){
        $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
        $this->_count = $this->_query->rowCount();
        $this->_query->closeCursor();
      }
      else{
        $this->_error = true;
        $arr = $this->_query->errorInfo();
        // Error::lanzar($this->_query->errorInfo());
        die($arr[2]);
      }
    }
    else {
      $this->_error = true;
      // Error::lanzar($this->_query->errorInfo());
    }

    return $this;

  }

  public function results(){
    return $this->_results;
  }

  public function error(){
    return $this->_error;
  }

  public function count(){
    return $this->_count;
  }

}