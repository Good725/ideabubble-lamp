<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class=""><?=$project['details']['title'] . '(' . $project['details']['jira_key'] . ')'?> Details</h2>
			<div class="pull-left"><a href='/admin/extra/project_sync_jira?project_id=<?=$project['details']['id']?>'>Syncronize With Jira</a></div>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<table class="table table-striped dataTable" id="categories_table">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Jira Key</th>
            <th scope="col">Title</th>
			<th scope="col">Description</th>
			<th scope="col">Status</th>
			<th scope="col">Updated</th>
			<th scope="col">Time Spent</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($project['issues'] as $issue): ?>
            <tr id="issue_<?= $issue['id'] ?>">
                <td><?= $issue['id'] ?></td>
                <td><?= $issue['jira_key'] ?></td>
                <td><?= htmlentities($issue['title']) ?></td>
				<td><?= htmlentities($issue['description']) ?></td>
				<td><?= $issue['status'] ?></td>
				<td><?= $issue['updated'] ?></td>
				<td><?= $issue['time_spent'] ? round($issue['time_spent'] / 3600, 2) . 'h' : ''?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
