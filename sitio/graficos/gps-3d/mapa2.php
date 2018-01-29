<?php


    $total = 0;
    $xmin = 0;
    $xmax = -100;
    $zmin = 0;
    $zmax = -100;

    if ( isset ( $_GET['equipo'] ) ) {
        $equipo = $_GET['equipo'];
    } else {
        $equipo = 0;
    }

    include("conectar.php");
    $info = mysql_query("SELECT X,ALTURA,Y FROM uman_gps WHERE EQUIPO='$equipo' LIMIT 20 OFFSET 20");
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
<html lang="en">
	<head>
		<title>three.js canvas - performance</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				font-family: Monospace;
				background-color: #f0f0f0;
				margin: 0px;
				overflow: hidden;
			}
		</style>
	</head>
	<body>

		<script src="build/three.js"></script>

		<script src="examples/js/renderers/Projector.js"></script>
		<script src="examples/js/renderers/CanvasRenderer.js"></script>

		<script src="examples/js/libs/stats.min.js"></script>

		<script>
			var container, stats;
			var camera, scene, renderer;
			var light;
			init();
			animate();
			function init() {
				container = document.createElement( 'div' );
				document.body.appendChild( container );
				scene = new THREE.Scene();
				camera = new THREE.PerspectiveCamera( 45, window.innerWidth / window.innerHeight, 1, 10000 );
				camera.position.set( 0, 1000, 1000 );
				camera.lookAt( scene.position );
				// Grid
				var size = 5, step = 10;
				var geometry = new THREE.Geometry();
				for ( var i = - size; i <= size; i += step ) {
					geometry.vertices.push( new THREE.Vector3( - size, 0, i ) );
					geometry.vertices.push( new THREE.Vector3(   size, 0, i ) );
					geometry.vertices.push( new THREE.Vector3( i, 0, - size ) );
					geometry.vertices.push( new THREE.Vector3( i, 0,   size ) );
				}
				var material = new THREE.LineBasicMaterial( { color: 0x000000, opacity: 0.5 } );
				var line = new THREE.LineSegments( geometry, material );
				scene.add( line );
				// Spheres
				geometry = new THREE.SphereGeometry( 100, 26, 18 );
				material = new THREE.MeshLambertMaterial( { color: 0xffffff, overdraw: 0.5 } );



				<?php
        			for ( $i = 0 ; $i < $total ; $i++){

    					?>
					var sphere = new THREE.Mesh( geometry, material );
					sphere.position.x = <?php print $x[$i]/10; ?>; //( <?php print $i; ?> % 5 ) * 200 - 400;
					console.log("<?php print $i.": ".$x[$i]." // ".$y[$i]." // ".$z[$i]; ?>");
					sphere.position.z = <?php print $y[$i]/10; ?>; //Math.floor( <?php print $i; ?> / 5 ) * 200 - 400;
					scene.add( sphere );
					<?php
					}
					?>


				// Lights
				var ambientLight = new THREE.AmbientLight( Math.random() * 0x202020 );
				scene.add( ambientLight );
				var directionalLight = new THREE.DirectionalLight( Math.random() * 0xffffff );
				directionalLight.position.set( 0, 1, 0 );
				scene.add( directionalLight );
				light = new THREE.PointLight( 0xff0000, 1, 500 );
				scene.add( light );
				renderer = new THREE.CanvasRenderer();
				renderer.setClearColor( 0xf0f0f0 );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );
				container.appendChild( renderer.domElement );
				stats = new Stats();
				container.appendChild(stats.dom);
				//
				window.addEventListener( 'resize', onWindowResize, false );
			}
			function onWindowResize() {
				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();
				renderer.setSize( window.innerWidth, window.innerHeight );
			}
			//
			function animate() {
				requestAnimationFrame( animate );
				render();
				stats.update();
			}
			function render() {
				var timer = Date.now() * 0.001;
				light.position.x = Math.cos( timer ) * 1000;
				light.position.y = 500;
				light.position.z = Math.sin( timer ) * 1000;
				renderer.render( scene, camera );
			}
		</script>

	</body>
</html>