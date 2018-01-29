</center>
<div class="row">
<div class="col-sm-3">
<button onclick="toggleMapa()" class="btn btn-default">Mostrar/Ocultar Mapa</button>
</div>
<div class="col-sm-3">
<button onclick="rotacion()" class="btn btn-default">Rotacion</button>
</div>
</div>

<?php

$info       = explode("/",file_get_contents("./graficos/gps-3d/".$faena.".info"));

$xmax       = $info[4];
$xmin       = $info[5];
$ymax       = $info[2];
$ymin       = $info[3];
$zmax       = $info[0];
$zmin       = $info[1];

$k          = ( $xmax - $xmin )/( $zmax - $zmin );

$a_x        = 600 / ( $xmax - $xmin );
$b_x        = 300 - ( $xmax * $a_x );

$a_z        = 600 / ( ( $zmax - $zmin ) * $k );
$b_z        = ( 300 / $k ) - ( $zmax * $a_z );

$lonFrom    = deg2rad( $zmin );
$lonTo      = deg2rad( $zmax );
$lat        = deg2rad( $xmin );

$deltaLon   = $lonTo - $lonFrom;

$angulo     = 2 * asin( sqrt( pow( sin( 0 / 2 ) , 2 ) + cos( $lat ) * cos( $lat ) * pow( sin( $deltaLon / 2 ) , 2 ) ) );

$radio      = 6371000;

$k_altura   = 600 / ( $angulo * $radio );

?>
<style>

.body {
    background-color: #eeeeee;
}
</style>
<div class="cc-divider">Posicionamiento 3D</div>

<div id="webgl" style="margin-top: 0px"></div>

<script src="./graficos/gps-3d-3/lib/three.min.js"></script>
<script src="./graficos/gps-3d-3/lib/TrackballControls.js"></script> 
<script>


///////////////////////////////////////////////////
//////////////////////////////////////////////////
/////                   M A P A
///////////////////////////////////////////////
//////////////////////////////////////////////


    var width  = window.innerWidth;
    var height = 480;

    var scene = new THREE.Scene();
    scene.add(new THREE.AmbientLight(0xeeeeee));

    var camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 10000);
    camera.position.set(0, 0, 700);

    var renderer = new THREE.WebGLRenderer();
    renderer.setSize(width, height);
    renderer.setClearColor(0xeeeeee, 1);

    var rotacion = false;

    var cube = Array();
    var group= Array();
    var meshee = Array();
    var data = [<?php

                $totalLineas = 0;
                $totalCoords = 0;
                $totaltodo = 0;
                $alturas = Array();

                $mapaString = file_get_contents('graficos/gps-3d/mapa_'.$faena.'.txt');
                $lineas = explode("#", $mapaString); // quedan las lineas en $lineas[]

                foreach ( $lineas as $linea ) {

                    $totalCoords = 0;

                    $coords = explode(",", $linea); // quedan las coords en $coords[linea][coords]

                    foreach ( $coords as $coord ) {

                        $ejes = explode("|",$coord);

                        $x = $ejes[0];
                        $y = $ejes[1];
                        $z = $ejes[2];

                        if( $x!=0 && $y!=0 && $z!=0 ) {

                            if ( $totaltodo != 0 ) {
                                print ",";
                            }

                            $totaltodo++;
                            $totalCoords++;
                            print ($y-$ymin)*$k_altura;
                        }
                    }
                $totalLineas++;
                }
                ?>];

    var geometry = new THREE.PlaneGeometry(600, 600/<?php print $k; ?>, <?php print $totalCoords; ?>-1, <?php print $totalLineas; ?>-1);

    for (var i = 0, l = geometry.vertices.length; i < l; i++) {
       geometry.vertices[i].z = data[i];
    }

    var material = new THREE.MeshPhongMaterial({
        map: THREE.ImageUtils.loadTexture('./graficos/gps-3d-3/assets/<?php print $faena; ?>.png')
    });

    var material_wireframe = new THREE.MeshPhongMaterial({
            color: 0xa0cb4b, 
            wireframe: true
        });

    var plane = new THREE.Mesh(geometry, material);
    scene.add(plane);

    var plane_wireframe = new THREE.Mesh(geometry, material_wireframe);
    scene.add(plane_wireframe);

    plane.visible = false;

    ///////////////////////////////////////////////
    //////////////////////////////////////////////
    ///////             CAMINO
    ////////////////////////////////////////////
    ///////////////////////////////////////////

    var dotMaterial = new THREE.PointsMaterial( { size: 1, sizeAttenuation: false, color: 0xff0000 } );
    var dotMaterial_2 = new THREE.PointsMaterial( { size: 1, sizeAttenuation: false, color: 0x0000ff } );
 
   <?php
    $camino = 0;
    $data_camino        = mysql_query("SELECT * FROM uman_gps LIMIT 6000");

        while ( $info_camion = mysql_fetch_array( $data_camino ) ) {
            $x_camino   = ($info_camion['X'] * $a_x + $b_x)*(-1);
            $y_camino   = ($info_camion['Y'] * $a_z + $b_z);
            $z_camino   = ( ( $info_camion['ALTURA'] - $ymin ) + 0) * $k_altura;

    ?>

    var dotGeometry<?php print $camino; ?> = new THREE.Geometry();
    dotGeometry<?php print $camino; ?>.vertices.push(new THREE.Vector3( <?php print $x_camino; ?>, <?php print $y_camino; ?>, <?php print $z_camino; ?>));
    var dot<?php print $camino; ?> = new THREE.Points( dotGeometry<?php print $camino; ?>, dotMaterial );
    scene.add( dot<?php print $camino; ?> );

    <?php
        $camino++;
    }

    $data_camino_2        = mysql_query("SELECT * FROM uman_gps ORDER BY ID desc LIMIT 6000");

        while ( $info_camion_2 = mysql_fetch_array( $data_camino_2 ) ) {
            $x_camino   = ($info_camion_2['X'] * $a_x + $b_x)*(-1);
            $y_camino   = ($info_camion_2['Y'] * $a_z + $b_z);
            $z_camino   = ( ( $info_camion_2['ALTURA'] - $ymin ) + 0) * $k_altura;

    ?>

    var dotGeometry<?php print $camino; ?> = new THREE.Geometry();
    dotGeometry<?php print $camino; ?>.vertices.push(new THREE.Vector3( <?php print $x_camino; ?>, <?php print $y_camino; ?>, <?php print $z_camino; ?>));
    var dot<?php print $camino; ?> = new THREE.Points( dotGeometry<?php print $camino; ?>, dotMaterial_2 );
    scene.add( dot<?php print $camino; ?> );

    <?php
        $camino++;
    } 
    ?>

    console.log(<?php print $camino; ?>);





    var dotGeometry = new THREE.Geometry();
    dotGeometry.vertices.push(new THREE.Vector3( 0, -<?php print 300/$k; ?>, 100));
    var dot = new THREE.Points( dotGeometry, dotMaterial );
   // scene.add( dot );




    var grid = new THREE.GridHelper( 600, 6 );// 400, 40, 0x0000ff, 0x808080 
    scene.add(grid);
    grid.visible = false;


    ////////////////////////////////////////////////////////////



    var controls = new THREE.TrackballControls(camera);
    controls.rotateSpeed = 0.1;
    controls.zoomSpeed = 1.0;
    controls.panSpeed = 1.0;
    controls.noZoom=false;
    controls.noPan=false;
    controls.noRotate=false;
    controls.dynamicDampingFactor=0.3;


    document.getElementById('webgl').appendChild(renderer.domElement);



    render();



    function render() {
        if ( rotacion ) {
            scene.rotation.z = scene.rotation.z + 0.0002;
        }
        controls.update();    
        requestAnimationFrame(render);
        renderer.render(scene, camera);

    }

</script>
<script type="text/javascript">

    //////////////////////////////////////////////////
    /////////////////////////////////////////////////
    ////////           EQUIPOS
    ///////////////////////////////////////////////
    //////////////////////////////////////////////

function creaCamiones() {

    var a_x     = <?php print $a_x; ?>;
    var b_x     = <?php print $b_x; ?>;
    var a_z     = <?php print $a_z; ?>;
    var b_z     = <?php print $b_z; ?>;
    var k       = <?php print $k; ?>;
    var k_a     = <?php print $k_altura; ?>;
    var x_min   = <?php print $xmin; ?>;
    var y_min   = <?php print $ymin; ?>;
    var z_min   = <?php print $zmin; ?>;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("GET","graficos/gps-3d-3/ajax_gps_3d.php?faena=<?php print $faena; ?>",true);
    xmlhttp.send();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            var data = JSON.parse(xmlhttp.responseText);

            data.forEach( function(data) {

                var id = data['ID_EQUIPO'];
                console.log(id + " X: " + data['X'] + " Y: " + data['Y']);

                cube[id] = new THREE.Mesh( new THREE.SphereGeometry( 3, 32, 32 ), new THREE.MeshBasicMaterial( {color: 0x0000ff}) );

                cube[id].position.x = ecuacionRecta( data['X'] , a_x , b_x ) * (-1);
                cube[id].position.y = ecuacionRecta( data['Y'] , a_z , b_z ) ;
                cube[id].position.z = ( data['ALTURA'] - y_min ) * k_a + 5;

                var canvasee = document.createElement('canvas');
                var context = canvasee.getContext('2d');
                context.font = "16px Arial";
                context.fillStyle = "rgba(0,0,0,1)";
                context.fillText(data['NUMCAMION'], 0, 50);
                
                var textureee = new THREE.Texture(canvasee) 
                textureee.needsUpdate = true;
                  
                var materialee = new THREE.MeshBasicMaterial( {map: textureee, side:THREE.DoubleSide } );
                materialee.transparent = true;

                meshee[id] = new THREE.Mesh(
                    new THREE.PlaneGeometry(canvasee.width, canvasee.height),
                    materialee
                  );

                meshee[id].position.set(
                    ecuacionRecta( data['X'] , a_x , b_x ) * (-1) + 140 ,
                    ecuacionRecta( data['Y'] , a_z , b_z ) -18,
                    ( data['ALTURA'] - y_min ) * k_a + 10
                    );

                scene.add( meshee[id] );
                scene.add( cube[id] );


            });

        }
    }

}
creaCamiones();
window.setInterval(function(){
  actualizaDatos();
}, 3000);

function actualizaDatos() {

    var a_x     = <?php print $a_x; ?>;
    var b_x     = <?php print $b_x; ?>;
    var a_z     = <?php print $a_z; ?>;
    var b_z     = <?php print $b_z; ?>;
    var k       = <?php print $k; ?>;
    var k_a     = <?php print $k_altura; ?>;
    var x_min   = <?php print $xmin; ?>;
    var y_min   = <?php print $ymin; ?>;
    var z_min   = <?php print $zmin; ?>;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("GET","graficos/gps-3d-3/ajax_gps_3d.php?faena=<?php print $faena; ?>",true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            var data = JSON.parse(xmlhttp.responseText);

            data.forEach( function(data) {
                var id = data['ID_EQUIPO'];
                //console.log(id);
                cube[id].position.x = ecuacionRecta( data['X'] , a_x , b_x ) * (-1);
                cube[id].position.y = ecuacionRecta( data['Y'] , a_z , b_z );
                cube[id].position.z = ( data['ALTURA'] - y_min ) * k_a + 5;

                meshee[id].position.set(
                    ecuacionRecta( data['X'] , a_x , b_x ) * (-1) + 140 ,
                    ecuacionRecta( data['Y'] , a_z , b_z )  -18,
                    ( data['ALTURA'] - y_min ) * k_a + 10
                    );

            });

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

function ecuacionRecta( x , a , b ) {
    var y = (a * x) + (b);
    return y;
}

function toggleMapa() {
    plane.visible   = !plane.visible;
    plane_wireframe.visible   = !plane.visible;

    //grid.visible    = !grid.visible;
}

function rotacion() {
    rotacion = !rotacion;
}


</script>
