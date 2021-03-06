<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class="table table-striped dataTable" id="categories_table">
	<thead>
		<tr>
			<th scope="col">Image</th>
			<th scope="col">ID</th>
			<th scope="col">Category</th>
			<th scope="col">Order</th>
			<th scope="col">Edit</th>
			<th scope="col">Publish</th>
			<th scope="col">Delete</th>
		</tr>
	</thead>
</table>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body" id="warning_message"><!-- DO NOT ENTER TEXT HERE --></div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete">Delete</a>
			</div>
		</div>
	</div>
</div>
