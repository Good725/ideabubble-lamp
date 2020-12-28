<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class="table table-striped dataTable list-screen-table list-facilitygroups-table" id="list-facilitygroups-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Facility Group') ?></th>
		<th scope="col"><?= __('Sort') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($facilityGroups as $facilityGroup)
	{
	?>
		<tr data-id="<?=$facilityGroup['id']?>">
			<td><a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$facilityGroup['id']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$facilityGroup['name']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$facilityGroup['sort']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($facilityGroup['created'])?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($facilityGroup['updated'])?></a></td>
			<td>
				<div class="dataTable-list-actions">
					<a class="edit-link" href="/admin/propman/edit_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Edit') ?>">
						<span class="icon-pencil"></span> <?= __('Edit') ?>
					</a>
					<a class="clone-link" href="/admin/propman/clone_facilitygroup/<?=$facilityGroup['id']?>" title="<?= __('Clone') ?>">
						<span class="icon-copy"></span> <?= __('Clone') ?>
					</a>
					<button
							type="button"
							class="btn-link list-delete-button"
							title="<?= __('Delete') ?>"
							data-toggle="modal"
							data-target="#delete-facilitygroup-modal"
							data-id="<?=$facilityGroup['id']?>"
					>
						<span class="icon-times"></span> <?= __('Delete') ?>
					</button>
				</div>
			</td>
			<td>
				<button type="button" class="btn-link list-publish-button">
					<span class="sr-only"><?=$facilityGroup['published']?><?php // 1 = published, 0 = unpublished to make it sortable ?></span>
					<span class="icon-<?=$facilityGroup['published'] ? 'ok' : 'times'?>"></span>
				</button>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-facilitygroup-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/propman/delete_facilitygroup" method="post" id="delete-facility-group">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete facility group') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this facility group?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="delete-facilitygroup-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-facilitygroup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_used_facilitygroup" method="post" id="delete-facility-group">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete a used facility group') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Please note this Facility is currently linked to a Property.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete-used-facilitygroup-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
