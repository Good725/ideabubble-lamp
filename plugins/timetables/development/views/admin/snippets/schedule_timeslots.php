<?php
$id_prefix = 'timetable-planner-add_slot-modalx-';
$edit_timeslot_label_classes  = 'col-xs-auto col-sm-4 col-md-2';
$edit_timeslot_column_classes = 'col-sm-8 col-md-6';

$search_icon   = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>', 'arrow_position' => false];
$trainers = isset($trainers) ? $trainers : Model_Contacts3::get_teachers();
$topics_all = isset($topics_all) ? $topics_all : Model_Topics::get_all_topics();
$engineCalendarEvents = isset($engineCalendarEvents) ? $engineCalendarEvents : Model_Calendar_Event::getEventList('courses');
?>
<div id="edit_schedule-tab-timeslots">
    <select id="trainer_id" class="hidden">
        <option value=""></option>
        <?=html::optionsFromRows('id', 'full_name', $trainers ?? [], "", "");?>
    </select>
    <select id="edit_schedule-topics-add" class="hidden">
        <option value=""></option>
        <?=html::optionsFromRows('id', 'name', $topics_all ?? [], "", "");?>
    </select>

    <div class="form-group vertically_center">
        <label class="<?= $edit_timeslot_label_classes ?>" for="timetable-planner-add_slot-course"><?= __('Course') ?></label>

        <div class="<?= $edit_timeslot_column_classes ?>">
            <input type="hidden" id="<?=$id_prefix?>course_id" name="course_id" value="" />
            <?php
            $options = '';

            $attributes = [
                'placeholder' => __('Course title'),
                'id' => $id_prefix.'course'
            ];
            echo Form::ib_input(null, 'course', '', $attributes, $search_icon);
            ?>
        </div>
    </div>

    <div class="form-group vertically_center">
        <label class="<?= $edit_timeslot_label_classes ?>" for="timetable-planner-add_slot-schedule"><?= __('Schedule') ?></label>

        <div class="<?= $edit_timeslot_column_classes ?>">
            <input type="hidden" id="<?=$id_prefix?>schedule_id" name="schedule_id" value="" />
            <?php
            $options = '';
            $attributes = [
                'placeholder' => __('Schedule'),
                'id' => $id_prefix . 'schedule'
            ];
            echo Form::ib_input(null, 'schedule', '', $attributes, $search_icon);
            ?>
        </div>
    </div>

    <div class="form-group vertically_center">
        <?php if ($mode == 'modal'): ?>
            <label class="<?= $edit_timeslot_label_classes ?>" for="timetable-planner-add_slot-topic"><?= __('Topic') ?></label>
        <?php endif; ?>

        <div class="<?= $edit_timeslot_column_classes ?>">
            <input type="hidden" id="<?=$id_prefix?>topic_id" name="topic_id" value="" />
            <?php
            $options = '';
            $attributes = [
                'placeholder' => __('Add topic'),
                'id' => $id_prefix.'topic'
            ];
            echo Form::ib_input(null, 'topic', '', $attributes, $search_icon);
            ?>
        </div>
    </div>

    <div class="form-group vertically_center">
        <?php if ($mode == 'modal'): ?>
            <label class="<?= $edit_timeslot_label_classes ?>" for="timetable-planner-add_slot-staff"><?= __('Staff') ?></label>
        <?php endif; ?>

        <div class="<?= $edit_timeslot_column_classes ?>">
            <input type="hidden" id="<?=$id_prefix?>contact_id" name="contact_id" value="" />
            <?php
            $options = '';

            $attributes = [
                'placeholder' => __('Staff'),
                'id' => $id_prefix.'contact'
            ];
            echo Form::ib_input(null, 'contact', '', $attributes, $search_icon);
            ?>
        </div>
    </div>

    <div class="form-group vertically_center">
        <?php if ($mode == 'modal'): ?>
            <label class="<?= $edit_timeslot_label_classes ?>" for="timetable-planner-add_slot-staff"><?= __('Location') ?></label>
        <?php endif; ?>

        <div class="<?= $edit_timeslot_column_classes ?>">
            <input type="hidden" id="<?=$id_prefix?>location_id" name="location_id" value="" />
            <?php
            $options = '';

            $attributes = [
                'placeholder' => __('Location'),
                'id' => $id_prefix.'location'
            ];
            echo Form::ib_input(null, 'location', '', $attributes, $search_icon);
            ?>
        </div>
    </div>

        <!-- Blackout Days -->
        <div class="form-group">
            <label class="<?= $edit_timeslot_label_classes ?>" for="blackout_calendar_event_ids[]">Blackouts</label>

            <div class="<?= $edit_timeslot_column_classes ?>">
                <?php
                $options = html::optionsFromRows('id', 'event', $engineCalendarEvents, '{all}');
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

        <!-- Repeat -->
        <div class="form-group">
            <label class="<?= $edit_timeslot_label_classes ?>" for="repeat">Repeat</label>
            <div class="col-sm-6">
                <?php
                $options = '<option value="">None</option>' . Model_Schedules::get_repeat_as_options();
                echo Form::ib_select(null, 'repeat', $options, null, ['id' => 'repeat']);
                ?>
            </div>
        </div>

    <!-- Start Time -->
    <div class="form-group hidden" id="repeat_start_time">
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
    <div id="custom_schedule" class="hidden">
        <!-- Frequency -->
        <div class="form-group">
            <label class="<?= $edit_timeslot_label_classes ?>" for="frequency">Frequency:</label>
            <div class="<?= $edit_timeslot_column_classes ?>">
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
                <button type="button" class="btn btn-primary" id="generate_dates">Generate Timeslots</button>
            </div>

            <div class="col-sm-3 text-right">
                <button type="button" class="btn btn-danger clear-timeslot" style="margin-right: 0;">Clear Timeslots</button>
            </div>
        </div>
    </div>

    <!-- Schedules Time Slots -->

    <!-- Schedule -->
    <div id="datepicker-outer" class="form-group">
        <label class="<?= $edit_timeslot_label_classes ?>" for="datepicker">Select your Dates</label>
        <div class="<?= $edit_timeslot_column_classes ?>">
            <div id="datepicker"></div>
        </div>
    </div>

    <h3>Timeslots <small id="schedule-form-timeslot-results" data-count="0">(Showing <span id="schedule-form-timeslot-results-count">0</span> results)</small></h3>

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
                <th scope="col">Location</th>
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
                <td colspan="11" class="text-right">
                    <button class="prev btn" type="button">Prev</button>
                    <button class="next btn" type="button">Next</button>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="hidden" id="selected_dates-empty">
        <p><?= __('No timeslots have been selected.') ?></p>
    </div>


    <div class="form-group">
        <div class="col-sm-6">
            <input type="hidden" id="timetable_id" name="timetable_id"/>
            <input type="hidden" value="" name="new_timetable_name" id="new_timetable_name" />
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
                <button type="button" class="btn btn-danger" id="clear_dates">Delete</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<select class="hidden" id="all_locations">
    <option value=""></option>
    <?php
    $all_locations = html::optionsFromRows('value', 'label', Model_Locations::autocomplete_locations());
    ?>
    <?=$all_locations?>
</select>
