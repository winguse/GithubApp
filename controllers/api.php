<?php
$app->group(
	'/api',
	function () use ($app) {$app->response->headers->set('Content-Type', 'application/json');},
	function () use ($app) {
		$app->group(
			'/majors',
			function () use ($app) {
				$app->get( // get a major, or a list of majors
					'(/:id)',
					function($id = -1) use ($app){
						$major = new Major();
						if($id != -1) $major->id = $id;
						echo json_encode($app->dao->find($major));
					}
				);
				$app->put( // add a new major
					'/',
					authenticate(PERMISSION_ADMIN),
					function() use ($app){
						$major = new Major();
						$major->name = $app->request->put('name');
						echo json_encode(array('code' => $app->dao->add($major)));
					}
				);
				$app->delete( // delete a major
					'/:id',
					authenticate(PERMISSION_ADMIN),
					function($id) use ($app){
						$major = new Major();
						$major->id = $id;
						echo json_encode(array('code' => $app->dao->delete($major)));
					}
				);
				$app->post( // update a existing major
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
				$app->post( // update ogined users' profiles
					'/profile',
					authenticate(PERMISSION_USER),
					function() use ($app){
						return profile($app);
					}
				);
				$app->delete( // update ogined users' profiles
					'/:id',
					authenticate(PERMISSION_ADMIN),
					function($id) use ($app){
						$user = new User();
						$user->id = $id;
						echo json_encode(array('code' => $app->dao->delete($user)));
					}
				);
				$app->post(
					'/add',
					authenticate(PERMISSION_USER),
					function() use ($app){
						
					}
				);
			}
		);
	}
);