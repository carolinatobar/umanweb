<?php
  require '../../autoload.php';

  $acc = new Acceso(true);

  $gen = new General();

  $porcentaje_tmax = $gen->getParamValue('desviotemp');
  $porcentaje_pmin = $gen->getParamValue('desviopresmin');
  $porcentaje_pmax = $gen->getParamValue('desviopresmax');
  $unidad_pres     = $gen->getParamValue('unidad_presion');
  $unidad_temp     = $gen->getParamValue('unidad_temperatura');
  $tiempo_falla    = $gen->getParamValue('tiempofalla');
  $nomenclatura    = $gen->getNomenclaturas();

  $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : NULL;

  $db = DB::getInstance();

  $sql = '';
  $sql_fecha = '';
  $data = array("data"=>array());
  $titulo = '';
  if($fecha == NULL){
    $fecha = date("Y-m-d 0:0:0");
    $sql = "SELECT DISTINCTROW EVENTONUMCAMION, EVENTOPOSICION 
      FROM uman_eventos
      WHERE EVENTOFECHA BETWEEN '{$fecha}' AND NOW() 
      ORDER BY EVENTOPOSICION ASC";
      $titulo = 'DESDE '.date("d/m/Y 0:0:0").' HASTA '.date("d/m/Y H:i:s");

    $equipos = $db->query($sql);
    $equipos = $equipos->results();

    $sql_fecha = "EVENTOFECHA BETWEEN '{$fecha}' AND NOW()";
  }
  else{
    if(stripos($fecha,' - ') !== false){
      $fecha = explode(' - ', $fecha);
      $sql = "SELECT DISTINCTROW EVENTONUMCAMION, EVENTOPOSICION 
        FROM uman_eventos
        WHERE UNIX_TIMESTAMP(EVENTOFECHA) BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$fecha[0]}', '%d/%m/%Y %H:%i:0')) AND UNIX_TIMESTAMP(STR_TO_DATE('{$fecha[1]}', '%d/%m/%Y %H:%i:0')) 
        ORDER BY EVENTOPOSICION ASC";

      $equipos = $db->query($sql);
      $equipos = $equipos->results();
      
      $titulo = 'DESDE '.$fecha[0].' HASTA '.$fecha[1];

      $sql_fecha = "UNIX_TIMESTAMP(EVENTOFECHA) BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('{$fecha[0]}', '%d/%m/%Y %H:%i:0')) AND UNIX_TIMESTAMP(STR_TO_DATE('{$fecha[1]}', '%d/%m/%Y %H:%i:0')) ";
    }
  }
  //echo "{$sql}\n";//exit();

  if($sql_fecha!='' && count($equipos)>0){
    $ut = UmanTemp::getInstance();
    $table  = 'uman_eventos_'.session_id();
    $ut->query("DROP TABLE IF EXISTS $table");
    $ut->query("CREATE TABLE `uman_temp`.`$table` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `idx` INT NULL,
      `camion` INT NULL,
      `posicion` INT NULL,
      `presion` INT NULL,
      `temperatura` INT NULL,
      `fecha` TIMESTAMP NULL,
      `temp_max` INT NULL,
      `pres_max` INT NULL,
      `pres_min` INT NULL,
      PRIMARY KEY (`id`));");

    $datos = array();
    foreach($equipos as $eq){
      $sql = "SELECT * 
        FROM uman_eventos
        WHERE $sql_fecha AND EVENTONUMCAMION=$eq->EVENTONUMCAMION AND EVENTOPOSICION=$eq->EVENTOPOSICION 
        ORDER BY EVENTOFECHA ASC";
      $evt = $db->query($sql);
      //echo "{$sql}\n";//exit();

      if($evt->count()>0){
        $escribir = false;
        $sql = "INSERT INTO $table (idx, camion, posicion, presion, temperatura, fecha, temp_max, pres_max, pres_min) VALUES ";
        foreach($evt->results() as $e){
          $sql .= "($e->IDEVENTO, $e->EVENTONUMCAMION, $e->EVENTOPOSICION, $e->EVENTOPRESION, $e->EVENTOTEMPERATURA, '$e->EVENTOFECHA', $e->TEMPMAX, $e->PRESMAX, $e->PRESMIN),";
        }
        $sql = substr($sql,0,strlen($sql)-1);
        $res = $ut->query($sql);

        #region Presión Baja
          $f = $ut->query("SELECT * FROM $table WHERE presion <= pres_min ORDER BY id ASC LIMIT 1");
          $bucle = false;
          if($f->count() == 1){
            //Guardar dato
            $f = $f->results()[0];
            $datos[$f->id] = array(
              'equipo'=>$f->camion,
              'posicion'=>$f->posicion,
              'tipo'=>'Presión Baja',
              'inicio'=>new DateTime($f->fecha),
              'fin'=>new DateTime($f->fecha),
              'umbral'=>$f->pres_min,
              'extremo'=>$f->presion,
              'duracion'=>''
            );
            $bucle = true;
          }

          while($bucle){
            $bucle = false;
            //Presión Baja
            $umbral = $f->pres_min * ((100 - $porcentaje_pmin) / 100);
            
            $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id AND presion > pres_min ORDER BY id ASC LIMIT 1");
            if($f2->count() == 1){
              $f2 = $f2->results()[0];
              $extremo = $ut->query("SELECT MIN(presion) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
              $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
              $datos[$f->id]['fin'] = new DateTime($f2->fecha);
              if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
            }
            else{
              $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id ORDER BY id DESC LIMIT 1");
              if($f2->count() == 1){
                $f2 = $f2->results()[0];
                $extremo = $ut->query("SELECT MIN(presion) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
                $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
                $datos[$f->id]['fin'] = new DateTime($f2->fecha);
                if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
              }
            }
            
            $f = $ut->query("SELECT * FROM $table WHERE presion <= pres_min AND id > $f2->id ORDER BY id ASC LIMIT 1");
            if($f->count() == 1){
              //Guardar dato
              $f = $f->results()[0];
              $datos[$f->id] = array(
                'equipo'=>$f->camion,
                'posicion'=>$f->posicion,
                'tipo'=>'Presión Baja',
                'inicio'=>new DateTime($f->fecha),
                'fin'=>new DateTime($f->fecha),
                'umbral'=>$f->pres_min,
                'extremo'=>$f->presion,
                'duracion'=>''
              );
              $bucle = true;
            }
          }
        #endregion

        #region Presión Alta
          $f = $ut->query("SELECT * FROM $table WHERE presion >= pres_max ORDER BY id ASC LIMIT 1");
          $bucle = false;
          if($f->count() == 1){
            //Guardar dato
            $f = $f->results()[0];
            $datos[$f->id] = array(
              'equipo'=>$f->camion,
              'posicion'=>$f->posicion,
              'tipo'=>'Presión Alta',
              'inicio'=>new DateTime($f->fecha),
              'fin'=>new DateTime($f->fecha),
              'umbral'=>$f->pres_max,
              'extremo'=>$f->presion,
              'duracion'=>''
            );
            $bucle = true;
          }

          while($bucle){
            $bucle = false;
            //Presión Alta
            $umbral = $f->pres_max * ((100 - $porcentaje_pmax) / 100);
            
            $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id AND presion < pres_max ORDER BY id ASC LIMIT 1");
            if($f2->count() == 1){
              $f2 = $f2->results()[0];
              $extremo = $ut->query("SELECT MAX(presion) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
              $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
              $datos[$f->id]['fin'] = new DateTime($f2->fecha);
              if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
            }
            else{
              $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id ORDER BY id DESC LIMIT 1");
              if($f2->count() == 1){
                $f2 = $f2->results()[0];
                $extremo = $ut->query("SELECT MAX(presion) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
                $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
                $datos[$f->id]['fin'] = new DateTime($f2->fecha);
                if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
              }
            }
            
            if($f2->id){
              $f = $ut->query("SELECT * FROM $table WHERE presion >= pres_max AND id > $f2->id ORDER BY id ASC LIMIT 1");
              if($f->count() == 1){
                //Guardar dato
                $f = $f->results()[0];
                $datos[$f->id] = array(
                  'equipo'=>$f->camion,
                  'posicion'=>$f->posicion,
                  'tipo'=>'Presión Alta',
                  'inicio'=>new DateTime($f->fecha),
                  'fin'=>new DateTime($f->fecha),
                  'umbral'=>$f->pres_max,
                  'extremo'=>$f->presion,
                  'duracion'=>''
                );
                $bucle = true;
              }
            }
          }
        #endregion

        #region Temperatura Alta
          $f = $ut->query("SELECT * FROM $table WHERE temperatura >= temp_max ORDER BY id ASC LIMIT 1");
          $bucle = false;
          if($f->count() == 1){
            //Guardar dato
            $f = $f->results()[0];
            $datos[$f->id] = array(
              'equipo'=>$f->camion,
              'posicion'=>$f->posicion,
              'tipo'=>'Temperatura',
              'inicio'=>new DateTime($f->fecha),
              'fin'=>new DateTime($f->fecha),
              'umbral'=>$f->temp_max,
              'extremo'=>$f->temperatura,
              'duracion'=>''
            );
            $bucle = true;
          }

          while($bucle){
            $bucle = false;
            //Temperatura
            $umbral = $f->temp_max * ((100 - $porcentaje_tmax) / 100);
            
            $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id AND temperatura < temp_max ORDER BY id ASC LIMIT 1");
            if($f2->count() == 1){
              $f2 = $f2->results()[0];
              $extremo = $ut->query("SELECT MAX(temperatura) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
              $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
              $datos[$f->id]['fin'] = new DateTime($f2->fecha);
              if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
            }
            else{
              $f2 = $ut->query("SELECT * FROM $table WHERE id > $f->id ORDER BY id DESC LIMIT 1");
              if($f2->count() == 1){
                $f2 = $f2->results()[0];
                $extremo = $ut->query("SELECT MAX(temperatura) AS extremo FROM $table WHERE id >= $f->id AND id <= $f2->id");
                $extremo = $extremo->count() == 1 ? $extremo->results()[0]->extremo : NULL;
                $datos[$f->id]['fin'] = new DateTime($f2->fecha);
                if($extremo != NULL) $datos[$f->id]['extremo'] = $extremo;
              }
            }
            
            $f = $ut->query("SELECT id FROM $table WHERE temperatura >= temp_max AND id > $f2->id ORDER BY id ASC LIMIT 1");
            if($f->count() == 1){
              //Guardar dato
              $f = $f->results()[0];
              $datos[$f->id] = array(
                'equipo'=>$f->camion,
                'posicion'=>$f->posicion,
                'tipo'=>'Temperatura',
                'inicio'=>new DateTime($f->fecha),
                'fin'=>new DateTime($f->fecha),
                'umbral'=>$f->temp_max,
                'extremo'=>$f->temperatura,
                'duracion'=>''
              );
              $bucle = true;
            }
          }
        #endregion

        $ut->query("DELETE FROM $table WHERE id>0");
      }
    }

    $ut->query("DROP TABLE IF EXISTS $table");

    // print_r($fallas);exit();
    $i = 1;
    $fallas = array();
    foreach($datos as $ff){
      $duracion = $ff['inicio']->diff($ff['fin']);
      // if($duracion->format('%d') > 1) $ff['duracion'] = $duracion->format('%d días ');
      // else if($duracion->format('%d') == 1) $ff['duracion'] = $duracion->format('%d día ');

      // if($duracion->format('%h') > 1) $ff['duracion'] .= $duracion->format('%h horas ');
      // else if($duracion->format('%h') == 1) $ff['duracion'] .= $duracion->format('%h hora ');

      // if($duracion->format('%i') > 1) $ff['duracion'] .= $duracion->format('%i minutos ');
      // else if($duracion->format('%i') == 1) $ff['duracion'] .= $duracion->format('%i minuto ');

      // if($duracion->format('%s') > 1) $ff['duracion'] .= $duracion->format('%s segundos ');
      // else if($duracion->format('%s') == 1) $ff['duracion'] .= $duracion->format('%s segundo ');

      $ff['duracion'] = 0;
      $ff['duracion'] = (($duracion->format('%d') * 86400) + ($duracion->format('%h') * 3600) + ($duracion->format('%i') * 60) + $duracion->format('%s'));

      $diff = $duracion->format('%s') + $duracion->format('%i')*60 + $duracion->format('%h')*3600 + $duracion->format('%d')*86400;

      //Se desestiman las duraciones inferiores a 6 minutos
      if($diff >= $tiempo_falla && $ff['equipo']!=''){
        $ff['num']       = $i++;
        $ff['inicio']    = $ff['inicio']->format('d/m/Y H:i:s');
        $ff['fin']       = $ff['fin']->format('d/m/Y H:i:s');

        // $alarma[8] 		= "Timeout";
        // $alarma[16] 	= "Bateria baja";
        // $alarma[32] 	= "Temperatura";
        // $alarma[64] 	= "Presi&oacute;n baja";
        // $alarma[128] 	= "Presi&oacute;n alta"; 

        $tipo = 0;
        if($ff['tipo'] == 'Presión Baja' || $ff['tipo'] == 'Presión Alta'){
          $ff['umbral']    = Core::tpConvert($ff['umbral'], $unidad_pres, true);
          $ff['extremo']   = Core::tpConvert($ff['extremo'], $unidad_pres, true);
          $tipo = $ff['tipo'] == 'Presión Baja' ? 64 : 128;          
        }
        else{
          $ff['umbral']    = Core::tpConvert($ff['umbral'], $unidad_temp, true);
          $ff['extremo']   = Core::tpConvert($ff['extremo'], $unidad_temp, true);
          $tipo = 32;
        }
        $ff['acciones']  = '<button class="btn btn-default btn-xs" 
            onClick="detalle('.$ff['equipo'].','.$ff['posicion'].',\''.$ff['inicio'].' - '.$ff['fin'].'\', \'eventos\');"
            title="Eventos" data-toggle="tooltip" data-placement="top">
          <i class="fa fa-search-plus" aria-hidden="true"></i>
          </button>';
        $ff['acciones'] .= '<button class="btn btn-danger btn-xs" 
            onClick="detalle('.$ff['equipo'].','.$ff['posicion'].',\''.$ff['inicio'].' - '.$ff['fin'].'\', \'alarmas\', '.$tipo.');"
            title="Alarmas" data-toggle="tooltip" data-placement="top">
          <i class="fa fa-search-plus" aria-hidden="true"></i>
          </button>';
        $ff['acciones'] .= '<button class="btn btn-default btn-xs"
            onClick="detalle('.$ff['equipo'].','.$ff['posicion'].',\''.$ff['inicio'].' - '.$ff['fin'].'\', \'grafico\');"
            title="Gráfico" data-toggle="tooltip" data-placement="top">
          <i class="fa fa-line-chart" aria-hidden="true"></i>
          </button>';
        //Conversión y colocación de unidades de medida y nomenclaturas
        $ff['posicion']  = $nomenclatura[$ff['posicion']];

        $fallas[]        = $ff;
      }
    }

    $data["data"]   = $fallas;
    $data['titulo'] = $titulo;
    
  }

  header("COntent-Type: application/json");
  echo json_encode($data);