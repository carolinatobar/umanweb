<?php
  if ( !$ok ) {
      exit();
  } else {
?>
<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

  <!-- <link href="https://fonts.googleapis.com/css?family=Muli|Libre+Franklin" rel="stylesheet"> -->
  <link rel="shortcut icon" type="image/png" href="sitio/favicon.png"/>
  <!-- Bootstrap CSS 3.3.7 -->
  <link rel="stylesheet" href="sitio/assets/css/bootstrap.min.css">
  <!-- Custom Index -->
  <link rel="stylesheet" href="sitio/assets/css/login.css">
  <!-- Jquery 1.12.4 -->
  <script src="sitio/assets/js/jquery.min.js"></script>
  <!-- Bootstrap JS 3.3.7 -->
  <script src="sitio/assets/js/bootstrap.min.js"></script>

    <title>Uman Net Cluster</title>
    <style type="text/css">
        .outer {
            display: table;
            position: absolute;
            height: 100%;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: middle;
        }

        .inner {
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }
    </style>
</head>
<body style="background-image:url(sitio/assets/img/imagen-fondo-web-uman-blue_3.jpg); background-size:cover;">
 <div class="outer">
  <div class="middle">
   <div class="inner">
    <center>
     <form action="faena-seleccionada.php" method="POST">
      <div class="panel panel-primary" style="max-width: 300px">
       <div class="panel-heading">
        <h3>Seleccionar faena</h3>
       </div>
       <div class="panel-body">
        <select name="faena" class="form-control" style="max-width: 300px">
         <?php            
            foreach($faenas as $f){
              print "<option value='{$f->nombre_db}/{$f->nombre_faena}/{$f->nombre_empresa}'>{$f->nombre_faena}</option>\n";
            }
         ?>
        </select>
        <br/>
        <input type="submit" value="Ir a faena" class="btn btn-primary">
       </div>
      </div>
     </form>
    </center>
   </div>
  </div>
 </div>

</body>
</html>

<?php
}
?>
