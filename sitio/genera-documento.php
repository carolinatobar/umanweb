<?php
  ini_set("include_path", '/home/uman/php:' . ini_get("include_path") );
  require 'autoload.php';
  // PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
  // var_dump(extension_loaded ('zip'));
  ini_set("max_execution_time","6000");
  ini_set('display_errors','on');
  
  error_reporting(E_ALL);
  session_start();
  // var_dump($_SESSION[session_id()]);
  if(isset($_SESSION[session_id()]['nombrefaena']) && isset($_SESSION[session_id()]['user']))
    $archivo = $_SESSION[session_id()]['nombrefaena'] .' - '. $_SESSION[session_id()]['user'];
  else{
    echo 'ha ocurrido un problema con la sesión.';
    exit();
  }

  $documento = isset($_REQUEST['documento']) ? $_REQUEST['documento'] : null;
  $tipo      = isset($_REQUEST['tipo']) ? strtolower($_REQUEST['tipo']) : null;

  // var_dump($_REQUEST); exit();

  $permitidos = array('xls','json','csv');
  $mime = array(
    'xls'=>'application/vnd.ms-excel',
    'json'=>'application/json',
    'csv'=>'text/csv',
    // 'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
  );

  $render = new Render();

  if(!in_array($tipo,$permitidos))
  {  
    $error = $render->make_block(
        'Tipo de archivo no permitido',
        'El tipo de archivo requerido ('.$tipo.') no está permitido actualmente. <br/>De momento usted puede generar archivos .xls, .csv o .json.'
      );
    echo $error;
    exit();
  }

  if($documento == 'presion_temperatura')
  {
    $equipo = isset($_REQUEST['equipo']) ? $_REQUEST['equipo'] : null;
    $fecha  = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : null;

    $f = $fecha;
      //Default Time
    $df_time1  = "00:00:00";
    $df_time2  = "23:59:59";

    if($fecha == null)
    {
      $ayer   = date("m/d/Y H:i:s",time() - ($tiempo*3600));
      $hoy    = date("m/d/Y H:i:s",time());
      $fecha  = "$ayer - $hoy";
    }

    $fecha = explode(" - ", $fecha);
    if($fecha[0] != $fecha[1])
    {
      $fecha = "UNIX_TIMESTAMP(ue.EVENTOFECHA) 
      BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
    }
    else
    {
      $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(ue.EVENTOFECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
    }    

    if(is_numeric($equipo) && $fecha != '')
    {
      
      $db = DB::getInstance();
      
      $sql = sprintf("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=%d;", $equipo);
      $camion = $db->query($sql);
      $camion = $camion->count() > 0 ? $camion->results()[0]->NUMCAMION : $equipo;
      
      $archivo .= ' - EQ' . $camion . ' - ' . trim(str_replace(' ','',str_replace('/','',str_replace(':','',str_replace(' - ','_',$f)))));

      $sql = "SELECT 
        uc.NUMCAMION,
        ue.EVENTOCODSENSOR,
        ue.EVENTOPOSICION,
        ue.EVENTOFECHA,
        ue.EVENTOTEMPERATURA,
        ue.EVENTOPRESION,
        ue.EVENTODESCARGA,
        ue.TEMPMAX,
        ue.PRESMIN,
        ue.PRESMAX 
        FROM uman_eventos ue INNER JOIN uman_camion uc ON uc.ID_CAMION = ue.EVENTONUMCAMION 
        WHERE uc.ID_CAMION = $equipo AND $fecha 
        ORDER BY EVENTOFECHA DESC";        
      
      $data = $db->query($sql);
      if($data->count() > 0)
      {
        if($tipo == 'json')
        {
          header("Content-type: text/json");
          echo json_encode($data->results());
        }
        elseif($tipo == 'xls')
        {
          $excel = new PHPExcel();
          $excel->getProperties()->setCreator("BAILAC")
						->setLastModifiedBy("BAILAC")
						->setTitle("Tabla de Presión / Temperatura")
						->setSubject("Tabla de Presión / Temperatura.")
						->setDescription("Test document for PHPExcel, generated using PHP classes.")
						->setKeywords("bailac uman presión temperatura")
						->setCategory("Test result file");

          $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', '#')
            ->setCellValue('B1', 'Equipo')
            ->setCellValue('C1', 'Sensor')
            ->setCellValue('D1', 'Posición')
            ->setCellValue('E1', 'Fecha Evento')
            ->setCellValue('F1', 'Temperatura')
            ->setCellValue('G1', 'Presión')
            ->setCellValue('H1', 'Fecha Descarga.')
            ->setCellValue('I1', 'Umbral Temp')
            ->setCellValue('J1', 'Umbral P. Baja')
            ->setCellValue('K1', 'Umbral P. Alta');

          $num_camion = '';
          $fila = 2;
          foreach($data->results() as $d)
          {
            $num_camion = $d->NUMCAMION;

            $excel->setActiveSheetIndex(0)
              ->setCellValue('A'.$fila, $fila-1)
              ->setCellValue('B'.$fila, $d->NUMCAMION)
              ->setCellValue('C'.$fila, $d->EVENTOCODSENSOR)
              ->setCellValue('D'.$fila, $d->EVENTOPOSICION)
              ->setCellValue('E'.$fila, (new DateTime($d->EVENTOFECHA))->format("d/m/Y H:i:s"))
              ->setCellValue('F'.$fila, $d->EVENTOTEMPERATURA)
              ->setCellValue('G'.$fila, $d->EVENTOPRESION)
              ->setCellValue('H'.$fila, (new DateTime($d->EVENTODESCARGA))->format("d/m/Y H:i:s"))
              ->setCellValue('I'.$fila, $d->TEMPMAX)
              ->setCellValue('J'.$fila, $d->PRESMIN)
              ->setCellValue('K'.$fila, $d->PRESMAX);

            $excel->getActiveSheet()
              ->getStyle('C'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $excel->getActiveSheet()
              ->getStyle('E'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
            $excel->getActiveSheet()
              ->getStyle('H'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

            $fila++;
          }

          $excel->setActiveSheetIndex(0);

          $excel->getActiveSheet()->getStyle('A1:K1')
            ->applyFromArray(array(
                'fill'=>array(
                  'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                  'color'=>array('argb'=>'FF8C9EFF')
                 ),
                'font'=>array(
                  'color'=>array('argb'=>'ff212121'),
                  'bold'=>true
                 )
              ));          
          $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

          $excel->getActiveSheet()->getStyle('A1:K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

          /* Guardar a archivo */
          // $callStartTime = microtime(true);
          // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
          // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
          // $callEndTime = microtime(true);
          // $callTime = $callEndTime - $callStartTime;

          /* Generar descargable */
          // Redirect output to a client’s web browser (Excel2007)
          // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Type: '.$mime[$tipo]);
          header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
          // header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          // header('Cache-Control: max-age=1');
          // If you're serving to IE over SSL, then the following may be needed
          // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
          // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          // header ('Pragma: public'); // HTTP/1.0
          $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
          $objWriter->save('php://output');
          exit();
        }
      }
      else
      {
        $error = $render->make('error_fullpage', 
          array(
            'title'=>'No hay datos',
            'content'=>'No hay datos, por lo tanto no se generará el archivo solicitado, para evitar este mensaje, por favor asegúrese de seleccionar un periodo con datos.',
            'footer'=>$sql)
          );
        echo $error;
        exit();
      }
    }

  }elseif($documento == 'historico_neumatico')
  {
    $neumatico = isset($_REQUEST['neumatico']) ? $_REQUEST['neumatico'] : null;
    $fecha  = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : null;

    $f = $fecha;
      //Default Time
    $df_time1  = "00:00:00";
    $df_time2  = "23:59:59";

    if($fecha == null)
    {
      $ayer   = date("m/d/Y H:i:s",time() - ($tiempo*3600));
      $hoy    = date("m/d/Y H:i:s",time());
      $fecha  = "$ayer - $hoy";
    }

    $fecha = explode(" - ", $fecha);
    
    if(is_numeric($neumatico) && $fecha != ''){
      
      $db = DB::getInstance();
      
      $sql = sprintf("SELECT NUMIDENTI FROM uman_neumaticos WHERE ID_NEUMATICO=%d;", $neumatico);
      $n = $db->query($sql);
      $n = $n->count() > 0 ? $n->results()[0]->NUMIDENTI : $neumatico;
      
      $archivo .= ' - ' . $n . ' - ' . trim(str_replace(' ','',str_replace('/','',str_replace(':','',str_replace(' - ','_',$f)))));

      $tabla = array();
      $tabla2 = array();
      $tabla3 = array();

      $myDateTimeI = DateTime::createFromFormat('d/m/Y H:i', $fecha[0]);
      $fecha[0] = $myDateTimeI->format('Y-m-d H:i:s');

      $myDateTimeI = DateTime::createFromFormat('d/m/Y H:i', $fecha[1]);
      $fecha[1] = $myDateTimeI->format('Y-m-d H:i:s');

      $sql   = "SELECT uh.*,uc.NUMCAMION,us.CODSENSOR FROM uman_historial uh INNER JOIN uman_camion uc ON uh.ID_CAMION=uc.ID_CAMION INNER JOIN uman_sensores us ON uh.ID_SENSOR=us.ID_SENSOR WHERE uh.ID_NEUMATICO ={$neumatico} AND (uh.ACCION LIKE  'Neumatico instalado en equipo' OR uh.ACCION LIKE  'Neumatico retirado de equipo') ORDER BY uh.FECHA ASC";
      // echo $sql;exit();
      $fetch = $db->query($sql);
      $fetch = ($fetch->count()>0) ? $fetch->results() : array();
      for($i=0;$i<count($fetch);$i++){
        $in = $fetch[$i];
        $i++;
        $tabla[$in->ID]= array('equipo'=>$in->NUMCAMION,'sensor'=>$in->CODSENSOR,'posicion'=>$in->ID_POSICION,'desde'=>$in->FECHA,'hasta'=>'','datos'=>0,'id_camion'=>$in->ID_CAMION,'id_sensor'=>$in->ID_SENSOR);

        $desde = '-';
        $hasta = '-';
        
        $sql_datos = "SELECT COUNT( * ) AS tot
          FROM uman_eventos
          WHERE EVENTONUMCAMION ={$in->ID_CAMION}
          AND EVENTOPOSICION =$in->ID_POSICION
          AND EVENTOFECHA >=  '{$in->FECHA}'";
        if($i<count($fetch)){
          $out = $fetch[$i];
          $sql_fecha = "SELECT EVENTOFECHA
            FROM uman_eventos
            WHERE EVENTONUMCAMION ={$in->ID_CAMION}
            AND EVENTOPOSICION ={$in->ID_POSICION}
            AND EVENTOFECHA <=  '{$out->FECHA}'
            AND EVENTOFECHA >=  '{$in->FECHA}'
            ORDER BY EVENTOFECHA ASC";
          $hasta = '-';
          $datos_fecha = $db->query($sql_fecha);
          if($datos_fecha->count()>0){
            $datos_fecha2 = $datos_fecha->results()[0];
            $desde = $datos_fecha2->EVENTOFECHA;
            $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
            $hasta = $datos_fecha2->EVENTOFECHA;
          }
          $tabla[$in->ID]['hasta']=$hasta;
          $sql_datos .= " AND EVENTOFECHA <= '{$out->FECHA}'";
        }else{
          $sql_fecha = "SELECT EVENTOFECHA
            FROM uman_eventos
            WHERE EVENTONUMCAMION ={$in->ID_CAMION}
            AND EVENTOPOSICION ={$in->ID_POSICION}
            AND EVENTOFECHA >=  '{$in->FECHA}'
            AND EVENTOFECHA <=  NOW()
            ORDER BY EVENTOFECHA ASC";
          
          $datos_fecha = $db->query($sql_fecha);
          if($datos_fecha->count()>0){
            $datos_fecha2 = $datos_fecha->results()[0];
            $desde = $datos_fecha2->EVENTOFECHA;
            $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
            $hasta = $datos_fecha2->EVENTOFECHA;
          }
          
        }
        $tabla[$in->ID]['desde']=$desde;
        $tabla[$in->ID]['hasta']=$hasta;
        $datos = $db->query($sql_datos);
        $datos = $datos->results()[0];
        $tabla[$in->ID]['datos']=$datos->tot;
        
        if($tabla[$in->ID]['desde']!='-'){
          if(strtotime($tabla[$in->ID]['desde'])<=strtotime($fecha[0])){
            if($tabla[$in->ID]['hasta']!='-'){
              if(strtotime($tabla[$in->ID]['hasta'])>=strtotime($fecha[0])){
                $tabla2[$in->ID]=$tabla[$in->ID];
                if(strtotime($tabla[$in->ID]['desde'])<strtotime($fecha[0])){
                  $tabla2[$in->ID]['desde']=$fecha[0];
                }
                if(strtotime($tabla[$in->ID]['hasta'])>strtotime($fecha[1])){
                  $tabla2[$in->ID]['hasta']=$fecha[1];
                }
              }
            }else{
              $tabla2[$in->ID]=$tabla[$in->ID];
              if(strtotime($tabla[$in->ID]['desde'])<strtotime($fecha[0])){
                $tabla2[$in->ID]['desde']=$fecha[0];
              }
              $tabla2[$in->ID]['hasta']=$fecha[1];
            }
          }else{
            if(strtotime($tabla[$in->ID]['desde'])<=strtotime($fecha[1])){
              if($tabla[$in->ID]['hasta']!=''){
                if(strtotime($tabla[$in->ID]['hasta'])>=strtotime($fecha[1])){
                  $tabla2[$in->ID]=$tabla[$in->ID];
                  if(strtotime($tabla[$in->ID]['hasta'])>strtotime($fecha[1])){
                    $tabla2[$in->ID]['hasta']=$fecha[1];
                  }         
                }else{
                  $tabla2[$in->ID]=$tabla[$in->ID];
                }
              }else{
                $tabla2[$in->ID]=$tabla[$in->ID];
                $tabla2[$in->ID]['hasta']=$fecha[1];
              }
            }
          }
        }
      }

      //grafico
      if($tipo == 'xls'){

        $excel = new PHPExcel();
        $excel->getProperties()->setCreator("BAILAC")
              ->setLastModifiedBy("BAILAC")
              ->setTitle("Tabla Histórico de Neumático")
              ->setSubject("Tabla Histórico de Neumático.")
              ->setDescription("Test document for PHPExcel, generated using PHP classes.")
              ->setKeywords("bailac uman presión temperatura")
              ->setCategory("Test result file");

        $excel->setActiveSheetIndex(0)
              ->setCellValue('A1', '#')
              ->setCellValue('B1', 'Equipo')
              ->setCellValue('C1', 'Sensor')
              ->setCellValue('D1', 'Posición')
              ->setCellValue('E1', 'Fecha Evento')
              ->setCellValue('F1', 'Temperatura')
              ->setCellValue('G1', 'Presión')
              ->setCellValue('H1', 'Fecha Descarga.')
              ->setCellValue('I1', 'Umbral Temp')
              ->setCellValue('J1', 'Umbral P. Baja')
              ->setCellValue('K1', 'Umbral P. Alta');

        $num_camion = '';
        $fila = 2;
        foreach($tabla2 as $pos => $t){
            $sql = "SELECT e.EVENTOCODSENSOR,
            e.EVENTOPOSICION,
            e.EVENTOFECHA,
            e.EVENTOTEMPERATURA,
            e.EVENTOPRESION,
            e.EVENTODESCARGA,
            e.TEMPMAX,
            e.PRESMIN,
            e.PRESMAX   
            FROM uman_eventos AS e 
            WHERE EVENTONUMCAMION ={$t['id_camion']}
            AND EVENTOPOSICION ={$t['posicion']}
            AND EVENTOFECHA >=  '{$t['desde']}'
            AND EVENTOFECHA <=  '{$t['hasta']}'
            ORDER BY e.EVENTOFECHA ASC";


            $fetch = $db->query($sql);
            $fetch = ($fetch->count()>0) ? $fetch->results() : array();

          
            foreach($fetch as $d)
            {
              $excel->setActiveSheetIndex(0)
                ->setCellValue('A'.$fila, $fila-1)
                ->setCellValue('B'.$fila, $t['equipo'])
                ->setCellValue('C'.$fila, $d->EVENTOCODSENSOR)
                ->setCellValue('D'.$fila, $d->EVENTOPOSICION)
                ->setCellValue('E'.$fila, (new DateTime($d->EVENTOFECHA))->format("d/m/Y H:i:s"))
                ->setCellValue('F'.$fila, $d->EVENTOTEMPERATURA)
                ->setCellValue('G'.$fila, $d->EVENTOPRESION)
                ->setCellValue('H'.$fila, (new DateTime($d->EVENTODESCARGA))->format("d/m/Y H:i:s"))
                ->setCellValue('I'.$fila, $d->TEMPMAX)
                ->setCellValue('J'.$fila, $d->PRESMIN)
                ->setCellValue('K'.$fila, $d->PRESMAX);

              $excel->getActiveSheet()
                ->getStyle('C'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $excel->getActiveSheet()
                ->getStyle('E'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
              $excel->getActiveSheet()
                ->getStyle('H'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

              $fila++;
            }
          }
          $excel->setActiveSheetIndex(0);

          $excel->getActiveSheet()->getStyle('A1:K1')
            ->applyFromArray(array(
                'fill'=>array(
                  'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                  'color'=>array('argb'=>'FF8C9EFF')
                 ),
                'font'=>array(
                  'color'=>array('argb'=>'ff212121'),
                  'bold'=>true
                 )
              ));          
          $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
          $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

          $excel->getActiveSheet()->getStyle('A1:K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

          /* Guardar a archivo */
          // $callStartTime = microtime(true);
          // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
          // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
          // $callEndTime = microtime(true);
          // $callTime = $callEndTime - $callStartTime;

          /* Generar descargable */
          // Redirect output to a client’s web browser (Excel2007)
          // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Type: '.$mime[$tipo]);
          header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
          // header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          // header('Cache-Control: max-age=1');
          // If you're serving to IE over SSL, then the following may be needed
          // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
          // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          // header ('Pragma: public'); // HTTP/1.0
          $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
          $objWriter->save('php://output');
          exit();
        }
      }else{
        $error = $render->make('error_fullpage', 
        array(
                'title'=>'No hay datos',
                'content'=>'No hay datos, por lo tanto no se generará el archivo solicitado, para evitar este mensaje, por favor asegúrese de seleccionar un periodo con datos.',
                'footer'=>$sql)
              );
        echo $error;
        exit();
      }

    

  }
  
  elseif($documento == 'datos_umanweb')
  {
    $f = date('d-m-Y');
   $archivo = 'Datos UmanWeb - ' . trim(str_replace(' ','',str_replace('/','',str_replace(':','',str_replace(' - ','_',$f)))));
      // echo $archivo; exit();
    $sql = "SELECT
      c.NUMCAMION AS EQUIPO,
      po.NOMENCLATURA AS POSICION,
      p.MARCA,
      n.MODELO,
      s.CODSENSOR AS SENSOR,
      n.NUMIDENTI AS NEUMATICO,
      u.tempmax AS TEMPMAX,
      u.presmax AS PRESMAX,
      u.presmin AS PRESMIN,
      u.eventopresion AS PRESION,
      u.eventotemperatura AS TEMPERATURA,
      u.eventobateria AS BATERIA,
      u.fecha_evento AS FECHA_EVENTO,
      u.fecha_descarga AS FECHA_DESCARGA,
      p.PIF,
      ROUND((p.PIF * (u.eventotemperatura + 273.15) / 291.15)) AS PIC
      FROM uman_camion c 
        LEFT JOIN uman_neumatico_camion nc ON c.ID_CAMION=nc.ID_EQUIPO
        LEFT JOIN uman_neumaticos n ON n.ID_NEUMATICO=nc.ID_NEUMATICO
        LEFT JOIN uman_ultimoevento u ON u.posicion=nc.ID_POSICION
        LEFT JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR
        LEFT JOIN uman_plantilla p ON p.ID_PLANTILLA=n.ID_PLANTILLA
        LEFT JOIN uman_posicion po ON po.POSICION=nc.ID_POSICION
        WHERE fecha_evento NOT LIKE '0000-00-00%'";        
    $db = DB::getInstance();
      
    $data = $db->query($sql);
    if($data->count() > 0)
    {
      if($tipo == 'json')
      {
        header('Content-Type: '.$mime[$tipo]);
        header('Content-Disposition: attachment;filename="'.$archivo.'.json"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        echo json_encode($data->results());
      }
      elseif($tipo == 'csv')
      {
        $content = "ID,Equipo,Posicion,Marca,Modelo,Sensor,Serie neumatico,T.Max,P.Max,P.Min,Eventopresion,Eventotemperatura,Bateria,EventoFecha,Ultima Actualizacion,PIF,PIC\n";
        $i=1;
        foreach($data->results() as $d){
          $content .= "{$i},{$d->EQUIPO},{$d->POSICION},{$d->MARCA},{$d->MODELO},{$d->SENSOR},{$d->NEUMATICO}";
          $content .= "{$d->TEMPMAX},{$d->PRESMAX},{$d->PRESMIN},{$d->PRESION},{$d->TEMPERATURA},{$d->BATERIA}";
          $content .= "{$d->FECHA_EVENTO},{$d->FECHA_DESCARGA},{$d->PIF},{$d->PIC}\n";
          $i++;
        }

        header('Content-Type: '.$mime[$tipo]);
        header('Content-Disposition: attachment;filename="'.$archivo.'.csv"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        echo $content;
        exit;
      }
      elseif($tipo == 'xls')
      {
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator("BAILAC")
          ->setLastModifiedBy("BAILAC")
          ->setTitle("Datos UmanWeb")
          ->setSubject("Datos UmanWeb")
          ->setDescription("Datos UmanWeb")
          ->setKeywords("bailac uman datos umanweb")
          ->setCategory("Datos UmanWeb");

        $excel->setActiveSheetIndex(0)
          ->setCellValue('A1', '#')
          ->setCellValue('B1', 'Equipo')
          ->setCellValue('C1', 'Posición')
          ->setCellValue('D1', 'Marca')
          ->setCellValue('E1', 'Modelo')
          ->setCellValue('F1', 'Sensor')
          ->setCellValue('G1', 'Neumático')
          ->setCellValue('H1', 'Temp. Máx.')
          ->setCellValue('I1', 'Pres. Máx.')
          ->setCellValue('J1', 'Pres. Mín.')
          ->setCellValue('K1', 'Presión')
          ->setCellValue('L1', 'Temperatura')
          ->setCellValue('M1', 'Batería')
          ->setCellValue('N1', 'Fecha Evento')
          ->setCellValue('O1', 'Fecha Descarga')
          ->setCellValue('P1', 'PIF')
          ->setCellValue('Q1', 'PIC');

        $num_camion = '';
        $fila = 2;
        foreach($data->results() as $d)
        {
          $num_camion = $d->NUMCAMION;

          $excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$fila, $fila-1)
            ->setCellValue('B'.$fila, $d->EQUIPO)
            ->setCellValue('C'.$fila, $d->POSICION)
            ->setCellValue('D'.$fila, $d->MARCA)
            ->setCellValue('E'.$fila, $d->MODELO)
            ->setCellValue('F'.$fila, $d->SENSOR)
            ->setCellValue('G'.$fila, $d->NEUMATICO)
            ->setCellValue('H'.$fila, $d->TEMPMAX)
            ->setCellValue('I'.$fila, $d->PRESMAX)
            ->setCellValue('J'.$fila, $d->PRESMIN)
            ->setCellValue('K'.$fila, $d->PRESION)
            ->setCellValue('L'.$fila, $d->TEMPERATURA)
            ->setCellValue('M'.$fila, $d->BATERIA)
            ->setCellValue('N'.$fila, (new DateTime($d->FECHA_EVENTO))->format("d/m/Y H:i:s"))
            ->setCellValue('O'.$fila, (new DateTime($d->FECHA_DESCARGA))->format("d/m/Y H:i:s"))
            ->setCellValue('P'.$fila, $d->PIF)
            ->setCellValue('Q'.$fila, $d->PIC);

          $excel->getActiveSheet()
            ->getStyle('C'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
          $excel->getActiveSheet()
            ->getStyle('D'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
          $excel->getActiveSheet()
            ->getStyle('E'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
          $excel->getActiveSheet()
            ->getStyle('F'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
          $excel->getActiveSheet()
            ->getStyle('G'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
          $excel->getActiveSheet()
            ->getStyle('N'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
          $excel->getActiveSheet()
            ->getStyle('O'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

          $fila++;
        }

        $excel->setActiveSheetIndex(0);

        // $excel->getActiveSheet()->getStyle('A1:K1')
        //   ->applyFromArray(array(
        //       'fill'=>array(
        //         'type'=>PHPExcel_Style_Fill::FILL_SOLID,
        //         'color'=>array('argb'=>'FF8C9EFF')
        //         ),
        //       'font'=>array(
        //         'color'=>array('argb'=>'ff212121'),
        //         'bold'=>true
        //         )
        //     ));          
        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);

        $excel->getActiveSheet()->getStyle('A1:Q'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        /* Guardar a archivo */
        // $callStartTime = microtime(true);
        // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        // $callEndTime = microtime(true);
        // $callTime = $callEndTime - $callStartTime;

        /* Generar descargable */
        // Redirect output to a client’s web browser (Excel2007)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: '.$mime[$tipo]);
        header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit;
      }
    }
  }

  elseif($documento == 'tabla_cobertura'){
    $db = DB::getInstance();
    $sql = "SELECT c.ID_CAMION, c.NUMCAMION, f.NOMBRE, te.NEUMATICOS
      FROM uman_camion c 
      INNER JOIN uman_flotas f ON c.NUMFLOTA=f.NUMFLOTAS
      INNER JOIN uman_tipo_equipo te ON te.ID=c.tipo";

    $equipos = $db->query($sql);

    $content = array();
    $i = 1;

    if($equipos->count() > 0){
      $equipos = $equipos->results();
      foreach($equipos as $e){
        //CANTIDAD DE NEUMÁTICOS
        $neumaticos = array_sum(explode(',',$e->NEUMATICOS));

        //OBTENER LA CANTIDAD REAL DE SENSORES INSTALADOS
        $sql = "SELECT ID_POSICION AS posicion
          FROM uman_neumatico_camion nc INNER JOIN uman_neumaticos n ON nc.ID_NEUMATICO=n.ID_NEUMATICO
          WHERE n.ID_SENSOR > 0 AND n.ID_SENSOR != ''  AND n.ID_SENSOR IS NOT NULL
          ORDER BY ID_POSICION ASC";
        $sensores = $db->query($sql);

        $sql = "SELECT DISTINCTROW  posicion
          FROM uman_ultimoevento
          WHERE fecha_evento < DATE_SUB(NOW(), INTERVAL 1 HOUR) AND numequipo={$e->ID_CAMION}";
        $enLinea = $db->query($sql);
        
        $c = array();
        $c['correlativo'] = $i;
        $c['flota']       = $e->NOMBRE;
        $c['equipo']      = $e->NUMCAMION;
        $c['neumaticos']  = $neumaticos;
        $c['sensores']    = $sensores->count();
        $c['emitiendo']   = $enLinea->count();
        $c['sensor']      = array_fill(1,$neumaticos,0);

        foreach($sensores as $s){
          foreach($enLinea as $l){
            if($s->posicion == $l->posicion){
              $c['sensor'][$s->posicion] = 1;
              break;
            }
          }
        }

        $content[] = $c;
      }

      print_r($content);
      
    }

  }

  elseif($documento == 'velocidad_recorrido'){
    $equipo = isset($_REQUEST['equipo']) ? $_REQUEST['equipo'] : null;
    $fecha  = isset($_REQUEST['fecha'])  ? $_REQUEST['fecha']  : null;

    // var_dump($_REQUEST); exit();

    $archivo = 'velocidad-recorrido - EQ_'.$equipo;
    
    if($fecha != null) {
      if(stripos($fecha,' - ') > 0){
        $fecha = explode(" - ", $fecha);
        if($fecha[0] != $fecha[1]){
          $rango = "$fecha[0] - $fecha[1]";
          $fecha = "UNIX_TIMESTAMP(FECHA) 
            BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
        }
        else{
          $rango = $fecha[0];
          $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(FECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
        }
      }
      else{
        $fecha = $fecha;
        $rango = $fecha;
        $fecha = "UNIX_TIMESTAMP(FECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
      }
    } 
    else {
      $fecha1 = date('d/m/Y H:i:s', time() - (24*3600));
      $fecha2 = date('d/m/Y H:i:s', time());
      $fecha = "UNIX_TIMESTAMP(FECHA) 
      BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR)) AND UNIX_TIMESTAMP(NOW())";

      $rango = "$fecha1 - $fecha2";
    }
    
    $db = DB::getInstance();
    
    $sql = sprintf("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=%d;", $equipo);
    $camion = $db->query($sql);
    $camion = $camion->count() > 0 ? $camion->results()[0]->NUMCAMION : $equipo;

    $sql = "SELECT * FROM uman_gps WHERE EQUIPO={$equipo} AND $fecha";
    $data = $db->query($sql);

    if($data->count() > 0){
      if($tipo == 'csv'){
        $correlativo = 1;
        $content = '"ID","EQUIPO","FECHA","X","Y","rapidez","direccion","ALTURA","FECHADESCARGA"'."\n";
        foreach($data->results() as $d){
          $content .= '"'.$d->ID.'", "'.$camion.'", "'.$d->FECHA.'", "'.$d->X.'", "'.$d->Y.'", "'.$d->rapidez.'", "'.$d->direccion.'", "'.$d->ALTURA.'", "'.$d->FECHADESCARGA.'"'."\n";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="'.$archivo.'.csv"');
        echo $content;
        exit();
      }

      if($tipo == 'xls'){
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator("BAILAC")
          ->setLastModifiedBy("BAILAC")
          ->setTitle("Tabla de Velocidad / Recorrido")
          ->setSubject("Tabla de Velocidad / Recorrido.")
          ->setDescription("Test document for PHPExcel, generated using PHP classes.")
          ->setKeywords("bailac uman velocidad recorrido")
          ->setCategory("Test result file");

        $excel->setActiveSheetIndex(0)
          ->setCellValue('A2', '#')
          ->setCellValue('B2', 'Fecha')
          ->setCellValue('C2', 'Latitud')
          ->setCellValue('D2', 'Longitud')
          ->setCellValue('E2', 'Velocidad')
          ->setCellValue('F2', 'Dirección')
          ->setCellValue('G2', 'Altura')
          ->setCellValue('H2', 'Fecha Descarga');

        $excel->getActiveSheet()->mergeCells('C1:D1');
        $excel->getActiveSheet()->mergeCells('E1:H1');
        $excel->getActiveSheet()
          ->setCellValue('A1', 'Equipo :  ')
          ->setCellValue('B1', $camion)
          ->setCellValue('C1', 'Rango Seleccionado :  ')
          ->setCellValue('E1', $rango);
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $excel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $excel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        

        $fila = 3;
        foreach($data->results() as $d)
        {
          $excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$fila, $fila-2)
            ->setCellValue('B'.$fila, (new DateTime($d->FECHA))->format("d/m/Y H:i:s"))
            ->setCellValue('C'.$fila, $d->X)
            ->setCellValue('D'.$fila, $d->Y)
            ->setCellValue('E'.$fila, $d->rapidez)
            ->setCellValue('F'.$fila, $d->direccion)
            ->setCellValue('G'.$fila, $d->ALTURA)
            ->setCellValue('H'.$fila, (new DateTime($d->FECHADESCARGA))->format("d/m/Y H:i:s"));

          $excel->getActiveSheet()
            ->getStyle('B'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
          $excel->getActiveSheet()
            ->getStyle('H'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

          $fila++;
        }

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->getStyle('A2:H2')
          ->applyFromArray(array(
              'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'color'=>array('argb'=>'FF8C9EFF')
               ),
              'font'=>array(
                'color'=>array('argb'=>'ff212121'),
                'bold'=>true
               )
            ));          
        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        $excel->getActiveSheet()->getStyle('A1:K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        /* Guardar a archivo */
        // $callStartTime = microtime(true);
        // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        // $callEndTime = microtime(true);
        // $callTime = $callEndTime - $callStartTime;

        /* Generar descargable */
        // Redirect output to a client’s web browser (Excel2007)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: '.$mime[$tipo]);
        header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
        // header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit();
      }

      if($tipo == 'json'){
        header("Content-type: text/json");
        echo json_encode($data->results());
      }
    }
  }

  elseif($documento == 'informe_diario_emisiones'){
    $archivo = date("d-m-Y").'_reporte-diario';
    $gen = new General();
    $verneumaticosegun  = $gen->getParamValue('verneumaticosegun');
    $unidad_presion     = $gen->getParamValue('unidad_presion');
    $unidad_temperatura = $gen->getParamvalue('unidad_temperatura');

    $sql = "SELECT * FROM (
      SELECT DISTINCTROW
        c.ID_CAMION,
        c.NUMCAMION,
        po.NOMENCLATURA,
        s.CODSENSOR,
        s.TIPO,
        n.NUMIDENTI,
        n.NUMEROFUEGO,
        n.MODELO,
        CONCAT(p.MARCA,' ',p.MODELO,' ',p.DIMENSION) AS PLANTILLA,
        u.tempmax,
        u.presmax,
        u.presmin,
        u.eventotemperatura,
        u.eventopresion,
        u.fecha_evento
              
        FROM uman_camion c 
          LEFT JOIN uman_neumatico_camion nc ON c.ID_CAMION=nc.ID_EQUIPO
          LEFT JOIN uman_neumaticos n ON n.ID_NEUMATICO=nc.ID_NEUMATICO
          LEFT JOIN uman_ultimoevento u ON u.posicion=nc.ID_POSICION
          LEFT JOIN uman_sensores s ON s.ID_SENSOR=n.ID_SENSOR
          LEFT JOIN uman_plantilla p ON p.ID_PLANTILLA=n.ID_PLANTILLA
          LEFT JOIN uman_posicion po ON po.POSICION=nc.ID_POSICION
        WHERE u.fecha_evento NOT LIKE '0000-00-00%' 
        ORDER BY u.fecha_evento DESC, nomenclatura ASC, c.ID_CAMION ASC) as X
      GROUP BY ID_CAMION, NOMENCLATURA";
    //
    $db = DB::getInstance();
    $data = $db->query($sql);
    // print_r($data);exit();
    if($data->count() > 0){
      if($tipo == 'xls'){
        #region preparación hoja
          $excel = new PHPExcel();
          $excel->getProperties()->setCreator("BAILAC")
            ->setLastModifiedBy("BAILAC")
            ->setTitle("Reporte de estado de emisiones y configuración")
            ->setDescription("Reporte de estado de emisiones y configuración")
            ->setKeywords("bailac uman Reporte de estado de emisiones y configuración");

          $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Reporte de Estado')
            ->setCellValue('A4', 'Equipo')
            ->setCellValue('B4', 'Posición')
            ->setCellValue('C4', 'Sensor')
            ->setCellValue('D4', 'Tipo')
            ->setCellValue('E4', 'Serie')
            ->setCellValue('F4', 'Modelo')
            ->setCellValue('G4', 'Plantilla')
            ->setCellValue('H4', 'T° Máx.')
            ->setCellValue('I4', 'P° Máx.')
            ->setCellValue('J4', 'P° Mín.')
            ->setCellValue('K4', 'Presión')
            ->setCellValue('L4', 'Temperatura')
            ->setCellValue('M4', 'Fecha');

          $excel->getActiveSheet()->mergeCells('C3:D3');
          $excel->getActiveSheet()->mergeCells('E3:G3');
          $excel->getActiveSheet()->mergeCells('H3:J3');
          $excel->getActiveSheet()->mergeCells('A1:M1');
          $excel->getActiveSheet()
            ->setCellValue('C3', 'Sensor')
            ->setCellValue('E3', 'Neumático')
            ->setCellValue('H3', 'Umbrales');
          $excel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

          $excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('L4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet()->getStyle('M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        #endregion

        $fila = 5;
        foreach($data->results() as $d)
        {
          $cod_neum = $verneumaticosegun == 'fuego' ? ($d->NUMEROFUEGO != '' ? $d->NUMEROFUEGO : $d->NUMIDENTI) : ($d->NUMIDENTI != '' ? $d->NUMIDENTI : $d->NUMEROFUEGO);
          $excel->setActiveSheetIndex(0)
          ->setCellValue('A'.$fila, $d->NUMCAMION)
          ->setCellValue('B'.$fila, $d->NOMENCLATURA)
          ->setCellValue('C'.$fila, $d->CODSENSOR)
          ->setCellValue('D'.$fila, $d->TIPO)
          ->setCellValue('E'.$fila, $cod_neum)
          ->setCellValue('F'.$fila, $d->MODELO)
          ->setCellValue('G'.$fila, $d->PLANTILLA)
          ->setCellValue('H'.$fila, Core::tpConvert($d->tempmax, $unidad_temperatura, true))
          ->setCellValue('I'.$fila, Core::tpConvert($d->presmax, $unidad_presion, true))
          ->setCellValue('J'.$fila, Core::tpConvert($d->presmin, $unidad_presion, true))
          ->setCellValue('K'.$fila, Core::tpConvert($d->eventopresion, $unidad_presion, true))
          ->setCellValue('L'.$fila, Core::tpConvert($d->eventotemperatura, $unidad_temperatura, true))
          ->setCellValue('M'.$fila, (new DateTime($d->fecha_evento))->format('d/m/Y H:i:s'));

          $excel->getActiveSheet()
            ->getStyle('M'.$fila)->getNUmberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

          $fila++;
        }

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

        $excel->getActiveSheet()->getStyle('A1:M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        /* Guardar a archivo */
        // $callStartTime = microtime(true);
        // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        // $callEndTime = microtime(true);
        // $callTime = $callEndTime - $callStartTime;

        /* Generar descargable */
        // Redirect output to a client’s web browser (Excel2007)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: '.$mime[$tipo]);
        header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit;
      }
    }
  }