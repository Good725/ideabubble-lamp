<?= (isset($alert)) ? $alert : ''; ?>
<?php $date_format = Settings::instance()->get('date_format') ?: 'd/M/Y'; ?>
<style>
	.star_checkbox{font-size:0.1px;opacity:0;width:0!important;}
	.star_checkbox + i:before{content:'\f006';font-family:FontAwesome;font-size:15px;display:inline-block;}
	.star_checkbox:checked + i:before{content:'\f005'}
	.star_checkbox:focus + i{outline:1px dotted #aaa;}
</style>
<table id="reports_datatable" class='table table-striped dataTable report_table'>
    <thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Title</th>
			<th scope="col">Created</th>
			<th scope="col">Updated</th>
			<th scope="col">Favourite</th>
            <?php if(Auth::instance()->has_access('reports_edit')): ?>
			    <th scope="col">Dashboard</th>
            <?php endif; ?>
            <th scope="col">Actions</th>
		</tr>
    </thead>
    <tbody>
    <?php foreach ($reports AS $report): ?>
        <tr data-report_id="<?=$report['id'];?>">
            <td><?= $report['id'] ?></td>
            <td class="report_name">
                <?php $string = substr($report['name'], 0, 25); ?>
                <?= $report['name'] ?>
                <div class="report_box"><?= $report['name'] ?></div>
            </td>
            <td><?= date($date_format, strtotime($report['date_created'])) ?></td>
            <td><?=date($date_format, strtotime($report['date_modified']));?></a></td>
			<td>
				<label>
					<span class="hidden"><?= ($report['is_favorite'] == '') ? 0 : 1 ?></span>
					<input class="star_checkbox toggle_favorite" type="checkbox"<?= ($report['is_favorite'] != '') ? ' checked="checked"' : '' ?> />
					<i class="icon-star-empty"></i>
				</label>
			</td>
            <?php if (Auth::instance()->has_access('reports_edit')): ?>
                <td class="toggle_dashboard">
                    <span class="hidden"><?= $report['dashboard'] ?></span>
                    <i class="icon-<?=$report['dashboard'] == '1' ? 'ok' : 'remove';?>"></i>
                </td>
            <?php endif; ?>
            <td>
                <?php
                $options      = [['type' => 'link',   'title' => 'Run',  'icon' => 'eye',    'link' => '/admin/reports/read/'.$report['id'], 'attributes' => ['class' => 'edit-link']]];
                if ($can_edit_report) {
                    $options[] = ['type' => 'link',   'title' => 'Edit',  'icon' => 'pencil', 'link' => '/admin/reports/add_edit_report/'.$report['id']];
                    $options[] = ['type' => 'link',   'title' => 'Clone', 'icon' => 'clone',  'link' => '/admin/reports/clone_report/'.$report['id']];
                }
                if ($can_delete_report) {
                    $options[] = ['type' => 'button', 'title' => 'Delete', 'icon' => 'remove', 'attributes' => ['class' => 'remove_report']];
                }

                echo View::factory('snippets/btn_dropdown')
                    /* Remove after ENGINE-1358 has been merged */
                    ->set('btn_type',      'outline-primary')
                    ->set('options_align', 'right')
                    ->set('sr_title',      'Actions')
                    ->set('title',          ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                    /* End of content to be removed */
                    ->set('type', 'actions')
                    ->set('options', $options);
                ?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Confirm Deletion</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you wish to delete this report?</p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" id="delete_report">Delete</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true" id="cancel_delete">Cancel</button>
			</div>
		</div>
	</div>
</div>
