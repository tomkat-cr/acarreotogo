/*
 * ocr-gmap-calc-tar.js
 * Calculo de tarifa de acarreo del App OCR-Acarreo
 */

/* https://stackoverflow.com/questions/456646/jquery-print-r-display-equivalent */
var repeat = function(str, count) {
	var array = [];
	for(var i = 0; i < count;)
		array[i++] = str;
	return array.join('');
}

var offsetCalculando = repeat('<br/>', 5);
var constCalculando = offsetCalculando+'Calculando...';
var ajaxGmCnfUrl = 'ocr-gmap-config.php';
var errorInPage;

function initialize_distance_calc() {
	toggleBotonAyuda();
	$('input#helpButton').click(function() {
		toggleBotonAyuda();
	});
	$('input#submitButton').click(function() {
		resultadoBotonVerAlternativas();
	});
	$('input#saveParButton').click(function() {
		saveParValues();
	});
	/*
	$('input#gapiKeyButton').click(function() {
		procesaGapiKeyButton();
	});
	*/
	pideGapiKeyButton();
}

var varToggleBotonAyuda = '1';
function toggleBotonAyuda() {
	if(varToggleBotonAyuda == '0') {
		varToggleBotonAyuda = '1';
	} else {
		varToggleBotonAyuda = '0';
	}
	if(varToggleBotonAyuda == '0') {
		$('input#helpButton').val('AYUDA');
		$('small#TravelModeHelp').hide();
		$('small#TrafficModelHelp').hide();
		$('small#nHelp').hide();
		$('small#tarifaDlrPorKmHelp').hide();
		$('small#cantidadViajesHelp').hide();
		$('small#kmPorLitroHelp').hide();
		$('small#costoLitroGasolinaHelp').hide();
		$('small#servicioCadaXkmHelp').hide();
		$('small#servicioCostoHelp').hide();
		$('small#kmMyOfficeToOriginHelp').hide();
		$('small#kmDestinationToMyOfficeHelp').hide();
	} else {
		$('input#helpButton').val('Apagar AYUDA');
		$('small#TravelModeHelp').show();
		$('small#TravelModeHelp').show();
		$('small#TrafficModelHelp').show();
		$('small#nHelp').show();
		$('small#tarifaDlrPorKmHelp').show();
		$('small#cantidadViajesHelp').show();
		$('small#kmPorLitroHelp').show();
		$('small#costoLitroGasolinaHelp').show();
		$('small#servicioCadaXkmHelp').show();
		$('small#servicioCostoHelp').show();
		$('small#kmMyOfficeToOriginHelp').show();
		$('small#kmDestinationToMyOfficeHelp').show();
	}
}

function pideGapiKeyButton() {
	var inputGapik = $('input#gapik').val();
	if(typeof inputGapik == 'undefined' || inputGapik === '') {
		ApagaMapa();
		var boton = document.getElementById('gapiKeyButton');
		boton.addEventListener('click', procesaGapiKeyButton);
		$('div#floating-get-gapi').show();
	}
}

function ApagaMapa() {
	if(errorInPage == false) {
		$('div#mainForma').hide();
	}
	$('div#floating-panel').hide();
	$('div#map').hide();
}

// https://stackoverflow.com/questions/4428915/how-do-i-catch-an-invalid-api-key-for-google-maps
//function gm_authFailure_123() { 
function gm_authFailure() { 
	// Hay errores con el API Key
	$('input#gapik').val('');
	$('div#gapi-response').html(getAlertStyle('ERROR: El Google API Key no es válido'));
	pideGapiKeyButton();
}

function procesaGapiKeyButton() {
	// Example starter JavaScript for disabling form submissions if there are invalid fields
	// https://getbootstrap.com/docs/4.0/components/forms/
	var AlertType = 'alert';
    var form = document.getElementById("needs-validation");
	if (form.checkValidity() == false) {
		msg = 'Por favor especifique datos obligatorios';
		$('div#gapi-response').html(getAlertStyle(msg, AlertType));
		return;
	}
	var ParObj = { gapik: $('input#gapiKey').val() };
	AjaxPostGmapConfig(ParObj, "SavePrivConfig", "Parámetros Privados", function(error, errorMsg) {
		var msg = '';
		if(error == true) {
			msg = errorMsg+'.<br/>Intenta de nuevo por favor';
			$('div#gapi-response').html(getAlertStyle(msg, AlertType));
		} else {
			AlertType = 'success';
			msg = 'Por favor refresca la pantalla para tomar el nuevo API Key';
			$('div#gapiKeyTitleZone').hide();
			$('div#gapiKeyInputZone').hide();
			$('div#gapi-response').html(getAlertStyle(msg, AlertType));
			location.reload();
		}
	});
}

function getAlertStyle(msg, alertType) {
	if(typeof alertType == 'undefined' || alertType == '') {
		alertType = 'warning';
	}
	return '<div class="alert alert-' + alertType + ' text-center" role="alert">' + msg + '</div>';
}

function AjaxPostGmapConfig(ParObj, gmcOption, gmcTitle, callBakFunc) {
	jsonString = JSON.stringify(ParObj);
	var url = ajaxGmCnfUrl;
	var error = false;
	var errorMsg = '';
	var request = $.ajax({
		method: "POST",
		url: url,
		data: { m: gmcOption, jsonString: jsonString }
	});
	request.done(function( msg ) {
		alert(gmcTitle+" guardados: "+msg);
	})
	request.fail(function( jqXHR, textStatus ) {
		error = true;
		errorMsg = "Error guardando "+gmcTitle+": "+textStatus;
		alert(errorMsg);
	});
	callBakFunc(error, errorMsg);
}

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
 * Matriz de distancia
 * https://developers.google.com/maps/documentation/javascript/distancematrix?hl=Es#distance_matrix_parsing_the_results
 * El servicio de matriz de distancia de Google computa la distancia 
 * y la duración de viajes entre varios orígenes y destinos según 
 * determinados modos de viaje.
 * Este servicio no devuelve información detallada sobre rutas. La información 
 * sobre rutas, incluidas las polilíneas y las indicaciones textuales, puede 
 * obtenerse pasando el origen y el destino deseados al "servicio de indicaciones":
 * https://developers.google.com/maps/documentation/javascript/directions?hl=Es
 */
function getDistance(lat1, long1, placeName1, lat2, long2, placeName2, varTravelMode, varTrafficModel, varAvoidHighways, varAvoidTolls, N, llamadaPostProcesamiento) {
	
//alert('getDistance(' + lat1 + ', ' + long1 + ', ' + placeName1 + ', ' + lat2 + ', ' + long2 + ', ' + placeName2 + ', ' + varTravelMode + ', ' + varTrafficModel + ', ' + varAvoidHighways + ', ' + varAvoidTolls + ', ' + N + ')');
	/*
	var origin1 = new google.maps.LatLng(55.930385, -3.118425);
	var origin2 = 'Greenwich, England';
	var destinationA = 'Stockholm, Sweden';
	var destinationB = new google.maps.LatLng(50.087692, 14.421150);
	*/
	var origin1 = new google.maps.LatLng(lat1, long1);
	var origin2 = placeName1;
	var destinationA = placeName2;
	var destinationB = new google.maps.LatLng(lat2, long2);
	/*
	https://developers.google.com/maps/documentation/javascript/3.exp/reference?hl=Es#DrivingOptions
	var varTravelMode = 'DRIVING';
		BICYCLING	Specifies a bicycling directions request.
		DRIVING	Specifies a driving directions request.
		TRANSIT	Specifies a transit directions request.
		WALKING	Specifies a walking directions request.				
	var varTrafficModel = 'optimistic';
		BEST_GUESS	Use historical traffic data to best estimate the time spent in traffic.
		OPTIMISTIC	Use historical traffic data to make an optimistic estimate of what the duration in traffic will be.
		PESSIMISTIC	Use historical traffic data to make a pessimistic estimate of what the duration in traffic will be.
	var varAvoidHighways = '1';
	var varAvoidTolls = '1';
	*/
	var varDrivingOptions = {
			departureTime: new Date(Date.now() + (N*60000)),  // for the time N milliseconds from now.
			trafficModel: varTrafficModel
		};
	var varTransitOptions = {
			/*
			arrivalTime: Date,
			departureTime: Date,
			modes: [transitMode1, transitMode2]
			routingPreference: TransitRoutePreference
			*/
		};
	var service = new google.maps.DistanceMatrixService();
	service.getDistanceMatrix(
	  {
		origins: [origin1, origin2],
		destinations: [destinationA, destinationB],
		travelMode: varTravelMode,
		transitOptions: varTransitOptions,
		drivingOptions: varDrivingOptions,
		//unitSystem: google.maps.UnitSystem.METRIC /* predeterminado */,
		avoidHighways: (varAvoidHighways == '1'),
		avoidTolls: (varAvoidTolls == '1'),
	  }, callbackGetDistance);

	// See Parsing the Results for
	// the basics of a callback function.
	function callbackGetDistance(response, status) {
		var routeOptions = [];
		var rpNdx = -1;
		var error=false;
		var errorMsg='';
		//var result = '';
		//var distanciaTotal = 0;
		//var duracionTotal = 0;
		if (status == 'OK') {
			var origins = response.originAddresses;
			var destinations = response.destinationAddresses;
			for (var i = 0; i < origins.length; i++) {
				var results = response.rows[i].elements;
				for (var j = 0; j < results.length; j++) {
					var element = results[j];
					if(element.status == 'OK') {
						var distance = element.distance;
						var duration = element.duration;
						var from = origins[i];
						var to = destinations[j];
						//distanciaTotal += parseFloat(distance.value);
						//duracionTotal += parseFloat(duration.value);
						routeOptions[++rpNdx] = {placeNameFrom:from, placeNameTo:to, distanceText:distance.text, distanceValue:parseFloat(distance.value), durationText:duration.text, durationValue:parseFloat(duration.value)};
						/*
						result += '<br/>Opción # ' + (i+1) + '.' + (j+1) + ': ';
						result += '<br/>Desde: ' + from + ' | Hasta: ' + to + ' | Distancia: ' + distance.text + ' | Duración: ' + duration.text + '<br/>';
						result += getTarifaText(parseFloat(distance.value), parseFloat(duration.value));
						*/
					}
				}
			}
		} else {
			//result = 'Error en el resultado: ' + status;
			error=true;
			errorMsg='Error en el resultado: ' + status;
		}
		//$('div#resultZone').html('<H2>POSIBLES VIAS</H2>' + result);
		var result = {routeOptions:routeOptions, error:error, errorMsg:errorMsg};
		if($('input#callbackGetDistance').length) {
			$('input#callbackGetDistance').val('');
		} else {
			$("body").append('<INPUT type="hidden" id="callbackGetDistance">');
		}
		$('input#callbackGetDistance').val( JSON.stringify(result) );
		
		llamadaPostProcesamiento(result);
	}
}

function resultadoBotonVerAlternativas() {
	// https://stackoverflow.com/questions/8271836/isnan-vs-parseFloat-confusion
	if( isNaN(parseFloat($('input#lat1').val())) || isNaN(parseFloat($('input#long1').val())) || isNaN(parseFloat($('input#lat2').val())) || isNaN(parseFloat($('input#long2').val()))) {
		alert('No muestra mapas de origen y destino porque alguna de las coordenadas NO es numerica');
		return;
	}
	
	//execute the js function here
	var resultado = getDistance( parseFloat($('input#lat1').val()), parseFloat($('input#long1').val()), $('input#placeName1').val(), parseFloat($('input#lat2').val()), parseFloat($('input#long2').val()), $('input#placeName2').val(), $('select#TravelMode').val(), $('select#TrafficModel').val(), $('select#AvoidHighways').val(), $('select#AvoidTolls').val(), parseFloat($('input#N').val()), function( resultado ) {
			$('div#resultZone').html( print_r(resultado) );
		}
	);

	//alert('Muestra mapas porque todas las coordenadas son numericas');
	//dibuja_map($('input#lat1').val(), $('input#long1').val(), 'mapOrigen', 'mapOrigenTitulo', 'Mapa de Origen');
	//dibuja_map($('input#lat2').val(), $('input#long2').val(), 'mapDestino', 'mapDestinoTitulo', 'Mapa de Destino');
}

/* Callback del mapa principal */
function initMap() {
	errorInPage = (! init_screen());
	if(errorInPage == true) {
		return;
	}
	var directionsService = new google.maps.DirectionsService;
	var directionsDisplay = new google.maps.DirectionsRenderer;
	var zoom0 = parseFloat(document.getElementById('zoom0').value);
	var lat0 = parseFloat(document.getElementById('lat0').value);
	var long0 = parseFloat(document.getElementById('long0').value);
	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: zoom0,
		center: {lat: lat0, lng: long0 }
	});
/*	
	element.addEventListener("DOMNodeRemoved", function(e){
	   if (e.target === element){
			//your code here
			gm_authFailure_123();
			element.removeEventListener("DOMNodeRemoved", mapWasRemovedHandler, true);
	   }
	 }, false);
*/	 
	directionsDisplay.setMap(map);
	var onChangeHandler = function() {
		putTarifaDiv(constCalculando, 'response', 'resultZone');
		calculateRoute('start', 'end', directionsService, null, function(response, response1) {
			displayRoute(directionsDisplay, response); // Del origen al destino
			calculateRoute('placeNameMyOffice', 'start', directionsService, response, function(response, response1) {
				var routeVal = getRouteParams(response);
				varKmMyOfficeToOrigin = distanciaEnKm(routeVal.totalDistance);
					$('input#kmMyOfficeToOrigin').val(varKmMyOfficeToOrigin);
				//displayRoute(directionsDisplay, response);	// de la oficina al origen
				calculateRoute('end', 'placeNameMyOffice', directionsService, response1, function(response, response1) {
					var routeVal = getRouteParams(response);
					varKmDestinationToMyOffice = distanciaEnKm(routeVal.totalDistance);
					$('input#kmDestinationToMyOffice').val(varKmDestinationToMyOffice);
					//displayRoute(directionsDisplay, response);	// Del destino a la oficina
					displayTarifa(response1);
				});
			});
		});
	};
	document.getElementById('start').addEventListener('change', onChangeHandler);
	document.getElementById('end').addEventListener('change', onChangeHandler);
	document.getElementById('calcButton').addEventListener('click', onChangeHandler);
	document.getElementById('mapRecalcButton').addEventListener('click', onChangeHandler);
}

/* Muestra en el mapa la ruta de un origen a un destino */
function displayRoute(directionsDisplay, response) {
	directionsDisplay.setDirections(response);
}

/* Calcula ruta de un origen a un destino */
function calculateRoute(inputStartName, inputEndName, directionsService, response1, responseCustomProcess) {
	var varDrivingOptions = {
		departureTime: new Date(Date.now() + (document.getElementById('N').value*60000)),  // for the time N milliseconds from now.
		trafficModel: document.getElementById('TrafficModel').value
	};
	var varTransitOptions = {
		/*
		arrivalTime: Date,
		departureTime: Date,
		modes: [transitMode1, transitMode2]
		routingPreference: TransitRoutePreference
		*/
	};
	directionsService.route({
			origin: document.getElementById(inputStartName).value,
			destination: document.getElementById(inputEndName).value,
			transitOptions: varTransitOptions,
			drivingOptions: varDrivingOptions,
			avoidHighways: (document.getElementById('AvoidHighways').value == '1'),
			avoidTolls: (document.getElementById('AvoidTolls').value == '1'),
			travelMode: document.getElementById('TravelMode').value
		}, function(response, status) {
			if (status === 'OK') {
				responseCustomProcess(response, response1);
			} else {
				errmsg = 'Directions request for "'+document.getElementById(inputStartName).value+'" to "'+document.getElementById(inputEndName).value+'" failed due to ' + status;
				window.alert(errmsg);
				putTarifaDiv(offsetCalculando+errmsg, 'response', 'resultZone');

			}
		});
}

/*
 * Extrae datos del objeto de respuesta de DirectionsService.route 
 * Muestra el resultado de los calculos de tarifas a cobrar y costos
 * en div#response
 */
function displayTarifa(response) {
	// El parametro "response" es un objeto tipo respuesta de "google.maps.DirectionsService.route()"
	var routeVal = getRouteParams(response);
	$('input#placeName1').val(routeVal.placeName1);
	$('input#lat1').val(routeVal.lat1);
	$('input#long1').val(routeVal.long1);
	$('input#placeName2').val(routeVal.placeName2);
	$('input#lat2').val(routeVal.lat2);
	$('input#long2').val(routeVal.long2);
	getTarifaText(routeVal.totalDistance, routeVal.totalDuration, function(TarifaTextResponse) {
		putTarifaDiv(TarifaTextResponse, 'response', 'resultZone');
	});
}

/*
 * Extrae del objeto de respuesta de DirectionsService.route 
 * los datos de origen y destino (nombre de lugares, longitud y latitud), 
 * distancia total a recorer y tiempo estimado del viaje.
 */
function getRouteParams(response) {
	// El parametro "response" es un objeto tipo respuesta de "google.maps.DirectionsService.route()"
	var responseToSend = '';
	var totalDistance = 0;
	var totalDuration = 0;
	var yaAsignoInputs = false;
	var placeName1 = '', placeName2 = '';
	var lat1 = 0, long1 = 0, lat2 = 0, long2 = 0;
	var leg;
	for (var i = 0; i < response.routes.length; i++) {
		leg = response.routes[i].legs;
		for (var j = 0; j < leg.length; j++) {
			totalDistance += leg[j].distance.value;
			totalDuration += leg[j].duration.value;
			if($("input#Debug").val() === '1') {
				responseToSend += (j+1) + ') Distancia: ' + leg[j].distance.text;
				responseToSend += ' | Tiempo: ' + leg[j].duration.text;
				responseToSend += '<br/>Origen: ' + leg[j].start_address + ' | ' + leg[j].start_location.lat() + ' --- ' + leg[j].start_location.lng();
				responseToSend += '<br/>Destino: ' + leg[j].end_address + ' | ' + leg[j].end_location.lat() + ' -- ' + leg[j].end_location.lng();
				responseToSend += '<br/>';
			}
			if(yaAsignoInputs == false) {
				placeName1 = leg[j].start_address;
				lat1 = leg[j].start_location.lat();
				long1 = leg[j].start_location.lng();
				placeName2 = leg[j].end_address;
				lat2 = leg[j].end_location.lat();
				long2 = leg[j].end_location.lng();
				yaAsignoInputs = true;
			}
		}
	}
	if($("input#Debug").val() === '1') {
		responseToSend += '<br/><br/>' + print_r(response);
	}
	return {
		responseToSend: responseToSend,
		totalDistance: totalDistance,
		totalDuration: totalDuration,
		placeName1: placeName1, 
		placeName2: placeName2,
		lat1: lat1, 
		long1: long1, 
		lat2: lat2, 
		long2: long2
	};
}

/*
 * Devuelve contenido completo de la division donde se muestra el resultado
 * de calculo de la tarifa de un recorrido específico
 */
function putTarifaDiv(responsePar, responseDivName, cleanDivName) {
	responseToSend = '<div class="row"><legend>CÁLCULO DE DISTANCIA Y TARIFA</legend></div>';
	responseToSend += '<div class="row">' + responsePar + '</div>';
	$('div#'+responseDivName).html(responseToSend);
	$('div#'+cleanDivName).html('');
}

/*
 * Devuelve textos con el resultado de calculo de la tarifa 
 * de un recorrido específico
 */
function getTarifaText(totalDistance, totalDuration, callBackFunTarifaText) {
	var result = '';
	var TarVal = getTarifaValues(totalDistance, totalDuration, function(TarVal) {
		var result = '';
		if(TarVal.error === true) {
			result = '<h4 class="alert-heading">ERROR!</h4>' + TarVal.errormsg + '</div>';
		} else {
			// Construye el texto que se va a devolver
			//result += 'Distancia Total: ' + TarVal.distanciaTotalEnKm + ' Km<br/>';
			//result += 'Tiempo Total: ' + TarVal.duracionTotalenMin + ' Min.<br/><br/>';
			result += 'Una sola vía:<br/>';
			result += 'Distancia Total: ' + TarVal.distanciaTotalEnKm + ' Km<br/>';
			result += 'Duración Total: ' + TarVal.duracionTotalenMin + ' min.<br/>';
			result += 'TARIFA: US$ ' + TarVal.tarifaUnaVia + '<br/>';
			result += '<br/>Ida y vuelta:<br/>';
			result += 'Distancia Total: ' + (TarVal.distanciaTotalEnKm*2) + ' Km<br/>';
			result += 'Duración Total: ' + (TarVal.duracionTotalenMin*2) + ' min.<br/>';
			result += 'TARIFA: US$ ' + (TarVal.tarifaUnaVia * 2).toFixed(2) + '<br/>';
			result += '<br/><h3>ACARREO Comercial de '+TarVal.cantidadViajes+' viaje(s)</h3>';
			result += 'Distancia Total: ' + TarVal.distanciaTotalEnKmConTodo + ' Km | '+TarVal.cantidadVueltas+' vuelta(s)<br/>';
			result += 'Duración Total: ' + (TarVal.duracionTotalenMin*TarVal.cantidadVueltas).toFixed(2) + ' min.<br/>';
			result += '<h2>TARIFA: US$ ' + TarVal.totalAcobrar + '</h2>';
			result += '<br/>COSTOS:<br/>';
			result += 'Costo total gasolina: US$ ' + TarVal.costoTotalGasolina + '<br/>';
			result += 'Costo prorrateado Mantenimiento: US$ ' + TarVal.costoServicioKmsRecorridos + '<br/>';
			result += 'Costos totales: US$ ' + TarVal.totalCostos + '<br/>';
			result += '<br/>GANANCIA:<br/>';
			result += 'Total: US$ ' + TarVal.gananciaTotal + '<br/>';
		}
		callBackFunTarifaText(result);
	});
}

/*
 * Devuelve los valores del resultado de calculos de tarifas 
 * a cobrar y costos, con base en la distancia total en Km
 * y la duracion total en minutos
 */
function getTarifaValues(totalDistance, totalDuration, callBackFunct) {
	var distanciaTotalEnKm = distanciaEnKm(totalDistance);
	var duracionTotalenMin = duracionEnMin(totalDuration);
	var tarifaDlrPorKm = parseFloat($('input#tarifaDlrPorKm').val());
	var cantidadViajes = parseInt($("input#cantidadViajes").val());
	var kmPorLitro = parseFloat($("input#kmPorLitro").val());
	var costoLitroGasolina = parseFloat($("input#costoLitroGasolina").val());
	var servicioCadaXkm = parseFloat($("input#servicioCadaXkm").val());
	var servicioCosto = parseFloat($("input#servicioCosto").val());
	var kmMyOfficeToOrigin = parseFloat($("input#kmMyOfficeToOrigin").val());
	var kmDestinationToMyOffice = parseFloat($("input#kmDestinationToMyOffice").val());
	var request = $.ajax({
		method: "POST",
		url: ajaxGmCnfUrl,
		dataType: "json",
		data: { 
			m: 'getTarifaValues',
			totalDistance: totalDistance,
			totalDuration: totalDuration,
			tarifaDlrPorKm: tarifaDlrPorKm,
			cantidadViajes: cantidadViajes,
			kmPorLitro: kmPorLitro,
			costoLitroGasolina: costoLitroGasolina,
			servicioCadaXkm: servicioCadaXkm,
			servicioCosto: servicioCosto,
			kmMyOfficeToOrigin: kmMyOfficeToOrigin,
			kmDestinationToMyOffice: kmDestinationToMyOffice
		}
	});
	request.done(function( data ) {
		callBackFunct(data);
	})
	request.fail(function( jqXHR, textStatus ) {
		var data = {error: true, errorMsg: textStatus}
		callBackFunct(data);
	});
/*
	var error = false;
	var result = '';
	var distanciaTotalEnKm = distanciaEnKm(totalDistance);
	var duracionTotalenMin = duracionEnMin(totalDuration);
	var tarifaDlrPorKm = parseFloat($('input#tarifaDlrPorKm').val());
	var cantidadViajes = parseInt($("input#cantidadViajes").val());
	var kmPorLitro = parseFloat($("input#kmPorLitro").val());
	var costoLitroGasolina = parseFloat($("input#costoLitroGasolina").val());
	var servicioCadaXkm = parseFloat($("input#servicioCadaXkm").val());
	var servicioCosto = parseFloat($("input#servicioCosto").val());
	var kmMyOfficeToOrigin = parseFloat($("input#kmMyOfficeToOrigin").val());
	var kmDestinationToMyOffice = parseFloat($("input#kmDestinationToMyOffice").val());
	if(isNaN(tarifaDlrPorKm)) {
		error = true;
		result += '<p>No está definida la Tarifa por Km</p>';
	}
	if(isNaN(cantidadViajes)) {
		error = true;
		result += '<p>No está definido la Cantidad de Viajes</p>';
	}
	if(isNaN(kmPorLitro)) {
		error = true;
		result += '<p>No está definido los Litros por Km</p>';
	}
	if(isNaN(costoLitroGasolina)) {
		error = true;
		result += '<p>No está definido el Costo del Litro de gasolina</p>';
	}
	if(isNaN(servicioCadaXkm)) {
		error = true;
		result += '<p>No está definido el Servicio de Mantenimiento cada X Km</p>';
	}
	if(isNaN(servicioCosto)) {
		error = true;
		result += '<p>No está definido el Costo por cada Servicio de Mantenimiento</p>';
	}
	if(isNaN(kmMyOfficeToOrigin)) {
		error = true;
		result += '<p>No está definido los Km desde Mi Oficina hasta el Origen</p>';
	}
	if(isNaN(kmDestinationToMyOffice)) {
		error = true;
		result += '<p>No está definido el Km desde el Destino hasta Mi Oficina</p>';
	}
	// Si es error, devuelve el mensaje con los errores en rojo!
	if(error === true) {
		result = '<h4 class="alert-heading">ERROR!</h4>' + result + '</div>';
		return {error: error, errormsg: result};
	}
	// Si es mas de 1 viaje, se debe incluir el recorrido de regreso en cada viaje adicional
	var cantidadVueltas = (cantidadViajes*2)-1;	
	// Tarifa a cobrar de 1 sólo viaje de ida
	var tarifaUnaVia = (distanciaTotalEnKm * tarifaDlrPorKm).toFixed(2);
	// Distancia super total, incluyendo ida desde mi oficina+viaje(s)+regreso a mi oficina
	var distanciaTotalEnKmConTodo = ((distanciaTotalEnKm*cantidadVueltas) + parseFloat(kmMyOfficeToOrigin) + parseFloat(kmDestinationToMyOffice)).toFixed(2);
	var totalAcobrar = (distanciaTotalEnKmConTodo * tarifaDlrPorKm).toFixed(2);
	// Costos
	var totalLitrosGasolina = (distanciaTotalEnKmConTodo / kmPorLitro).toFixed(2);
	var costoServicioPorCadaKm = (servicioCosto / servicioCadaXkm).toFixed(2);
	var costoTotalGasolina = (totalLitrosGasolina * costoLitroGasolina).toFixed(2);
	var costoServicioKmsRecorridos = (costoServicioPorCadaKm*distanciaTotalEnKmConTodo).toFixed(2);
	var totalCostos = (parseFloat(costoTotalGasolina)+parseFloat(costoServicioKmsRecorridos)).toFixed(2);
	// Ganancia
	var gananciaTotal = (parseFloat(totalAcobrar)-parseFloat(totalCostos)).toFixed(2);
	return {
		error: error, 
		distanciaTotalEnKm: distanciaTotalEnKm,
		duracionTotalenMin: duracionTotalenMin,
		cantidadViajes: cantidadViajes,
		cantidadVueltas: cantidadVueltas,
		tarifaUnaVia: tarifaUnaVia,
		distanciaTotalEnKmConTodo:distanciaTotalEnKmConTodo,
		totalAcobrar: totalAcobrar,
		totalLitrosGasolina: totalLitrosGasolina,
		costoServicioPorCadaKm: costoServicioPorCadaKm,
		costoTotalGasolina: costoTotalGasolina,
		costoServicioKmsRecorridos: costoServicioKmsRecorridos,
		totalCostos: totalCostos,
		gananciaTotal: gananciaTotal
	}
*/
}

/* Funciones de libreria general */
	  
/* https://stackoverflow.com/questions/456646/jquery-print-r-display-equivalent */
function print_r(myobject) {
	return print_r2(myobject, 0, '');
}

function print_r2(myobject, level, prefix) {
	var result = '';
	$.each(myobject, function(key, element) {
		if(jQuery.type(element) === 'function') {
			return;
		}
		result += (repeat('&nbsp;', level*2) + prefix + '[' + key + '] &lt;' + jQuery.type(element) + '&gt; = ' + element + '<br>\n');
		if(jQuery.type(element) === 'array' || jQuery.type(element) === 'object') {
			result += print_r2(element, level+1, prefix+'['+key+']');
		}
	});
	return result;
}

/* Convierte de metros a Km redondeado */
function distanciaEnKm(distanciaTotal) {
	return (distanciaTotal / 1000).toFixed(2);
}

/* Convierte de segundos a minutos redondeado */
function duracionEnMin(duracionTotal) {
	return (duracionTotal/60).toFixed(2);
}

/* --------------------------------- */

/*
	var url = ajaxGmCnfUrl+'?m=SaveConfig';
	//var url = ajaxGmCnfUrl+'?m=GetConfig';
	//console.log( "Ajax URL="+url );
	$.ajax({
		//async: false,
		//dataType: "json",
		url: url
	})
	.done(function( data ) {
		alert(data);
	})
	.fail(function( data ) {
		alert( "Error guardando parámetros: " + data );
	});
	/*
	// o bien...
		$.getJSON( url, function( data ) {
			//alert('inicia valores de input');
	//		...
		});
*/

//callbackGetDistanceResponse = getJsonFromInput( 'callbackGetDistance' );
function getJsonFromInput( inputFieldName ) {
	var jsonResult = $('input#'+inputFieldName).val();
	var response;
	try {
		response = $.parseJSON(jsonResult);
	}
	catch (err) {
		jsonResult = [ "unReady" ]
		response = $.parseJSON(jsonResult);
	}
//$('div#resultZone').hide();
//$('div#resultZone').html('');
//$('div#resultZone').html(response);
//$('div#resultZone').show();
	return response;
}
