<?php

function profile($app){
	$verifyRules = array(
		'real_name' => array(
			'parse' => function($real_name){
				return preg_match(APP_REAL_NAME_REGEX, $real_name) ? $real_name : FALSE;
			},
			'error_message' => 'Real name is not acceptable, expected Chinese characters.'
		),
		'sex' => array(
			'parse' => function($sex){
				return $sex == '0' ? 0 : 1;
			}
		),
		'grade' => array(
			'parse' => function($grade){
				return filter_var($grade, FILTER_VALIDATE_INT, array(
					"options" => array("min_range" => APP_GRADE_MIN, "max_range" => APP_GRADE_MAX))
				);
			},
			'error_message' => 'Grade should be an integer between '. APP_GRADE_MIN.' and ' . APP_GRADE_MAX . '.'
		),
		'student_id' => array(
			'parse' => function($student_id){
				return (!is_numeric($student_id) || strlen($student_id) != APP_STUDENT_ID_LENGTH) ? FALSE : intval($student_id);
			},
			'error_message' => 'Student ID should be an integer with ' . APP_STUDENT_ID_LENGTH . ' in length.'
		),
		'email' => array(
			'parse' => function($email){
				return filter_var($email, FILTER_VALIDATE_EMAIL);
			},
			'error_message' => 'Invalid email address.'
		),
		'major_id' => array(
			'parse' => function($major_id) use ($app) {
				$major_id = filter_var($major_id, FILTER_VALIDATE_INT, array(
					"options"=> array("min_range" => 0, "max_range" => PHP_INT_MAX))
				);
				if($major_id === FALSE) return FALSE;
				$major = new Major();
				$major->id = $major_id;
				$majors = $app->dao->find($major);
				if(count($majors) == 0) return FALSE;
				return $major_id;
			},
			'error_message' => 'Invalid major ID.'
		)
	);
	
	$verified_data = array();
	foreach($verifyRules as $field => $rule){
		$value = $app->request->post($field);
		$value = $rule['parse']($value);
		if($value === FALSE){
			echo json_encode(array('code' => 1, 'field' => $field, 'message' => $rule['error_message']));
			return;
		}
		$verified_data[$field] = $value;
	}
	foreach($verified_data as $field => $value){
		$app->user->{$field} = $value;
	}
	$app->dao->update($app->user);
	$_SESSION['user'] = json_encode($app->user);
	echo json_encode(array('code' => 0));
}