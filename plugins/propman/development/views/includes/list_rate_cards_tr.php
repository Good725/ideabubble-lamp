<tr data-id="<?=$ratecard['id']?>">
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?=$ratecard['id']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?=$ratecard['name']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?=$ratecard['p_name']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?= date('D d/M/Y', strtotime($ratecard['starts'])) ?> &#8594; <?= date('D d/M/Y', strtotime($ratecard['ends'])) ?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?=$ratecard['g_name']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($ratecard['created']) ?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?=$ratecard['update_username']?></a></td>
	<td><a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>"><?= IbHelpers::relative_time_with_tooltip($ratecard['updated']) ?></a></td>
	<td>
		<div class="dataTable-list-actions">
			<a class="edit-link" href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>" title="<?= __('Edit') ?>">
				<span class="icon-pencil"></span> <?= __('Edit') ?>
			</a>
			<a class="clone-link" href="/admin/propman/clone_rate_card/<?=$ratecard['id']?>" title="<?= __('Clone') ?>">
				<span class="icon-copy"></span> <?= __('Clone') ?>
			</a>
			<button
				type="button"
				class="btn-link list-delete-button"
				title="<?= __('Delete') ?>"
				data-toggle="modal"
				data-target="#delete-ratecard-modal"
				data-id="<?=$ratecard['id']?>"
				>
				<span class="icon-times"></span> <?= __('Delete') ?>
			</button>
		</div>
	</td>
	<td>
		<button type="button" class="btn-link list-publish-button">
			<span class="sr-only"><?=$ratecard['created']?></span>
			<span class="icon-<?=$ratecard['published'] ? 'ok' : 'times'?>"></span>
		</button>
	</td>
</tr>