<?php

    include("conectar.php");
    $info = mysql_query("SELECT X,ALTURA,Y FROM uman_ultimogps WHERE EQUIPO='10000' ORDER BY ID desc LIMIT 1");
        $x[$total] = $data[0];
        $y[$total] = $data[1];
        $z[$total] = $data[2];
?>


{
    "xData": [],
    "datasets": [{
        "name": "Velocidad",
        "data": [],
        "unit": "km/h",
        "type": "line",
        "valueDecimals": 1
    }, {
        "name": "Altura",
        "data": [],
        "unit": "m",
        "type": "area",
        "valueDecimals": 0
    }, {
        "name": "Aceleracion",
        "data": [],
        "unit": "km/h",
        "type": "area",
        "valueDecimals": 0
    }]
}