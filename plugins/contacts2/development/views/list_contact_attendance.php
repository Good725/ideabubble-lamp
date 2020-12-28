<div class="col-sm-12">
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
	<?php if ( ! empty($attendance)): ?>
		<table class="table table-striped dataTable attending_timeslots_table">
			<thead>
				<tr>
					<th scope="col">Schedule</th>
					<th scope="col">Course</th>
					<th scope="col">Location</th>
					<th scope="col">Room</th>
					<th scope="col">Date</th>
					<th scope="col">Time</th>
					<th scope="col">Attending</th>
				</tr>
			</thead>
			<thead>
				<tr>
					<th scope="col">
						<label for="search_schedule_id" class="sr-only">Search by Schedule</label>
						<input type="text" id="search_schedule_id" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_course" class="sr-only">Search by Course</label>
						<input type="text" id="search_course" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_location" class="sr-only">Search by Location</label>
						<input type="text" id="search_location" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_room" class="sr-only">Search by Room</label>
						<input type="text" id="search_room" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_date" class="sr-only">Search by Date</label>
						<input type="text" id="search_date" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_time" class="sr-only">Search by Time</label>
						<input type="text" id="search_time" class="form-control search_init" name="" placeholder="Search" />
					</th>
					<th scope="col">
						<label for="search_note" class="sr-only">Search by Status</label>
						<input type="text" id="search_note" class="form-control search_init" name="" placeholder="Search" />
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($attendance['timeslots'] as $timeslot): ?>
					<tr data-id="<?= $timeslot['booking_item_id']; ?>">
						<td><?= '#' . $timeslot['id'] . ' ' . $timeslot['schedule']; ?></td>
						<td><?= $timeslot['course']; ?></td>
						<td><?= $timeslot['plocation'] ? $timeslot['plocation'] : $timeslot['location']; ?></td>
						<td><?= $timeslot['plocation'] ? $timeslot['location'] : ''; ?></td>
						<td><?= date('d/m/Y', strtotime($timeslot['datetime_start'])); ?></td>
						<td><?= date('H:i', strtotime($timeslot['datetime_start'])); ?></td>
						<td><?= ($timeslot['attending'] ? 'Yes' : 'No') .
								($timeslot['timeslot_status'] ?  ': ' . $timeslot['timeslot_status'] : '') .
								($timeslot['note'] ? ' <span class="popinit" data-placement="left" rel="popover" data-content="' . html::chars($timeslot['note']) . '"><i class="icon-book"></i></span>'  : '');
							?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>There are no messages.</p>
	<?php endif; ?>
</div>
