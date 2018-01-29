<?php
 require '../../autoload.php';
 
 $acc = new Acceso(true);

 $n = new Neumatico();
 $n = $n->listar_todos();
 $data = [];
 if(count($n) > 0)
 {
   foreach($n as $neum)
   {
      $btn_editar = '<button data-id="'.$neum->ID_NEUMATICO.'" data-plantilla="'.$neum->ID_PLANTILLA.'" class="btn btn-xs btn-primary edit" data-toggle="modal" data-target="#editarNeumatico"><i class="fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Ver / Modificar"></i></button>';
      $btn_borrar = '<button data-id="'.$neum->ID_NEUMATICO.'" class="btn btn-xs btn-danger" onclick="eliminar('.$neum->ID_NEUMATICO.')"><i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar"></i></button>';
      if($neum->ESTADO == 'USO') $btn_borrar = '<button class="btn btn-xs btn-danger remove" disabled><i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Primero debe quitar el neumático del equipo."></i></button>';
      
      $data[] = [
        'btn'=>$btn_editar.$btn_borrar,
        'id_neumatico'=>$neum->ID_NEUMATICO,
        'numidenti'=>$neum->NUMIDENTI,
        'numfuego'=>$neum->NUMEROFUEGO,
        'marca'=>$neum->MARCA,
        'modelo'=>$neum->MODELO,
        'estado'=>$neum->ESTADO,
        'id_plantilla'=>$neum->ID_PLANTILLA
      ];
   }
 }

 echo json_encode(['data'=>$data]);