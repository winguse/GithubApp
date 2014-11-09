<?php require_once 'header.php';?>
<div class="alert <?=$alert_type?>" role="alert">
    <strong><?=$process_name?></strong>
    <p>If your broswer doesn't refresh, please <a href="<?=$url?>">click here</a>.</p>
</div>
<script>window.location="<?=$url?>";</script>
<?php require_once 'footer.php';?>