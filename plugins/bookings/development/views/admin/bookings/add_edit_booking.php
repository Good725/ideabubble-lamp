<div id="add_edit_booking_wrapper" class="add_edit_booking_wrapper">
<div id="add-edit-booking-extra-headings" class="hidden">
    <?=$bill_payer_flag?>
    <?=$additional_flags?>
    <?=$booking_status_label ? '<span class="label location-flag">' . $booking_status_label . '</span>' : ''?>
</div>

<div id="booking_form_alerts" class="alert-area"><?= (isset($alert)) ? $alert : ''; ?>
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
<style>
    @media screen and (min-width: 768px) {
        #booking_schedules_list_table td{padding:4px;}
    }

    .footer {
        overflow: hidden;
    }

    .panel-title {
        font-size: 18px;
    }
</style>
<form class="educate-form form-horizontal kes_booking_edit_form" id="kes_booking_edit_form" method="post"
	  action="<?= URL::site(); ?>admin/bookings/process_booking">
<?php
$booking_id = $booking->get_booking_id();
$new = ($booking_id == '');
$status_name = $booking->orm()->status->title;
?>
<input type="hidden" name="edit_booking_id" value="<?=$edit_booking_id?>" />
<input type="hidden" name="booking_id" value="<?= $booking_id; ?>" id="booking_id"/>
<input type="hidden" id="calendar_data"
	   value="<?= htmlspecialchars(json_encode(Model_Schedules::get_all_contact_booked_dates($contact_data->get_id()))); ?>"/>
<input type="hidden" name="contact_id" id="contact_id" value="<?= $contact_data->get_id() ?>"/>
<input type="hidden" name="booking_items" id="booking_items"
	   value="<?= $new ? '' : htmlspecialchars($booking->get_booking_items(true)); ?>"/>
<input type="hidden" name="additional_booking_data" id="additional_booking_data"
	   value="<?= $new ? '' : htmlspecialchars($booking->get_additional_booking_details(true)); ?>"/>
<input type="hidden" name="redirect" id="redirect" value="save"/>
<input type="hidden" name="booking_status" id="booking_status"
	   value="<?= $new ? '' : $booking->get_booking_status(); ?>"/>
<input type="hidden" name="first_period_date" id="booking_first_period_date" value="<?= $first_period ?>">
<input type="hidden" name="amount" id="amount" value="<?= $new ? '' : $booking->get_booking_amount() ?>">
<input type="hidden" id="contact_study_year_id" name="study_year_id" value="<?= $contact_data->get_year_id(); ?>"/>
<input type="hidden" name="type" id="type" value="">
<input type="hidden" name="schedule_id" id="schedule_id" value="<?= $new ? '' : $booking->get_column('schedule_id') ?>">
<input type="hidden" id="multiple_transaction"
	   value="<?= isset($multiple_transaction) ? $multiple_transaction : '' ?>"/>
<?php
if ($link_contacts_to_bookings_access == '1'):
    $host_family = Model_KES_Bookings::get_linked_booking_contacts($booking_id,
        Model_Contacts3::find_type('Host Family')['contact_type_id']);
    $coordinator = Model_KES_Bookings::get_linked_booking_contacts($booking_id,
        Model_Contacts3::find_type('Coordinator')['contact_type_id']);
    $agent = Model_KES_Bookings::get_linked_booking_contacts($booking_id,
        Model_Contacts3::find_type('Agent')['contact_type_id']);
    ?>
<div class="bookings_contacts_linking col-sm-12 col-md-6">
    <h3 class="pb-3">Booking details</h3>
    <div class="host-contact-form">
        <label class="control-label align-left" for="contact-coordinator-id">Host</label>
        <input type="text" contact-type="Host Family" class="form-control host-name-input name ui-autocomplete-input linked-contact-booking-autocomplete" placeholder="Select contact"
               value="<?= (isset($host_family['name'])) ? $host_family['name'] : '' ?>">
        <input type="hidden" name="contact-host-id" id="contact-host-id" class="host-id linked-contact-id" value="<?= (isset($host_family['id'])) ? $host_family['id'] : '' ?>">
    </div>
    <div class="coordinator-contact-form">
        <label class="control-label align-left" for="contact-coordinator-id">Co Ordinator</label>
        <input type="text" contact-type="Coordinator" class="form-control coordinator-name-input name ui-autocomplete-input linked-contact-booking-autocomplete" placeholder="Select contact"
               value="<?= (isset($coordinator['name'])) ? $coordinator['name'] : '' ?>">
        <input type="hidden" name="contact-coordinator-id" id="contact-coordinator-id" class="coordinator-id linked-contact-id" value="<?= (isset($coordinator['id'])) ? $coordinator['id'] : '' ?>">
    </div>
    <div class="agent-contact-form">
        <label class="control-label align-left" for="contact-agent-id">Agent</label>
        <input type="text" contact-type="Agent"
               class="form-control agent-name-input name ui-autocomplete-input linked-contact-booking-autocomplete"
               placeholder="Select contact"
               value="<?= (isset($agent['name'])) ? $agent['name'] : '' ?>">
        <input type="hidden" name="contact-agent-id" id="contact-agent-id"
               class="agent-id linked-contact-id"
               value="<?= (isset($agent['id'])) ? $agent['id'] : '' ?>">
    </div>
</div>
<?php endif; ?>

<div class="panel panel-primary" id="booking-form-panel-contact">
    <div class="panel-heading" data-toggle="collapse" data-target="#booking-form-section-contact" aria-expanded="true">
        <button type="button" class="button--plain right expanded-invert">
            <span class="icon-angle-up"></span>
        </button>
        <h3 class="panel-title">Find a contact</h3>
    </div>

    <div class="panel-body collapse in" id="booking-form-section-contact">
        <div id="booking_select_contact_fieldset" class="booking_select_contact_fieldset panel-primary">

            <section class="booking-qty-set">
                <div class="form-row gutters">
                    <div class="col-sm-4 col-md-2">
                        <label class="control-label" for="booking_qty_type">Booking Type</label>
                    </div>

                    <div class="col-sm-8 col-md-4">
                        <label class="form-select">
                            <input type="hidden" id="bookings_require_primary_biller_organisation_booking" value="<?=Settings::instance()->get('bookings_require_primary_biller_organisation_booking')?>" />
                            <select name="booking_qty_type" class="form-input" id="booking_qty_type">
                                <option value="single" <?=!is_numeric($booking_id) ? 'selected="selected"' : ''?>><?=__('Booking for a single student')?></option>

                                <?php if (Auth::instance()->has_access('bookings_create_for_delegates')): ?>
                                    <option value="delegates" <?= is_numeric($booking_id) && count(Model_KES_Bookings::get_delegates($booking_id)) > 0 ? 'selected="selected"' : '' ?> ><?=__('Organization booking for multiple delegates')?></option>
                                <?php endif; ?>

                                <?php if (Auth::instance()->has_access('bookings_create_for_multiple')): ?>
                                    <option value="multiple"><?=__('Booking for multiple students')?></option>
                                <?php endif; ?>
                            </select>
                        </label>
                    </div>
                </div>
            </section>
            <section class="booking-select_contact panel-heading">
                <div class="form-row gutters" style="margin-bottom: 0;">
                    <div class="col-sm-8">
                        <h3 id="booking-select_contact-heading"><span class="student"><?=__('Student')?> </span><span class="organization hidden"><?=__('Organization')?> </span> name</h3>

                        <div class="row gutters booking-contact-details hidden" id="ajax_contact_data">
                            <div class="col-sm-4 booking-contact-details-column">
                                <h3>
                                    <span class="first_name"></span>
                                    <span class="last_name"></span>
                                </h3>
                            </div>

                            <div class="col-sm-4 booking-contact-details-column">
                                <ul class="list-unstyled">
                                    <li><span class="address1"></span></li>
                                    <li><span class="address2"></span></li>
                                    <li><span class="address3"></span></li>
                                    <li><span class="town"></span></li>
                                    <li><span class="postcode"></span></li>
                                </ul>

                                <span class="county hidden"></span>
                                <span class="country hidden"></span>
                            </div>

                            <div class="col-sm-4 booking-contact-details-column">
                                <ul class="list-unstyled">
                                    <li><span class="mobile"></span></li>
                                    <li><span class="email"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <input type="text" class="form-control validate[required]" id="select_contact" name="select_contact"
                               value="<?= trim($contact_data->get_first_name().' '.$contact_data->get_last_name()) ?>"
                               placeholder="Enter mobile / family / last name">
                    </div>
                </div>
            </section>
            <section id="lead-booker-wrapper" class="hidden">
                    <div class="lead-booker-details">
                        <div class="form-group">
                            <div class="col-xs-auto col-sm-4 col-md-2">
                                <label class="control-label" for="lead-booker">Lead booker</label>
                            </div>

                            <div class="col-xs-12 col-sm-8 col-md-4">
                                <input type="hidden" name="lead_booker_id" class="form-input" id="lead_booker_id">
                                <?= Form::ib_input(null, 'lead_booker', null, ['id' => 'select_lead_booker', 'placeholder' => 'Enter mobile / family / last name']) ?>
                            </div>
                        </div>
                    </div>
            </section>

            <section id="delegates-wrapper" class="delegates-wrapper hidden">
                <header><?=__('Delegates')?></header>
                <table class="table table-striped booking-delegates-list dataTable-collapse" id="booking-delegates-list">
                    <thead>
                        <tr id="booking-delegates-list-add">
                            <th><h3><?=__('Select delegate')?></h3></th>
                            <th style="font-weight: normal;">
                                <input type="text" class="form-control" id="select_delegate" value="" placeholder="Enter mobile / family / last name">
                            </th>
                            <th><button type="button" class="delegate-add btn"><?=__('Add delegate')?></button> </th>
                        </tr>

                        <tr>
                            <th colspan="2"><?=__('Name')?></th>
                            <th><?=__('Action')?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="hidden delegate-row-template">
                            <td colspan="2"><input type="hidden" class="delegate-id" name="delegate_ids[]" value="" /><span class="delegate-name"></span></td>
                            <td><button type="button" class="delete-delegate btn"><?=__('Remove')?></button> </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section id="students-wrapper" class="hidden">
                <header><?=__('Students')?></header>
                <table class="table table-striped booking-delegates-list dataTable-collapse" id="booking-students-list">
                    <thead>
                    <tr>
                        <th><h3><?=__('Select Student')?></h3></th>
                        <th style="font-weight: normal;">
                            <input type="text" class="form-control" id="select_student" value="" placeholder="enter mobile / family / last name">
                        </th>
                        <th><button type="button" class="student-add btn"><?=__('Add Student')?></button> </th>
                    </tr>

                    <tr id="booking-students-list-add_by_tag">
                        <th><h3><?=__('Select tag')?></h3></th>
                        <th style="font-weight: normal;">
                            <?php
                            $attributes = [
                                'class' => 'ib-combobox',
                                'id' =>'students-filter-tag',
                                'data-placeholder' => 'Enter tag...'
                            ];
                            $options = '<option></option>'.Model_Contacts3_Tag::get_all()->as_options(['name_column' => 'label', 'please_select' => false]);
                            echo Form::ib_select(null, 'add_tag', $options, null, $attributes);
                            ?>
                        </th>
                        <th><button type="button" class="student-add_by_tag btn"><?=__('Add students')?></button> </th>
                    </tr>

                    <tr><th colspan="2"><?=__('Name')?></th><th><?=__('Action')?></th></tr>
                    </thead>

                    <tbody>
                    <tr class="hidden student-row-template">
                        <td colspan="2"><input type="hidden" class="student-id" name="student_ids[]" value="" /><span class="student-name"></span></td>
                        <td><button type="button" class="delete-student btn"><?=__('Remove')?></button> </td>
                    </tr>
                    </tbody>
                </table>

                <?php
                echo View::factory('snippets/modal')->set([
                    'id'     => 'students-missing-tag-modal',
                    'title'  => 'No tag selected',
                    'body'   => 'You must select a tag in order to perform this action.',
                    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>'
                ]);
                ?>

            </section>

            <h2 hidden><?= __('Create a new booking') ?></h2>

        </div>
    </div>
</div>

<?php
$hide_transfers = empty($transfer_booking_id);
$hide_schedules_list = ($booking->get_booking_id() > 0 && !$edit_booking_id);
?>
    <div class="schedule-range-dropdown hidden dropdown left">
        <button type="button" style="width: 6rem;"
                class="btn btn-default button--full form-btn mb-2 dropdown-toggle timeoff-grid_period-button"
                id="booking-time_range-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Range
        </button>

        <ul class="timeoff-grid_period-options dropdown-menu pull-right" aria-labelledby="timeoff-grid_period-button"
            id="timeoff-grid_period-options" style="left: 0;">
            <li>
                <div class="text-uppercase py-2 px-4">Range</div>
            </li>
            <li>
                <label class="timeoff-radio-bullet w-100">
                    <input type="radio" class="sr-only timeoff-grid_period" name="timeslots_range" value="all"
                           checked="checked"/>
                    <span>All</span>
                </label>
            </li>
            <li>
                <label class="timeoff-radio-bullet w-100">
                    <input type="radio" class="sr-only timeoff-grid_period" name="timeslots_range" value="past">
                    <span>Past</span>
                </label>
            </li>
            <li>
                <label class="timeoff-radio-bullet w-100">
                    <input type="radio" class="sr-only timeoff-grid_period" name="timeslots_range" value="upcoming">
                    <span>Upcoming</span>
                </label>
            </li>
        </ul>
    </div>

    <div class="m-md-auto hidden" id="booking-form-select_type-wrapper">
        <span class="d-inline-block mr-1">Select</span>
        <?php
        echo Form::btn_options(
            'select_type',
            ['single' => __('Single'), 'multiple' => __('Multiple')],
            'single',
            false,
            ['class' => 'booking-form-select_type', 'style' => 'width: 50%;'],
            ['class' => 'stay_inline d-sm-inline-block w-auto']
        );
        ?>
    </div>

    <div class="panel panel-primary<?= $hide_transfers && $hide_schedules_list ? ' hidden' : '' ?>"
         id="booking-form-panel-schedules">
    <div class="panel-heading" data-toggle="collapse" data-target="#booking-form-section-schedules" aria-expanded="true">
        <button type="button" class="button--plain right expanded-invert">
            <span class="icon-angle-up"></span>
        </button>

        <h3 class="panel-title"><?= __('Select a schedule') ?></h3>
    </div>

    <div class="panel-body collapse in" id="booking-form-section-schedules">
        <div class="timetable_selection_wrapper">
            <section id="transfer_booking"<?= $hide_transfers ? ' style="display:none;"' : '' ?>>
                <h3 class="pb-3">Transfer Booking</h3>
                <div class="form-group col-sm-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking Schedule</th>
                                <th>Credit</th>
                                <th>Check to Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $transfer_credit = "";
                            $ts_index = 0;
                            ?>
                            <?php foreach ($existing_bookings as $existing_booking)
                            { ?>
                                <?php if (in_array($existing_booking['status'], array('Confirmed', 'In Progress')))
                            { ?>
                                <?php foreach ($existing_booking['schedules'] as $existing_tschedule)
                            { ?>
                                <?php
                                if (in_array($existing_tschedule['status'], array('Cancelled'))) {
                                    continue;
                                }
                                if ($existing_booking['booking_id'] == $transfer_booking_id || $transfer_booking_id == 'all')
                                {
                                    $transfer_credit = $existing_tschedule['default_transfer_credit'];
                                }
                                else
                                {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td><?=$existing_booking['booking_id'].'; Schedule #'.$existing_tschedule['id'].'; '.$existing_tschedule['name']?></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text"
                                                   name="cancel_booking_schedule[<?= $ts_index ?>][credit]"
                                                   class="form-control credit" value="<?= $transfer_credit ?>"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="hidden" class="booking_id"
                                                   name="cancel_booking_schedule[<?= $ts_index ?>][booking_id]"
                                                   value="<?= $existing_booking['booking_id'] ?>"/>
                                            <input type="hidden" class="schedule_id"
                                                   name="cancel_booking_schedule[<?= $ts_index ?>][schedule_id]"
                                                   value="<?= $existing_tschedule['id'] ?>"/>
                                            <input type="checkbox"
                                                   name="cancel_booking_schedule[<?= $ts_index ?>][confirm]"
                                                   class="form-control confirm" value="1"/>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <?php
            // Options for dropdown filters. (Fetching directly here for now, but this should really be passed in via the controller)
            $subjects_list   = DB::select(array('name',     'title'))->from('plugin_courses_subjects'  )->where('publish', '=', 1)->where('deleted', '=', 0)->order_by('name'    )->execute()->as_array();
            $courses_list    = DB::select(array('title',    'title'))->from('plugin_courses_courses'   )->where('publish', '=', 1)->where('deleted', '=', 0)->order_by('title'   )->execute()->as_array();
            $categories_list = DB::select(array('category', 'title'))->from('plugin_courses_categories')->where('publish', '=', 1)->where('delete',  '=', 0)->order_by('category')->execute()->as_array();
            $years_list      = DB::select(array('year',     'title'))->from('plugin_courses_years'     )->where('publish', '=', 1)->where('delete',  '=', 0)->order_by('year'    )->execute()->as_array();
            $levels_list     = DB::select(array('level',    'title'))->from('plugin_courses_levels'    )->where('publish', '=', 1)->where('delete',  '=', 0)->order_by('level'   )->execute()->as_array();
            ?>

            <section id="booking_schedules_list"<?=$hide_schedules_list ? ' class="hidden"' : ''?>>
                <table id="booking_schedules_list_table" class="table table-striped mode--single">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode(array('Ennis', 'Limerick'))) ?>">Location</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode($subjects_list))   ?>">Subject</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode($courses_list))    ?>">Course</th>
                            <th scope="col" data-options_src="/admin/bookings/ajax_search_schedules">Schedule</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode($categories_list)) ?>">Category</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode($years_list))      ?>">Year</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode($levels_list))     ?>">Level</th>
                            <th scope="col" data-options_src="/admin/bookings/ajax_search_trainers">Trainer</th>
                            <th scope="col" data-options="<?= htmlentities(json_encode([
                                ['id' => 'Mon', 'title' => __('Monday')   ],
                                ['id' => 'Tue', 'title' => __('Tuesday')  ],
                                ['id' => 'Wed', 'title' => __('Wednesday')],
                                ['id' => 'Thu', 'title' => __('Thursday') ],
                                ['id' => 'Fri', 'title' => __('Friday')   ],
                                ['id' => 'Sat', 'title' => __('Saturday') ],
                                ['id' => 'Sun', 'title' => __('Sunday')   ]
                            ])) ?>">Day and time</th>
                            <th scope="col">Next Timeslot</th>
                            <th scope="col">Fee</th>
                            <th scope="col">Payment</th>
                            <th scope="col">Booked</th>
                            <th scope="col">Classes</th>
                            <th scope="col">Select</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>

                <div class="form-actions form-action-group py-3" style="clear: both;">
                    <button type="button" class="btn btn-default clear">Clear timetable</button>
                    <button type="button" class="btn btn-primary display_timetable">Show on timetable</button>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="panel panel-primary" id="booking-form-panel-timetable">
    <div class="panel-heading" data-toggle="collapse" data-target="#booking-form-section-timetable" aria-expanded="true">
        <button type="button" class="button--plain right expanded-invert">
            <span class="icon-angle-up"></span>
        </button>

        <h3 class="panel-title"><?= __('Timetable') ?></h3>
    </div>

    <div class="panel-body collapse in" id="booking-form-section-timetable">

        <section id="timetable_filters_fieldset" class="timetable_filters_fieldset hidden">
            <h3 class="pb-3">Select a Course</h3>
            <div class="form-group text-left">
                <label class="col-sm-3 control-label">Select a course, using the fields below</label>
                <div class="col-sm-4">
                <div class="alert-area" id="timetable_selection_alert_area"></div>

                <input type="hidden" name="select_course_name_id" id="select_schedule_name_id"/>
                <input type="text" placeholder="Search for a schedule" name="select_schedule_name"
                       class="form-input select_course_name validate[funcCall[validate_schedule_location_category]]"
                       id="select_schedule_name"/>

                <ul id="search_course_bar" class="row gutters mt-3 list-unstyled search_course_bar">
                    <li class="col-sm-6 mb-3">
                        <select name="select_location"
                                class="form-control select_course_dropdown validate[funcCall[validate_schedule_location_category]]"
                                data-placeholder="Select Location" id="select_location">
                            <option value=""></option>
                            <?php foreach ($course_locations as $key => $location): ?>
                                <?php if ($location['parent_id'] == null)
                                { ?>
                                    <option value="<?= $location['id']; ?>"><?=$location['name'];?></option>
                                <?php } ?>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li class="col-sm-6 mb-3">
                        <select name="select_category"
                                class="form-control select_course_dropdown validate[funcCall[validate_schedule_location_category]]"
                                data-placeholder="Select Category" id="select_category">
                            <option value=""></option>
                            <?php foreach ($categories as $key => $category): ?>
                                <option value="<?= $category['id']; ?>"><?=$category['category'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li class="col-sm-6 mb-3">
                        <select name="select_subject" class="form-control" id="select_subject"
                                data-placeholder="Select Subject">
                            <option value="">All Subjects</option>
                            <?php foreach ($subjects as $key => $subject): ?>
                                <option value="<?= $subject['id']; ?>"><?= $subject['name'] ;?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li class="col-sm-6 mb-3">
                        <select name="select_year" class="form-control select_course_dropdown" id="select_year"
                                data-placeholder="Select Year">
                            <option value="">All Years</option>
                            <?php foreach ($years as $key => $year): ?>
                                <option value="<?= $year['id']; ?>"><?=$year['year'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li class="col-sm-12 form-action-group text-left">
                        <button type="button" class="btn  btn-primary" id="search_courses_schedules" data-original-title="Search"
                                data-content="Search Schedules using selected parameters">Search
                        </button>
                    </li>
                </ul>
            </div>
        </section>

        <div class="tab-content" style="padding:0;">
            <div class="tab-pane active" id="category_tab">

            </div>
        </div>
    </div>
</div>

<div class="time_results_wrapper">

<section>
	<header class="hidden">
		<div class="btn-group pull-right confirmed_periods">
			<a href="#" data-toggle="dropdown" class="btn dropdown-toggle">Actions<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a class="attend_all_timeslots">Attend All</a></li>
				<li><a class="attend_no_timeslots">Attend None</a></li>
				<li><a href="#bulk-update-modal" data-toggle="modal" data-target="#bulk-update-modal">Bulk Update</a>
				</li>
			</ul>
		</div>
		<h3>Time Slots</h3>
		<button type="button" class="minimize_button" data-minimize="time_slots_section_content" title="Minimise">_
		</button>
	</header>
	<div class="time_slots_section_content hidden" id="time_slots_section_content">
		<div class="confirmed_periods_table_wrapper">
			<table class="table table-striped confirmed_periods_table" id="confirmed_periods_table">
				<thead>
					<tr>
						<th scope="col">Course</th>
						<th scope="col">Schedule</th>
						<th scope="col">Day</th>
						<th scope="col">Date</th>
						<th scope="col">Time</th>
						<th scope="col">Attend</th>
						<th scope="col">Note</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>

			<?php
			$prefix = '';
			include 'snippets/bulk_update_modal.php';
			?>

		</div>
	</div>
</section>
    <?php if(Settings::instance()->get('how_did_you_hear_enabled') == 1 || Settings::instance()->get('cart_special_requirements_enable') == 1):?>
        <div class="panel panel-primary">
        <div class="panel-heading" data-toggle="collapse" data-target="#booking-form-section-additional-info" aria-expanded="true">
            <button type="button" class="button--plain right expanded-invert">
                <span class="icon-angle-up"></span>
            </button>

            <h3 class="panel-title"><?= __('Additional Info') ?></h3>
        </div>
        <div class="panel-body collapse in" id="booking-form-section-payment">
            <section>
                <?php if(Settings::instance()->get('how_did_you_hear_enabled') == 1):?>
                    <div class="form-group">
                    <div class="col-sm-4">
                        <?php $how_did_you_hear = Model_Lookup::lookupList('How did you hear'); ?>
                        <label for="how_did_you_hear"> How did you hear about us?</label>
                        <?= Form::ib_select('', 'how_did_you_hear', html::optionsFromRows('value', 'label', $how_did_you_hear), null, array('disabled' => true, 'id'=>'how_did_you_hear'), array('please_select' => true))?>
                    </div>
                </div>
                <?php endif?>
                <?php if(Settings::instance()->get('cart_special_requirements_enable') == 1):?>
                    <div class="form-group" id="checkout-special-requirements-wrapper">
                        <div class="col-sm-12">
                            <label for="special_requirements"><?= __('Listed special requirements, including delegate names') ?></label>
                            <?php
                            $attributes = ['id' => 'special_requirements', 'rows' => 5, 'style' => 'resize: vertical;'];
                            echo Form::ib_textarea(null, 'special_requirements','', $attributes);
                            ?>
                        </div>
                    </div>
                <?php endif?>
            </section>
        </div>
    <?endif?>
<div class="panel panel-primary">
    <div class="panel-heading" data-toggle="collapse" data-target="#booking-form-section-payment" aria-expanded="true">
        <button type="button" class="button--plain right expanded-invert">
            <span class="icon-angle-up"></span>
        </button>

        <h3 class="panel-title"><?= __('Payment details') ?></h3>
    </div>

    <div class="panel-body collapse in" id="booking-form-section-payment">
        <section>

        <table class="table table-striped booking-schedules-list dataTable-collapse" id="booking-schedules-list">
            <thead>
                <tr>
                    <th scope="col" class="hidden select-schedule">&nbsp;</th>
                    <th scope="col">Category</th>
                    <th scope="col">Schedule title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Classes</th>
                    <th scope="col">Attendance</th>
                    <th scope="col">Created</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price</th>
                    <th scope="col">Discount</th>
                    <th scope="col">Next payment</th>
                    <th scope="col">Due</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr data-schedule_id="" data-amount="">
                    <td scope="col" class="hidden select-schedule">&nbsp;</td>
                    <td colspan="8">&nbsp;</td>
                    <td class="bdiscount">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td>
                        <button type="button"
                                class="btn btn-default booking-discount-modal-display"
                                data-toggle="modal"
                                data-target="#booking-discount-modal">Booking Discounts
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="modal fade booking-discount-modal" tabindex="-1" role="dialog" id="booking-discount-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header booking-discount-modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Apply Discount - <span>&nbsp;</span></h4>
                    </div>
                    <div class="modal-body clearfix">
                        <p>Discounts</p>

                        <div class="row">
                            <div class="col-sm-4">Schedule</div>
                            <div class="col-sm-8 schedule-title">&nbsp;</div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">Booking Amount</div>
                            <div class="col-sm-8"><span class="label label-default">&euro;</span> <span class="fee"></span>
                            </div>
                        </div>

                        <div style="margin-top: .5em;">
                            <table class="table table-striped discounts-table dataTable">
                                <thead>
                                <tr>
                                    <th scope="col">Discount</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Amount (&euro;)</th>
                                    <th scope="col">New Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="add-coupon">
                                    <td>
                                        <label>
                                            <span class="sr-only">Enter coupon code</span>
                                            <input type="text" class="form-control coupon"
                                                   placeholder="Enter coupon code" style="width: auto;" name="coupon"/>
                                        </label>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default">Add Coupon</button>
                                    </td>
                                    <td>
                                        <label>
                                            <span class="sr-only">Discount amount</span>
                                            <input type="text" class="form-control " value="" style="width: 90px;"
                                                   readonly="readonly"/>
                                        </label>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="add-custom">
                                    <td>
                                        <label>
                                            <span class="sr-only">Custom Discount</span>
                                            <input type="text" class="form-control custom"
                                                   placeholder="Enter custom discount" style="width: auto;"
                                                   name="custom"/>
                                        </label>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default">Apply</button>
                                    </td>
                                    <td>
                                        <label>
                                            <span class="sr-only">Discount amount</span>
                                            <input type="text" class="form-control negative" value=""
                                                   style="width: 90px;" readonly="readonly"/>
                                        </label>
                                    </td>
                                    <td class="balance"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="apply-discount-memo">Discount memo</label>

                            <div class="col-sm-9">
                                <textarea class="form-control memo" id="apply-discount-memo" style="max-width: 100%;"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="apply-discount-price">Price (&euro;)</label>

                            <div class="col-sm-9">
                                <input type="text" readonly="readonly" class="form-control price" id="apply-discount-price"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="apply-discount-discounts">Discounts (&euro;)</label>

                            <div class="col-sm-9">
                                <input type="text" readonly="readonly" class="form-control discounts" id="apply-discount-discounts"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="apply-discount-subtotal">Sub Total (&euro;)</label>

                            <div class="col-sm-9">
                                <input type="text" readonly="readonly" class="form-control subtotal" id="apply-discount-subtotal"/>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success apply">Apply Discounts</button>
                        <button type="button" class="btn-link" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (Settings::instance()->get('schedule_enable_invoice')) { ?>

            <?php
            $schedules = $booking->get_booking_schedules();
            $flexi_delegate = (isset($schedules[0]) && $schedules[0]['charge_per_delegate'] == 0);
            ?>

            <section id="delegates-wrapper">
                <?php if (Settings::instance()->get('edit_booking_po_number')): ?>
                    <div class="invoice-details col-sm-12">
                        <div class="form-group">
                            <div class="col-sm-4"><label><?= __('Purchase order number') ?>:</label></div>
                            <div class="col-xs-12 col-sm-8 col-md-5">
                                <?= Form::ib_input(null, 'purchase_order_number', $booking->invoice_details, ['id' => 'purchase_order_number', 'placeholder' => 'Enter purchase order number', 'data-mandatory' => Settings::instance()->get('booking_invoice_number_is_mandatory')])?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php // So this field's value does not get lost if the setting is temporarily off. ?>
                    <input type="hidden" name="purchase_order_number" value="<?= $booking->invoice_details ?>" data-mandatory="<?=Settings::instance()->get('booking_invoice_number_is_mandatory')?>"/>
                <?php endif; ?>
                <?php if (is_numeric($booking_id)) { ?>
                <div class="delegates-wrapper">

                    <div class="row gutters vertically_center">
                        <div class="col-sm-3">
                            <h3><?= __('Delegates') ?></h3>
                        </div>

                        <?php if ($flexi_delegate): ?>
                            <div class="col-sm-6">
                                <div class="form-ajax_typeselect-wrapper">
                                    <input type="text" class="form-input form-ajax_typeselect" id="booking-delegates-list-new-input" value="" placeholder="Enter mobile / family / last name" />
                                    <input type="hidden" class="form-ajax_typeselect-value" id="booking-delegates-list-new-id" />

                                    <button type="button" class="btn-link form-typeselect-clear">
                                        <span class="fa fa-remove-circle icon-remove-circle"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <button
                                    type="button" class="btn form-btn w-100"
                                    id="booking-delegates-list-new-btn">Add delegate</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <table class="table table-striped booking-delegates-list dataTable-collapse" id="booking-delegates-list">
                        <thead>
                            <tr>
                                <th scope="col"><?=__('Name')?></th>
                                <th scope="col"><?=__('Organisation')?></th>
                                <th scope="col"><?=__('Cancelled')?></th>
                                <th scope="col"><?=__('Cancellation reason')?></th>
                                <?php if ($flexi_delegate): ?>
                                    <th scope="col"><?=__('Remove')?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $delegates = Model_KES_Bookings::get_delegates($booking_id, ['include_cancelled' => true]);
                        foreach ($delegates as $delegate) { ?>
                            <tr data-id="<?= $delegate['id'] ?>">
                                <td>
                                    <input type="hidden" class="delegate-id" name="delegate_ids[]" value="<?=$delegate['id']?>" />
                                    <span class="delegate-name"><?= htmlspecialchars($delegate['first_name'] . ' ' . $delegate['last_name']) ?></span>
                                </td>
                                <td class="delegate-organisation_name"><?= htmlspecialchars($delegate['organisation_name']) ?></td>
                                <td><?= $delegate['cancelled'] ? 'Yes' : 'No' ?></td>
                                <td><?= $delegate['cancel_reason_code'] ? htmlspecialchars(Model_Lookup::get_label('Booking cancellation reason', $delegate['cancel_reason_code'])) : '' ?></td>
                                <?php if ($flexi_delegate): ?>
                                    <td>
                                        <button type="button" class="btn-link" data-toggle="modal" data-target="#delegate-remove-modal">
                                            <span class="icon-times"></span>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php } ?>
                        </tbody>

                        <tfoot class="hidden">
                            <tr id="booking-delegates-list-template">
                                <td>
                                    <input type="hidden" class="delegate-id" name="delegate_ids[]" disabled="disabled" />
                                    <span class="delegate-name"></span>
                                </td>
                                <td class="delegate-organisation_name"></td>
                                <td>No</td>
                                <td></td>
                                <?php if ($flexi_delegate): ?>
                                    <td>
                                        <button type="button" class="btn-link" data-toggle="modal" data-target="#delegate-remove-modal">
                                            <span class="icon-times"></span>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>

                    <?php
                    echo View::factory('snippets/modal')->set([
                        'id'     => 'delegate-duplicate-modal',
                        'title'  => 'Delegate already added',
                        'body'   => 'This delegate has already been added to the booking.',
                        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>'
                    ]);
                    ?>

                    <?php ob_start() ?>
                        <button type="button" class="btn btn-danger" id="delegate-remove-btn-confirm">Remove</button>
                        <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <?php $buttons = ob_get_clean(); ?>

                    <?php
                    echo View::factory('snippets/modal')->set([
                        'id'     => 'delegate-remove-modal',
                        'title'  => 'Confirm removal',
                        'body'   => 'Are you sure you wish to remove this delegate from this booking?',
                        'footer' => $buttons
                    ]);
                    ?>
                </div>
                <?php } ?>
            </section>
        <?php } ?>

        <div class="col-sm-3 payment_details_column">
            <dl style="font-weight: bold;">
                <dt class="col-sm-6">Sub Total</dt>
                <dd class="col-sm-6">
                    <span class="label label-default">&euro;</span> <span class="subtotal">0.00</span>
                </dd>

                <dt class="col-sm-6">Discounts</dt>
                <dd class="col-sm-6">
                    <span class="label label-default">&euro;</span> <span class="discounts">0.00</span>
                </dd>

                <dt class="col-sm-6 text-danger">Due Now</dt>
                <dd class="col-sm-6 text-danger">
                    <span class="label label-danger">&euro;</span> <span class="total">0.00</span>
                </dd>
            </dl>
            <!-- <div class="text-center">
                <button type="button" class="btn btn-default" style="margin-top: 1.5rem;padding-right: 3rem;padding-left: 3rem;">Pay Now</button>
            </div> -->

        </div>

        </section>
    </div>
</div>
</div>

<?php
if ($booking->extra_data) {
?>
<div id="modal_extra_details" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">View Extra Details</h3>
            </div>
            <div class="modal-body">
            <?php
            $extra_data = $booking->extra_data;
			if ($extra_data) {
				require Kohana::find_file('views', 'application_additional_information');
				require Kohana::find_file('views', 'application_travel_details');
			}
            ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"
                        data-content="Close the course detail window">Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
<div class="ol-md-14 booking-send-email-wrapper">
    <?= Form::ib_checkbox_switch('Send booking email', 'booking-send-email', 1, false, ['id' => 'booking-send-email']) ?>
</div>
<div class="form-action-group action-buttons" id="booking-form-actions" style="clear:both;">
    <p id="booking-form-no_session_selected"><?=
            (@$potential_booking_application !== null && @$potential_booking_application['status'] === 'Enquiry')
            ? __('You cannot edit a booking application that has not been confirmed yet. Go to the applications tab and confirm the booking.')
            : __('You must select a session from the timetable in order to continue.');
         ?></p>

    <?php
    $status = $booking->get_booking_status();
    $save_button = ' <button type="button" class="btn btn-primary" id="booking_save" disabled="disabled" data-original-title="Save" data-content="This will create an Enquiry booking">Save</button>';
    if ($status < 2 || $edit_booking_id):
        ?>
        <button type="button" class="btn btn-primary" id="booking_book" disabled="disabled"
                data-original-title="Book" data-content="This will create a Confirmed booking">Book
        </button>

        <?php if (Auth::instance()->has_access('bookings_book_and_pay')): ?>
            <button
                type="button"
                class="btn btn-success"
                id="booking_book_and_pay"
                disabled="disabled"
                data-original-title="Book and Pay"
                data-content="This will create a confirmed booking and present a payment screen. Booking emails will not be sent until a payment has been processed in the next step."
            >
                Book and Pay
            </button>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('bookings_book_and_bill')): ?>
            <button
                type="button"
                class="btn btn-success"
                id="booking_book_and_bill"
                disabled="disabled"
                data-original-title="Book and Bill"
                data-content="This will create a confirmed booking and present a billing screen For the pre-pay bookings."
            >
                Book and Bill
            </button>
        <?php endif; ?>

        <button type="button" class="btn btn-default hidden" id="booking-create_sales_quote">
            Create Sales Quote
        </button>
    <?php
    endif;
	//confirmed or in progress
	if (in_array($status, array(2, 4)) && !$edit_booking_id):
		?>
		<button type="button" class="btn btn-primary" id="booking_save_change" data-original-title="Save Change"
				data-content="This will update a Confirmed booking">Save Changes
		</button>
	<?php
	endif; ?>

    <?php if ($status_name == 'Sales Quote'): ?>
        <button type="button" class="btn btn-primary" id="booking-confirm">
            <?= __('Confirm Booking') ?>
        </button>
    <?php endif; ?>

    <?php
	// not cancelled, not completed
	if ($booking->get_booking_id() > 0 AND !in_array($status, array(3, 5)) AND !$edit_booking_id):
		?>
		<?php if (Model_KES_Bookings::has_subscription($booking->get_booking_id())) { ?>
		<a class="btn email_subscription"
		   data-original-title="Email Subscription Link"
		   data-content=""
		>Email Subscription Link</a>
		<?php } ?>

        <?php if (Settings::instance()->get('accreditation_application_page') != ''): ?>
            <a class="btn email_accreditation"
               data-original-title="Email Accreditation Application Link"
               data-content=""
            >Email Accreditation Application Link</a>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('bookings_view_additional')): ?>
            <a class="btn view_modal_extra_details"
               data-content="View Additional Details"
               data-toggle="modal"
               data-target="#modal_extra_details"
            >View Additional Details</a>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('bookings_transfer')): ?>
            <a href="/admin/bookings/add_edit_booking/<?= '?contact='.$contact_data->get_id().'&transfer_booking_id='.$booking->get_booking_id() ?>"
               class="btn add_contact_booking"
               id="booking_transfer_start"
               data-contact_id="<?= $contact_data->get_id(); ?>"
               data-booking_id="<?= $booking->get_booking_id() ?>"
               data-original-title="Transfer Booking"
               data-content="This will allow you to transfer to another schedule">Transfer to Another Schedule</a>
        <?php endif; ?>

        <?php if (Auth::instance()->has_access('bookings_add_schedules')): ?>
            <a href="/admin/bookings/add_edit_booking/<?= $booking->get_booking_id() . '?contact='.$contact_data->get_id().'&edit_booking_id='.$booking->get_booking_id() ?>"
               class="btn add_contact_booking"
               id="booking_edit_start"
               data-contact_id="<?= $contact_data->get_id(); ?>"
               data-booking_id="<?= $booking->get_booking_id() ?>"
               data-original-title="Add New Schedules Booking"
               data-content="This will allow you to add new schedules to an existing booking">Add Schedules</a>
        <?php endif; ?>

        <?php if ($status_name != 'Sales Quote'): ?>
            <button type="button" class="btn btn-danger"
                id="booking_cancel_booking_multiple"
                data-original-title="Cancel Booking"
                data-content="This will change a Confirmed Booking or Enquiry and mark as Cancelled"
                data-contact_id="<?= $contact_data->get_id(); ?>"
                data-booking_id="<?= $booking->get_booking_id() ?>">Cancel Booking
            </button>
        <?php endif; ?>
	<?php
	endif;
	?>
	<button type="button" class="btn-cancel" id="booking_cancel" data-original-title="Cancel"
			data-content="This will cancel all unsaved items.">Cancel
	</button>
</div>
<div class="floating-nav-marker"></div>
</form>

<? // Modal Boxes ?>
<? // Register Modal ?>
<div id="register_modal" class="modal fade register_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header register_modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title register_modal-title" id="myModalLabel"></h3>
			</div>

			<div class="form-horizontal modal-body">
				<div id="modal_display_schedule" data-booking_type="">
					<fieldset>
						<legend>Student Details</legend>
						<div id="student_name_modal_display"></div>
					</fieldset>
					<fieldset>
						<legend>Course Details</legend>
						<div id="register_modal_course_details"></div>
					</fieldset>
					<fieldset>
						<legend>Period Attending</legend>
						<div class="period_booking_table_wrapper">
							<p>Any absences must have their reason outlined in the corresponding note field.</p>

							<div class="btn-group pull-right">
								<a href="#" data-toggle="dropdown"
								   class="btn btn-primary dropdown-toggle text-uppercase">Actions <span
										class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a class="attend_all_timeslots">Attend All</a></li>
									<li><a class="attend_no_timeslots">Attend None</a></li>
									<li><a href="#display-schedule-bulk-update-modal" data-toggle="modal"
										   data-target="#display-schedule-bulk-update-modal">Bulk Update</a></li>
								</ul>
							</div>

							<?php
							$prefix = 'display-schedule-';
							include 'snippets/bulk_update_modal.php';
							?>

							<table class="table table-striped dataTable" id="period_booking_table">
								<thead>
									<tr>
										<th scope="col">Course</th>
										<th scope="col">Schedule</th>
										<th scope="col">Day</th>
										<th scope="col">Date</th>
										<th scope="col">Time</th>
										<th scope="col" class="fee_per_timeslot">Price</th>
										<th scope="col">Attend</th>
										<th scope="col">Note</th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</fieldset>
					<fieldset style="display: none;">
						<legend>Notes</legend>
						<textarea class="register_modal_notes" disabled="disabled"></textarea>
					</fieldset>
					<fieldset>
						<legend>Additional Details</legend>
						<label><input type="checkbox" id="studied_outside_of_school"/> This subject is studied by the
							student outside of school.</label>
					</fieldset>
					<fieldset>
						<legend>Schedule Cost</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="period_attending">Periods Attending: </label>

							<div class="col-sm-6">
								<input class="form-control" type="text" id="period_attending" readonly="readonly"/>
							</div>
						</div>
						<div class="form-group" id="cost_per_class_div">
							<label class="col-sm-3 control-label" for="cost_per_class">Cost per Class: </label>

							<div class="col-sm-6">
								<input class="form-control" type="text" readonly="readonly" id="cost_per_class"
									   value=""/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="schedule_total_cost">Total Cost: </label>

							<div class="col-sm-6">
								<input class="form-control" type="text" readonly="readonly" id="schedule_total_cost"/>
							</div>
						</div>
						<input type="hidden" id="schedule_payment_type" value=""/>
						<input type="hidden" id="schedule_fee_per" value=""/>
						<input type="hidden" id="schedule_fee_amount" value=""/>
						<input type="hidden" id="schedule_detail_schedule_id" value=""/>
					</fieldset>
				</div>

			</div>

			<div class="modal-footer register_modal-footer">
				<button type="button" class="btn btn-success" id="add_places_to_booking"
						data-content="Register for the selected booking on the dates displayed above">Register
				</button>
				<button type="button" class="btn-link" data-dismiss="modal" id="cancel_add_places_to_booking"
						data-content="Do not register for this booking">Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<? // Course Details ?>
<div id="modal_course_details" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">View Course Details</h3>
			</div>
			<div class="modal-body">
				<h4 id="modal_course_name"></h4>

				<p id="modal_course_details_detail"></p>

				<p id="modal_course_summary"></p>

				<p id="modal_course_description"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"
						data-content="Close the course detail window">Close
				</button>
			</div>
		</div>
	</div>
</div>

<? // Calendar ?>
<div id="calendar_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="calendar_modal"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Change Date Period</h3>
			</div>
			<div class="modal-body form-horizontal">
				<ul id="week_view_selector">
					<li><a href="#" data-value="last_week">Last</a></li>
					<li><a href="#" data-value="current_week">Current</a></li>
					<li><a href="#" data-value="next_week">Next</a></li>
					<li><a href="#" data-value="custom">Custom</a></li>
				</ul>
				<div id="calendar_dates">
					<?php
					$from_date = strtotime('this week', (isset($first_period) AND !is_null($first_period)) ? strtotime($first_period) : time());
					$to_date = $from_date + 24 * 3600 * 34;
					?>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="from_date">From</label>

						<div class="col-sm-4"><input class="form-control" id="from_date"
													 value="<?= date('d-m-Y', $from_date); ?>" readonly/></div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="to_date">To</label>

						<div class="col-sm-4"><input class="form-control" id="to_date"
													 value="<?= date('d-m-Y', $to_date); ?>" readonly/></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"
						onclick="$('#search_courses_schedules').click();"
						data-content="Search the schedule for the selected dates">Ok
				</button>
			</div>
		</div>
	</div>
</div>

<? // Course Level Warning ?>
<div id="course_level_warning" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Booking warning</h3>
			</div>
			<div class="modal-body" id="modal_body_message">
				<h4 id="modal_schedule_name">One or more schedules do not match the student.</h4>

				<p id="modal_schedule_summary"></p>

				<p id="modal_schedule_alert"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="process_unmatched_booking"
						data-content="Continue with booking the selected courses that do not match the student profile">
					Process Booking
				</button>
				<button type="button" class="btn btn-danger cancel_place" data-dismiss="modal"
						data-content="Return to the booking form to edit the bookings">Cancel Booking
				</button>
			</div>
		</div>
	</div>
	<input type="hidden" id="data" name="data" value=""/>
</div>

<form id="cancel_booking_form" method="post" action="/admin/bookings/cancel">
	<input type="hidden" id="cancel_booking_id" name="cancel_booking_id"/>
</form>

<form id="delete_booking_form" method="post" action="/admin/bookings/delete">
	<input type="hidden" id="delete_booking_id" name="delete_booking_id"/>
</form>

<? // Billing Modal ?>
<div id="make_billed_booking_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Make a Billed Booking</h3>
			</div>
			<div class="modal-body form-horizontal">
				<form id="make_billed_booking_modal_form" method="post" action="">
					<div class="alert-area"></div>

					<div class="field">
						<fieldset>
							<legend>Booking</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label"
									   for="modal_make_billed_booking_contact_name">Name</label>

								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_billed_booking_contact_name"
										   readonly="readonly">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Schedule</label>

								<div class="col-sm-8" id="modal_bill_matched_schedule_summary">

								</div>
							</div>
							<input type="hidden" id="bill_data" name="data" value=""/>
						</fieldset>
					</div>
					<div class="field hide" id="booking_warning">
						<h4 id="modal_schedule_name">One or more Schedule doesn't match the student.</h4>

						<p id="modal_bill_unmatched_schedule_summary"></p>
					</div>
					<div class="field">
						<fieldset>
							<legend>Select Bill Payer</legend>
							<div class="form-group">
								<!--                            <label class="col-sm-3 control-label" for="select_contact">Select Contact</label>-->
								<!--                            <div class="col-sm-8">-->
								<!--                                <input type="text" class="form-control" id="select_contact" name="select_contact" value="" placeholder="enter mobile / family / last name">-->
								<!--                            </div>-->
								<label class="col-sm-3 control-label" for="modal_make_billed_booking_select_bill_payer">Select
									Payer</label>

								<div class="col-sm-8">
									<select class="form-control" id="modal_make_billed_booking_select_bill_payer"
											name="bill_payer">
										<option value="">Please select Bill Payer</option>
										<?php
										$contact = new Model_Contacts3();
										$payers = $contact->get_billed_organization();
										foreach ($payers as $key => $payer)
										{
											echo '<option value="'.$payer['id'].'" data-payer_family_id="'.$payer['family_id'].'">'
												.$payer['title'].' '.$payer['first_name'].' '.$payer['last_name']
												.'</option>';
										}
										?>
									</select>
								</div>
							</div>

						</fieldset>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" id="make_billed_booking_modal_btn" class="btn btn-primary"
				   data-content="Create a booking for all Pre pay courses that will be billed to a Third Party">Book And
					Create Bill</a>
				<a href="#" class="btn" data-dismiss="modal" data-content="Return to the booking form">Cancel</a>
			</div>
		</div>
	</div>
</div>

<? // Multiple Transaction Warning ?>
<div id="modal_multiple_transaction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Booking with Multiple Transaction</h3>
			</div>
			<div class="modal-body">
				<h4>This Booking has multiple transactions attached</h4>

				<p>Please Cancel the transactions from the account tabs before you can cancel the Booking</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" data-content="Return to the booking form">Cancel</a>
			</div>
		</div>
	</div>
</div>

<? // No Contact selected ?>
<div id="alert_no_contact" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>No Contact Have been Selected</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				Please select a contact before you can make a booking.
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal"
				   data-content="Return to the booking form and add a contact for the student">Ok</a>
			</div>
		</div>
	</div>
</div>

<? // Minus Total ?>
<div id="alert_minus_total" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Please Check the Payment Details</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				Please check the payment details on this booking. <br>You cannot book a course with a MINUS total
				amount.
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal"
				   data-content="Return to the booking form and edit the discounts and total price">Ok</a>
			</div>
		</div>
	</div>
</div>

<? // Too Many discounts used ?>
<div id="alert_too_many_discount" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Multiple Discounts applied</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				Discounts: More than 1 discount has been applied to this booking. <br/>Are you sure you wish to
				continue?
			</div>
			<div class="modal-footer">
				<a class="btn continue" data-dismiss="modal" data-content="Apply multiple discounts">Continue</a>
				<a class="btn cancel" data-dismiss="modal"
				   data-content="Return to the booking form and edit the discounts applied">Cancel</a>
			</div>
		</div>
	</div>
</div>

<div id="email_subscription_info" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3><?=__('Subscription link sent')?></h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <p><?=__('Subscription link sent')?></p>
            </div>
            <div class="modal-footer">
                <a class="btn close" data-dismiss="modal"
                   data-content="close"><?=__('Close')?></a>
            </div>
        </div>
    </div>
</div>

    <div id="email_accreditation_application_info" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3><?=__('Accreditation application link sent')?></h3>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="alert-area"></div>
                    <p><?=__('Accreditation application link sent')?></p>
                </div>
                <div class="modal-footer">
                    <a class="btn close" data-dismiss="modal"
                       data-content="close"><?=__('Close')?></a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="alternative-schedules-warning-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= __('Capacity Warning') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('This schedule is near full capacity? Do you want to continue or select alternative schedules?') ?></p>
				<table id="alternative-schedules" class="table">
					<thead><tr><th>#ID</th><th>Room</th><th>Trainer</th><th>Schedule</th><th>Capacity</th><th>Booked</th><th>Action</th></tr></thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="alternative-schedules-warning-continue"
						data-dismiss="modal"><?= __('Continue') ?></button>
				<button type="button" class="btn btn-default btn-green" id="activity-lock-continue-no"
						data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="activity-lock-warning-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= __('Do you want to continue') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('Please note a booking has been commenced by <b class="username"></b> at <b class="time"></b> for this contact do you wish to proceed?<br /> Please note if you continue you may create a double booking for this contact, If unsure please click NO.?') ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="activity-lock-continue-yes"
						data-dismiss="modal"><?= __('YES') ?></button>
				<button type="button" class="btn btn-default btn-green" id="activity-lock-continue-no"
						data-dismiss="modal"><?= __('NO') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade booking-schedule-edit-modal" tabindex="-1" role="dialog" id="booking-schedule-edit-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="label label-primary category">Revision</span>
                    <span class="schedule">Math</span>
                    <span class="label label-default right location">Limerick</span>
                </h4>
            </div>

            <div class="modal-body">
                <label><input type="radio" name="customize" value="0" checked="checked" /> <span class="schedule-title">Limerick Fri 1.5 hrs Julie 30.00 room 1</span></label><br />
                <label><input type="radio" name="customize" value="1"/> Custom</label>
                <div class="attendance-custom hidden">
                    <div class="row gutters">
                        <div class="col-sm-8 days-filter">

                            <?php $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat', '0' => 'Sun'); ?>
                            <?php foreach ($days as $key => $day): ?>
                                <label class="checkbox-icon" data-day="<?=$key?>">
                                    <input class="booking-register-day" type="checkbox" checked="checked" name="days[]" value="<?= $key ?>" />
                                    <span class="checkbox-icon-unchecked btn btn-default"><?= $day ?></span>
                                    <span class="checkbox-icon-checked btn btn-primary"><?= $day ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-sm-4">
                            <?= Form::ib_select(null, 'attend-all', array('' => 'Select', '1' => 'Attend All', '0' => 'Attend None')); ?>
                        </div>
                    </div>

                    <div class="booking-frequency_table-wrapper">
                        <table class="frequency table">
                            <thead>
                                <tr><th>Frequency</th><th>Sessions</th><th>Bulk</th><th>Attend</th><th>Note</th></tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary add">Register sessions</button>
                <button type="button" class="btn update">Update registration</button>
                <a class="dismiss"  data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>

	<div class="modal fade unexplained_absence_modal" id="unexplained_absence_modal" tabindex="-1" role="dialog"
		 aria-labelledby="unexplained_absence_modal_heading" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 id="unexplained_absence_modal_heading">View Course Details</h3>
				</div>
				<div class="modal-body">
					<p>You must add an explanation note to each time slot not being attended.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"
							data-content="You need to edit the booking and give a reason for any absence">Ok
					</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" tabindex="-1" role="dialog" id="booking-schedule-remove-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span class="category">Revision</span> <span class="schedule">Math</span> <span class="location">Limerick</span></h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to remove <span class="schedule-title">schedule</span> ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn danger" data-dismiss="modal">Remove</button>
				<a class="dismiss"  data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>


<div class="modal fade bulk_attend_update2" id="bulk-update-modal2" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Bulk Update</h4>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <label class="col-sm-2 control-label">Frequency</label>

                    <div class="col-sm-4">
                        <select class="form-control bulk_attend2_update_frequency" multiple="multiple">

                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <label class="col-sm-2 control-label" for="bulk-update2-starts">Starts</label>

                    <div class="col-sm-4">
                        <label class="input-group">
                            <input type="text" class="form-control bulk_attend_update2_date_from"
                                   id="bulk-update2-starts" size="6"
                                   data-date-format="dd/mm/yyyy"/>

                            <span class="input-group-addon">
                                <span class="icon-calendar"></span>
                            </span>
                        </label>
                    </div>

                    <label class="col-sm-2 control-label" for="bulk-update2-ends">Ends</label>

                    <div class="col-sm-4">
                        <label class="input-group">
                            <input type="text" class="form-control bulk_attend_update2_date_to"
                                   id="bulk-update2-ends" size="6"
                                   data-date-format="dd/mm/yyyy"/>

                            <span class="input-group-addon">
                                <span class="icon-calendar"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <label class="col-sm-2 control-label" for="bulk-update2-notes">Notes</label>

                    <div class="col-sm-10">
                       <textarea class="form-control bulk_attend_update2_note_all" id="bulk-update2-notes"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <label class="col-sm-2 control-label">Attending</label>

                    <div class="col-sm-10">
                        <div class="btn-group btn-group-slide" data-toggle="buttons">
                            <label class="btn btn-plain">
                                <input type="radio" name="bulk_attend2_update_attending_all" value="1"
                                       class="bulk_attend2_update_attending_all_yes"/> Yes
                            </label>
                            <label class="btn btn-plain active">
                                <input type="radio" name="bulk_attend2_update_attending_all" value="0"
                                       class="bulk_attend_update_attending_all_no" checked="checked"/> No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="booking-timeslots_table-wrapper">
                        <table class="table timeslots">
                            <thead><tr><th>Date</th><th>Attending</th><th>Note</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="text-center">
                    <button class="btn btn-primary bulk_attend2_update_set" type="button" data-dismiss="modal">Update</button>
                    <a class="dismiss" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo View::factory('snippets/modal')->set([
    'id'     => 'booking-modal-po_number-required',
    'title'  => 'PO number required',
    'body'   => '<p>You must enter a purchase-order number in order to continue.</p>',
    'footer' => '<button type="button" class="btn-cancel" data-dismiss="modal">OK</button>'
]);
?>

<div id="alert_schedule_full" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Schedule is full</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<h3 style="color:#F00" id="schedule_full_message"></h3>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal"
				   data-content="Return to the booking form and add a contact for the student">Ok</a>
			</div>
		</div>
	</div>
</div>

<div id="duplicate_schedule_booking_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Duplicate Schedule Booking</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
                <p class="text-danger" id="schedule_duplicate_booking_message">
                    This student already has a booking for this schedule.
                    <?php if (!Settings::instance()->get('allow_duplicate_bookings_per_student')): ?>
                        Cannot book again.
                    <?php endif; ?>
                </p>
			</div>
			<div class="modal-footer">
                <?php if (Settings::instance()->get('allow_duplicate_bookings_per_student')): ?>
                    <button type="button" class="btn btn-primary" id="schedule_duplicate_booking_continue">Continue anyway</button>

                    <button type="button" class="btn btn-cancel" data-dismiss="modal"
                        data-content="Return to the booking form and add a contact for the student"
                    >Cancel</button>
                <?php else: ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                       data-content="Return to the booking form and add a contact for the student"
                    >Dismiss</button>
                <?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div id="primary_biller_error_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3><?=__('Organisation incomplete')?></h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <h3 style="color:#F00"><?=__("Primary biller of the organisation has not been set")?></h3>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"
                   data-content="Return to the booking form">Dismiss</a>
            </div>
        </div>
    </div>
</div>

<div id="invoice_number_error_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3><?=__('Invoice number is not set')?></h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <h3 style="color:#F00"><?=__("Invoice number is not set")?></h3>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"
                   data-content="Return to the booking form">Dismiss</a>
            </div>
        </div>
    </div>
</div>
