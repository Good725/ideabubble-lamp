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
<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<div id="list_contacts_wrapper">
    <table id="list_contacts_table" class="table dataTable dataTable-collapse">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <?php if (Settings::instance()->get('contacts3_list_display_student_id') == 1) { ?>
                    <th scope="col"><abbr title="Student Id">Student Id</abbr></th>
                <?php } ?>

                <th scope="col">Name</th>
                <th scope="col">Type</th>
                <th scope="col">Role</th>
                <th scope="col">Label</th>
                <th scope="col">Mobile</th>
                <?php if(Settings::instance()->get('contacts_create_family') == 1):?>
                <th scope="col">Family</th>
                <?php endif?>
                <th scope="col">Status</th>
                <th scope="col">Primary</th>
                <th scope="col">Primary contacts</th>
                <th scope="col">Address</th>
                <th scope="col">School</th>
                <th scope="col">Year</th>
                <th scope="col">Updated</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <td>
                    <label for="search_id" class="hide2">Search by ID</label>
                    <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
                </td>

                <?php if (Settings::instance()->get('contacts3_list_display_student_id') == 1) { ?>
                <td>
                    <label for="search_remote_id" class="hide2">Student ID</label>
                    <input type="text" id="search_student_id" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <?php } ?>

                <td>
                    <label for="search_name" class="hide2">Search by name</label>
                    <input type="text" id="search_name" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_type" class="hide2">Search by type</label>
                    <input type="text" id="search_type" class="form-control search_init" name="" placeholder="Search"/>
                </td>
                <td>
                    <label for="search_role" class="hide2">Search by role</label>
                    <input type="text" id="search_role" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_label" class="hide2">Search by label</label>
                    <input type="text" id="search_label" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_mobile" class="hide2">Search by mobile</label>
                    <input type="text" id="search_mobile" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <?php if (Settings::instance()->get('contacts_create_family') == 1):?>
				<td>
					<label for="search_family_name" class="hide2">Search by family name</label>
					<input type="text" id="search_family_name" class="form-control search_init" name="" placeholder="Search" />
				</td>
                <?php endif?>
                <td>
                    <label for="search_status" class="hide2">Search by status</label>
                    <input type="text" id="search_status" class="form-control search_init" name=""
                           placeholder="Search"/>
                </td>
                <td>
                    <label for="search_is_primary" class="hide2 hidden">Search by "is primary"</label>
                    <input type="text" id="search_is_primary" class="form-control search_init hidden" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_primary_contacts" class="hide2 hidden">Search by primary contacts</label>
                    <input type="text" id="search_primary_contacts" class="form-control search_init hidden" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_address" class="hide2">Search by address</label>
                    <input type="text" id="search_address" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_school" class="hide2">Search by school</label>
                    <input type="text" id="search_school" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_year" class="hide2">Search by year</label>
                    <input type="text" id="search_year" class="form-control search_init" name="" placeholder="Search" />
                </td>
                <td>
                    <label for="search_modification_date" class="hide2">Search by modification date</label>
                    <input type="text" id="search_modification_date" class="form-control search_init" name="" placeholder="Search" />
                </td>
            </tr>
        </thead>
    </table>
    <div id="family_menu_wrapper" class="family_menu_wrapper"></div>
</div>
