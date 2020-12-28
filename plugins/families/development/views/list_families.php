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
<div id="list_families_wrapper" class="col-sm-12">

    <table id="list_families_table" class="col-sm-12 table table-striped dataTable">
        <thead>
        <tr>
             <th scope="col">ID</th>
             <th scope="col">Name</th>
             <th scope="col">Last modification</th>
             <th>View</th>
             <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="contacts2-editor-container"></div>

</div>

<div class="modal fade" id="confirm_family_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected family.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_family_yes">Delete</a>
			</div>

		</div>
	</div>
</div>
