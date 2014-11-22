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
	'api_path' => APP_BASE_PATH.'/api',
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
					authenticate(PERMISSION_ADMIN),
					function() use ($app){
						$major = new Major();
						$major->name = $app->request->put('name');
						echo json_encode(array('code' => $app->dao->add($major)));
					}
				);
				$app->delete(
					'/:id',
					authenticate(PERMISSION_ADMIN),
					function($id) use ($app){
						$major = new Major();
						$major->id = $id;
						echo json_encode(array('code' => $app->dao->delete($major)));
					}
				);
				$app->post(
					'/:id',
					authenticate(PERMISSION_ADMIN),
					function($id) use ($app){
						$major = new Major();
						$major->id = $id;
						$major->name = $app->request->post('name');
						echo json_encode(array('code' => $app->dao->update($major)));
					}
				);
			}
		);
		$app->group(
			'/user',
			function () use ($app){
				$app->post(
					'/profile',
					authenticate(PERMISSION_USER),
					function() use ($app){
						$verifyRules = array(
							'real_name' => array(
								'parse' => function($real_name){
									return preg_match(APP_REAL_NAME_REGEX, $real_name) ? $real_name : FALSE;
								},
								'error_message' => 'Real name is not acceptable, expected Chinese characters.'
							),
							'sex' => array(
								'parse' => function($sex){
									return $sex == '0' ? 0 : 1;
								}
							),
							'grade' => array(
								'parse' => function($grade){
									return filter_var($grade, FILTER_VALIDATE_INT, array(
										"options" => array("min_range" => APP_GRADE_MIN, "max_range" => APP_GRADE_MAX))
									);
								},
								'error_message' => 'Grade should be an integer between '. APP_GRADE_MIN.' and ' . APP_GRADE_MAX . '.'
							),
							'student_id' => array(
								'parse' => function($student_id){
									return (!is_numeric($student_id) || strlen($student_id) != APP_STUDENT_ID_LENGTH) ? FALSE : intval($student_id);
								},
								'error_message' => 'Student ID should be an integer with ' . APP_STUDENT_ID_LENGTH . ' in length.'
							),
							'email' => array(
								'parse' => function($email){
									return filter_var($email, FILTER_VALIDATE_EMAIL);
								},
								'error_message' => 'Invalid email address.'
							),
							'major_id' => array(
								'parse' => function($major_id) use ($app) {
									$major_id = filter_var($major_id, FILTER_VALIDATE_INT, array(
										"options"=> array("min_range" => 0, "max_range" => PHP_INT_MAX))
									);
									if($major_id === FALSE) return FALSE;
									$major = new Major();
									$major->id = $major_id;
									$majors = $app->dao->find($major);
									if(count($majors) == 0) return FALSE;
									return $major_id;
								},
								'error_message' => 'Invalid major ID.'
							)
						);
						
						$verified_data = array();
						foreach($verifyRules as $field => $rule){
							$value = $app->request->post($field);
							$value = $rule['parse']($value);
							if($value === FALSE){
								echo json_encode(array('code' => 1, 'field' => $field, 'message' => $rule['error_message']));
								return;
							}
							$verified_data[$field] = $value;
						}
						foreach($verified_data as $field => $value){
							$app->user->{$field} = $value;
						}
						$app->dao->update($app->user);
						$_SESSION['user'] = json_encode($app->user);
						echo json_encode(array('code' => 0));
					}
				);
			}
		);
	}
);

$app->group(
	'/admin',
	authenticate(PERMISSION_ADMIN),
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
	authenticate(PERMISSION_USER),
	function() use ($app) {
		$app->get(
			'/',
			function () use ($app) {
				$app->render('/user/index.php');
			}
		);
		$app->get(
			'/profile',
			function () use ($app) {
				$ref = new ReflectionClass('BasicUser');
				$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
				$userEditableInfo = array();
				foreach($properties as $property) {
					$userEditableInfo[$property->getName()] = $property->getValue($app->user);
				}
				$major = new Major();
				$majors = $app->dao->find($major);
				$app->render('/user/profile.php', array('userEditableInfo' => $userEditableInfo, 'majors' => $majors));
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
				"code"	  	=> $code
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
				$user->github_name = isset($userInfo['name']) ? $userInfo['name'] : '';
				$user->github_location = isset($userInfo['location']) ? $userInfo['location'] : '';
				if($user->email != null && $user->email != '')
					$user->email = isset($userInfo['email']) ? $userInfo['email'] : '';
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