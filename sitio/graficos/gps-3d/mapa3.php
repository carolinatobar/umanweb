<?php


    $total = 0;
    $xmin = 0;
    $xmax = -100;
    $ymin = 0;
    $ymax = -100;
    $zmin = 0;
    $zmax = -100;

    if ( isset ( $_GET['equipo'] ) ) {
        $equipo = $_GET['equipo'];
    } else {
        $equipo = 0;
    }

    include("conectar.php");
    $info = mysql_query("SELECT X,ALTURA,Y FROM uman_gps WHERE EQUIPO='$equipo' LIMIT 200");
    while ( $data = mysql_fetch_array ( $info ) ) {
        $x[$total] = $data[0];
        $y[$total] = $data[1];
        $z[$total] = $data[2];

        if ( $data[0] < $xmin ) {
            $xmin = $data[0];
        }

        if ( $data[0] > $xmax ) {
            $xmax = $data[0];
        }
        if ( $data[1] < $ymin ) {
            $xmin = $data[1];
        }

        if ( $data[1] > $ymax ) {
            $xmax = $data[1];
        }
        if ( $data[2] < $zmin ) {
            $zmin = $data[2];
        }
        if ( $data[2] > $zmax ) {
            $zmax = $data[2];
        }
        $total++;
    }
?>
<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8" />
  <title>Cube</title>
  <style>
    body {
      text-align: center;
    }

    canvas { 
      width: 100%; 
      height: 100%;
      border: 1px solid black;
    }
  </style>
</head>

<body>
  <h1>Liquid Three.js Cube</h1>
  <p>Change the browser's window size.</p>
  <script src="https://rawgithub.com/mrdoob/three.js/master/build/three.js"></script> <!-- Get the latest version of the Three.js library. -->
  <script>
    var scene = new THREE.Scene(); // Create a Three.js scene object.
    var camera = new THREE.PerspectiveCamera(110, window.innerWidth / window.innerHeight, 0.1, 1000); // Define the perspective camera's attributes.

    var renderer = window.WebGLRenderingContext ? new THREE.WebGLRenderer() : new THREE.CanvasRenderer(); // Fallback to canvas renderer, if necessary.
    renderer.setSize(window.innerWidth, window.innerHeight); // Set the size of the WebGL viewport.
    document.body.appendChild(renderer.domElement); // Append the WebGL viewport to the DOM.

    var geometry = new THREE.CubeGeometry(1, 1, 1); // Create a 20 by 20 by 20 cube.
    var material = new THREE.MeshBasicMaterial({ color: 0x0000FF }); // Skin the cube with 100% blue.


        <?php
              for ( $i = 0 ; $i < $total ; $i++){
                  if($i!=0) {
                  }
              ?>
              //particle = particles[<?php print $i; ?>] = new THREE.Sprite( material );
              //particles.position.x = <?php $x[$i] = ($x[$i]-$xmin)*100000; print $x[$i]; ?>*2;
              //particle.position.y = <?php print ($y[$i]-$ymin); ?>;
              //particle.position.z = <?php $z[$i] = ($z[$i]-$zmin)*100000; print $z[$i]?>*1;
              //console.log("<?php print $i.": ".$x[$i]." // ".($y[$i]/10)." // ".$z[$i]; ?>");
              //scene.add( particle );

            var cube<?php print $i; ?> = new THREE.Mesh(geometry, material); // Create a mesh based on the specified geometry (cube) and material (blue skin).
            cube<?php print $i; ?>.position.x = <?php $x[$i] = ($x[$i]-$xmin-20)*1; print $x[$i]; ?>;
            cube<?php print $i; ?>.position.y = <?php $y[$i] = (($y[$i]-$ymin)); print $y[$i]; ?>;
            cube<?php print $i; ?>.position.z = <?php $z[$i] = ($z[$i]-$zmin-50)*1; print $z[$i]; ?>;
            scene.add(cube<?php print $i; ?>); // Add the cube at (0, 0, 0).


          <?php
          }
          ?>


        camera.position.x = 20;
        camera.position.y = 500;
        camera.position.z = 40; // Move the camera away from the origin, down the positive z-axis.

    var render = function () {
      //cube.rotation.x += 0.01; // Rotate the sphere by a small amount about the x- and y-axes.
      //cube.rotation.y += 0.01;

      renderer.render(scene, camera); // Each time we change the position of the cube object, we must re-render it.
      requestAnimationFrame(render); // Call the render() function up to 60 times per second (i.e., up to 60 animation frames per second).
    };

    render(); // Start the rendering of the animation frames.
  </script>
</body>
</html>
