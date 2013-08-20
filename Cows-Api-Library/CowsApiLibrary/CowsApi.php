<?php

require_once 'Config.php';

/**
 * Class used to interact with COWS-RESTful-API
 * @author its-zach
 *
 */
class CowsApi	{
	private $handle;
	private $siteId;
	
	private $publicKey;
	private $privateKey;
	
	private $errorCodeTranslation;
	public $errorCode;
	public $errorMessage;
	
	/**
	 * Builds class to interact with a specific COWS sub-site
	 * @param String $siteId
	 */
	public function __construct($siteId,$publicKey, $privateKey)	{
		$this->handle = curl_init();

		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		
		$this->siteId = $siteId;
		$this->errorCodeTranslation = json_decode($this->getRequest("/error/"),true);
	}
	/**
	 * Get the Cows error code name from a given int string.
	 * @param Error Code $out
	 * @return Error Name
	 */
	private function translateError($out)	{
		return $err[$out['code']];
	}
	
	private function getRequest($uri,$params = array())	{
		$url = API_PATH . $uri;
		
		if (is_array($params)) $params = http_build_query($params);

		$this->auth("GET", $uri, $params);
		
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->handle, CURLOPT_URL, $url . "?" . $params);
		$out = curl_exec($this->handle);
		
		if ($out == false)	{
			$this->errorCode = curl_errno($this->handle);
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	/**
	 * Executes a POST request using the given URI.
	 *
	 * Do NOT prepend the API_PATH/Hostname of the Api.
	 *
	 * Returns the response text or false if a cURL error occurs
	 *
	 * @param String $uri
	 * @param String $params
	 * @return boolean|mixed
	 */
	private function postRequest($uri,$params = array())	{
		$url = API_PATH . $uri;
		
		if (is_array($params)) $params = http_build_query($params);

		$this->auth("POST", $uri, $params);
		
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->handle, CURLOPT_URL, $url);
		$out = curl_exec($this->handle);
		
		if ($out === false)	{
			$this->errorCode = curl_errno($this->handle);
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	/**
	 * Executes a DELETE request using the given URI.
	 *
	 * Do NOT prepend the API_PATH/Hostname of the Api.
	 *
	 * Returns the response text or false if a cURL error occurs
	 *
	 * @param String $uri
	 * @param String $params
	 * @return boolean|mixed
	 */
	private function deleteRequest($uri,$params = "")	{
		$url = API_PATH . $uri;
		
		if (is_array($params)) $params = http_build_query($params);
		
		$this->auth("DELETE", $uri, $params);
		
		curl_setopt($this->handle, CURLOPT_URL, $url . "?" . $params);
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
		$out = curl_exec($this->handle);
		
		if ($out === false)	{
			$this->errorCode = curl_errno($this->handle);
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	/**
	 * 
	 * Creates a session with the Cows api and thus, with COWS
	 * 
	 * Returns the json response as an associative array
	 * 
	 * @param Ticket Granting Cookie $tgc
	 * @return mixed
	 */
	public function getSession($tgc)	{
		$params = array(
				"tgc" => $tgc
		);
		return $this->postRequest(SESSION_PATH . $this->siteId . "/", $params);
	}
	/**
	 * Ends the COWS/Cows api session
	 * 
	 * Returns the json response as an associative array
	 * @return mixed
	 */
	public function destroySession()	{
		return $this->deleteRequest(SESSION_PATH);
	}
	/**
	 * 
	 * Creates an event on COWS
	 * 
	 * Returns the json response as an associative array
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function createEvent($params)	{
		return $this->postRequest(EVENT_PATH . $this->siteId . "/",$params);
	}
	/**
	 * Gets all the event info, pared down by the parameters
	 * 
	 * Uses Cows RSS parameters
	 * 
	 * Returns the json response as an associative array
	 * 
	 * @param unknown $params
	 * @return mixed
	 */
	public function getEventInfo($params = array())	{
		return $this->getRequest(EVENT_PATH . $this->siteId . "/",$params);
	}
	/**
	 * 
	 * Returns the json response as an associative array
	 * 
	 * @param EventId $id
	 * @return mixed
	 */
	public function getEventIdInfo($id)	{
		return $this->getRequest(EVENT_PATH . $this->siteId . "/" . $id);
	}
	/**
	 * 
	 * Deletes a single event by the event ID
	 * 
	 * Returns the json response as an associative array
	 * 
	 * @param Event Id $id
	 * @return mixed
	 */
	public function deleteEventById($id)	{
		return $this->deleteRequest(EVENT_PATH . $this->siteId . "/" . $id);
	}
	
	private function auth($method, $uri, $params)	{
		$time = time();
		$arr = array(
			"Authorization: " . $this->publicKey . "|" . $time  . "|" . $this->getSignatureParameter($method, $uri, $params,$time)
		);
		
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $arr);
	}
	/**
	 * 
	 * Generates the signature for the request
	 * 
	 * @param string $requestMethod
	 * @param string $requestURI
	 * @param string $requestParameters
	 * @return string
	 */
	private function getSignatureParameter($requestMethod, $requestURI, $requestParameters, $time)	{
		return hash_hmac('sha256',$requestMethod.$requestURI.$requestParameters.$time,$this->privateKey);
	}
	
}