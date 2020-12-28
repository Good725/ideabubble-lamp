<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div id="list_mailing_lists_wrapper">
	<table class="table table-striped dataTable list_mailing_lists_table" id="list_mailing_lists_table">
		<thead>
			<tr>
				<th scope="col"><?= __('ID') ?></th>
				<th scope="col"><?= __('Name') ?></th>
				<th scope="col"><?= __('Created') ?></th>
				<th scope="col"><?= __('Modified') ?></th>
				<th scope="col"><?= __('Delete') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($lists as $list): ?>
				<tr>
					<td><?= $list['id'] ?></td>
					<td><a class="edit-link" href="/admin/contacts2/edit_mailing_list/<?= $list['id'] ?>"><?= $list['name'] ?></a></td>
					<td><?= IbHelpers::relative_time_with_tooltip($list['date_created']) ?></td>
					<td><?= IbHelpers::relative_time_with_tooltip($list['date_modified']) ?></td>
					<td>
						<button class="btn-link">
							<span class="flaticon-remove-button"></span>
						</button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="modal modal-primary fade" id="confirm_delete_mailing_list">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><?= __('Confirm deletion') ?></h3>
			</div>

			<div class="modal-body">
				<p><?= __('Are you sure you want to delete the selected mailing list?') ?></p>
			</div>

			<div class="modal-footer form-actions">
				<a href="#" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes"><?= __('Delete') ?></a>
			</div>

		</div>
	</div>
</div>
<script>
	$('#list_mailing_lists_table').on('click mousedown', 'tbody tr', function(ev)
	{
		if ( ! $(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0])
		{
			var link = $(this).find('.edit-link').attr('href');

			<?php // If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab. ?>
			<?php // Otherwise open it in the same tab ?>
			if (ev.ctrlKey || ev.metaKey || ev.which == 2) {
				window.open(link, '_blank');
			}
			else {
				window.location.href = link;
			}
		}
	});
</script>
