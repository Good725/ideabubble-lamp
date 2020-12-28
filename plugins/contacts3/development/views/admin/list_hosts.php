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
    <table id="list_hosts_table" class="table table-striped dataTable dataTable-collapse">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Primary Contact</th>
                <th scope="col">Phone</th>
                <th scope="col">Address</th>
                <th scope="col">Facilities</th>
                <th scope="col">Student Profile</th>
                <th scope="col">Availability</th>
                <th scope="col">Pets</th>
                <th scope="col">Status</th>
                <th scope="col">Updated</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th scope="col">
                    <label for="search_id" class="hide2">Search by ID</label>
                    <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_name" class="hide2">Search by Primary Contact</label>
                    <input type="text" id="search_name" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_phone" class="hide2">Search by phone</label>
                    <input type="text" id="search_phone" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_address" class="hide2">Search by address</label>
                    <input type="text" id="search_address" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_facilities" class="hide2">Search by facilities</label>
                    <input type="text" id="search_facilities" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_student_profile" class="hide2">Search by student profile</label>
                    <input type="text" id="search_student_profile" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_availability" class="hide2">Search by availability</label>
                    <input type="text" id="search_availability" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_pets" class="hide2">Search by pets</label>
                    <input type="text" id="search_pets" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_status" class="hide2">Search by status</label>
                    <input type="text" id="search_status" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_modification_date" class="hide2">Search by modification date</label>
                    <input type="text" id="search_modification_date" class="form-control search_init" name="" placeholder="Search" />
                </th>
            </tr>
        </thead>
    </table>
    <div id="family_menu_wrapper" class="family_menu_wrapper"></div>
</div>
