<?php

class Usuario{

  private $_db,
          $_pdo,
          $_schema,
          $_data,
          $_id,
          $_code,
          $_desc,
          $_stt;

  public function __construct($id = null){
    $this->_db      = DB2::getInstance();
    $this->_pdo     = $this->_db->getPDO();
  }

  public function obtenerPerfiles($id_usuario){
    $sql = sprintf("SELECT p.* 
          FROM uman_perfil p INNER JOIN uman_perfil_usuario pu ON p.id=pu.id_perfil 
          WHERE pu.id_usuario=%d;", $id_usuario);
    $data = $this->_db->query($sql);

    $perfil = array();

    if(!$data->error()){
      if($data->count()>0){
        foreach($data->results() as $d){
          $perfil[] = $d;
        }
      }
    }

    return $perfil;
  }

  public function obtenerAccesoPerfil($id_usuario, $link, $id_perfil){
    $sql = sprintf("SELECT COUNT(*) as 'total' 
      FROM uman_perfil p INNER JOIN uman_acceso_perfil ap ON p.id=ap.id_perfil 
      INNER JOIN uman_perfil_usuario pu ON pu.id_perfil=p.id 
      INNER JOIN uman_modulo m ON m.id=ap.id_modulo 
      WHERE p.id=%d AND m.link='%s' AND pu.id_usuario=%d AND m.link!='';", 
      $id_perfil, $link, $id_usuario);
    // echo $sql;
    $data = $this->_db->query($sql);

    $acceso = ($data->results()[0]->total == 1) ? TRUE : FALSE;

    return $acceso;
  }

  public function obtenerFaenas($id_usuario){
    $sql = sprintf("SELECT f.* 
        FROM uman_faenas f INNER JOIN uman_faena_usuario fu ON f.id=fu.id_faena 
        WHERE fu.id_usuario=%d;", $id_usuario);
    $data = $this->_db->query($sql);

    $faena = array();

    if(!$data->error()){
      if($data->count()>0){
        foreach($data->results() as $d){
          $faena[] = $d;
        }
      }
    }

    return $faena;
  }


}
