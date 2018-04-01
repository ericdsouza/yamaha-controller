<?php
require_once("SimpleRest.php");
		
class YamahaRestHandler extends SimpleRest {

	public function encodeHtml($responseData) {
	
		$htmlResponse = "<table border='1'>";
		foreach($responseData as $key=>$value) {
    			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;		
	}
	
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;		
	}
	
	public function encodeXml($responseData) {
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><yamaha></yamaha>');
		foreach($responseData as $key=>$value) {
			$xml->addChild($key, $value);
		}
		return $xml->asXML();
	}
	
	public function setRelativeVolume($direction, $halfdecibels) {

		switch ($halfdecibels) {
    			case '0':
    			case '1':
    			case '2':
    			case '3':
    			case '4':
    			case '5':
    			case '6':
    			case '7':
    			case '8':
    			case '9':
    			case '10':
				$cleanHalfDecibels = $halfdecibels;
				break;
    			default:
				$cleanHalfDecibels = '5';
    			}
		switch ($direction) {
			case 'up':
			case 'down':
				$cleanDirection = $direction;
				break;
			default:
				$cleanDirection = 'down';
			}

		exec("/usr/lib/cgi-bin/rxvc {$cleanDirection} {$cleanHalfDecibels} 2>&1", $out, $status);

                $txt = "request headers\n";
                foreach (getallheaders() as $name => $value) 
                {
                        $txt .= "'" . $name . "' : '" . $value . "'\n";
                }
                $myfile = file_put_contents('/var/www/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
                fwrite($myfile, "\n". $txt);
                fclose($myfile);

		if (0 === $status) {
    			$volume = $out[0];
			$rawData = array("status"=>"OK", "direction"=>$direction, "halfdecibels"=>$halfdecibels, "volume"=>$volume);
		} else {
			$rawData = array("status"=>"Error", "direction"=>$direction, "out"=>$out, "reasoncode"=>$status);
		}

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Something went really bad');		
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			# $response = $this->encodeHtml($rawData);
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}

        public function setAbsoluteVolume($decibels) {

		$intDecibels = intval($decibels);
		if (($intDecibels > 19) and ($intDecibels < 51)){
			$cleanDecibels = $intDecibels;
		} else {
			$cleanDecibels = '35';
		}

                # rxvc volume -v -39.5
                $command = "/usr/lib/cgi-bin/rxvc volume -v -" . $cleanDecibels . ".0";
                exec("{$command} 2>&1", $out, $status);

                if (0 === $status) {
                        $volume = $out[0];
                        $rawData = array("status"=>"OK", "decibels"=>$decibels, "volume"=>$volume);
                } else {
                        $rawData = array("status"=>"Error", "decibels"=>$decibels, "out"=>$out, "reasoncode"=>$status);
                }

                if(empty($rawData)) {
                        $statusCode = 404;
                        $rawData = array('error' => 'Something went really bad');
                } else {
                        $statusCode = 200;
                }

                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, $statusCode);

                if(strpos($requestContentType,'application/json') !== false){
                        $response = $this->encodeJson($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'text/html') !== false){
                        $response = $this->encodeHtml($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'application/xml') !== false){
                        $response = $this->encodeXml($rawData);
                        echo $response;
                }
        }

        public function setOutputB($onoff) {

                if ($onoff == 'on'){
                        $cleanOnOff = 'on';
                } else {
                        $cleanOnOff = 'off';
                }

                # there is no rxvc command to set output B or scene
                $command = "/usr/lib/cgi-bin/rxvc volume";
                exec("{$command} 2>&1", $out, $status);

                if (0 === $status) {
                        $volume = $out[0];
                        $rawData = array("status"=>"OK", "onoff"=>$cleanOnOff, "volume"=>$volume);
                } else {
                        $rawData = array("status"=>"Error", "onoff"=>$cleanOnOff, "out"=>$out, "reasoncode"=>$status);
                }

                if(empty($rawData)) {
                        $statusCode = 404;
                        $rawData = array('error' => 'Something went really bad');
                } else {
                        $statusCode = 200;
                }

                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, $statusCode);

                if(strpos($requestContentType,'application/json') !== false){
                        $response = $this->encodeJson($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'text/html') !== false){
                        $response = $this->encodeHtml($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'application/xml') !== false){
                        $response = $this->encodeXml($rawData);
                        echo $response;
                }
        }

        public function setInputSource($inputsource) {

                switch ($inputsource) {
                        case 'HDMI1':
                        case 'HDMI2':
                        case 'TUNER':
                                $cleanInputSource = $inputsource;
                                break;
                        default:
                                $cleanInputSource = 'HDMI2';
                        }

                # rxvc input HDMI1 
                $command = "/usr/lib/cgi-bin/rxvc input " . $cleanInputSource;
                exec("{$command} 2>&1", $out, $status);

                if (0 === $status) {
                        $rxvcResponse = $out[0];
                        $rawData = array("status"=>"OK", "inputsource"=>$cleanInputSource, "rxvcresponse"=>$rxvcResponse);
                } else {
                        $rawData = array("status"=>"Error", "inputsource"=>$cleanInputSource, "out"=>$out, "reasoncode"=>$status);
                }

                if(empty($rawData)) {
                        $statusCode = 404;
                        $rawData = array('error' => 'Something went really bad');
                } else {
                        $statusCode = 200;
                }

                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, $statusCode);

                if(strpos($requestContentType,'application/json') !== false){
                        $response = $this->encodeJson($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'text/html') !== false){
                        $response = $this->encodeHtml($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'application/xml') !== false){
                        $response = $this->encodeXml($rawData);
                        echo $response;
                }
        }

        public function setPower($onoff) {

                switch ($onoff) {
                        case 'on':
                        case 'On':
                                $cleanOnOff = 'on';
                                break;
                        case 'off':
                        case 'Off':
                                $cleanOnOff = 'off';
                                break;
                        default:
                                $cleanOnOff = 'on';
                        }

                # rxvc power on
                $command = "/usr/lib/cgi-bin/rxvc power " . $cleanOnOff;
                exec("{$command} 2>&1", $out, $status);

                if (0 === $status) {
                        $rxvcResponse = $out[0];
                        $rawData = array("status"=>"OK", "onoff"=>$cleanOnOff, "rxvcresponse"=>$rxvcResponse);
                } else {
                        $rawData = array("status"=>"Error", "onoff"=>$cleanOnOff, "out"=>$out, "reasoncode"=>$status);
                }

                if(empty($rawData)) {
                        $statusCode = 404;
                        $rawData = array('error' => 'Something went really bad');
                } else {
                        $statusCode = 200;
                }

                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, $statusCode);

                if(strpos($requestContentType,'application/json') !== false){
                        $response = $this->encodeJson($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'text/html') !== false){
                        $response = $this->encodeHtml($rawData);
                        echo $response;
                } else if(strpos($requestContentType,'application/xml') !== false){
                        $response = $this->encodeXml($rawData);
                        echo $response;
                }
        }

}
?>
