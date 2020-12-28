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
<span style="margin-left:10px; float:right;"> 
	<button type="button" class="btn btn-primary" id="resetdatatable" title="Reset filter">Reset filter</button>
</span>
<table class="table table-striped" id="schedule_table">
    <thead>
        <tr>
            <th scope="col">Schedule ID</th>
            <th scope="col">Course title</th>
            <th scope="col">Schedule title</th>
            <th scope="col">Course Category</th>
            <th scope="col">Fee</th>
            <th scope="col">Location</th>
            <th scope="col">Schedule status</th>
            <th scope="col">Start date</th>
            <th scope="col">Repeat</th>
            <th scope="col">Times</th>
            <th scope="col">Trainer</th>
            <th scope="col">Confirmed</th>
            <th scope="col">Last&nbsp;Modified</th>
            <th scope="col">Actions</th>
            <th scope="col">Availability</th>
            <th scope="col">Publish</th>
        </tr>
    </thead>
    <thead>
    <tr>
        <th scope="col">
            <label for="search_id" class="hide2">Search by ID</label>
            <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_course" class="hide2">Search by course</label>
            <input type="text" id="search_course" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_name" class="hide2">Search by name</label>
            <input type="text" id="search_name" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_category" class="hide2">Search by category</label>
            <input type="text" id="search_category" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_fee_amount" class="hide2">Search by repeat</label>
            <input type="text" id="search_fee_amount" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_repeat_name" class="hide2">Search by repeat</label>
            <input type="text" id="search_repeat_name" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_id" class="hide2">Search by schedule status</label>
            <input type="text" id="schedule_status" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_start_date" class="hide2">Search by start date</label>
            <input type="text" id="search_start_date" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_location" class="hide2">Search by location</label>
            <input type="text" id="search_location" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_times" class="hide2">Search by times</label>
            <input type="text" id="search_times" class="form-control search_init hidden" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_trainer" class="hide2">Search by trainer</label>
            <input type="text" id="search_trainer" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_is_confirmed" class="hide2">Search by Confirmed</label>
            <input type="text" id="search_is_confirmed" class="form-control search_init hidden" name="" placeholder="Search" />
        </th>
        <th scope="col">
            <label for="search_date_modified" class="hide2">Search by last&nbsp;modified</label>
            <input type="text" id="search_date_modified" class="form-control search_init" name="" placeholder="Search" />
        </th>
        <th scope="col"></th>
        <th scope="col">
        </th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>

</table>
<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected schedule.</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>

