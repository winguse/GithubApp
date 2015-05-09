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
			<th>Addition</th>
			<th>deletion</th>
			<th>Week</th>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $commit_activity[$i]!=0, $code_frequency[$i]!=0; $i++) {?>
		<tr>
			<td><?= $commit_activity[$i]['SUN'] ?></td>
			<td><?= $commit_activity[$i]['MON'] ?></td>
			<td><?=$commit_activity[$i]['TUE'] ?></td>
			<td><?=$commit_activity[$i]['WED'] ?></td>
			<td><?=$commit_activity[$i]['THU'] ?></td>
			<td><?=$commit_activity[$i]['FRI'] ?></td>
			<td><?=$commit_activity[$i]['SAT'] ?></td>
			<td><?=$commit_activity[$i]['total'] ?></td>
			<td><?=$code_frequency[$i]['addition'] ?></td>
			<td><?=$code_frequency[$i]['deletion'] ?></td>
			<td><?=$commit_activity[$i]['week'] ?></td>

		</tr>
		<?php } ?>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>