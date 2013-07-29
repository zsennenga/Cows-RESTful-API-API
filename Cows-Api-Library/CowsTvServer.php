<?php

require_once 'CowsApiLibrary/CowsApi.php';

if (!isset($_GET['callback']) ||
	!isset($_GET['siteId']))	{
	exit(0);
}

$siteId = $_GET['siteId'];
$callback = $_GET['callback'];

$cows = new CowsApi($siteId);

$params = array(
		'timeStart' => 'midnight',
		'timeEnd' => 'midnight tomorrow',
		'display' => 'Front-TV'
);

$resp = $cows->getEventInfo($params);

if ($resp === null || $resp === false)	{
	exit(0);
}

else	{
	$resp = json_decode($resp,true);
	
	if (!is_array($resp)) exit(0);
	
	if (count($resp) == 0)	{
		$json = array(0 => "noEvent",1 => "<div class='noevent'>No events scheduled for today</div>");
	}
	else	{
		$json = array();
		foreach ($resp as $event)	{
			if (!isPast($event['date'],$event['endTime']))	{
				
				$val = "<div class = 'row'><div class = 'event'><div class = 'title'>" . $event['title'] . "</div><br><div class = 'other'> " . $event['startTime'] . " - " . $event['endTime'] . "<br>" . $event['location'] . "<br></div></div></div>";
				array_push($json,$val);
			}
		}
		
		if (count($json) == 0)	{
			$json = array(0 => "noEvent",1 => "<div class='noevent'>No events remaining for today</div>");
		}
		
	}
	echo $callback . "(" . json_encode($json) . ");";
}

function isPast($date,$endTime)	{
	return time() > strtotime($date . " ". $endTime);
}

?>
