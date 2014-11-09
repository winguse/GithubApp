<?php
session_start();
require_once 'Slim/Slim.php';
require_once 'config.php';
require_once 'libs.php';
require_once 'models/index.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
	'templates.path' => dirname(__FILE__).'/assets/',
	'cookies.path' => APP_BASE_PATH,
	'cookies.domain' => APP_DOMAIN,
	'cookies.lifetime' => '30 days',
	'cookies.secure' => true,
	'cookies.httponly' => true,
	'cookies.encrypt' => true,
	'cookies.secret_key' => APP_SECRET_KEY,
	'debug' => true,
	'mode' => 'development'
));
\Slim\Route::setDefaultConditions(array(
    'id' => '\\d+'
));
$app->view->setData(array(
	'assets_path' => APP_BASE_PATH.'/assets',
	'site_name' => APP_NAME
));

$app->container->singleton('pdo', function () {
    return new PDO('mysql:dbname='.APP_DB_NAME.';host='.APP_DB_HOST, APP_DB_USER, APP_DB_PASSWORD);
});
$app->container->singleton('mcrypt', function () {
    return new McryptWrapper();
});
$app->container->singleton('dao', function () use ($app) {
    return new Dao($app->pdo);
});
$app->container->singleton('user', function () use ($app) {
	if(!isset($_SESSION['user'])) return null;
	$user = new User();
	foreach (json_decode($_SESSION['user'], true) AS $key => $value) {
		$user->{$key} = $value;
	}
	return $user;
});
$app->container->singleton('github', function () use ($app) {
	$access_token = $app->user == null ? null : $app->user->github_access_token;
	return new GithubApiCaller($access_token);
});

$app->get(
	'/',
	function () use ($app){
		$username = $app->getCookie('username');
		$app->render('index.php', array('username' => $username));
	}
);


$app->group(
	'/api',
	function () use ($app) {$app->response->headers->set('Content-Type', 'application/json');},
	function () use ($app) {
		$app->group(
			'/majors',
			function () use ($app) {
				$app->get(
					'(/:id)',
					function($id = -1) use ($app){
						$major = new Major();
						if($id != -1) $major->id = $id;
						echo json_encode($app->dao->find($major));
					}
				);
				$app->put(
					'/',
					authenticate(ROLE_ADMIN),
					function() use ($app){
						$major = new Major();
						$major->name = $app->request->put('name');
						echo json_encode(array('code' => $app->dao->add($major)));
					}
				);
				$app->delete(
					'/:id',
					authenticate(ROLE_ADMIN),
					function($id) use ($app){
						$major = new Major();
						$major->id = $id;
						echo json_encode(array('code' => $app->dao->delete($major)));
					}
				);
				$app->post(
					'/:id',
					authenticate(ROLE_ADMIN),
					function($id) use ($app){
						$major = new Major();
						$major->id = $id;
						$major->name = $app->request->post('name');
						echo json_encode(array('code' => $app->dao->update($major)));
					}
				);
			}
		);
	}
);

$app->group(
	'/admin',
	authenticate(ROLE_ADMIN),
	function () use ($app){
		$app->group(
			'/majors',
			function () use ($app){
				$app->get(
					'/',
					function() use ($app){
						$filter = new Major();
						$app->render('/admin/majors.php', array('majors' => $app->dao->find($filter)));
					}
				);
			}
		);
	}
);

$app->group(
    '/user',
    function() use ($app) {
        $app->get(
            '/',
            function () use ($app) {
                print_r($app->user);
            }
        );
        $app->get(
            '/profile',
            function () use ($app) {
                print_r($app->user);
            }
        );
    }
);

$app->get(
	'/auth',
	function () use ($app){
	    unset($_SESSION['user']);
		$redirect_url = $app->request->getUrl().$_SERVER['REQUEST_URI'];
	    if(isset($_SESSION['code'])){
	        $code = $_SESSION['code'];
	        unset($_SESSION['code']);
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
    			$app->github->setAccessToken($access_token);
    			$userInfo = $app->github->get(GITHUB_USER_URL);
    			$user = new User();
    			$user->github_id = $userInfo['id'];
    			$userInDb = $app->dao->find($user);
    			$isNewUser = true;
    			if($userInDb != null && count($userInDb) > 0){
    			    $user = $userInDb[0];
    			    $isNewUser = false;
    			} else {
    			    $user->role = 0;
    			    $user->grade = 0;
    			    $user->real_name = '';
    			    $user->student_id = 0;
    			    $user->sex = false;
    			    $user->major_id = null;
    			}
    			$user->github_id = $userInfo['id'];
    			$user->github_login = $userInfo['login'];
    			$user->github_name = $userInfo['name'];
    			$user->github_location = $userInfo['location'];
    			$user->github_email = $userInfo['email'];
    			$user->github_create_at = strtotime($userInfo['created_at']);
    			$user->github_updated_at = strtotime($userInfo['updated_at']);
    			$user->github_access_token = $access_token;
    			if($isNewUser){
    			    $app->dao->add($user);
    			}else{
    			    $app->dao->update($user);
    			}
    			$user = new User();
    			$user->github_id = $userInfo['id'];
    			$user = $app->dao->find($user)[0];
    			$_SESSION['user'] = json_encode($user);
    			$app->redirect(APP_BASE_PATH.($isNewUser ? '/user/profile' : '/user'));
			}
	    }
		$state = $app->request->params('state');
		$code = $app->request->get('code');
		if($state == null && $code == null) {
			$state = mt_rand();
			$_SESSION['state']= $state;
		    $app->render('/auth.php', array('alert_type' => 'alert-info', 'process_name' => 'Redirecting you to github.com ...', 'url' => GITHUB_AUTH_URL.'?client_id='.GITHUB_CLIENT_ID.'&redirect_uri='.urlencode($redirect_url).'&scope='.APP_AUTH_SCOPE.'&state='.$state));
		} elseif ($state == $_SESSION['state']){
		    unset($_SESSION['state']);
		    $_SESSION['code'] = $code;
		    $app->render('/auth.php', array('alert_type' => 'alert-success', 'process_name' => 'Processing...', 'url' => APP_BASE_PATH.'/auth'));
		} else {
			$app->response->setStatus(400);
		}
	}
);

$app->get(
	'/test',
	function() use ($app) {
		$str = $app->mcrypt->encrypt('test');
		echo $str. ' ' . $app->mcrypt->decrypt($str);
	}
);

$app->run();