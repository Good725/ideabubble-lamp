<?= isset($alert) ? $alert : '' ?>
<table id="list_categories_table" class="table table-striped dataTable list_categories_table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Category</th>
            <th scope="col">Order</th>
            <th scope="col">Edit</th>
            <th scope="col">Publish</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr data-id="<?= $category['id'] ?>">
                <td><?= $category['id'] ?></td>
                <td><?= $category['title'] ?></td>
                <td><?= $category['order'] ?></td>
                <td><a href="/admin/cars/add_edit_category/<?= $category['id'] ?>"><i class="icon-pencil"></i></a></td>
                <td class="toggle_publish"><i class="icon-<?= ($category['publish'] == 1) ? 'ok' : 'remove' ?>"></i></td>
                <td class="delete_item"><i class="icon-ban-circle"></i></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="modal fade" id="delete_category_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="exampleModalLabel">Confirm Deletion</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this category?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="confirm_delete_btn" data-id="">Delete</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
	$('.toggle_publish').click(function()
	{
		var $icon = $(this).find('i');
		var id    = $(this).closest('tr').data('id');
		$.post('/admin/cars/ajax_publish_category/'+id, function(result)
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

	$('.delete_item').click(function()
	{
		var id = $(this).closest('tr').data('id');
		$('#confirm_delete_btn').attr('data-id', id);
		$('#delete_category_modal').modal('show');
	});

	$('#confirm_delete_btn').click(function()
	{
		var id = this.getAttribute('data-id');
		$.post('/admin/cars/ajax_delete_category/'+id, function(result)
		{
			$('#list_categories_table').find('tbody tr[data-id="'+id+'"]').remove();
		});
		$('#delete_category_modal').modal('hide');
	});

</script>