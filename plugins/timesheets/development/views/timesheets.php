<?php
$role = isset($role) ? $role : 'staff';

// Open the mode specified in the URL.
// Only managers have access to the mode switcher.
$view = ($role == 'manager' && isset($_GET['view']) && in_array(strtolower($_GET['view']), ['details', 'overview', 'approvals'])) ? strtolower($_GET['view']) : 'overview';

echo isset($alert) ? $alert : '';
?>

<div>

    <div>
        <div class="timeoff-header" data-staff_id="<?php echo $staffId ?>" data-staff_role="<?php echo $role?>">
            <?php if ($role == 'manager'): ?>
                <div>
                    <?php
                    $attributes = array('multiple' => 'multiple', 'id' => 'timesheets-department');
                    $args = array('multiselect_options' => array('includeSelectAllOption' => true, 'selectAllText' => __('ALL')));
                    echo Form::ib_select(__('Department'), 'department_ids[]', $departments, array_keys($departments), $attributes, $args);
                    ?>
                </div>
            <?php else: ?>
                <div class="timeoff-username">
                    <img src="<?= URL::get_avatar($user['id']) ?>" alt="" width="40" height="40" style="margin-right: .5em;" />
                    <strong class="nowrap-ellipsis"><?= $user['name'].' '.$user['surname'] ?></strong>
                </div>
            <?php endif; ?>
        
            <div style="display: flex; align-items: center;">
                <span class="nowrap"><?= __('Total hours') ?>&nbsp;</span>
        
                <strong class="nowrap">
                    <span class="timesheets-hours_logged time_logged" ></span> /
                    <span class="timesheets-hours_logged time_available"></span>
                </strong>
            </div>

            <?php if ($role != 'manager'): ?>
                <div>
                    <div class="btn-group" role="group" style="display: flex;">
                        <button type="button" class="btn btn-primary form-btn">
                            Submit period <span ng-bind="vm.fmtPeriod(vm.openTimesheets[0])"></span>
                        </button>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary form-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width: 2.5em;">
                                <span class="icon-caret-down"></span>
                            </button>

                            <div class="dropdown-menu pull-right" style="padding: .75em;">
                                <h5 class="text-center text-uppercase"><?= __('Recent timesheets') ?></h5>

                                <ul class="list-unstyled" id="recent_timesheets">
                                    <li class="timesheets-period-status-dropdown-item hidden">
                                        <div class="timesheets-period-status-item-container">
                                            <div class="row gutters vertically_center">
                                                <div class="col-xs-7">
                                                    <span class="timesheets-period-status-item-title"></span>
                                                    <span class="timesheets-period-status-item-status timesheets-status-text" ></span>
                                                </div>

                                                <div class="col-xs-5 timeoff-period-status-text-indicator text-right">
                                                    <span ng-bind="vm.diffHuman(timesheet)" ng-show="vm.timesheetDifference(timesheet) != 0" ng-class="{'timesheets-period-status-negative': vm.timesheetDifference(timesheet) < 0, 'timesheets-period-status-positive': vm.timesheetDifference(timesheet) > 0}">
                                                    </span>
                                                    <button type="button" class="btn btn-default submit"><?= __('Submit') ?></button>
                                                </div>
                                            </div>

                                            <div class="timesheets-period-status-bar">
                                                <div ng-show="vm.duePercentage(timesheet) < 100" class="timesheets-period-status-bar-marker" ng-style="{left: vm.duePercentage(timesheet) + '%'}"></div>
                                                <div class="timesheets-period-status-bar-logged"        ng-style="{width: vm.loggedPercentage(timesheet, 100)+'%'}"></div>
                                                <div class="timesheets-period-status-bar-due"           ng-style="{width: vm.duePercentage(timesheet) + '%'}"></div>
                                                <div class="timesheets-period-status-bar-logged_to_due" ng-style="{width: vm.loggedToDuePercentage(timesheet) + '%'}"></div>
                                            </div>

                                            <div class="row gutters">
                                                <span class="col-xs-6 timesheets-period-status-item-bottom">2018-10-08 - 2018-10-14</span>
                                                <span class="col-xs-6 timesheets-period-status-item-bottom text-right">
                                                    <span class="minutes_logged"></span>
                                                    /
                                                    <span class="minutes_available"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (Auth::instance()->has_access('timesheets_edit') || Auth::instance()->has_access('timesheets_edit_limited')) { ?>
            <div style="flex: 0;">
                <button
                    ng-click="vm.newRequest()"
                    type="button"
                    class="btn btn-primary form-btn button--full text-uppercase"
                    id="timesheets-log_work"
                ><?= __('Log work') ?></button>
            </div>
            <?php } ?>

            <?php if ($role == 'manager'): ?>
                <div class="btn-group btn-group-pills btn-group-pills-regular stay_inline timeoff-mode-toggle" style="margin: 0;">
                    <button class="btn form-btn timesheet-view overview"><span><?= __('Overview')  ?></span></button>
                    <button class="btn form-btn timesheet-view details"><span><?= __('Details')   ?></span></button>
                    <button class="btn form-btn timesheet-view approvals"><span><?= __('Approvals') ?></span></button>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-row gutters vertically_center">
            <div class="col-xs-12 col-sm-8">
                <?php
                $current_weekday    = (date('w') + 6) % 7; // 1 = Monday, 2 = Tuesday, ... 7 = Sunday
                $current_week_start = date('Y-m-d', strtotime('-'.$current_weekday.' days')); // Get the Monday of this week
                $current_week_end   = date('Y-m-d', strtotime('+'.(6 - $current_weekday).' days')); // Get the Sunday of this week
                $attributes = ['id' => 'timesheets-daterange'];
                echo Form::ib_daterangepicker('datetime_start', 'datetime_end', $current_week_start, $current_week_end, $attributes);
                ?>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="timesheet-request_status-wrapper hidden">
                    <?php
                    $log_types = ['course' => __('Course'), 'internal' => __('Internal')];
                    $attributes = array(
                        'multiple' => 'multiple',
                        'id' => 'timesheets-log_types',
                    );
                    $args = array('multiselect_options' => array('includeSelectAllOption' => true, 'selectAllText' => __('ALL')));
                    echo Form::ib_select('Log type', 'type[]', $log_types, array_keys($log_types), $attributes, $args);
                    ?>
                </div>
        
                <div class="row no-gutters timesheet-grid-wrapper hidden">
                    <div class="col-xs-7">
        
                        <div class="dropdown">
                            <button
                                type="button"
                                class="btn btn-default button--full form-btn dropdown-toggle timeoff-grid_period-button"
                                id="timeoff-grid_period-button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?=__('Days')?>
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
                                            <input type="radio" class="timeoff-grid_period" name="grid_period" value="<?= $key ?>"<?= $key == $checked ? ' checked="checked"' : '' ?> />
                                            <span><?= $grid_period ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
        
                    <div class="col-xs-offset-1 col-xs-4">
                        <div class="dropdown">
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
                                    <a href="/api/timesheets/detailscsv" target="_blank" class="btn-link" id="details-export-csv"><?= __('Export CSV') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters timesheets-status-wrapper hidden">
                    <?php
                    $statuses = array(
                        'pending' => __('Waiting for approval'),
                        'ready' => __('Ready to submit'),
                        'open' => __('Open'),
                        'approved' => __('Approved'),
                        'declined' => __('Declined')
                    );
                    $attributes = array(
                        'multiple' => 'multiple',
                        'id' => 'timesheets-status',
                    );
                    $args = array('multiselect_options' => array('includeSelectAllOption' => true, 'selectAllText' => __('ALL')));
                    echo Form::ib_select('Status', 'status[]', $statuses, array_keys($statuses), $attributes, $args);
                    ?>
                </div>
            </div>
        </div>
        
        <div id="timesheets-overview" class="hidden">
            <div class="form-row no-gutters">
                <div class="col-xs-12 timeoff-reports">
                    <div class="timeoff-report">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount total-available"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Total hours available</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period total-available"></div>
                        </div>
                    </div>
                
                    <div class="timeoff-report">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount course-logged"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Course hours logged</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period course-logged"></div>
                        </div>
                    </div>
                
                    <div class="timeoff-report">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount internal-logged"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Internal hours logged</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period internal-logged"></div>
                        </div>
                    </div>
                
                    <div class="timeoff-report">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount total-logged"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Total hours logged</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period total-logged"></div>
                        </div>
                    </div>
                
                    <div class="timeoff-report">
                        <div class="timeoff-report-top">
                            <p class="timeoff-report-amount hours-left"></p>
                            <p class="timeoff-report-text">
                                <span class="timeoff-report-title">Hours left</span>
                            </p>
                        </div>
                        <div class="timeoff-report-bottom">
                            <div class="timeoff-report-period hours-left"></div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="form-row" page-size="10">
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
                <table class="table table-striped dataTable-collapse" id="timesheets_table">
                    <thead>
                        <tr>
                            <th scope="col" ib-th="period_start_date">Date</th>
                            <th scope="col" ib-th="person">Person</th>
                            <th scope="col" ib-th="department">Department</th>
                            <th scope="col" ib-th="type">Log type</th>
                            <th scope="col" ib-th="item">Schedule / item</th>
                            <th scope="col" ib-th="description">Description</th>
                            <th scope="col" ib-th="duration">Hours</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="timesheet_row hidden">
                            <td data-label="Date"></td>
                            <td data-label="Person"></td>
                            <td data-label="Department"></td>
                            <td data-label="Log type"></td>
                            <td data-label="Schedule / item"></td>
                            <td data-label="Description"></td>
                            <td data-label="Hours"></td>
                            <td data-label="Actions">
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
        
        <div id="timesheet-details" class="hidden">
            <div style="overflow-x: auto; white-space: nowrap">
                <table class="table" id="timesheet-details-table"></table>
            </div>
            <div class="modal fade" id="period-log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Select request</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped dataTable-collapse" id="timesheets_table_period">
                                <thead>
                                <tr>
                                    <th scope="col" ib-th="period_start_date">Date</th>
                                    <th scope="col" ib-th="person">Person</th>
                                    <th scope="col" ib-th="department">Department</th>
                                    <th scope="col" ib-th="type">Log type</th>
                                    <th scope="col" ib-th="item">Schedule / item</th>
                                    <th scope="col" ib-th="description">Description</th>
                                    <th scope="col" ib-th="duration">Hours</th>
                                    <th scope="col">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="timesheet_row hidden">
                                    <td data-label="Date"></td>
                                    <td data-label="Person"></td>
                                    <td data-label="Department"></td>
                                    <td data-label="Log type"></td>
                                    <td data-label="Schedule / item"></td>
                                    <td data-label="Description"></td>
                                    <td data-label="Hours"></td>
                                    <td data-label="Actions">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="timesheet-approvals" class="hidden">
            <?php echo View::factory('snippets/ng-approvals') ?>
        </div>
        
        <?php echo View::factory('snippets/select-request') ?>
        <?php echo View::factory('snippets/log-work', array('staffs' => $staffs)) ?>
        

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

        <?php ob_start(); ?>
            <h3><?= __('Submit timesheet for review') ?> - <span>Fred Flintstone</span></h3>

            <div class="timesheets-period-submit form-horizontal">
                <div class="row gutters timesheets-submit-header">
                    <div class="col-xs-7">
                        <span class="timesheets-submit-header-title">Period 2018-09-24 </span>
                        <span class="timesheets-submit-header-dates">2018-09-24 - 2018-09-30</span>
                    </div>

                    <div class="col-xs-5 text-right">
                        <span class="badge timesheets-status-badge" data-status="ready to submit">ready to submit</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="row no-gutters timesheets-submit-work_required">
                            <div class="col-xs-7">
                                <span class="timesheets-submit-work_required-label">Hours logged</span>
                                <span>24.92</span>
                            </div>

                            <div class="col-xs-5 text-right">
                                <span class="timesheets-submit-work_required-reached">All worklogs valid</span>
                            </div>
                        </div>

                        <div class="row no-gutters timesheets-submit-work_required">
                            <div class="col-xs-7">
                                <span class="timesheets-submit-work_required-label">Hours required</span><span>40</span>
                            </div>

                            <div class="col-xs-5 text-right">
                                <span class="timesheets-submit-work_required-not_reached">15.08 hours missing</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group gutters vertically_center">
                    <label class="col-sm-2" for="timesheets-submit-reviewer"><?= __('Reviewer') ?></label>

                    <div class="col-sm-6">
                        <?php
                        echo Form::ib_select(null, 'reviewer_id', $reviewers, null, array('class' => 'ib-combobox', 'id' => 'timesheets-submit-reviewer'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <?= Form::ib_textarea(null, 'note', null, array('rows' => '3', 'placeholder' => 'Leave a note for the reviewer...')) ?>
                    </div>
                </div>

                <div class="row gutters vertically_center timesheets-submit-footer">
                    <div class="col-sm-6 text-left">
                        <a href="#"><?= __('View timesheet') ?></a>
                    </div>

                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-primary"><?= __('Submit') ?></button>
                        <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                    </div>
                </div>
            </div>
        <?php $modal_body = ob_get_clean(); ?>

        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'timesheets-period-submit-modal')
            ->set('title',  false)
            ->set('body',   $modal_body);
        ?>

        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'timeoff-error-modal')
            ->set('title',  __('Error'))
            ->set('body',   '<div id="timeoff-error-modal-message"></div>')
            ->set('footer', '<button type="button" class="btn btn-default" data-dismiss="modal">'.__('OK').'</button>');
        ?>
    
        <?php echo View::factory('snippets/timesheet-details', array('staffs' => $staffs, 'reviewers' => $reviewers)) ?>
        
    </div>

</div>

<script>
    $('#timesheets-log_work-schedule-combobox-wrapper').on('change', '[name="schedule_filter"]', function()
    {
        // Don't dismiss the autocomplete when one of these options is chosen.
        $('.ui-autocomplete').show();
    });
</script>
