<?php
  require '../../autoload.php';

  $obj_sns  = new Sensor();
  $arr_sns  = $obj_sns->get_disponibles();

  if(isset($arr_sns) && count($arr_sns) > 0){
    foreach ($arr_sns as $sns) {
      $sns_tipo = strtolower($sns->TIPO);
?>
<div s-string="<?=$sns->CODSENSOR?>" class="<?=Core::col(3)?> left-margin-5px">
  <div class="sensor-cell-selector center-block" s-id="<?=$sns->ID_SENSOR?>">
    <div class="sensor-icono-selector center-block" style="background: url('assets/img/sensor_<?=strtolower($sns_tipo)?>.png');"></div>
    <div class="sensor-codigo-selector center-block"><?=$sns->CODSENSOR?></div>
    <div class="sensor-tipo-selector center-block"><?=$sns->TIPO?></div>
    </div>
</div>
<?php
    }
  }

?>