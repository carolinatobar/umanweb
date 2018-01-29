<?php
	require '../autoload.php';

	session_start();
	$archivo = $_SESSION[session_id()]['nombrefaena'] .' - '. $_SESSION[session_id()]['user'];	

	$equipo = isset($_GET['equipo']) ? $_GET['equipo'] : null;
  $fecha  = isset($_GET['fecha'])  ? $_GET['fecha']  : null;
  $tipo   = isset($_GET['tipo'])   ? strtolower($_GET['tipo']) : null;
  // var_dump($_GET);


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

    $sql = "SELECT uman_gps.*, uman_camion.NUMCAMION 
      FROM uman_gps INNER JOIN uman_camion ON uman_gps.EQUIPO=uman_camion.ID_CAMION 
     WHERE EQUIPO={$equipo} AND $fecha";
    // echo $sql;

    $db = DB::getInstance();
    $data = $db->query($sql);

    if($data->count() > 0){
      if($tipo == 'csv'){
        $correlativo = 1;
        $content = '"ID","EQUIPO","FECHA","X","Y","rapidez","direccion","ALTURA","FECHADESCARGA"'."\n";
        foreach($data->results() as $d){
          $fecha = (new DateTime($d->FECHA))->format("d/m/Y H:i:s");
          $fechaDescarga = (new DateTime($d->FECHADESCARGA))->format("d/m/Y H:i:s");
          $content .= '"'.$d->ID.'", "'.$d->NUMCAMION.'", "'.$fecha.'", "'.$d->X.'", "'.$d->Y.'", "'.$d->rapidez.'", "'.$d->direccion.'", "'.$d->ALTURA.'", "'.$fechaDescarga.'"'."\n";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="'.$archivo.'.csv"');
        echo $content;
      }

      if($tipo == 'json'){
        header("Content-type: text/json");
        echo json_encode($data->results());
      }

      if($tipo == 'xlsx'){
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
          ->setCellValue('B1', $equipo)
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
            ->setCellValue('B'.$fila, $d->FECHA)
            ->setCellValue('C'.$fila, $d->X)
            ->setCellValue('D'.$fila, $d->Y)
            ->setCellValue('E'.$fila, $d->rapidez)
            ->setCellValue('F'.$fila, $d->direccion)
            ->setCellValue('G'.$fila, $d->ALTURA)
            ->setCellValue('H'.$fila, $d->FECHADESCARGA);

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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$archivo.'.xlsx"');
        // header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
      }
    }


?>