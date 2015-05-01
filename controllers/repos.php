<?php
$app->group(
	'/repos',
	function () use ($app) {
		$app->group(
			'/content',
			function() use ($app) {
				$app->get(
					'/:id',
					function($id) use ($app){
						$user = new User();
						$user->id = $id;
						$user = $app->dao->find($user)[0];
						$username=$user->github_login;
						$contents = $app->github->get('https://api.github.com/users/'.$username.'/repos');
						$app->render('/repos/content.php' , array('contents'=>$contents));
					}
				);
			}
		);
		$app->group(
			'/create',
			function() use ($app){
				$app->get(
					'/',
					function() use ($app){
						$app->render('/repos/index.php');
					}
				);
				$app->post(
					'/create',
					function() use ($app){
						$repository_name=$app->request->post('repository_name');
						echo $repository_name;
						$data = array( 
							"name"          =>$repository_name
						);
						$access_token = $app->user->github_access_token;
						$app->github->setAccessToken($access_token);
						$repos_create_response = $app->github->post(GITHUB_REPOS_URL,$data);
						if(array_key_exists('message' , $repos_create_response)){
							$message = $repos_create_response['message'];
							echo $message;
						}else{
							$owner = $repos_create_response['owner'];
							//var_dump($repos_create_response);
						}
						//var_dump($repos_create_response) ;
						$app->render('repos/create.php',array('repository_name'=>$repository_name));
					}
				);
			}
		);
	}
);