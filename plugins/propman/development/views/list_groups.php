<div id="dashboard_list_alerts"><?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>

<table class="table table-striped dataTable list-screen-table list-property-groups-table" id="list-property-groups-table">
	<thead>
		<tr>
			<th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Title') ?></th>
			<th scope="col"><?= __('Contact') ?></th>
			<th scope="col"><?= __('Address') ?></th>
			<th scope="col"><?= __('Total Houses') ?></th>
			<th scope="col"><?= __('Created') ?></th>
			<th scope="col"><?= __('Updated') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
			<th scope="col"><?= __('Publish') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		// should be a server-side datatable
		foreach ($groups as $group)
		{
			include 'includes/list_groups_tr.php';
		}
		?>
	</tbody>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-property-group-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/propman/delete_group" method="post" id="delete-property-group">
				<input type="hidden" name="id" value="" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete group') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this group?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-property-group-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
