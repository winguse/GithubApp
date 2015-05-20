<?php require_once dirname(__FILE__). '/../header.php';?>
<a href="<?=APP_BASE_PATH?>/user/profile"><span class="glyphicon glyphicon-user"></span> Your Profile</a>
<table class="table">
	<thead>
		<tr>
			<th>Major Id</th>
			<th>Major name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($majors as $major) {?>
		<tr>
			<form class="form-horizontal" role="form" id="profile-form" action="/app/github/repos/create/create" method="post">
				<td>
					<div class="form-group"><!--"winguse.com/app/github/repos/create/create"-->
						<div class="col-sm-7">
							<input name="major_id" type="number" class="form-control" id="major_id" value="<?=$major->id?>" disabled="disabled">
						</div>
					</div>
				</td>
				<td>
					<div class="form-group"><!--"winguse.com/app/github/repos/create/create"-->
						<div class="col-sm-7">
							<input name="repository_name" type="text" class="form-control" id="inputRepositoryName" value="<?=$major->name?>">
						</div>
					</div>
				</td>
				<td>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-7">
							<button type="submit" class="btn btn-primary">Add</button>
						</div>
					</div>
				</td>
			</form>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php require_once dirname(__FILE__). '/../footer.php';?>