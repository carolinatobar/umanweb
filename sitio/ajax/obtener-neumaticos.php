<?php
 require '../autoload.php';
 
 $acc = new Acceso(true);

 $n = new Neumatico();
 $n = $n->listar_todos();
 $data = [];
 if(count($n) > 0)
 {
   foreach($n as $neum)
   {
      $btn_editar = '<button data-id="'.$neum->ID_NEUMATICO.'" class="btn btn-xs btn-primary edit" data-toggle="modal" data-target="#editarNeumatico"><i class="fa fa-pencil" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Ver / Modificar"></i></button>';
      $btn_borrar = '<button data-id="'.$neum->ID_NEUMATICO.'" class="btn btn-xs btn-danger" onclick="eliminar('.$neum->ID_NEUMATICO.')"><i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar"></i></button>';
      if($neum->ESTADO == 'USO') $btn_borrar = '<button class="btn btn-xs btn-danger remove" disabled><i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Primero debe quitar el neumático del equipo."></i></button>';
      
      $data[] = [
        $btn_editar.$btn_borrar,
        // $neum->ID_NEUMATICO,
        $neum->NUMIDENTI,
        $neum->NUMEROFUEGO,
        $neum->MARCA,
        $neum->MODELO,
        $neum->ESTADO
      ];
   }
 }

 echo json_encode(['data'=>$data]);