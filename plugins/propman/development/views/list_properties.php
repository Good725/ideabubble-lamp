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
<table class="table table-striped dataTable list-screen-table  list-properties-table" id="list-properties-table">
	<thead>
		<tr>
			<th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Title') ?></th>
			<th scope="col"><?= __('Address') ?></th>
			<th scope="col"><?= __('Group') ?></th>
			<th scope="col"><?= __('Created') ?></th>
			<th scope="col"><?= __('Updated') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
			<th scope="col"><?= __('Publish') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		// should be a server-side datatable
		foreach ($properties as $propery)
		{
			include 'includes/list_properties_tr.php';
		}
		?>
	</tbody>
</table>


<div class="modal fade" tabindex="-1" role="dialog" id="delete-property-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="delete-property" method="post" action="/admin/propman/delete_property">
				<input type="hidden" name="id" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete property') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this property?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-property-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
