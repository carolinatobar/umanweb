<?php
  require '../autoload.php';

  @session_start();
  if(isset($_SESSION['error'])){
    $error = $_SESSION['error'];
    if(is_array($error)){
      Render::make('error_fullpage', array(
        'title'=>$error[1],
        'content'=>$error[2],
        'footer'=>(isset($error[3]) ? $error[3] : '')
      ));
    }
    else{
        Render::make('error_fullpage', array(
        'title'=>'Error',
        'content'=>$error,
        'footer'=>''
      ));
    }
  }