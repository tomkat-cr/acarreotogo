<?php
/*
 * ocr-gmap-draw.php
 * Test para dibujar un mapa
 * Las coordenadas de prueba corresponden a:
 * 1) Credicorp Bank | Via España, obarrio
 *    $('input#lat1').val(8.9843743);
 *    $('input#long1').val(-79.5240926);
 * 2) Solmaforo de la cinta costera 2, justo al comienzo del mirador del pacifico
 *    $('input#lat2').val(8.9635629);
 *    $('input#long2').val(-79.5346246);
 */
include_once('ocr-gmap-config.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
	<?php echo $configClass->selectAction('getHeadLinks'); ?>
  </head>
  <body>
		<?php echo $configClass->selectAction('getGeneralForm'); ?>
		<!--div id='inputVarZone'>
			<p>Origen:</p>
			<p>Latitud: <input type="text" id="lat1" /> Longitud: <input type="text" id="long1" /><br></p>
			<p>Destino:</p>
			<p>Latitud: <input type="text" id="lat2" /> Longitud: <input type="text" id="long2" /><br></p>
			<p><input type="button" id="submitButton" value='CALCULAR'/></p>
		</div-->
    <div id="map"></div>
    <script>
      var map;
      function initMap() {
			$('input#lat2').val(8.9635629);
			$('input#long2').val(-79.5346246);
				var latlng = new google.maps.LatLng($('input#lat2').val(),$('input#long2').val());
				var myOptions = {
					zoom: 18,
					center: latlng,
					//mapTypeId: google.maps.MapTypeId.ROADMAP
				};
//alert('lat2 = '+ lat2 + ', long2 = '+long2);
			/*map = new google.maps.Map(document.getElementById('map'), {
			  center: {lat: lat2, lng: long2},
			  zoom: 8
			});
			*/
			var map = new google.maps.Map(document.getElementById("map"), myOptions);
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key;?>&callback=initMap"
    async defer></script>
  </body>
</html>