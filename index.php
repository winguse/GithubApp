<?php
require_once 'Slim/Slim.php';
require_once 'config.php';
require_once 'libs.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
	'templates.path' => __FILE__.'/views/',
	'cookies.path' => APP_BASE_PATH,
	'cookies.domain' => APP_DOMAIN,
	'cookies.lifetime' => '30 days',
	'cookies.secure' => true,
	'cookies.httponly' => true,
	'cookies.encrypt' => true,
	'cookies.secret_key' => APP_COOKIE_SECRET,
	'debug' => true,
	'mode' => 'development'
));

$app->add(new \Slim\Middleware\SessionCookie(array(
	'expires' => '20 minutes',
	'path' => APP_BASE_PATH,
	'domain' => APP_DOMAIN,
	'secure' => true,
	'httponly' => true,
	'name' => APP_NAME,
	'secret' => 'CHANGE_ME',
	'cipher' => MCRYPT_RIJNDAEL_256,
	'cipher_mode' => MCRYPT_MODE_CBC
)));

$app->github = new GithubApiCaller($app->getCookie('access_token'));

$app->get(
	'/',
	function () {
		global $app;
		$username = $app->getCookie('username');
		echo $username;
		$app->setCookie('username', 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest');
	}
);

$app->get(
	'/auth',
	function () use ($app){
		$state = $app->request->params('state');
		$code = $app->request->get('code');
		$redirect_url = $app->request->getUrl().$_SERVER['REQUEST_URI'];
		if($state == null && $code == null) {
			$state = mt_rand();
			$app->setCookie('state', $state);
			$app->redirect(GITHUB_AUTH_URL.'?client_id='.GITHUB_CLIENT_ID.'&redirect_uri='.urlencode($redirect_url).'&scope='.APP_AUTH_SCOPE.'&state='.$state);
		} elseif ($state == $app->getCookie('state')){
			$data = array( 
				"client_id" 	=> GITHUB_CLIENT_ID, 
				"client_secret" => GITHUB_CLIENT_SECRET, 
				"redirect_uri" 	=> $redirect_url, 
				"code"      	=> $code
			);
			$access_token_response = $app->github->post(GITHUB_ACCESS_TOKEN_URL, $data);
			if(array_key_exists('error', $access_token_response)){
				$error = $access_token_response['error'];
				echo $error; // TODO application error while calling
			}else{
				$access_token = $access_token_response['access_token'];
				$scope = $access_token_response['scope'];
				$token_type = $access_token_response['token_type'];
				if(APP_AUTH_SCOPE != $scope) {
					return; // TODO auth error
				}
				$app->setCookie('access_token', $access_token); // TODO store other information
				$app->github->setAccessToken($access_token);
				$userInfo = $app->github->get(GITHUB_USER_URL);
				print_r($userInfo);
			}
		} else {
			$app->response->setStatus(400);
		}
	}
);

$app->get(
	'/test',
	function() use ($app) {
		
	}
);

$app->run();