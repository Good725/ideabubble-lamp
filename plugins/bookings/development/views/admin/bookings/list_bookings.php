<?php
/**
 *  List the bookings in the Bookings plugin
 */
(isset($alert)) ? $alert : '';?>
<table class='table table-striped dataTable dataTable-collapse' id="list_bookings_table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <?php if (Settings::instance()->get('cms_platform') === 'training_company'): ?>
                <th scope="col">Contact</th>
                <th scope="col">Organisation</th>
            <?php else: ?>
                <th scope="col">Student</th>
                <th scope="col">Student year</th>
            <?php endif; ?>
            <th scope="col">Schedule</th>
            <th scope="col">Course</th>
            <th scope="col">Type</th>
            <th scope="col">Status</th>
            <th scope="col">Start date</th>
            <th scope="col">Room</th>
            <th scope="col">Outstanding</th>
<!--            <th scope="col">Created</th>-->
            <?php if (Settings::instance()->get('link_contacts_to_bookings') == 1) : ?>
                <th scope="col">Agent</th>
                <th scope="col">Host</th>
                <th scope="col">Coordinator</th>
            <?php endif; ?>
            <th scope="col"><abbr title="Quantity of delegates">Qty</abbr></th>
            <th scope="col">Updated</th>
        </tr>
    </thead>
	<thead>
            <tr>
                <th scope="col">
                    <label for="search_id" class="hide2">Search by ID</label>
                    <input type="text" id="search_booking_id" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_student" class="hide2">Search by student</label>
                    <input type="text" id="search_student" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_student" class="hide2">Search by school year</label>
                    <input type="text" id="search_student" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_schedule_title" class="hide2">Search by schedule</label>
                    <input type="text" id="search_schedule_title" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_course_title" class="hide2">Search by course</label>
                    <input type="text" id="search_course_title" class="form-control search_init" name="" placeholder="Search" />
                </th>
				<th scope="col">
					<label for="search_type" class="hide2">Search by type</label>
					<input type="text" id="search_type" class="form-control search_init" name="" placeholder="Search" />
				</th>
                <th scope="col">
					<label for="search_status" class="hide2">Search by status</label>
					<input type="text" id="search_status" class="form-control search_init" name="" placeholder="Search" />
				</th>
				<th scope="col">
					<label for="search_start_date" class="hide2">Search by start date</label>
					<input type="text" id="search_start_date" class="form-control search_init" name="" placeholder="Search" />
				</th>
                <th scope="col">
                    <label for="search_location_name" class="hide2">Search by location name</label>
                    <input type="text" id="search_location_name" class="form-control search_init" name="" placeholder="Search" />
                </th>
				<th></th>
                <?php if (Settings::instance()->get('link_contacts_to_bookings') == 1) : ?>
                    <th scope="col">
                        <label for="search_last_modified" class="hide2">Search by agent</label>
                        <input type="text" id="search_last_modified" class="form-control search_init" name=""
                               placeholder="Search"/>
                    </th>
                    <th scope="col">
                        <label for="search_last_modified" class="hide2">Search by host family</label>
                        <input type="text" id="search_last_modified" class="form-control search_init" name=""
                               placeholder="Search"/>
                    </th>
                    <th scope="col">
                        <label for="search_last_modified" class="hide2">Search by coordinator</label>
                        <input type="text" id="search_last_modified" class="form-control search_init" name=""
                               placeholder="Search"/>
                    </th>
                <?php endif; ?>
                <th></th>
                <th scope="col">
                    <label for="search_last_modified" class="hide2">Search by modification date</label>
                    <input type="text" id="search_last_modified" class="form-control search_init" name="" placeholder="Search" />
                </th>
            </tr>
        </thead>
    <tbody></tbody>
</table>
<div id="family_menu_wrapper" class="family_menu_wrapper"></div>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Modal header</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you wish to delete this discount?</p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" id="delete_report">Delete</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true" id="cancel_delete">Cancel</button>
			</div>
		</div>
	</div>
</div>

