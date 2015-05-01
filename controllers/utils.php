<?php

$app->get(
	'/',
	function () use ($app){
		$username = $app->getCookie('username');
		$app->render('index.php', array('username' => $username));
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
				"code"          => $code
			);
			$access_token_response = $app->github->post(GITHUB_ACCESS_TOKEN_URL, $data);
			//$_SESSION['$access_token_response']=$access_token_response;
			//var_dump($access_token_response);
			if(array_key_exists('error', $access_token_response)){
				$error = $access_token_response['error'];
				echo $error; // TODO application error while calling
			}else{
				$access_token = $access_token_response['access_token'];
				$scope = $access_token_response['scope'];
				echo "the scope".$scope;
				$token_type = $access_token_response['token_type'];
				if(APP_AUTH_SCOPE != $scope) {
					return; // TODO auth error
				}
				$app->github->setAccessToken($access_token);
				$userInfo = $app->github->get(GITHUB_USER_URL);
				$user = new User();
				$user->github_id = $userInfo['id'];
				$userInDb = $app->dao->find($user);
				$_SESSION['userInDb'] = json_encode($userInDb);
				$isNewUser = true;
				if($userInDb != null && count($userInDb) > 0){
					$user = $userInDb[0];
					$isNewUser = false;
				} else {
					$user->role = 1;
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
				$user = new User();//为啥重新new user??查出add或者update后的user
				$user->github_id = $userInfo['id'];
				$user = $app->dao->find($user)[0];
				$_SESSION['user'] = json_encode($user);
				//$_SESSION['$access_token_response']=$access_token_response;
				$app->redirect(APP_BASE_PATH.($isNewUser ? '/user/profile' : '/user'));
			}
		}
		$state = $app->request->params('state');
		$code = $app->request->get('code');
		if($state == null && $code == null) {
			$state = mt_rand();
			$_SESSION['state']= $state;
			//https://winguse.com/app/github/auth?code=48976a3883761c3e7150&state=652470324
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