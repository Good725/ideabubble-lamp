<style>
    #applications-list-table td{vertical-align: middle;}
</style>

<input type="hidden" id="application-stage" value="<?= $stage ?>" />

<div id="applications-alert_area"></div>

<form class="form-horizontal form--thin_gutters" method="post">
    <div class="form-group gutters">
        <div class="col-sm-6">
            <?php
            $options    = html::optionsFromRows('id', 'title', $courses, null, ['value' => '', 'label' => '']);
            $attributes = ['class' => 'ib-combobox', 'id' => 'applications-course', 'data-placeholder' => __('Select a course')];
            $args       = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>', 'arrow_position' => false];
            echo Form::ib_select(null, 'course_id', $options, null, $attributes, $args);
            ?>
        </div>

        <div class="col-xs-12 col-sm-4">
            <?= View::factory('admin/applications/snippets/schedules_dropdown')
                ->set('class', 'refresh_application_reports')
                ->set('id', 'applications-schedules')
                ->set('schedules', $schedules);
            ?>
        </div>
    </div>

    <div class="form-group gutters">
        <div class="col-sm-6">
            <?php
            $attributes = ['class' => 'refresh_application_reports', 'id' => 'applications-daterange'];
            echo Form::ib_daterangepicker('daterange_start', 'daterange_end', $daterange_start, $daterange_end, $attributes);
            ?>
        </div>

        <div class="col-sm-3">
            <?= View::factory('/admin/applications/snippets/status_dropdown')
                ->set('button_text', ucfirst($stage).' status')
                ->set('class',       'refresh_application_reports')
                ->set('statuses',    $status_groups[$stage]['statuses'])
                ->set('id',          'applications-'.$stage.'_statuses');
            ?>
        </div>
    </div>
</form>

<div class="form-row no-gutters" id="applications-reports">
    <div class="col-xs-12 timeoff-reports">
        <?php foreach ($reports as $report): ?>
            <div class="timeoff-report application-report">
                <div class="timeoff-report-top">
                    <p class="timeoff-report-amount application-report-amount"><?= $report['amount'] ?></p>
                    <p class="timeoff-report-text">
                        <span class="timeoff-report-title application-report-text"><?= $report['text'] ?></span>
                    </p>
                </div>

                <div class="timeoff-report-bottom">
                    <div class="timeoff-report-period application-report-period" style="font-size: .75rem;">
                        <?= date('d/M/Y', strtotime($daterange_start)).' - '.date('d/M/Y', strtotime($daterange_end)) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<p id="applications-loading-message"><?= ucfirst($stage) ?>s loading. Please wait...</p>

<div class="clearfix hidden" id="applications-list">
    <?php
    switch ($stage) {
        case 'interview': include 'interviews_datatable.php';   break;
        case 'offer':     include 'offers_datatable.php';       break;
        default:          include 'applications_datatable.php'; break;
    }
    ?>
</div>

<p class="hidden" id="applications-empty-message">There are no <?= $stage ?>s to display.</p>

<?php
if ($stage == 'interview') {
    include 'interviews_edit_modal.php';
}
?>