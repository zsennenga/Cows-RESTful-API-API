<?php

require_once 'CowsApiLibrary/CowsApi.php';

$params = $_POST;

$siteId = $params['siteId'];
$tgc = $params['tgc'];

unset($params['siteId']);
unset($params['tgc']);

$cows = new CowsApi($siteId);
$cows->getSession($tgc);

$out = json_decode($cows->createEvent($params),true);
if ($out)	{
	if (isset($out['code']))	
		echo $out['code'] . ":" . $out['message'];
	else 
		echo "0:0";
}
else	
	echo "-1:Unable to parse response";

$cows->destroySession();
?>