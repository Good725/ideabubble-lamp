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
<table class="table table-striped" id="subjects_table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Colour</th>
			<th scope="col">Cycle</th>
            <th scope="col">Name</th>
            <th scope="col">Date Created</th>
            <th scope="col">Date Modified</th>
            <th scope="col">Edit</th>
            <th scope="col">Publish</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected subject.</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>

