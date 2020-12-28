<?=(isset($alert)) ? $alert : '';?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class='table table-striped dataTable'>
    <thead>
    <tr>
        <th>ID</th>
        <th>Project</th>
        <th>Category</th>
        <th>Date Modified</th>
        <th>Published</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($projects AS $project): ?>
        <tr data-id="<?= $project['id'] ?>">
            <td><a href='<?= URL::Site('admin/projects/add_edit_project/'.$project['id']); ?>'><?=$project['id']?></a></td>
            <td><a href='<?= URL::Site('admin/projects/add_edit_project/'.$project['id']); ?>'><?=$project['name']?></a></td>
            <td><a href='<?= URL::Site('admin/projects/add_edit_project/'.$project['id']); ?>'><?=$project['category']?></a></td>
            <td><a href='<?= URL::Site('admin/projects/add_edit_project/'.$project['id']); ?>'><?=$project['date_modified']?></a></td>
            <td><a href='<?= URL::Site('admin/projects/add_edit_project/'.$project['id']); ?>'><?=$project['publish']?></a></td>
            <td data-toggle="modal" data-target="#delete-project-modal"><span class="icon-remove"></span></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

<div class="modal fade" id="delete-project-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirm Deletion</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this project?</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-danger" id="confirm-delete-project-button">Delete</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<script>
	$('#delete-project-modal').on('show.bs.modal', function (ev)
	{
		var $button = $(ev.relatedTarget);
		var id      = $button.parents('tr').data('id');
		document.getElementById('confirm-delete-project-button').href = '/admin/projects/delete/'+id;
	})
</script>
