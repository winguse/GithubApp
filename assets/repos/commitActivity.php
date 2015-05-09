<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Statistics</h1>
</div>
<table class="table">
	<thead>
		<tr>
			<th>Sunday</th>
			<th>Monday</th>
			<th>Tuesday</th>
			<th>Wednesday</th>
			<th>Thursday</th>
			<th>Friday</th>
			<th>Saturday</th>
			<th>TotalCommit</th>
			<th>Week</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($commit_activity as $statistic) {?>
		<tr>
			<td><?= $statistic['SUN'] ?></td>
			<td><?= $statistic['MON'] ?></td>
			<td><?=$statistic['TUE'] ?></td>
			<td><?=$statistic['WED'] ?></td>
			<td><?=$statistic['THU'] ?></td>
			<td><?=$statistic['FRI'] ?></td>
			<td><?=$statistic['SAT'] ?></td>
			<td><?=$statistic['total'] ?></td>
			<td><?=$statistic['week'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>