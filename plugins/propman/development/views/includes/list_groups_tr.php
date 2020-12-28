<tr data-id="<?=$group['id']?>">
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?=$group['id']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?=$group['name']?></a></td>
	<td>
		<?php
		if ($group['host_contact_id']) {
		?>
		<a class="edit-link" href="/admin/contacts2/edit/<?=$group['host_contact_id']?>" title="<?= __('Edit') ?>"><?=$group['first_name'] . ' ' . $group['last_name']?></a></td>
		<?php
		}
		?>
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?=$group['address1']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?=$group['has_properties']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($group['created']) ?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($group['updated']) ?></a></td>
	<td>
		<div class="dataTable-list-actions">
			<a class="edit-link" href="/admin/propman/edit_group/<?=$group['id']?>" title="<?= __('Edit') ?>">
				<span class="icon-pencil"></span> <?= __('Edit') ?>
			</a>
			<a class="clone-link" href="/admin/propman/clone_group/<?=$group['id']?>" title="<?= __('Clone') ?>">
				<span class="icon-copy"></span> <?= __('Clone') ?>
			</a>
			<button
				type="button"
				class="btn-link list-delete-button"
				title="<?= __('Delete') ?>"
				data-toggle="modal"
				data-target="#delete-property-group-modal"
				data-id="<?=$group['id']?>"
				>
				<span class="icon-times"></span> <?= __('Delete') ?>
			</button>
		</div>
	</td>
	<td>
		<button type="button" class="btn-link list-publish-button">
			<span class="sr-only"><?=$group['published']?><?php // 1 = published, 0 = unpublished to make it sortable ?></span>
			<span class="icon-<?=$group['published'] ? 'ok' : 'times'?>"></span>
		</button>
	</td>
</tr>