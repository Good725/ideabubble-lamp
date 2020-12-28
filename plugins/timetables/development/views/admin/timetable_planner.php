<?php
$please_select = ['' => __('Please select')];
$overlay       = ['overlay' => '<span class="form-input-overlay-inner timetables-status-text" data-status="Pending"></span>'];
$search_icon   = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>', 'arrow_position' => false];
?>

<form class="form--thin_gutters" id="timetable-planner-filters">
    <div class="form-row gutters vertically_center">
        <div class="col-sm-6">
            <input type="hidden" id="calendar-filter-course_id" value="" />
            <?php
            $options = '';

            $attributes = [
                'placeholder' => __('Course title'),
                'id' => 'calendar-filter-course'
            ];
            echo Form::ib_input(null, 'course', '', $attributes, $search_icon);
            ?>
        </div>

        <div class="col-xs-12 col-sm-4">
            <?php
            $attributes = array('multiple' => 'multiple', 'id' => 'timetable-planner-schedule');
            $args = [
                'multiselect_options' => [
                    'enableCaseInsensitiveFiltering' => true,
                    'enableClickableOptGroups' => true,
                    'enableFiltering' => true,
                    'enableHTML' => true,
                    'includeSelectAllOption' => true,
                    'numberDisplayed' => 1,
                    'selectAllText' => __('ALL')
                ],
                'has_parent_label' => true
            ];

            $options = '';
            foreach ($schedules as $schedule) {
                $schedule_statuses = [
                    ['name' => 'Done', 'amount' => $schedule['done']],
                    ['name' => 'Booked',  'amount' => $schedule['booked']],
                    ['name' => 'Pending', 'amount' => $schedule['pending']]
                ];
                
                $percentage_hours_done = ($schedule['hours'] == 0 || $schedule['done'] == 0) ? 0 :
                    round($schedule['done'] / $schedule['hours'] * 100);
                $percentage_hours_done = ($percentage_hours_done > 100) ? 100 : $percentage_hours_done;
                $progress_bar = View::factory('snippets/progress_bar')->set('statuses', $schedule_statuses)->set('total_hours', $schedule['hours']);

                $options .= '<option value="'.$schedule['id'].'" data-course_id="'.$schedule['course_id'].'" selected="selected"">'.
                    htmlspecialchars("{$schedule['name']} - $percentage_hours_done% progress {$schedule['pending']}h pending $progress_bar"). '</option>';
            }
            ?>

            <?php ob_start(); ?>
                <button type="button" class="btn btn-primary form-btn btn--full">
                    <?= __('Select schedules') ?>
                    <span class="form-select-mask-count"><?= count($schedules) ?></span>
                    <span class="arrow_caret-down"></span>
                </button>
            <?php $args['mask'] = ob_get_clean(); ?>

            <div class="schedule-selector">
                <?= Form::ib_select(null, 'schedule_ids[]', $options, null, $attributes, $args); ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-primary form-btn btn--full add-slot" data-toggle="modal" data-target="#timetables-calendar-schedule_timeslots-modal"><?= __('Add slot') ?></button>
        </div>
    </div>

    <div class="form-group gutters">
        <div class="col-sm-12">
            <?php
            $start_date = date('Y-m-d');
            $end_date   = date('Y-m-d', strtotime('+1 week'));
            //echo Form::ib_daterangepicker('daterange_start', 'daterange_end', $start_date, $end_date, ['id' => 'timetable-planner-daterange']);
            ?>
        </div>
    </div>

    <div class="form-row no-gutters vertically_center">
        <div class="col-md-11">
            <div class="form-row gutters">
                <div class="col-sm-4">
                    <?php
                    $options = Model_Timetables::status_optgroups($staff, true);
                    $attributes = ['multiple' => 'multiple', 'id' => 'timetable-planner-trainer_id'];
                    $args = [
                        'multiselect_options' => [
                            'enableClickableOptGroups' => true,
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering' => true,
                            'enableHTML' => true,
                            'filterPlaceholder' => __('Search staff'),
                            'includeSelectAllOption' => true,
                            'numberDisplayed' => 1,
                            'selectAllJustVisible' => false,
                            'selectAllText' => __('ALL')
                        ]
                    ];

                    echo Form::ib_select(__('Teacher'), 'teacher_ids[]', $options, null, $attributes, $search_icon + $args)
                    ?>
                </div>

                <div class="col-sm-4">
                    <?php
                    $options = Model_Timetables::status_optgroups($locations, true);
                    $attributes = ['multiple' => 'multiple', 'id' => 'timetable-planner-location_id'];
                    $args['multiselect_options']['filterPlaceholder'] = __('Search locations');
                    echo Form::ib_select(__('Location'), 'location_ids[]', $options, null, $attributes, $search_icon + $args);
                    ?>
                </div>

                <div class="col-sm-4">
                    <?php
                    $options = Model_Timetables::status_optgroups($topics, true);
                    $attributes = ['multiple' => 'multiple', 'id' => 'timetable-planner-topic_id'];
                    $args['multiselect_options']['filterPlaceholder'] = __('Search topics');
                    echo Form::ib_select(__('Topics'), 'topic_ids[]', $options, null, $attributes, $search_icon + $args) ?>
                </div>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-row">
                <button type="reset" class="btn-cancel"><?= __('Clear all') ?></button>
            </div>
        </div>
    </div>
</form>

<div class="form--thin_gutters">
    <div class="form-row gutters" style="display: flex; align-items: flex-end; flex-wrap: wrap;">
        <div class="col-xs-12 col-md-6">
            <?php $statuses = ['Done', 'Booked', 'Pending', 'Conflict', 'Available']; ?>

            <?php foreach ($statuses as $status): ?>
                <span class="timetable-planner-key" data-status="<?= $status ?>">
                    <?= $status ?>
                </span>
            <?php endforeach; ?>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="row gutters">
                <div class="col-xs-4">
                    <div class="dropdown">
                        <?php
                        $views   = ['agendaDay' => 'Day', 'agendaWeek' => 'Week', 'month' => 'Month', 'list' => 'List'];
                        $checked = 'agendaWeek';
                        ?>

                        <button
                            type="button"
                            class="btn btn-default button--full form-btn dropdown-toggle"
                            id="timetable-planner-view-button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                            style="font-weight: 300;"
                        >
                            <?= __('View') ?>
                            <span class="btn-dropdown-selected_text"><?= $views[$checked] ?></span>
                        </button>

                        <ul class="dropdown-menu pull-right" aria-labelledby="timetable-planner-view-button" id="timetable-planner-view-options" style="min-width: 0; width: 100%;">
                            <?php foreach ($views as $key => $view): ?>
                                <li>
                                    <label class="radio-bullet">
                                        <input
                                            type="radio" class="dropdown-menu-radio timetables-grid_period"
                                            name="grid_period" value="<?= $key ?>"
                                            data-range_type="<?= strtolower($view) ?>"
                                            <?= $key == $checked ? ' checked="checked"' : '' ?> />
                                        <span><?= $view ?></span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- are these needed -->
                <div class="col-xs-4 hidden">
                    <div class="dropdown">
                        <?php
                        $units        = ['hour' => __('Hours'), 'credit' => __('Credits')];
                        $unit_types   = ['%' => '%', 'numeric' => __('Numeric')];
                        $default_unit = 'hour';
                        $default_type = '%';
                        ?>

                        <button
                            type="button"
                            class="btn btn-default btn--full form-btn"
                            id="timetable-planner-units-button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                            style="font-weight: 300;"
                        >
                            <?= __('Units') ?>
                            <span class="btn-dropdown-selected_text"><?= $units[$default_unit] ?></span>
                        </button>

                        <ul class="dropdown-menu pull-right" aria-labelledby="timetable-planner-view-button" id="timetable-planner-view-options" style="min-width: 0; width: 100%;">
                            <li>
                                <h3 class="text-uppercase"><?= __('Unit') ?></h3>
                                <ul class="list-unstyled">
                                    <?php foreach ($units as $key => $unit): ?>
                                        <li>
                                            <label class="radio-bullet">
                                                <input type="radio" class="dropdown-menu-radio" name="unit" value="<?= $key ?>"<?= $key == $default_unit ? ' checked="checked"' : '' ?> />
                                                <span><?= $unit ?></span>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                            <li>
                                <h3 class="text-uppercase"><?= __('Type') ?></h3>
                                <ul class="list-unstyled">
                                    <?php foreach ($unit_types as $key => $type): ?>
                                        <li>
                                            <label class="radio-bullet">
                                                <input type="radio" name="unit_type" value="<?= $key ?>"<?= $key == $default_type ? ' checked="checked"' : '' ?> />
                                                <span><?= $type ?></span>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="col-xs-4 hidden">
                    <?php
                    $activities = array(
                        'booking'   => __('Bookings'),
                        'timeslot'     => __('Timeslots'),
                        'exam'      => __('Exams'),
                        'todo'      => __('Todos'),
                        'timesheet' => __('Timesheets'),
                        'timeoff'   => __('Timeoff')
                    );
                    $blackouts = array(
                        'holiday'        => __('Holidays'),
                        'college_closed' => __('College closed'),
                        'farm_closed'    => __('Farm closed')
                    );

                    $options = array('Activities' => array(), 'Blackouts' => array());

                    foreach ($activities as $key => $activity) {
                        $options['Activities'][$key] = htmlentities('<span class="timetable-activities-item" data-type="'.$key.'"></span>').$activity;
                    }

                    foreach ($blackouts as $key => $blackout) {
                        $options['Blackouts'][$key] = htmlentities('<span class="timetable-activities-item" data-type="'.$key.'"></span>').$blackout;
                    }

                    $attributes = ['multiple' => 'multiple', 'id' => 'timetable-activities'];
                    $args = [
                        'arrow_position' => 'none',
                        'multiselect_options' => [
                            'enableHTML' => true,
                            'includeSelectAllOption' => true,
                            'numberDisplayed' => 2,
                            'selectAllText' => __('ALL')
                        ]
                    ];
                    echo Form::ib_select('Activities', 'activities[]', $options, null, $attributes, $args);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="timetables-fullcalendar" id="timetable-planner-fullcalendar"></div>

<span class="timetable-attendance right hidden" id="timetable-attendance-template">
    <span class="icon_group"></span>
    <span class="timetable-attendance-amount"></span>
</span>

<?php include 'timetable_planner_requests.php'; ?>

<?php
ob_start();
$mode = 'modal';
include 'snippets/slot_form.php';
$modal_body = ob_get_clean();
?>

<?php ob_start(); ?>
    <div class="row gutters">
        <div class="col-sm-3">
            <?= Form::ib_checkbox(__('Add another'), 'add_another', 1, false, null, 'left') ?>
        </div>

        <div class="col-sm-9">
            <button type="button" class="btn btn-primary save"><?= __('Add slot') ?></button>
            <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
        </div>
    </div>
<?php $modal_footer = ob_get_clean(); ?>

<form class="timetable-planner-event-edit-form" id="timetable-planner-event-edit-form-modal">
<?php
echo View::factory('snippets/modal')
    ->set('id',     'timetable-planner-add_slot-modal')
    ->set('title',  __('Add a course slot'))
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer);
?>
</form>

<?php
ob_start();
include 'snippets/schedule_timeslots.php';
$schedule_timeslots_body = ob_get_clean();
?>

<?php ob_start(); ?>
<div class="row gutters">
    <button type="button" class="btn btn-primary save"><?= __('Save slots') ?></button>
    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
</div>
<?php $schedule_timeslots_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'timetables-calendar-schedule_timeslots-modal')
    ->set('title',  __('Manage schedule slots'))
    ->set('body',   $schedule_timeslots_body)
    ->set('footer', $schedule_timeslots_footer)
    ->set('size', 'lg');
?>


<?php // Form for event popover ?>
<div class="hidden" id="timetable-planner-event-edit">
    <form class="form--thin_gutters timetable-planner-event-edit-form" id="timetable-planner-event-edit-form-popover">
        <?php
        $mode = 'popover';
        include 'snippets/slot_form.php';
        ?>
        <div class="row gutters">
            <button type="button" class="btn btn-primary save"><?= __('Save slot') ?></button>
        </div>
    </form>
</div>

<!-- Template for "day" view heading in the calendar -->
<div class="hidden" id="timetable-agendaDay-header">
    <h3 class="timetable-agendaDay-header-title text-primary text-left">Test</h3>

    <div class="progressbar">
        <span class="progressbar-item" data-status="Done"></span>
        <span class="progressbar-item" data-status="Booked"></span>
        <span class="progressbar-item" data-status="Pending">1</span>
        <span class="progressbar-item" data-status="Conflict">1</span>
        <span class="progressbar-item" data-status="Available">40</span>
    </div>
</div>

<?php echo View::factory('admin/snippets/schedule_timeslot_conflicts'); ?>

