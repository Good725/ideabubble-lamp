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
<table class="table table-striped dataTable list-screen-table list-suitabilitygroups-table" id="list-suitabilitygroups-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Suitability Group') ?></th>
		<th scope="col"><?= __('Sort') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($suitabilityGroups as $suitabilityGroup)
	{
	?>
		<tr data-id="<?=$suitabilityGroup['id']?>">
			<td><a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$suitabilityGroup['id']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$suitabilityGroup['name']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>"><?=$suitabilityGroup['sort']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($suitabilityGroup['created'])?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($suitabilityGroup['updated'])?></a></td>
			<td>
				<div class="dataTable-list-actions">
					<a class="edit-link" href="/admin/propman/edit_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Edit') ?>">
						<span class="icon-pencil"></span> <?= __('Edit') ?>
					</a>
					<a class="clone-link" href="/admin/propman/clone_suitabilitygroup/<?=$suitabilityGroup['id']?>" title="<?= __('Clone') ?>">
						<span class="icon-copy"></span> <?= __('Clone') ?>
					</a>
					<button
							type="button"
							class="btn-link list-delete-button"
							title="<?= __('Delete') ?>"
							data-toggle="modal"
							data-target="#delete-suitabilitygroup-modal"
							data-id="<?=$suitabilityGroup['id']?>"
					>
						<span class="icon-times"></span> <?= __('Delete') ?>
					</button>
				</div>
			</td>
			<td>
				<button type="button" class="btn-link list-publish-button">
					<span class="sr-only"><?=$suitabilityGroup['published']?><?php // 1 = published, 0 = unpublished to make it sortable ?></span>
					<span class="icon-<?=$suitabilityGroup['published'] ? 'ok' : 'times'?>"></span>
				</button>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-suitabilitygroup-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/propman/delete_suitabilitygroup" method="post" id="delete-suitability-group">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete suitability group') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this suitability group?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="delete-suitabilitygroup-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-suitabilitygroup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_used_suitabilitygroup" method="post" id="delete-used-suitability-group">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete suitability group') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('This Suitability Group is currently linked to a Property.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-used-suitabilitygroup-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
