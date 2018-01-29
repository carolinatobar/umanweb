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
    $info = mysql_query("SELECT X,ALTURA,Y FROM uman_gps WHERE EQUIPO='$equipo' LIMIT 2200");
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



<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
#container {
    height: 700px; 
    min-width: 310px; 
    max-width: 800px;
    margin: 0 auto;
}
		</style>
	</head>
	<body>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="height: 400px"></div>


		<script type="text/javascript">


// Give the points a 3D feel by adding a radial gradient
Highcharts.getOptions().colors = $.map(Highcharts.getOptions().colors, function (color) {
    return {
        radialGradient: {
            cx: 0.4,
            cy: 0.3,
            r: 0.5
        },
        stops: [
            [0, color],
            [1, Highcharts.Color(color).brighten(-0.2).get('rgb')]
        ]
    };
});

// Set up the chart
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'container',
        margin: 100,
        type: 'scatter',
        options3d: {
            enabled: true,
            alpha: 10,
            beta: 30,
            depth: 750,
            viewDistance: 5,
            fitToPlot: false,
            frame: {
                bottom: { size: 1, color: 'rgba(0,0,0,0.02)' },
                back: { size: 1, color: 'rgba(0,0,0,0.04)' },
                side: { size: 1, color: 'rgba(0,0,0,0.06)' }
            }
        }
    },
    events: {
          load: function() {

        // set up the updating of the chart each second
        var series = this.series[0];
        setInterval(function(){
        var chart = new Highcharts.Chart(options);
        $.getJSON('json.php', function(jsondata) {
            options.series[0].data = JSON.parse(jsondata.cpu);
        });
        }, 5000);
       }              
    },
    title: {
        text: 'Circuito 3D'
    },
    subtitle: {
        text: 'Equipo <?php print $equipo; ?> - Faena El Soldado'
    },
    plotOptions: {
        scatter: {
            width: 200,
            height: 200,
            depth: 200
        }
    },
    yAxis: {
        min: 300,
        max: 1000,
        title: null
    },
    xAxis: {
        min: <?php print $xmin; ?>,
        max: <?php print $xmax; ?>,
        gridLineWidth: 1
    },
    zAxis: {
        min: <?php print $zmin; ?>,
        max: <?php print $zmax; ?>,
        showFirstLabel: false
    },
    legend: {
        enabled: true
    },
    series: [{
        name: 'Posicion',
        dashStyle: 'Dot',
        lineWidth: 1,
        colorByPoint: false,
        data: [<?php
        for ( $i = 0 ; $i < $total ; $i++){
            if($i!=0) {
                print ",";
            }
            print "[".$x[$i].",".$y[$i].",".$z[$i]."]";
        }
        ?>]
    }]
});


// Add mouse events for rotation
$(chart.container).on('mousedown.hc touchstart.hc', function (eStart) {
    eStart = chart.pointer.normalize(eStart);

    var posX = eStart.pageX,
        posY = eStart.pageY,
        alpha = chart.options.chart.options3d.alpha,
        beta = chart.options.chart.options3d.beta,
        newAlpha,
        newBeta,
        sensitivity = 1; // lower is more sensitive

    $(document).on({
        'mousemove.hc touchdrag.hc': function (e) {
            // Run beta
            newBeta = beta + (posX - e.pageX) / sensitivity;
            chart.options.chart.options3d.beta = newBeta;

            // Run alpha
            newAlpha = alpha + (e.pageY - posY) / sensitivity;
            chart.options.chart.options3d.alpha = newAlpha;

            chart.redraw(false);
        },
        'mouseup touchend': function () {
            $(document).off('.hc');
        }
    });
});

		</script>
	</body>
</html>
