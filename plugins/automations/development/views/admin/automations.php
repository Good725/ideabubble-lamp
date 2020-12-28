<script>
window.automations = {};
window.automations.actions = <?=json_encode($actions, JSON_PRETTY_PRINT)?>;
window.automations.triggers = <?=json_encode($triggers, JSON_PRETTY_PRINT)?>;
</script>

<button class="btn new" type="button"><?=__('Create new')?></button>

<div id="edit_automation_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            $form = new IbForm('automation_edit_form');
            ?>
            <form id="automation_edit_form" action="/admin/automations/save" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3><?=__('New Automation')?></h3>
            </div>
            <div class="modal-body form-horizontal">
                <?php
                $form->tab_start('Details', true);
                ?>
                <input type="hidden" name="id" value="" />
                <div class="form-group">
                    <label><?=__('Name')?></label>
                    <input type="text" name="name" />
                </div>

                <div class="form-group">
                    <?=Form::ib_checkbox_switch('Published', 'published', 1 , false)?>
                </div>

                <div class="form-group">
                    <label><?=__('Trigger')?></label>
                    <select name="trigger">
                        <option value="">Select</option>
                        <?=html::optionsFromArray(array_combine(array_keys($triggers), array_keys($triggers)), null)?>
                    </select>

                    <div id="trigger_variables" class="hidden">
                        <p>You can use following variables in email subject, message, sms text. Any contact id variable can be used as recipient.</p>
                        <ul>
                            <li>@var1@</li>
                            <li>@var2@</li>
                            <li>@var3@</li>
                        </ul>
                    </div>
                </div>

                <fieldset class="sequence-wrapper col-sm-12">
                    <legend>Sequences</legend>

                    <div class="form-group">
                        <label><?=__('Run Type')?></label>
                        <select id="sequence_run_type_select">
                            <option value="sms">Send an sms</option>
                            <option value="email">Send an email</option>
                            <!-- <option value="action">A predefined action</option> -->
                            <!-- <option value="automation">Another automation</option> -->
                            <option value="create_todo">Create Todo</option>
                        </select>
                        <button class="form-control btn add-sequence" type="button"><?=__('Add Sequence')?></button>
                    </div>

                    <div class="form-group sequence-list">

                    </div>

                    <div class="sequence hidden">
                        <input type="hidden" name="sequence[index][id]" class="sequence_id" />
                        <div class="form-group">
                            <input type="hidden" name="sequence[index][run_type]" class="run_type" />
                        </div>

                        <div class="form-group hidden wait_wrapper">
                            <label><?=__('Wait')?></label>
                            <select class="form-control wait_interval" name="sequence[index][wait_interval]">
                                <option value="">None</option>
                                <option value="">Minute</option>
                                <option value="">Hour</option>
                                <option value="">Day</option>
                            </select>
                            <input class="form-control wait" name="sequence[index][wait]" />
                        </div>

                        <div class="form-group repeat_by_field_wrapper hidden">
                            <?=Form::ib_select('Repeat By Field', 'sequence[index][repeat_by_field]', array('' => 'None'))?>
                        </div>

                        <div class="form-group conditions_wrapper">
                            <label><?=__('Conditions')?>
                                <select name="sequence[index][conditions_mode]" class="form-control conditions-mode">
                                    <option value="AND">AND</option>
                                    <option value="OR">OR</option>
                                </select>
                            </label>
                            <table>
                                <thead>
                                <tr><th>Condition</th><th>Operator</th><th>Value</th><th><button type="button" class="btn add_condition">add condition</button> </th></tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="form-group intervals_wrapper">
                            <label><?=__('Intervals')?></label>
                            <table>
                                <thead>
                                    <tr><th>Type</th><th>Interval</th><th><button type="button" class="btn add_interval">add interval</button> </th></tr>
                                    <tr class="interval hidden">
                                        <td>
                                            <select class="is_periodic" name="interval[sequence][index][is_periodic]">
                                                <option value="1">Schedule</option>
                                                <option value="-1">Immediately</option>
                                                <option value="0">Run Once</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="execute_once_at_datetime date hidden" type="text" name="interval[sequence][index][execute_once_at_datetime]" />
                                            <div class="interval_frequency hidden">
                                                <input type="hidden" class="frequency" name="interval[sequence][index][frequency]"/>
                                                <div class="col-sm-2">Frequency</div>
                                                <div class="col-sm-10">
                                                    <select multiple class="hour">
                                                        <option value="*">Every Hour</option>
                                                        <?php
                                                        for($i = 0;$i < 24;$i++):
                                                            ?>
                                                            <option value="<?=$i;?>"><?=$i;?></option>
                                                            <?php
                                                        endfor;
                                                        ?>
                                                    </select>

                                                    <select multiple class="minute">
                                                        <option value="*">Every Minute</option>
                                                        <?php
                                                        for($i = 0;$i < 60;$i++):
                                                            ?>
                                                            <option value="<?=$i;?>"><?=$i;?></option>
                                                            <?php
                                                        endfor;
                                                        ?>
                                                    </select>
                                                    <select multiple class="month">
                                                        <option value="*">Every Month</option>
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                    <select multiple class="day_of_month">
                                                        <option value="*">Every Day of Month</option>
                                                        <?php
                                                        for($i = 1;$i < 32;$i++):
                                                            ?>
                                                            <option value="<?=$i;?>"><?=$i;?></option>
                                                            <?php
                                                        endfor;
                                                        ?>
                                                    </select>
                                                    <select multiple class="day_of_week">
                                                        <option value="*">Every Day</option>
                                                        <option value="1">Monday</option>
                                                        <option value="2">Tuesday</option>
                                                        <option value="3">Wednesday</option>
                                                        <option value="4">Thursday</option>
                                                        <option value="5">Friday</option>
                                                        <option value="6">Saturday</option>
                                                        <option value="7">Sunday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td><button type="button" class="remove btn">remove</button>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>

                        <div class="form-group hidden action_wrapper">
                            <label><?=__('Action')?></label>
                            <select name="sequence[index][action]" class="automation_action">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="form-group hidden another_automation_wrapper">
                            <label><?=__('Run another automation')?></label>
                            <select name="sequence[index][run_after_automation_id]" class="run_after_automation_id">
                                <option value="">Select</option>
                                <?=html::optionsFromRows('id', 'name', $triggers_actions)?>
                            </select>
                        </div>

                        <div class="hidden message_wrapper">
                            <input type="hidden" name="sequence[index][message_driver]" class="message_driver" />
                            <div class="form-group">
                                <label>Message Template</label>
                                <select class="form-control message_template" name="sequence[index][message_template]">
                                    <option value="">Select</option>
                                    <?=html::optionsFromRows('id', 'name', $message_templates)?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Attachments <button type="button" class="btn add-attachment">Add</button></label>
                                <div class="attachment-list">

                                </div>
                                <div class="attachment_wrapper">
                                    <?=Form::ib_checkbox_switch('Process Docx', 'attachment_process_docx', 1, false, array('class' => 'attachment_process_docx'))?>
                                    <input type="file" name="sequence[index][attachment][attachment_index]" class="attachment" />

                                </div>
                            </div>
                            <div class="form-group">
                                <label>To</label>
                                <input type="text" name="sequence[index][message_to]" class="form-control message_to" />
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="sequence[index][message_subject]" class="form-control message_subject" />
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="sequence[index][message_body]" class="form-control message_body"></textarea>
                            </div>
                        </div>

                        <div class="hidden create_todo_wrapper">
                            <div class="form-group">
                                <?= Form::ib_input('Task Title', 'sequence[index][todo_title]', '', array('class' => 'todo_title'))?>
                            </div>
                            <div class="form-group">
                                <label>Assignee</label>
                                <?= Form::ib_radio('Attendee', 'sequence[index][todo_assignee]', 'attendee', true, array('class' => 'todo_assignee'))?>
                                <?= Form::ib_radio('Contact', 'sequence[index][todo_assignee]', 'contact', false, array('class' => 'todo_assignee'))?>

                                <input type="hidden" name="sequence[index][todo_schedule_id][]" id="sequence_n_todo_schedule_id" class="todo_schedule_id" />
                                <input type="hidden" name="sequence[index][todo_contact_id][]" id="sequence_n_todo_contact_id" class="todo_contact_id" />
                                <?= Form::ib_input(null, 'sequence[index][todo][schedule]', '', array('class' => 'todo_schedule_select', 'id' => 'sequence_n_todo_schedule'))?>
                            </div>
                        </div>

                        <div>
                            <button class="form-control btn remove-sequence" type="button"><?=__('Remove')?></button>
                        </div>
                    </div>
                </fieldset>

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
                $form->tab_start('Test', false, 'automation_test_tab', 'hidden');
                ?>
                <div>
                    <div class="form-group">
                        <?=$form->input('Test for date', 'for_date_test', '', array('id' => 'for_date_test'))?>
                    </div>
                    <div class="form-group">
                        <button type="button" id="test_preview"><?=__('Preview')?></button>
                    </div>
                    <div id="automation_preview" style="overflow: auto; max-width: 100%;"></div>
                </div>

                <?php
                echo $form->tabs();
                ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn submit"><?=__('Save')?></button>
                <a href="#" class="btn" data-dismiss="modal"
                   data-content="Dismiss">Dismiss</a>
            </div>
            </form>
        </div>
    </div>
</div>

<form name="automations" id="automations" method="post">
<table class="table">
    <thead><tr><th><?=__('ID')?></th><th><?=__('Name')?></th><th><?=__('Trigger')?></th><th><?=__('Last Updated')?></th><th><?=__('Last Executed')?></th><th><?=__('Details')?></th><th><?=__('Actions')?></th></tr></thead>
    <tbody>
    <?php
    foreach ($triggers_actions as $trigger_action) {
    ?>
        <tr data-automation_id="<?=$trigger_action['id']?>">
            <td><b><?= $trigger_action['id'] ?></b></td>
            <td><b><?= $trigger_action['name'] ?></b></td>
            <td><?= $trigger_action['trigger'] ?></td>
            <td><?= IbHelpers::formatted_time($trigger_action['updated_date']) ?></td>
            <td><?= IbHelpers::formatted_time($trigger_action['last_executed']) ?></td>
            <td><a class="btn view">view</a></td>
            <td><a class="delete btn" href="/admin/automations/delete?id=<?=$trigger_action['id']?>" >delete</a></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>

    </tfoot>
</table>
</form>
