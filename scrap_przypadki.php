<?php
header('Content-Type:text/html; charset=UTF-8');
function logme($data){
	file_put_contents('logs/'.date('Y-m-d').'.log', date('Y-m-d H:i:s ').$data."\n", FILE_APPEND );
}
?>
<html>
<head>
<meta charset="utf-8" />
</head>
<body>
<?php

	
	$adr = 'https://www.gov.pl/web/koronawirus/wykaz-zarazen-koronawirusem-sars-cov-2';

	$ch = curl_init($adr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	echo('<pre>');
	// print_r($result);
	
	$regexp = '#<pre id="registerData" class="hide">(.*?)</pre>#';
	$matches = array();
	preg_match($regexp, $result, $matches);
	// print_r($matches[1]);
	
	$data = json_decode($matches[1],true);
	if(!is_array($data) || !count($data))
	{
		logme('wrong data');
		exit();
	}
	// print_r($data);
	
	$csv = $data['data'];
	//print_r($csv);
	file_put_contents('data/przypadki_'.date('Y-m-d_His').'.csv',$csv);
	logme('zapisalem csv');
	
	
?>

</body></html>