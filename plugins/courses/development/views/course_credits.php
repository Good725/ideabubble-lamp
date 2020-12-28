<script>
    if (!window.ibpdata){
        window.ibpdata = {};
    }
    window.ibpdata.academicyears = <?=json_encode($academic_years, JSON_PRETTY_PRINT)?>;
</script>

<div class="timeoff-header">
    <div>
        <?php
        $options = $courses;
        $attributes = array('multiple' => 'multiple', 'id' => 'course-credits-course');
        $args = array('multiselect_options' => array('includeSelectAllOption' => true, 'selectAllText' => __('ALL')));
        echo Form::ib_select(__('Course'), 'course_ids[]', $options, null, $attributes, $args);
        ?>
    </div>

    <div></div>

    <div>
        <button
            type="button"
            class="btn btn-primary form-btn button--full text-uppercase"
            id="course-credits-create-btn"
            ><?= __('Create credit') ?></button>
    </div>

    <div id="switch-view">
        <?php
        $options = array('overview' => __('Overview'), 'details' => __('Details'));
        $selected = 'overview';
        $attributes = array('class' => 'stay_inline', 'style' => 'margin: 0;');
        $input_attributes = array('class' => 'coursecredits-view_toggle');
        echo Form::btn_options('view', $options, $selected, false, $input_attributes, $attributes, array('selected_class' => 'btn-default'));
        ?>
    </div>
</div>

<div class="form-row gutters gutters--narrow">
    <div class="col-sm-7">
        <?php
        $periods = array(
            array('title' => '2017', 'start_date' => '2017-01-01', 'end_date' => '2017-12-31'),
            array('title' => '2018', 'start_date' => '2018-01-01', 'end_date' => '2018-12-31'),
            array('title' => '2019', 'start_date' => '2019-01-01', 'end_date' => '2019-12-31')
        );
        ?>

        <input type="hidden" id="timeoff-periods" data-periods="<?= htmlentities(json_encode($periods)) ?>" />

        <div class="row no-gutters btn btn-default button--full form-btn timeoff-period-selector">
            <div class="col-xs-2 col-sm-1">
                <button type="button" class="button--plain button--full form-btn" id="timeoff-daterange-prev">
                    <span class="icon-angle-left text-primary"></span>
                </button>
            </div>

            <div class="col-xs-8 col-sm-10">
                <button type="button" class="button--plain button--full form-btn" id="coursecredits-daterange-selector">
                    <span style="position: relative; top: .25em;">
                        <?= IbHelpers::embed_svg('calendar', array('color' => true, 'width' => '25', 'height' => '25')); ?>
                    </span>
                    &nbsp;
                    <span id="timeoff-period">Period: 2018</span>
                </button>
            </div>

            <div class="col-xs-2 col-sm-1">
                <button type="button" class="button--plain button--full form-btn" id="coursecredits-daterange-next">
                    <span class="icon-angle-right text-primary"></span>
                </button>
            </div>
        </div>

        <div class="nav nav-tabs hidden timeoff-daterange-tabs" id="coursecredits-daterange-tabs">
            <button type="button" class="btn-link" data-tab="last"><?=    __('Last')    ?></button>
            <button type="button" class="btn-link" data-tab="current" class="active"><?= __('Current') ?></button>
            <button type="button" class="btn-link" data-tab="next"><?=    __('Next')    ?></button>
        </div>
    </div>

    <div class="col-sm-5">
        <div class="row gutters gutters--narrow">
            <div class="col-xs-4">
                <?php
                $options = array();
                foreach ($subjects as $subject) {
                    $options[$subject['id']] = $subject['name'];
                }

                $attributes = array('multiple' => 'multiple', 'id' => 'course-credits-filters-modules');
                $args = array('multiselect_options' => array('enableHTML' => true, 'selectAllText' => __('ALL')), 'plain' => true);
                echo Form::ib_select('Modules', 'modules[]', $options, null, $attributes, $args);
                ?>
            </div>

            <div class="col-xs-4">
                <?php
                $options = array(
                    'theory' => 'Theory',
                    'practical' => 'Practical'
                );

                $attributes = array('multiple' => 'multiple', 'id' => 'course-credits-filters-types');
                $args = array('multiselect_options' => array('enableHTML' => true, 'selectAllText' => __('ALL')), 'plain' => true);
                echo Form::ib_select('Credit type', 'types[]', $options, null, $attributes, $args);
                ?>
            </div>

            <div class="col-xs-4">
                <?php
                $options = array(
                    'hours'   => 'Hours',
                    'credit' => 'Credits'
                );

                $attributes = array('id' => 'course-credits-filters-unit');
                $args = array('plain' => true);
                echo Form::ib_select('Unit', 'unit', $options, null, $attributes, $args);
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// todo: replace with data from controller
$data = array(
    'period_type' => 'days',
    'periods' => [
        // start date, end date, is_blackout
        ['2018-01-01', '2018-01-02', false],
        ['2018-01-02', '2018-01-03', false],
        ['2018-01-03', '2018-01-04', false],
        ['2018-01-04', '2018-01-05', false],
        ['2018-01-05', '2018-01-06', false],
        ['2018-01-06', '2018-01-07', false],
        ['2018-01-07', '2018-01-08', false]
    ],
    'breakdown'  => [
        // id, name, total, period 1, period 2, ...
        [1, 'John Flannery',     30, 10, 0, 10, 10,  0,  0, 0],
        [2, 'Derek O\'Donoghue', 30,  0, 0,  0, 10, 10, 10, 0],
    ],
    'totals' => [60, 10, 0, 10, 20, 10, 10, 0]
);
$period_type = !empty($data['period_type']) ? $data['period_type'] : 'days';
?>

<div id="credits-list-details" class="col-sm-12 credits-list-view hidden" style="overflow-x: auto; white-space: nowrap">
    <div class="col-sm-6">
        <table class="table" id="credit-totals-details-table">
        <thead>
            <tr>
                <th scope="col"><?= __('Teacher') ?></th>
                <th scope="col"><?= __('Credits') ?></th>
                <th scope="col"><?= __('Hours') ?></th>
                
            </tr>
        </thead>

        <tbody>
            
        </tbody>

        <tfoot class="timeoff-details-table-totals">
        <tr>
            <td><?= __('Total') ?></td>
            <td></td>
            <td></td>
            
        </tr>
        </tfoot>
    </table>
    </div>
    <div class="col-sm-6">
        <div id="credit-totals-calendar">
        </div>
    </div>
</div>

<div id="credits-list-overview" class="credits-list-view" style="overflow-x: auto; white-space: nowrap">

    <div class="form-row no-gutters">
        <div class="col-xs-12 timeoff-reports">
            <?php
            $range = ' - ';
            $reports = [
                ['amount' => '0', 'title' => __('Total target')],
                ['amount' => '0', 'title' => __('To schedule')],
                ['amount' => '0', 'title' => __('Planned')],
                ['amount' => '0', 'title' => __('Completed')],
                ['amount' => '0', 'title' => __('Pending')]
            ];
            ?>

            <?php foreach ($reports as $report): ?>
                <div class="timeoff-report">
                    <div class="timeoff-report-top">
                        <p class="timeoff-report-amount"><?= $report['amount'] ?></p>
                        <p class="timeoff-report-text">
                            <span class="timeoff-report-title"><?= $report['title'] ?></span>
                        </p>
                    </div>
                    <div class="timeoff-report-bottom">
                        <div class="timeoff-report-period"><?= $range ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <table class="table credits-list" id="credits-list-overview-table">
        <thead>
        <tr>
            <th scope="col"><?= __('Academic Year') ?></th>
            <th scope="col"><?= __('Course') ?></th>
            <th scope="col"><?= __('Module') ?></th>
            <th scope="col"><?= __('Type') ?></th>
            <th scope="col"><?= __('Credits') ?></th>
            <th scope="col"><?= __('Hours') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
        </thead>

        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="credit-details-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/coursecredits/save" method="post" id="credit-details-form">
                <input type="hidden" name="id" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('View/Add Credit') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="alert-area"></div>
                    <div class="form-group">
                    <?php
                    $options = array();
                    foreach ($academic_years as $academic_year) {
                        $options[$academic_year['id']] = $academic_year['title'];
                    }
                    echo Form::ib_select(__('Academic Year'), 'academicyear_id', $options, null);
                    ?>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="course_id" value="" />
                    <?php
                    echo Form::ib_input('Course', 'course', null);
                    ?>
                    </div>

                    <div class="form-group">
                    <?php
                    $options = array();
                    foreach ($subjects as $subject) {
                        $options[$subject['id']] = $subject['name'];
                    }
                    echo Form::ib_select(__('Module'), 'subject_id', $options, null);
                    ?>
                    </div>

                    <div class="form-group">
                        <?php
                        $options = array(
                            ''                 => '',
                            'Additional study' => __('Additional study'),
                            'Assignment'       => __('Assignment'),
                            'Practical'        => __('Practical'),
                            'Theory'           => __('Theory'),
                        );

                        echo Form::ib_select(__('Type'), 'type', $options, null);
                        ?>
                    </div>


                    <div id="credit-schedules-list" class="form-group">
                        <div>
                            <?php
                            echo Form::ib_input('Schedule', null, null, array('id' => 'credit-schedules-autocomplete', 'class' => 'col-sm-8'));
                            ?>
                            <button type="button" class="btn col-sm-2" id="credit-schedules-add">Add</button>
                        </div>

                        <br clear="both" />

                        <ul>
                            <li class="hidden">
                                <input type="hidden" name="schedule_id[]" value="" />
                                <span>sample</span>
                                <a class="remove">x</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row gutters">
                        <div class="col-sm-6">
                            <?php
                            echo Form::ib_input('Credit', 'credit', null);
                            ?>
                        </div>

                        <div class="col-sm-6">
                            <?php
                            echo Form::ib_input('Hours', 'hours', null);
                            ?>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="save-credit-button"><?= __('Save') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="trainer-credit-details-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <table class="table credits-list" id="trainer-credits-list-details-table">
                <thead>
                <tr>
                    <th scope="col"><?= __('Academic Year') ?></th>
                    <th scope="col"><?= __('Course') ?></th>
                    <th scope="col"><?= __('Module') ?></th>
                    <th scope="col"><?= __('Type') ?></th>
                    <th scope="col"><?= __('Credits') ?></th>
                    <th scope="col"><?= __('Hours') ?></th>
                    <th scope="col"><?= __('Actions') ?></th>
                </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="warning-select-schedule-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Schedule Not selected') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Please select a schedule.') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Dismiss') ?></button>
            </div>
        </div>
    </div>
</div>