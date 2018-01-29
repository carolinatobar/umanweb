<?php
  require '../../autoload.php';

  $acc = new Acceso(true); 

  $obj_neu  = new Neumatico();
  $arr_neu  = $obj_neu->get_master();

  $verNeumaticoPor = (new General())->getParamvalue('verneumaticosegun');

  if(isset($arr_neu) && count($arr_neu) > 0){
    foreach ($arr_neu as $neu) {
      $click_event = 'n-id="'.$neu->ID_NEUMATICO.'" ';
      $class = 'SS';
      if($neu->ID_SENSOR){
        $click_event .= ' data-estado="'.$neu->ESTADO.'" onclick="modalViewSensor(this);" s-id="'.$neu->ID_SENSOR.'" s-code="'.$neu->CODSENSOR.'" s-type="'.$neu->TIPO.'"';
        $class = 'CS';
      }
      else{
        if($neu->ESTADO == 'BAJA') $click_event .= ' data-toggle="modal" data-target="#modal-baja" ';
        // else $click_event .= ' data-toggle="modal" data-target="#modal-add-sensor" ';
        else $click_event .= ' onclick="modalViewSensor(this);" ';
      }
      
      $n = array('id'=>$neu->ID_NEUMATICO, 'num'=>($verNeumaticoPor=='fuego' && $neu->NUMEROFUEGO ? $neu->NUMEROFUEGO : $neu->NUMIDENTI), 
        'marca'=>$neu->MARCA, 'click-event'=>$click_event, 'path'=>'../..', 'showTooltip'=>false);
      $s = array('id'=>$neu->ID_SENSOR, 'num'=>$neu->CODSENSOR, 'tipo'=>$neu->TIPO);
      if($neu->ESTADO == 'DISPONIBLE')
        Core::neumatico_arrastrable($n,$s,'libre '.$class, true, false);
    }
    //   if($neu->ID_SENSOR){
    //     $click_event .= ' data-estado="'.$neu->ESTADO.'" data-toggle="modal" data-target="#modal-view-sensor" s-id="'.$neu->ID_SENSOR.'" s-code="'.$neu->CODSENSOR.'" s-type="'.$neu->TIPO.'"';
    //     $class = 'CS';
    //   }
    //   else{
    //     if($neu->ESTADO == 'BAJA') $click_event .= ' data-toggle="modal" data-target="#modal-baja" ';
    //     // else $click_event .= ' data-toggle="modal" data-target="#modal-add-sensor" ';
    //     else $click_event .= ' data-toggle="modal" data-target="#modal-view-sensor" ';
    //   }
      
    //   $n = array('id'=>$neu->ID_NEUMATICO, 'num'=>($verNeumaticoPor=='fuego' && $neu->NUMEROFUEGO ? $neu->NUMEROFUEGO : $neu->NUMIDENTI), 
    //     'marca'=>$neu->MARCA, 'click-event'=>$click_event, 'path'=>'../..', 'showTooltip'=>false);
    //   $s = array('id'=>$neu->ID_SENSOR, 'num'=>$neu->CODSENSOR, 'tipo'=>$neu->TIPO);
    //   if($neu->ESTADO == 'DISPONIBLE')
    //     Core::neumatico_arrastrable($n,$s,'libre '.$class, true, false);
    // }
  }