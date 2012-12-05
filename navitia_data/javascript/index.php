<html>
<head>
<script src="http://code.jquery.com/jquery.min.js"></script>
<script src="jquery.xdomainajax.js"></script>
<script>
var NETWORK = '1|CTA18|IRIGO';
var lines = null;
var stops_indexed = new Array();
var co = 0;

$(function() {
    call_lines(NETWORK);
	
	$('.line').on('click', function() {
		console.info($(this).attr('id'));
	});
});

// Recupere la liste des lignes
var call_lines = function(network) {
	var url = 'http://angers.prod.navitia.com/Navitia/HP_2_Line.asp';
	var parameters = {
		'NetworkList' : network,
	};
	$.ajax({
		url: url,
		data: parameters,
		type: 'GET',
		dataType: 'html',
		crossDomain: true,
		success: function(dataLines) {
			on_receive_lines(network, dataLines);
		}
	});
};

// parse la liste des lignes
var on_receive_lines = function(network, dataLines) {
	var r = dataLines.responseText;
	lines = new Array();
	$(r).find('option').each(function() {
		var v = $(this).val();
		if (v != '') {
			va = v.split("#");
			va = va[1].split("|");
			var line = {
				'id' : va[0],
				'code' : va[1],
				'ref' : va[2],
				'name' : va[3],
				'to' : va[4],
				'from' : va[5],
				'idType' : va[6],
				'nameType' : va[7],
			}
			lines[lines.length] = line;
		}
	});
	//console.log(lines);
	//print_lines(lines);
	//for (i=0; i < lines.length; i++) {
		call_stops(network, lines[3]);
	//}
};

// Recupere la liste des arrets
var call_stops = function(network, dataLine) {
	var url = 'http://angers.prod.navitia.com/Navitia/HP_3_StopAreaSensDate.asp';
	var parameters = {
		'Network' : network,
		'NetworkList' : network,
		'Line' : dataLine.id + '|' + dataLine.code + '|' + dataLine.ref + '|' + dataLine.name + '|' + dataLine.to + '|' + dataLine.from + '|' + dataLine.idType + '|' + dataLine.nameType,
		'Date' : '2012|12|5',
		'DateFinBases' : '2013|06|30',
		'DateMajBases' : '2012|12|03',
	};
	
	$.ajax({
		url: url,
		data: parameters,
		type: 'GET',
		dataType: 'html',
		crossDomain: true,
		success: function(dataStops) {
			on_receive_stops(network, dataLine, dataStops);	
		}
	});
};

// parse la liste des arrets
var on_receive_stops = function(network, dataLine, dataStops) {
	var r = dataStops.responseText;
	var stops = new Array();
	$(r).find('.selectStopArea option').each(function() {
		var v = $(this).val();
		if (v != '') {
			va = v.split("|");
			var stop = {
				'id' : va[0],
				'code' : va[1],
				'name' : va[2],
				'city' : va[3],
			}
			stops[stops.length] = stop;
			stops_indexed[stop.id] = stop;
		}	
	});
	//co++;
	//if (co == lines.length) print_stops_indexed();
	
	//for (i=0; i < stops.length; i++) {
		call_hours(network, dataLine, stops[0]);
	//}
};

// Recupere la liste des horaires
var call_hours = function (network, dataLine, dataStop){
	var url = 'http://angers.prod.navitia.com/Navitia/HP_4_HP.asp';
	var parameters = {
		'Network' : network,
		//'Line' : '1|CTA73|01|Belle-Beille <> Monplaisir|Monplaisir|Belle Beille|5|Bus',
		//'Line' : '4|CTA74|02|Trélazé <> Banchais / St Sylvain|Banchais / Saint Sylvain|Trélazé|5|Bus',
		//'Line' : '39|CTA72|A|Angers - Roseraie <> Avrillé - Ardenne|Avrillé - Ardenne|Angers - Roseraie|16|Tramway',
		'Line' : dataLine.id + '|' + dataLine.code + '|' + dataLine.ref + '|' + dataLine.name + '|' + dataLine.to + '|' + dataLine.from + '|' + dataLine.idType + '|' + dataLine.nameType,
		'Direction' : '1',
		//'StopArea' : '7|CTA1241|Allonneau|Angers',
		//'StopArea' : '102|CTA82|Foch - Haras|Angers',
		'StopArea' : dataStop.id + '|' + dataStop.code + '|' + dataStop.name + '|' + dataStop.city,
		'Date' : '2012|12|5',
		'NetworkList' : network,
		'DateFinBases' : '2013|06|30',
		'DateMajBases' : '2012|12|03',
	};
	$.ajax({
		url: url,
		data: parameters,
		type: 'GET',
		dataType: 'html',
		crossDomain: true,
		success: function(dataHours) {
			on_receive_hours(network, dataLine, dataStop, dataHours);
		}
	});
	parameters.Direction = '-1';
	$.ajax({
		url: url,
		data: parameters,
		type: 'GET',
		dataType: 'html',
		crossDomain: true,
		success: function(dataHours) {
			on_receive_hours(network, dataLine, dataStop, dataHours);
		}
	});
};

// parse la liste des horaires
var on_receive_hours = function(network, dataLine, dataStop, dataHours) {
	//console.info(dataStop.name);
	var heures = new Array();
	var minutes = new Array();
	var horaires = new Array();
	var r = dataHours.responseText;
	var main_terminus = {'ref' : 0, 'name' : null, times : new Array()};
	var sec_terminus = new Array();
	$(r).find('.hp_dest_principale_description').each(function() {
		main_terminus.name = $(this).text().replace(/^[\s]+/g, '').replace(/[\s]+$/g, '');
		//console.info(main_terminus);
	});
	$(r).find('.hp_dest_secondaire_td').each(function() {
		var sec_term = {'ref' : null, 'name' : null, times : new Array()};
		$(this).find('.hp_dest_secondaire_renvoi').each(function() {
			sec_term.ref = $(this).text().replace(/^[\s]+/g, '').replace(/[\s]+$/g, '').substring(1, 2);
			//console.info('['+sec_term.ref+']');
		});
		$(this).parent().find('.hp_dest_secondaire_description').each(function() {
			sec_term.name = $(this).text().replace(/^[\s]+/g, '').replace(/[\s]+$/g, '').replace(/Terminus\s/i, '');
			//console.info('['+sec_term.name+']');
		});
		sec_term.times = new Array();
		sec_terminus[sec_terminus.length] = sec_term;
	});
	//console.info(sec_terminus);
	// heures
	$(r).find('.heure_paire p, .heure_impaire p').each(function() {
		horaires[heures.length] = new Array();
		heures[heures.length] = $(this).text().substring(0, 2);
	});
	//console.info(heures);
	var i = 0;
	// minutes
	$(r).find('.li_ci, .li_cp, .lp_ci, .lp_cp').each(function() {
		var m = $.trim($(this).text()).replace(/[\r\n\s\)]+/g, '').replace(/[\(]+(?=[^\(])/g, '-');
		minutes[minutes.length] = m;
		if (m != '') {
			//console.info(m);
			horaires[i][horaires[i].length] = heures[i] + ':' + m;
		}
		i++;
		if (i >= heures.length) i = 0;
	});
	// tri
	horairesTriees = new Array();
	for (i=0; i < horaires.length; i++) {
		for (j=0; j < horaires[i].length; j++) {
			horairesTriees[horairesTriees.length] = horaires[i][j];
		}
	}
	// classement
	for (i=0; i < horairesTriees.length; i++) {
		if (horairesTriees[i].length > 5) {
			var h = horairesTriees[i].split('-');
			for (j=0; j < sec_terminus.length; j++) {
				if (sec_terminus[j].ref == h[1]) {
					sec_terminus[j].times[sec_terminus[j].times.length] = h[0];
				}
			}
		} else {
			main_terminus.times[main_terminus.times.length] = horairesTriees[i];
		}
	}
	console.info(main_terminus);
	console.info(sec_terminus);
};

var print_lines = function(lines) {
	$('#lines').append('<div>');
	$('#lines').append('route_id,agency_id,route_short_name,route_long_name,route_type');//,route_color');
	$('#lines').append('</div>');
	for (i=0; i < lines.length; i++) {
		$('#lines').append('<div class="line" id="line-'+i+'">');
		$('#lines').append(lines[i].id + ',1,');
		$('#lines').append(lines[i].ref + ',');
		$('#lines').append(lines[i].name + ',');
		var route_type = '3';
		if (lines[i].idType == '16') route_type = '1';
		if (lines[i].idType == '5') route_type = '3';
		if (lines[i].idType == '11') route_type = '5';
		$('#lines').append(route_type);
		//$('#lines').append(',#cccccc');
		$('#lines').append('</div>');
	}
};

var print_stops = function(stops) {
	$('#stops').append('<div>');
	$('#stops').append('stop_id,stop_code,stop_name,stop_lat,stop_long');
	$('#stops').append('</div>');
	for (i=0; i < stops.length; i++) {
		$('#stops').append('<div class="stop" id="stop-'+i+'">');
		$('#stops').append(stops[i].id + ',');
		$('#stops').append(stops[i].code + ',');
		$('#stops').append(stops[i].name + ',,');
		$('#stops').append('</div>');
	}
};

var print_stops_indexed = function() {
	$('#stops').append('<div>');
	$('#stops').append('stop_id,stop_code,stop_name,stop_lat,stop_long');
	$('#stops').append('</div>');
	for (var i in stops_indexed) {
		$('#stops').append('<div class="stop" id="stop-'+i+'">');
		$('#stops').append(stops_indexed[i].id + ',');
		$('#stops').append(stops_indexed[i].code + ',');
		$('#stops').append(stops_indexed[i].name + ',,');
		$('#stops').append('</div>');
	}
};
</script>
</head>
<body>
<div id="lines"></div>
<div id="stops"></div>
</body>
</html>