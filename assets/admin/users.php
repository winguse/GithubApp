<?php require_once dirname(__FILE__). '/../header.php';?>
<!--
    public $grade;
    public $real_name;
    public $student_id;
    public $sex;
    public $major_id;
    public $email;
    
    public $id;
    public $role;
    public $github_id;
    public $github_login;
    public $github_name;
    public $github_location;
    public $github_created_at;
    public $github_updated_at;
    public $github_access_token;
-->
<div class="page-header">
	<h1>Users</h1>
</div>
<table class="table">
	<thead>
		<tr>
			<th>Id</th>
			<th>Role</th>
			<th>Github Info</th>
			<th>Real name</th>
			<th>Grade</th>
			<th>Student ID</th>
			<th>Major</th>
			<th>Email</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $user) {?>
		<tr data-id="<?=$user->id?>">
			<td><?=$user->id?></td>
			<td><?=$major->github_login?></td>
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
	</tbody>
</table>
<script>
var page = "admin_users";
</script>
<?php require_once dirname(__FILE__). '/../footer.php';?>