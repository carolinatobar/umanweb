<?php
class Config{
 /*
 local       | para test y debug en equipo local con base de datos local
 remote      | para test y debug en equipo local con base de datos remota
 produccion  | para poducción
 */
 private static $conn_type = 'produccion';

 private static $local_ip   = '127.0.0.1';
 private static $local_port = 3306;
 private static $local_user = 'mysql';
 private static $local_pass = 'bailac09';

 private static $remote_ip   = '192.168.20.100';
 private static $remote_port = 3306;
 private static $remote_user = 'server';
 private static $remote_pass = 'radagast';

 /*
 private static $produccion_ip   = 'localhost';
 private static $produccion_port = 3306;
 private static $produccion_user = 'server';
 private static $produccion_pass = 'radagast';
 */
 
 private static $produccion_ip   = 'localhost';
 private static $produccion_port = 3306;
 private static $produccion_user = 'uman_sistemas';
 private static $produccion_pass = 'b@ilac1999';
 
 

 public static function getHost()
 { 
   $var = self::$conn_type.'_ip';
   return self::${$var};
 }
 public static function getPort()
 { 
   $var = self::$conn_type.'_port';
   return self::${$var};
 }
 public static function getDB()
 {
  @session_start();
  $sess_id = session_id();
  return 'uman_'.$_SESSION[$sess_id]['faena'];
 }
 public static function getDBLogin()
 {
  return 'uman_sitio';
 }
 public static function getDBTemp()
 {
  return 'uman_temp';
 }
 public static function getUserName()
 { 
   $var = self::$conn_type.'_user';
   return self::${$var};
 }
 public static function getPassword()
 { 
   $var = self::$conn_type.'_pass';
   return self::${$var};
 }

}
// $faena = $_SESSION['faena'];
// //$GLOBALS['config']['host']      = '201.236.97.74';
// $GLOBALS['config']['host']      = '127.0.0.1';
// $GLOBALS['config']['port']      = 3306;
// $GLOBALS['config']['db']        = 'uman_'.$faena;
// $GLOBALS['config']['username']  = 'mysql';
// $GLOBALS['config']['password']  = 'bailac09';
