<?php
$role = isset($role) ? $role : 'staff';
$mine_only = ($role != 'manager');

// Open the mode specified in the URL.
// Only managers have access to the mode switcher.
$view = ($role == 'manager' && isset($_GET['view']) && strtolower($_GET['view']) == 'details') ? 'details' : 'overview';

echo isset($alert) ? $alert : '';
?>

<style>
    /* Temporary. When this is moved to the iblisting template, we can properly place the heading. */
    #navbar-breadcrumbs {margin-bottom: 0;}
    #navbar-breadcrumbs h1 {display: none;}
</style>

<div ng-app="app.timeoff" id="timeoff-app">
    <input type="hidden" id="timeoff-role" value="<?= Auth::instance()->has_access('timeoff_requests_edit') ? 'manager' : 'staff' ?>" />
    <input type="hidden" id="filterStaffId" value="<?=@$filterStaffId?>" />
    <div ng-controller="TimeoffController as vm" ng-init="vm.init(<?php echo $staffId ?>, '<?php echo $role ?>');">
        <div class="timeoff-header">
            <div>
                <h1><?= ($mine_only) ? 'My requests' : 'All requests' ?></h1>
            </div>

            <div>
                <span><?= __('Days left') ?></span>

                <strong><span id="timeoff-days-remaining">0</span> / <span id="timeoff-days-total">0</span></strong>
            </div>

            <?php if ($role != 'manager'): ?>
                <div></div>
            <?php endif; ?>

            <div>
                <button
                    type="button"
                    class="btn btn-primary form-btn button--full text-uppercase"
                    id="timeoff-make_request"
                ><?= __('Request timeoff') ?></button>
            </div>

            <?php if ($role == 'manager'): ?>
                <div ng-show="vm.role=='manager'" class="btn-group btn-group-pills btn-group-pills-regular stay_inline timeoff-mode-toggle" style="margin: 0;">
                    <button id="display_overview" class="btn btn-primary"><span>Overview</span></button>
                    <button id="display_details" class="btn"><span>Details</span></button>
                </div>
            <?php endif; ?>

        </div>



        <div class="form-row gutters vertically_center">
            <div class="col-xs-12 col-sm-5">
                <?php
                $current_weekday    = (date('w') + 6) % 7; // 1 = Monday, 2 = Tuesday, ... 7 = Sunday
                $current_week_start = date('Y-m-d', strtotime('-'.$current_weekday.' days')); // Get the Monday of this week
                $current_week_end   = date('Y-m-d', strtotime('+'.(6 - $current_weekday).' days')); // Get the Sunday of this week
                $attributes = ['id' => 'timeoff-daterange-selector'];
                echo Form::ib_daterangepicker('datetime_start', 'datetime_end', $current_week_start, $current_week_end, $attributes);
                ?>
            </div>

            <div class="col-xs-12 col-sm-7 timeoff-request_filters">
                <?php
                // todo: Get from API
                $statuses = array(
                    'pending'   => __('Pending'),
                    'approved'  => __('Approved'),
                    'declined'  => __('Declined'),
                    'cancelled' => __('Cancelled')
                );

                $staff_options = [];
                foreach ($staff_members as $staff_member) {
                    $staff_options[$staff_member->id] = $staff_member->get_full_name();
                }
                $options = [];
                if ($mine_only) {
                    $options[] = ['name' => 'staff_id',      'label' => 'Staff',      'options' => [$staffId => Auth::instance()->get_contact()->get_full_name()], 'selected' => $staffId];
                } else {
                    $options[] = ['name' => 'department_id', 'label' => 'Department', 'options' => $departments];
                    $options[] = ['name' => 'staff_id',      'label' => 'Staff',      'options' => $staff_options];
                }
                $options[] = ['name' => 'status', 'label' => 'Status', 'options' => $statuses];

                $options[] = ['name' => 'type', 'label' => 'Type', 'options' => array_combine($leave_types, array_map('ucfirst', $leave_types))];

                echo Form::ib_filter_menu($options)
                ?>
                <div class="hidden timeoff-details-options">

                        <div class="dropdown col-sm-4 pr-1 pl-1">
                            <button
                                    type="button"
                                    class="btn btn-default button--full form-btn dropdown-toggle timeoff-grid_period-button"
                                    id="timeoff-grid_period-button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                                    ng-bind="vm.periodTypeLabel()">
                            </button>

                            <ul class="timeoff-grid_period-options dropdown-menu pull-right" aria-labelledby="timeoff-grid_period-button" id="timeoff-grid_period-options">
                                <?php
                                $grid_periods = array('days' => __('Days'), 'weeks' => __('Weeks'));
                                $checked = 'days';
                                ?>
                                <li>
                                    <div class="text-uppercase" style="padding: .5em;"><?= __('Grid periods') ?></div>
                                </li>

                                <?php foreach ($grid_periods as $key => $grid_period): ?>
                                    <li>
                                        <label class="timeoff-radio-bullet">
                                            <input type="radio" ng-model="vm.filters.period_type" class="timeoff-grid_period" name="grid_period" value="<?= $key ?>"<?= $key == $checked ? ' checked="checked"' : '' ?> />
                                            <span><?= $grid_period ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="dropdown col-sm-1 pr-1 pl-1">
                            <button
                                    type="button"
                                    class="btn btn-default button--full form-btn dropdown-toggle timeoff-details-more"
                                    id="timeoff-details-more"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
                            >
                                <span class="flaticon-more"></span>
                            </button>

                            <ul class="timeoff-details-more-options dropdown-menu pull-right" aria-labelledby="timeoff-details-more" id="timeoff-details-more-options">
                                <li>
                                    <button type="button" class="btn-link" id="export-csv"><?= __('Export CSV') ?></button>
                                </li>
                            </ul>
                        </div>
                    </div>

            </div>
        </div>

        <div id="timeoff-overview">
            <div class="form-row no-gutters">
                <div class="col-xs-12 timeoff-reports">
                    <div class="timeoff-report days_available">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount" ng-bind="vm.timeFormat(vm.stats.days_available)"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Total days available</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period" ng-bind="vm.period()"></div>
                        </div>
                    </div>

                    <div class="timeoff-report days_pending_approval">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount" ng-bind="vm.timeFormat(vm.stats.days_pending_approval)"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Days pending approval</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period" ng-bind="vm.period()"></div>
                        </div>
                    </div>

                    <div class="timeoff-report days_in_lieu">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount" ng-bind="vm.timeFormat(vm.stats.days_in_lieu)"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Days in lieu</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period" ng-bind="vm.period()"></div>
                        </div>
                    </div>

                    <div class="timeoff-report days_approved">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount" ng-bind="vm.timeFormat(vm.stats.days_approved)"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Days approved</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period" ng-bind="vm.period()"></div>
                        </div>
                    </div>

                    <div class="timeoff-report days_left">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount" ng-bind="vm.timeFormat(vm.stats.days_left)"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Days left</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period" ng-bind="vm.period()"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div id="timeoff-requests" class="hidden">
                    <div class="dataTables_length">
                        <label>
                            Show
                            <select class="pagination-limit">
                                <option ng-value="10">10</option>
                                <option ng-value="20">20</option>
                                <option ng-value="50">50</option>
                            </select>
                            entries
                        </label>
                    </div>
                    <div class="dataTables_filter">
                        <label>
                            Search: <input type="text" class="search" id="timesheets_table-search">
                        </label>
                    </div>
                    <table class="table table-striped dataTable-collapse" id="timeoff_requests_table">
                        <thead>
                        <tr>
                            <th scope="col">Staff ID</th>
                            <th scope="col">Full name</th>
                            <th scope="col">Department</th>
                            <th scope="col">Position</th>
                            <th scope="col">Start date</th>
                            <th scope="col">End date</th>
                            <th scope="col">Leave type</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Status</th>
                            <th scope="col">Manager updated</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="request_row hidden">
                            <td class="staff_id"></td>
                            <td class="staff_name"></td>
                            <td class="department_name"></td>
                            <td class="staff_position"></td>
                            <td class="period"></td>
                            <td class="date"></td>
                            <td class="type"></td>
                            <td class="duration"></td>
                            <td class="status"></td>
                            <td class="updated"></td>
                            <td scope="col">
                                <div class="action-btn">
                                    <a href="#" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li><button type="button" class="view">View</button></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="no_records hidden">
                        <p>There are no records to display.</p>
                    </div>
                    <div class="pagination-wrapper hidden">
                        <div class="form-row gutters vertically_center">
                            <div class="col-sm-6">
                                <p>Showing <span class="from"></span> to <span class="to"></span> of <span class="total"></span> entries</p>
                            </div>

                            <div class="col-sm-6 text-right">
                                <div class="paging_bootstrap pagination">
                                    <ul>
                                        <li class="prev">
                                            <button type="button">← Previous</button>
                                        </li>

                                        <li class="page hidden">
                                            <button type="button" class="hidden"></button>
                                        </li>

                                        <li class="next">
                                            <button type="button">Next →</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="timeoff-details" class="hidden">
            <div style="overflow-x: auto; white-space: nowrap">
                <table class="table" id="timeoff-details-table"></table>
            </div>
        </div>

        <?php ob_start(); ?>
            <?php $user = Auth::instance()->get_user(); ?>
            <form action="" class="form-horizontal" id="timeoff-request-modal-form">
                <div class="form-group">
                    <div class="col-sm-8">
                        <label class="control-label text-left" for="timeoff-request-modal-department"><?= __('Department') ?></label>
                        <?php

                        $department_options = $default_options;
                        foreach ($departments as $department_id => $department) {
                            $department_options[$department_id] = $department;
                        }

                        echo Form::ib_select(null, 'department_id', $department_options, null, [
                                'id' => 'timeoff-request-modal-department',
                                'ng-model' => 'vm.formdata.department',
                                'ng-disabled' => 'vm.mode == \'view\' && vm.role == \'staff\'',
                                'ng-options' => 'option.name for option in vm.departments'
                        ]);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-8">
                        <label class="control-label text-left" for="timeoff-request-modal-type"><?= __('Type') ?></label>

                        <?php
                        // Todo: get from API
                        $options = array(
                            '' => __('Please select'),
                            'annual'        => __('Annual'),
                            'bereavement'   => __('Bereavement'),
                            'force majeure' => __('Force majeure'),
                            'sick'          => __('Sick'),
                            'lieu'          => __('Time in lieu'),
                            'other'         => __('Other')
                        );
                        echo Form::ib_select(null, 'type', $options, null, [
                            'class' => 'validate[required]',
                            'id' => 'timeoff-request-modal-type',
                        ]);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-5">
                        <label class="control-label text-left" for="timeoff-request-modal-date-input"><?= __('Date') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-xs-10">
                                <?php
                                $hidden_attributes = [
                                    'type'        => 'text',
                                    'class'       => 'timeoff-request-conflict_check sr-only',
                                    'id'          => 'timeoff-request-modal-date',
                                    'ng-model'    => 'vm.formdata.startDate',
                                ];
                                $display_attributes = [
                                    'class'       => 'validate[required]',
                                    'id'          => 'timeoff-request-modal-date-input',
                                    'data-before' => '#timeoff-request-modal-end_date',
                                ];
                                echo Form::ib_datepicker(null, 'start_date', null, $hidden_attributes, $display_attributes)
                                ?>
                            </span>
        
                            <span class="col-xs-2 text-center">
                                <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>

                    <div class="col-sm-5" id="timeoff-request-modal-end_date-wrapper" ng-show="vm.formdata.range_toggle">
                        <label class="control-label text-left" for="timeoff-request-modal-end_date-input"><?= __('End date') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-sm-10">
                                <?php
                                $hidden_attributes = [
                                    'type'        => 'text',
                                    'class'       => 'datepicker1 timeoff-request-conflict_check sr-only',
                                    'id'          => 'timeoff-request-modal-end_date'
                                ];
                                $display_attributes = [
                                    'class'       => 'validate[required]',
                                    'id'          => 'timeoff-request-modal-end_date-input',
                                    'data-after'  => '#timeoff-request-modal-date'
                                ];
                                echo Form::ib_datepicker(null, 'end_date', null, $hidden_attributes, $display_attributes);
                                ?>
                            </span>
        
                            <span class="col-sm-2 text-center">
                                <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>
                </div>

                <div>
                    <?php
                    $attributes = array(
                            'id' => 'timeoff-request-modal-date_range-toggle',
                            'ng-model' => 'vm.formdata.range_toggle',
                            'ng-disabled' => 'vm.mode == \'view\' && vm.role == \'staff\'',
                            'ng-change' => 'vm.updateDuration()'
                    );
                    echo Form::ib_checkbox('<span class="control-label">'.__('Range').'</span>', 'hourly_range', 1, true, $attributes)
                    ?>
                </div>

                <div class="form-group hidden" id="timeoff-request-modal-time_range">
                    <div class="col-sm-5">
                        <label class="control-label text-left" for="timeoff-request-modal-start_time"><?= __('Start time') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-xs-10">
                                <?php
                                $attributes = array(
                                    'autocomplete' => 'off',
                                    'class' => 'timepicker timeoff-request-conflict_check',
                                    'id' => 'timeoff-request-modal-start_time',
                                    'ng-model' => 'vm.formdata.startTime',
                                    'ng-disabled' => 'vm.mode == \'view\' && vm.role == \'staff\'',
                                    'ng-change' => 'vm.updateDuration()'
                                );
                                echo Form::ib_input(null, 'start_time', '00:00', $attributes);
                                ?>
                            </span>
        
                            <span class="col-xs-2 text-center">
                                <?= IbHelpers::embed_svg('clocks', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>

                    <div class="col-sm-5">
                        <label class="control-label text-left" for="timeoff-request-modal-end_time"><?= __('End time') ?></label>

                        <label class="row no-gutters vertically_center">
                            <span class="col-xs-10">
                                <?php
                                $attributes = array(
                                    'class' => 'timepicker timeoff-request-conflict_check',
                                    'id' => 'timeoff-request-modal-end_time',
                                    'ng-model' => 'vm.formdata.endTime',
                                    'ng-disabled' => 'vm.mode == \'view\' && vm.role == \'staff\'',
                                    'ng-change' => 'vm.updateDuration()'
                                );
                                echo Form::ib_input(null, 'end_time', '00:00', $attributes);
                                ?>
                            </span>
        
                            <span class="col-xs-2 text-center">
                                <?= IbHelpers::embed_svg('clocks', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <label class="control-label text-left" for="timeoff-request-modal-duration_formatted"><?= __('Duration') ?></label>

                        <?= Form::ib_input(null, 'time', null, [
                            'ng-model' => 'vm.durationFormatted',
                            'readonly' => 'readonly',
                            'id' => 'timeoff-request-modal-duration_formatted'
                        ]) ?>
                    </div>
                </div>

                <div class="form-group" ng-show="v.role == 'manager' || vm.formdata.status != 'approved'">
                    <div class="col-sm-12">
                        <label class="control-label text-left" for="timeoff-request-modal-staff_note"><?= __('Note') ?></label>
                        <?= Form::ib_textarea(null, 'staff_note', null, [
                                'rows' => '3',
                                'ng-model' => 'vm.formdata.note',
                                'id' => 'timeoff-request-modal-staff_note'
                        ]); ?>
                    </div>
                </div>


                    <div ng-hide="vm.formdata.type == 'lieu'">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <h3><?= __('Schedule conflicts') ?></h3>
                            </div>
                        </div>

                        <div class="form-group hidden--time_in_lieu">
                            <div class="col-sm-12 hidden" id="timeoff-request-modal-schedule_conflicts-section">
                                <table class="table table-striped dataTable dataTable-collapse" id="timeoff-request-modal-schedule_conflicts">
                                    <thead>
                                    <tr>
                                        <th scope="col"><?= __('ID') ?></th>
                                        <th scope="col"><?= __('Title') ?></th>
                                        <th scope="col"><?= __('Date') ?></th>
                                        <th scope="col"><?= __('Time') ?></th>
                                        <th scope="col"><?= __('Course') ?></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>

                                    <tfoot class="hidden" id="schedule-conflict-template">
                                    <tr>
                                        <td data-label="<?= __('ID')      ?>" data-stub="id"></td>
                                        <td data-label="<?= __('Title')   ?>" data-stub="title"></td>
                                        <td data-label="<?= __('Date')    ?>" data-stub="date"></td>
                                        <td data-label="<?= __('Time')    ?>" data-stub="time"></td>
                                        <td data-label="<?= __('Course')  ?>" data-stub="course"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-sm-12" id="timeoff-request-modal-schedule_conflicts-none">
                                <p><?= __('There are no conflicts in the selected time range.') ?></p>
                            </div>
                        </div>

                        <div class="form-group hidden--time_in_lieu">
                            <div class="col-sm-4">
                                <h3><?= __('Leave conflicts') ?></h3>
                            </div>

                            <div class="col-sm-5">

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-12" id="timeoff-request-modal-request_conflicts-none">
                                    <p><?= __('There are no conflicts in the selected time range.') ?></p>
                                </div>
                                <table class="table table-striped dataTable-collapse hidden" id="conflict-requests">
                                    <thead>
                                    <tr>
                                        <th scope="col">Start date</th>
                                        <th scope="col">End date</th>
                                        <th scope="col">Leave type</th>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date approved</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr class="conflict-request-row hidden">
                                        <td scope="col"></td>
                                        <td scope="col"></td>
                                        <td scope="col"></td>
                                        <td scope="col"></td>
                                        <td scope="col"></td>
                                        <td scope="col"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                <?php
                $auth = Auth::instance();
                if ($auth->has_access('timeoff_requests_approve') || $auth->has_access('timeoff_requests_approve_limited')):
                ?>
                    <div class="form-group hidden--new">
                        <div class="col-sm-8">
                            <label class="control-label text-left" for="timeoff-request-modal-status"><?= __('Status') ?></label>

                            <?php
                            // todo: Get from API
                            $options = array(
                                ''          => __('Please select'),
                                'pending'   => __('Pending'),
                                'approved'  => __('Approved'),
                                'declined'  => __('Declined'),
                                'cancelled' => __('Cancelled')
                            );
                            echo Form::ib_select(null, 'status', $options, null, array(
                                'id' => 'timeoff-request-modal-status',
                                'ng-model' => 'vm.formdata.status'
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif ?>
            </form>
        <?php $modal_body = ob_get_clean(); ?>

        <?php ob_start(); ?>
            <div class="timeoff-edit-actions">
                <button type="button" class="btn btn-primary hidden--existing" id="timeoff-request-modal-submit">
                    <?= __('Submit request') ?>
                </button>
                <button type="button" class="btn btn-primary hidden--new" id="timeoff-request-modal-save">
                    <?= __('Save request') ?>
                </button>

                <?php
                $auth = Auth::instance();
                if ($auth->has_access('timeoff_requests_approve')) {
                    ?>
                    <span class="hidden--new">
                <button id="timeoff-request-modal-approve" type="button" class="btn btn-default"><?= __('Approve') ?></button>
                <button id="timeoff-request-modal-decline" type="button" class="btn btn-default"><?= __('Decline') ?></button>
                </span>
                <?php
                }
                ?>
                <button type="button" class="btn btn-cancel"><?= __('Cancel') ?></button>
            </div>

            <div class="timeoff-readonly-actions hidden">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('OK') ?></button>
            </div>


            <input type="hidden" id="timeoff-request-modal-id" value="" />
        <?php $modal_footer = ob_get_clean(); ?>

        <?php echo View::factory('snippets/select-request') ?>

        <div ng-controller="RequestFormController as vm">
            <?php
            echo View::factory('snippets/modal')
                ->set('id',     'timeoff-request-modal')
                ->set('title',  '<div id="timeoff-request-modal-title">Submit a request</div>')
                ->set('body',   $modal_body)
                ->set('footer', $modal_footer)
            ?>
        </div>


        <?php ob_start(); ?>
            <div class="form-row">
                <table class="table table-striped dataTable dataTable-collapse" id="timeoff-requests-table">
                    <thead>
                    <tr>
                        <th scope="col"><?= __('Staff ID') ?></th>
                        <th scope="col"><?= __('Full name') ?></th>
                        <th scope="col"><?= __('Department') ?></th>
                        <th scope="col"><?= __('Position') ?></th>
                        <th scope="col"><?= __('Start date') ?></th>
                        <th scope="col"><?= __('End date') ?></th>
                        <th scope="col"><?= __('Leave type') ?></th>
                        <th scope="col"><?= __('Duration') ?></th>
                        <th scope="col"><?= __('Status') ?></th>
                        <th scope="col"><?= __('Date approved') ?></th>
                        <th scope="col"><?= __('Actions') ?></th>
                    </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        <?php $modal_body = ob_get_clean(); ?>

        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'timeoff-multiple_requests-modal')
            ->set('size',   'lg')
            ->set('title',  __('View requests'))
            ->set('body',   $modal_body)
        ?>


        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'timeoff-error-modal')
            ->set('title',  __('Error'))
            ->set('body',   '<div id="timeoff-error-modal-message"></div>')
            ->set('footer', '<button type="button" class="btn btn-default" data-dismiss="modal">'.__('OK').'</button>');
        ?>

        <?php
        // todo: handle this within the API?
        function timeoff_format_time($time_in_hours, $format, $show_zero = false)
        {
            switch ($format) {
                case 'd':
                    $days = round($time_in_hours / 7, -1);
                    $days = ($days < 1 && $days > 0) ? '0'.$days : $days;
                    $return = $days.'d';
                    break;

                case 'h m':
                default:
                    $hours   = floor($time_in_hours);
                    $minutes = round($time_in_hours * 60) % 60;
                    $return  = ($hours   != 0) ? $hours.'h '   : '';
                    $return .= ($minutes != 0) ? $minutes.'m ' : '';

                    if ($return == '' && $show_zero) {
                        $return = '0m';
                    }

                    break;
            }

            return $return;
        }
        ?>
    </div>
</div>