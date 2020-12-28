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
<table class="table table-striped dataTable" id="producttags_table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
			<th scope="col">Created</th>
			<th scope="col">Modified</th>
			<th scope="col">Edit</th>
			<th scope="col">Publish</th>
			<th scope="col">Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php if (isset($tags)): ?>
			<?php foreach ($tags as $tag): ?>
				<tr data-id="<?= $tag['id'] ?>">
					<td><?= $tag['id'] ?></td>
					<td><?= $tag['title'] ?></td>
					<td><?= $tag['date_created'] ?></td>
					<td><?= $tag['date_modified'] ?></td>
					<td><a class="edit_link" href="/admin/products/add_edit_tag/<?= $tag['id'] ?>" title="Edit"><i class="icon-pencil"></i></a></td>
					<td><a class="toggle_publish" href="#"><i class="icon-<?= ($tag['publish'] == 1) ? 'ok' : 'remove'?>"></i></a></td>
					<td><a class="delete_item" href="#"><i class="icon-ban-circle"></i></a></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<!-- Delete modal -->
<div class="modal fade" id="delete_producttag_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirm Deletion</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this tag?</p>
			</div>
			<div class="modal-footer">
				<a href="#" type="button" class="btn btn-danger" id="producttag_confirm_delete_btn" data-id="">Delete</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
	$('#producttags_table').on('click', 'tbody td', function()
	{
		if ($(this).find('a').length == 0)
		{
			window.location = this.parentNode.getElementsByClassName('edit_link')[0].href;
		}
	});

	$('.toggle_publish').click(function(ev)
	{
		ev.preventDefault();
		var $icon = $(this).find('i');
		var id    = $(this).closest('tr').data('id');
		$.post('/admin/products/ajax_toggle_publish_tag/'+id, function(result)
		{
			if (result == 1)
			{
				$icon.removeClass('icon-remove').addClass('icon-ok');
			}
			else if (result == 0)
			{
				$icon.removeClass('icon-ok').addClass('icon-remove');
			}
		});
	});

	$('.delete_item').click(function(ev)
	{
		ev.preventDefault();
		var id = $(this).parents('tr').data('id');
		document.getElementById('producttag_confirm_delete_btn').href = (id) ? '/admin/products/delete_tag/'+id : '#';
		$('#delete_producttag_modal').modal('show');
	});

	$('#producttag_confirm_delete_btn').click(function()
	{
		var id = this.getAttribute('data-id');
		$.post(''+id);
	});

</script>
