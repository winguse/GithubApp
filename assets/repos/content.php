<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="page-header">
	<h1>CONTENTS</h1>
</div>
<div id="content1">
</div>
<script>
var page = "list";
var repos_list = <?=json_encode($repos_list)?>;
</script>
<?php require_once dirname(__FILE__). '/../footer.php';?>