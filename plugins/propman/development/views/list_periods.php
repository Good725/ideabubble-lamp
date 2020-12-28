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

<table class="table table-striped dataTable list-screen-table list-periods-table" id="list-periods-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Period') ?></th>
		<th scope="col"><?= __('Start') ?></th>
		<th scope="col"><?= __('End') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($periods as $period)
	{
	?>
		<tr data-id="<?=$period['id']?>">
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=$period['id']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=$period['name']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=$period['starts']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=$period['ends']?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($period['created'])?></a></td>
			<td><a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>"><?=IbHelpers::relative_time_with_tooltip($period['updated'])?></a></td>
			<td>
				<div class="dataTable-list-actions">
					<a class="edit-link" href="/admin/propman/edit_period/<?=$period['id']?>" title="<?= __('Edit') ?>">
						<span class="icon-pencil"></span> <?= __('Edit') ?>
					</a>
					<a class="clone-link" href="/admin/propman/clone_period/<?=$period['id']?>" title="<?= __('Clone') ?>">
						<span class="icon-copy"></span> <?= __('Clone') ?>
					</a>
					<button
							type="button"
							class="btn-link list-delete-button"
							title="<?= __('Delete') ?>"
							data-toggle="modal"
							data-target="#delete-period-modal"
							data-id="<?=$period['id']?>"
					>
						<span class="icon-times"></span> <?= __('Delete') ?>
					</button>
				</div>
			</td>
			<td>
				<button type="button" class="btn-link list-publish-button">
					<span class="sr-only"><?=$period['published']?><?php // 1 = published, 0 = unpublished to make it sortable ?></span>
					<span class="icon-<?=$period['published'] ? 'ok' : 'times'?>"></span>
				</button>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-period-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/propman/delete_period" method="post" id="delete-period">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete period') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this period?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-period-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
