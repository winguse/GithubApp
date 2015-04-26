<?php require_once 'header.php';?>
<div class="page-header">
<h1><?=APP_NAME ?></h1>
</div>
<a href="auth">auth</a>
<!--echo "role=".<?=$role ?>; -->
<?php 
	$app = \Slim\Slim::getInstance();
//	echo "user->role=".$app->user->role;
?>
<?php require_once 'footer.php';?>