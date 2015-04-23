<?php
$app->group(
	'/user',
	authenticate(PERMISSION_USER),
	function() use ($app) {
		$app->get(
			'/',
			function () use ($app) {
				$user_major = new User_Major();
				$user = $app->user;
				$user_major->user_id = $user->id;
				/**echo "in view user user->id=";
				var_dump($user);
				echo "\\n in view user user-majors=";
				$user_majorArr = $app->dao->find($user_major);
				var_dump($user_majorArr);**/
				$app->render('/user/index.php',array('user_major' => $app->dao->find($user_major)));
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