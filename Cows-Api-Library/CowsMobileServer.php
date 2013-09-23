<?php

require_once 'CowsApiLibrary/CowsApi.php';

$publicKey = "EDITME";
$privateKey = "EDITME";

$params = $_POST;

if (!isset($params['siteId']) || !isset($params['tgc']))	{
	exit(0);
}

if ($_SERVER['REQUEST_METHOD'] != 'POST')	{
	exit(0);
}

$siteId = $params['siteId'];
$tgc = $params['tgc'];
unset($params['siteId']);
unset($params['tgc']);

$cows = new CowsApi($siteId,$publicKey,$privateKey);
parseError($cows->getSession($tgc),$cows);
parseError($cows->createEvent($params),$cows);

$cows->destroySession();

echo "0:0";

function parseError($arr,$cows)	{
		$out = json_decode($arr,true);
		if ($out === null || $out === false)	{
			echo "-1: Unable to parse JSON from API";
			exit(0);
		}
		if ($out['code'] < 0 )	{
			echo $out['code'] . ":" . $out['message'];
			$cows->destroySession();
			exit(0);
		}
}
?>