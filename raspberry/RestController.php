<?php
require_once("YamahaRestHandler.php");
		
$intent = "";
if(isset($_GET["intent"]))
	$intent = $_GET["intent"];
/*
controls the RESTful services
URL mapping
*/
switch($intent){

	case "relative":
		// to handle REST Url /yamaha/volume/relative/<$direction>/<$halfdecibels>
		$yamahaRestHandler = new YamahaRestHandler();
		$yamahaRestHandler->setRelativeVolume($_GET["direction"],$_GET["halfdecibels"]);
		break;
		
	case "absolute":
		// to handle REST Url /yamaha/volume/absolute/<$decibels>
		$yamahaRestHandler = new YamahaRestHandler();
		$yamahaRestHandler->setAbsoluteVolume($_GET["decibels"]);
		break;

	case "outputb":
		// to handle REST Url /yamaha/outputb/<$onoff>
		$yamahaRestHandler = new YamahaRestHandler();
		$yamahaRestHandler->setOutputB($_GET["onoff"]);
		break;

	case "inputsource":
		// to handle REST Url /yamaha/inputsource/<$inputsource>
		$yamahaRestHandler = new YamahaRestHandler();
		$yamahaRestHandler->setInputSource($_GET["inputsource"]);
		break;

	case "onoff":
		// to handle REST Url /yamaha/power/<$onoff>
		$yamahaRestHandler = new YamahaRestHandler();
		$yamahaRestHandler->setPower($_GET["onoff"]);
		break;

	case "" :
		//404 - not found;
		break;
}
?>
