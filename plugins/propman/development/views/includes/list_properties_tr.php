<tr data-id="<?=$propery['id']?>">
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?=$propery['id']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?=$propery['name']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?=$propery['address1']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?=$propery['group']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($propery['created']) ?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($propery['updated']) ?></a></td>
	<td>
		<div class="dataTable-list-actions">
			<a class="edit-link" href="/admin/propman/edit_property/<?=$propery['id']?>" title="<?= __('Edit') ?>">
				<span class="icon-pencil"></span> <?= __('Edit') ?>
			</a>
			<a class="clone-link" href="/admin/propman/clone_property/<?=$propery['id']?>" title="<?= __('Clone') ?>">
				<span class="icon-copy"></span> <?= __('Clone') ?>
			</a>
			<button
				type="button"
				class="btn-link list-delete-button"
				title="<?= __('Delete') ?>"
				data-toggle="modal"
				data-target="#delete-property-modal"
				data-id="<?=$propery['id']?>"
				>
				<span class="icon-times"></span> <?= __('Delete') ?>
			</button>
		</div>
	</td>
	<td>
		<button type="button" class="btn-link list-publish-button">
			<span class="sr-only"><?=$propery['published']?></span>
			<span class="icon-<?=$propery['published'] ? 'ok' : 'times'?>"></span>
		</button>
	</td>
</tr>