<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>Your profile</h1>
</div>
<div role="alert" id="message"></div>
<form class="form-horizontal" role="form" id="profile-form" action="<?=$api_path ?>/user/profile" method="post">
	<div class="form-group">
		<label for="inputRealName" class="col-sm-3 control-label">Real Name</label>
		<div class="col-sm-7">
			<input name="real_name" type="text" class="form-control" id="inputRealName" placeholder="Real Name">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Sex</label>
		<div class="col-sm-7">
			<label class="checkbox-inline">
				<input type="radio" name="sex" id="optionsRadios1" value="1"> Male
			</label>
			<label class="checkbox-inline">
				<input type="radio" name="sex" id="optionsRadios2" value="0"> Female
			</label>
		</div>
	</div>
	<div class="form-group">
		<label for="inputGrade" class="col-sm-3 control-label">Grade</label>
		<div class="col-sm-7">
			<select name="grade" class="form-control" id="inputGrade">
			    <option value="0">Please select your grade</option>
			    <?php for($grade = APP_GRADE_MIN; $grade <= APP_GRADE_MAX; $grade++){?>
			    <option value="<?=$grade?>"><?=$grade?></option>
			    <?php }?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="inputStudentId" class="col-sm-3 control-label">StudentId</label>
		<div class="col-sm-7">
			<input name="student_id" type="number" class="form-control" id="inputStudentId" placeholder="StudentId">
		</div>
	</div>
	<div class="form-group">
		<label for="inputEmail" class="col-sm-3 control-label">Email</label>
		<div class="col-sm-7">
			<input name="email" type="email" class="form-control" id="inputEmail" placeholder="Email">
		</div>
	</div>
	<div class="form-group">
		<label for="inputMajor" class="col-sm-3 control-label">Major</label>
		<div class="col-sm-7">
			<select name="major_id" class="form-control" id="inputMajor">
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-7">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
</form>
<script>
var userInfo = <?=json_encode($userEditableInfo) ?>;
var majors = <?=json_encode($majors) ?>;
var page = 'profile';
</script>
<?php require_once dirname(__FILE__). '/../footer.php';?>