<?php 

class VersionControl{

  public static function verificarVersion($act, $req){
    $error1 = false; $error2 = false; $error3 = false;
    if($act[0]<$req[0]) $error1 = true;
    if($act[1]<$req[1] && $error1) $error2 = true;
    if($act[2]<$req[2] && $error2) $error3 = true;
    // var_dump([$error1, $error2, $error3]);
    // var_dump($error1 || $error2 || $error3);
    if($error1 || $error2 || $error3){
      echo('<!DOCTYPE html>
        <html>
          <head>
            <meta charset="UTF-8" />
            <title>UmanWeb</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
            <link rel="stylesheet prefetch" href="http://fonts.googleapis.com/css?family=Open+Sans" />
            <link rel="stylesheet" href="'.$GLOBALS['ASSETS'].'/css/login.css" />
            <script type="text/javascript" src="'.$GLOBALS['ASSETS'].'/jqwidgets/scripts/jquery-1.11.1.min.js"></script>
            <link rel="stylesheet" type="text/css" href="'.$GLOBALS['ASSETS'].'/css/bootstrap.min.css">
            <script src="'.$GLOBALS['ASSETS'].'/sweetalert/sweetalert.min.js"></script>
            <link rel="stylesheet" type="text/css" href="'.$GLOBALS['ASSETS'].'/sweetalert/sweetalert.css" />
          </head>
          <body style="background-image:url('.$GLOBALS['ASSETS'].'/img/bg_login.jpg); background-size:cover;">
            
            <div class="container">
              <br/>
              <div class="panel panel-danger clearfix" style="width:50%; margin:5% 25%;">
                <div class="panel-heading"><h3 class="bg-<?=$__type__?>"><center>Debe actualizar su versión de PHP</center></h3></div>
                <div class="panel-body text-center">
                  <h5>Versión mínima requerida <span class="text-primary"><strong>'.$req[0].'.'.$req[1].'.'.$req[2].'</strong></span></h5>
                 <h5>Versión instalada <span class="text-danger"><strong>'.$act[0].'.'.$act[1].'.'.$act[2].'</strong></span></h5>
                 <br/>
                 <center><a href="http://php.net/downloads.php" class="btn btn-primary">Descargar versión actualizada</a>
                  <p>&nbsp;</p>
                </div>
              </div>
            </div>
          </div>
          <script type="text/javascript">
            // EVITAR goBack() EN BROWSER
            (function (global) {
              if(typeof (global) === "undefined"){ throw new Error("window is undefined"); }
                var _hash = "!";
                var noBackPlease = function () {
                  global.location.href += "#";
                    // making sure we have the fruit available for juice....
                    // 50 milliseconds for just once do not cost much (^__^)
                  global.setTimeout(function () { global.location.href += "!"; }, 50);
                };
                
                // Earlier we had setInerval here....
                global.onhashchange = function () {
                  if (global.location.hash !== _hash) {
                    global.location.hash = _hash;
                  }
                };
                global.onload = function () {
                  noBackPlease();
                  // disables backspace on page except on input fields and textarea..
                  document.body.onkeydown = function (e) {
                  var elm = e.target.nodeName.toLowerCase();
                  if (e.which === 8 && (elm !== \'input\' && elm  !== \'textarea\')) {
                    e.preventDefault();
                  }
                  // stopping event bubbling up the DOM tree..
                  e.stopPropagation();
                };
              };
            })(window);
          </script>
        </body>
        </html>');
      exit();
    }
  }
}