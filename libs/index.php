<?php
require_once 'config.php';
require_once 'functions.php';

function authenticate($role = PERMISSION_NONE) {
    return function () use ($role) { //role从哪儿来？？echo "role=".1; app->user->role=4 ,4 1 ,1 2,4 3,1 0
    	if($role == PERMISSION_NONE) return;
    	$app = \Slim\Slim::getInstance();
    	if($app->user == null || ($app->user->role & $role) != $role){
            $app->flash('error', 'Not authenticated');
            //$app->render('/index.php',array('role'=>$role));
            $app->redirect(APP_BASE_PATH);
    	}
        
    };
};

class GithubApiCaller {
	private $access_token;
	private $url;
	private $curl;
	
	public function setAccessToken($access_token){
		$this->access_token = $access_token;
	}
	
	public function __construct($access_token = null){
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
		//$header[] = {"scopes" : ["public_repo"]};
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header); 
	}
	
	private function callExecute(){
		$response = curl_exec($this->curl);
		if($response === false){
			throw new Exception('Call '.$this->url.' failed.');
		}
		var_dump($response);
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


class McryptWrapper {
	private $td;
	private $key;
	private $iv;

	public function __construct(){
		$this->key = APP_SECRET_KEY;
		$this->td = mcrypt_module_open('des', '', 'ecb', '');
		$this->key = substr($this->key, 0, mcrypt_enc_get_key_size($this->td));
		$iv_size = mcrypt_enc_get_iv_size($this->td);
		$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	}

	public function encrypt($plain_text){
		mcrypt_generic_init($this->td, $this->key, $this->iv);
		$result = mcrypt_generic($this->td, $plain_text);
		mcrypt_generic_deinit($this->td);
		return base64_encode($result);
	}

	public function decrypt($entrypted_text){
		mcrypt_generic_init($this->td, $this->key, $this->iv);
		$result = mdecrypt_generic($this->td, base64_decode($entrypted_text));
		mcrypt_generic_deinit($this->td);
		return $result;
	}

	function __destruct(){
		mcrypt_module_close($this->td);
	}
}