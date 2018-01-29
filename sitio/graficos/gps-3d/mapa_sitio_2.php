<div class="cc-divider"><?php print $texto_sitio['Posicionamiento 3D en faena']; ?></div>
<?php

	

    $total = 0;
    $xmin = 0;
    $xmax = -100;
    $zmin = 0;
    $zmax = -100;

    if ( isset ( $_GET['equipo'] ) ) {
        $equipo = $_GET['equipo'];
    } else {
        $equipo = 10000;
    }

    //include("conectar.php");


	

    /*for ( $i = 0 ; $i < 1000 ; $i++) {
    	$coords = explode("|",$mapa[$i]);

    	$mapax[$i] = $coords[0];
    	$mapay[$i] = $coords[2];
    	$mapaz[$i] = $coords[1];
    }
	*/

  /*  
    $info = mysql_query("SELECT X,ALTURA,Y FROM uman_gps WHERE EQUIPO='$equipo' ORDER BY ID desc LIMIT 700 OFFSET 16000");
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
            $ymin = $data[1];
        }
        if ( $data[1] > $ymax ) {
            $ymax = $data[1];
        }
        if ( $data[2] < $zmin ) {
            $zmin = $data[2];
        }
        if ( $data[2] > $zmax ) {
            $zmax = $data[2];
        }
        $total++;
    }
    */
	 //print $xmax."/".$xmin."/".$ymax."/".$ymin."/".$zmax."/".$zmin."<br>";
    $data = mysql_query("SELECT X,ALTURA,Y,RAPIDEZ,DIRECCION,FECHA FROM uman_ultimogps WHERE NUMCAMION='$equipo' LIMIT 1");



$info = explode("|",file_get_contents("graficos/gps-3d/".$faena.".info"));

$xmax = $info[0];
$xmin = $info[1];
$zmax = $info[2];
$zmin = $info[3];

$xmin = $xmin + ($xmax - $xmin)/2;
$zmin = $zmin + ($zmax - $zmin)/2;

//print $xmax."/".$xmin."/".$ymax."/".$ymin."/".$zmax."/".$zmin."<br>";

$info=mysql_fetch_array($data);
$X_inst=$info['X'];
$ALTURA_inst=$info['ALTURA'];
$Y_inst=$info['Y'];
$RAPIDEZ_inst=$info['RAPIDEZ'];
$DIRECCION_inst=$info['DIRECCION'];
$FECHA_inst=$info['FECHA'];


?>

		<style>
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

		<script src="graficos/gps-3d/build/three.js"></script>
		<script src="graficos/gps-3d/examples/js/renderers/Projector.js"></script>
		<script src="graficos/gps-3d/examples/js/renderers/CanvasRenderer.js"></script>
		<script src="graficos/gps-3d/examples/js/controls/OrbitControls.js"></script>
		<script src="graficos/gps-3d/examples/js/libs/stats.min.js"></script>
		<script src="graficos/gps-3d/examples/js/Detector.js"></script>
    	<script type="text/javascript" src="graficos/gps-3d/examples/js/libs/dat.gui.min.js"></script>
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
				camera.position.y = 1200;
				camera.position.z = 750;
				camera.position.x = 400;

				scene = new THREE.Scene();
				particles = new Array();

				cube = new THREE.Mesh( new THREE.SphereGeometry( 10, 32, 32 ), new THREE.MeshBasicMaterial( {color: 0x0000ff}) );
				cube.position.x = <?php print ((($X_inst-$xmin)*50000 - 100)/(-2)); ?>;
				cube.position.y = <?php print (($ALTURA_inst-400)/2); ?>;
				cube.position.z = <?php print ((($Y_inst-$zmin)*50000 -700)/(-2)); ?>;
				scene.add( cube );

				var PI2 = Math.PI * 2;

				group = new THREE.Group();
				var material1 = new THREE.LineBasicMaterial( {
					color: 0x007700
				} );

				var material2 = new THREE.LineBasicMaterial( {
					color: 0x333333,
					linewidth: 4
				} );

				controls = new THREE.OrbitControls( camera );
  				controls.addEventListener( 'change', render );

				var geometry = new THREE.Geometry();
				geometry.vertices.push(
				<?php 
				$totalCoords = 0;
				for ( $i = 0 ; $i < $total ; $i++){
					if ( $i != 0){
						print ",";
					}
				?>

				new THREE.Vector3( <?php $x[$i] = ((($x[$i]-$xmin)*50000 - 100)/(-2)); print $x[$i]; ?>, <?php print ((($y[$i]-$ymin-300))/2); ?>, <?php $z[$i] = ((($z[$i]-$zmin)*50000 -700)/(-2)); print $z[$i]?> )
				<?php
					}
					?>
				);
				
				var line = new THREE.Line( geometry, material2 );
				scene.add( line );
				<?php //include("camino.txt"); ?>

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

				$mapaString = file_get_contents('graficos/gps-3d/mapa_'.$faena.'.txt');
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

				    
				    	if( $x!=0 && $y!=0 && $z!=0 ) {

				    	if ( $totalCoords != 0 ) {
				    		print ",";
				    	}
				    	$totalCoords++;
				    ?>

				    new THREE.Vector3( <?php $x = ((($x-$xmin)*50000 - 100)/(-2)); print $x; ?>, <?php print ((($y-400))/2); ?>, <?php $z = ((($z-$zmin)*50000 -700)/(-2)); print $z; ?> )

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



	// crea elemtno de texto
	var canvas1 			= document.createElement('canvas');
	var context1 			= canvas1.getContext('2d');
	context1.font 			= "40px Arial";
	context1.fillStyle 		= "rgba(0,0,0,0.95)";

    context1.fillText('10000', 0, 50);

	var texture1 			= new THREE.Texture(canvas1) 
	texture1.needsUpdate 	= true;
    var material3 			= new THREE.MeshBasicMaterial( {map: texture1, side:THREE.DoubleSide } );
    material1.transparent 	= true;

    mesh1 = new THREE.Mesh(
        new THREE.PlaneGeometry(canvas1.width, canvas1.height),
        material3
      );
	mesh1.position.set(<?php print ((($X_inst-$xmin)*50000 - 100)/(-2)); ?>,<?php print (($ALTURA_inst-400)/2); ?>,<?php print ((($Y_inst-$zmin)*50000 -700)/(-2)); ?>);
	//scene.add( mesh1 );


	// create a canvas element
	var canvas2 = document.createElement('canvas');
	var context2 = canvas2.getContext('2d');
	context2.font = "40px Arial";
	context2.fillStyle = "rgba(255,255,255,0.95)";
    context2.fillText('Talleres Bailac', 0, 50);
    
	// canvas contents will be used for a texture
	var texture2 = new THREE.Texture(canvas2) 
	texture2.needsUpdate = true;
      
    var material4 = new THREE.MeshBasicMaterial( {map: texture2, side:THREE.DoubleSide } );
    material4.transparent = true;

    mesh2 = new THREE.Mesh(
        new THREE.PlaneGeometry(canvas2.width, canvas2.height),
        material4
      );
	mesh2.position.set(0,400,-350);
	//scene.add( mesh2 );




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
				renderer.setClearColor(0xffffff, 1);
				container.appendChild( renderer.domElement );
				stats = new Stats();
				//container.appendChild( stats.dom );
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
				}
				//scene.rotation.z = scene.rotation.z + 0.0001;
				camera.lookAt( group.position );
				renderer.render( scene, camera );
				count += 0.1;

				if ( subida ) {
					scale = scale*1.07;
				} else {
					scale = scale/1.07;
				}
				cube.scale.x = scale;
				cube.scale.y = scale;
				cube.scale.z = scale;

				if ( scale > 5 || scale < 1 ) {
					subida = !subida;
				}
			}
			function actualizaDatos() {

			}
			window.setInterval(function(){
  				actualizaDatos(<?php print $equipo; ?>);
			}, 3000);

			function actualizaDatos(str) {
				if (str == "") {
			    	return;
			  	} else {
			    	if (window.XMLHttpRequest) {
			      		xmlhttp = new XMLHttpRequest();
			    	} else {
			      		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    	}
			    	xmlhttp.open("GET","graficos/gps-3d/getdatos.php?equipo="+str,true);
			    	xmlhttp.send();
			    	xmlhttp.onreadystatechange = function() {
			      		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			                
			        		var objeto = JSON.parse(xmlhttp.responseText);
				        	cube.position.x = ((objeto['X']-(<?php print $xmin; ?>)*50000 - 100)/(-2));
				        	mesh1.position.x = ((objeto['X']-(<?php print $xmin; ?>)*50000 - 100)/(-2));
				        	cube.position.y = ((objeto['ALTURA'])-300)/2;
				        	mesh1.position.y = ((objeto['ALTURA'])-300)/2;
				        	cube.position.z = (((objeto['Y']-(<?php print $zmin; ?>))*50000 -700)/(-2));
				        	mesh1.position.z = (((objeto['Y']-(<?php print $zmin; ?>))*50000 -700)/(-2));
				        	document.getElementById("lat").innerHTML = objeto['X'];
				        	document.getElementById("lon").innerHTML = objeto['Y'];
				        	document.getElementById("alt").innerHTML = objeto['ALTURA'] + " m";
			                document.getElementById("rap").innerHTML = objeto['RAPIDEZ'] + " km/h";
			                if ( objeto['X'] != ult_lat || objeto['Y'] != ult_lon) {
			                	document.getElementById("pendiente").innerHTML= pendiente(objeto['X'],objeto['Y'],ult_lat,ult_lon, objeto['ALTURA'], ult_alt) + "°";
				                ult_lat = objeto['X'];
					        	ult_lon = objeto['Y'];	
					        	ult_alt = objeto['ALTURA'];
			                } 
				    	}
				    }
			  	}
			}
	function pendiente(lat1, lon1, lat2, lon2, alt1, alt2){
	    var R = 6378.137; // Radio de la tierra en KM
	    var dLat = lat2 * Math.PI / 180 - lat1 * Math.PI / 180;
	    var dLon = lon2 * Math.PI / 180 - lon1 * Math.PI / 180;
	    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
	    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
	    Math.sin(dLon/2) * Math.sin(dLon/2);
	    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	    var d = R * c * 1000;
	    var alt = alt2 - alt1;
	    var arcsin = Math.atan(alt/d);
	    arcsin = arcsin*(180/Math.PI);
	    arcsin = Math.round(arcsin * 100) / 100;
	    arcsin = arcsin || 0;
	    return arcsin;
	}
		</script>

	</body>
</html>
<!--
<div style="position: fixed; color: black; top: 60px; width:100%; text-align: center; font-family: 'Libre Franklin', sans-serif; font-size: 30px">
FAENA EL SOLDADO</h1></div>-->
<div style="position: fixed; color: black; bottom: 30%; right: 10px; width: 300px; display: none">
<select class="form-control">
<option>10000</option>
<option>10001</option>
<option>10002</option>
<option>10003</option>
<option>10004</option>
</select><br>
<!--Latitud: <div id="lat" style="color: red"><?php print $X_inst; ?></div><br>
Longitud: <div id="lon" style="color: red"><?php print $Y_inst; ?></div><br>-->
Altura: <div id="alt" style="color: red"><?php print $ALTURA_inst; ?> m</div><p>&nbsp;</p>
Rapidez: <div id="rap" style="color: red"><?php print $RAPIDEZ_inst; ?> km/h</div><br>
Pendiente: <div id="pendiente" style="color: red">0°</div><br>
<p>
</p>
</div>

<!--
<div style="position: fixed; color: white; top: 10px;width:100%; text-align: right; font-family: 'Libre Franklin', sans-serif; font-size: 30px">
<button onclick="rotacione()">Rotacion</button>&nbsp;&nbsp;&nbsp;<p>
<button onclick="resetCamera()">Resetear camara</button>&nbsp;&nbsp;&nbsp;<p>
<form action="calcularalrededores.php" method="GET">
<input type="hidden" name="xmin" value="<?php print ($xmin-0.01); ?>">
<input type="hidden" name="ymin" value="<?php print $ymin; ?>">
<input type="hidden" name="zmin" value="<?php print ($zmin-0.001); ?>">
<input type="hidden" name="xmax" value="<?php print ($xmax+0.02); ?>">
<input type="hidden" name="ymax" value="<?php print $ymax; ?>">
<input type="hidden" name="zmax" value="<?php print ($zmax+0.02); ?>">
<input type="submit" value="Calcular alrededores">&nbsp;&nbsp;&nbsp;
</form><p>
<button onclick="window.location.href = 'index.php?s=monitoreo'">Volver a monitoreo</button>&nbsp;&nbsp;&nbsp;


</div>

<div id="footer2"><img src="imagenes/logo.png"></center></div>
<div id="footer1"><img src="../images/logo_uman_blue.png" width="200"></div>
-->
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