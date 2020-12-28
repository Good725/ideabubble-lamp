<script>
if (!window.ibpdata){
    window.ibpdata = {};
}
window.ibpdata.academicyears = <?=json_encode($academic_years, JSON_PRETTY_PRINT)?>;
</script>
<?php $view = (isset($_GET['view']) && in_array(strtolower($_GET['view']), 'people', 'locations', 'courses')) ? strtolower($_GET['view']) : 'people'; ?>


<div class="form-row gutters">
    <?php if (!$my) { ?>
    <div class="col-sm-4">
        <input type="hidden" id="timetables-filter_object_id" value="" />
        <?php
        $attributes = array(
            'id' => 'timetables-filter_object',
            'placeholder' => __('Search staff'),
            'data-people-placeholder'    => __('Search staff'),
            'data-locations-placeholder' => __('Search locations'),
            'data-courses-placeholder'   => __('Search courses')
        );
        echo Form::ib_input(null, '', null, $attributes);
        ?>
    </div>


    <div class="col-sm-offset-2 col-sm-6">
        <?php
        $options = array('people' => __('People'), 'locations' => __('Locations'), 'courses' => __('Courses'));
        $selected = $view;
        $attributes = array('class' => 'stay_inline', 'style' => 'margin: 0;');
        $input_attributes = array('class' => 'timetables-view_toggle');
        echo Form::btn_options('view', $options, $selected, false, $input_attributes, $attributes, array('selected_class' => 'btn-default'));
        ?>
    </div>
    <?php }  else { ?>
    <input type="hidden" id="mytimetables_only" value="<?=$me['id']?>" />
    <div class="col-sm-10"><h3><?=$me['first_name'] . ' ' . $me['last_name'];?></h3></div>
    <?php } ?>
</div>


<div class="form-row gutters">
    <div class="col-sm-8">
        <div class="row no-gutters btn btn-default button--full form-btn timeoff-period-selector timetables-period-selector">
            <div class="col-xs-2 col-sm-1">
                <button type="button" class="button--plain button--full form-btn" id="timeoff-daterange-prev">
                    <span class="icon-angle-left text-primary"></span>
                </button>
            </div>

            <div class="col-xs-8 col-sm-10">
                <button type="button" class="button--plain button--full form-btn" id="timetables-daterange-selector">
                    <span style="position: relative; top: .25em;">
                        <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                    </span>
                    &nbsp;
                    <span id="timetables-period">Period: 2018</span>
                </button>
            </div>

            <div class="col-xs-2 col-sm-1">
                <button type="button" class="button--plain button--full form-btn" id="timeoff-daterange-next">
                    <span class="icon-angle-right text-primary"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="nav nav-tabs hidden timeoff-daterange-tabs" id="timeoff-daterange-tabs">
        <button type="button" class="btn-link" data-tab="last"><?=    __('Last')    ?></button>
        <button type="button" class="btn-link" data-tab="current" class="active"><?= __('Current') ?></button>
        <button type="button" class="btn-link" data-tab="next"><?=    __('Next')    ?></button>
    </div>


    <div class="col-sm-4">
        <div class="row gutters">
            <div class="col-xs-6 timetable-activities-wrapper">
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

                $attributes = array('multiple' => 'multiple', 'id' => 'timetable-activities');
                $args = array('multiselect_options' => array('enableHTML' => true, 'selectAllText' => __('ALL')));
                echo Form::ib_select('Activities', 'activities[]', $options, null, $attributes, $args);
                ?>
            </div>

            <div class="col-xs-6">
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn btn-default button--full form-btn dropdown-toggle timetables-grid_period-button"
                        id="timetables-grid_period-button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                        >
                        <?= __('View') ?>
                    </button>

                    <ul class="timetables-grid_period-options dropdown-menu pull-right" aria-labelledby="timetables-grid_period-button" id="timetables-grid_period-options">
                        <?php
                        $grid_periods = array('agendaDay' => __('Day'), 'agendaWeek' => __('Week'), 'month' => __('Month'), 'listMonth' => __('List'));
                        $checked = 'weeks';
                        ?>
                        <?php foreach ($grid_periods as $key => $grid_period): ?>
                            <li>
                                <label class="timeoff-radio-bullet">
                                    <input
                                        type="radio"
                                        class="timetables-grid_period"
                                        data-calendar_view="<?= $key ?>"
                                        name="grid_period"
                                        value="<?= $key ?>"
                                        <?= $key == $checked ? 'checked="checked"' : '' ?>
                                        />
                                    <span><?= $grid_period ?></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-row no-gutters">
    <div class="col-xs-12 timeoff-reports" id="timetables-reports">
        <div class="timeoff-report">
            <div class="timeoff-report-top">
                <p class="timeoff-report-amount"></p>
                <p class="timeoff-report-text">
                    <span class="timeoff-report-title"></span>
                </p>
            </div>

            <div class="timeoff-report-bottom">
                <div class="timeoff-report-period"></div>
            </div>
        </div>
    </div>
</div>

<div class="timetables-fullcalendar" id="timetables-fullcalendar" ></div>
