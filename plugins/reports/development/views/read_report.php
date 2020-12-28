<div class="alert-area" id="edit_report_alert_area"><?= (isset($alert)) ? $alert : ''; ?></div>

<div class="form-horizontal">
    <input type="hidden" id="id" name="id" value="<?= $report->get_id() ?>" />
    <input type="hidden" id="autoload_report" value="<?= $report->get_autoload() ?>" />

    <div>
        <div class="row gutters" id="temporary_parameters"></div>
        <?php if($report->get_autosum() || $report->get_action_button()): ?>
            <div class="clearfix"></div>

            <div class="form-group padd-top-bottom20">
                <?php if($report->get_autosum()): ?>
                    <label class="col-sm-2 control-label" for="prependedInput">Total:</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon">&euro;</span>
                            <input class="form-control total_settlement" id="prependedInput" type="text" readonly />
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-sm-2">
                    <?php if($report->get_action_button()): ?>
                        <input type="button"
                               data-event="<?=htmlspecialchars($report->get_action_event()); ?>"
                               value="<?=$report->get_action_button_label(); ?>"
                               class="btn btn-primary" onclick="this.disabled = true; invoke_custom_script(this); this.disabled = false;" />
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($report->get_generate_documents() == 1 ): ?>
            <?php if (method_exists('Model_Files','getDirectoryTree')): ?>
                <button
                    type="button" class="btn"
                    id="generate_document_no_print_zip"
                    <?= $report->get_generate_documents() == 1 ? '' : 'disabled="disabled"' ?>
                    title="Generates report, all Parameters must be set, will generate the displayed records">Generate Documents Zip</button>

                <button
                    type="button" class="btn"
                    id="generate_document_no_print"
                    <?= $report->get_generate_documents() == 1 ? '' : 'disabled="disabled"' ?>
                    title="Generates report, all Parameters must be set, will generate the displayed records">Generate Documents</button>

                <button type="button" class="btn"
                    id="generate_document"
                    <?= $report->get_generate_documents() == 1 ? '' : 'disabled="disabled"' ?>
                    title="Prints report, all Parameters must be set, will print the displayed records">Print Documents</button>
            <?php endif; ?>
        <?php endif; ?>

        <p id="generate_document_result"></p>

        <div class="report_table_wrapper">
            <?php if ($report->get_show_results_counter()): ?>
                <p class="hidden" id="report_table-records_found">
                    <?= $report->get_results_counter_text() ? $report->get_results_counter_text() : 'Records found' ?>:
                    <span id="report_table-records_found-amount"></span>
                </p>
            <?php endif; ?>

            <table id="report_table" class="table report_datatable"></table>
        </div>
    </div>
    <div id="chart_<?php echo $chart->get_id() ?>"></div>
    <div id="widget_<?php echo $report->get_widget_id() ?>"></div>

    <input type="hidden" name="parameter_fields" id="parameter_fields" value="" />

    <div class="hidden" id="parameter_area">
        <?php
        $parameters = Model_Parameter::get_all_parameters($report->get_id());
        foreach ($parameters as $parameter) {
            $parameter = new Model_Parameter($parameter['id']);
            echo View::factory('add_edit_parameter')->bind('parameter',$parameter);
        }
        ?>
    </div>
</div>

<form action="/admin/reports/export_report_as_csv/<?= $report->get_id() ?>" id="csv_form" method="POST">
    <input type="hidden" id="csv_sql" name="csv_sql"/>
    <input type="hidden" id="csv_parameters" name="csv_parameters"/>
</form>


<?php // SQL and report id is needed for the downloading of a CSV and printing of a report?>
<div class="report_information hidden">
    <textarea name="sql" id="sql" <?= (isset($_SESSION['admin_user']) ? 'active' : 'readonly'); ?>><?= $report->get_sql(); ?></textarea>
    <input type="hidden" name="report_id" id="report_id" value="<?= $report->get_id(); ?>"/>
    <?php // Custom report rules to be executed after report is executed ?>
    <textarea name="custom_report_rules"
              id="custom_report_rules"><?= $report->get_custom_report_rules(); ?></textarea>
    <textarea class="form-control" id="action_event" name="action_event"><?= $report->get_action_event(); ?></textarea>
</div>
<div class="hidden" id="report-parameter-template-date">
    <?= Form::ib_input(
        null,
        null,
        null,
        ['class' => 'temporary_value datepicker input_date'],
        ['icon' => '<span class="flaticon-calendar-1"></span>', 'icon_position' => 'right']
    ); ?>
</div>

<div class="hidden" id="report-parameter-template-select">
    <?= Form::ib_select(null, null, array(), null, array('class' => 'temporary_value value_input input_select')); ?>
</div>

<div class="form-group">
    <div class="col-sm-12 form-actions form-action-group">
        <button type="button" class="btn btn-primary" id="generate_report">Run Report</button>
    </div>
</div>

<style>
    table.table td a {display:inline;} <?php // The rule should really be taken out of the stylish.css, rather than overwritten here ?>
</style>