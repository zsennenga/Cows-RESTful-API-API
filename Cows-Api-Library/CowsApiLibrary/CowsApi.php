<?php

require_once 'Config.php';

class CowsApi	{
	private $handle;
	private $sessionKey;
	private $siteId
	private $err
	
	public function __construct($siteId)	{
		$this->curlHandle = curl_init();

		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->siteId = $siteId;
		$this->$err = handleError($this->getRequest("/error/"));
	}
	
	private function translateError($out)	{
		return $err[$out['code']];
	}
	
	private function getRequest($uri,$params = "")	{
		$url = API_PATH . $uri;
		$params = http_build_query($params);
		$params = $params . "&sessionKey=" . $this->sessionKey;
		$params = $params . $this->getSignatureParameter("GET", $uri, $params);
		curl_setopt($this->curlHandle, CURLOPT_URL, $url . "?" . $params);
		$out = curl_exec($this->handle);
		if ($out == false)	{
			$err = json_encode(array(
					"code" => "-1",
					"message" => curl_error($this->handle)
			));
			return $err;
		}
		else return $out;
	}
	
	private function postRequest($uri,$params = array())	{
		$params = http_build_query($params);
		$params = $params . "&sessionKey=" . $this->sessionKey;
		$params = $params . $this->getSignatureParameter("POST", $uri, $params);
		curl_setopt($this->curlHandle, CURLOPT_HTTPGET, true);
		curl_setopt($this->curlHandle, CURLOPT_POST, true);
		curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $params);
		$out = curl_exec($this->curlHandle);
		if ($out == false)	{
			$err = json_encode(array(
					"code" => "-1",
					"message" => curl_error($this->handle)
			));
			return $err;
		}
		else return $out;
	}
	
	private function deleteRequest($uri,$params = "")	{
		$url = API_PATH . $uri;
		$params = http_build_query($params);
		$params = $params . "&sessionKey=" . $this->sessionKey;
		$params = $params . $this->getSignatureParameter("DELETE", $uri, $params);
		curl_setopt($this->curlHandle, CURLOPT_URL, $url . "?" . $params);
		curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
		$out = curl_exec($this->handle);
		if ($out == false)	{
			$err = json_encode(array(
					"code" => "-1",
					"message" => curl_error($this->handle)
			));
			return $err;
		}
		else return $out;
	}
	
	public function getSessionKey($tgc)	{
		$params = array(
				"tgc" => $tgc,
				"publicKey" => PUBLIC_KEY
		);
		$out = $this->postRequest(SESSION_PATH . $this->siteId . "/", $params);
		$out = json_decode($out);
		if (isset($out['sessionKey'])	{
			$this->sessionKey = $out['sessionKey'];
		}
		else return handleError($out);
	}
	
	public function destroySessionKey()	{
		$out = $this->deleteRequest(SESSION_PATH . $this->sessionKey);
		return handleError($out);
	}
	
	public function createEvent($params)	{
		$out = $this->postRequest(EVENT_PATH . $this->siteId . "/",$params);
		return handleError($out);
	}
	
	public function getEventInfo()	{
		$out = $this->getRequest(EVENT_PATH . $this->siteId . "/",$params);
		return handleError($out);
	}
	
	public function handleError($out)	{
		$out = json_decode($out);
		if (isset($out['code']))	{
			return array (
					"code" => translateError($out['code']),
					"message" => $out['message']
			);
		}
		return $out;
	}
	
	public function getEventIdInfo($id)	{
		$out = $this->getRequest(EVENT_PATH . $this->siteId . "/" . $id);
		return handleError($out);
	}
	
	public function deleteEventById($id)	{
		$out = $this->deleteRequest(EVENT_PATH . $this->siteId . "/" . $id);
		return handleError($out);
	}
	
	private function getSignatureParameter($requestMethod, $requestURI, $requestParameters)	{
		return "&signature=" . hash_hmac($requestMethod.$requestURI.$requestParameters,PRIVATE_KEY);
	}	
	
}