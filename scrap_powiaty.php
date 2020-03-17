<?php exit();

include("proj4php/vendor/autoload.php");
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

function pl2wgs($point)
{
    if(!$point || count($point)!=2) return false;
    $proj4 = new Proj4php();
    $projL93    = new Proj('EPSG:2180', $proj4);
    $projWGS84  = new Proj('EPSG:4326', $proj4);
    $pointSrc = new Point($point[1],$point[0], $projL93);
    $pointDest = $proj4->transform($projWGS84, $pointSrc);
    return [ $pointDest->toArray()[1] , $pointDest->toArray()[0] ];
}



function center($polygon)
{
	return geom_center($polygon);
}


function box_center($polygon)
{
	
	$maxx=0.;$minx=9999999999.;
	$maxy=0.;$miny=9999999999.;
	
	foreach($polygon as $point)
	{
		$maxx = floatval($point[0])>$maxx ? floatval($point[0]) : $maxx;
		$minx = floatval($point[0])<$minx ? floatval($point[0]) : $minx;
		$maxy = floatval($point[1])>$maxy ? floatval($point[1]) : $maxy;
		$miny = floatval($point[1])<$miny ? floatval($point[1]) : $miny;
	}
	return array( ($minx+$maxx)/2., ($miny+$maxy)/2. );
}

function geom_center($polygon)
{
	$x = 0; $y = 0;
	for($i=0;$i<count($polygon);$i++)
	{
		$x+=$polygon[$i][0];
		$y+=$polygon[$i][1];
	}
	$ile = count($polygon);
	$x/=$ile; $y/=$ile;
	return array($x,$y);
}
function dummy_center($polygon)
{
	return $polygon[0];
}

$src = file_get_contents('powiaty.gml');
$regexp = '#<gml:posList>(.*?)</gml:posList>.*?<prg:kodJednostki>(.*?)</prg:kodJednostki>.*?<prg:nazwaJednostki>(.*?)</prg:nazwaJednostki>#ims';
$matches = array();
preg_match_all($regexp, $src, $matches);
//echo('<pre>');print_r($matches);exit();


$prebake = array();

for($j=0;$j<count($matches[1]);$j++)
{
	$teryt = $matches[2][$j];
	$names[$teryt]=$matches[3][$j];
	//$outpoint = pl2wgs(array($x,$y));
	$polygon = explode(' ',$matches[1][$j] );
	
	for($i=0;$i<count($polygon);$i+=2)
	{
		$x=$polygon[$i];
		$y=$polygon[$i+1];
		$prebake[$teryt][] = array($x,$y);
	}
//	if(++$counter>10){break;}
}
$outfile = '';
foreach($prebake as $teryt => $polygon)
{
	
	list($x,$y) = center($polygon);
	
	//$outpoint = array($x,$y);
	$outpoint = pl2wgs(array($x,$y));
	$out = $teryt.";".$outpoint[0].";".$outpoint[1].';'.$names[$teryt]."\n";
	echo($out);
	$outfile .= $out;
}
file_put_contents('wspolrzedne_powiatow.csv',$outfile);
