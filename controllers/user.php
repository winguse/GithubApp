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
			function () use ($app){
				$app->redirect(APP_BASE_PATH.'/user/'.$app->user->id);
			}
		);
		$app->get(
			'/:id',
			function ($id) use ($app) {
				if($app->user->id != $id){
					if(($app->user->role & PERMISSION_ADMIN) != PERMISSION_ADMIN){
						$app->flash('error', 'You can only edit the profile of yourself.');
	        			$app->redirect('/user/'.$app->user->id);
	        			return;
					}else{
						$user = new User();
						$user->id = $id;
						$userArr = $app->dao->find($user);
						if(count($userArr) == 0){
							$app->response->setStatus(404);
							return;
						}
						$user = $userArr[0];
					}
				}else{
					$user = $app->user;
				}
				$ref = new ReflectionClass('BasicUser');
				$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
				$userEditableInfo = array();
				foreach($properties as $property) {
					$userEditableInfo[$property->getName()] = $property->getValue($user);
				}
				$major = new Major();
				$majors = $app->dao->find($major);
				$app->render('/user/profile.php', array('userEditableInfo' => $userEditableInfo, 'majors' => $majors));
			}
		);
	}
);