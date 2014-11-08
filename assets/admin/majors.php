<?php require_once dirname(__FILE__).'/../header.php';?>
      <div class="page-header">
        <h1>Majors</h1>
      </div>
      <table class="table">
      <thead>
        <tr>
          <th>Id</th>
          <th>Major name</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($majors as $major) {?>
        <tr>
          <td><?=$major->getId()?></td>
          <td><?=$major->getName()?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
<?php require_once dirname(__FILE__).'/../footer.php';?>