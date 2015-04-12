<?php
session_start();
require_once 'Slim/Slim.php';
require_once 'config.php';
require_once 'libs/index.php';
require_once 'models/index.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
	'templates.path' => dirname(__FILE__).'/assets/',
	'cookies.path' => APP_BASE_PATH,
	'cookies.domain' => APP_DOMAIN,
	'cookies.lifetime' => '30 days',
	'cookies.secure' => true,
	'cookies.httponly' => true,
	'cookies.encrypt' => true,
	'cookies.secret_key' => APP_SECRET_KEY,
	'debug' => true,
	'mode' => 'development'
));
\Slim\Route::setDefaultConditions(array( 
	'id' => '\\d+'
));
$app->view->setData(array( //inject data into the view object. usually pass data to the view with the Slim application’s `render()` method
	'assets_path' => APP_BASE_PATH.'/assets',
	'api_path' => APP_BASE_PATH.'/api',
	'site_name' => APP_NAME
));

$app->container->singleton('pdo', function () { //单例模式 Every time you request the pdo resource with $app->pdo, it will return the same instance
	return new PDO('mysql:dbname='.APP_DB_NAME.';host='.APP_DB_HOST, APP_DB_USER, APP_DB_PASSWORD);
});
$app->container->singleton('mcrypt', function () {
	return new McryptWrapper();
});
$app->container->singleton('dao', function () use ($app) {
	return new Dao($app->pdo);
});
$app->container->singleton('user', function () use ($app) {
	if(!isset($_SESSION['user'])) return null;
	$user = new User();
	foreach (json_decode($_SESSION['user'], true) AS $key => $value) {
		$user->{$key} = $value;
	}
	return $user;
});
$app->container->singleton('github', function () use ($app) {
	$access_token = $app->user == null ? null : $app->user->github_access_token;
	return new GithubApiCaller($access_token);
});

require_once 'controllers/api.php';
require_once 'controllers/admin.php';
require_once 'controllers/user.php';
require_once 'controllers/utils.php';

$app->run();