<?php
/*
 * ocr-gmap-config.php
 */
class configClass {
	
	private $genPar = array();
	private $genParType = array();
	private $privatePar = array();
	private $privateParType = array();
	private $genParFileName = 'ocr-gmap-cnf1.php';
	private $privateParFileName = 'ocr-gmap-cnf2.php';
	private $error = false;
	private $errorCode = '';
	private $errorMsg = '';
	
	public $titulo = '';
	public $intro = '';
	
	private $secInitString = '<?php ';
	private $secEndString = '?'.'>';
	
	// Constructor
	function __construct($Param = array()) {
		$this->genPar = $Param;
		if (!$this->loadParamsFromEnvFile()) {
			$this->loadParamsFromFile();
		}
	}
	
	function loadParamsFromEnvFile() {
		// Load the environment variables from a .env file
		if (file_exists('.env')) {
			$lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach ($lines as $line) {
				if (strpos($line, '#') === 0) {
					continue; // Skip comments
				}
				list($name, $value) = explode('=', $line, 2);
				putenv("$name=$value");
			}
		}
		$this->privatePar['gapik'] = getenv('GOOGLE_MAPS_API_KEY');

		$this->genPar['appName'] = getenv('APP_NAME');
		$this->genPar['appIG'] = getenv('APP_IG');
		$this->genPar['appWeb'] = getenv('APP_WEB');
		$this->genPar['placeNameMyOffice'] = getenv('PLACE_NAME_MY_OFFICE');
		$this->genPar['latMyOffice'] = getenv('LAT_MY_OFFICE');
		$this->genPar['longMyOffice'] = getenv('LONG_MY_OFFICE');
		$this->genPar['lat0'] = getenv('LAT0');
		$this->genPar['long0'] = getenv('LONG0');
		$this->genPar['zoom0'] = getenv('ZOOM0');
		$this->genPar['placeName1'] = getenv('PLACE_NAME_1');
		$this->genPar['lat1'] = getenv('LAT1');
		$this->genPar['long1'] = getenv('LONG1');
		$this->genPar['placeName2'] = getenv('PLACE_NAME_2');
		$this->genPar['lat2'] = getenv('LAT2');
		$this->genPar['long2'] = getenv('LONG2');
		$this->genPar['TravelMode'] = getenv('TRAVEL_MODE'); // "DRIVING",
		$this->genPar['TrafficModel'] = getenv('TRAFFIC_MODEL'); // "bestguess",
		$this->genPar['AvoidHighways'] = getenv('AVOID_HIGHWAYS'); // "1",
		$this->genPar['AvoidTolls'] = getenv('AVOID_TOLLS'); // "1",
		$this->genPar['N'] = getenv('N'); // "60",
		$this->genPar['tarifaDlrPorKm'] = getenv('TARIFA_DLR_POR_KM'); // "1.00",
		$this->genPar['cantidadViajes'] = getenv('CANTIDAD_VIAJES'); // "1",
		$this->genPar['kmPorLitro'] = getenv('KM_POR_LITRO'); // "8.7",
		$this->genPar['costoLitroGasolina'] = getenv('COSTO_LITRO_GASOLINA'); // "0.63",
		$this->genPar['servicioCadaXkm'] = getenv('SERVICIO_CADA_X_KM'); // "5000",
		$this->genPar['servicioCosto'] = getenv('SERVICIO_COSTO'); // "150",
		$this->genPar['kmMyOfficeToOrigin'] = getenv('KM_MY_OFFICE_TO_ORIGIN'); // "7.06",
		$this->genPar['kmDestinationToMyOffice'] = getenv('KM_DESTINATION_TO_MY_OFFICE'); // "13.19",

		return true;
	}

	// Asigna valores por defecto de parametros privados
	function setDefPrivateValues() {
		$defValues = array();
		$defValues['gapik'] = '';
		$this->privatePar = array_merge($defValues, $this->privatePar);
	}
	
	// Asigna valores por defecto de parametros de trabajo
	function setDefValues() {
		$defValues = array();
		
		// Nombre del App
		$defValues['appName'] = '@acarreotogo'; // 'OCR-U-Move';
		$defValues['appIG'] = 'https://www.instagram.com/acarreotogo';
		$defValues['appWeb'] = 'http://acarreotogo.aclics.com';
		
		// Coordenadas de MyOffice
		$defValues['placeNameMyOffice'] = 'Credicorp Bank | Via España, Panama';
		$defValues['latMyOffice'] = 8.9843743;
		$defValues['longMyOffice'] = -79.5240926;
		// Coordenadas de mapa central
		$defValues['placeName0'] = 'Panamá';
		$defValues['lat0'] = 8.974;
		$defValues['long0'] = -79.528;
		$defValues['zoom0'] = 14;
		// Coordenadas de sitio de origen - Marcador 1
/*
		$defValues['placeName1'] = 'Credicorp Bank | Via España, Panama';
		$defValues['lat1'] = 8.9843743;
		$defValues['long1'] = -79.5240926;
*/
		$defValues['placeName1'] = 'Plaza Simon Bolivar, Panama';
		$defValues['lat1'] = 8.9533376;
		$defValues['long1'] = -79.5336858;
		// Coordenadas de sitio de destino - Marcador 2
/*
		$defValues['placeName2'] = 'Mirador del Pacífico';
		$defValues['lat2'] = 8.9635629;
		$defValues['long2'] = -79.5346246;
*/
		$defValues['placeName2'] = 'El Crisol, Panama';
		$defValues['lat2'] = 9.046653;
		$defValues['long2'] = -79.4759095;
		// Para calculo de distancias
		//$defValues['TravelMode'] = 'TRANSIT';
		$defValues['TravelMode'] = 'DRIVING';
		$this->genParType['TravelMode'] = 'select';
		$defValues['TrafficModel'] = 'bestguess';
		$this->genParType['TrafficModel'] = 'select';
		$defValues['AvoidHighways'] = '1';	// "1" = Si
		$this->genParType['AvoidHighways'] = 'select';
		$defValues['AvoidTolls'] = '1';	// "1" = Si
		$this->genParType['AvoidTolls'] = 'select';
		// Tiempo previo de proyección de tráfico
		$defValues['N'] = 60; // Minutos de antelación para estimado de tiempo en tráfico
		// Para calculo de tarifa
		$defValues['tarifaDlrPorKm'] = 1.50; // US$ / Km
		$defValues['cantidadViajes'] = 1; // Cantidad de viajes a realizar
		// Para calculo de costos
		$defValues['kmPorLitro'] = 8.7 ; // km/l(itro) de Hilux 2017 en ciudad segun: http://www.autocosmos.com.mx/catalogo/vigente/toyota/hilux/cabina-doble-base/161247
		$defValues['costoLitroGasolina'] = 0.63; // US$/l(itro) al 2017-08-22
		$defValues['servicioCadaXkm'] = 5000; // Servicio cada X Km
		$defValues['servicioCosto'] = 150.00; // Costo US$ de cada servicio
		// Distancia desde mi oficina a los sitios de origen y destino
		$defValues['kmMyOfficeToOrigin'] = 0.00;
		$defValues['kmDestinationToMyOffice'] = 0.00;
		// Miscelaneos
		$defValues['Debug'] = '0';
		
		// Une los valores por defecto con lo que viene del archivo de configuración de parametros
		$this->genPar = array_merge($defValues, $this->genPar);
	}

	function error() {
		return $this->error;
	}
	
	function errorMsg() {
		return $this->errorMsg;
	}
	
	function loadParamsFromFile() {
		if(file_exists($this->genParFileName)) {
			$response = $this->loadParFromFile($this->genParFileName);
			if($this->error == false) {
				$this->genPar = $response;
			} else {
				$this->errorCode = 'CNF-010';
			}
		}
		$this->setDefValues();
		if(file_exists($this->privateParFileName)) {
			$response = $this->loadParFromFile($this->privateParFileName);
			if($this->error == false) {
				$this->privatePar = $response;
			} else {
				$this->errorCode = 'CNF-020';
			}
		}
		$this->setDefPrivateValues();
		// Arregla los errores leyendo archivos de configuracion
		if($this->error == true) {
			switch($this->errorCode) {
				case 'CNF-010':
					$this->saveParToFile($this->genParFileName, $this->genPar);
					break;
				case 'CNF-020':
					$this->saveParToFile($this->privateParFileName, $this->privatePar);
					break;
			}
		}
	}
	
	function getJsonString($errCode, $parName = 'jsonString') {
		$error = false;
		$msg = 'OK';	// Se debe devolver siempre OK para que el Ajax lo tome como llamada exitosa
		$jsonString = (isset($_POST[$parName]) ? $_POST[$parName] : '');
//$msg = $jsonString;
		if($jsonString === '') {
			$error = true;
			$msg = 'ERROR: no se especificó parámetros ['.$errCode.']';
		}
		return array('error' => $error, 'msg' => $msg, 'jsonString' => $jsonString);
	}

	function saveGenParamsToFile() {
		$response = $this->getJsonString('JSON-ERR-010');
		if($response['error'] == false) {
			$this->genPar = json_decode($response['jsonString']);
			$this->saveParToFile($this->genParFileName, $this->genPar);
		}
		return $response['msg'];
	}

	function saveGAKtoFile() {
		/*
			<!--form class="form-inline" id="needs-validation" action="ocr-gmap-config.php" method="POST"-->
				<!--input type="hidden" id="m" value="sgak">
				<input type="hidden" id="r" value="ocr-gmap-simple-direction.php"-->
		*/
		$val = $_POST['gapiKey'];
		$returnPage = $_POST['r'];
		$this->privatePar['gapik'] = $val;
		$this->saveParToFile($this->privateParFileName, $this->privatePar);
		header('location: ' . $returnPage);
	}
	
	function savePrivParamsToFile() {
		$response = $this->getJsonString('JSON-ERR-020');
		if($response['error'] == false) {
			$newParValues = json_decode($response['jsonString']);
			foreach($newParValues as $key => $val) {
				$this->privatePar[$key] = $val;
			}
			$this->saveParToFile($this->privateParFileName, $this->privatePar);
		}
		return $response['msg'];
	}
	function loadParFromFile($pFileName) {
		$response = file_get_contents($pFileName);
		if(substr($response, 0, mb_strlen($this->secInitString)) == $this->secInitString) {
			// Elimina caracteres de seguridad al principio
			$response = substr($response, mb_strlen($this->secInitString));
		}
		if(substr($response, mb_strlen($response)-mb_strlen($this->secEndString)) == $this->secEndString) {
			// Elimina caracteres de seguridad al final
			$response = substr($response, 0, mb_strlen($response)-mb_strlen($this->secEndString));
		}
		$parRead = json_decode($response, true);
		if(!is_array($parRead)) {
			// Hubo algun problema con el JSON
			$this->error = true;
			$this->errorMsg = 'ERROR: leyendo la configuración [' . $this->getFileName($pFileName) . ']';
		}
		return $parRead;
	}

	function saveParToFile($pFileName, $arrayPar) {
		$content = json_encode($arrayPar);
		$content = $this->secInitString . $content . $this->secEndString;
		$response = file_put_contents($pFileName, $content);
		return ($response);
	}
	
	// Devuelve parametros generales
	function getGenPar($parName = '') {
		if($parName === '') {
			return $this->genPar;
		}
		return $this->genPar[$parName];
	}
	
	// Devuelve parametros privados
	function getPrivatePar($parName = '') {
		if($parName === '') {
			return $this->privatePar;
		}
		return $this->privatePar[$parName];
	}
	
	// Verifica llamadas Ajax
	function selectActionFromGet() {
		$callMode = (isset($_REQUEST['m']) ? $_REQUEST['m'] : '');
		return $this->selectAction($callMode);
	}
	
	function selectAction($callMode) {
		$response = '';
		switch($callMode) {
			case 'GetConfig':
				$response = $this->GetConfigJson($this->genPar);
				break;
			case 'SaveConfig':
				$response = $this->saveGenParamsToFile();
				break;
			case 'SavePrivConfig':
				$response = $this->savePrivParamsToFile();
				break;
			case 'sgak':
				$response = $this->saveGAKtoFile();
				break;
			case 'getTarifaValues':
				$response = $this->getTarifaValues();
				break;
			case 'GetConfigJS':
				$response = $this->GetConfigJS();
				break;
			case 'getGeneralForm':
				$response = $this->getGeneralForm();
				break;
			case 'getHeadLinks':
				$response .= $this->getHeadLinks();
				$response .= $this->GetConfigJS();
				break;
		}
		return $response;
	}
	
	// Devuelve parametros generales en formato Json
	function GetConfigJson($arrayPar) {
		return json_encode($arrayPar);
	}

	// Devuelve programacion JS con asignacion de parametros generales
	function GetConfigJS() {
		$response = '';
		$response1 = '';
		$response2 = '';
		$sep = '';
		// Parametros generales
		foreach($this->genPar as $key => $val) {
			$inputType = 'input';
			if(isset($this->genParType[$key])) {
				$inputType = $this->genParType[$key];
			}
			// Removes doculbe-quotes at the begining and the end of $val
			$response1 .= '		$("' . $inputType . '#' . $key . '").val("' . trim($val, '"') . '");' . "\n";
			$response2 .= $sep . '		' . $key . ': $("' . $inputType . '#' . $key . '").val()';
			$sep = ',' . "\n";
		}
		// Parametros privados
		foreach($this->privatePar as $key => $val) {
			$inputType = 'input';
			if(isset($this->privateParType[$key])) {
				if($this->privateParType[$key] === 'EXCLUDE') {
					// No incluye en los fuentes HMTL
					continue;
				}
				$inputType = $this->privateParType[$key];
			}
			$response1 .= '		$("' . $inputType . '#' . $key . '").val("' . trim($val, '"') . '");' . "\n";
		}
		$response = '
<script type="text/javascript">
var yaInicializo = false;
function init_screen() {
	if(yaInicializo == false) {
' . $response1 . 
'		yaInicializo = true;
	}
	$(document).ready(function() {
		initialize_distance_calc();
	});
	return ' . ($this->error == true ? 'false' : 'true') . ';
}

function saveParValues() {
	var ParObj = {
' . $response2 . 
'	}
	AjaxPostGmapConfig(ParObj, "SaveConfig", "Parámetros Generales", function(error, errorMsg) {});
}
</script>
';
		return $response;
	}
	
	function getHeadLinks() {
		/*
		 * Para carga del Popper, JQuery y Bootstrap simplificados, buscar:
		 *
		 * Bootstrap CDN: When you only need to include Bootstrap's compiled CSS or JS, 
		 * you can use the Bootstrap CDN:
		 *
		 * CSS only
		 * <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
		 *
		 * JS, Popper, and jQuery
		 * <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		 * <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		 * <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
		 */
		$response = 
'	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<link rel="stylesheet" href="icons/fontawesome/css/font-awesome.min.css" type="text/css" media="all"></link>
	<script type="text/javascript" src="js/popper.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/ocr-gmap-calc-tar.js"></script>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" ></link>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="all" ></link>
	<!--link rel="stylesheet" href="/css/bootstrap-grid.min.css" type="text/css" media="all" ></link-->
	<!--link rel="stylesheet" href="/css/bootstrap-reboot.min.css" type="text/css" media="all" ></link-->
';
		return $response;
	}
	
	function getGeneralForm() {
		/*
		 * Para la disposición de columnas responsive con col-md-X, ver:
		 * https://getbootstrap.com/docs/4.0/examples/grid/
		 */
		$response = '
	<div class="container-fluid" id="mainForma">
		<div class="row">
			<div class="col-md-12">
				<div id="intro">' .
					($this->titulo === '' ? '' : '<legend>'.$this->titulo.'</legend>') .
					($this->intro === '' ? '' : '<p>'.$this->intro.'</p>') . '
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="row">
					<div class="col-auto">
						<legend>' . $this->genPar['appName'] . '</legend>
					</div>
					<div class="col-auto">
						<a href="' . $this->genPar['appIG'] . '" target="_new" class="btn btn-secondary" aria-label="Instagram">
							<i class="fa fa-instagram" aria-hidden="true"></i>
						</a>
					</div>
					<div class="col-auto">
						<a href="' . $this->genPar['appWeb'] . '" target="_new" class="btn btn-secondary" aria-label="Sitio Web">
							<i class="fa fa-cloud" aria-hidden="true"></i>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" id="response">
						Introduzca el lugar de origen, lugar de destino.<br/>Haga clic en el botón CALCULAR para ver costos de acarreo.
					</div>
				</div>
			</div>
			<div class="col-md-8" id="inputVarZone">
				<form>
					<input type="hidden" id="appName" />
					<input type="hidden" id="gapik" />
					<input type="hidden" id="zoom0" />
					<input type="hidden" id="lat0" />
					<input type="hidden" id="long0" />
					<div class="form-row">
						<div class="col-auto">
							<input type="button" id="mapRecalcButton" value="CALCULAR" class="btn btn-primary" />
						</div>
						<div class="col-auto">
							<input type="button" id="submitButton" value="VER ALTERNATIVAS" class="btn btn-secondary" />
						</div>
						<div class="col-auto">
							<input type="button" id="helpButton" value="AYUDA" class="btn btn-secondary" />
						</div>
						<div class="col-auto">
							<input type="button" id="saveParButton" value="GUARDAR" class="btn btn-secondary" />
						</div>
					</div>
					<div class="form-row">
						&nbsp;
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="TravelMode" class="mr-sm-2">Modalidad:</label>
							<select id="TravelMode" class="custom-select mb-2 mr-sm-2 mb-sm-0" aria-describedby="TravelModeHelp">
								<option value="TRANSIT">[TRANSIT] Direcciones de tránsito</option>
								<option value="DRIVING">[DRIVING] Automóvil</option>
								<option value="WALKING">[WALKING] Caminando</option>
								<option value="BICYCLING">[BICYCLING] Bicicleta</option>
							</select>
							<small id="TravelModeHelp" class="form-text text-muted">Modalidad de viaje: para calcular la ruta en base al tipo de vehículo, servicio o modo del traslado.</small>
						</div>
						<div class="form-group col-md-6">
							<label for="TrafficModel" class="mr-sm-2">Modelo:</label>
							<select id="TrafficModel" class="custom-select mb-2 mr-sm-2 mb-sm-0" aria-describedby="TrafficModelHelp">
								<option value="bestguess">[BEST_GUESS] Mejor estimado de tiempo</option>
								<option value="optimistic">[OPTIMISTIC] Optimista</option>
								<option value="pessimistic">[PESSIMISTIC] Pesimista</option>
							</select>
							<small id="TrafficModelHelp" class="form-text text-muted">Modelo de cálculo de tráfico: para estimar el tiempo del traslado.</small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label for="AvoidHighways" class="mr-sm-2">Evitar autopistas:</label>
							<select id="AvoidHighways" class="custom-select mb-2 mr-sm-2 mb-sm-0" />
								<option value="1">Si</option>
								<option value="0">No</option>
							</select>
						</div>
						<div class="form-group col-md-4">
							<label for="AvoidTolls" class="mr-sm-2">Evitar ruta de cuota:</label>
							<select id="AvoidTolls" class="custom-select mb-2 mr-sm-2 mb-sm-0" />
								<option value="1">Si</option>
								<option value="0">No</option>
							</select>
						</div>
						<div class="form-group col-md-4">
							<label for="N" class="mr-sm-2">Antelación:</label>
							<input type="text" id="N" aria-describedby="nHelp" />
							<small id="nHelp" class="form-text text-muted">Minutos de antelación para cálculo de tráfico ruta.</small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label for="Debug" class="mr-sm-2">Modo de Debug:</label>
							<select id="Debug" class="custom-select mb-2 mr-sm-2 mb-sm-0" />
								<option value="0">No</option>
								<option value="1">Si</option>
							</select>
						</div>
						<div class="form-group col-md-4">
							<label for="tarifaDlrPorKm" class="mr-sm-2">Tarifa:</label>
							<input type="text" id="tarifaDlrPorKm" maxlenght="5" aria-describedby="tarifaDlrPorKmHelp" />
							<small id="tarifaDlrPorKmHelp" class="form-text text-muted">Tarifa en US$ a cobrar por cada Km recorrido.</small>
						</div>
						<div class="form-group col-md-4">
							<label for="cantidadViajes" class="mr-sm-2">Viajes:</label>
							<input type="text" id="cantidadViajes" aria-describedby="cantidadViajesHelp"/><br> 
							<small id="cantidadViajesHelp" class="form-text text-muted">Cantidad de Viajes a realizar.</small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-3">
							<label for="kmPorLitro" class="mr-sm-2">Rendimiento:</label>
							<input type="text" id="kmPorLitro" aria-describedby="kmPorLitroHelp" />
							<small id="kmPorLitroHelp" class="form-text text-muted">Cantidad de Km que rinde cada litro de gasolina.</small>
						</div>
						<div class="form-group col-md-3">
							<label for="costoLitroGasolina" class="mr-sm-2">Costo Gasolina:</label>
							<input type="text" id="costoLitroGasolina" aria-describedby="costoLitroGasolinaHelp" /><br> 
							<small id="costoLitroGasolinaHelp" class="form-text text-muted">Costo en US$ de cada litro de gasolina.</small>
						</div>
						<div class="form-group col-md-3">
							<label for="servicioCadaXkm" class="mr-sm-2">Frecuencia Servicio:</label>
							<input type="text" id="servicioCadaXkm" aria-describedby="servicioCadaXkmHelp" />
							<small id="servicioCadaXkmHelp" class="form-text text-muted">Cantidad de Km para hacer cada servicio de mantenimiento.</small>
						</div>
						<div class="form-group col-md-3">
							<label for="servicioCosto" class="mr-sm-2">Costo Servicio:</label>
							<input type="text" id="servicioCosto" aria-describedby="servicioCostoHelp" />
							<small id="servicioCostoHelp" class="form-text text-muted">Costo en US$ de cada servicio de mantenimiento.</small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="kmMyOfficeToOrigin" class="mr-sm-2">Km al Origen:</label>
							<input type="text" id="kmMyOfficeToOrigin" aria-describedby="kmMyOfficeToOriginHelp" />
							<small id="kmMyOfficeToOriginHelp" class="form-text text-muted">Distancia en Km desde mi oficina hasta el origen.</small>
						</div>
						<div class="form-group col-md-6">
							<label for="kmDestinationToMyOffice" class="mr-sm-2">Km desde Destino:</label>
							<input type="text" id="kmDestinationToMyOffice" aria-describedby="kmDestinationToMyOfficeHelp" /><br> 
							<small id="kmDestinationToMyOfficeHelp" class="form-text text-muted">Distancia en Km del destino hasta mi oficina.</small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="placeName1">Origen:</label>
							<input type="text" class="form-control" id="placeName1" />
						</div>
						<div class="form-group col-md-3">
							<label for="lat1" class="mr-sm-2">Latitud:</label>
							<input type="text" class="form-control" id="lat1" />
						</div>
						<div class="form-group col-md-3">
							<label for="long1" class="mr-sm-2">Longitud:</label>
							<input type="text" class="form-control" id="long1" />
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="placeName2" class="mr-sm-2">Destino:</label>
							<input type="text" class="form-control" id="placeName2" />
						</div>
						<div class="form-group col-md-3">
							<label for="lat2" class="mr-sm-2">Latitud:</label>
							<input type="text" class="form-control" id="lat2" />
						</div>
						<div class="form-group col-md-3">
							<label for="long2" class="mr-sm-2">Longitud:</label>
							<input type="text" class="form-control" id="long2" />
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="placeNameMyOffice" class="mr-sm-2">Mi Oficina:</label>
							<input type="text" class="form-control" id="placeNameMyOffice" />
						</div>
						<div class="form-group col-md-3">
							<label for="latMyOffice" class="mr-sm-2">Latitud:</label>
							<input type="text" class="form-control" id="latMyOffice" />
						</div>
						<div class="form-group col-md-3">
							<label for="longMyOffice" class="mr-sm-2">Longitud:</label>
							<input type="text" class="form-control" id="longMyOffice" />
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="resultZone">
			</div>
		</div>
	</div>
';
		if($this->error == true) {
			$response .= '<script>ApagaMapa();$("div#mainForma").html(\'' . $this->getAlertStyle($this->errorMsg) .'\');</script>
';
		}
		return $response;
	}
	

	/*
	 * Devuelve los valores del resultado de calculos de tarifas 
	 * a cobrar y costos, con base en la distancia total en Km
	 * y la duracion total en minutos
	 */
	function getTarifaValues() {
		$response = array();
		$response['error'] = false;
		$response['errormsg'] = '';
	
		$response['totalDistance'] = $this->defValFromPostNum('totalDistance');
		$response['totalDuration'] = $this->defValFromPostNum('totalDuration');
		$response['tarifaDlrPorKm'] = $this->defValFromPostNum('tarifaDlrPorKm');
		$response['cantidadViajes'] = $this->defValFromPostNum('cantidadViajes');
		$response['kmPorLitro'] = $this->defValFromPostNum('kmPorLitro');
		$response['costoLitroGasolina'] = $this->defValFromPostNum('costoLitroGasolina');
		$response['servicioCadaXkm'] = $this->defValFromPostNum('servicioCadaXkm');
		$response['servicioCosto'] = $this->defValFromPostNum('servicioCosto');
		$response['kmMyOfficeToOrigin'] = $this->defValFromPostNum('kmMyOfficeToOrigin');
		$response['kmDestinationToMyOffice'] = $this->defValFromPostNum('kmDestinationToMyOffice');
		$response['distanciaTotalEnKm'] = $this->distanciaEnKm($response['totalDistance']);
		$response['duracionTotalenMin'] = $this->duracionEnMin($response['totalDuration']);
		
		if(!is_numeric($response['tarifaDlrPorKm'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definida la Tarifa por Km</p>';
		}
		if(!is_numeric($response['cantidadViajes'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido la Cantidad de Viajes</p>';
		}
		if(!is_numeric($response['kmPorLitro'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido los Litros por Km</p>';
		}
		if(!is_numeric($response['costoLitroGasolina'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido el Costo del Litro de gasolina</p>';
		}
		if(!is_numeric($response['servicioCadaXkm'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido el Servicio de Mantenimiento cada X Km</p>';
		}
		if(!is_numeric($response['servicioCosto'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido el Costo por cada Servicio de Mantenimiento</p>';
		}
		if(!is_numeric($response['kmMyOfficeToOrigin'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido los Km desde Mi Oficina hasta el Origen</p>';
		}
		if(!is_numeric($response['kmDestinationToMyOffice'])) {
			$response['error'] = true;
			$response['errormsg'] .= '<p>No está definido el Km desde el Destino hasta Mi Oficina</p>';
		}
		// Si es error, devuelve el mensaje con los errores en rojo!
		if($response['error'] === true) {
			$response['errormsg'] = '<h4 class="alert-heading">ERROR!</h4>' + $response['errormsg'] + '</div>';
			return json_encode($response);
		}
		// Si es mas de 1 viaje, se debe incluir el recorrido de regreso en cada viaje adicional
		$response['cantidadVueltas'] = ($response['cantidadViajes']*2)-1;	
		// Tarifa a cobrar de 1 sólo viaje de ida
		$response['tarifaUnaVia'] = $this->rounder($response['distanciaTotalEnKm']*$response['tarifaDlrPorKm'], 2);
		// Distancia super total, incluyendo ida desde mi oficina+viaje(s)+regreso a mi oficina
		$response['distanciaTotalEnKmConTodo'] = $this->rounder(($response['distanciaTotalEnKm']*$response['cantidadVueltas']) + ($response['kmMyOfficeToOrigin']) + ($response['kmDestinationToMyOffice']), 2);
		$response['totalAcobrar'] = $this->rounder($response['distanciaTotalEnKmConTodo']*$response['tarifaDlrPorKm'], 2);
		// Costos
		$response['totalLitrosGasolina'] = $this->rounder($response['distanciaTotalEnKmConTodo']/$response['kmPorLitro'], 2);
		$response['costoServicioPorCadaKm'] = $this->rounder($response['servicioCosto'] / $response['servicioCadaXkm'], 2);
		$response['costoTotalGasolina'] = $this->rounder($response['totalLitrosGasolina'] * $response['costoLitroGasolina'], 2);
		$response['costoServicioKmsRecorridos'] = $this->rounder($response['costoServicioPorCadaKm']*$response['distanciaTotalEnKmConTodo'], 2);
		$response['totalCostos'] = $this->rounder(($response['costoTotalGasolina'])+($response['costoServicioKmsRecorridos']), 2);
		// Ganancia
		$response['gananciaTotal'] = $this->rounder(($response['totalAcobrar'])-($response['totalCostos']), 2);
		// Retorna todos los valores involucrados en el cálculo
		return json_encode($response);
	}

	function defValFromPostNum($parName, $defValToReturn = null) {
		$response = $this->defValFromPost($parName, $defValToReturn);
		if(!is_null($response)) {
			$response = 0.00+$response;
		}
		return $response;
	}
	
	function defValFromPost($parName, $defValToReturn = null) {
		$response = $defValToReturn;
		if(isset($_POST[$parName])) {
			$response = $_POST[$parName];
		}
		return $response;
	}
	
	/* Convierte de metros a Km redondeado */
	function distanciaEnKm($distanciaTotal) {
		return $this->rounder($distanciaTotal/1000, 2);
	}

	/* Convierte de segundos a minutos redondeado */
	function duracionEnMin($duracionTotal) {
		return $this->rounder($duracionTotal/60, 2);
	}

	function rounder($value, $precision) {
		//return (string) round((float) $value, $precision);
		return (string) round((float) $value, $precision);
    }
	
	function getFileName($fileSpec, $part = 'filename') {
		$path_parts = pathinfo($fileSpec);
		return $path_parts[$part];
	}
	
	function getAlertStyle($msg, $alertType = 'warning') {
		return '<div class="alert alert-' . $alertType . ' text-center" role="alert">' . $msg . '</div>';
	}
}

$configClass = new configClass();
echo $configClass->selectActionFromGet();
?>