<?php

class Equipo{

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


  public function actualizarEstadoUmanBlue($camion) {

    $id             = $camion->ID_CAMION;
    $tipo_equipo    = $camion->tipo;
    $id_caja        = $camion->ID_CAJAUMAN;
    $numcamion      = $camion->NUMCAMION;
    // var_dump($camion);

    $arr_insert['ID_CAMION'] = $id;

    $data_caja    = $this->_db->query("SELECT CODIGOCAJA FROM uman_cajauman WHERE ID_CAJAUMAN='$id_caja'");
    $arr_caja     = $data_caja->results();

    $codigo_caja    = $arr_caja[0]->CODIGOCAJA;

    $arr_insert['UMAN_BLUE']   = $codigo_caja;
    $arr_insert['TIPO_EQUIPO'] = $tipo_equipo;

    $datax             = $this->_db->query("SELECT ID_POSICION,ID_NEUMATICO FROM uman_neumatico_camion WHERE ID_EQUIPO='$id'");
    $arr_neu_camion    = $datax->results();

    for ( $i = 1 ; $i <= 17 ; $i++ ) {
      if($i<17){
        $arr_insert['SENSOR'.$i]     = "";
        $arr_insert['PRESUP'.$i]     = "";
        $arr_insert['PRESINF'.$i]    = "";
        $arr_insert['UTEMP'.$i]      = "";
        $arr_insert['PIF'.$i]        = "";
      }

      $sql = sprintf("SELECT * FROM uman_ultimoevento WHERE posicion=%d AND numequipo='%s';", $i, $id);
      // echo "$sql\n";
      $res = $this->_db->query($sql);

      if($res->count() == 0){
        $sql = sprintf("INSERT INTO uman_ultimoevento 
          VALUES(NULL, '%s', %d, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0);",
          $id, $i);
        // echo "$sql\n";
        $this->_db->query($sql);
      }
    }

    //print "<P><b>ID CAMION: $id || TIPO EQUIPO: $tipo_equipo || ID CAJA: $id_caja<p>";
    //print "CAMION: $numcamion || UMAN BLUE: ".$arr_insert['UMAN_BLUE']."</b><P>";

    foreach ( $arr_neu_camion as $neu_cam ) {


      $posicion         = $neu_cam->ID_POSICION;
      $id_neumatico     = $neu_cam->ID_NEUMATICO;

      $data_sens        = $this->_db->query("SELECT ID_PLANTILLA,ID_SENSOR FROM uman_neumaticos WHERE ID_NEUMATICO='$id_neumatico'");
      $arr_id_sensor    = $data_sens->results();

      $id_sensor        = $arr_id_sensor[0]->ID_SENSOR;
      $id_plantilla     = $arr_id_sensor[0]->ID_PLANTILLA;

      $data_nom_sens    = $this->_db->query("SELECT CODSENSOR FROM uman_sensores WHERE ID_SENSOR='$id_sensor'");
      $arr_nom_sens     = $data_nom_sens->results();

      $data_umbrales    = $this->_db->query("SELECT TEMPMAX,PRESMAX,PRESMIN,PIF FROM uman_plantilla WHERE ID_PLANTILLA='$id_plantilla'");
      $arr_umbrales     = $data_umbrales->results();

      $codsensor        = $arr_nom_sens[0]->CODSENSOR;
      $tempmax          = $arr_umbrales[0]->TEMPMAX;
      $presmax          = $arr_umbrales[0]->PRESMAX;
      $presmin          = $arr_umbrales[0]->PRESMIN;
      $pif              = $arr_umbrales[0]->PIF;

      $arr_insert['SENSOR'.$posicion]     = $codsensor;
      $arr_insert['PRESUP'.$posicion]     = $presmax;
      $arr_insert['PRESINF'.$posicion]    = $presmin;
      $arr_insert['UTEMP'.$posicion]      = $tempmax;
      $arr_insert['PIF'.$posicion]        = $pif;


      $sql = sprintf("SELECT * FROM uman_ultimoevento WHERE posicion=%d AND numequipo='%s';", $posicion, $id);
      // echo "$sql\n";
      $res = $this->_db->query($sql);

      if($res->count() > 0){
        $sql = sprintf("UPDATE uman_ultimoevento SET SENSOR='%s' WHERE posicion=%d AND numequipo='%s'", $codsensor, $posicion, $id);
        // echo "$sql\n";
        $this->_db->query($sql);
      }
      else{
        $sql = sprintf("INSERT INTO uman_ultimoevento 
          VALUES(NULL, '%s', %d, '%s', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, %d, %d, %d);",
          $id, $posicion, $codsensor, $tempmax, $presmax, $presmin);
        // echo "$sql\n";
        $this->_db->query($sql);
      }
    }

    $arr_insert['FLAG_SERV_UMAN']       = "1";
    $arr_insert['TIPO_EQUIPO']          = $tipo_equipo;

    $data_param       = $this->_db->query("SELECT NOMBREPARAMETRO,VALOR1 FROM uman_parametros WHERE NOMBREPARAMETRO='atm' OR NOMBREPARAMETRO='sampleogps' OR NOMBREPARAMETRO='timeout' OR NOMBREPARAMETRO='bateria' OR NOMBREPARAMETRO='sonido' OR NOMBREPARAMETRO='led'");
    $arr_param        = $data_param->results();

    foreach ( $arr_param as $param ) {
      $nombre_param = $param->NOMBREPARAMETRO;
      $valor        = $param->VALOR1;

      switch( $nombre_param ) {
        case "atm":
          $arr_insert["PRES_ATM"] = $valor;
          break;
        case "sampleogps":
          $arr_insert["SAMPLEO_GPS"] = $valor;
          break;
        case "timeout":
          $arr_insert["TIMEOUT"] = $valor;
          break;
        case "bateria":
          $arr_insert["BATERIA"] = $valor;
          break;
        case "sonido":
          $arr_insert["ALRMSON"] = $valor;
          break;
        case "led":
          $arr_insert["ALRMLUM"] = $valor;
          break;
      }
    }

    //var_dump($arr_insert);

    $dataxx   = $this->_db->update('uman_estado_umanblue', $arr_insert, $codigo_caja, 'UMAN_BLUE');

    $_SESSION[session_id()]['ACTUALIZAR_CAJA'] = true;
    $_SESSION[session_id()]['CAJAS'][] = $codigo_caja;
  }

  public function listar(){
    $data   = $this->_db->query('SELECT ID_CAMION, NUMCAMION, ID_CAJAUMAN, NUMFLOTA, tipo, CLASS_IMG 
      FROM uman_camion uc INNER JOIN uman_tipo_equipo ute ON uc.tipo=ute.ID ORDER BY NUMCAMION DESC');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function listar_full($id){
    //  Listado de todo lo relacionado con el Equipo
    $data   = $this->_db->query("SELECT
                                  c.ID_CAMION,
                                  c.NUMCAMION,
                                  c.NUMFLOTA,
                                  te.ID,
                                  te.TIPO_EQUIPO,
                                  te.NOMBRE_EQUIPO,
                                  te.EJES,
                                  te.NEUMATICOS,
                                  te.IMG_ESQUEMA,
                                  te.CLASS_IMG,
                                  cu.CODIGOCAJA,
                                  cu.IPUMAN,
                                  cu.PUERTO,
                                  cu.ID_CAJAUMAN
                                FROM
                                  uman_camion AS c
                                  LEFT JOIN uman_tipo_equipo AS te ON c.tipo = te.ID
                                  LEFT JOIN uman_cajauman AS cu ON c.ID_CAJAUMAN = cu.ID_CAJAUMAN
                                WHERE
                                  c.ID_CAMION = {$id}");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }

  public function listar_this($id){
    //  Listado de Ruedas
    //  $data   = $this->_db->query("SELECT * FROM uman_camion WHERE ID_CAMION = {$id}"); //old - modelo antiguo
    $data   = $this->_db->query("SELECT
                                  nc.ID,
                                  nc.ID_EQUIPO,
                                  nc.ID_POSICION,
                                  nc.ID_NEUMATICO,
                                  n.NUMIDENTI,
                                  n.NUMEROFUEGO,
                                  n.MARCA,
                                  n.MODELO,
                                  n.ID_SENSOR,
                                  n.COMPUESTO,
                                  p.ID_PLANTILLA,
                                  p.MODELO,
                                  p.DIMENSION,
                                  p.TEMPMAX,
                                  p.PRESMAX,
                                  p.PRESMIN,
                                  c.NUMCAMION,
                                  c.ID_CAJAUMAN,
                                  c.tipo,
                                  c.NUMFLOTA,
                                  s.CODSENSOR,
                                  s.TIPO as TIPO_SENSOR 
                                FROM
                                  uman_neumatico_camion AS nc
                                  LEFT JOIN uman_neumaticos AS n ON nc.ID_NEUMATICO = n.ID_NEUMATICO
                                  LEFT JOIN uman_plantilla AS p ON n.ID_PLANTILLA = p.ID_PLANTILLA
                                  LEFT JOIN uman_camion AS c ON nc.ID_EQUIPO = c.ID_CAMION
                                  LEFT JOIN uman_sensores AS s ON n.ID_SENSOR = s.ID_SENSOR
                                WHERE
                                  nc.ID_EQUIPO = {$id}
                                ORDER BY
                                  nc.ID_POSICION ASC");


    

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }





  public function listar_alone(){
    $data   = $this->_db->query('SELECT * 
      FROM uman_camion c INNER JOIN uman_tipo_equipo te ON c.tipo=te.ID
      WHERE ISNULL(NUMFLOTA) OR NUMFLOTA=0
      ORDER BY NUMCAMION DESC');

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }


  public function get_tipo($id){
    $data   = $this->_db->query("SELECT tipo FROM uman_camion WHERE ID_CAMION='$id'");

    if(!$data->error()){
      return $data->results();
    }
    else{
      return false;
    }
  }


  public function insertar($fields = array()){
    $data   = $this->_db->insertar('uman_camion', $fields, 'ID_CAMION');
    return !$data->error();
  }


  public function modificar($id, $fields = array()){
    $data   = $this->_db->update('uman_camion', $fields, $id, 'ID_CAMION');
    return !$data->error();
  }

  public function eliminar($id,$num){
    $data   = $this->_db->delete('uman_camion', $id, 'ID_CAMION');
    $data   = $this->_db->delete('uman_ultimogps', $num, 'NUMCAMION');
    $data   = $this->_db->delete("uman_ultimoevento", $num, 'numequipo');
    return !$data->error();
  }

  public function crear_ultimo($id,$tipo){


    $data   = $this->_db->query("SELECT ID_CAMION FROM uman_camion WHERE NUMCAMION='$id'");
    $arr_id = $data->results();
    $eqp_id = $arr_id[0]->ID_CAMION;

    $arr_insert['numequipo'] = $eqp_id;

    for ( $i = 1; $i<=17 ; $i++ ) {
      $arr_insert['posicion']   = $i;
      $data   = $this->_db->insertar('uman_ultimoevento',$arr_insert, 'id');
    }

    $data   = $this->_db->query("SELECT ID_CAMION FROM uman_camion WHERE NUMCAMION='$id'");
    $arr_id = $data->results();
    $eqp_id = $arr_id[0]->ID_CAMION;

    $arr_insert_gps['NUMCAMION']    = $id;
    $arr_insert_gps['TIPOEQUIPO']   = $tipo;
    $arr_insert_gps['ID_EQUIPO']    = $eqp_id;

    $data   = $this->_db->insertar('uman_ultimogps',$arr_insert_gps, 'NUMCAMION');

    return !$data->error();
  }




  // Asociación de Neumáticos a Equipos
  //------------------------------------------------

  public function instalar_neumatico($fields){
    $data   = $this->_db->insertar('uman_neumatico_camion', $fields, 'ID');
    return !$data->error();
  }

  public function cambiar_neumatico($id, $fields){
    $data   = $this->_db->update('uman_neumatico_camion', $fields, $id, 'ID');
    return !$data->error();
  }
  public function retirar_neumatico($id){
    $data   = $this->_db->delete('uman_neumatico_camion', $id, 'ID');
    return !$data->error();
  }

  //------------------------------------------------


  function reset_flota($id_flota){
    $data   = $this->_db->query("UPDATE uman_camion SET NUMFLOTA=0 WHERE (NUMFLOTA={$id_flota})");
    return !$data->error();
  }

 public function obtener_neumaticos($id_equipo, &$neumaticos)
 {
   $neumaticos = NULL;
   $sql = 'SELECT *
    FROM uman_neumatico_camion unc 
    WHERE unc.ID_EQUIPO='.$id_equipo.' 
    ORDER BY ID_POSICION ASC';
   $data = $this->_db->query($sql);

   if(!$data->error())
   {
    if($data->count()>0)
    {
      $neumaticos = $data->results();
      return TRUE;
    }
    else return FALSE;
   } 
   
   return FALSE;
 }

 public function obtener_nombre($id_equipo)
 {
  $nombre = $id_equipo;
  $data = $this->_db->query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=$id_equipo;");

  if(!$data->error())
  {
    if($data->count()>0){
      $data = $data->results();
      $nombre = $data[0]->NUMCAMION;
    }
  }

  return $nombre;

 }


//end class
}
