<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Rapid Views</h2>
			<div class="pull-left"><a href='/admin/extra/rapid_views_sync_jira'>Syncronize With Jira</a></div>
			<div class="pull-right"><a href='/admin/extra/sprints_report'>Sprints Report</a></div>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<table class="table table-striped dataTable" id="rapid_views_table">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
            <th scope="col">Sprints</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($rapid_views as $rapid_view): ?>
            <tr id="rapid_view_<?= $rapid_view['id'] ?>">
                <td><a href="<?php echo URL::Site('admin/extra/rapid_view?id=' . $rapid_view['id']); ?>"><?= $rapid_view['id'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/rapid_view?id=' . $rapid_view['id']); ?>"><?= $rapid_view['name'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/rapid_view?id=' . $rapid_view['id']); ?>"><?= $rapid_view['sprints'] ?></a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
