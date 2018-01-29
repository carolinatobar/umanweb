<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$obj_eqp  = new Equipo();
  $arr_eqp  = $obj_eqp->listar();

  $obj_box  = new Caja();
  $arr_box  = $obj_box->listar();

  $obj_flt  = new Flota();
  $arr_flt  = $obj_flt->listar();

  foreach ($arr_eqp as $eqp) {
    $eqp_id   = $eqp->ID_CAMION;
    $eqp_code = $eqp->NUMCAMION;
    $eqp_num  = $eqp->NUMNEUMATICOS;
    $eqp_type = $eqp->tipo;
    $eqp_flt  = $eqp->NUMFLOTA;
    $eqp_img  = $eqp->CLASS_IMG.'.png';

    echo "<li class='truck truck-window'>
            <div class='truck-header'>{$eqp_code}</div>
            <div class='truck-body'>
              <a class='truck_mod' href='#' u-id='{$eqp_id}' u-code='{$eqp_code}' u-wheels='{$eqp_num}' u-type='{$eqp_type}' u-fleet='{$eqp_flt}' onclick='editar(this);'><img src='assets/img/{$eqp_img}' alt='' style='width:108px; height: auto;'></a>
            </div>
          </li>";
  }