<?php

	error_reporting(0);

    $total = 2500;

    $lugar = $_GET['lugar'];

	$info = file_get_contents($lugar.".info");

	$minmax = explode("|",$info);

    $xmin = $minmax[0];
    $xmax = $minmax[1];
    $zmin = $minmax[2];
    $zmax = $minmax[3];

    $xmin = $xmin + ($xmax - $xmin)/2;
	$zmin = $zmin + ($zmax - $zmin)/2;

    /*for ( $i = 0 ; $i < 1000 ; $i++) {
    	$coords = explode("|",$mapa[$i]);

    	$mapax[$i] = $coords[0];
    	$mapay[$i] = $coords[2];
    	$mapaz[$i] = $coords[1];
    }
	*/

?><!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php print $lugar; ?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link href="https://fonts.googleapis.com/css?family=Muli|Libre+Franklin" rel="stylesheet">
		<style>
			body {
				background-color: #000000;
				margin: 0px;
				overflow: hidden;
				font-family: 'Muli', sans-serif;
			}
			a {
				color:#0078ff;
			}

			#footer1 {
	            position : absolute;
	            bottom : 0;
	            left: 5px;
	            margin-top : 40px;
	            border: 0;
	            border-collapse: collapse;
	        }
	        #footer2 {
	            position : absolute;
	            bottom : 0;
	            margin-top : 40px;
	            right:0px;
	            border: 0;
	            border-collapse: collapse;
	        }
		</style>
	</head>
	<body>
		<script src="build/three.js"></script>
		<script src="examples/js/renderers/Projector.js"></script>
		<script src="examples/js/renderers/CanvasRenderer.js"></script>
		<script src="examples/js/controls/OrbitControls.js"></script>
		<script src="examples/js/libs/stats.min.js"></script>
		<script src="examples/js/Detector.js"></script>
    	<script type="text/javascript" src="examples/js/libs/dat.gui.min.js"></script>
		<script>


			var container, stats, rotacion=1;
			var subida = true;
			var scale = 1;
			var camera, scene, renderer, mesh;
			var particles, particle, count = 0;
			var mouseX = 0, mouseY = 0;
			var windowHalfX = window.innerWidth / 2;
			var windowHalfY = window.innerHeight / 2;
			var cube;
			var ult_lat, ult_lon, ult_alt;
			var mesh1,mesh2;
			init();
			animate();
			function init() {

				container = document.createElement( 'div' );
				document.body.appendChild( container );

				camera = new THREE.PerspectiveCamera( 100, window.innerWidth / window.innerHeight, 1, 10000 );
				camera.position.y = 300;
				camera.position.z = 50;
				camera.position.x = 600;

				scene = new THREE.Scene();
				particles = new Array();

				var PI2 = Math.PI * 2;

				group = new THREE.Group();
				var material1 = new THREE.LineBasicMaterial( {
					color: 0x00ff00
				} );

				var material2 = new THREE.LineBasicMaterial( {
					color: 0xFAE7A5,
					linewidth: 4
				} );

				controls = new THREE.OrbitControls( camera );
  				controls.addEventListener( 'change', render );


				var material = new THREE.SpriteCanvasMaterial( {
					color: 0xffff00,
					program: function ( context ) {
						context.beginPath();
						context.arc( 0, 0, 0.5, 0, PI2, true );
						context.fill();
					}
				} );
				
				<?php

			    $totalLineas = 0;
			    $totalCoords = 0;

				$mapaString = file_get_contents('mapa_'.$lugar.'.txt');
				$lineas = explode("#", $mapaString); // quedan las lineas en $lineas[]

				foreach ( $lineas as $linea ) {

					$totalCoords = 0;

					$coords = explode(",", $linea); // quedan las coords en $coords[linea][coords]

					?>
					var geometria<?php print $totalLineas; ?> = new THREE.Geometry();
					geometria<?php print $totalLineas; ?>.vertices.push(

					<?php

					foreach ( $coords as $coord ) {


							$ejes = explode("|",$coord);

							$x = $ejes[0];
					    	$y = $ejes[1];
					    	$z = $ejes[2];

					    
					    	if( $x != 0 && $y != 0 && $z != 0 ) {

					    	if ( $totalCoords != 0 ) {
					    		print ",";
					    	}


				    	$totalCoords++;
				    ?>

				    new THREE.Vector3( <?php $x = (((($x-$xmin))*50000 - 100)/(-2)); print $x; ?>, <?php print ((($y-400)/2)); ?>, <?php $z = ((($z-$zmin)*50000 -700)/(2)); print $z; ?> )

				    <?php
				    	}
					}
				?>
				);
				var line<?php print $totalLineas; ?> = new THREE.Line( geometria<?php print $totalLineas; ?>, material1 );
				scene.add( line<?php print $totalLineas; ?> );
						
				<?php
				$totalLineas++;
				}
				?>



	



<?php /*

				for ( $i = 0 ; $i < $totalLineas-1 ; $i++){
    			?>						
					var geometria<?php print $i; ?> = new THREE.Geometry();
					geometria<?php print $i; ?>.vertices.push(
					<?php
					$k = 0;
					while ( $coords[$i][$k] != NULL ) {
						if ( $k != 0){
							print ",";
						}
						$ejes = explode("|",$coords[$i][$k]);

    					$x = $ejes[0];
				    	$y = $ejes[1];
				    	$z = $ejes[2];
					?>

						new THREE.Vector3( <?php $x = ((($x-$xmin)*50000 - 100)/2); print $x; ?>, <?php print ((($y-$ymin-300))/2); ?>, <?php $z = ((($z-$zmin)*50000 -1000)/2); print $z; ?> )
						<?php
						$k++;
							}
						?>
				);

				var line<?php print $i; ?> = new THREE.Line( geometria<?php print $i; ?>, material2 );
				scene.add( line<?php print $i; ?> );
						
				<?php
				} */
				?>

					// lines

				//var linea = new THREE.Line( geometria, new THREE.LineBasicMaterial( { color: 0xffffff, opacity: 0.5 } ) );
				//scene.add( linea );

					//FLOOR
						/*var floorTexture = new THREE.ImageUtils.loadTexture( 'asalto.jpg' );
						floorTexture.wrapS = floorTexture.wrapT = THREE.RepeatWrapping; 
						floorTexture.repeat.set( 1, 1 );
						var floorMaterial = new THREE.MeshBasicMaterial( { map: floorTexture, side: THREE.DoubleSide } );
						var floorGeometry = new THREE.PlaneGeometry(1000, 1000, 10, 10);
						var floor = new THREE.Mesh(floorGeometry, floorMaterial);
						floor.rotation.x = Math.PI / 2;
						floor.doubleSided = true;
						scene.add(floor);*/



				var grid = new THREE.GridHelper( 600, 6 );// 400, 40, 0x0000ff, 0x808080 
				//scene.add(grid);


				renderer = new THREE.CanvasRenderer();
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );
				renderer.setClearColor(0x000000, 1);
				container.appendChild( renderer.domElement );
				stats = new Stats();
				container.appendChild( stats.dom );
				document.addEventListener( 'mousemove', onDocumentMouseMove, false );
				document.addEventListener( 'touchstart', onDocumentTouchStart, false );
				document.addEventListener( 'touchmove', onDocumentTouchMove, false );
				//
				window.addEventListener( 'resize', onWindowResize, false );
			}
			function onWindowResize() {
				windowHalfX = window.innerWidth / 2;
				windowHalfY = window.innerHeight / 2;
				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();
				renderer.setSize( window.innerWidth, window.innerHeight );
			}
			//
			function onDocumentMouseMove( event ) {
				mouseX = event.clientX - windowHalfX;
				mouseY = event.clientY - windowHalfY;
			}
			function onDocumentTouchStart( event ) {
				if ( event.touches.length === 1 ) {
					event.preventDefault();
					mouseX = event.touches[ 0 ].pageX - windowHalfX;
					mouseY = event.touches[ 0 ].pageY - windowHalfY;
				}
			}
			function onDocumentTouchMove( event ) {
				if ( event.touches.length === 1 ) {
					event.preventDefault();
					mouseX = event.touches[ 0 ].pageX - windowHalfX;
					mouseY = event.touches[ 0 ].pageY - windowHalfY;
				}
			}
			//
			function animate() {

				requestAnimationFrame( animate );
				render();
				stats.update();
			}
			function render() {
				//console.log(camera.position.x);
				//console.log(camera.position.y);
				//console.log(camera.position.z);
				if ( mesh1 ) {
					mesh1.lookAt( camera.position );
				}
				if ( mesh2 ) {
					mesh2.lookAt( camera.position );
				}
				if(rotacion){
					scene.rotation.y = scene.rotation.y + 0.002;
					//scene.rotation.z = scene.rotation.z - 0.001;
				}
				//scene.rotation.z = scene.rotation.z + 0.0001;
				camera.lookAt( group.position );
				renderer.render( scene, camera );
				count += 0.1;


			}


		</script>

	</body>
</html>



</div>


<script>
	function rotacione(){
		rotacion = !rotacion;
	}
	function resetCamera() {
						camera.position.y = 750;
				camera.position.z = 0;
				camera.position.x = 0;
	}
	</script>