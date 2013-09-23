<?php
/**
 * A script to test most of the API's functions
 * 
 * 
 */
require_once '../CowsApi.php';

$params = array(
	'EventTitle' => 'test',
	'StartDate' => '9/1/2013',
	'EndDate' => '9/1/2013',	
	'StartTime' => "8:00 AM",
	'EndTime' => "9:00 AM",
	'DisplayStartTime' => '8:00 AM',
	'DisplayEndTime' => '9:00 AM',
	'BuildingAndRoom' => '1590_Tilia!1142',
	'Categories' => 'Other',
	'EventTypeName' => 'Maintenance_Other'
);

$tgc = 'TGT-259274-Fng0sf3DahhWdVJcJPKzuPTcpAmKA66yzF5pwOecsbs1YAcc40-casweb9';
$publicKey = 'test';
$privateKey = 'test';

$cows = new CowsApi('its',$publicKey,$privateKey);

echo "Creating Session\n";

$out = $cows->getSession($tgc);
echo ($out);

$out = $cows->createEvent($params);
echo "\nCreating Event:\n";
echo($out);
$out = json_decode($out, true);
$out = $out['message'];
if (isset($out['eventId']))	{
	$id = $out['eventId'];
}

else	{
	$id = 126933;
}

echo "\nGot id $id\n";

echo "\nGetting Event Info by ID:\n";
$out = $cows->getEventIdInfo($id);
var_dump($out);

$out = $cows->deleteEventById($id);
echo "\nDeleting Event:\n";
var_dump($out);

$out = $cows->getEventIdInfo($id);
echo "\nGetting Event Info by ID after delete:\n";
var_dump($out);

echo "\nDestroying Session:\n";
$out = $cows->destroySession();
var_dump($out);

$out = $cows->createEvent($params);
echo "\nCreating event after session destroyed:\n";
var_dump($out);

$out = $cows->getEventInfo();
echo "\nGetting generic event info";
var_dump($out);

?>