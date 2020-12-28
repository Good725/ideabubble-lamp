<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Sprints in <?=$rapid_view['name']?></h2>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<table class="table table-striped dataTable" id="sprints_table">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
			<th scope="col">State</th>
            <th scope="col">Issues</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($rapid_view['sprints'] as $sprint): ?>
            <tr id="sprint_<?= $sprint['id'] ?>">
                <td><a href="<?php echo URL::Site('admin/extra/sprint?id=' . $sprint['id']); ?>"><?= $sprint['id'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/sprint?id=' . $sprint['id']); ?>"><?= $sprint['name'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/sprint?id=' . $sprint['id']); ?>"><?= $sprint['state'] ?></a></td>
				<td><a href="<?php echo URL::Site('admin/extra/sprint?id=' . $sprint['id']); ?>"><?= $sprint['issues'] ?></a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
