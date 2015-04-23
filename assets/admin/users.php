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
<div id="content">
</div>
<script>
var page = "admin_users";
var users = <?=json_encode($users)?>;
</script>
<?php require_once dirname(__FILE__). '/../footer.php';?>