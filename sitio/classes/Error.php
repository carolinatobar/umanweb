<?php
  // require '../autoload.php';
  
  class Error{

    // public static function lanzar($excepcion,$titulo=''){
    //   @session_start();
    //   $_SESSION['error'] = $excepcion;
    //   $p = $GLOBALS['ERRORS'];

    //   header("Location: {$p}");

    //   // var_dump($excepcion);

    // }

    public static function lanzar2($excepcion,$titulo=''){
      @session_start();
      $_SESSION['error'] = $excepcion;

      header("Location: ".$GLOBALS['ERRORS']);
    }
  }