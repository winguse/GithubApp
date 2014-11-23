<?php

$app->group(
	'/admin',
	authenticate(PERMISSION_ADMIN),
	function () use ($app){
		$app->get(
			'/',
			function () use ($app) {
				$app->render('/admin/index.php');
			}
		);
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
