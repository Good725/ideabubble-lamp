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

<table class="table table-striped dataTable list-screen-table list-facilitytypes-table" id="list-facilitytypes-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Group') ?></th>
		<th scope="col"><?= __('Facility') ?></th>
		<th scope="col"><?= __('Sort') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($facilityTypes as $facilityType)
	{
	?>
		<tr data-id="<?=$facilityType['id']?>">
			<td><?=$facilityType['id']?></td>
			<td><?=$facilityType['group']?></td>
			<td><?=$facilityType['name']?></td>
			<td><?=$facilityType['sort']?></td>
			<td><?=IbHelpers::relative_time_with_tooltip($facilityType['created'])?></td>
			<td><?=IbHelpers::relative_time_with_tooltip($facilityType['updated'])?></td>
			<td>
				<div class="dataTable-list-actions">
					<a class="edit-link" href="/admin/propman/edit_facilitytype/<?=$facilityType['id']?>" title="<?= __('Edit') ?>">
						<span class="icon-pencil"></span> <?= __('Edit') ?>
					</a>
					<a class="clone-link" href="/admin/propman/clone_facilitytype/<?=$facilityType['id']?>" title="<?= __('Clone') ?>">
						<span class="icon-copy"></span> <?= __('Clone') ?>
					</a>
					<button
							type="button"
							class="btn-link list-delete-button"
							title="<?= __('Delete') ?>"
							data-toggle="modal"
							data-target="#delete-facilitytype-modal"
							data-id="<?=$facilityType['id']?>"
					>
						<span class="icon-times"></span> <?= __('Delete') ?>
					</button>
				</div>
			</td>
			<td>
				<button type="button" class="btn-link list-publish-button">
					<span class="sr-only"><?=$facilityType['published']?><?php // 1 = published, 0 = unpublished to make it sortable ?></span>
					<span class="icon-<?=$facilityType['published'] ? 'ok' : 'times'?>"></span>
				</button>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-facilitytype-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/propman/delete_facilitytype" method="post" id="delete-facility-type">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete facility type') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this facility type?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-propertytype-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
