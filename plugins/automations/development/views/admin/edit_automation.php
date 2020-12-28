<style>
    /* Quick hack for demo */
    #automation-edit-email-attachments .control-label,
    .hide-control-labels .control-label {display: none !important;}

    .trigger_variables {
        font-size: .875em;
    }

    .trigger_variables ul {
        max-height: 365px;
        overflow-y: auto;
    }
</style>
<?php
$form = new IbForm('automation-edit', '/admin/automations/save2/'.$automation->id, 'post', ['layout' => 'vertical']);
$form->name_field = 'name';
$form->cancel_url = '/admin/automations/';
$form->delete_url = '/admin/automations/delete/'.$automation->id;
$form->load_data($automation);

echo $form->start();

$form->tab_start('Details', 'active');
?>
<input type="hidden" name="id" value="<?=$automation->id?>" />
<script>
    window.automations = {};
    window.automations.actions = <?=json_encode($actions, JSON_PRETTY_PRINT)?>;
    window.automations.triggers = <?=json_encode($triggers, JSON_PRETTY_PRINT)?>;
    window.automation_data = <?=json_encode($automation_data)?>;
</script>

<div id="automation-write-email-wrapper" class="hidden">
    <?php
    $write_email = View::factory('popout/write_email')->set(['action_buttons' => false, 'from' => 'phpmail_from_email', 'last_sent_all' => true]);
    /*remove some unwanted elements so it wont break main form*/
    $write_email = str_replace(array('<form class="send-message-form">', '</form>'), '', $write_email);
    $write_email = preg_replace('/\<input type=\"hidden\"[^>]*\/>/', '', $write_email);
    echo $write_email;
    ?>
</div>

<!-- When this happens... -->
    <?php ob_start(); ?>

    <div class="row gutters vertically_center">
        <div class="col-sm-3">
            <label for="automation-edit-trigger">Choose trigger event</label>
        </div>

        <div class="col-sm-5">
            <?php
            $options = ['' => '-- Please select'] + $trigger_options;
            echo Form::ib_select(null, 'trigger', $options, $automation->trigger, ['class' => 'set_title open_next']);
            ?>
        </div>
    </div>

    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'id'        => 'automation-edit-select_trigger',
        'collapsed' => false,
        'title'     => ['html' => '1. When <span class="panel-has_subtitle-hidden">this happens...</span>'],
        'subtitle'  => true,
        'body'      => ob_get_clean()
    ]);
    ?>

<!-- Do this... -->
    <?php ob_start(); ?>
    <div class="row gutters">
        <div class="col-sm-5">
            <?php
            $options = [
                'alert' => 'Send an Alert',
                'sms' => 'Send an SMS',
                'email' => 'Send an email',
                'create_todo' => 'Create a task'
            ];
            echo Form::ib_select(null, 'action', $options, null, ['class' => 'set_title', 'id' => 'automations-edit-action'], ['please_select' => true]) ?>
        </div>

        <div class="col-sm-3">
            <button type="button" class="btn btn-primary form-btn w-100" id="automation-edit-add-sequence-btn">Add action</button>
        </div>
    </div>

    <div class="mt-3 hidden automation-edit-action-edit" id="automation-edit-action-edit-sms">
        <?php ob_start(); ?>
        <div class="row gutters">
            <div class="col-sm-9">
                <div class="automation-write-sms">
                    <input type="hidden" class="message_driver" name="sequence[index][message_driver]" value="sms" />
                    <input type="hidden" class="message_from" name="sequence[index][message_from]" value="" />
                    <input type="hidden" class="message_template_id" name="sequence[index][message_from]" value="" />
                    <input type="hidden" class="message_subject" name="sequence[index][message_subject]" value="" />

                    <?php
                    $write_sms = View::factory('popout/write_sms')->set(['action_buttons' => false]);
                    /*remove some unwanted elements so it wont break main form*/
                    $write_sms = str_replace(array('<form class="send-message-form">', '</form>'), '', $write_sms);
                    $write_sms = preg_replace('/\<input type=\"hidden\"[^>]*\/>/', '', $write_sms);
                    $write_sms = str_replace('name="sms[message]"', 'class="message_body" name="sequence[index][message_body]"', $write_sms);

                    echo $write_sms;
                    ?>
                </div>
            </div>

            <div class="col-sm-3 trigger_variables">
                You can use the following variables in your sms:
                <ul></ul>
            </div>
        </div>

        <?php
        $body = ob_get_clean();
        echo View::factory('snippets/panel_collapse')->set([
            'class'     => 'mb-0',
            'id'        => 'automation-edit-sms-edit',
            'collapsed' => true,
            'title'     => 'Customise SMS',
            'subtitle'  => false,
            'body'      => $body
        ]);
        ?>
    </div>

    <div class="mt-3 hidden automation-edit-action-edit" id="automation-edit-action-edit-alert">
        <?php ob_start(); ?>
        <div class="row gutters">
            <div class="col-sm-9">
                <div class="automation-write-alert">
                    <input type="hidden" class="message_driver" name="sequence[index][message_driver]" value="dashboard" />
                    <input type="hidden" class="message_from" name="sequence[index][message_from]" value="" />
                    <input type="hidden" class="message_template_id" name="sequence[index][message_from]" value="" />
                    <input type="hidden" class="message_subject" name="sequence[index][message_subject]" value="" />

                    <?php
                    $write_sms = View::factory('popout/write_sms')->set(['action_buttons' => false]);
                    /*remove some unwanted elements so it wont break main form*/
                    $write_sms = str_replace(array('<form class="send-message-form">', '</form>'), '', $write_sms);
                    $write_sms = preg_replace('/\<input type=\"hidden\"[^>]*\/>/', '', $write_sms);
                    $write_sms = str_replace('name="sms[message]"', 'class="message_body" name="sequence[index][message_body]"', $write_sms);

                    echo $write_sms;
                    ?>
                </div>
            </div>

            <div class="col-sm-3 trigger_variables">
                You can use the following variables in your alert:
                <ul></ul>
            </div>
        </div>

        <?php
        $body = ob_get_clean();
        echo View::factory('snippets/panel_collapse')->set([
            'class'     => 'mb-0',
            'id'        => 'automation-edit-alert-edit',
            'collapsed' => true,
            'title'     => 'Customise Alert',
            'subtitle'  => false,
            'body'      => $body
        ]);
        ?>
    </div>

    <?php ob_start(); ?>
    <div class="row gutters">
        <div class="col-sm-9">
            <div class="automation-write-email">
                <input type="hidden" class="message_driver" name="sequence[index][message_driver]" value="email" />
                <input type="hidden" class="message_from" name="sequence[index][message_from]" value="" />
                <input type="hidden" class="message_template_id" name="sequence[index][message_from]" value="" />
                <input type="hidden" class="message_subject" name="sequence[index][message_subject]" value="" />
                <input type="hidden" class="message_body" name="sequence[index][message_body]" value="" />
                <!--
                <input type="hidden" class="message_recipient_target" name="sequence[index][recipient][recipient_index][target]" value="" />
                <input type="hidden" class="message_recipient_x_details" name="sequence[index][recipient][recipient_index][x_details]" value="" />
                <input type="hidden" class="message_recipient_target_type" name="sequence[index][recipient][recipient_index][target_type]" value="" />
                -->
            </div>
        </div>

        <div class="col-sm-3 trigger_variables">
            You can use the following variables in your email:
            <ul></ul>
        </div>
    </div>

    <?php ob_start(); ?>
    <ul class="list-unstyled">
        <li><?= Form::ib_radio('None',                           'attachment_option', '', true, ['class' => 'attachment_option'])           ?></li>
        <li>
            <?= Form::ib_radio('Upload your file',               'attachment_option', 'upload', false, ['class' => 'attachment_option'])           ?>
            <span class="uploaded_file_wrapper"></span>
        </li>
        <li><?= Form::ib_radio('Generate and attach',            'attachment_option', 'generate_attach', false, ['class' => 'attachment_option'])  ?></li>
        <li><?= Form::ib_radio('Generate and publish to portal', 'attachment_option', 'generate_publish', false, ['class' => 'attachment_option']) ?></li>
    </ul>

    <div class="row gutters hidden automation-edit-attachment-upload" id="automation-edit-attachment-upload">
        <?php
        $form->input_size_small = 'col-sm-9';
        echo $form->image_uploader(null, 'attachment_file', null, [], ['onsuccess' => 'automation_image_uploaded', 'presetmodal' => 'no']);
        $form->input_size_small = 'col-sm-12';
        ?>
    </div>

    <div class="row gutters hidden automation-edit-attachment-generate" id="automation-edit-attachment-generate">
        <div class="col-sm-7">
            <?= $form->select(null, 'attachment_template', html::optionsFromRows('id', 'name', $docx_templates), null, ['class' => 'attachment_template']) ?>
        </div>
    </div>
    <input type="hidden" name="attachment_process_docx" class="attachment_process_docx" value="0" />
    <input type="hidden" name="attachment_convert_docx_to_pdf" class="attachment_convert_docx_to_pdf" value="0" />
    <input type="hidden" name="attachment_share" class="attachment_share" value="0" />

    <?php $add_attachments_html = ob_get_clean(); ?>

    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'class'     => 'automation-edit-email-attachments hidden',
        'id'        => 'automation-edit-email-attachments',
        'collapsed' => true,
        'title'     => 'Add email attachments',
        'subtitle'  => false,
        'body'      => $add_attachments_html
    ]);
    ?>

    <?php $body = ob_get_clean(); ?>

    <div class="automation-edit-action-edit mt-3 hidden" id="automation-edit-action-edit-email">
        <?php
        echo View::factory('snippets/panel_collapse')->set([
            'class'     => 'mb-0',
            'id'        => 'automation-edit-email-edit',
            'collapsed' => true,
            'title'     => ['html' => '<span class="section-number">2.1.</span> Send email ' . '<button type="button" class="remove-sequence btn right mr-3" style="margin-top: -.375em;">Remove</button>'],
            'subtitle'  => false,
            'body'      => $body
        ]);
        ?>
    </div>

    <div class="automation-edit-action-predefined_function mt-3 hidden" id="automation-edit-action-edit-predefined_function">
        <?php
        echo View::factory('snippets/panel_collapse')->set([
            'class'     => 'mb-0',
            'id'        => 'automation-edit-predefined_function-edit',
            'collapsed' => true,
            'title'     => ['html' => '<span class="section-number">2.1.</span> <span class="run_function"></span>' . '<button type="button" class="remove-sequence btn right mr-3" style="margin-top: -.375em;">Remove</button>'],
            'subtitle'  => false,
            'body'      => $body
        ]);
        ?>
    </div>

    <div id="automation-edit-sequence-list"></div>

    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'id'        => 'automation-edit-do_this',
        'collapsed' => true,
        'title'     => ['html' => '<span class="section-number">2.</span> <span class="panel-has_subtitle-hidden">Do this...</span>'],
        'subtitle'  => true,
        'body'      => ob_get_clean()
    ]);
    ?>

    <div class="automation-edit-sequence">
        <input type="hidden" class="sequence_id" name="sequence[index][id]" value="" />
        <input type="hidden" class="run_type" name="sequence[index][run_type]" value="" />
        <input type="hidden" class="action" name="sequence[index][action]" value="" />
    </div>

    <!-- At this moment... -->
    <?php ob_start(); ?>
    <div class="form-row gutters">
        <div class="col-sm-5 interval_when_wrapper">
            <?php
            $options = ['immediately' => __('Immediately'), 'scheduled' => __('Scheduled')];
            echo Form::btn_options(
                'when',
                $options,
                null,
                false,
                ['class' => 'set_title interval_when'],
                ['class' => 'stay_inline']
            );
            ?>
        </div>
    </div>

    <div class="hidden automation-edit-interval-schedule-edit">
        <input type="hidden" class="interval_frequency" value="" />
        <input type="hidden" class="interval_execute_once_at_datetime" value="" />
        <input type="radio" class="is_periodic hidden" value="-1" />
        <div class="form-row gutters">
            <div class="col-xs-2 col-sm-1 text-right">
                <div style="margin-top: 2.25rem;"><?php // Sorry. Magic number. There are better ways of aligning this. ?>
                    <?= Form::ib_radio(null, 'is_periodic', '0', false, ['class' => 'is_periodic']) ?>
                </div>
            </div>

            <div class="col-xs-10 col-sm-4">
                <?= $form->datepicker('Date', 'scheduled_date', null,  ['class' => 'interval_date']) ?>
            </div>

            <div class="col-xs-offset-2 col-xs-10 col-sm-offset-0 col-sm-4">
                <?= $form->timepicker('Time', 'scheduled_time', null,  ['class' => 'interval_time']) ?>
            </div>
        </div>

        <div class="form-row gutters">
            <div class="col-xs-2 col-sm-1 text-right">
                <div style="margin-top: 2.25rem;">
                    <?= Form::ib_radio(null, 'is_periodic', '1', false, ['class' => 'is_periodic']) ?>
                </div>
            </div>

            <div class="col-xs-10 col-sm-3 hidden"><!-- move relative dates to conditions -->
                <?= $form->numeric_input('Interval', '', null,   ['class' => 'interval_amount']) ?>
            </div>

            <div class="col-xs-offset-2 col-xs-10 col-sm-offset-0 col-sm-6">
                <?php
                $options = ['minute' => __('Run every minute'), 'hour' => __('Run every hour'), 'day' => __('Run every day'), 'week' => __('Run every week'), 'month' => __('Run every month')];
                echo $form->select(null, '', $options, null, ['class' => 'interval_type']);
                ?>

                <div class="col-xs-offset-2 col-xs-10 col-sm-offset-0 col-sm-3">
                    <div style="margin-top: 2.25rem;">
                        <?= Form::ib_checkbox('Don\'t repeat', null, 1, false, array('class' => 'do_not_repeat')) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-row gutters hide-control-labels hidden">
            <div class="col-xs-offset-2 col-xs-10 col-sm-offset-1 col-sm-3">
                <?php
                $options = ['before' => __('Before'), 'after' => __('After')];
                echo $form->select(null, '', $options, null, ['class' => 'interval_execute']);
                ?>
            </div>

            <div class="col-xs-offset-2 col-xs-10 col-sm-offset-0 col-sm-3">
                <?php
                $options = ['=' => '=', '<=' => '<=', '>=' => '>='];
                echo $form->select(null, '', $options, null, ['class' => 'interval_operator']);
                ?>
            </div>

            <div class="col-xs-offset-2 col-xs-10 col-sm-offset-0 col-sm-4">
                <?php
                $options = [];
                echo $form->select(null, '', $options, null, ['class' => 'interval_relative_field']);
                ?>
            </div>
        </div>

        <div class="hidden automation-frequency-rows">
            <h4>Frequency</h4>

            <!-- Hour -->
            <div class="automation-frequency-row form-row gutters vertically_center mb-0 hidden" data-show_for="['hour']">
                <div class="col-sm-4">
                    <label>Run this every hour on this minute</label>
                </div>

                <div class="col-sm-3">
                    <?php
                    $options = [];
                    for ($i = 0; $i < 60; $i++) {
                        $options[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
                    }
                    echo $form->select('Minute', '', $options, null, ['class' => 'frequency_hour_minute']);
                    ?>
                </div>
            </div>

            <!-- Day -->
            <div class="automation-frequency-row form-row gutters vertically_center mb-0 hidden" data-show_for="['day', 'week', 'month']">
                <div class="col-sm-4">
                    <label>Run this every day at this time</label>
                </div>

                <div class="col-sm-3">
                    <?= $form->timepicker('Time', '', null,  ['class' => 'frequency_day_time']) ?>
                </div>
            </div>

            <!-- Week -->
            <div class="automation-frequency-row form-row gutters vertically_center mb-0 hidden" data-show_for="['week', 'month']">
                <div class="col-sm-4">
                    <label>Run this every week on</label>
                </div>

                <div class="col-sm-3">
                    <?php
                    $options = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    echo $form->select('Day', '', $options, null,  ['class' => 'frequency_day_of_week']);
                    ?>
                </div>
            </div>

            <!-- Month on date -->
            <div class="automation-frequency-row form-row gutters vertically_center mb-0 hidden" data-show_for="['month']">
                <div class="col-sm-4">
                    <label>Run this every month on</label>
                </div>

                <div class="col-sm-3">
                    <?php
                    $options = [];
                    for ($i = 1; $i <= 31; $i++) {
                        $options[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
                    }
                    echo $form->select('Day of month', '', $options, null,  ['class' => 'frequency_day_of_month']);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'id'        => 'automation-edit-at_this_moment',
        'class'        => 'automation-edit-at_this_moment mb-0',
        'collapsed' => true,
        'title'     => ['html' => '<span class="section-number">2.2</span> At this moment...'],
        'subtitle'  => true,
        'body'      => ob_get_clean()
    ]);
    ?>

    <!-- If [select] conditions are met... -->
    <?php  $select = Form::select(null, ['AND' => __('All'), 'OR' => __('Any')], 'AND', ['class' => 'conditions_mode']); ?>

    <?php ob_start() ?>

    <!-- what is this for? double add condition button?
    <div class="mb-3 text-center">
        <button
            type="button" class="btn btn-primary form-btn"
            data-toggle="collapse" data-target="#automation-edit-condition-add"
            aria-expanded="false" aria-controls="automation-edit-condition-add"
            style="min-width: 200px;"
        >Add condition</button>
    </div> -->

    <div class="automation-edit-conditions-list"></div>

    <div class="border p-3 mx-auto automation-edit-condition-add">
        <div class="row gutters">
            <div class="col-sm-8">
                <?php
                $options = [
                    '' => '-- Please select--',
                ];
                echo Form::ib_select(null, null, $options, null, ['class' => 'automation-edit-condition-add-type']);
                ?>
            </div>

            <div class="col-sm-4">
                <button type="button" class="btn btn-primary form-btn w-100 automation-edit-condition-add-btn">Add condition</button>
            </div>
        </div>
    </div>

    <div class="automation-edit-condition automations-edit-condition-template hidden" data-type="course_type">
        <div class="row no-gutters vertically_center mb-3">
            <div class="col-xs-10 col-sm-11">
                <div class="border p-3">
                    <div class="row gutters vertically_center">
                        <div class="col-sm-4">
                            <?= Form::ib_select(null, null, [], null, ['class' => 'condition_field']) ?>
                        </div>

                        <div class="col-sm-3">
                            <?php
                            echo Form::ib_select(null, null, [], null, ['class' => 'condition_operator']);
                            ?>
                        </div>

                        <div class="col-sm-5">
                            <?php
                            // Select list template
                            $attributes = ['multiple' => 'multiple', 'class' => 'condition_value condition_value--select'];
                            echo Form::ib_select(null, null, [], null, $attributes);

                            // Input template
                            $attributes = ['class' => 'condition_value condition_value--input hidden', 'disabled' => 'disabled'];
                            echo Form::ib_input(null, null, null, $attributes);

                            // Percentage template
                            $attributes = ['type' => 'number', 'class' => 'condition_value condition_value--percentage hidden', 'disabled' => 'disabled'];
                            echo Form::ib_input(null, null, null, $attributes, ['right_icon' => '%']);

                            // Datepicker template
                            $attributes = ['class' => 'contition_value condition_value--datepicker', 'disabled' => 'disabled'];
                            echo Form::ib_datepicker(null, null, null, $attributes);
                            ?>
                            <div class="hidden contition_value contition_value--relative-date">
                                <!-- <input type="hidden" class="condition_value--relative_date" /> -->
                                <?php
                                $attributes = ['class' => 'relative-date-interval-amount col-xs-2', 'disabled' => 'disabled'];
                                echo Form::ib_input(null, null, null, $attributes);
                                ?>
                                <select class="relative-date-interval-type form-control">
                                    <option value=""><?=__('Please select')?></option>
                                    <option value="minute"><?=__('Minutes')?></option>
                                    <option value="hour"><?=__('Hours')?></option>
                                    <option value="day"><?=__('Days')?></option>
                                    <option value="week"><?=__('Weeks')?></option>
                                    <option value="month"><?=__('Months')?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-2 col-sm-1 text-center">
                <button type="button" class="btn-link text-decoration-none automation-condition-remove">
                    <span class="icon_close" style="font-size: 2rem;"></span>
                </button>
            </div>
        </div>
    </div>


    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'id'        => 'automation-edit-conditions_are_met',
        'class'        => 'automation-edit-conditions_are_met',
        'collapsed' => true,
        'title'     => ['html' => '<span class="section-number">2.1</span> If... '.$select.' of the following conditions are met'],
        'subtitle'  => true,
        'body'      => ob_get_clean()
    ]);
    ?>

    <?php ob_start() ?>

    <div class="border p-3">
        <div class="row gutters vertically_center">
            <?= Form::ib_input('Task Title', 'sequence[index][todo_title]', '', array('class' => 'todo_title'))?>
        </div>
        <div class="row gutters vertically_center">
            <div class="col-xs-10 col-sm-3">
                <label>Assignee</label>
            </div>
            <div class="col-xs-10 col-sm-9">
                <?= Form::ib_radio('Attendee', 'sequence[index][todo_assignee]', 'Attendee', true, array('class' => 'todo_assignee'))?>
                <?= Form::ib_radio('Trainer', 'sequence[index][todo_assignee]', 'Trainer', true, array('class' => 'todo_assignee'))?>
                <?= Form::ib_radio('Contact', 'sequence[index][todo_assignee]', 'Contact', false, array('class' => 'todo_assignee'))?>
            </div>
        </div>
        <div class="row gutters vertically_center">
            <div class="col-xs-10 col-sm-6">
            <?php
            $attributes = ['multiple' => 'multiple', 'class' => 'todo_contact_id  hidden'];
            echo Form::ib_select(null, null, html::optionsFromRows('id', 'fullname', $contacts), null, $attributes);
            ?>
            <?php
            $attributes = ['multiple' => 'multiple', 'class' => 'todo_schedule_id hidden'];

            echo Form::ib_select(null, null, html::optionsFromRows('id', 'name', $schedules), null, $attributes);
            ?>
            </div>
        </div>
    </div>

    <?php
    $todo_body = ob_get_clean()
    ?>

    <div class="automation-edit-action-edit mt-3 hidden" id="automation-edit-action-edit-todo">
    <?php
    echo View::factory('snippets/panel_collapse')->set([
        'id'        => 'automation-edit-todo-edit',
        'class'        => 'mb-0',
        'collapsed' => true,
        'title'     => ['html' => '<span class="section-number">2.1.</span> Create a Task ' . '<button type="button" class="remove-sequence btn right mr-3" style="margin-top: -.375em;">Remove</button>'],
        'subtitle'  => true,
        'body'      => $todo_body
    ]);
    ?>
    </div>
<?php
$form->tab_start('Activity');
?>
<div>
    <table class="table" id="automation_activity">
        <thead>
            <tr>
                <th><?=__('Date Time')?></th>
                <th><?=__('Link')?></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<?php
if (Auth::instance()->has_access('automations_test'))
if ($automation->id > 0) {
    $form->tab_start('Test', false, 'automation_test_tab', '');
?>
<div>
    <div class="form-group">
        <?= $form->input('Test for date', 'for_date_test', '', array('id' => 'for_date_test')) ?>
    </div>
    <div class="form-group">
        <button type="button" id="test_preview" class="btn"><?= __('Preview') ?></button>
    </div>
    <div id="automation_preview" style="overflow: auto; max-width: 100%;"></div>
</div>
<?php
}

echo $form->tabs();
echo $form->action_buttons();
echo $form->end();
?>

<script>
    // When a "set title" field is changed, update the panel title to match the value of the input
    $(document).on('change', '.set_title', function() {

        let text = $(this).find(':selected').html();

        text = text || this.value.charAt(0).toUpperCase() + this.value.slice(1);

        $(this)
            .closest('.panel').addClass('panel-has_subtitle')
            .find('.panel-subtitle').html(text);
    });

    // When an "open next" field is changed, collapse the current panel and open the next one.
    $('.open_next').on('change', function() {
        $(this).parents('.panel-body').collapse('hide');
        $(this).parents('.panel').find('\+ .panel > .panel-body').collapse('show');
    });

    if (0) {
        // When the "when" input is changed to "scheduled", show the "scheduled" form.
        $('[name="when"]').change(function () {
            const selected = $('[name="when"]:checked').val();
            $('#automation-edit-interval-schedule-edit').toggleClass('hidden', selected != 'scheduled');
        });

        // Add a new condition
        $('#automation-edit-condition-add-btn').click(function () {
            const type = $('#automation-edit-condition-add-type').val();
            const $clone = $('.automations-edit-condition-template[data-type="' + type + '"]').clone();

            $clone.removeClass('automations-edit-condition-template').removeClass('hidden');

            $clone.find('select[multiple]').multiselect({
                'enableCaseInsensitiveFiltering': true,
                'enableFiltering': true,
                'includeSelectAllOption': true,
                'maxHeight': 460,
                'selectAllText': 'ALL'
            });

            $('#automation-edit-conditions-list').append($clone);

            $('[data-target="#automation-edit-condition-add"]').click();
        });

        // Remove existing condition
        $('#automation-edit-conditions-list').on('click', '.automation-condition-remove', function () {
            $(this).parents('.automation-edit-condition').remove();
        });
    }
</script>
