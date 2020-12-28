<?php
$old_ui = !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'old_ui=');
$is_add_booking_screen = !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'bookings/add_edit');
?>

<div id="booking-calendar-alert_area"></div>

<?php
if ($old_ui && !empty($print_filter)) {
    echo View::factory('admin/generate_timetable_doc');
}
?>

<?php if (!$old_ui): ?>
    <?php
    $contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : null;
    $my = true;

    $filter_types = ['activities', 'blackouts', 'family_members', 'schedules', 'statuses'];
    $filter_args = [
        'contact_id' => $contact_id,
        'selected_contact_id' => isset($filter_contact_id) ? $filter_contact_id : null,
        'selected_activity' => 'booking',
        'selected_schedule_id' => isset($_POST['schedule_id']) ? $_POST['schedule_id'] : null
    ];

    if ($is_add_booking_screen) {
        $filter_args['statuses'] = ['booked']; // "booked" means "scheduled"
    }

    $filter_menu_options = Model_Timetables::get_filter_options($filter_types, $filter_args);

    echo View::factory('iblisting')->set([
        'bookings_enabled'    => true,
        'daterangepicker'     => true,
        'filter_menu_options' => $filter_menu_options,
        'id_prefix'           => 'booking-calender',
        'popover_mode'        => $my ? 'read' : 'edit',
//        'show_mine_only'      => $my,
        // 'timeslots_url'       => '/frontend/contacts3/get_timetables_data',
        'timeslots_url'       => '/admin/timetables/load_data',
        'views'               => ['calendar'],
    ])->render();
    ?>
<?php else: ?>
<div id="booking-fullcalendar-wrapper">
	<div class="booking-fullcalendar" id="booking-fullcalendar" data-events="<?= htmlentities(json_encode($calendar_events)) ?>"></div>

	<div class="hidden" id="calendar-popover-template">
		<div class="calendar-popover">
			<div class="calendar-popover-category"></div>
			<div class="calendar-popover-trainer"></div>
			<div class="calendar-popover-location"></div>
			<div><span class="calendar-popover-start_time"></span> &ndash; <span class="calendar-popover-end_time"></span></div>
			<div class="customize_register">
				<label><input type="radio" value="1" name="customize_register_place" class="customize no" /> &nbsp;Book All sessions</label> <br />
				<label><input type="radio" value="0" name="customize_register_place" class="customize yes" /> &nbsp;Custom</label>
			</div>

			<hr />

			<div class="form-action-group">
				<button
					type="button"
					class="btn btn-register register_place hidden">Register <span class="register_place-amount"></span>
				</button>
				<p class="calendar-popover-is_attending hidden">
					<span class="icon-check is_attending_icon"></span>
					Attending
				</p>
			</div>
		</div>
	</div>

    <div class="hidden" id="booking-fullcalendar-templates">
        <span class="booking-fullcalendar-flag booking-fullcalendar-flag--booked"><?= __('Booked') ?></span>
    </div>
</div>
<?php endif; ?>