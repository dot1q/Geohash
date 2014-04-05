<?php
// Set vars to 0
$finalLong = 0;
$finalLat = 0;
$error = 0;

// Get location
$long = (int)$_GET["long"];
$lat = (int)$_GET["lat"];

// set timezone and get date
// set to New_York so time is not edited and DOW does not open until 9am eastern or 6am pacific
date_default_timezone_set('America/New_York');
$date = date('Y/m/d', time());

//echo 'TIME IS: '.date('G:i:s a', time());

//Check if time is between 12am and 8:59am, and return error if it is true
if ((date('G')>=0) && ((date('G')<=8) && (date('i')<60))){
	//echo 'Hashpoint is currently not available';
	$error = 1;
}else{
	//echo 'Hashpoint is available <br />';
	$error = 0;
	
	//Get Dow Opening
	//You many manually set a date by using the layout below
	//$url = "http://carabiner.peeron.com/xkcd/map/data/2014/04/03";
	$url = "http://carabiner.peeron.com/xkcd/map/data/".$date;
	$dow = file_get_contents($url);

	//get year, month and day and $dow and calculate the MD5 hash
	$value = date('Y',time())."-".date('m',time())."-".date('d',time())."-".$dow;
	$hash = md5($value);

	//split hash where each is 16
	$first = substr($hash, 0, 16);
	$second = substr($hash, 16);

	$total = 0;
	for ($i = 0; $i<strlen($first); $i++){
		$num = (hexdec($first[$i]))*pow(16,0-$i-1);
		//echo ' '.$num;
		$total = $num + $total;
	}
	$finalLat = $lat.substr($total, 1);

	$total =0;
	for ($i = 0; $i<strlen($second); $i++){
		$num = (hexdec($second[$i]))*pow(16,0-$i-1);
		//echo ' '.$num;
		$total = $num + $total;
	}
	$finalLong = $long.substr($total, 1);

	//Print out the cords *debugging*
	//echo number_format($finalLat,6) . '<br />';
	//echo number_format($finalLong,6);
}
//Output in JSON
$arr = array('name' => $date.' Hashpoint', 'error' => $error, 'latitude' => number_format($finalLat,6), 'longitude' => number_format($finalLong,6) );
echo '{ "landmarks": [ '.json_encode($arr).' ] }';
?>
