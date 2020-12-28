<link type="text/css" rel="stylesheet" href="<?= URL::get_engine_assets_base() ?>css/validation.css" />
<link type="text/css" rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('courses') ?>css/eventCalendar.css" />
<link type="text/css" rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('courses') ?>css/eventCalendar_theme_responsive.css" />
<?php $blackout_types = ORM::factory('Calendar_type')->order_by('title')->find_all_published() ?>
<style>
    .ib-fullcalendar {
        position: relative;
        z-index: 2;
    }

    [data-status]               { --status-color: #c9c9c9; }
    [data-status="done" i]      { --status-color: #365eaa; }
    [data-status="scheduled" i],
    [data-status="booked" i]    { --status-color: #3b9e48; }
    [data-status="conflict" i]  { --status-color: #ff493e; }
    [data-status="cancelled" i] { --status-color: #ff9505; }

    [data-type]                  { --type-color: #425ca9; }
    [data-type="booking"]        { --type-color: #425ca9; }
    [data-type="event"]          { --type-color: #f7971e; }
    [data-type="exam"]           { --type-color: #db2139; }
    [data-type="todo"]           { --type-color: #603eb6; }
    [data-type="timesheet"]      { --type-color: #43a149; }
    [data-type="timeoff"]        { --type-color: #cc0; }
    [data-type="holiday"]        { --type-color: #8f8f8f; }
    [data-type="college_closed"] { --type-color: #b6b6b6; }
    [data-type="farm_closed"]    { --type-color: #323232; }

    /* Different shade of grey for each blackout. */
    <?php
    // Avoid division by zero error when there is exactly one.
    $count = count($blackout_types) > 1 ? count($blackout_types) : 2;
    ?>
    <?php
    // This will give each blackout a different shade of grey spread over lightnesses form 30% to 90%
    foreach ($blackout_types as $i => $blackout_type): ?>
        [data-type="blackout-<?= htmlspecialchars($blackout_type->title) ?>"] {
            --type-color: hsl(0, 0%, <?= 30 + $i * (60 / ($count - 1)) ?>%);
        }
    <?php endforeach; ?>

    .ib-fullcalendar .fc-event.fc-event {
        background: none;
        color: #000;
        border-width: 0 0 0 .25em;
        border-left-style: solid;
    }

    .fc-event:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        opacity: .3;
    }

    .fc-event[data-type] { border-color: var(--type-color); }
    .fc-event[data-type]:before { background-color: var(--type-color); }

    .fc-event[data-type="booking"][data-status] { border-color: var(--status-color); }
    .fc-event[data-type="booking"][data-status]:before { background-color: var(--status-color); }

    .progressbar-item { background-color: var(--status-color); }

    .timetable-activities-item[data-type] {
        background-color: var(--type-color);
    }

    .timetable-activities-item[data-status] {
        background-color: var(--status-color);
    }

    .ib-fullcalendar .fc-toolbar {
        display: none;
    }

    .fc-month-view .fc-event .fc-time {
        display: block
    }

    .popover.timetable-add_slot-popover {
        transform: translate(0, -50%);
        width: 100%;
        max-width: 500px;
    }

    .ib-fullcalendar .popover {
        width: 500px;
        max-width: 500px;
    }

    .timetable-planner-key {
        display: inline-block;
        font-size: .875em;
        margin-right: 1em;
    }

    .timetable-planner-key:before {
        content: '';
        background-color: var(--status-color);
        border: 1px solid #aaa;
        display: inline-block;
        width: .8em;
        height: .8em;
    }

    /* Ensure these items are visible when the filter item is hovered. */
    .progressbar-item[data-status] {
        border: solid #fff;
        border-width: 1px 0;
    }

    .progressbar-item[data-status]:first-child {
        border-left-width: 1px;
    }

    .progressbar-item[data-status]:last-child {
        border-right-width: 1px;
    }

    .dropdown-menu li:hover .progressbar-item-label {
        color: #fff;
    }

    /* This reset needs to be removed at the root after confirming it is no longer needed. */
    .fc-month-view .fc-day:hover:after {
        display: none;
    }

    .fc-month-view .fc-day:hover {
        background: none;
        cursor: unset;
    }

    .ib-fullcalendar .eventCalendar-wrap {
        overflow: unset;
    }

    td[data-label="Status"] {
        position: relative;
    }

    td[data-label="Status"] .timetable-planner-timeslot-status:before {
        content: attr(data-status);
        background: var(--status-color);
        color: #fff;
        display: flex;
        align-items: center;
        padding: .5em;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
</style>

<?php
$daterangepicker = isset($daterangepicker) ? $daterangepicker : true;
$daterange_start = date('Y-m-01'); // start of month
$daterange_end = date('Y-m-t'); // end of month

$filter_menu_options = isset($filter_menu_options) ? $filter_menu_options : [];

$add_timeslot_button = (!empty($add_timeslot_button) && Auth::instance()->has_access('timetables_add_slot'));
?>

<input type="hidden" class="ibcalendar-timeslots-url" id="ibcalendar-timeslots-url" value="<?= isset($timeslots_url) ? $timeslots_url : '' ?>" />
<input type="hidden" class="ibcalendar-popover-mode"  id="ibcalendar-popover-mode" value="<?=  isset($popover_mode)  ? $popover_mode  : '' ?>" />

<div class="form-row gutters vertically_center">
    <div class="col-xs-12 col-sm-6 ibcalendar-range-text text-center text-sm-left"></div>

    <div class="col-xs-12 col-sm-6 text-sm-right mb-2 mb-sm-0">
        <div class="ibcalendar-view-toggle d-block d-sm-inline-block">
            <?php
            echo Form::btn_options(
                'grid_period',
                ['agendaDay' => 'Day', 'agendaWeek' => 'Week', 'month' => 'Month', 'listMonth' => 'List'],
                'month',
                false,
                ['class' => 'timetables-grid_period', 'style' => 'width: 25%;'],
                ['class' => 'stay_inline d-sm-inline-block w-auto']
            );
            ?>
        </div>
    </div>
</div>

<div class="ib-fullcalendar" id="<?= $id_prefix ?>-fullcalendar"></div>

<?php // Form for event popover ?>
<div class="hidden" id="timetable-planner-event-edit">
    <div class="form--thin_gutters timetable-planner-event-edit-form" id="timetable-planner-event-edit-form-popover">
        <ul class="nav nav-tabs" id="timetable-planner-event-tabs">
            <li class="active">
                <a href="#timetable-planner-event-tab-details" data-toggle="tab">Details</a>
            </li>

            <li>
                <a href="#timetable-planner-event-tab-attendees" data-toggle="tab">
                    Attendees
                    (<span id="timetable-planner-event-tab-attendees-count">0</span>)
                </a>
            </li>
        </ul>

        <div class="tab-content bg-white border-left border-right border-bottom">
            <div class="tab-pane active" id="timetable-planner-event-tab-details">

                <?php
                if ($popover_mode == 'read') {
                    echo View::factory('admin/snippets/slot_form_read_mode')->set('mode', 'popover');
                } else {
                    echo View::factory('admin/snippets/slot_form')->set('mode', 'popover');
                }
                ?>

                <div class="customize_register">
                    <?php if ($popover_mode == 'read'): ?>
                        <hr />
                    <?php endif; ?>

                    <label><input type="radio" value="1" name="customize_register_place" class="customize no"  /> &nbsp;Book All sessions</label> <br />
                    <label><input type="radio" value="0" name="customize_register_place" class="customize yes" /> &nbsp;Custom</label>
                </div>

                <hr />

                <div class="form-action-group">
                    <?php if (isset($popover_mode) && $popover_mode == 'read'): ?>
                        <button
                            type="button"
                            class="btn btn-register register_place hidden">Register <span class="register_place-amount"></span>
                        </button>
                        <p class="calendar-popover-is_attending hidden">
                            <span class="icon-check is_attending_icon"></span>
                            Attending
                        </p>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary save"><?= __('Save slot') ?></button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane" id="timetable-planner-event-tab-attendees">
                <div id="timetable-planner-event-attendees-wrapper"></div>
            </div>
        </div>
    </div>
</div>

<?php if ($add_timeslot_button): ?>
    <?php ob_start(); ?>
        <div class="row gutters">
            <button type="button" class="btn btn-primary save"><?= __('Save slots') ?></button>
            <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
        </div>
    <?php $schedule_timeslots_footer = ob_get_clean(); ?>

    <?= View::factory('snippets/modal')
    ->set('id',     $id_prefix.'-schedule_timeslots-modal')
    ->set('title',  __('Manage schedule slots'))
    ->set('body',   View::factory('admin/snippets/schedule_timeslots')->set('mode', 'modal'))
    ->set('footer', $schedule_timeslots_footer)
    ->set('size', 'lg');
    ?>

    <?= View::factory('timeslot_generation_warnings') ?>

    <script src="<?= URL::get_engine_plugin_assets_base('courses') ?>js/schedules_form.js"></script>
<?php endif; ?>

<script src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2-en.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/timetable_view.js"></script>

<script>
    $(document).ready(function() {
        var calendar = new Ib_calendar({
            id: '<?= $id_prefix ?>',
            bookings_enabled: <?= !empty($bookings_enabled) ? 'true' : 'false' ?>,
            popover_mode: '<?= !empty($popover_mode) ? $popover_mode : 'read' ?>'
        });
    });
</script>