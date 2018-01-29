<?php
	require 'autoload.php';
	
	$acc = new Acceso();
	
	$e=array();
	
	$gen          = new General();
	$db           = DB::getInstance();
	$dat          = $db->query("SELECT x, y FROM uman_ultimogps ORDER BY FECHAGPS DESC LIMIT 1");
	$dat          = $dat->results()[0];
	$LAT          = $dat->x;
	$LNG          = $dat->y;
	
	$utemp        = $gen->getParamValue('unidad_temperatura');
	$upres        = $gen->getParamValue('unidad_presion');
	$nomenclatura = $gen->getNomenclaturas();
	$zoom         = $gen->getParamValue('zoom-2d',15);
	$oeem2dceet   = $gen->getParamValue('oeem2dceet', 0); //Ocultar Equipos En Mapa 2D Cuando Están En Timeout
	
	$TITULO    = $module_label; //'Monitoreo GPS 2D';
	$SUBTITULO = '';
?>

<style>
	#map-canvas { 
		/*width: 100%;*/
		/* height: 600px;  */
		position: relative;
		width: 100%;
		height: 90vh !important;
	}
	.detalle-mini {
		min-width: 400px;
		min-height: 480px;
	}
	.marcador{
		font-size: 20px;
		font-weight: bold;
		color: yellow;
	}
	#modal_equipo_content{
		text-align: -webkit-center;
		height: 95vh !important;
	}
	.modal-body{
		height: calc( 100% - 129.8906px ) !important;
	}
	@media (max-width: 799px){
		.modal-dialog{
			width: 98% !important;
			left: 0 !important;
		}
		.real-time-data{
			height: 500px;
			overflow-y: auto;
		}
	}
	@media (min-width: 800px){
		.modal-dialog{
			width: 90% !important;
      max-width: 90% !important;
		}
		.real-time-data{
			margin-left: -10px;
			overflow-y: hidden;
		}
	}
</style>

<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">

<div id="map-canvas"></div>
<br />

<!-- Modal Vista Equipo -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="modal_equipo">
  <div class="modal-dialog center-block">
    <div class="modal-content center-block" id="modal_equipo_content">
      <!-- Aqui se carga la información del equipo, via ajax -->
    </div>
  </div>
</div>


<?php if($map_api == 'here'){ ?><!-- here -->
	<script type="text/javascript">
		var platform = new H.service.Platform({
			'app_id': '<?=$GLOBALS['HERE']['ID']?>', 
			'app_code': '<?=$GLOBALS['HERE']['CODE']?>'
		});

		var defaultLayers = platform.createDefaultLayers();

		var map = new H.Map(
	  	document.getElementById('map-canvas'),
			defaultLayers.satellite.map,
			{
				zoom: <?=$zoom?>,
				center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
				fixedCenter: false,
			});

		var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

		var ui = H.ui.UI.createDefault(map, defaultLayers);
	</script>
<?php }else{ ?><!-- google -->
	<script type="text/javascript">
	  var map = new google.maps.Map(document.getElementById('map-canvas'), {
	    zoom: <?=$zoom?>,
	    center: { lat: <?=$LAT?>, lng: <?=$LNG?> },
	    mapTypeId: 'satellite'
	  });
	</script>
<?php } ?>

<script type="text/javascript">
	var equipos = Array(); //Marcadores

	var dEquipo      = new Array();
	var dEquipoPos   = new Array();

	var nomenclatura = <?=json_encode($nomenclatura)?>;
	var utemp        = '<?=$utemp?>';
	var upres        = '<?=$upres?>';
	var equipoActivo = '';

	var ult_inc      = {posicion:'', pendiente: ''};

	function obtener_datos(){
		$.post('ajax/mapa-2d/datos-gps-equipos.php', function(json){
			if(json.type == 'success'){
				if(json.data){
					for(i=0; i<json.data.length; i++){
						var gps = json.data[i];
						var cy = 0;
						var lat1 = parseFloat(gps.lat);
						var lon1 = parseFloat(gps.lng);
						var alt1 = parseInt(gps.altura);

						dEquipo[gps.id_equipo] = gps;

						if(dEquipoPos[gps.id_equipo]){
							dEquipoPos[gps.id_equipo] = {
								lat1: dEquipoPos[gps.id_equipo]['lat2'],
								lon1: dEquipoPos[gps.id_equipo]['lon2'],
								alt1: dEquipoPos[gps.id_equipo]['alt2'],

								lat2: gps.lat,
								lon2: gps.lng,
								alt2: gps.altura
							};
						}
						else{
							lat1 = parseFloat(gps.lat);
							lon1 = parseFloat(gps.lng);
							alt1 = parseInt(gps.altura);

							dEquipoPos[gps.id_equipo] = {
								lat1: gps.lat, lon1: gps.lng, alt1: gps.altura, 
								lat2: gps.lat, lon2: gps.lng, alt2: gps.altura
							};
						}
						
						<?php if($map_api == 'here'){ ?>
							var icn_url = new H.map.Icon(
								'assets/img/gps/marcador.php?direccion='+gps.direccion+'&tipo='+gps.tipo_equipo+'&color='+gps.color, 
								{ size: { w: 56, h: 56 } }
							);

							if(equipos[gps.id_equipo]){
								var pos = equipos[gps.id_equipo].getPosition();
								if(pos.lat != gps.lat || pos.lng != gps.lng){
									equipos[gps.id_equipo].setPosition({ lat: gps.lat, lng: gps.lng, alt: gps.altura });
									equipos[gps.id_equipo].setIcon(icn_url);
								}
								
								//console.log(equipos[gps.id_equipo].getData());
							}
							else{
								equipos[gps.id_equipo] = new H.map.Marker({ lat: gps.lat, lng: gps.lng }, { icon: icn_url });
								equipos[gps.id_equipo].addEventListener('tap', function(evt){
									var g = evt.target.getData().gps;
									var html = '';
									
									$("#modal_equipo").data('idequipo',g.id_equipo);
									$("#modal_equipo_content").load("./modal/modal.equipo.gps.php?equipo="+g.id_equipo);
									$("#modal_equipo").modal('show');
								}, false);
								map.addObject(equipos[gps.id_equipo]);
							}

							equipos[gps.id_equipo].setData({ gps: gps });
						<?php }else{ ?>
							// google
							var icn_url = {
								url: './assets/img/gps/marcador.php?direccion='+gps.direccion+'&tipo='+gps.tipo_equipo+'&color='+gps.color, 
								scaledSize: new google.maps.Size(56, 56),
								// origin: new google.maps.Point(0, 0),
								// anchor: new google.maps.Point(0, 27)
								labelOrigin: new google.maps.Point(28,56)
							};

							if(equipos[gps.id_equipo]){
								var pos = equipos[gps.id_equipo].getPosition();
								if(pos.lat != gps.lat || pos.lng != gps.lng){
									equipos[gps.id_equipo].setPosition({ lat: gps.lat, lng: gps.lng });
									equipos[gps.id_equipo].setIcon(icn_url);
								}
							}
							else{
								equipos[gps.id_equipo] = new google.maps.Marker({
									position: { lat: gps.lat, lng: gps.lng }, 
									icon: icn_url,
									map: map,
									label: {
										text: gps.nom_equipo,
										color: "white",
										fontSize: "17px",
										fontWeight: "bold",
									}
								});
								// equipos[gps.id_equipo] = new MarkerWithLabel({
								// 	position: { lat: gps.lat, lng: gps.lng }, 
								// 	animation: google.maps.Animation.DROP,
								// 	icon: icn_url,
								// 	map: map,
								// 	labelContent: gps.nom_equipo,
								// 	labelAnchor: new google.maps.Point(28,56),
								// 	labelClass: "marcador",
								// 	labelInBackground: true
								// });
								equipos[gps.id_equipo].addListener('click', function(e){
									console.log(this);
									var g = this.gps;
									var html = '';
									
									$("#modal_equipo").data('idequipo',g.id_equipo);
									$("#modal_equipo_content").load("./modal/modal.equipo.gps.php?equipo="+g.id_equipo);
									$("#modal_equipo").modal('show');
								});
							}

							equipos[gps.id_equipo]['gps'] = gps;
						<?php } ?>

						if(equipoActivo!=''){
							var icn_url = 'assets/img/gps/marcador.php';
							icn_url += '?direccion='+dEquipo[equipoActivo].direccion+'&tipo='+dEquipo[equipoActivo].tipo_equipo+'&color='+dEquipo[equipoActivo].color;
							$("#orientacion").attr("src",icn_url);
							$("#rapidez").html(dEquipo[equipoActivo].rapidez);
							$("#altura").html(dEquipo[equipoActivo].altura);

							lat1 = parseFloat(dEquipoPos[gps.id_equipo]['lat1']);
							lon1 = parseFloat(dEquipoPos[gps.id_equipo]['lon1']);
							alt1 = parseInt(dEquipoPos[gps.id_equipo]['alt1']);

							var lat2 = parseFloat(dEquipoPos[gps.id_equipo]['lat2']);
							var lon2 = parseFloat(dEquipoPos[gps.id_equipo]['lon2']);
							var alt2 = parseInt(dEquipoPos[gps.id_equipo]['alt2']);

							var DH = google.maps.geometry.spherical.computeDistanceBetween(
								new google.maps.LatLng(lat1,lon1), 
								new google.maps.LatLng(lat2,lon2));
							if(DH<5 || gps.rapidez == 0) DH = 0;
							var DV = (alt2 - alt1);

							var Porcentaje = 0;
							var Pendiente = 0;
							if(DH!=0) Pendiente = Math.round( (Math.atan(DV/DH)*180)/Math.PI);
							//if(DH!=0) Porcentaje = Math.abs(Math.round((DV/DH)*100));
							if(DH!=0) Porcentaje = Math.round( Math.tan(Pendiente/57.3) * 100 );

							if(ult_inc.posicion != (gps.lat+','+gps.lng) ){
								ult_inc.posicion = (gps.lat+','+gps.lng);
								ult_inc.pendiente = '<strong>'+Pendiente+'&deg; / '+Porcentaje+'%</strong>';
							} 

							$("#pendiente").html(ult_inc.pendiente);

							$("#latitud").html(gps.lat);
							$("#longitud").html(gps.lng);
							$("#fecha-gps").html(gps.fecha);
							var tbody = '';
							for(k=1; k<=16; k++){
								if($("#pos"+k).length>0){
									if(dEquipo[equipoActivo]['sensor'+k] != ""){
										dEquipo[equipoActivo]['color'+k] = ((dEquipo[equipoActivo]['color'+k] != "") ? dEquipo[equipoActivo]['color'+k] : 'gray')
										$("#pos"+k).removeClass('neum-orange').removeClass('neum-red').removeClass('neum-yellow').removeClass('neum-lilac').removeClass('neum-black').removeClass('neum-gray');
										$("#pos"+k).addClass('neum-'+ dEquipo[equipoActivo]['color'+k]);

										tbody += '<tr class="'+ dEquipo[equipoActivo]['color'+k] +'">';
										tbody += '<td>'+dEquipo[equipoActivo]['sensor'+k]+'</td>';
										tbody += '<td><center>'+nomenclatura[k]+'</center></td>';
										// if(dEquipo[equipoActivo]['color'+k] != 'gray'){
											tbody += '<td><center>'+celsius2other(dEquipo[equipoActivo]['temperatura_evento'+k])+'</center></td>';
											tbody += '<td><center>'+ 
												psi2other(dEquipo[equipoActivo]['presion_evento'+k]) +
												' / ' +
												psi2other(dEquipo[equipoActivo]['presion_recomendada'+k]) +
												'</center></td>';
											tbody += '<td>'+dEquipo[equipoActivo]['fecha_evento'+k]+'</td>';
										// }
										// else{
										// 	tbody += '<td><center>-</center></td>';
										// 	tbody += '<td><center>-</center></td>';
										// 	tbody += '<td><center>-</center></td>';
										// }

										tbody += '</tr>';
									}
								}
							}
							$("#datos > tbody").html(tbody);
						}
					}
				}
			}
			else{
				console.log(json);
			}
		});
	}

	var ixx = setInterval(obtener_datos, 4000);
	obtener_datos();
</script>

<script type="text/javascript">
	$(document).ready(function () {

		$("#modal_equipo").on('hidden.bs.modal', function(e){
			equipoActivo = '';
		});

		$("#modal_equipo").on('shown.bs.modal', function(e){
			let minHeight = window.screen.availHeight-200;
			console.log("window.screen.availHeight: "+window.screen.availHeight);
			$("div.modal-body").css("min-height", minHeight+'px');
			let actHeight = parseInt($('#body-detalle').css("height").replace('px',''));
			maxContentHeight = (minHeight > actHeight ? minHeight : actHeight) - 62;
			console.log(maxContentHeight);
			$("div.modal-body").css("min-height", (window.screen.availHeight*0.65)+'px');
			equipoActivo = $(this).data('idequipo');
		});
				
		// $(window).on('resize', function(){
		// 	$("#map-canvas").css("height", (window.screen.availHeight-190)+'px');
		// });
		// $("#map-canvas").css("height", (window.screen.availHeight-190)+'px'); 
	});
	
	function celsius2other(temperature,rounded=true){
		var unit = 'C';
		if(utemp=='kelvin'){
			temperature = (temperature + 273.15);
			unit = 'K';
		}
		else if(utemp=='fahrenheit'){
			temperature = (temperature*1.8) + 32;
			unit = 'F';
		}

		if(rounded) temperature = Math.round(temperature);
		return temperature+'&deg; <small>'+unit+'</small>';
	}

	function psi2other(pressure,rounded=true){
		var unit = 'PSI';
		if(upres=='bar'){
			pressure = pressure / 14.5;
			unit = 'BAR';
		}
	
		if(rounded) pressure = Math.round(pressure);
		return pressure+' <small>'+unit+'</small>';
	}
</script>