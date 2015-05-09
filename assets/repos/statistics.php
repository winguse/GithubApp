<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Statistics</h1>
</div>
<table class="table">
	<thead>
		<tr>
			<th>User</th>
			<th>Addition</th>
			<th>Deletion</th>
			<th>Commit</th>
			<th>Time</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($statistic_array as $statistic) {?>
		<tr>
			<td><?= $statistic['login'] ?></td>
			<td><?= $statistic['addition'] ?></td>
			<td><?=$statistic['deletion'] ?></td>
			<td><?=$statistic['commit'] ?></td>
			<td><?=$statistic['Time'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>