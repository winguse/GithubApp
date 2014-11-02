<?php
require_once 'config.php';

class GithubApiCaller {
	private $access_token;
	private $url;
	private $curl;
	
	public function setAccessToken($access_token){
		$this->access_token = $access_token;
	}
	
	public function __construct($access_token){
		$this->setAccessToken($access_token);
	}
	
	private function callInit($url){
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLINFO_HEADER_OUT, true); 
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($this->curl, CURLOPT_USERAGENT, APP_NAME);
		$header = array();
		$header[] = 'Accept: application/json';
		if($this->access_token != null) {
			$header[] = 'Authorization: token '.$this->access_token;
		}
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header); 
	}
	
	private function callExecute(){
		$response = curl_exec($this->curl);
		if($response === false){
			throw new Exception('Call '.$this->url.' failed.');
		}
		return json_decode($response, true);
	}
	
	public function post($url, $data) {
		$this->callInit($url);
		curl_setopt($this->curl, CURLOPT_POST, TRUE);
		curl_setopt($this->curl, CURLOPT_POST, count($data)); 
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data); 
		return $this->callExecute();
	}
	
	public function get($url) {
		$this->callInit($url);
		return $this->callExecute();
	}
}