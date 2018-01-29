<?php
    require '../../autoload.php';

    $acc = new Acceso(true);

    $color           = isset($_POST['color'])           ? $_POST['color']           : null;
    $vigencia_inicio = isset($_POST['vigencia_inicio']) ? $_POST['vigencia_inicio'] : null;
    $vigencia_fin    = isset($_POST['vigencia_fin'])    ? $_POST['vigencia_fin']    : null;
    $nombre          = isset($_POST['nombre'])          ? $_POST['nombre']          : null;
    $poligono        = isset($_POST['poligono'])        ? $_POST['poligono']        : null;
    $icono           = isset($_POST['icono'])           ? $_POST['icono']           : null;
    $accion          = isset($_POST['acc'])             ? $_POST['acc']             : null;

    $dias            = ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'];

    $error = '';
    $data  = [];
    if($accion == 'crear'){

        if($nombre == null)          $error .= 'Debe ingresar un nombre para identificar a la Geocerca.'."\n";
        if($poligono == null)        $error .= 'Debe crear el polígono utilizando que definirá la geocerca.'."\n";
        if($color == null)           $error .= 'Debe asignarle un color.'."\n";

        $iconob64 = '';
        if($icono != null){
            $fileName = $_FILES['userfile']['name'];
            $tmpName  = $_FILES['userfile']['tmp_name'];
            $fileSize = $_FILES['userfile']['size'];
            $fileType = $_FILES['userfile']['type'];

            $fp      = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            $content = addslashes($content);
            fclose($fp);

            $iconob64 = 'data:image/' . $fileType . ';base64,' . base64_encode($content);
        }

        if($error == ''){
            $db = DB::getInstance();
            $sql = sprintf("INSERT INTO uman_geocerca VALUES (null, '%s', '%s', '%s', '%s', '%s');", 
                $nombre, $color, $iconob64, $vigencia_inicio, $vigencia_fin);

            $res = $db->query($sql);
            $res = $db->_pdo->lastInsertId();

            $sql = "INSERT INTO uman_poligono VALUES";
            foreach($poligono as $p){
                $xy = explode(';',$p);
                $sql .= sprintf("(null,%d,%f,%f),", $res, $xy[0], $xy[1]);
            }
            $sql = substr($sql, 0, strlen($sql)-1);

            $res = $db->query($sql);

            return json_encode(['status'=>200, 'mensaje'=>'Geocerca creada con éxito.']);
        }
        else{
            return json_encode(['status'=>400, 'mensaje'=>$error]);
        }
    }
    else if($accion == 'modificar'){

    }
    else if($accion == 'eliminar'){

    }
    else if($accion == 'obtener'){

    }