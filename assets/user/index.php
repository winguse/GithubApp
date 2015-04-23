<?php require_once dirname(__FILE__). '/../header.php';?>



<a href="<?=APP_BASE_PATH?>/user/profile"><span class="glyphicon glyphicon-user"></span> Your Profile</a>
<table class="table">
	<thead>
		<tr>
			<th>UserId</th>
			<th>Major name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($user_major as $user_major) {?>
			<tr data-id="<?=$user_major->user_id?>" data-name="<?=$user_major->major_name ?>">
			<td><?=$user_major->user_id?></td>
			<td><?=$user_major->major_name?></td>
			<td>
				<button type="button" class="btn btn-xs major-delete" title="Delete">
				    <span class="glyphicon glyphicon-trash"></span>
				    <span class="sr-only">Delete</span>
				</button>
			</td>
			</tr>
		<?php } ?>
		<tr>
			<td></td>
			<td></td>
			<td>
				<button type="button" class="btn btn-xs" title="Add" id="major-add">
				    <span class="glyphicon glyphicon-plus"></span>
				    <span class="sr-only">Add</span>
				</button>
			</td>
		</tr>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>