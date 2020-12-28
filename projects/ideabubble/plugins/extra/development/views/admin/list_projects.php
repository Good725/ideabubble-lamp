<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Projects</h2>
			<div class="pull-left"><a href='/admin/extra/projects_sync_jira'>Syncronize With Jira</a></div>
			<div class="pull-right"><a href='/admin/extra/projects_report'>Report</a>&nbsp;</div>
			<div class="pull-right">&nbsp;&minus;&nbsp;<a href='/admin/extra/sprints'>Sprints</a>&nbsp;&minus;&nbsp;</div>
            <div class="pull-right"><a href='/admin/extra/upwork_import'>Upwork Import</a>&nbsp;</div>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<table class="table table-striped dataTable" id="projects_table">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Key</th>
            <th scope="col">Title</th>
			<th scope="col">Last Synced&nbsp;-&nbsp;<a href="/admin/extra/project_sync_jira?project_id=all" onclick="return confirm('Can take a long time. Continue?');">Sync All Now</a></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($projects as $project): ?>
            <tr id="project_<?= $project['id'] ?>">
                <td><a href="<?php echo URL::Site('admin/extra/project_view?id=' . $project['id']); ?>"><?= $project['id'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/project_view?id=' . $project['id']); ?>"><?= $project['jira_key'] ?></a></td>
                <td><a href="<?php echo URL::Site('admin/extra/project_view?id=' . $project['id']); ?>"><?= $project['title'] ?></a></td>
				<td>
                <?php
                if ($project['jira_id']) {
                ?>
                <?= IbHelpers::relative_time($project['synced']) ?>&nbsp;-&nbsp;<a href="/admin/extra/project_sync_jira?project_id=<?=$project['id']?>">Sync Now</a>
                <?php
                } else {
                ?>
                    Renamed / Deleted
                <?php
                }
                ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
