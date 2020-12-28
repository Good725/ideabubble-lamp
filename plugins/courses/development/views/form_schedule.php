<?php
$data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data :[]);
// Moving up here due to merge conflict with ENGINE-1412
$data['booking_type'] = (!isset($data['booking_type']) && $edit_all) ? "Whole Schedule" : $data['booking_type'];
$interviews_enabled = Settings::instance()->get('courses_schedule_interviews_enabled') == 1;
$please_select = ['value' => '', 'label' => __('Please select')];
$search_icon = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>'];
$fee_required = isset($data['is_fee_required']) ? (bool) $data['is_fee_required'] : true;
?>

<div id="schedule_alert_area" class="col-sm-12">
    <?php if(isset($alert)): ?>
        <?= $alert ?>
        <script>remove_popbox();</script>
    <?php endif; ?>
</div>


<form class="form-horizontal validate-on-submit" id="form_add_edit_schedule" name="form_add_edit_schedule" action="/admin/courses/save_schedule/" method="post">
    <?php
    echo View::factory('form_title')
        ->set([
            'name'            => (isset($data['name']) ? $data['name'] : ''),
            'name_field'      => 'name',
            'name_attributes' => ['required' => 'required', 'id' => 'name', 'placeholder' => __('Schedule title')],
            'published'       => (isset($data['publish']) ? $data['publish'] : '1'),
            'publish_field'   => 'publish'
        ]);
    ?>

	<ul class="nav nav-tabs form-tabs hidden-xs" id="edit_schedule-tabs">
        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'navapi')){ ?>
            <li><a href="#edit_schedule-tab-navision" data-toggle="tab">Navision</a></li>
        <?php } ?>
		<li class="active"><a href="#edit_schedule-tab-summary" data-toggle="tab">Summary</a></li>
		<li><a href="#edit_schedule-tab-timeslots" data-toggle="tab">Timeslots</a></li>
		<li><a href="#edit_schedule-tab-booking" data-toggle="tab">Booking</a></li>
		<li><a href="#edit_schedule-tab-fees" data-toggle="tab">Fees</a></li>
		<li class="edit_schedule-tab-paymentplan <?=!$fee_required ? 'hidden' : '' ?>"><a href="#edit_schedule-tab-paymentplan" data-toggle="tab">Payment Plan</a></li>
        <?php if ($edit_all): ?>
            <?php if (Auth::instance()->has_access('courses_zone_edit')): ?>
                <li><a href="#edit_schedule-tab-zones" data-toggle="tab">Zones</a></li>
            <?php endif;
            if (Auth::instance()->has_access('courses_topic_edit')): ?>
                <li><a href="#edit_schedule-tab-topics" data-toggle="tab">Topics</a></li>
            <?php endif; ?>
            <li><a href="#edit_schedule-tab-content" data-toggle="tab">Content</a></li>
        <?php endif; ?>
	</ul>
    <div class="edit_schedule-tab btn-group btn-group- btn--full visible-xs" id="edit_schedule-section_toggle">
        <button
            type="button"
            class="btn btn-success btn-lg btn--full dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            <span id="edit_schedule-section_toggle-text">Schedules</span>
            <span class="arrow_caret-down"></span>
        </button>

        <ul class="dropdown-menu btn--full" style="margin-top: 0;">
            <li class=" active"><a href="#edit_schedule-tab-summary" data-toggle="tab">Summary</a></li>
            <li><a href="#edit_schedule-tab-timeslots" data-toggle="tab">Timeslots</a></li>
            <li><a href="#edit_schedule-tab-booking" data-toggle="tab">Booking</a></li>
            <li><a href="#edit_schedule-tab-fees" data-toggle="tab">Fees</a></li>
            <li class="edit_schedule-tab-paymentplan <?=!$fee_required ? 'hidden' : '' ?>"><a href="#edit_schedule-tab-paymentplan" data-toggle="tab">Payment Plan</a></li>
            <?php if ($edit_all): ?>
                <li><a href="#edit_schedule-tab-zones" data-toggle="tab">Zones</a></li>
                <li><a href="#edit_schedule-tab-topics" data-toggle="tab">Topics</a></li>
                <li><a href="#edit_schedule-tab-content" data-toggle="tab">Content</a></li>
             <?php endif; ?>
            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'navapi')){ ?>
            <li><a href="#edit_schedule-tab-navision" data-toggle="tab">Navision</a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="tab-content">
        <!-- Summary -->
        <div class="tab-pane active" id="edit_schedule-tab-summary">
            <input type="hidden" name="timetable_hidden" id="timetable_hidden" value=""/>
            <input type="hidden" value="<?=(isset($_GET['id']) && is_numeric($_GET['id']))?$_GET['id']:"new";?>" id="schedule_id" name="schedule_id"/>
            <input type="hidden" name="timetable_post_name" id="timetable_post_name" value=""/>
            <input type="hidden" name="trainer_hidden" id="trainer_hidden" value=""/>
            <input type="hidden" name="custom_hidden" id="custom_hidden" value="[]"/>
            <input type="hidden" name="timeslots_json" id="timeslots_json" value="" />
            <input type="hidden" id="duplicated" value="<?= $cloned ?>">

            <?php if ($interviews_enabled && $edit_all): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="is_interview">Interview</label>

                    <div class="col-sm-3">
                        <?php $is_interview = (isset($data['is_interview']) && $data['is_interview'] == 'YES') ?>
                        <input type="hidden" name="is_interview"  value="0" />
                        <?= Form::ib_checkbox_switch(null, 'is_interview', 'YES', $is_interview, ['id' => 'is_interview']); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group <?=@$data['is_interview'] != 'YES' ? 'hidden' : ''?>" id="has_course_ids_selector">
                <label class="col-sm-2 control-label" for="course_ids">Courses</label>

                <div class="col-sm-6">
                    <?php
                    $options = [];
                    foreach ($courses as $item) {
                        $options[$item['id']] = $item['title'].' - '.$item['category'];
                    }
                    $attributes = ['multiple' => 'multiple', 'id' => 'course_ids'];
                    $args = [];

                    if (count($courses) > 10) {
                        $args = $search_icon + ['multiselect_options' => [
                            'defaultText' => __('Please select'),
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering' => true
                        ]];
                    }

                    $selected = isset($data['has_courses']) ? $data['has_courses'] : '';
                    echo Form::ib_select(null, 'course_ids[]', $options, $selected, $attributes, $args);
                    ?>
                </div>
            </div>

            <!-- Course -->
            <div class="form-group <?=@$data['is_interview'] == 'YES' ? 'hidden' : ''?>" id="course_id_selector">
                <label class="col-sm-2 control-label" for="course_id">Course</label>

                <div class="col-sm-6">
                    <?php ob_start(); ?>
                        <option value="">Please select</option>

                        <?php foreach ($courses as $item): ?>
                            <option value="<?=$item['id']?>"
                                <?=( isset($data['course_id']) AND ($data['course_id'] == $item['id']) ) ? 'selected="selected"' : '' ?>
                                    data-grinds="<?=$item['payg'];?>"
                                    data-category-id="<?=$item['category_id'];?>"
                                    data-category_start_time="<?= $item['start_time'] == '' ? '' : date('H:i',strtotime($item['start_time'])) ; ?>"
                                    data-category_end_time="<?= $item['end_time'] == '' ? '' : date('H:i',strtotime($item['end_time'])) ; ?>"
                                    data-category_end_time="<?= $item['end_time'] == '' ? '' : date('H:i',strtotime($item['end_time'])) ; ?>"
                                    data-schedule_is_fee_required="<?=$item['schedule_is_fee_required']?>"
                                    data-schedule_fee_amount="<?=$item['schedule_fee_amount']?>"
                                    data-schedule_fee_per="<?=$item['schedule_fee_per']?>"
                                    data-schedule_allow_price_override="<?=$item['schedule_allow_price_override']?>"
                                    data-subject_id="<?=$item['subject_id'];?>"
                                ><?=$item['title'].' - '.$item['category']?></option>
                        <?php endforeach; ?>
                    <?php $options = ob_get_clean(); ?>

                    <?php
                    $attributes = ['id' => 'course_id', 'data-edit_all' => $edit_all];
                    $args = [];
                    if (count($courses) > 10) {
                        $attributes['class'] = 'ib-combobox';
                        $attributes['data-placeholder'] = __('Please select');
                        $args = $search_icon;
                    }
                    echo Form::ib_select(null, 'course_id', $options, null, $attributes, $args);
                    ?>
                </div>
            </div>

            <div class="form-group <?= (empty($data['schedule_status_label'])) ? ' hidden ' : '' ?>" >
                <label class="col-sm-2 control-label" for="course_ids">Status</label>
                <div class="col-sm-6">
                    <?= Form::ib_input(null, 'schedule_status_label', @$data['schedule_status_label'],
                        ['class' => '', 'id' => 'schedule_status_label', 'disabled' => 'disabled']) ?>
                </div>
            </div>

            <?php if ($edit_all) { ?>
                <!-- Academic Year -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="academic_year_id">Academic Year</label>

                    <div class="col-sm-6">
                        <?php
                        $options = html::optionsFromRows('id', 'value', $academic_years, @$data['academic_year_id'], $please_select);
                        echo Form::ib_select(null, 'academic_year_id', $options, @$data['academic_year_id'], ['id' => 'academic_year_id']);
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($edit_all) { ?>
                <!-- Study mode -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="study_mode_id">Study mode</label>

                    <div class="col-sm-6">
                        <?php ob_start(); ?>
                            <option	value=""><?= __('Please select') ?></option>
                            <?php foreach ($study_modes as $study_mode): ?>
                                <option	value="<?= $study_mode['id'] ?>"
                                    <?=(isset($data['study_mode_id']) && ($data['study_mode_id'] == $study_mode['id'])) ? 'selected="selected"' : '' ?>
                                    <?=(!isset($data['study_mode_id']) || ($data['study_mode_id'] == '')) && $study_mode['study_mode'] == 'Part Time' ? 'selected="selected"' : '' ?>><?=$study_mode['study_mode']?></option>
                            <?php endforeach; ?>
                        <?php $options = ob_get_clean(); ?>

                        <?php
                        echo Form::ib_select(null, 'study_mode_id', $options, null, ['id' => 'study_mode_id']);
                        ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Subject -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject_id">Subject</label>

                <div class="col-sm-6">
                    <?php
                    $options = html::optionsFromRows('id', 'name', $subjects, @$data['subject_id'], $please_select);
                    echo Form::ib_select(null, 'subject_id', $options, @$data['subject_id'], ['id' => 'subject_id']);
                    ?>
                </div>
            </div>

            <!-- Location -->
            <input type="hidden" id="location_id" name="location_id" value="<?= isset($data['location_id']) ? $data['location_id'] : ''?>">

            <!-- Parent Location -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="parent_location_id">Parent Location</label>

                <div class="col-sm-6">
                    <?php
                    $options = html::optionsFromRows('id', 'name', $locations, @$parent_location_id, $please_select);
                    echo Form::ib_select(null, null, $options, @$parent_location_id, ['id' => 'parent_location_id']);
                    ?>
                </div>
            </div>

            <!-- Child Location -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="child_location_id">Sub Location</label>

                <div class="col-sm-6">
                    <?php
                    $options = html::optionsFromRows('id', 'name', $children_locations, @$data['location_id'], $please_select);
                    echo Form::ib_select(null, null, $options, @$data['location_id'], ['id' => 'child_location_id']);
                    ?>

                    <select class="hidden" id="all_locations">
                        <option value=""></option>
                        <?php
                        $all_locations = html::optionsFromRows('value', 'label', Model_Locations::autocomplete_locations());
                        ?>
                        <?=$all_locations?>
                    </select>
                </div>
            </div>

            <!-- Trainer -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="trainer_id"><?=__('Staff')?></label>

                <div class="col-sm-6">
                    <?php
                    $options = html::optionsFromRows('id', 'full_name', $trainers, @$data['trainer_id'], $please_select);
                    $attributes = ['id' => 'trainer_id'];
                    $args = [];

                    if (count($trainers) > 10) {
                        $args = $search_icon;
                        $attributes['data-placeholder'] = __('-- Please select-- ');
                        $attributes['class'] = 'ib-combobox';
                    }

                    echo Form::ib_select(null, 'trainer_id', $options, @$data['trainer_id'], $attributes, $args);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="course-schedule-delivery_mode_id"><?=__('Delivery Mode')?></label>

                <div class="col-sm-6">
                    <?php
                    $please_select = ['' => '-- Please select --'];
                    $options = $please_select + $delivery_modes;
                    echo Form::ib_select(null, 'delivery_mode_id', $options, @$data['delivery_mode_id']);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="course-schedule-learning_mode_id"><?=__('Learning Mode')?></label>

                <div class="col-sm-6">
                    <?php
                    $options = $please_select + $learning_modes;
                    echo Form::ib_select(null, 'learning_mode_id', $options, @$data['learning_mode_id']);
                    ?>
                </div>
            </div>

            <?php if ($edit_all) { ?>
                <!-- Description -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="description">Description</label>

                    <div class="col-sm-8">
                        <textarea class="form-control" id="description" name="description" rows="4"><?=isset($data['description']) ? $data['description'] : ''?></textarea>
                    </div>
                </div>
            <?php } ?>

            <?php if ($edit_all) { ?>
                <!-- is confirmed -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="is_confirmed">Confirmed</label>

                    <div class="col-sm-6">
                        <?php $is_confirmed = (!isset($data['is_confirmed']) || $data['is_confirmed'] == '1'); ?>
                        <input type="hidden" name="is_confirmed" value="0" />
                        <?= Form::ib_checkbox_switch(null, 'is_confirmed', 1, $is_confirmed); ?>
                    </div>
                </div>
            <?php } else { ?>
                <input type="hidden" name="is_confirmed" value="1" />
            <?php } ?>

            <!-- Category Identifier -->
            <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>
            <input type="hidden" id="save_exit" name="save_exit" value="false" />
            <input type="hidden" id="booking_count" value="<?= $booking_count ?>" />
        </div><!-- #edit_schedule-tab-summary - end -->

        <div class="tab-pane" id="edit_schedule-tab-timeslots">
            <?php if ($edit_all): ?>
                <!-- Blackout Days -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="blackout_calendar_event_ids[]">Blackouts</label>

                    <div class="col-sm-6">
                        <?php
                        $options = html::optionsFromRows('id', 'event', $engineCalendarEvents, $blackout_calendar_event_ids);
                        $attributes = ['id' => 'blackout_calendar_event_ids', 'multiple' => 'multiple'];

                        if (count($engineCalendarEvents) > 10) {
                            $args = $search_icon + ['multiselect_options' => [
                                'defaultText' => __('Please select'),
                                'enableCaseInsensitiveFiltering' => true,
                                'enableFiltering' => true
                            ]];
                        } else {
                            $args = [];
                        }
                        echo Form::ib_select(null, 'blackout_calendar_event_ids[]', $options, null, $attributes, $args);
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($edit_all): ?>
                <!-- Repeat -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="repeat">Repeat</label>
                    <div class="col-sm-6">
                        <?php
                        $options = '<option value="">None</option>'.Model_Schedules::get_repeat_as_options(@$data['repeat']);
                        echo Form::ib_select(null, 'repeat', $options, @$data['repeat'], ['id' => 'repeat']);
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" id="repeat" name="repeat" value="<?= @$data['repeat'] ?>" />
            <?php endif; ?>

            <!-- Start Time -->
            <div class="form-group" id="repeat_start_time">
                <label class="col-sm-2 control-label" for="start_day_time">Start Time</label>
                <div class="col-sm-2">
                    <input type="text" name='start_day_time' class="form-control datetimepicker start_time time_range_picker" id="start_day_time" value="<?=isset($data['start_date'])?date('H:i',strtotime($data['start_date'])):'' ;?>" />
                </div>
                <label class="col-sm-2 control-label" for="end_day_time">End Time</label>
                <div class="col-sm-2">
                    <input type="text" name='end_day_time'  class="form-control datetimepicker end_time time_range_picker" id="end_day_time" value="<?=isset($data['end_date'])?date('H:i',strtotime($data['end_date'])):'' ;?>" />
                </div>
            </div>

            <!-- Custom Days -->
            <div id="custom_schedule">
                <!-- Frequency -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="frequency">Frequency:</label>
                    <div class="col-sm-4">
                        <?php
                        $options = Model_Schedules::get_custom_frequency((isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : null);;
                        echo Form::ib_select(null, 'frequency', $options, htmlspecialchars(@$_GET['id']), ['id' => 'frequency']);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10" id="days_of_week">
                        <?php $days = array('Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thur' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday'); ?>

                        <?php foreach ($days as $day_short => $day): ?>
                            <label class="day_button_wrapper">
                                <input type="checkbox" class="day_button sr-only" data-day="<?= $day ?>" />
                                <span class="btn"><span class="day_button_icon icon-remove"></span> <?= $day_short ?> </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div id="timeslot_selection" class="tabbable">
                            <ul id="daily_frequency" class="nav nav-tabs">
                                <li class="timeslot-tab active" id="Preview_tab">
                                    <a href="#Preview" data-toggle="tab">Preview <small class="timeslot-tab-count"></small></a>
                                </li>
                                <?php foreach ($days as $day): ?>
                                    <li class="timeslot-tab" id="<?= $day?>_tab">
                                        <a href="#<?= $day ?>" data-toggle="tab"><?= $day ?> <small class="timeslot-tab-count"></small></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane timeslot-tab-pane active" id="Preview">
                                    <input type="hidden" value="Preview" class="day_name"/>
                                    <table class="frequency_table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Day</th>
                                            <th scope="col">Start Time</th>
                                            <th scope="col">End Time</th>
                                            <th scope="col">Teacher</th>
                                            <th scope="col">Location</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <?php foreach ($days as $day): ?>
                                    <div class="tab-pane timeslot-tab-pane" id="<?= $day ?>">
                                        <input type="hidden" value="<?= $day ?>" class="day_name"/>
                                        <table class="frequency_table">
                                            <thead>
                                            <tr>
                                                <th scope="col">Day</th>
                                                <th scope="col">Start Time</th>
                                                <th scope="col">End Time</th>
                                                <th scope="col">Teacher</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Remove</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <button class="btn add_timeslot" type="button">New Frequency</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Selection -->
            <div id="date_selection" class="hidden">
                <!-- Start End Dates -->
                <div class="form-group ">
                    <label class="col-sm-2 control-label" for="start_date">Start Date</label>
                    <div class="col-sm-2">
                        <input type="text"  class="form-control" id="start_date" autocomplete="off" name="start_date" value="<?=(isset($data['start_date']) AND ! $cloned)?date('d-m-Y',strtotime($data['start_date'])):'' ;?>"/>
                    </div>

                    <label class="col-sm-2 control-label" for="end_date">End Date</label>
                    <div class="col-sm-2">
                        <input type="text"  class="form-control" id="end_date" autcomplete="off" name="end_date" value="<?=(isset($data['end_date']) AND ! $cloned)?date('d-m-Y',strtotime($data['end_date'])):'' ;?>"/>
                    </div>
                </div>
                <!-- Get Timetable -->
                <div class="form-group form-action-group text-left">
                    <div class="col-sm-offset-2 col-sm-3">
                        <button type="button" class="btn btn-primary" id="generate_dates" <?=$booking_count == 0 || $data['booking_type'] ==  'Whole Schedule' ? '' : ' disabled="disabled"'?>>Generate Timeslots</button>
                    </div>

                    <div class="col-sm-3 text-right">
                        <button type="button" class="btn btn-danger clear-timeslots" <?=$booking_count == 0 || $data['booking_type'] == 'Whole Schedule' ? '' : ' disabled="disabled"'?> style="margin-right: 0;">Clear Timeslots</button>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function(){
                    $("#datepicker").eventCalendar();
                });
            </script>

            <!-- Schedules Time Slots -->

            <!-- Schedule -->
            <div id="datepicker-outer" class="form-group">
                <label class="col-sm-2 control-label" for="datepicker">Select your Dates</label>
                <div class="col-sm-6">
                    <div id="datepicker"></div>
                </div>
            </div>

            <h3>Timeslots <small id="schedule-form-timeslot-results" data-count="0">(Showing <span id="schedule-form-timeslot-results-count">0</span> results)</small></h3>

            <?php if (is_numeric(@$_GET['id']) && Settings::instance()->get('course_rescheduler_enabled')) { ?>
                <div id="bulk_timeslot_update">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <button
                                type="button"
                                class="btn-link right"
                                data-hide_toggle="#schedule-form-timeslot-panel-body"
                                data-show_text="show"
                                data-hide_text="hide"
                            >show</button>
                            <h4 class="panel-title">Bulk Timeslot Change</h4>
                        </div>

                        <div class="panel-body hidden" id="schedule-form-timeslot-panel-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="bulk_timeslot_update_action">Action</label>
                                <label class="col-sm-4 control-label" for="bulk_timeslot_update_days">Reschedule Days</label>
                                <div class="col-sm-2">
                                    <?= Form::ib_input(null, null, 0, ['type' => 'number', 'id' => 'bulk_timeslot_update_days']); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Between</label>
                                <div class="col-sm-3">
                                    <?= Form::ib_input(null, null, null, ['class' => 'datepicker', 'placeholder' => 'From', 'id' => 'bulk_timeslot_update_from']); ?>
                                </div>

                                <div class="col-sm-3">
                                    <?= Form::ib_input(null, null, null, ['class' => 'datepicker', 'placeholder' => 'To', 'id' => 'bulk_timeslot_update_to']); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Update Timeslots</label>
                                <div class="col-sm-9">
                                    <table class="table" id="bulk_timeslot_update_times">
                                        <thead>
                                            <tr>
                                                <th><?=__('Old Timeslot')?></th>
                                                <th><?=__('New Timeslot Start')?></th>
                                                <th><?=__('New Timeslot End')?></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $distinct_timeslots = array();
                                            if (is_array(@$data['timeslots']))
                                                foreach ($data['timeslots'] as $timeslot) {
                                                    $timeslot_start = date('l H:i', strtotime($timeslot['datetime_start']));
                                                    $distinct_timeslots[$timeslot_start.$timeslot['location_id']] = array(
                                                        'start' => $timeslot_start,
                                                        'end' => date('H:i', strtotime($timeslot['datetime_end'])),
                                                        'day' => date('l', strtotime($timeslot['datetime_start'])),
                                                        'time' => date('H:i', strtotime($timeslot['datetime_start'])),
                                                        'duration' => (strtotime($timeslot['datetime_end']) - strtotime($timeslot['datetime_start'])),
                                                        'location_id' => $timeslot['location_id']
                                                    );
                                                }
                                            foreach ($distinct_timeslots as $distinct_timeslot) {
                                                ?>
                                                <tr data-start="<?=$distinct_timeslot['start']?>" data-day="<?=$distinct_timeslot['day']?>" data-time="<?=$distinct_timeslot['time']?>">
                                                    <td><?=$distinct_timeslot['start'] . ' - ' . $distinct_timeslot['end']?></td>
                                                    <td><input type="text" class="form-control timepicker time_range_picker start_time" value="<?=$distinct_timeslot['time']?>" /> </td>
                                                    <td><input type="text" class="form-control timepicker time_range_picker end_time" value="<?=$distinct_timeslot['end']?>" /> </td>
                                                    <td>
                                                        <select class="form-control dt_location_id" data-selected="<?=$distinct_timeslot['location_id']?>">
                                                            <option></option>
                                                            <?=html::optionsFromRows('value', 'label', Model_Locations::autocomplete_locations(),$distinct_timeslot['location_id'])?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Trainer</label>
                                <div class="col-sm-4">
                                    <?php
                                    $options = html::optionsFromRows('id', 'full_name', $trainers, @$data['trainer_id'], $please_select);
                                    $attributes = array('id' => 'bulk_timeslot_update_trainer_id');
                                    echo Form::ib_select(null, null, $options, @$data['trainer_id'], $attributes);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="child_location_id">Location</label>

                                <div class="col-sm-6">
                                    <?php
                                    echo Form::ib_select(null, null, '<option></option>' . $all_locations, @$data['location_id'], ['id' => 'bulk_timeslot_update_child_location_id']);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-1">
                                    <button class="btn btn-default" type="button" id="bulk_timeslot_update_preview">Preview New Timeslots</button>
                                </div>
                            </div>

                            <?php if ($booking_count > 0): ?>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?=Form::ib_checkbox(__('Email students'), 'bulk_timeslot_update_email_students', 'YES') ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="clearfix" id="selected_dates">
                <table class="table schedule-selected_dates" id="selected_dates-table">
                    <thead>
                        <tr>
                            <th scope="col">Order</th>
                            <th scope="col" class="column-date">Day</th>
                            <th scope="col" class="column-date">Date</th>
                            <th scope="col">Price</th>
                            <th scope="col">Start Time</th>
                            <th scope="col">End Time</th>
                            <th scope="col">Trainer <button id="timeslot-trainers-reset" type="button" class="btn btn-default">Reset All</button></th>
                            <th scope="col">Room</th>
                            <th scope="col">Monitored</th>
                            <th scope="col">Topic</th>
                            <th scope="col" class="max_capacity">Max Capacity</th>
                            <th scope="col">Delete </th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <?php // Empty table row hidden in the DOM. This is to be cloned and populated, when a table row is added via JavaScript ?>
                    <tfoot class="hidden" id="schedule-timeslot-template">
                        <?= View::factory('/snippets/schedule_timeslot') ?>
                    </tfoot>

                    <tfoot>
                        <tr>
                            <td colspan="<?= $interviews_enabled ? 11 : 10 ?>" class="text-right">
                                <button class="prev btn" type="button">Prev</button>
                                <button class="next btn" type="button">Next</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <button type="button" class="btn btn-danger right clear-timeslot" <?=$booking_count == 0 ? '' : ' disabled="disabled"'?> style="clear: both; margin-top: 1em;">Delete All</button>
            </div>

            <div class="hidden" id="selected_dates-empty">
                <p><?= __('No timeslots have been selected.') ?></p>
            </div>


            <div class="form-group">
                <div class="col-sm-6">
                    <input type="hidden" id="timetable_id" name="timetable_id"/>
                    <input type="hidden" value="<?=isset($data['name']) ? $data['name'] : '' ?><?=time();?>" name="new_timetable_name" id="new_timetable_name" placeholder="Type name to add new" />
                </div>
            </div>
        </div><?php // #edit_schedule-tab-timeslots ?>

        <div class="tab-pane" id="edit_schedule-tab-booking">
            <?php if ($edit_all): ?>
                <!-- Book on Website -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="book_on_website">Book on Website</label>

                    <div class="col-sm-3">
                        <?php
                        $book_on_website = false;
                        if (isset($data['book_on_website'])) {
                            $book_on_website = $data['book_on_website'] == '1';
                        } else {
                            if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) {
                                $book_on_website = true;
                            }
                        }
                        ?>
                        <input type="hidden" name="book_on_website" value="0" />
                        <?= Form::ib_checkbox_switch(null, 'book_on_website', 1, $book_on_website) ?>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="book_on_website" value="1" />
            <?php endif; ?>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="display_timeslots_on_frontend">Show timeslots on frontend</label>

                <div class="col-sm-3">
                    <div class="left popinit" rel="popover" data-trigger="hover" data-content="When a schedule is selected on the course details or availability page, show the user a list of timeslots.">
                        <input type="hidden" name="display_timeslots_on_frontend" value="0" />
                        <?php
                        $display_timeslots_on_frontend = (isset($data['display_timeslots_on_frontend'])) ? $data['display_timeslots_on_frontend'] : 1;
                        echo Form::ib_checkbox_switch(null, 'display_timeslots_on_frontend', 1, $display_timeslots_on_frontend == 1);
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="display_timeslots_on_frontend">Show timeslots in cart</label>

                <div class="col-sm-3">
                    <div class="left popinit" rel="popover" data-trigger="hover" data-content="Show all timeslots being booked, in the sidebar cart">
                        <input type="hidden" name="display_timeslots_in_cart" value="0" />
                        <?php
                        $display_timeslots_in_cart = (isset($data['display_timeslots_in_cart'])) ? $data['display_timeslots_in_cart'] : 0;
                        echo Form::ib_checkbox_switch(null, 'display_timeslots_in_cart', 1, $display_timeslots_in_cart == 1);
                        ?>
                    </div>
                </div>
            </div>

            <?php if ($edit_all): ?>
                <div id="booking_type_display" class="form-group ">
                    <label class="col-sm-2 control-label" for="booking_type">Booking Type</label>
                    <div class="col-sm-5">
                        <?php
                        $options =  ['One Timeslot' => __('One Timeslot'), 'Whole Schedule' => __('Whole Schedule'), 'Subscription' => __('Subscription')];
                        echo Form::ib_select(null, 'booking_type', $options, @$data['booking_type'], ['id' => 'booking_type']);
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="booking_type" id="booking_type" value="<?=@$data['booking_type'] ?: 'One Timeslot' ?>" />
            <?php endif; ?>

            <!-- Group booking -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="edit-schedule-is_group_booking">Group booking</label>

                <div class="col-sm-3">
                    <input type="hidden" name="is_group_booking" value="0" />
                    <?php
                        $attributes = ['id' => 'edit-schedule-is_group_booking'];
                        $is_group_booking_enabled = (($data['id'] && !empty($data['is_group_booking'])) ||
                            (!$data['id'] && Settings::instance()->get('default_schedule_group_bookings')));
                        echo Form::ib_checkbox_switch(null, 'is_group_booking', 1, $is_group_booking_enabled, $attributes);
                    ?>
                </div>
            </div>

            <!-- Capacity -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="min_capacity">Min. Capacity</label>
                <div class="col-sm-3">
                    <?= Form::ib_input(null, 'min_capacity', @$data['min_capacity'], ['id' => 'min_capacity']); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="max_capacity">Max. Capacity</label>
                <div class="col-sm-3">
                    <?= Form::ib_input(null, 'max_capacity', @$data['max_capacity'], ['id' => 'max_capacity']); ?>
                </div>
            </div>

            <!-- Run-off schedule -->
            <div class="form-group vertically_center">
                <label class="col-sm-2 control-label" for="run_off_schedule">Run-Off Schedule</label>

                <div class="col-sm-5">
                    <?php
                    $options = html::optionsFromRows('id', 'name', $schedules, @$data['run_off_schedule'], $please_select);
                    $attributes = ['id' => 'run_off_schedule'];
                    $args = [];

                    if (count($schedules) > 10) {
                        $attributes['class'] = 'ib-combobox';
                        $attributes['data-placeholder'] = __('Please select');
                        $args = $search_icon;
                    }

                    echo Form::ib_select(null, 'run_off_schedule', $options, @$data['run_off_schedule'], $attributes, $args);
                    ?>
                </div>

                <div class="col-sm-2 control-label text-left">
                    <span id="room_size"></span>
                </div>
            </div>

            <?php if ($edit_all): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="attend_all_default">Attend All Default</label>

                    <div class="col-sm-6">
                        <?php $attend_all_default = @$data['attend_all_default'] != 'NO'; ?>
                        <input type="hidden" name="attend_all_default" value="NO" />
                        <?= Form::ib_checkbox_switch(null, 'attend_all_default', 'YES', $attend_all_default) ?>
                    </div>
                </div>
            <?php endif; ?>

        </div><?php // #edit_schedule-tab-booking ?>

        <div class="tab-pane" id="edit_schedule-tab-paymentplan">
            <?php if (@$edit_all) { ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table" id="paymentoptions">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="min-width:140px;">Plan&nbsp;Type</th>
                                <th>Deposit</th>
                                <th>Months</th>
                                <th>Interest Rate</th>
                                <th>Start after first timeslot</th>
                                <th><button class="btn add" type="button">add</button> </th>
                            </tr>
                            </thead>

                            <tbody><?php
                            if (is_array(@$data['paymentoptions']))
                                foreach ($data['paymentoptions'] as $poindex => $paymentoption) {
                                    ?>
                                    <tr class="payment_option" data-index="<?=$poindex?>">
                                        <td class="c1">
                                            <span class="po_id">#<?=$paymentoption['id']?></span>
                                            <input type="hidden" name="paymentoption[<?=$poindex?>][id]" class="po_id" value="<?=$paymentoption['id']?>" />
                                        </td>
                                        <td class="c2">
                                            <?=Form::ib_select(null, 'paymentoption['.$poindex.'][interest_type]', array('Percent' => 'Percent', 'Custom' => 'Custom'), $paymentoption['interest_type'], ['class' => 'interest_type'])?>
                                        </td>
                                        <td class="c3"  <?=$paymentoption['interest_type'] == 'Custom' ? 'colspan="3"' : ''?>>
                                            <?= Form::ib_input(null, 'paymentoption['.$poindex.'][deposit]', $paymentoption['deposit'], ['class' => 'deposit' . ($paymentoption['interest_type'] == 'Custom' ? ' hidden' : '')], ['icon' => '<span>€</span>']); ?>
                                            <table class="table custom payment_plan <?=$paymentoption['interest_type'] == 'Custom' ? '' : 'hidden'?>">
                                                <thead>
                                                <th>Amount</th><th>Interest Amount</th><th style="min-width:110px;">Due&nbsp;Date</th><th>Total</th><th><button type="button" class="btn add_custom">Add</button></th>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if ($paymentoption['custom_payments'])
                                                    foreach ($paymentoption['custom_payments'] as $poindex2 => $custom_payment) {
                                                        ?>
                                                        <tr class="custom_option" data-index2="<?=$poindex2?>">
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][amount]', $custom_payment['amount'], ['class' => 'amount'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][interest]', $custom_payment['interest'], ['class' => 'interest'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][due_date]', $custom_payment['due_date'], ['class' => 'due_date']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][total]', $custom_payment['total'], ['class' => 'total'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><button type="button" class="btn remove_custom">remove</button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                </tfoot>
                                            </table>
                                        </td>
                                        <td class="c4 <?=$paymentoption['interest_type'] == 'Custom' ? 'hidden' : ''?>">
                                            <?= Form::ib_input(null, 'paymentoption['.$poindex.'][months]', $paymentoption['months'], ['class' => 'months', 'min' => 2]); ?>
                                        </td>
                                        <td class="c5 <?=$paymentoption['interest_type'] == 'Custom' ? 'hidden' : ''?>">
                                            <?= Form::ib_input(null, 'paymentoption['.$poindex.'][interest_rate]', $paymentoption['interest_rate'], ['class' => 'interest_rate'], ['icon' => '<span>%</span>']); ?>
                                        </td>
                                        <td class="c4 <?=$paymentoption['interest_type'] == 'Custom' ? 'hidden' : ''?>">
                                            <?=Form::ib_checkbox(null, 'paymentoption[' . $poindex . '][start_after_first_timeslot]', 1, $paymentoption['start_after_first_timeslot'] == 1)?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn remove btn-outline-danger">remove</button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            ?></tbody>
                            <tfoot>
                            <tr class="payment_option hidden">
                                <td class="c1">
                                    <span class="po_id"></span>
                                    <input type="hidden" name="paymentoption[index][id]" class="po_id" />
                                </td>
                                <td class="c2">
                                    <?=Form::ib_select(null, 'paymentoption[index][interest_type]', array('Percent' => 'Percent', 'Custom' => 'Custom'), null, ['class' => 'interest_type'])?>
                                </td>
                                <td class="c3">
                                    <?= Form::ib_input(null, 'paymentoption[index][deposit]', null, ['class' => 'deposit'], ['icon' => '<span>€</span>']); ?>
                                </td>
                                <td class="c4">
                                    <?= Form::ib_input(null, 'paymentoption[index][months]', null, ['class' => 'months', 'min' => 2]); ?>
                                </td>
                                <td class="c5">
                                    <?= Form::ib_input(null, 'paymentoption[index][interest_rate]', null, ['class' => 'interest_rate'], ['icon' => '<span>%</span>']); ?>
                                </td>
                                <td class="c4">
                                    <?=Form::ib_checkbox(null, 'paymentoption[index][start_after_first_timeslot]', 1)?>
                                </td>
                                <td>
                                    <button type="button" class="btn remove">remove</button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table class="table hidden custom payment_plan tpl">
                            <thead>
                            <th>Amount</th><th>Interest Amount</th><th style="min-width:110px;">Due&nbsp;Date</th><th>Total</th><th><button type="button" class="btn add_custom">Add</button></th>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr class="custom_option hidden">
                                <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][amount]', null, ['class' => 'amount'], ['icon' => '<span>€</span>']); ?></td>
                                <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][interest]', null, ['class' => 'interest'], ['icon' => '<span>€</span>']); ?></td>
                                <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][due_date]', null, ['class' => 'due_date']); ?></td>
                                <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][total]', null, ['class' => 'total'], ['icon' => '<span>€</span>']); ?></td>
                                <td><button type="button" class="btn remove_custom">remove</button></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="tab-pane" id="edit_schedule-tab-fees">
            <?php if ($edit_all): ?>
                <!-- amendable -->
                <?php $amendable = (isset($data['amendable']) && $data['amendable'] == 1) ? true : false;
                if (Auth::instance()->has_access('courses_schedule_amendable')): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="amendable">Amendable</label>
                        <div class="col-sm-6">
                            <input type="hidden" name="amendable" value="0" />
                            <?= Form::ib_checkbox_switch(null, 'amendable', 1, $amendable) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="amendable" value="<?= ($amendable) ? '1' : '0' ?>" />
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($edit_all) { ?>
                <div class="form-group">
                    <label class="col-sm-3 col-md-2 control-label" for="is_fee_required">Fee required</label>

                    <div class="col-sm-9 col-md-10">
                        <input type="hidden" name="is_fee_required" value="0" />
                        <?php
                        $attributes = ['id' => 'is_fee_required', 'data-hide_toggle' => '#edit-schedule-fee-fields'];
                        echo Form::ib_checkbox_switch(null, 'is_fee_required', 1, $fee_required, $attributes);
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <input type="hidden" id="is_fee_required" name="is_fee_required"  value="<?= ($fee_required) ? '1' : '0'; ?>" />
            <?php } ?>


            <!-- charge per delegate -->
            <?php $checked = (empty($data['id']) || @$data['charge_per_delegate'] == 1); ?>

            <?php if ($edit_all): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="edit-schedule-charge_per_delegate">Charge per delegate</label>

                    <div class="col-sm-9 col-md-10">
                        <div class="left popinit" rel="popover" data-trigger="hover"
                             data-content="If booking for multiple people, charge the fee once per delegate. Turn off to charge a fix rate, regardless of number booked, and not change the pricing if delegates are added or removed after the initial booking.">
                            <input type="hidden" name="charge_per_delegate" value="0" />
                            <?php
                            $attributes = ['id' => 'edit-schedule-charge_per_delegate'];
                            echo Form::ib_checkbox_switch(null, 'charge_per_delegate', 1, $checked, $attributes);
                            ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="charge_per_delegate" value="<?= $checked ? 1 : 0 ?>" />
            <?php endif; ?>


            <?php if (Settings::instance()->get('schedule_enable_invoice') === '1'): ?>
                <div class="form-group">
                    <label class="col-sm-3 col-md-2 control-label" for="edit-schedule-allow_purchase_order">Payment options</label>
                    <div class="col-sm-4">
                        <input type="hidden" name="allow_purchase_order" value="0" />
                        <?php
                        $options = [
                            'allow_purchase_order' => 'Invoice',
                            'allow_credit_card' => 'Credit Card',
                            'allow_sales_quote' => 'Sales Quote',
                        ];
                        $selected = [];
                        $selected[] = ($data['allow_purchase_order'] !== '0') ? 'allow_purchase_order' : '';
                        $selected[] = ($data['allow_credit_card'] !== '0') ? 'allow_credit_card' : '';
                        $selected[] = ($data['allow_sales_quote'] == '1') ? 'allow_sales_quote' : '';
                        $attributes = array('multiple' => 'multiple', 'id' => 'schedule-payment-options');
                        $args = ['multiselect_options' => ['defaultText' => __('Please select')]];
                        echo Form::ib_select('', 'payment_options[]', $options, $selected, $attributes, $args);
                        ?>
                    </div>
                </div>
            <?php else:?>
                <input type="hidden" name="payment_options[]" value="allow_credit_card" />
            <?php endif; ?>

            <div class="form-group<?= $fee_required ? '' : ' hidden'?>" id="edit-schedule-fee-fields">
                <label class="col-sm-3 col-md-2 control-label" for="fee_amount">Fee</label>
                <div class="col-sm-4">
                    <?php
                    $fee_disabled = (@$selected_course['schedule_allow_price_override'] !== null && $selected_course['schedule_allow_price_override'] == 0 && !$edit_all);
                    echo Form::ib_input(null, 'fee_amount', @$data['fee_amount'], ['id' => 'fee_amount', 'disabled' => $fee_disabled], ['icon' => '<span>€</span>']);
                    ?>
                </div>

                <div class="col-sm-4">
                    <?php
                    $options = html::optionsFromArray(['Timeslot' => __('Timeslot'), 'Day' => 'Day', 'Schedule' => __('Schedule'), 'Month' => __('Month')], @$data['fee_per'] ?? 'Schedule');
                    echo Form::ib_select(null, 'fee_per', $options, @$data['fee_per'], ['id' => 'fee_per', 'disabled' => $fee_disabled]);
                    ?>
                </div>
            </div>

            <?php
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') && Model_Plugin::is_enabled_for_role('Administrator', 'bookings') && is_numeric(@$data['id'])) {
                $related_discounts = Model_KES_Discount::get_discounts_for_schedule($data['id'], false);
                if (count($related_discounts) > 0) {
                    ?>
                    <div class="from-group">
                        <div class="col-sm-2"></div>
                        <ul class="col-sm-10">
                            <?php foreach ($related_discounts as $related_discount) { ?>
                                <li><a href="/admin/bookings/add_edit_discount/<?=$related_discount['id']?>" target="_blank"><?=$related_discount['title']?></a> active for this schedule</li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php
                }
            }
            ?>

            <?php if ($edit_all) { ?>
                <!-- deposit -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="deposit">Deposit</label>

                    <div class="col-sm-4">
                        <?= Form::ib_input(null, 'deposit', @$data['deposit'], ['id' => 'deposit'], ['icon' => '<span>€</span>']); ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Fee Type -->
            <?php if ($edit_all) { ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="payment_type">Payment Type</label>

                    <div class="col-sm-4">
                        <?php
                        $options = [1 => 'Pre-pay', 2 => 'Pay as you go'];
                        echo Form::ib_select(null, 'payment_type', $options, @$data['payment_type'], ['id' => 'payment_type']);
                        ?>
                    </div>

                    <div class="col-sm-4<?= (empty($data['payment_type']) || $data['payment_type'] != 2) ? ' hidden' : '' ?>" id="payg_period-wrapper">
                        <?php
                        $options = ['timeslot' => __('Timeslot'), 'week' => __('Week'), 'month' => __('Month')];
                        echo Form::ib_select(null, 'payg_period', $options, @$data['payg_period'], ['id' => 'payg_period']);
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <input type="hidden" name="payment_type" value="1" />
            <?php } ?>

            <?php if ($edit_all) { ?>
                <?php $payg_apply_fees_when_absent = isset($data['payg_apply_fees_when_absent']) ? $data['payg_apply_fees_when_absent'] : false; ?>
                <div class="form-group <?=@$data['payment_type'] == 2 ? '' : 'hidden' ?>" id="payg_apply_fees_when_absent_div">

                    <label class="col-sm-2 control-label" for="payg_absent_fee">Absent Fee</label>
                    <div class="col-sm-4">
                        <?php
                        echo Form::ib_input(null, 'payg_absent_fee', @$data['payg_absent_fee'], ['id' => 'payg_absent_fee'], ['icon' => '<span>€</span>']);
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($edit_all) { ?>
                <!-- Rental Value -->
                <div id="rental_fee_display" class="form-group ">
                    <label class="col-sm-2 control-label" for="rental_fee">Rental Fee %</label>
                    <div class="col-sm-4">
                        <?php
                        $default_rental_fee = Settings::instance()->get('schedule_default_rental_fee');
                        $value = isset($data['rental_fee']) ? $data['rental_fee'] : $default_rental_fee;
                        $attributes = ['id' => 'rental_fee', 'data-default-value' => $default_rental_fee];
                        echo Form::ib_input(null, 'rental_fee', $value, $attributes, ['icon_right' => '%']);
                        ?>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label" for="edit-schedule-trial_timeslot_free_booking">Enable Free Trial Lesson booking</label>

                <div class="col-sm-9 col-md-10">
                    <input type="hidden" name="trial_timeslot_free_booking" value="0" />
                    <?php
                    $attributes = ['id' => 'edit-schedule-trial_timeslot_free_booking'];
                    echo Form::ib_checkbox_switch(null, 'trial_timeslot_free_booking', 1, @$data['trial_timeslot_free_booking'] == 1, $attributes);
                    ?>
                </div>
            </div>

        </div><?php // #edit_schedule-tab-fees ?>

        <?php if ($edit_all && Auth::instance()->has_access('courses_zone_edit')): ?>
            <div class="tab-pane" id="edit_schedule-tab-zones">
                <?php if (sizeof($zones)>0):?>
                    <!-- is zone management enabled -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="zone_management_switch">Zone Management</label>
                        <div class="col-sm-6">
                            <?php $zone_management = isset($data['zone_management']) ? (bool) $data['zone_management'] : false; ?>
                            <input type="hidden" name="zone_management" value="0" />
                            <?php
                            $attributes = ['id' => 'zone_management_switch', 'data-hide_toggle' => '#schedule_zones_area'];
                            echo Form::ib_checkbox_switch(null, 'zone_management', 1, $zone_management, $attributes);
                            ?>
                        </div>
                    </div>

                    <!-- show zones with rows -->
                    <div id="schedule_zones_area"<?= ($zone_management == 1) ? '' : ' class="hidden"' ?>>
                        <table id="schedule_zones_table" class="table table-striped" >
                            <thead>
                                <tr>
                                    <th scope="col">Row Name</th>
                                    <th scope="col">Select Zone</th>
                                    <th scope="col">Price for Zone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (sizeof($location_rows) > 0) : ?>
                                    <?php foreach ($location_rows as $location_row): ?>
                                        <?php $selected_zone_id = 0; $selected_price = 0; ?>
                                        <tr>
                                            <td data-row-id="<?= $location_row['id'] ?>" data-content="row"><?= $location_row['name'] ?></td>
                                            <td data-content="zone">
                                                <select class="form-control">
                                                    <?php foreach ($zones as $zone): ?>
                                                        <?php
                                                        if(sizeof($schedule_zones)>0){

                                                            foreach ($schedule_zones as $schedule_zone){
                                                                if( $zone['id'] == $schedule_zone['zone_id'] AND $schedule_zone['schedule_id']== $data['id'] AND $schedule_zone['row_id']== $location_row['id']){
                                                                    $selected_zone_id = $zone['id'];
                                                                    $selected_price = $schedule_zone['price'];
                                                                }
                                                            }
                                                            if ($selected_zone_id == $zone['id']){
                                                                echo '<option selected="selected" value="'.$zone['id'].'" >'.$zone['name'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$zone['id'].'" >'.$zone['name'].'</option>';
                                                            }
                                                        }else{
                                                            echo '<option value="'.$zone['id'].'" >'.$zone['name'].'</option>';
                                                        }

                                                        ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td data-content="price">
                                                <input data-row-id="<?=$location_row['id']?>" type="number" min="0" value="<?= $selected_price ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else :?>
                                    <tr><td colspan="3">There are no rows for this location.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Zone Management</label>
                        <div class="col-sm-6">
                            <p class="control-label text-left">There are no zones in the system. <a href="/admin/courses/add_zone" target="_blank"><strong>Add zone</strong></a></p>
                        </div>
                    </div>
                <?php endif;?>
            </div><?php // #edit_schedule-tab-zones ?>
        <?php endif; ?>

        <?php if ($edit_all) { ?>
            <?php if (Auth::instance()->has_access('courses_topic_edit')): ?>
                <div class="tab-pane" id="edit_schedule-tab-topics">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left" for="edit_schedule-add_topic">Topic</label>

                        <div class="col-sm-5">
                            <?php
                            $attributes = ['id' => 'edit_schedule-topics'];
                            if (count($topics) > 10) {
                                $options = html::optionsFromRows('id', 'name', $topics, null, ['value' => '', 'label' => '']);
                                $attributes['class'] = 'ib-combobox';
                                $attributes['data-placeholder'] = __('Please select');
                                $args = $search_icon;
                            } else {
                                $options = html::optionsFromRows('id', 'name', $topics, null, $please_select);
                                $args = [];
                            }
                            echo Form::ib_select(null, null, $options, null, $attributes, $args);
                            ?>
                        </div>

                        <div class="col-sm-5">
                            <button type="button" class="btn btn-default form-btn" id="edit_schedule-topics-add">Add</button>
                        </div>
                    </div>

                    <!-- Table -->
                    <p>Added topics</p>

                    <table class="table table-striped" id="edit_schedule-topics-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Remove</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            if ( ! empty($schedule_topics)) {
                                foreach ($schedule_topics as $topic) {
                                    echo View::factory('snippets/item_topic_tr')->set('topic', $topic);
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                </div><!-- #edit_schedule-tab-topics - end -->
            <?php endif; ?>
            <?php if (Auth::instance()->has_access('courses_schedule_content_tab')): ?>
                <div class="tab-pane" id="edit_schedule-tab-content">
                    <?php
                    $id = isset($data['id']) ? $data['id'] : '';
                    echo $content->render_editor(['edit_button_at_depth' => 2, 'schedule_id' => $id]);
                    ?>
                </div>
            <?php endif; ?>
        <?php } ?>

        <div class="tab-pane" id="edit_schedule-tab-navision">
            <table id="navision-events-table" class="table dataTable">
                <thead>
                    <tr>
                        <th><?=__('Event No')?></th>
                        <th><?=__('Cost Centre')?></th>
                        <th><?=__('Campaign Title')?></th>
                        <th><?=__('Description')?></th>
                        <th><?=__('Venue')?></th>
                        <th><?=__('Event Date')?></th>
                        <th><?=__('Status')?></th>
                        <th><?=__('Select')?></th>
                    </tr>
                </thead>
                <thead>
                <tr>
                    <td>
                        <label for="search_remote_event_no" class="hidden">Search by <?=__('Event No')?></label>
                        <input type="text" id="search_remote_event_no" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_event_title" class="hidden">Search by <?=__('Cost Centre')?></label>
                        <input type="text" id="search_remote_event_title" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_cost_centre" class="hidden">Search by <?=__('Campaign Title')?></label>
                        <input type="text" id="search_remote_cost_centre" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_description" class="hidden">Search by <?=__('Description')?></label>
                        <input type="text" id="search_remote_description" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_venue" class="hidden">Search by <?=__('Venue')?></label>
                        <input type="text" id="search_remote_venue" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_event_date" class="hidden">Search by <?=__('Event Date')?></label>
                        <input type="text" id="search_remote_event_date" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td>
                        <label for="search_remote_event_no" class="hidden">Search by <?=__('Status')?></label>
                        <input type="text" id="search_remote_status" class="form-control search_init" name="" placeholder="Search" />
                    </td>
                    <td></td>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div><!-- .tab-content - end -->

    <div class="col-sm-12">
        <div class="form-action-group text-center">
            <button type="submit" class="btn btn-primary save_timetable">Save</button>
            <button type="submit" class="btn btn-primary save_timetable" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
            <button type="reset" class="btn" id="schedule-form-reset">Reset</button>
            <?php if (isset($data['id'])) : ?>
                <a class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if (isset($data['id'])) : ?>
	<div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
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

    <div class="modal fade" id="cannot_delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3>Warning!</h3>
                </div>

                <div class="modal-body">
                    <p>This schedule can not be deleted.</p>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="confirm_events">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 id="ev_title">Confirm</h3>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-primary" id="btn_confirm">Create events</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirm_delete_event">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">?</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected event.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_event_yes">Delete</a>
			</div>
		</div>
	</div>
</div>

<?= View::factory('timeslot_generation_warnings') ?>

<div class="modal fade" id="course_selection_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Course Required</h4>
            </div>
            <div class="modal-body">
                <p>You must select a <strong>course</strong>, before you can generate timeslots.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clear-timeslots-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirm deletions</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to remove all the generated timeslots?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="clear_dates">Delete</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="blackout-date-warning-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirm Addition</h4>
			</div>
			<div class="modal-body">
				<p>The date you have selected, <strong id="blackout-date-warning-date"></strong>, is of the following type, <strong id="blackout-date-warning-type"></strong>.</p>
				<p>Are you sure you wish to add it to the schedule?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" data-dismiss="modal" id="blackout-date-warning-proceed">Proceed Anyway</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="category-date-warning-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm Addition</h4>
            </div>
            <div class="modal-body">
                <p>The date you have selected, <strong id="calendar-date-warning-date"></strong>, is not in the range available for the course.</p>
                <p>Are you sure you wish to add it to the schedule?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal" id="calendar-date-warning-proceed">Proceed Anyway</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="start_end_time_warning_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Timeslots and Schedule Start/End Time mis match</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_data">
                <p id="start_time_warning"></p>
                <p id="end_time_warning"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="proceed_with_times">Continue</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="navision_date_warning_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Navision event date and schedule start date do not match!</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_data">
                <p id="navision_date_warning"></p>
                <p id="start_date_warning"></p>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-primary" data-dismiss="modal" id="proceed_with_unmatched_navision">Continue</button> -->
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
