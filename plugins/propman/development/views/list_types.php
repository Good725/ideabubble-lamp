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
<table class="table table-striped dataTable list-screen-table list-types-table" id="list-types-table">
	<thead>
	<tr>
		<th scope="col"><?= __('ID') ?></th>
		<th scope="col"><?= __('Title') ?></th>
		<th scope="col"><?= __('Period') ?></th>
		<th scope="col"><?= __('Groups linked') ?></th>
		<th scope="col"><?= __('Created') ?></th>
		<th scope="col"><?= __('Updated') ?></th>
		<th scope="col"><?= __('Actions') ?></th>
		<th scope="col"><?= __('Publish') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	// should be a server-side datatable
	for ($i = 1; $i < 5; $i++)
	{
		include 'includes/list_rate_cards_tr.php';
	}
	?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-types-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= __('Delete type') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('Are you sure you want to delete this rate card?') ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="delete-ratecard-button"><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>
