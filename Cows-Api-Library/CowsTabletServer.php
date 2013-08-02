<?php

require_once 'CowsApiLibrary/CowsApi.php';

if (!isset($_GET['siteId']) || !isset($_GET['date']) || !isset($_GET['bldgRoom']))      {
	exit(0);
}

$siteId = $_GET['siteId'];

$cows = new CowsApi($siteId);

$params = array(
		'timeStart' => 'midnight ' . $_GET['date'],
		'timeEnd' => 'midnight tomorrow ' . $_GET['date'],
		'bldgRoom' => $_GET['bldgRoom']
);

$resp = $cows->getEventInfo($params);

if ($resp === null || $resp === false)  {
	exit(0);
}

else    {
	$resp = json_decode($resp,true);

	if (!is_array($resp)) exit(0);

	if (count($resp) == 0)  {
		$json = array(0 => "noEvent");
	}
	else    {
		$json = array();
		foreach ($resp as $event)       {
			if (!isPast($event['date'],$event['endTime']))  {
				$val = array(
						'Title' => $event['title'],
						'Time' => $event['startTime'] . " -" . $event['endTime'],
						'Location' => $event['location']
				);
				array_push($json,$val);
			}
		}

		if (count($json) == 0)  {
			$json = array(0 => "noEventToday");
		}

	}
	echo json_encode($json);
}

function isPast($date,$endTime) {
	return time() > strtotime($date . " ". $endTime);
}

?>
