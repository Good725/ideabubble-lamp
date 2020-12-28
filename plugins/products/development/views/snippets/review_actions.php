<div>
	<a href="/admin/products/edit_review/<?= $id ?>" class="edit-link">
		<span class="icon-pencil"></span> <?= __('Edit') ?>
	</a>
</div>
<div style="white-space: nowrap">
	<button
		type="button"
		class="btn-link list-delete-button"
		title="<?= __('Delete') ?>"
		data-toggle="modal"
		data-target="#delete-product-review-modal"
		data-id="<?= $id ?>"
		>
		<span class="icon-times"></span> <?= __('Delete') ?>
	</button>
</div>