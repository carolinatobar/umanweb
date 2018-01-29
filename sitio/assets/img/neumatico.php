<?php
  $file = 'rueda.png';

  $imageBase = imagecreatefrompng($file);
  $transparent = imagecolorallocatealpha($imageBase,0,0,0,127);
  imagealphablending($imageBase, false);
  imageSaveAlpha($imageBase, true);

  header("Content-type: image/png");
  readfile($file);