<?php

require_once 'CowsApiLibrary/CowsApi.php';

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

$cows = new CowsApi($siteId);
parseError($cows->getSession($tgc),$cows);
parseError($cows->createEvent($params),$cows);

$cows->destroySession();

echo "0:0";

function parseError($arr,$cows)	{
	$out = json_decode($arr,true);
	if (is_array($out))	{
		if (isset($out['code']))	{
			echo $out['code'] . ":" . $out['message'];
			$cows->destroySession();
			exit(0);
		}
	}
	else	{
		echo "-1:Unable to parse JSON response from api";
		$cows->destroySession();
		exit(0);
	}
	
}
?>