<?php
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