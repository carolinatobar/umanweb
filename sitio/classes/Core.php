<?php
class Core
{
  public static $colorLineaPosicion = array(
    1=> '#1abc9c',
    2=> '#f1c40f',
    3=> '#3498db',
    4=> '#e74c3c',
    5=> '#9b59b6',
    6=> '#7FFF00',
    7=> '#FF6F00',
    8=> '#3E2723',
    9=> '#0050ef',
    10=> '#a20025',
    11=> '#a4c400',
    12=> '#00B7C3',
    13=> '#FF8C00',
    14=> '#7E735F',
    15=> '#8E8CD8',
    16=> '#607D8B'
  );

  public static function obtener_simbolo_unidad($unidad)
  {
    if(strtolower($unidad) == 'celsius')    return '°C';
    if(strtolower($unidad) == 'kelvin')     return '°K';
    if(strtolower($unidad) == 'fahrenheit') return '°F';
    if(strtolower($unidad) == 'bar')        return 'BAR';
    if(strtolower($unidad) == 'psi')        return 'PSI';
  }

  public static function celsius2kelvin(&$temperature,$rounded=true)
  {
    $kelvin = ($temperature + 273.15);
    if($rounded) $kelvin = round($kelvin);
    
    $temperature = $kelvin;
  }

  public static function celsius2fahrenheit(&$temperature,$rounded=true)
  {
    $fah = ($temperature*1.8) + 32;
    if($rounded) $fah = round($fah);

    $temperature = $fah;
  }

  function psi2bar(&$pressure,$rounded=true){
    $bar = $pressure / 14.5;
    if($rounded) $bar = round($bar);
    
    $pressure = $bar;
  }

  public static function dibuja_caja($number, $sensor, $temp = '', $pres = '', $rec = '', 
    $color = array('color'=>'none', 'dato'=>'', 'fecha'=>''), $date = '', $bat='')
  {

    $max_evento   = $GLOBALS['datos_eventos'][$number][0];
    $presion      = $GLOBALS['datos_eventos'][$number][1];
    $temperatura  = $GLOBALS['datos_eventos'][$number][2];
    // $bateria      = $GLOBALS['datos_eventos'][3];
    $u_temp       = $GLOBALS['unidad_temperatura'];
    $u_pres       = $GLOBALS['unidad_presion'];
    $a            = 0;

    $db = DB::getInstance();

    $consulta_nomen = $db->query("SELECT * FROM uman_posicion");
    foreach( $consulta_nomen->results() as $datos_nom ) {
      $valor_rueda[$datos_nom->POSICION] = $datos_nom->NOMENCLATURA;
    }
    //  Parametros
    //  $number =>  Numero Neumático
    //  $sensor =>  Tipo Sensor, Ej: 'interno', 'externo' (tambien como 'canister')
    //  $temp   =>  Temperatura, ya sea la actual o la ultima registrada
    //  $pres   =>  Presion, ya sea la actual o la ultima registrada
    //  $color  =>  Color de cuadro, Ej: 'green', 'red', 'gray'
    //  $date   =>  Fecha de la ultima medición / conexion

    $temperatura  = isset($temp) && $temp != '' && is_numeric($temp) ? $temp : '--';
    $presion      = isset($pres) && $pres != '' && is_numeric($pres) ? $pres : '--';
    $bateria      = isset($bat) && $bat != '' && is_numeric($bat) ? $bat : '--';
    
    if($temperatura!='--'){
      if($u_temp=='kelvin'){ self::celsius2kelvin($temperatura); $u_temp = 'K'; }
      if($u_temp=='fahrenheit'){ self::celsius2fahrenheit($temperatura); $u_temp = 'F'; }
      else $u_temp = 'C';

      $temperatura = "<strong>{$temperatura}&deg;</strong><span style=\"font-size: 55%\">{$u_temp}</span>&nbsp;";
    }

    if($presion!='--')  {
      if($u_pres=='bar'){ self::psi2bar($presion); $u_pres = 'BAR'; }
      else $u_pres = 'PSI';

      $presion = "<strong>{$presion}</strong><span style=\"font-size: 55%\">{$u_pres}</span>&nbsp;";
    }

    if($bateria != '--'){
      if($bateria <= 0){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-empty" aria-hidden="true"></i> '
        .'0 %'
        .'</div>';
      }
      else if($bateria >=1 && $bateria <= 39){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-quarter" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=40 && $bateria <= 69){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-half" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=70 && $bateria <= 89){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-three-quarters" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=90){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-full" aria-hidden="true"></i> '
        . ($bateria > 100 ? 100 : $bateria) .' %'
        .'</div>';
      }
    }
    else{
      $bateria = '';
    }

    $recomendado  = isset($rec) && $rec != '' && is_numeric($rec) ? "P&deg; Recomend. <strong>{$rec}</strong> <span style=\"font-size: 65%\">$u_pres</span>" : '';

    if($color['color'] != 'gray'){
      $temp_class = 'title_single';
      $temp_text  = 'Temp.';
      $pres_text  = 'Presión Actual';
      $extra      = $recomendado;
    } 
    else {
      $temp_class = 'title_double';
      $temp_text  = 'Última Temp.';
      $pres_text  = 'Última Presión';
      if($GLOBALS['mostrar_fecha_evento']==1) $extra = '';
      else $extra      = "<b>{$date}</b>";
    }

    $box_header = "
    <div class='tire-box-header'>
      <div class='tire-box-header__number'><b>{$valor_rueda[$number]}</b></div>
      <div class='tire-box-header__sensor {$sensor}'>{$bateria}</div>
    </div>";

    $box_data = '';
    if($GLOBALS['mostrar_fecha_evento']==1){
      $box_temp_pres = '
        <div class="tire-box-temp tire-box-psi">
        <div class="tire-box-temp__value pull-left">'.$temperatura.'</div>
        <div class="tire-box-psi__value pull-right">'.$presion.'</div>
        </div>';
      $box_date = '
        <div class="tire-box-date" style="padding-top: 20px;">
        '.date("d/m/Y H:i",$GLOBALS['datos_eventos'][$number]['fecha_evento']).'
        </div>';
      $box_data = $box_temp_pres.$box_date;
    }
    else{
      $box_temp  = "
        <div class='tire-box-temp'>
        <div class='tire-box-temp__title'>
          <span class='{$temp_class}'>{$temp_text}</span>
        </div>
        <div class='tire-box-temp__value'>{$temperatura}</div>
        </div>";

      $box_psi  = "
        <div class='tire-box-psi'>
        <div class='tire-box-psi__title'><span class='title_double'>{$pres_text}</span></div>
        <div class='tire-box-psi__value'>{$presion}</div>
        </div>";
      $box_data = $box_temp.$box_psi;
    }

    $box_extra  = "<div class='tire-box-extra'>{$extra}</div>";


    $box  = "<div class='tire-box' title='Clic para ver información general del neumático y sensor' data-toggle='tooltip' data-target='#detalleneum".$number."'>
                {$box_header}
              <div class='tire-box-body {$color[color]}'>
                {$box_data}
                {$box_extra}
              </div>
            </div>";

    return $box;
  }

  public static function dibuja_caja_temp($number, $sensor, $temp = '', 
    $color = array('color'=>'none', 'dato'=>'', 'fecha'=>''), $date = '', $bat='')
  {
    $a            = 0;

    $db = DB::getInstance();

    $consulta_nomen = $db->query("SELECT * FROM uman_posicion");
    foreach( $consulta_nomen->results() as $datos_nom ) {
      $valor_rueda[$datos_nom->POSICION] = $datos_nom->NOMENCLATURA;
    }
    //  Parametros
    //  $number =>  Numero Neumático
    //  $sensor =>  Tipo Sensor, Ej: 'interno', 'externo' (tambien como 'canister')
    //  $temp   =>  Temperatura, ya sea la actual o la ultima registrada
    //  $color  =>  Color de cuadro, Ej: 'green', 'red', 'gray'
    //  $date   =>  Fecha de la ultima medición / conexion

    $temperatura  = isset($temp) && $temp != '' && is_numeric($temp) ? $temp : '--';
    $bateria      = isset($bat) && $bat != '' && is_numeric($bat) ? $bat : '--';
    
    if($temperatura!='--'){
      if($u_temp=='kelvin'){ self::celsius2kelvin($temperatura); $u_temp = 'K'; }
      if($u_temp=='fahrenheit'){ self::celsius2fahrenheit($temperatura); $u_temp = 'F'; }
      else $u_temp = 'C';

      $temperatura = "<strong>{$temperatura}&deg;</strong><span style=\"font-size: 55%\">{$u_temp}</span>&nbsp;";
    }

    if($bateria != '--'){
      if($bateria <= 0){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-empty" aria-hidden="true"></i> '
        .'0 %'
        .'</div>';
      }
      else if($bateria >=1 && $bateria <= 39){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-quarter" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=40 && $bateria <= 69){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-half" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=70 && $bateria <= 89){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-three-quarters" aria-hidden="true"></i> '
        .$bateria.' %'
        .'</div>';
      }
      else if($bateria >=90){
        $bateria = '<div class="bateria">'
        .'<i class="fa fa-battery-full" aria-hidden="true"></i> '
        . ($bateria > 100 ? 100 : $bateria) .' %'
        .'</div>';
      }
    }
    else{
      $bateria = '';
    }

    if($color['color'] != 'gray'){
      $temp_class = 'title_single';
      $temp_text  = 'Temp.';
    } 
    else {
      $temp_class = 'title_double';
      $temp_text  = 'Última Temp.';
    }

    $box_header = "
    <div class='tire-box-header'>
      <div class='tire-box-header__number'><b>{$valor_rueda[$number]}</b></div>
      <div class='tire-box-header__sensor Aceite'>{$bateria}</div>
    </div>";

    $extra = '
      <div class="tire-box-date" style="font-size: 85%;">
      '.$date.'
      </div>';

    $box_data = "
        <div class='tire-box-temp'>
          <div class='tire-box-temp__title'>
            <span class='{$temp_class}'>{$temp_text}</span>
          </div>
          <div class='tire-box-temp__value'>{$temperatura}</div>
        </div>";

    $box_extra  = "<div class='tire-box-extra'>{$extra}</div>";


    $box  = "<div class='tire-box' 
      title='Clic para ver información general del sensor' 
      data-toggle='tooltip' data-target='#detallesensor".$number."'
      style='height: auto !important'>
                {$box_header}
              <div class='tire-box-body {$color[color]}'>
                {$box_data}
                {$box_extra}
              </div>
            </div>";

    return $box;
  }

  public static function dibuja_esquema_equipo($neumaticos, $colores, $ruedas, $attr=NULL, $btn_rec_alarma='')
  {
    $baseAttr = array(
      'esquema'=>array('style'=>'','class'=>''), 
      'container'=>array('style'=>'','class'=>''),
      'popover'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'',13=>'',14=>'',15=>'',16=>''),
      'neum'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'',13=>'',14=>'',15=>'',16=>''),
      'nomenclatura'=>array('class'=>'bottom', 'style'=>''),
      'showPopover'=>true, 'neum-drag-n-drop'=>false, 'putNeumatico'=>true, 'showNomenclatura'=>false
    );

    $gen = new General();
    $nomenclatura = $gen->getNomenclaturas();

    // print_r($attr);
    if($attr!=NULL)
    {
      for($i=0; $i<count($baseAttr); $i++)
      {
        if(array_key_exists('esquema',$attr))
        {
          if(array_key_exists('style',$attr['esquema'])) $baseAttr['esquema']['style'] = $attr['esquema']['style'];
          if(array_key_exists('class',$attr['esquema'])) $baseAttr['esquema']['class'] = $attr['esquema']['class'];
        }
        if(array_key_exists('container',$attr))
        {
          if(array_key_exists('style',$attr['container'])) $baseAttr['container']['style'] = $attr['container']['style'];
          if(array_key_exists('class',$attr['container'])) $baseAttr['container']['class'] = $attr['container']['class'];
        }
        if(array_key_exists('popover',$attr))
        {
          for($j=1; $j<=count($baseAttr['popover']); $j++)
          {
            if(array_key_exists($j,$attr['popover'])) $baseAttr['popover'][$j] = $attr['popover'][$j];
          }
        }
        if(array_key_exists('neum',$attr))
        {
          for($j=1; $j<=count($baseAttr['neum']); $j++)
          {
            if(array_key_exists($j,$attr['neum'])) $baseAttr['neum'][$j] = $attr['neum'][$j];
          }
        }
        if(array_key_exists('nomenclatura',$attr))
        {
          if(array_key_exists('style',$attr['nomenclatura'])) $baseAttr['nomenclatura']['style'] = $attr['nomenclatura']['style'];
          if(array_key_exists('class',$attr['nomenclatura'])) $baseAttr['nomenclatura']['class'] = $attr['nomenclatura']['class'];
        }
        if(array_key_exists('showPopover',$attr)) $baseAttr['showPopover'] = $attr['showPopover'];
        if(array_key_exists('neum-drag-n-drop',$attr)) $baseAttr['neum-drag-n-drop'] = $attr['neum-drag-n-drop'];
        if(array_key_exists('showNomenclatura',$attr)) $baseAttr['showNomenclatura'] = $attr['showNomenclatura'];
        
      }
    }
    // print_r($baseAttr)
    $attr = $baseAttr;
    
    
    $neums = ''; $pops = '';

    $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
    $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
    $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
    $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

    $pos = '';

    if($eje1==2)
    {
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[1].'</div>';

      $neums .= '<div id="pos1" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[1]['color'].' neum-big-1112" 
       style="'.$attr['neum'][1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[1]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('left',$ruedas[1],'pop-eje1 pop-1112',$attr['popover'][1]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[2].'</div>';

      $neums .= '<div id="pos2" data-eje="1"
       class="eje1-big neum-big neum-'.$colores[2]['color'].' neum-big-1314" 
       style="'.$attr['neum'][2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[2]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('right',$ruedas[4],'pop-eje1 pop-1314',$attr['popover'][2]);
    }
    elseif($eje1==4)
    {
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[1].'</div>';

      $neums .= '<div id="pos1" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[1]['color'].' neum-big-11" 
       style="'.$attr['neum'][1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[1]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('',$ruedas[1],'pop-eje1 pop-11',$attr['popover'][1]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[2].'</div>';

      $neums .= '<div id="pos2" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[2]['color'].' neum-big-12" 
       style="'.$attr['neum'][2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[2]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('',$ruedas[2],'pop-eje1 pop-12',$attr['popover'][2]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[3].'</div>';

      $neums .= '<div id="pos3" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[3]['color'].' neum-big-13" 
       style="'.$attr['neum'][3].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[3]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('',$ruedas[3],'pop-eje1 pop-13',$attr['popover'][3]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[4].'</div>';

      $neums .= '<div id="pos4" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[4]['color'].' neum-big-14" 
       style="'.$attr['neum'][4].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[4]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('',$ruedas[4],'pop-eje1 pop-14',$attr['popover'][4]);
    }

    if($eje2==2)
    {
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+1].'</div>';

      $neums .= '<div id="pos'.($eje1+1).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[3]['color'].' neum-big-2122" 
       style="'.$attr['neum'][3].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[3]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('left',$ruedas[5],'pop-eje2 pop-2122',$attr['popover'][3]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+2].'</div>';

      $neums .= '<div id="pos'.($eje1+2).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[4]['color'].' neum-big-2324" 
       style="'.$attr['neum'][4].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[4]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('right',$ruedas[8],'pop-eje2 pop-2324',$attr['popover'][4]);
    }
    elseif($eje2==4)
    {
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+1].'</div>';

      $neums .= '<div id="pos'.($eje1+1).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[3]['color'].' neum-big-21" 
       style="'.$attr['neum'][3].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';

      if($colores[3]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('left',$ruedas[5],'pop-eje2 pop-21',$attr['popover'][3]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+2].'</div>';

      $neums .= '<div id="pos'.($eje1+2).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[4]['color'].' neum-big-22" 
       style="'.$attr['neum'][4].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[4]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('bottom',$ruedas[6],'pop-eje2 pop-22',$attr['popover'][4]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+3].'</div>';

      $neums .= '<div id="pos'.($eje1+3).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[5]['color'].' neum-big-23" 
       style="'.$attr['neum'][5].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[5]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('bottom',$ruedas[7],'pop-eje2 pop-23',$attr['popover'][5]);
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+4].'</div>';

      $neums .= '<div id="pos'.($eje1+4).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[6]['color'].' neum-big-24" 
       style="'.$attr['neum'][6].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.($attr['showNomenclatura'] ? $pos : '').'</div>';
      
      if($colores[6]['color']!='none' && $attr['showPopover']) 
       $pops .= self::crearPopover('right',$ruedas[8],'pop-eje2 pop-24',$attr['popover'][6]);
    }

    $html = '
      <div class="center-block '.$attr['container']['class'].'" style="'.$attr['container']['style'].'">
      <div id="esquema" class="esquema '.$attr['esquema']['class'].'" style="'.$attr['esquema']['style'].'">
        '. ($attr['putNeumatico'] ? $neums : '' ).$pops.$btn_rec_alarma.'
      </div>
      </div>';

    return $html;
  }

  public static function dibuja_esquema_equipo_DND($neumaticos, $colores, $ruedas, $attr=NULL)
  {
    // print_r(array($neumaticos, $colores, $ruedas, $attr));
    $baseAttr = array(
      'esquema'=>array('style'=>'','class'=>''), 
      'container'=>array('style'=>'','class'=>''),
      'popover'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'',13=>'',14=>'',15=>'',16=>''),
      'neum'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'',13=>'',14=>'',15=>'',16=>''),
      'nomenclatura'=>array('class'=>'bottom', 'style'=>''),
      'showPopover'=>true, 'neum-drag-n-drop'=>false, 'showNomenclatura'=>false
    );

    $gen = new General();
    $nomenclatura = $gen->getNomenclaturas();

    // print_r($attr);
    if($attr!=NULL)
    {
      for($i=0; $i<count($baseAttr); $i++)
      {
        if(array_key_exists('esquema',$attr))
        {
          if(array_key_exists('style',$attr['esquema'])) $baseAttr['esquema']['style'] = $attr['esquema']['style'];
          if(array_key_exists('class',$attr['esquema'])) $baseAttr['esquema']['class'] = $attr['esquema']['class'];
        }
        if(array_key_exists('container',$attr))
        {
          if(array_key_exists('style',$attr['container'])) $baseAttr['container']['style'] = $attr['container']['style'];
          if(array_key_exists('class',$attr['container'])) $baseAttr['container']['class'] = $attr['container']['class'];
        }
        if(array_key_exists('popover',$attr))
        {
          for($j=1; $j<=count($baseAttr['popover']); $j++)
          {
            if(array_key_exists($j,$attr['popover'])) $baseAttr['popover'][$j] = $attr['popover'][$j];
          }
        }
        if(array_key_exists('neum',$attr))
        {
          for($j=1; $j<=count($baseAttr['neum']); $j++)
          {
            if(array_key_exists($j,$attr['neum'])) $baseAttr['neum'][$j] = $attr['neum'][$j];
          }
        }
        if(array_key_exists('nomenclatura',$attr))
        {
          if(array_key_exists('style',$attr['nomenclatura'])) $baseAttr['nomenclatura']['style'] = $attr['nomenclatura']['style'];
          if(array_key_exists('class',$attr['nomenclatura'])) $baseAttr['nomenclatura']['class'] = $attr['nomenclatura']['class'];
        }
        if(array_key_exists('showPopover',$attr)) $baseAttr['showPopover'] = $attr['showPopover'];
        if(array_key_exists('neum-drag-n-drop',$attr)) $baseAttr['neum-drag-n-drop'] = $attr['neum-drag-n-drop'];
        if(array_key_exists('showNomenclatura',$attr)) $baseAttr['showNomenclatura'] = $attr['showNomenclatura'];
      }
    }
    // print_r($baseAttr);exit();
    $attr = $baseAttr;
    
    
    $neums = ''; $pops = '';

    $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
    $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
    $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
    $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

    if($eje1==2){
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[1].'</div>';

      $neums .= '<div id="pos1" data-posicion="1" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[1]['color'].' neum-big-1112" 
       style="'.$attr['neum'][1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[1].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[2].'</div>';

      $neums .= '<div id="pos2" data-posicion="2" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[2]['color'].' neum-big-1314" 
       style="'.$attr['neum'][2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[2].$pos.'</div>';
    }
    elseif($eje1==4){
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[1].'</div>';

      $neums .= '<div id="pos1" data-posicion="1" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[1]['color'].' neum-big-11" 
       style="'.$attr['neum'][1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[1].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[2].'</div>';

      $neums .= '<div id="pos2" data-posicion="2" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[2]['color'].' neum-big-12" 
       style="'.$attr['neum'][2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[2].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[3].'</div>';

      $neums .= '<div id="pos3" data-posicion="3" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[3]['color'].' neum-big-13" 
       style="'.$attr['neum'][3].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[3].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[4].'</div>';

      $neums .= '<div id="pos4" data-posicion="4" data-eje="1" 
       class="eje1-big neum-big neum-'.$colores[4]['color'].' neum-big-14" 
       style="'.$attr['neum'][4].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[4].$pos.'</div>';
    }

    if($eje2==2){
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+1].'</div>';

      $neums .= '<div id="pos'.($eje1+1).'" data-posicion="'.($eje1+1).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[3]['color'].' neum-big-2122" 
       style="'.$attr['neum'][$eje1+1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+1].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+2].'</div>';

      $neums .= '<div id="pos'.($eje1+2).'" data-posicion="'.($eje1+2).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[4]['color'].' neum-big-2324" 
       style="'.$attr['neum'][$eje1+2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+2].$pos.'</div>';
    }
    elseif($eje2==4){
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+1].'</div>';

      $neums .= '<div id="pos'.($eje1+1).'" data-posicion="'.($eje1+1).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[3]['color'].' neum-big-21" 
       style="'.$attr['neum'][$eje1+1].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+1].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+2].'</div>';

      $neums .= '<div id="pos'.($eje1+2).'" data-posicion="'.($eje1+2).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[4]['color'].' neum-big-22" 
       style="'.$attr['neum'][$eje1+2].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+2].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+3].'</div>';

      $neums .= '<div id="pos'.($eje1+3).'" data-posicion="'.($eje1+3).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[5]['color'].' neum-big-23" 
       style="'.$attr['neum'][$eje1+3].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+3].$pos.'</div>';
      
      $pos = '<div class="lblpos '.$attr['nomenclatura']['class'].'" style="'.$attr['nomenclatura']['style'].'">'.$nomenclatura[$eje1+4].'</div>';

      $neums .= '<div id="pos'.($eje1+4).'" data-posicion="'.($eje1+4).'" data-eje="2" 
       class="eje2-big neum-big neum-'.$colores[6]['color'].' neum-big-24" 
       style="'.$attr['neum'][$eje1+4].'" '.
       (($attr['neum-drag-n-drop'])?'ondrop="drop(event)" ondragover="allowDrop(event)"':'').'>'.$ruedas[$eje1+4].$pos.'</div>';
    }

    $html = '
      <div class="center-block '.$attr['container']['class'].'" style="'.$attr['container']['style'].'">
      <div id="esquema" class="esquema '.$attr['esquema']['class'].'" style="'.$attr['esquema']['style'].'">
        '.$neums.$pops.'
      </div>
      </div>';

    return $html;
  }

  public static function neumatico_arrastrable($neumatico,$sensor,$class,$print=false,$draggable=true)
  {
    $data_class = $class=='usado' ? 'class="center-block hidden"' : 'class="center-block"';

    $col_class = self::absolute_col_x(4);

    $data_tt = '<div style=\'padding: 5px; font-size: 80%\'>
      <div><h4 style=\'color:oldlace\'><center>NEUMÁTICO</center></h4></div>
      <div style=\'padding: 5px 5px 5px 10px\'>      
        <span>N&deg; SERIE: '.$neumatico['num'].'</span><br />
        <span>N&deg; FUEGO: '.$neumatico['numfuego'].'</span><br />
        <span>MARCA: '.$neumatico['marca'].'</span><br />
        <span>COMPUESTO: '.$neumatico['compuesto'].'</span>
      </div>
      <div><h4 style=\'color:oldlace\'><center>SENSOR</center></h4></div>
      <div style=\'padding: 5px 5px 5px 10px\'>
        <span>CÓDIGO: '.$sensor['num'].'</span><br />
        <span>TIPO: '.$sensor['tipo'].'</span>
      </div>
      </div>';

    $data_po = '<div style="font-size: 80%; margin: -12px -1px 3px -1px;">
      <div style="margin: -10px auto 0 0;"><h4 style="background-color: #3b3b3b; color:oldlace; border-radius: 6px 6px 0 0;"><center>NEUMÁTICO</center></h4></div>
      <div class="bg-primary" style="padding: 5px 10px 0 10px; margin: -10px auto 0 0;">      
        <span>N&deg; SERIE: '.$neumatico['num'].'</span><br />
        <span>N&deg; FUEGO: '.$neumatico['numfuego'].'</span><br />
        <span>MARCA: '.$neumatico['marca'].'</span><br />
        <span>COMPUESTO: '.$neumatico['compuesto'].'</span>
      </div>
      <div style="margin: -10px auto 0 0;"><h4 style="background-color: #3b3b3b; color:oldlace"><center>SENSOR</center></h4></div>
      <div style="padding: 5px 10px 0 10px; margin: -10px auto 0 0;">
        <span>CÓDIGO: '.$sensor['num'].'</span><br />
        <span>TIPO: '.$sensor['tipo'].'</span>
        '.self::imagen_sensor($sensor['tipo'], 36, 'float: right; margin-top: -20px !important').'
      </div>
      </div>';

    $tt_placement = 'bottom';

    if(array_key_exists('tooltip',$neumatico)){
      $tooltip = $neumatico['tooltip'];
      $style = '';
      $class = '';
      if(array_key_exists('style', $tooltip)) $style = $tooltip['style'];
      if(array_key_exists('placement', $tooltip)) $tt_placement = $tooltip['placement'];
      if(array_key_exists('class', $tooltip)) $class = $tooltip['class'];
      if(array_key_exists('type', $tooltip))
      {
        if($tooltip['type'] == 'tooltip'){
          $tooltip = self::crearTooltip($tt_placement,$data_tt);
          break;
        }
        else if($tooltip['type'] == 'box')
        {
          $tooltip = '';
          $box = self::crearPopover($tt_placement, $data_po, $class, $style);
        }
        else if($tooltip['type'] == 'box-only')
        {
          return self::crearPopover('bottom', $data_po, $class, $style);
        }
        else if($tooltip['type'] == 'none'){
          $tooltip = '';
        }
      }
      // else $tooltip = self::crearTooltip($tt_placement,$data_tt);
    }
    else $tooltip = self::crearTooltip($tt_placement,$data_tt);

    $showBrand = true;
    if(array_key_exists('showBrand', $neumatico)) $showBrand = $neumatico['showBrand'];

    $neum = '<div class="neum-big neumatico '.$class.' '.$col_class.'" 
        style="'.$neumatico['style'].'" 
        id="neum-'.$neumatico['id'].'" 
        data-posicion="'.(isset($neumatico['pos'])?$neumatico['pos']:'').'" 
        '.($neumatico['showTooltip'] ? $tooltip : '').'
        title="'.($neumatico['showTooltip'] ? $neumatico['num'].' '.$neumatico['marca'] : '' ).'" 
        n-string="'.$neumatico['num'].' '.$neumatico['marca'].' '.$sensor['num'].'" ' .
        ($draggable ? 'draggable="true" ondragstart="drag(event)" ondragend="dragEnd(event)" ' : '') .
        $neumatico['click-event'].'>';
    $neum .= '<header '.$data_class.'>'.$neumatico['num'].'</header>';

    // $brand_img = strtolower($GLOBALS['ASSETS'].'img/brands/'.strtolower($neumatico['marca']));
    // $brand_img_lg = "{$brand_img}-lg.png";
    // $brand_img_sm = "{$brand_img}-sm.png";
    $brand_img_lg = strtolower($GLOBALS['ASSETS'].'img/brands/brand.php?tam=lg&marca='.strtolower($neumatico['marca']));
    $brand_img_sm = strtolower($GLOBALS['ASSETS'].'img/brands/brand.php?tam=sm&marca='.strtolower($neumatico['marca']));
    // if(file_exists( $brand_img_lg ) && file_exists( $brand_img_sm )) 
      $neum .= '<footer '.(($showBrand === false)?'class="hidden"':'').'>
        <div class="brand-lg" style="background-image: url('.$brand_img_lg.');" title="'.$neumatico['marca'].'"></div>
        <div class="brand-sm" style="background-image: url('.$brand_img_sm.');" title="'.$neumatico['marca'].'"></div>
        </footer>';
    // else $neum .= '<footer '.$data_class.'>'.$neumatico['marca'].'</footer>';

    $s_tt = strtolower($sensor['tipo']) == '' ? 'Sin sensor asignado' : 'Sensor '.$sensor['num'].' asignado';
    $sensor_img = strtolower($GLOBALS['ASSETS'].'img/sensor_'.strtolower($sensor['tipo']).'.png');    
    // if(file_exists($sensor_img) || file_exists($sensor_img)) 
      $neum .= '<sensor style="'.$sensor['style'].'">
        <div class="circle"></div>
        <div class="sensor" style="background-image: url('.$sensor_img.');" title="'.$s_tt.'" data-toggle="tooltip"></div>
        <div class="sensor-number">'.$sensor['num'].'</div>
        </sensor>';
    // else $neum .= '<sensor><div class="circle"></div><div class="sensor"><small>'.$sensor['tipo'].'</small></div></sensor>';

    $neum .= '</div>';

    if($print) print($neum.$box);
    else return $neum.$box;
  }

  public static function crearTooltip($placement, $data)
  {
    $tooltip = 'data-toggle="tooltip" data-placement="'.$placement.'" data-html="true" title="'.$data.'"';
    return $tooltip;
  }

  public static function dibuja_posiciones($num, $attr=NULL)
  {
    $baseAttr = array_fill(1,16,array('style'=>'', 'class'=>''));

    if($attr!=NULL)
    {
      foreach($attr as $i => $a)
      {
        if(array_key_exists('style',$a)) $baseAttr[$i]['style'] = $a['style'];
        if(array_key_exists('class',$a)) $baseAttr[$i]['class'] = $a['class'];
      }
    }

    $attr = $baseAttr;

    $html = '';
    for($i=1; $i<=$num; $i++)
    {
      $html .= '<div id="lblpos1" class="lblpos '.$attr[$i]['class'].'" style="'.$attr[$i]['style'].'">'.$i.'</div>';
    }

    return $html;
  }

  public static function crearPopover($placement, $data, $class, $style='', $type='popover-fixed')
  {
    $html = '
      <div class="popover '.$class.' '.$type.' '.$placement.'" style="'.$style.'">
      <div class="arrow hidden-xs"></div>
      <div class="popover-content nopadding">'.$data.'</div>
      <div class="clearfix"></div>
      </div>';

    return $html;
  }

  public static function distance($lat1, $lon1, $lat2, $lon2, $unit='M')
  {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    unset($theta);unset($dist);unset($lon1);unset($lon2);unset($lat1);unset($lat2);
    $value = 0;

    if ($unit == 'K') {
      $value = $miles * 1.609344;
    }
    else if($unit == 'M'){
      $value = $miles * 1609.344;
    } 
    else if ($unit == 'N') {
      $value = $miles * 0.8684;
    }
    else {
      $value = $miles;
    }

    return $value;
  }

  public static function distance2($lat1, $lon2, $lat2, $lon2)
  {
    $dist = 6371000 * acos( cos( deg2rad($lat1) ) 
      * cos( deg2rad($lat2) ) 
      * cos( deg2rad($lon2) - deg2rad($lon1)) + sin(deg2rad($lat1))
      * sin( deg2rad($lat2) ));

    return $dist;
  }

  public static function pendiente($lat1, $lon1, $lat2, $lon2, $alt1, $alt2)
  {
    $R = 6378.137; // Radio de la tierra en KM
    $dLat = $lat2 * M_PI / 180 - $lat1 * M_PI / 180;
    $dLon = $lon2 * M_PI / 180 - $lon1 * M_PI / 180;
    $a = sin($dLat/2) * sin($dLat/2) +
    cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) *
    sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $d = $R * $c * 1000;
    $alt = $alt2 - $alt1;
    $arcsin = atan($alt/d);
    $arcsin = $arcsin*(180/M_PI);
    $arcsin = round($arcsin * 100) / 100;
    // $arcsin = $arcsin || 0;
    return $arcsin;
	}

  public static function determinar_color_equipo(
    $presion, $temperatura, $presMax, $presMin, $tempMax, $ambar=false, $rango=NULL)
  {
    $val = 'black';
    if( $rango==NULL ) $rango = round($tempMax * 0.95);
    if( $presion >= $presMax ) $val = "orange";
    if( $presion <= $presMin ) $val = "yellow";
    if( $ambar ) {       
       if( ($temperatura >= $rango) && ($temperatura < $tempMax) ) $val = "lilac";
		}
		if( $temperatura >= $tempMax ) $val = "red";

    return $val;
  }

  public static function absolute_col_x($lg,$md=NULL,$sm=NULL,$xs=NULL)
  {
    $class = '';    
    if($md==NULL && $sm == NULL && $xs == NULL)
    {
      $size = $lg;
      if($size >= 1 && $size <= 12) $class = "col-lg-$size col-md-$size col-sm-$size col-xs-$size";
    } 
    else
    {
      if($lg >= 1 && $lg <= 12) $class .= ' col-lg-'.$lg;
      if($md >= 1 && $md <= 12) $class .= ' col-md-'.$md;
      if($sm >= 1 && $sm <= 12) $class .= ' col-sm-'.$sm;
      if($xs >= 1 && $xs <= 12) $class .= ' col-xs-'.$xs;
    }

    return trim($class);
  }

  public static function absolute_col_offset_x($lg,$md=NULL,$sm=NULL,$xs=NULL)
  {
    $class = '';    
    if($md==NULL && $sm == NULL && $xs == NULL)
    {
      $size = $lg;
      if($size >= 1 && $size <= 12) $class = "col-lg-offset-$size col-md-offset-$size col-md-offset-$size col-sm-offset-$size col-xs-offset-$size";
    } 
    else
    {
      if($lg >= 1 && $lg <= 12) $class .= ' col-lg-offset-'.$lg;
      if($md >= 1 && $md <= 12) $class .= ' col-md-offset-'.$md;
      if($sm >= 1 && $sm <= 12) $class .= ' col-sm-offset-'.$sm;
      if($xs >= 1 && $xs <= 12) $class .= ' col-xs-offset-'.$xs;
    }

    return trim($class);
  }

  public static function actualizar_umanblue()
  {
    $obj_eqp_n  	= new Equipo();
    $arr_camion   	= $obj_eqp_n->listar();
    
    foreach ( $arr_camion as $camion ) {
      if ( $camion->ID_CAJAUMAN != 0 ) {
          $obj_eqp_n->actualizarEstadoUmanBlue($camion);
      }    
    }
  }

  public static function perfil($p)
  {
    $perfil = array(
      'cc'=>'Consultor Cliente',
      'co'=>'Consultor Operaciones',
      'uman'=>'Administrador UMAN',
      'planif'=>'Planificador',
      'stt'=>'Soporte Técnico Terreno',
      'sgu'=>'Soporte Gestión UMAN',
      'su'=>'Súper Usuario'
    );

    return $perfil[$p];
  }
  
  public static function imagen_sensor($type, $size, $style=''){
    return '<div class="sensor '.$type.' icono-x'.$size.'" style="'.$style.'"></div>';
  }

  public static function imagen_marca_neumatico($marca, $size='16', $type='sm'){
    $brand_img = strtolower($GLOBALS['ASSETS'].'img/brands/'.strtolower($marca));
    $brand_img_lg = "{$brand_img}-lg.png";
    $brand_img_sm = "{$brand_img}-sm.png";
        
    if($type == 'lg') return '<div class="brand-lg center-block" style="background-image: url('.$brand_img_lg.');" ></div>';
    else return '<div class="brand-sm icono-x'.$size.' center-block" style="background-image: url('.$brand_img_sm.');" ></div>';
  }

  public static function createModal($id, $params)
  {
    if(!array_key_exists('content', $params))        $params['content'] = '';
    if(!array_key_exists('includeLoader', $params))  $params['includeLoader'] = false;
    if(!array_key_exists('includeContent', $params)) $params['includeContent'] = false;
    if(!array_key_exists('saveButton', $params))     $params['saveButton'] = '';
    if(!array_key_exists('style', $params))          $params['style'] = '';
    if(!array_key_exists('title', $params))          $params['title'] = '';

    $loader  = '<div id="loader" class="loader center-block" style="display:none"></div>';
    $content = $params['content'];
    
    echo '<!-- MODAL -->
    <div class="modal fade" tabindex="-1" role="dialog" id="'.$id.'">
      <div class="modal-dialog" role="document" style="'.$params['style'].'">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">'.$params['title'].'</h4>
          </div>
          <div class="modal-body">
            '.($params['includeLoader'] ? $loader : '').'
            '.($params['includeContent'] ? $content : '').'
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            '.($params['saveButton']).'
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->';
  }


  /* FUNCTIONS ALIASES */
  public static function col($lg,$md=NULL,$sm=NULL,$xs=NULL){
    echo self::absolute_col_x($lg,$md,$sm,$xs);
  }

  public static function offset($lg,$md=NULL,$sm=NULL,$xs=NULL){
    echo self::absolute_col_offset_x($lg,$md,$sm,$xs);
  }

  public static function tpConvert($valor, $unidadDestino, $espacio=false){

    if($valor != NULL){
      if(strtolower($unidadDestino) == 'celsius'){
        return $espacio ? $valor.' °C' : $valor.'°C';
      }
      if(strtolower($unidadDestino) == 'kelvin'){
        return $espacio ? self::celsius2kelvin($valor).' °K' : self::celsius2kelvin($valor).'°K';
      }
      if(strtolower($unidadDestino) == 'fahrenheit'){
        return $espacio ? self::celsius2fahrenheit($valor).' °F' : self::celsius2fahrenheit($valor).'°F';
      }
      if(strtolower($unidadDestino) == 'bar'){
        return $espacio ? self::psi2bar($valor).' BAR' : self::psi2bar($valor).'BAR';
      }
      if(strtolower($unidadDestino) == 'psi'){
        return $espacio ? $valor.' PSI' : $valor.'PSI';
      }
    }

    return '';
  }
}

?>