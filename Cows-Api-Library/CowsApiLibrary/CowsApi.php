<?php

require_once 'Config.php';

class CowsApi	{
	private $handle;
	private $siteId;
	private $publicKey;
	
	private $errorCodeTranslation;
	private $errorCode;
	private $errorMessage;
	
	public function __construct($siteId)	{
		$this->handle = curl_init();

		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->siteId = $siteId;
		$this->errorCodeTranslation = json_decode($this->getRequest("/error/"),true);
	}
	
	private function translateError($out)	{
		return $err[$out['code']];
	}
	
	private function getRequest($uri,$params = array())	{
		$url = API_PATH . $uri;
		if (is_array($params)) $params = http_build_query($params);
		if ($params == "") $params = $params . "publicKey=" . PUBLIC_KEY;
		else $params = $params . "&publicKey=" . PUBLIC_KEY;
		$params = $params . $this->getSignatureParameter("GET", $uri, $params);
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->handle, CURLOPT_URL, $url . "?" . $params);
		$out = curl_exec($this->handle);
		if ($out == false)	{
			$this->errorCode = $this->errorCodeTranslation["-1"];
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	
	private function postRequest($uri,$params = array())	{
		$url = API_PATH . $uri;
		if (is_array($params)) $params = http_build_query($params);
		if ($params == "") $params = $params . "publicKey=" . PUBLIC_KEY;
		else $params = $params . "&publicKey=" . PUBLIC_KEY;
		$params = $params . $this->getSignatureParameter("POST", $uri, $params);
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->handle, CURLOPT_URL, $url);
		$out = curl_exec($this->handle);
		if ($out == false)	{
			$this->errorCode = $this->errorCodeTranslation["-1"];
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	
	private function deleteRequest($uri,$params = "")	{
		$url = API_PATH . $uri;
		if (is_array($params)) $params = http_build_query($params);
		if ($params == "") $params = $params . "publicKey=" . PUBLIC_KEY;
		else $params = $params . "&publicKey=" . PUBLIC_KEY;
		$params = $params . $this->getSignatureParameter("DELETE", $uri, $params);
		curl_setopt($this->handle, CURLOPT_URL, $url . "?" . $params);
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
		$out = curl_exec($this->handle);
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, null);
		if ($out == false)	{
			$this->errorCode = $this->errorCodeTranslation["-1"];
			$this->errorMessage = curl_error($this->handle);
			return false;
		}
		else return $out;
	}
	
	public function getSession($tgc)	{
		$params = array(
				"tgc" => $tgc
		);
		$out = $this->postRequest(SESSION_PATH . $this->siteId . "/", $params);
		$out = json_decode($out,true);
		return $out;
	}
	
	public function destroySession()	{
		$out = $this->deleteRequest(SESSION_PATH);
		return json_decode($out,true);
	}
	
	public function createEvent($params)	{
		$out = $this->postRequest(EVENT_PATH . $this->siteId . "/",$params);
		return json_decode($out,true);
	}
	
	public function getEventInfo($params = array())	{
		$out = $this->getRequest(EVENT_PATH . $this->siteId . "/",$params);
		return json_decode($out,true);
	}
	
	public function getEventIdInfo($id)	{
		$out = $this->getRequest(EVENT_PATH . $this->siteId . "/" . $id);
		return json_decode($out,true);
	}
	
	public function deleteEventById($id)	{
		$out = $this->deleteRequest(EVENT_PATH . $this->siteId . "/" . $id);
		return json_decode($out,true);
	}
	
	private function getSignatureParameter($requestMethod, $requestURI, $requestParameters)	{
		return "&signature=" . hash_hmac('sha256',$requestMethod.$requestURI.$requestParameters,PRIVATE_KEY);
	}	
	
}