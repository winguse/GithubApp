<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Majors</h1>
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
		<?php foreach($majors as $major) {?>
		<tr data-id="<?=$major->id?>" data-name="<?=$major->name?>">
			<td><?=$major->id?></td>
			<td><?=$major->name?></td>
			<td>
				<button type="button" class="btn btn-xs major-edit" title="Edit">
				    <span class="glyphicon glyphicon-edit"></span>
				    <span class="sr-only">Edit</span>
				</button>
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
<div class="modal fade" id="major-editor" tabindex="-1" role="dialog" aria-labelledby="major-editor-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="major-editor-label"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger" role="alert" id="major-editor-error"></div>
				<form class="form-horizontal" role="form">
					<div class="form-group" id="major-id-group">
						<label for="major-id" class="col-sm-3 control-label">Major ID</label>
						<div class="col-sm-9">
							<input type="number" class="form-control" id="major-id" placeholder="Major ID" disabled="disabled">
						</div>
					</div>
					<div class="form-group">
						<label for="major-name" class="col-sm-3 control-label">Major Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="major-name" placeholder="Major Name">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="major-editor-save">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
var page = "admin_majors";
</script>
<?php require_once dirname(__FILE__). '/../footer.php';?>