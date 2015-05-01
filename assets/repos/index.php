<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Create</h1>
</div>
<form class="form-horizontal" role="form" id="profile-form" action="/app/github/repos/create/create" method="post">
	<div class="form-group"><!--"winguse.com/app/github/repos/create/create"-->
		<label for="inputRepositoryName" class="col-sm-3 control-label">Resposity Name</label>
		<div class="col-sm-7">
			<input name="repository_name" type="text" class="form-control" id="inputRepositoryName" placeholder="repository Name">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-7">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
</form>
<?php require_once dirname(__FILE__). '/../footer.php';?>