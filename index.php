<?php
  // error_reporting(E_ALL);
  include './sitio/errors/error_codes.php';
  
  $msg = isset($_GET['ERROR']) ? $GLOBALS['_ERROR']['e_'.$_GET['ERROR']] : NULL;
  
?>
<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript">
    /*
    <?php 
      var_dump($msg);
      //var_dump($GLOBALS['_ERROR']);
    ?>
    */
  </script>
  <meta charset="UTF-8" />
  <title>UmanWeb</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
  <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Open+Sans' />
  <link rel="stylesheet" href="sitio/assets/css/login.css" />
  <script type="text/javascript" src="sitio/assets/jqwidgets/scripts/jquery-1.11.1.min.js"></script>
  <link rel="stylesheet" type="text/css" href="sitio/assets/css/bootstrap.min.css">
  <script src="sitio/assets/sweetalert/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="sitio/assets/sweetalert/sweetalert.css" />
  <script type="text/javascript">
    var translator, dcc;
    $(document).ready(function() {

      var animating = false,
        submitPhase1 = 1100,
        submitPhase2 = 400,
        logoutPhase1 = 800,
        $login = $(".login"),
        $form = $("#form1");

      function ripple(elem, e) {
        $(".ripple").remove();
        var elTop = elem.offset().top,
          elLeft = elem.offset().left,
          x = e.pageX - elLeft,
          y = e.pageY - elTop;
        var $ripple = $("<div class='ripple'></div>");
        $ripple.css({
          top: y,
          left: x
        });
        elem.append($ripple);
      };

      $("input[type=text]")
      .keyup(function(event) { if (event.keyCode == 13) $("input[type=password]").focus(); });

      $("input[type=password]")
      .keyup(function(event) { if (event.keyCode == 13) $("#ingresar").click(); });
    });

    
  </script>
  <style>
    img {
      max-width: 100%;
      height: auto;
    }
  </style>
</head>

<body style="background-image:url(sitio/assets/img/imagen-fondo-web-uman-blue_3.jpg); background-size:cover;">
  <div class="cont">
    <div class="demo">
      <div class="login">
        <div><br><br>
          <img src="sitio/assets/img/logo_uman_blue.png" width="303" height="74" alt="" />
          <p class="login__signup" style="color:#FFFFFF; text-align:center; font-size:10px">&nbsp;&nbsp;&nbsp;<a><span class="trn">Sistema para la Gestión y Administración de Neumático</span></a><br/></p><br><br>
        </div>
        <form class="form-login" action="login.php" id='form1' method="POST">
          <div class="login__form">
            <div class="login__row">
              <svg class="login__icon name svg-icon" viewBox="0 0 20 20">
                <path d="M0,20 a10,8 0 0,1 20,0z M10,0 a4,4 0 0,1 0,8 a4,4 0 0,1 0,-8" />
              </svg>
              <input type="text" class="login__input name trn" placeholder="Usuario" name="user" id="user" autofocus />
            </div>
            <div class="login__row">
              <svg class="login__icon pass svg-icon" viewBox="0 0 20 20">
                <path d="M0,20 20,20 20,8 0,8z M10,13 10,16z M4,8 a6,8 0 0,1 12,0" />
              </svg>
              <input type="password" class="login__input pass trn" placeholder="Contraseña" name="pass" id="pass" />
              <input type="hidden" name="antes" value="<?php print $_SERVER['HTTP_REFERER']; ?>">
            </div>
            <button type="submit" class="login__submit trn" id='ingresar'>Entrar</button>
            <p class="login__signup">
              <div class="row">
                <p class="login__signup">
                  <img src="sitio/assets/img/flags/chile_32.png" alt="">&nbsp;&nbsp;&nbsp;
                  <img src="sitio/assets/img/flags/usa_32.png" alt="">&nbsp;&nbsp;&nbsp;
                  <img src="sitio/assets/img/flags/brazil_32.png" alt=""></p>
              </div>
            </p>
            <p class="login__signup"><a>www.bailac.cl</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php 
    if($msg != null){
  ?>
    <script type="text/javascript">
      $(function(){
        swal({
          title: '<?=$msg->title?>',
          text: '<?=$msg->text?>',
          type:'<?=$msg->type?>',
          html: <?=($msg->html ? 'true':'false')?>,
        });
      });
    </script>
  <?php
    }
  ?>
</body>
</html>