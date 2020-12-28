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
<table class="table table-striped dataTable" id="locations_table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Title</th>
        <th>County</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Edit</th>
        <th>Publish</th>
        <th>Delete</th>
    </tr>
    </thead>
</table>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
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
