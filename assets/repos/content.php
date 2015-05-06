<?php require_once dirname(__FILE__). '/../header.php';?>

<h1>CONTENTS</h1>
<table class="table">
	<thead>
	</thead>
	<tbody>
		<?php foreach($contents as $content) {?>
		<tr data-name="<?=$content['name']?>" data-html_url="<?=$content['html_url']?>">
			<td><?=$content['name']?></td>
			<td><a href="<?=$content['html_url']?> "> <?=$content['html_url']?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
	//$userInDb=$_SESSION['userInDb'];
	//var_dump($userInDb);
	//echo $userInDb;
	
	//echo "in content php"; 
	var_dump($contents);
	//$contents=$_SESSION['contents'];
	//var_dump($contents[0]);
	
	
?>
<?php require_once dirname(__FILE__). '/../footer.php';?>