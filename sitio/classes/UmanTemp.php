<?php

class UmanTemp {

  private static $_instance = null;
  public  $_pdo  = null;

  private $_query,
          $_error = false,
          $_results,
          $_count = 0;

  private function __construct(){
      try{

        $host           = Config::getHost();
        $db             = Config::getDBTemp();
        $user           = Config::getUserName();
        $password       = Config::getPassword();

        $this->_pdo     = new PDO("mysql:host=$host;dbname=$db;",$user,$password);

      }
      catch(PDOException $e){
        die(utf8_encode($e->getMessage()));
        // Error::lanzar($e->getMessage());
      }
  }

  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new UmanTemp();  //si no hay instancia, creela.
    }
    return self::$_instance;
  }

  public function getPDO(){
    return $this->_pdo;
  }
  
  public function closeCursor(){
    $this->_query->closeCursor();
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
        die($arr[2]."\n\n".$sql);
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