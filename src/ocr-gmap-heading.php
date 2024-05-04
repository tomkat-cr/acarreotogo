<?php
/*
 * ocr-gmap-heading.php
 *
 * Navigation functions (heading)
 * https://developers.google.com/maps/documentation/javascript/examples/geometry-headings
 * This example demonstrates computing the heading between two coordinates using the 
 * Geometry library. Drag the markers on the map to see the origin, destination 
 * and heading change accordingly.
 *
 * Llamada URL para invocar al modo Interactivo:
 * https://www.google.com.pa/maps/dir/Casco+Viejo,+Panamá/EL+CRISOL,+Panamá
 *
 */
include_once('ocr-gmap-config.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>OCR-Acarreo - Navigation functions (Heading)</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 60%;
		/*
        width: 50%;
        position: relative;
        top: 50px;
        left: 15%;
		*/
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
    </style>
	<?php echo $configClass->selectAction('getHeadLinks'); ?>
  </head>
  <body>
	<div id="map"></div>
	<div id="floating-panel">
	  Origin: <input type="text" readonly id="origin">
	  Destination: <input type="text" readonly id="destination"><br>
	  Heading: <input type="text" readonly id="heading"> degrees
	</div>
	<div id="info"></div>
	<?php echo $configClass->selectAction('getGeneralForm'); ?>
    <script>
      // This example requires the Geometry library. Include the libraries=geometry
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=geometry">

	    init_screen();
      var marker1, marker2;
      var poly, geodesicPoly;
 
      var varLat1 = parseFloat($('input#lat1').val());
      var varLong1 = parseFloat($('input#long1').val());
      var varLat2 = parseFloat($('input#lat2').val());
      var varLong2 = parseFloat($('input#long2').val());

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: <?php echo $genPar['zoom0']; ?>,
          center: {lat: <?php echo $genPar['lat0']; ?>, lng: <?php echo $genPar['long0']; ?>}
/*
          zoom: 4,
          center: {lat: 34, lng: -40.605}
*/
        });

        map.controls[google.maps.ControlPosition.TOP_CENTER].push(
            document.getElementById('info'));

        marker1 = new google.maps.Marker({
          map: map,
          draggable: true,
          position: {lat: varLat1, lng: varLong1}
//		  position: {lat: 40.714, lng: -74.006}
        });

        marker2 = new google.maps.Marker({
          map: map,
          draggable: true,
          position: {lat: varLat2, lng: varLong2}
//		  position: {lat: 48.857, lng: 2.352}
        });

        var bounds = new google.maps.LatLngBounds(
            marker1.getPosition(), marker2.getPosition());
        //map.fitBounds(bounds);

        google.maps.event.addListener(marker1, 'position_changed', update);
        google.maps.event.addListener(marker2, 'position_changed', update);

        poly = new google.maps.Polyline({
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 3,
          map: map,
        });

        geodesicPoly = new google.maps.Polyline({
          strokeColor: '#CC0099',
          strokeOpacity: 1.0,
          strokeWeight: 3,
          geodesic: true,
          map: map
        });

        update();
      }

      function update() {
        var path = [marker1.getPosition(), marker2.getPosition()];
        poly.setPath(path);
        geodesicPoly.setPath(path);
        var heading = google.maps.geometry.spherical.computeHeading(path[0], path[1]);
        document.getElementById('heading').value = heading;
        document.getElementById('origin').value = path[0].toString();
        document.getElementById('destination').value = path[1].toString();
		// Actualiza campos en pantalla y recalcula tarifa
		updateInputFields(path);
      }
	  
		function updateInputFields(path) {
			$('input#placeName1').val('');
			$('input#lat1').val(path[0].lat);
			$('input#long1').val(path[0].lng);
			$('input#placeName2').val('');
			$('input#lat2').val(path[1].lat);
			$('input#long2').val(path[1].lng);
		}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key;?>&libraries=geometry&callback=initMap" async defer></script>
  </body>
</html>