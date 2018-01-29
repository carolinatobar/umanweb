<!doctype html>
<html lang="en">
<head>
<title>three.js - Jotunheimen</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<style>
    body { margin: 0; overflow: hidden; }
</style>
</head>
<body>
<div id="webgl" style="width:100%"></div>
<script src="./graficos/gps-3d-3/lib/three.min.js"></script>
<script src="./graficos/gps-3d-3/lib/TrackballControls.js"></script> 
<script src="./graficos/gps-3d-3/lib/TerrainLoader.js"></script> 
<script>

    var width  = window.innerWidth,
        height = window.innerHeight;

    var scene = new THREE.Scene();
    scene.add(new THREE.AmbientLight(0xeeeeee));

    var camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.set(0, -300, 300);

    var renderer = new THREE.WebGLRenderer();
    renderer.setSize(width, height);

    var terrainLoader = new THREE.TerrainLoader();

    terrainLoader.load('./graficos/gps-3d-3/assets/jotunheimen.bin', function(data) {

        var geometry = new THREE.PlaneGeometry(600, 600, 199, 199);

        for (var i = 0, l = geometry.vertices.length; i < l; i++) {
            geometry.vertices[i].z = data[i] / 65535 * 5 * 10;
            console.log(data[i]/65535*50)
        }

        var material = new THREE.MeshPhongMaterial({
            map: THREE.ImageUtils.loadTexture('./graficos/gps-3d-3/assets/jotunheimen-texture.jpg')
        });

        var plane = new THREE.Mesh(geometry, material);
        scene.add(plane);

    });

    var controls = new THREE.TrackballControls(camera); 

    document.getElementById('webgl').appendChild(renderer.domElement);

    render();

    function render() {
        controls.update();    
        requestAnimationFrame(render);
        renderer.render(scene, camera);
    }

</script>
</body>
</html>