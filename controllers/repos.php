<?php
$app->group(
	'/repos',
	function () use ($app) {
		$app->group(
			'/content',
			function() use ($app) {
				$app->get(
					'/',
					function() use ($app){
						//if user is not in session return to auth;
						//$access_token=$app->user->github_access_token;
						//echo "github_access_token=".$access_token;
						//$app->github->setAccessToken($access_token);
						$contents = $app->github->get('https://api.github.com/repos/typecho-fans/plugins/contents/');
						//$_SESSION['contents']=$contents;
						$app->render('/repos/content.php' , array('contents'=>$contents));
					}
				);/**
				$app->get(
					'/file',
					function() use ($app){
						$file = $app->github->get('https://api.github.com/repos/typecho-fans/plugins/contents/GoLinks/Plugin.php');
						$app->render();
					}
				);**/
			}
		);
	}
);