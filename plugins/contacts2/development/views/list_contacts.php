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
<div id="list_contacts_wrapper">

    <table id="list_contacts_table" class="table table-striped dataTable list_contacts_table">
        <thead>
        <tr>
             <th scope="col">ID</th>
             <th scope="col">First name</th>
             <th scope="col">Last name</th>
             <th scope="col">Email</th>
             <th scope="col">Mobile</th>
             <th scope="col">Mailing list</th>
             <th scope="col">Last modification</th>
             <th>View</th>
             <th>Delete</th>
        </tr>
        </thead>
        <?/* <thead>
        <tr>
            <th scope="col">
                <label for="search_id" class="hide2">Search by ID</label>
                <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <label for="search_first_name" class="hide2">Search by First name</label>
                <input type="text" id="search_first_name" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <label for="search_last_name" class="hide2">Search by Last name</label>
                <input type="text" id="search_last_name" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <label for="search_email" class="hide2">Search by Email</label>
                <input type="text" id="search_email" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <label for="search_list" class="hide2">Search by Mailing list</label>
                <input type="text" id="search_list" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <label for="search_modification_date" class="hide2">Search by modification date</label>
                <input type="text" id="search_modification_date" class="form-control search_init" name="" placeholder="Search" />
            </th>
             <th scope="col">&nbsp;</th>
             <th scope="col">&nbsp;</th>
             <th scope="col">&nbsp;</th>
        </tr>
        </thead> */ ?>
        <tbody class="list_contacts_tbody">
        </tbody>
    </table>

    <div id="contacts2-editor-container"></div>

</div>

<div class="modal modal-primary fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected contact.</p>
			</div>

			<div class="modal-footer form-actions">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>

		</div>
	</div>
</div>
