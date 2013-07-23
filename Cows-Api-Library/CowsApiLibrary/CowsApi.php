<?php

require_once 'Config.php';

class CowsApi	{
	private $handle;
	private $sessionKey;
	
	public function __construct()	{
		$this->curlHandle = curl_init();

		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
	}
	
	private function errorInfo()	{
		
	}
	
	private function getRequest($url,$params)	{
		
	}
	
	private function postRequest($url,$params)	{
		
	}
	
	private function deleteRequest($url,$params)	{
		
	}
	
	public function getSessionKey()	{
		
	}
	
	public function destroySessionKey()	{
		$this->deleteRequest();
	}
	
	public function createEvent()	{
		
	}
	
	public function getEventInfo()	{
		
	}
	
	public function handleError()	{
		
	}
	
	public function getEventIdInfo($id)	{
		
	}
	
	public function deleteEventById($id)	{
		
	}
	
	private function getSignatureParameter($requestMethod, $requestURI, $requestParameters)	{
		return "&signature=" . hash_hmac($requestMethod.$requestURI.$requestParameters,PRIVATE_KEY);
	}	
	
}