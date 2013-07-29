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
	'ContactPhone' => '1',
	'EventTypeName' => 'Maintenance_Other'
);

$tgc = 'TGT-30455-O6XbmSLiLXBnKD9vuhl9EcuhKf9qL7QseugDspGDDx6lmrJnPG-56';

$cows = new CowsApi('its');

echo "Creating Session\n";

$out = $cows->getSession($tgc);
var_dump ($out);

$out = $cows->createEvent($params);
echo "Creating Event:\n";
var_dump($out);
if (isset($out['eventId']))	{
	$id = $out['eventId'];
}

else	{
	exit(0);
}

echo "Got id $id\n";

echo "Getting Event Info by ID:\n";
$out = $cows->getEventIdInfo($id);
var_dump($out);

$out = $cows->deleteEventById($id);
echo "Deleting Event:\n";
var_dump($out);

$out = $cows->getEventIdInfo($id);
echo "Getting Event Info by ID after delete:\n";
var_dump($out);

echo "Destroying Session:\n";
$out = $cows->destroySession();
var_dump($out);

$out = $cows->createEvent($params);
echo "Creating event after session destroyed:\n";
var_dump($out);

$out = $cows->getEventInfo();
echo "Getting generic event info";
var_dump($out);

?>