<?php
/*
 * ocr-gmap-from-to-test.php
 * Test para calcular distancia total de recorrido y dibujar mapa
 * Las coordenadas de prueba corresponden a:
 * 1) Credicorp Bank | Via España, Obarrio
 *    $('input#lat1').val(8.9843743);
 *    $('input#long1').val(-79.5240926);
 * 2) Solmaforo de la cinta costera 2, justo al comienzo del mirador del pacifico
 *    $('input#lat2').val(8.9635629);
 *    $('input#long2').val(-79.5346246);
 */
include_once('ocr-gmap-config.php');
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>OCR-Acarreo - Calculadora de Distancia de recorrido para Acarreos</title>
		<?php echo $configClass->selectAction('getHeadLinks'); ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key;?>&libraries=geometry"></script>
	</head>
	<body>
		<?php 
		$configClass->titulo = 'OCR-Acarreo - Calculadora de Distancia para Acarreos';
		$configClass->intro = 'El resultado corresponde a la distancia total entre dos coordenadas.<br/> 
		De toma en cuenta la distancia a recorrer en las calles, bien sea a pie o en auto.<br/>
		Seleccionar [TRANSIT] y [BEST_GUESS] para obtener el estimado de tiempo más realista posible.';
		echo $configClass->selectAction('getGeneralForm');
		?>
		<!--Start Google Map-->
		<div id="mapOrigenTitulo"></div>
		<div id="mapOrigen"></div>
		<div id="mapDestinoTitulo"></div>
		<div id="mapDestino"></div>
		<!--End Google Map-->
		<script>
			init_screen();
		</script>
	</body>
</html>