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
					'/contributors',
					function () use ($app){
						$results=$app->github->get(GITHUB_STATISTIAC_URL.'/winguse/GithubApp/stats/contributors');
						/*Unix timestamp → 普通时间 PHP date('r', Unix timestamp)*/
						//var_dump($results);
						$statistic_array = array();
						foreach ($results as $result) {
							$athuor = $result['author'];
							$login = $athuor['login'];
							$total = $result['total'];
							$weeks = $result['weeks'];
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
						//var_dump($statistic_array);
						$app->render('/repos/statistics.php' , array('statistic_array' =>$statistic_array) );
					}
				);
				$app->get(
					'/commitActivity',
					function () use ($app){
						$results_commit_activity = $app->github->get(GITHUB_STATISTIAC_URL.'/winguse/GithubApp/stats/commit_activity');
						$results_code_frequency = $app->github->get(GITHUB_STATISTIAC_URL.'/winguse/GithubApp/stats/code_frequency');
						//var_dump($results_code_frequency);
						$commit_activity =  array();
						$code_frequency = array();
						foreach ($results_commit_activity as $result) {
							$total_commit = $result['total'];
							if($total_commit == 0) continue;
							$week = $result['week'];
							$week = date('r',$week);
							$commit_SUN = $result['days'][0];
							$commit_MON = $result['days'][1];
							$commit_TUE = $result['days'][2];
							$commit_WED = $result['days'][3];
							$commit_THU = $result['days'][4];
							$commit_FRI = $result['days'][5];
							$commit_SAT = $result['days'][6];
							$commit_activity[] = array('SUN'=>$commit_SUN,'MON'=>$commit_MON, 'TUE'=>$commit_TUE, 
							'WED'=>$commit_WED,'THU'=>$commit_THU,'FRI'=>$commit_FRI,'SAT'=>$commit_SAT,'total'=>$total_commit,'week'=>$week);
						}
						$commit_activity[] = 0;
						foreach($results_code_frequency as $result) {
							//$week = $result[0];
							//$week = date('r',$week);
							$addition = $result[1];
							$deletion = $result[2];
							if($addition ==0 && $deletion == 0) continue;
							$code_frequency[]= array('addition'=>$addition,'deletion'=>$deletion);
						}
						$code_frequency[] = 0;
						$app->render('repos/commitActivity.php', array('commit_activity'=>$commit_activity,'code_frequency'=>$code_frequency));
					}
				);
			}
		);
	}
);