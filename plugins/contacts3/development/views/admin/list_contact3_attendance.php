<div class="row-fluid header list_notes_alert">
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
</div>
<?php if (!empty($attendance) && !empty($attendance['timeslots'])): ?>
    <table class="table dataTable attending_timeslots_table">
        <thead>
            <tr>
                <th scope="col">Schedule ID</th>
                <th scope="col">Course</th>
                <th scope="col">Location</th>
                <th scope="col">Room</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">Planned Arrival</th>
                <th scope="col">Planned Leave</th>
                <th scope="col">Arrived</th>
                <th scope="col">Left</th>
                <th scope="col">Attending</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th scope="col">
                    <label for="search_schedule_id" class="hide2">Search by Schedule</label>
                    <input type="text" id="search_schedule_id" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_course" class="hide2">Search by Course</label>
                    <input type="text" id="search_course" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_location" class="hide2">Search by Location</label>
                    <input type="text" id="search_location" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_room" class="hide2">Search by Room</label>
                    <input type="text" id="search_room" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_date" class="hide2">Search by Date</label>
                    <input type="text" id="search_date" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_time" class="hide2">Search by Time</label>
                    <input type="text" id="search_time" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_planned_arrival" class="hide2">Search by Planned Arrival</label>
                    <input type="text" id="search_planned_arrival" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_planned_leave" class="hide2">Search by Planned Leave</label>
                    <input type="text" id="search_planned_leave" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_arrived" class="hide2">Search by Arrived</label>
                    <input type="text" id="search_arrived" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_left" class="hide2">Search by Left</label>
                    <input type="text" id="search_left" class="form-control search_init" name="" placeholder="Search" />
                </th>
                <th scope="col">
                    <label for="search_note" class="hide2">Search by Status</label>
                    <input type="text" id="search_note" class="form-control search_init" name="" placeholder="Search" />
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($attendance['timeslots'] as $timeslot){
                if ($timeslot['attendance_status'] == '') {
                    continue;
                }
            ?>
                <tr data-id="<?= $timeslot['booking_item_id']; ?>">
					<td><?= '#' . $timeslot['id'] . ' ' . $timeslot['schedule']; ?></td>
                    <td><?= $timeslot['course']; ?></td>
                    <td><?= $timeslot['plocation'] ? $timeslot['plocation'] : $timeslot['location']; ?></td>
                    <td><?= $timeslot['plocation'] ? $timeslot['location'] : ''; ?></td>
                    <td><span class="hidden"><?= date('Y-m-d', strtotime($timeslot['datetime_start'])); ?></span><?= date('j M Y', strtotime($timeslot['datetime_start'])); ?></td>
                    <td><?= date('H:i', strtotime($timeslot['datetime_start'])); ?></td>
                    <td><?= $timeslot['planned_arrival'] ? date('H:i', strtotime($timeslot['planned_arrival'])) : ''; ?></td>
                    <td><?= $timeslot['planned_leave'] ? date('H:i', strtotime($timeslot['planned_leave'])) : ''; ?></td>
                    <td><?= $timeslot['arrived'] ? date('H:i', strtotime($timeslot['arrived'])) : ''; ?></td>
                    <td><?= $timeslot['left'] ? date('H:i', strtotime($timeslot['left'])) : ''; ?></td>
                    <td><?= ($timeslot['planned_to_attend'] ? $timeslot['attendance_status'] : '') .
                        ($timeslot['note'] ? ' <span class="popinit" data-placement="left" rel="popover" data-content="' . html::chars($timeslot['note']) . '"><span class="icon-book"></span></span>'  : '');
                    ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no attendance records yet.</p>
<?php endif; ?>
