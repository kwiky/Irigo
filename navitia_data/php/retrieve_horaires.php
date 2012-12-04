<?php 
/*
== Horaires ==
URL : http://angers.prod.navitia.com/Navitia/HP_4_HP.asp
Parametres GET :
Network:1|CTA18|IRIGO
Line:1|CTA73|01|Belle-Beille <> Monplaisir|Monplaisir|Belle Beille|5|Bus
Direction:1
StopArea:7|CTA1241|Allonneau|Angers
Date:2012|12|3
NetworkList:1|CTA18|IRIGO
DateFinBases:2013|06|30
DateMajBases:2012|12|03
*/
$url = 'http://angers.prod.navitia.com/Navitia/HP_4_HP.asp?';

$parameters = array(
	'Network' => '1|CTA18|IRIGO',
	//'Line' => '1|CTA73|01|Belle-Beille <> Monplaisir|Monplaisir|Belle Beille|5|Bus',
	'Line' => '4|CTA74|02|Trélazé <> Banchais / St Sylvain|Banchais / Saint Sylvain|Trélazé|5|Bus',
	'Direction' => '1',
	//'StopArea' => '7|CTA1241|Allonneau|Angers',
	'StopArea' => '102|CTA82|Foch - Haras|Angers',
	'Date' => '2012|12|3',
	'NetworkList' => '1|CTA18|IRIGO',
	'DateFinBases' => '2013|06|30',
	'DateMajBases' => '2012|12|03',
);

foreach ($parameters as $key => $value) {
	if ($key != 'Network') $url .= '&';
	$url .= $key . '=' . urlencode($value);
}

// TODO : Bouchon (à retirer)
// $url = 'arrets-complexe.html';
// $url = 'arrets.html';

$page = file_get_contents($url);

$lignes = explode("\n", $page);
$stops = array();
$h = array();
$terminus = array();
$i = 0;

foreach ($lignes as $ligne) {
	if (strpos($ligne, "heure_paire") !== false || strpos($ligne, "heure_impaire") !== false) {
		$h[$i] = substr($ligne, -9, 2);
		$i++;
	}	
	if (strpos($ligne, "li_cp") !== false || strpos($ligne, "li_ci") !== false || strpos($ligne, "lp_cp") !== false || strpos($ligne, "lp_ci") !== false) {
		if (strpos($ligne, "hp_dest_secondaire_renvoi") === false) {
			$m = substr($ligne, -8, 2);
			if (strpos($m, ">") === false) {
				//$heures[$i]['m'][] = $m;
				$stops[0][] = $h[$i] . ':' . $m;
			}
		} else {
			$m = substr($ligne, -58, 2);
			$t = substr($ligne, -15, 1);
			//$heures[$i]['m'][] = $m.'-'.$t;
			$stops[$t][] = $h[$i] . ':' . $m;
		}
		$i++;
	} else {
		if (strpos($ligne, "hp_dest_secondaire_renvoi") !== false) {
			$terminusIndice = substr($ligne, -6, 1);
		}
		if (strpos($ligne, "hp_dest_principale_description") !== false) {
			$terminusName = substr($ligne, 43, -6);
			$terminus[0] = $terminusName;
		}
		if (strpos($ligne, "hp_dest_secondaire_description") !== false) {
			$terminusName = substr($ligne, 43, -6);
			$terminus[$terminusIndice] = str_replace('Terminus ', '', $terminusName);
		}
	}
	if (strpos($ligne, "<tr>") !== false) {
		$i = 0;
	}
}

list($t, $networkCode, $t) = explode('|', $parameters['Network']);
list($t, $lineCode, $lineRef, $lineName, $to, $from, $t, $t) = explode('|', $parameters['Line']);
list($t, $stopCode, $stopName, $stopCity) = explode('|', $parameters['StopArea']);
$direction = $parameters['Direction'];

$fichier = fopen($networkCode.'.'.$lineCode.'.'.$direction.'.'.$stopCode.'.txt', 'w+');
fputs($fichier, 'line.ref;' . $lineRef . "\n");
fputs($fichier, 'line.name;' . $lineName . "\n");
fputs($fichier, 'direction;' . $direction . "\n");
fputs($fichier, 'from;' . $from . "\n");
fputs($fichier, 'to;' . $to . "\n");
fputs($fichier, 'stop.name;' . $stopName . "\n");
fputs($fichier, 'stop.city;' . $stopCity . "\n");
if (!empty($terminus)) {
	foreach ($terminus as $i => $terminusName) {
		$data = 'terminus;' . $i . ';' . $terminusName . "\n";
		fputs($fichier, $data);
	}
}
foreach ($stops as $t => $stopsTerminus) {
	$data = 'stops;'.$t;
	sort($stopsTerminus);
	foreach ($stopsTerminus as $h => $stop) {
		$data .= ';' . $stop;
	}
	fputs($fichier, $data . "\n");
}
fclose($fichier);