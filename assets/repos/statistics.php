<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Statistics</h1>
</div>
<table class="table">
	<thead>
		<tr>
			<th>Id</th>
			<th>Major name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($statistic_array as $statistic) {?>
		<tr>
			<td><?= $statistic->login ?></td>
			<td><?=$statistic->Time ?></td>
			<td><?= $statistic->addition ?></td>
			<td><?=$statistic->Time ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>