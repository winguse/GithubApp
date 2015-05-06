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
						$repository_name=trim($repository_name);
						$repository_description= "hahahahah";//$app->request->post('repository_description');
						$data='{"name": '.' "'.$repository_name.'" '.','.
						' "description": "$repository_description",
							  "homepage": "https://github.com",
							  "private": false,
							  "has_issues": true,
							  "has_wiki": true,
							  "has_downloads": true
						}';
						$access_token = $app->user->github_access_token;
						$app->github->setAccessToken($access_token);
						$repos_create_response = $app->github->post(GITHUB_REPOS_URL,$data);
						if(array_key_exists('message' , $repos_create_response)){
							$message = $repos_create_response['message'];
							echo $message;
						}else{
							$owner = $repos_create_response['owner'];
						}
						echo "create repository success!!!";
						//var_dump($repos_create_response) ;
						$app->render('repos/create.php',array('repository_name'=>$repository_name));
					}
				);
			}
		);
		$app->group(
			'/statistics',
			function () use($app){
				$app->get(
					'/total',
					function () use ($app){
						$results=$app->github->get(GITHUB_STATISTIAC_URL.'/winguse/GithubApp/stats/contributors');
						/*Unix timestamp → 普通时间 PHP date('r', Unix timestamp)*/
						//var_dump($res);
						$statistic_array = array();
						foreach ($results as $result) {
							$athuor = $result['athuor'];
							$login = $athuor['login'];
							$total = $result['total'];
							$weeks = $reslut['weeks'];
							foreach ($weeks as $week) {
								$time = $week['w'];
								$time = date('r',$time);
								$addition = $week['a'];
								$deletion = $week['d'];
								$commit = $week['c'];
								$flag = $addition||$deletion||$commit;
								if($flag == false) continue;
								$tmp = array('login'=>$login,'Time'=>$time, 'addition'=>$addition, 'deletion'=>$deletion,'commit'=>$commit);
								$statistic_array[] = $tmp; 
							}
						}
						$app->render('/repos/statistics.php' , array('statistic_array' =>$statistic_array) );
					}
				);
			}
		);
	}
);