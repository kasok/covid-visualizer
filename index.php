<?php
header('Content-Type:text/html; charset=UTF-8');
?>
<html>


<head>
<link rel="manifest" href="manifest.json">
<link rel="icon" sizes="32x32" href="virus.png">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="COVID-19 PL">
<link rel="apple-touch-icon" href="/virus-192.png">
<link rel="apple-touch-startup-image" href="/virus-192.png">
<link rel="apple-touch-icon-precomposed" sizes="192x192" href="/virus-192.png" />
<meta name="viewport" content="initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>COVID-19 w Polsce - gdziewirus.eu</title>
<meta name="keywords" content="koronawirus,polska,covid,mapa,przypadki,oficjalne" />
<meta name="description" content="Aktualna mapa przypadków koronawirusa COVID-19 w Polsce" />
<meta property="og:title" content="COVID-19 w Polsce - gdziewirus.eu" />
<meta property="og:image" content="virus-192.png" />
<meta property="og:image:width" content="192″ />
<meta property="og:image:height" content="192″ />
<meta property="og:url" content="https://gdziewirus.eu" />
<meta property="og:description" content="Aktualna mapa przypadków koronawirusa COVID-19 w Polsce" />

</head>
<body>
<style type="text/css">
body{
	background: rgb(223,246,254);
	background: radial-gradient(circle, rgba(223,246,254,1) 0%, rgba(197,223,233,1) 100%);
}
h1, h2, h3, h4, h5{
	margin:auto; text-align:center;
	font-family: Century Gothic, Verdana, Helvetica, Arial;
}
</style>
<?php
echo('<h2>COVID-19 w Polsce</h2>');

// --------------------get CASES---------------------
$filelist = glob('data/przypadki_*');
rsort($filelist);
$plik_przypadki = $filelist[0];


//$przypadki = file($plik_przypadki);


$przypadki = str_getcsv(file_get_contents($plik_przypadki), "\n"); //parse the rows
foreach($przypadki as &$Row) $Row = str_getcsv($Row, ";"); //parse the items in rows

 // print_r($przypadki); exit();
 
 //------------------------- get REGIONS ----------------------
 

$powiaty = str_getcsv(file_get_contents('data/wspolrzedne_powiatow.csv'), "\n"); //parse the rows
$powiaty_wspolrz = array();
foreach($powiaty as &$Row) $Row = str_getcsv($Row, ";"); //parse the items in rows
for($i=0;$i<count($powiaty);$i++)
{
	$powiaty_wspolrz[ $powiaty[$i][0] ] = array( $powiaty[$i][1],$powiaty[$i][2] );
}
 
 

$przypadki_proc = array();

for($i=0;$i<count($przypadki);$i++)
{
	$row = $przypadki[$i];
	if($row[4] === 'Id')
		continue;
	if($row[4] === 't0000')
	{
		
		$total_cases = $row[2];
		$total_deceased = $row[3];
		continue;
		
	}
	
	$teryt = str_replace('t','',$row[4]);
	
	$coords = [50,20]; //środek Polski
	
	//                id powiatu       woj    powiat         przypadki        zgony             latitude                   longitude
	$przypadki_proc[ $teryt ] = array($row[0],$row[1],intval($row[2]), intval($row[3]), $powiaty_wspolrz[$teryt][0], $powiaty_wspolrz[$teryt][1] ); 
	
}

//print_r($przypadki_proc);

//--------------- end get regions --------------------------





function renderDataScript(){
	global $przypadki_proc, $total_cases ;
	$js_str = '';
	$js_str .= 'var addressPoints = [';
	foreach($przypadki_proc as $teryt => $przypadek)
		$js_str.='['.$przypadek[4].', '.$przypadek[5].', '.sprintf('%0.4f',$przypadek[2]*100/$total_cases ).'],';
	$js_str=substr($js_str,0,-1);
	$js_str .='];';
	echo($js_str);
	//echo(file_get_contents('Leaflet.heat-gh-pages/realworld.10000.js'));
}
function renderHtmlBubble($p){
	return sprintf('<h2>woj.: %s<br />powiat: %s</h2><hr /><h3>Przypadków: %d<br />Zgonów: %d</h3>',
		$p[0], $p[1], $p[2], $p[3]
	
	);
}
function renderMarkerScript(){
	
	$js_str = '';
	global $przypadki_proc;
	foreach($przypadki_proc as $teryt => $przypadek)
	{
		$js_str .= sprintf('markers[%d] = L.marker([%0.6f, %0.6f], {
			title: "%s",
			icon: L.icon({iconUrl: \'virus.png\', iconSize: [%d, %d]})
		  }).addTo(map);

		  markers[%d].bindPopup("%s");', $teryt, $przypadek[4], $przypadek[5], 'empty', 32*sqrt($przypadek[2])/2, 32*sqrt($przypadek[2])/2, $teryt, renderHtmlBubble($przypadek));
		  
	}
	echo( $js_str ); 
}
printf('<h3>Razem zakażeń: %d, razem zgonów: %d</h3>', $total_cases, $total_deceased);
echo('<button style="height:30px;" onclick="window.location.reload();">Odśwież</button>');
echo('<h4>kliknij ikonę na mapie, aby zobaczyć szczegóły, dodaj stronę do ekranu głównego swego telefonu, żeby być na bieżąco</h4>');
require('map.php');
$updata = explode('_',str_replace(array('data/przypadki_','.csv'),'',$plik_przypadki));
$updata[1] = substr($updata[1],0,2).':'.substr($updata[1],2,2).':'.substr($updata[1],4,2);
$updata = $updata[0].' '.$updata[1];
echo('<hr/>
Źródło danych: <a href="https://gov.pl/web/koronawirus/wykaz-zarazen-koronawirusem-sars-cov-2" target="_blank">https://gov.pl/web/koronawirus/wykaz-zarazen-koronawirusem-sars-cov-2</a><br />
Zachorowania na świecie: <a target="_blank" href="https://gisanddata.maps.arcgis.com/apps/opsdashboard/index.html#/bda7594740fd40299423467b48e9ecf6">link</a><br />
ostatnia aktualizacja: '.$updata.'<br />opr. Kacper Sokołowski, <!--firma <img src="logo.svg" style="position:relative;top:3px;" width="60px" />--><br />zgłoszenia awarii: jamjest[at]gmail.com, +48696700130<br />
Wyświetlana lokalizacja przypadku to przybliżenie geometrycznego środka powiatu na podstawie danych konturowych Głównego Urzędu Geodezji i Kartografii.
'
);

echo('</body>');