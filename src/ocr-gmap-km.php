<?php
/*
 * ocr-gmap-draw.php
 * Test para calcular distancia lineal y dibujar mapas de punto de origen y destino
 * Las coordenadas de prueba corresponden a:
 * 1) Credicorp Bank | Via España (Panama)
 *    $('input#lat1').val(8.9843743);
 *    $('input#long1').val(-79.5240926);
 * 2) Solmaforo de la cinta costera 2, justo al comienzo del mirador del pacifico (Panama)
 *    $('input#lat2').val(8.9635629);
 *    $('input#long2').val(-79.5346246);
 */
include_once('ocr-gmap-config.php');
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>OCR-Acarreo - Calculadora de Distancia lineal para Acarreos</title>
		<?php echo $configClass->selectAction('getHeadLinks'); ?>
		
		<!--script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false&v=3&libraries=geometry'></script-->
		
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key;?>&libraries=geometry"></script>
	
		<script>
			$(document).ready(function() {
				//alert('inicia valores de input');
				$('input#lat1').val(8.9843743);
				$('input#long1').val(-79.5240926);
				$('input#lat2').val(8.9635629);
				$('input#long2').val(-79.5346246);
				initialize_distance_calc();
			});
		</script>
		<script>
			
			/*
			 * https://stackoverflow.com/questions/5243410/call-javascript-function-from-jquery
			 */
			function dibuja_map(parLat, parLong, divName, tituloDivName, titulo) {
				//var latlng = new google.maps.LatLng(40.7562008,-73.9903784);
				var latlng = new google.maps.LatLng(parLat, parLong);
				var myOptions = {
					zoom: 18,
					center: latlng /*,
					mapTypeId: google.maps.MapTypeId.ROADMAP */
				};
				$('div#'+tituloDivName).html('<p>'+titulo+':</p>');
				var map = new google.maps.Map(document.getElementById(divName), myOptions);
			}
			
			/*
			 * Obteniendo distancia (km’s) entre dos puntos Google Maps
			 * https://raualvron.wordpress.com/2016/11/28/obteniendo-distancia-kms-entre-dos-puntos-google-maps/
			 * Utilizaremos el metodo google.maps.geometry.spherical de la libreria GMap3
			 * Agrega la libreria geometry de Google Maps
			 * http://maps.google.com/maps/api/js?sensor=false&v=3&libraries=geometry
			 */
			function getDistance(lat1, long1, lat2, long2) {
				//alert('getDistance('+lat1+', '+long1+', '+lat2+', '+long2+')');
				var distance  = (google.maps.geometry.spherical.computeDistanceBetween(
					new google.maps.LatLng(lat1,long1), 
					new google.maps.LatLng(lat2,long2)
					) / 1000).toFixed(2);
				return distance;
			}
			
			function initialize_distance_calc() {
				$('input#submitButton').click(function() {
					
					// https://stackoverflow.com/questions/8271836/isnan-vs-parseint-confusion
					if( isNaN(parseInt($('input#lat1').val())) || isNaN(parseInt($('input#long1').val())) || isNaN(parseInt($('input#lat2').val())) || isNaN(parseInt($('input#long2').val()))) {
						alert('No muestra mapas de origen y destino porque alguna de las coordenadas NO es numerica');
						return;
					}
					
					//execute the js function here
					var resultado=getDistance($('input#lat1').val(), $('input#long1').val(), $('input#lat2').val(), $('input#long2').val());
					
					//alert('resultado: '+resultado);
					$('div#resultZone').html('Distancia = ' + resultado + ' Km');

					//alert('Muestra mapas porque todas las coordenadas son numericas');
					dibuja_map($('input#lat1').val(), $('input#long1').val(), 'mapOrigen', 'mapOrigenTitulo', 'Mapa de Origen');
					dibuja_map($('input#lat2').val(), $('input#long2').val(), 'mapDestino', 'mapDestinoTitulo', 'Mapa de Destino');
				});
		}
		</script>
	
	</head>
	<body>
		<?php 
		$configClass->titulo = 'OCR-Acarreo - Calculadora para Acarreos (basico)';
		$configClass->intro = 'El resultado corresponde a la distancia en línea recta entre dos coordenadas. 
		No se toma en cuenta la distancia a recorrer en las calles, bien sea a pie o en auto.';
		echo $configClass->selectAction('getGeneralForm'); 
		?>
		<!--div id='intro'>
		<h1>OCR-Acarreo - Calculadora de Distancia para Acarreos</h1>
		<p>El resultado corresponde a la distancia en línea recta entre dos coordenadas. 
		No se toma en cuenta la distancia a recorrer en las calles, bien sea a pie o en auto.</p>
		</div>
		<div id='inputVarZone'>
			<p>Origen:</p>
			<p>Latitud: <input type="text" id="lat1" /> Longitud: <input type="text" id="long1" /><br></p>
			<p>Destino:</p>
			<p>Latitud: <input type="text" id="lat2" /> Longitud: <input type="text" id="long2" /><br></p>
			<p><input type="button" id="submitButton" value='CALCULAR'/></p>
		</div>
		<div id="resultZone">
		</div-->
		<!--Start Google Map-->
		<div id="mapOrigenTitulo"></div>
		<div id="mapOrigen"></div>
		<div id="mapDestinoTitulo"></div>
		<div id="mapDestino"></div>
		<!--End Google Map-->
	</body>
</html>
