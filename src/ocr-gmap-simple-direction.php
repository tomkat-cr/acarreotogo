<?php
/*
 * ocr-gmap-simple-direction.php
 *
 * https://developers.google.com/maps/documentation/javascript/directions?hl=Es
 * >>>
 * https://developers.google.com/maps/documentation/javascript/examples/directions-simple?hl=Es
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
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title><?php echo $configClass->getGenPar('appName'); ?> - Directions service</title>
	<?php echo $configClass->selectAction('getHeadLinks'); ?>
  </head>
  <body>
	<div id="floating-panel">
		<b>Origen: </b>
		<input type="text" id="start" value="<?php echo $configClass->getGenPar('placeName1'); ?>">
		<b>Destino: </b>
		<input type="text" id="end" value="<?php echo $configClass->getGenPar('placeName2'); ?>">
		<input type="button" id="calcButton" value='CALCULAR'/>
	</div>
	<div id="floating-get-gapi">
		<div class="container-fluid">
				<div class="row">
					<legend>Solicitud de Google API Key</legend>
				</div>
				<div class="row" id="gapiKeyTitleZone">
					<div class="col-md-12">
					Es necesario registrar el Google API Key para tener acceso a las funcionalidades de esta aplicación.<br/>
					<a href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=es" target="_new">Para más información, haz clic aquí</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<form class="form-inline" id="needs-validation" method="POST">
							<div class="form-group" id="gapiKeyInputZone">
								<div class="form-group mx-sm-3">
									<label for="gapiKey" class="mr-sm-2">Google API Key:</label>
									<input type="text" id="gapiKey" class="form-control" size="35" placeholder="API Key" required>
								</div>
								<input type="button" id="gapiKeyButton" class="btn btn-primary" value='GUARDAR' />
							</div>
						</form>
					</div>
					<div class="col-md-4"></div>
				</div>
				<div class="row">
					<div class="col-md-12" id="gapi-response"></div>
				</div>
		</div>
	</div>
	<div id="map"></div>
	<?php echo $configClass->selectAction('getGeneralForm'); ?>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $configClass->getPrivatePar('gapik'); ?>&callback=initMap">
    </script>
  </body>
</html>