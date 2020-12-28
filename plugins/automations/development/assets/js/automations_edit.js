var InitiatorType = {
    USER : 'USER',
    CRON : 'CRON'
};

$(document).on("ready", function(){
    var $automation_write_email_wrapper = $("#automation-write-email-wrapper");
    $automation_write_email_wrapper.remove(); // email editor can have only once instance in the page. it will be moved to each email sequence and then values will be copied
    $automation_write_email_wrapper.copy_values = function(){
        var $parent = $automation_write_email_wrapper.parents(".automation-write-email");
        $parent.find(".message_from").val($automation_write_email_wrapper.find("[name='email[from]']").val());
        $parent.find(".message_template_id").val($automation_write_email_wrapper.find("[name='message_template_select']").val());
        $parent.find(".message_subject").val($automation_write_email_wrapper.find("[name='email[subject]']").val());
        $parent.find(".message_body").val($automation_write_email_wrapper.find("[name='email[message]']").val());

        $parent.find(".recipient_type, .recipient_target, .recipient_x_details").remove();
        $automation_write_email_wrapper.find(".messaging-recipient").each(function(){
            $parent.append('<input type="hidden" class="recipient_type" value="' + $(this).find("[name='email_recipient[pid][]']").val() + '" />');
            $parent.append('<input type="hidden" class="recipient_target" value="' + $(this).find("[name='email_recipient[id][]']").val() + '" />');
            $parent.append('<input type="hidden" class="recipient_x_details" value="' + $(this).find("[name='email_recipient[x_details][]']").val() + '" />');
        });
        try {
            $parent.find(".message_body").val(CKEDITOR.instances["messaging-sidebar-email-message"].getData());
        } catch (exc) {

        }
        set_sequence_message_recipients_index();
    };
    $automation_write_email_wrapper.reset_inputs = function() {
        var $parent = $automation_write_email_wrapper.parents(".automation-write-email");
        $automation_write_email_wrapper.find("[name='email[from]']").val($parent.find(".message_from").val());
        var $from = $automation_write_email_wrapper.find("[name='email[from]']");
        if ($from.val() == null || $from.val() == ""){
            $from.find("option").first().prop("selected", true);
        }
        $automation_write_email_wrapper.find("[name='message_template_select']").val($parent.find(".message_template_id").val());
        $automation_write_email_wrapper.find("[name='email[subject]']").val($parent.find(".message_subject").val());
        $automation_write_email_wrapper.find("[name='email[message]']").val($parent.find(".message_body").val());
        $automation_write_email_wrapper.find(".messaging-recipient").remove();
        $parent.find(".recipient_target").each(function(){
            messaging_target_add(
                $automation_write_email_wrapper.find('.contact-list-labels'),
                "email_recipient",
                {value: this.value, label: this.value, category: "TAG"},
                {x_details: "to"}
            );
        });
        var $send_email_to = $automation_write_email_wrapper.find('.send-email-to');
        try {
            $send_email_to.autocomplete('destroy');
        } catch (exc) {

        }
        $send_email_to.autocomplete({
            source: function (data, callback) {
                var term = $send_email_to.val();
                data.driver = 'email';
                $.get('/admin/messaging/to_autocomplete', data, function (response) {
                    var trigger = automations.triggers[$("[name=trigger]").val()];
                    var options = [];
                    for (var i in trigger.generated_message_params) {
                        if (trigger.generated_message_params[i].indexOf(term) != -1) {
                            options.push(
                                {
                                    category: 'TAG',
                                    label: trigger.generated_message_params[i],
                                    value: trigger.generated_message_params[i]
                                }
                            );
                        }
                    }
                    for (var i in response) {
                        options.push(response[i]);
                    }
                    console.log(response);
                    callback(options);
                    $(".ui-helper-hidden-accessible").addClass("sr-only");
                    $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
                });
            },
            select: function (event, ui) {
                event.preventDefault();
                messaging_target_add($(this).find('\+.contact-list-labels'), "email_recipient", ui.item, {x_details: "to"});
                this.value = '';
            }
        });

        try {
            CKEDITOR.instances["messaging-sidebar-email-message"].destroy();
        } catch (exc) {

        }
        CKEDITOR.replace($automation_write_email_wrapper.find(".ckeditor-email")[0] , {
            toolbar :
                [
                    [
                        'Format', '-',
                        'Bold', 'Italic', 'Underline', 'TextColor', 'RemoveFormat', '-',
                        'NumberedList', 'BulletedList', 'Outdent', 'Indent',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight'
                    ],
                    [
                        'Link', 'Unlink'
                    ]
                ],
            height : '150px'
        });
        CKEDITOR.instances["messaging-sidebar-email-message"].setData($parent.find(".message_body").val());
        set_sequence_message_recipients_index();
    };


    var $condition_template = $(".automations-edit-condition-template");
    $condition_template.remove();
    var $edit_email_template = $("#automation-edit-action-edit-email");
    $edit_email_template.remove();
    var $edit_sms_template = $("#automation-edit-action-edit-sms");
    $edit_sms_template.remove();
    var $edit_alert_template = $("#automation-edit-action-edit-alert");
    $edit_alert_template.remove();
    var $edit_todo_template = $("#automation-edit-action-edit-todo");
    $edit_todo_template.remove();
    var $edit_predefined_function_template = $("#automation-edit-action-edit-predefined_function");
    $edit_predefined_function_template.remove();
    var $interval_template = $(".automation-edit-at_this_moment");
    $interval_template.remove();
    var $conditions_template = $(".automation-edit-conditions_are_met");
    $conditions_template.remove();

    var $sequence_wrapper_template = $(".automation-edit-sequence");
    $sequence_wrapper_template.remove();

    function sequence_panel_heading_click_handler()
    {
        var $parent = $(this).parent().parent();
        if ($parent.find(".automation-write-email").length > 0) {
            $automation_write_email_wrapper.copy_values();
            $automation_write_email_wrapper.remove();
            $automation_write_email_wrapper.removeClass("hidden");
            $parent.find(".automation-write-email").append($automation_write_email_wrapper);
            $automation_write_email_wrapper.reset_inputs();
        }
    }

    $(".automation-sequence > .panel > .panel-heading").on("click", sequence_panel_heading_click_handler);
    function set_sequence_index()
    {
        var $sequences = $(".automation-edit-sequence");
        var index = 0;
        $sequences.each(function(){
            $(this).data("index", index);
            $(this).find(".sequence_id").attr("name", "sequence[" + index + "][id]");
            $(this).find(".repeat_by_field_wrapper select").attr("name", "sequence[" + index + "][repeat_by_field]");
            $(this).find(".run_type").attr("name", "sequence[" + index + "][run_type]");
            $(this).find(".conditions_mode").attr("name", "sequence[" + index + "][conditions_mode]");
            $(this).find(".action").attr("name", "sequence[" + index + "][action]");
            $(this).find(".wait_interval").attr("name", "sequence[" + index + "][wait_interval]");
            $(this).find(".wait").attr("name", "sequence[" + index + "][wait]");
            $(this).find(".run_after_automation_id").attr("name", "sequence[" + index + "][run_after_automation_id]");

            $(this).find(".message_driver").attr("name", "sequence[" + index + "][message_driver]");
            $(this).find(".message_template").attr("name", "sequence[" + index + "][message_template]");
            $(this).find(".attachment").attr("name", "sequence[" + index + "][attachment]");
            $(this).find(".message_to").attr("name", "sequence[" + index + "][message_to]");
            $(this).find(".message_subject").attr("name", "sequence[" + index + "][message_subject]");
            $(this).find(".message_body").attr("name", "sequence[" + index + "][message_body]");

            $(this).find(".todo_title").attr("name", "sequence[" + index + "][todo_title]");
            $(this).find(".todo_assignee").attr("name", "sequence[" + index + "][todo_assignee]");
            $(this).find(".todo_contact_id").attr("name", "sequence[" + index + "][todo_contact_id][]");
            $(this).find(".todo_schedule_id").attr("name", "sequence[" + index + "][todo_schedule_id][]");

            $(this).find(".attachment_option").attr("name", "sequence[" + index + "][attachment][0][attachment_option]");
            $(this).find(".attachment_template").attr("name", "sequence[" + index + "][attachment][0][template_id]");
            $(this).find(".attachment_process_docx").attr("name", "sequence[" + index + "][attachment][0][process_docx]");
            $(this).find(".attachment_convert_docx_to_pdf").attr("name", "sequence[" + index + "][attachment][0][convert_docx_to_pdf]");
            $(this).find(".attachment_share").attr("name", "sequence[" + index + "][attachment][0][share]");
            $(this).find(".attachment_file_id").attr("name", "sequence[" + index + "][attachment][0][file_id]");

            ++index;
        });
        set_sequence_conditions_index();
        set_sequence_message_recipients_index();
        set_sequence_intervals_index();
    }

    function set_condition_fields($condition_wrapper, data)
    {
        var trigger = automations.triggers[$("#automation-edit [name=trigger]").val()];
        if (!trigger){
            return;
        }

        var $select = $(".automation-edit-condition-add-type");
        var options = '<option value="">-- Please select --</option>';
        for (var i in trigger.filters) {
            options += '<option value="' + trigger.filters[i].field + '" ' + (data ? (data.field == trigger.filters[i].field ? 'selected="selected"' : '') : '') + '>' + trigger.filters[i].label + '</option>';
        }
        $select.html(options);
    }

    function set_condition_operators($condition_wrapper, field, data)
    {
        var trigger = automations.triggers[$("[name=trigger]").val()];
        if (!trigger){
            return;
        }

        var $operator = $condition_wrapper.find(".condition_operator");
        $operator.html("");

        for (var i in trigger.filters) {
            if (trigger.filters[i].field == field) {
                if (trigger.filters[i].operators) {
                    for (var o in trigger.filters[i].operators) {
                        var operator = trigger.filters[i].operators[o];
                        $operator.append('<option value="' + o + '" ' + (data == o ? 'selected="selected"' : '') + '>' + operator + '</option>');
                    }
                }
            }
        }
    }
    function set_sequence_message_recipients_index()
    {
        var sequence_index = 0;
        var $sequences = $(".automation-edit-sequence");
        $sequences.each(function(){
            $(this).find(".message_driver").attr("name", "sequence[" + sequence_index + "][message_driver]");
            $(this).find(".message_from").attr("name", "sequence[" + sequence_index + "][message_from]");
            $(this).find(".message_template_id").attr("name", "sequence[" + sequence_index + "][message_template_id]");
            $(this).find(".message_subject").attr("name", "sequence[" + sequence_index + "][message_subject]");
            $(this).find(".message_body").attr("name", "sequence[" + sequence_index + "][message_body]");
            var $recipient_targets = $(this).find(".recipient_target");
            var $recipient_x_details = $(this).find(".recipient_x_details");
            var $recipient_typess = $(this).find(".recipient_type");
            var recipient_index = 0;
            $recipient_targets.each(function(){
                $($recipient_targets[recipient_index]).attr("name", "sequence[" + sequence_index + "][recipient][" + recipient_index + "][recipient]");
                $($recipient_x_details[recipient_index]).attr("name", "sequence[" + sequence_index + "][recipient][" + recipient_index + "][x_details]");
                $($recipient_typess[recipient_index]).attr("name", "sequence[" + sequence_index + "][recipient][" + recipient_index + "][recipient_type]");

                ++recipient_index;
            });
            ++sequence_index;
        });
    }
    function set_sequence_conditions_index()
    {
        var sequence_index = 0;
        var $sequences = $(".automation-edit-sequence");
        $sequences.each(function(){
            var $conditions = $(this).find(".automation-edit-condition");
            var condition_index = 0;
            $conditions.each(function(){
                $(this).find(".condition_field").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][field]");
                $(this).find(".condition_operator").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][operator]");
                $(this).find(".condition_value--select").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val][]");
                $(this).find(".condition_value--input").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val]");
                $(this).find(".condition_value--percentage").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val]");
                $(this).find(".condition_value--datepicker").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val]");
                $(this).find(".condition_value--relative_date").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val]");
                ++condition_index;
            });
            ++sequence_index;
        });
    }
    function set_condition_value_select($condition_wrapper, field, data)
    {
        var $select = $condition_wrapper.find(".condition_value--select");
        var $input = $condition_wrapper.find(".condition_value--input");
        var $percentage = $condition_wrapper.find(".condition_value--percentage");
        var $percentage_wrapper = $percentage.parents('.input_group');
        var $datepicker = $condition_wrapper.find(".condition_value--datepicker");
        var $datepicker_wrapper = $datepicker.parents('.form-datepicker-wrapper');
        var $relative_date_wrapper = $condition_wrapper.find(".contition_value--relative-date");

        $condition_wrapper.ib_initialize_datepickers();
        $select.parents(".form-select").removeClass("hidden");
        $select.prop("disabled", false);
        $select.html("");
        $select.multiselect({
            'enableCaseInsensitiveFiltering': true,
            'enableFiltering': true,
            'includeSelectAllOption': true,
            'maxHeight': 460,
            'selectAllText': 'ALL'
        });
        $input.addClass("hidden").prop("disabled", true);
        $input.attr("placeholder", "");
        $percentage_wrapper.addClass("hidden");
        $percentage.prop("disabled", true);
        $datepicker_wrapper.addClass('hidden');
        $datepicker.prop('disabled', true);

        if (field == "course_type_id") {
            $.get(
                "/api/courses/types",
                function (response) {
                    for (var i in response.types) {
                        var option = document.createElement('option');
                        option.value = response.types[i].id;
                        option.innerHTML = response.types[i].type;
                        $select.append(option);
                    }
                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }
                    $select.multiselect('rebuild');
                }
            )
        } else if (field == "course_category_id") {
            $.get(
                "/api/courses/categories",
                function (response) {
                    for (var i in response.categories) {
                        var option = document.createElement('option');
                        option.value = response.categories[i].id;
                        option.innerHTML = response.categories[i].category;
                        $select.append(option);
                    }
                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }
                    $select.multiselect('rebuild');
                }
            )
        } else if (field == "course_subject_id") {
            $.get(
                "/api/courses/subjects",
                function (response) {
                    for (var i in response.subjects) {
                        var option = document.createElement('option');
                        option.value = response.subjects[i].id;
                        option.innerHTML = response.subjects[i].name;
                        $select.append(option);
                    }
                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }
                    $select.multiselect('rebuild');
                }
            )
        }else if (field == "course_id") {
            $.get(
                "/admin/courses/find_course",
                function (response) {
                    for (var i in response) {
                        var option = document.createElement('option');
                        option.value = response[i].id;
                        option.innerHTML = response[i].value;
                        $select.append(option);
                    }

                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }
                    $select.multiselect('rebuild');
                }
            )
        } else if (field == "schedule_id") {
            $.get(
                "/admin/courses/autocomplete_schedules?alltime=yes",
                function (response) {
                    for (var i in response) {
                        var option = document.createElement('option');
                        option.value = response[i].value;
                        option.innerHTML = response[i].label;
                        $select.append(option);
                    }

                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }

                    $select.multiselect('rebuild');
                }
            )
        }  else if (field == "trainer_id") {
            $.get(
                "/admin/courses/autocomplete_trainers",
                function (response) {
                    for (var i in response) {
                        var option = document.createElement('option');
                        option.value = response[i].value;
                        option.innerHTML = response[i].label;
                        $select.append(option);
                    }
                    if ($condition_wrapper[0].set_values) {
                        $select.find("option").each(function(){
                            for (var i in $condition_wrapper[0].set_values) {
                                if ($condition_wrapper[0].set_values[i] == this.value) {
                                    this.selected = true;
                                }
                            }
                        });
                    }
                    $select.multiselect('rebuild');
                }
            )
        } else if(field == 'application_status') {
            var applicaton_statuses = {no_submit: 'Not Submitted', 'submitted': 'Submitted'};
            for (var applicaton_status in applicaton_statuses) {
                var option = document.createElement('option');
                option.value = applicaton_status;
                option.innerHTML = applicaton_statuses[applicaton_status];
                $select.append(option);
            }
        } else if(field == 'todo_status') {
            var todo_statuses = {'Open': 'Open', 'In Progress': 'In Progress', 'Done': 'Done'};
            for (var todo_status in todo_statuses) {
                var option = document.createElement('option');
                option.value = todo_status;
                option.innerHTML = todo_statuses[todo_status];
                $select.append(option);
            }
        } else if(field == 'payment_method') {
            var payment_methods = {cc: 'Credit Card', sms: 'SMS', invoice: 'Invoice', cash: 'Cash', sales_quote: 'Sales Quote'};
            for (var payment_method in payment_methods) {
                var option = document.createElement('option');
                option.value = payment_method;
                option.innerHTML = payment_methods[payment_method];
                $select.append(option);
            }
        } else {
            $select.parents(".form-select").addClass("hidden");
            $select.prop("disabled", true);
            $select.html("");
            $select.multiselect('rebuild');

            if (field == 'schedule_capacity') {
                $percentage.prop('disabled', false).removeClass('hidden');
                $percentage_wrapper.removeClass('hidden');
            } else if (field.indexOf('date_interval') != -1) {
                $input.prop('disabled', false).removeClass('hidden');
                $input.attr("placeholder", "n minute/hour/day/week/month");
                /*$relative_date_wrapper.find("input,select").prop('disabled', false);
                $relative_date_wrapper.removeClass("hidden");
                if ($condition_wrapper[0].set_values && $condition_wrapper[0].set_values.length > 0) {
                    $relative_date_wrapper.find(".condition_value--relative_date").val($condition_wrapper[0].set_values[0]);
                    var rdate = $condition_wrapper[0].set_values[0].match(/(\d+)\s*(minute|hour|day|month|week)/);
                    if (rdate) {
                        $relative_date_wrapper.find(".relative-date-interval-amount").val(rdate[1]);
                        $relative_date_wrapper.find(".relative-date-interval-type").val(rdate[2]);
                    }
                }
                $relative_date_wrapper.find(".relative-date-interval-amount, .relative-date-interval-type").on("change", function(){
                    $relative_date_wrapper.find(".condition_value--relative_date").val(
                        $relative_date_wrapper.find(".relative-date-interval-amount").val() +
                        " " +
                        $relative_date_wrapper.find(".relative-date-interval-type").val()
                    )
                });*/
            } else if (field.indexOf('date') != -1) {
                $datepicker.prop('disabled', false);
                $datepicker_wrapper.removeClass('hidden');
                $datepicker_wrapper.find("input").val($condition_wrapper[0].set_values[0]);
                var dt = new Date($condition_wrapper[0].set_values[0]);
                $datepicker_wrapper.find("input.form-datepicker").val(dt.dateFormat("d/M/Y"));
            } else {
                $input.prop('disabled', false).removeClass('hidden');
                if ($condition_wrapper[0].set_values) {
                    for (var i in $condition_wrapper[0].set_values) {
                        $input.val($condition_wrapper[0].set_values[i]);
                    }
                }
            }
        }
    }
    function condition_field_change_handler()
    {
        var field = this.value;
        var $condition_wrapper = $(this).parents(".automation-edit-condition");
        set_condition_operators($condition_wrapper, field, $condition_wrapper[0].operator);
        set_condition_value_select($condition_wrapper, field, $condition_wrapper[0].operator);

    }
    function condition_remove_handler()
    {
        $(this).parents(".automation-edit-condition").remove();
        set_sequence_conditions_index();
    }
    function add_condition($condition_wrapper, data)
    {
        var trigger = automations.triggers[$("[name=trigger]").val()];
        if (!trigger){
            return;
        }

        var $condition = $condition_template.clone();
        $condition.removeClass("hidden");
        $condition[0].set_values = data.val;
        $condition[0].operator = data.operator;

        var $select = $condition.find(".condition_field");
        var options = '<option value=""></option>';
        for (var i in trigger.filters) {
            options += '<option value="' + trigger.filters[i].field + '" ' + (data ? (data.field == trigger.filters[i].field ? 'selected="selected"' : '') : '') + '>' + trigger.filters[i].label + '</option>';
        }
        $select.html(options);
        $condition_wrapper.append($condition);
        $select.on("change", condition_field_change_handler);
        $select.change();
        $condition.find(".automation-condition-remove").on("click", condition_remove_handler);
        set_sequence_conditions_index();

        if (data) { // does not work. selects are init from xhr requests
            $condition.find("select.condition_value option").each(function(){
                for (var i in data.val) {
                    if (data.val[i] == this.value) {
                        this.selected = true;
                    }
                }
            });
            $condition.find("select.condition_value").multiselect('rebuild');
            $condition.find("input.condition_value").each(function(){
                for (var i in data.val) {
                    this.value = data.val[i];
                }
            });
        }
    }
    function add_condition_click_handler()
    {
        var $condition_wrapper = $(this).parents(".automation-edit-conditions_are_met").find(".automation-edit-conditions-list");
        var field = $(this).parents(".automation-edit-conditions_are_met").find(".automation-edit-condition-add-type").val();
        $(this).parents(".automation-edit-conditions_are_met").find(".automation-edit-condition-add-type").val("");
        var add = true;
        $condition_wrapper.find(".condition_field").each(function(){
            if (this.value == field) {
                add = false;
            }
        });
        if (add) {
            add_condition($condition_wrapper, {field: field, operator: null, val: null});
        }
    }

    function set_action_options($action)
    {
        var trigger = automations.triggers[$("#automation-edit [name=trigger]").val()];
        if (!trigger){
            return;
        }

        $("#automations-edit-action option[data-action=yes]").remove();
        var options = '';
        for (var i in automations.actions) {
            var action = automations.actions[i];

            if (action.purpose == trigger.purpose) {
                for (var p in trigger.params) {
                    var param = trigger.params[p];
                    if (action.params.indexOf(param) != -1) {
                        options += '<option value="' + action.name + '" data-action="yes">' + action.name + '</option>';
                    }
                }
            }
        }
        $("#automations-edit-action").append(options);

    }
    function add_interval($interval_wrapper, data)
    {
        if (data) {
            if (data.is_periodic == -1) {
                $interval_wrapper.find(".interval_when[value=immediately]").prop("checked", true);
            } else {
                $interval_wrapper.find(".interval_when[value=scheduled]").prop("checked", true);
            }
            $interval_wrapper.find(".interval_when:checked").click();
            $interval_wrapper.find(".is_periodic[value='" + data.is_periodic + "']").prop("checked", true);

            if (data.is_periodic == 1) {
                $interval_wrapper.find(".interval_frequency").val(data.frequency);
                $interval_wrapper.find(".interval_amount").val(data.interval_amount);
                $interval_wrapper.find(".interval_type").val(data.interval_type);
                $interval_wrapper.find(".interval_execute").val(data.execute);
                $interval_wrapper.find(".interval_operator").val(data.interval_operator);
                $interval_wrapper.find(".interval_relative_field").val(data.interval_field);
                $interval_wrapper.find(".do_not_repeat").prop("checked", data.allow_duplicate_message != 1);
                $interval_wrapper.find(".interval_type").trigger('change');
                try {
                    var frequency = JSON.parse(data.frequency);
                    if (data.interval_type == "hour") {
                        $interval_wrapper.find(".frequency_hour_minute").val(frequency.minute[0].substr(0, 5));
                    }
                    if (data.interval_type == "day") {
                        $interval_wrapper.find(".frequency_day_time").val(frequency.hour[0] + ":" + frequency.minute[0].substr(0, 5));
                    }
                    if (data.interval_type == "week") {
                        $interval_wrapper.find(".frequency_day_time").val(frequency.hour[0] + ":" + frequency.minute[0].substr(0, 5));
                        $interval_wrapper.find(".frequency_day_of_week").val(frequency.day_of_week[0]);
                    }
                    if (data.interval_type == "month") {
                        $interval_wrapper.find(".frequency_day_time").val(frequency.hour[0] + ":" + frequency.minute[0].substr(0, 5));
                        $interval_wrapper.find(".frequency_day_of_week").val(frequency.day_of_week[0]);
                        $interval_wrapper.find(".frequency_day_of_month").val(frequency.day_of_month[0]);
                    }
                    $interval_wrapper.find(".frequency_hour_minute,.frequency_day_time,.frequency_day_of_week,.frequency_day_of_month").trigger('change');
                } catch (exc) {

                }
            }
            if (data.is_periodic == 0) {
                var datetime = new Date(data.execute_once_at_datetime);
                $interval_wrapper.find(".form-datepicker").val(datetime.dateFormat("d/M/Y"));
                $interval_wrapper.find(".form-timepicker").val(datetime.dateFormat("H:i"));
            }
        }
    }
    function interval_when_click_handler()
    {
        const selected = $(this).parents('.btn-group').find('.interval_when:checked').val();
        $(this).parents('.panel-body').find('.automation-edit-interval-schedule-edit').toggleClass('hidden', selected != 'scheduled');
        if (this.value == 'immediately') {
            $(this).parents('.panel-body').find(".is_periodic[value='-1']").prop("checked", true);
        }
    }
    function set_sequence_intervals_index()
    {
        var sequence_index = 0;
        var $sequences = $(".automation-edit-sequence");
        $sequences.each(function(){
            var $intervals = $(this).find(".automation-edit-interval-schedule-edit");
            var interval_index = 0;
            $intervals.each(function(){
                $(this).find(".interval_execute_once_at_datetime").val($(this).find(".form-datepicker").val() + " " + $(this).find(".form-timepicker").val());
                $(this).find(".is_periodic").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][is_periodic]");
                $(this).find(".interval_execute_once_at_datetime").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][execute_once_at_datetime]");
                $(this).find(".interval_frequency").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][frequency]");
                $(this).find(".interval_amount").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][interval_amount]");
                $(this).find(".interval_type").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][interval_type]");
                $(this).find(".interval_execute").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][execute]");
                $(this).find(".interval_operator").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][interval_operator]");
                $(this).find(".interval_relative_field").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][interval_field]");
                $(this).find(".do_not_repeat").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][do_not_repeat]");
                ++interval_index;
            });
            ++sequence_index;
        });
    }
    function set_interval_relative_field($sequence_wrapper)
    {
        var trigger = automations.triggers[$("[name=trigger]").val()];
        if (trigger.initiator != InitiatorType.CRON) {
            $sequence_wrapper.find(".interval_execute option[value=before]").addClass("hidden");
            $sequence_wrapper.find(".interval_execute option[value=after]").prop("selected", true)
        } else {
            $sequence_wrapper.find(".interval_execute option[value=before]").removeClass("hidden");
            $sequence_wrapper.find(".interval_execute option[value=after]").prop("selected", false)
        }
        var interval_relative_field_options = '';
        for (var i in trigger.filters) {
            if (trigger.filters[i].field.indexOf("date_interval") == -1) {
                continue;
            }
            interval_relative_field_options += '<option value="' + trigger.filters[i].field + '" >' + trigger.filters[i].label + '</option>';
        }
        $sequence_wrapper.find(".interval_relative_field").html(interval_relative_field_options);
    }
    function interval_type_change_handler()
    {
        var $sequence = $(this).parents(".automation-edit-sequence");

        const unit = $(this).val();
        $sequence.find('.automation-frequency-rows').toggleClass('hidden', unit == '');
        if (unit == 'hour') {
            $sequence.find('.frequency_hour_minute').val('0');
        } else if (unit == 'day') {
            $sequence.find('.frequency_day_time').val('00:00');
        } else if (unit == 'week') {
            $sequence.find('.frequency_day_time').val('00:00');
            $sequence.find('.frequency_day_of_week').val('1');
        } else if (unit == 'month') {
            $sequence.find('.frequency_day_time').val('00:00');
            $sequence.find('.frequency_day_of_week').val('1');
            $sequence.find('.frequency_day_of_month').val('1');
        }

        $sequence.find('.automation-frequency-row').each(function (i, element) {
            var show_for = $(element).data('show_for');

            $(element).toggleClass('hidden', (unit && show_for.indexOf(unit) == -1));
        });
    }
    function interval_frequency_change_handler()
    {
        var frequency = {
            minute: ["*"],
            hour: ["*"],
            day_of_week: ["*"],
            day_of_month: ["*"],
            month: ["*"]
        };
        var $frequency_wrapper = $(this).parents(".automation-edit-interval-schedule-edit");
        $frequency_wrapper.find("input.interval_frequency").val("");
        if ($frequency_wrapper.find(".is_periodic:checked").val() == 1) {
            var interval_type = $frequency_wrapper.find(".interval_type").val();
            if (interval_type == "hour") {
                frequency.minute = [$frequency_wrapper.find(".frequency_hour_minute").val()];
                if (frequency.minute[0] == "") {
                    frequency.minute[0] = "*";
                    console.log("invalid frequency minute");
                }
            }
            if (interval_type == "day" || interval_type == "week" || interval_type == "month") {
                var daytime = $frequency_wrapper.find(".frequency_day_time").val().split(":");
                frequency.minute = [daytime[1]];
                frequency.hour = [daytime[0]];

                if (frequency.minute[0] == "") {
                    frequency.minute[0] = "0";
                    console.log("invalid frequency minute 2");
                }
                if (frequency.hour[0] == "") {
                    frequency.hour[0] = "*";
                    console.log("invalid frequency hour");
                }
            }
            if (interval_type == "week") {
                frequency.day_of_week = [$frequency_wrapper.find(".frequency_day_of_week").val()];
                if (frequency.day_of_week[0] == "") {
                    frequency.day_of_week[0] = "1";
                    console.log("invalid frequency day_of_week");
                }
            }
            if (interval_type == "month") {
                frequency.day_of_month = [$frequency_wrapper.find(".frequency_day_of_month").val()];
                if (frequency.day_of_month[0] == "") {
                    frequency.day_of_month[0] = "1";
                    console.log("invalid frequency day_of_month");
                }
            }
            $frequency_wrapper.find("input.interval_frequency").val(JSON.stringify(frequency));
        } else {
            $frequency_wrapper.find("input.interval_frequency").val("");
        }
    }
    function todo_assignee_change_handler()
    {
        var $todo_wrapper = $(this).parents(".automation-sequence");
        var assignee = $todo_wrapper.find(".todo_assignee:checked").val();
        $todo_wrapper.find(".todo_schedule_id").parents(".form-select").addClass("hidden");
        $todo_wrapper.find(".todo_contact_id").parents(".form-select").addClass("hidden");
        if (assignee == "Attendee") {
            $todo_wrapper.find(".todo_schedule_id").parents(".form-select").removeClass("hidden");
        } else if (assignee == "Trainer") {
            $todo_wrapper.find(".todo_schedule_id").parents(".form-select").removeClass("hidden");
        } else {
            $todo_wrapper.find(".todo_contact_id").parents(".form-select").removeClass("hidden");
        }
    }
    function attachment_option_change_handler()
    {
        var $attachments_wrapper = $(this).parents(".automation-edit-email-attachments");
        $attachments_wrapper.find('.automation-edit-attachment-upload, .automation-edit-attachment-generate').addClass('hidden');
        $attachments_wrapper.find(".attachment_process_docx").val("0");
        $attachments_wrapper.find(".attachment_convert_docx_to_pdf").val("0");
        $attachments_wrapper.find(".attachment_share").val("0");

        const selected = $attachments_wrapper.find('.attachment_option:checked').val();

        if (selected == 'upload') {
            $attachments_wrapper.find('.automation-edit-attachment-upload').removeClass('hidden');
        }
        else if (selected.indexOf('generate') === 0) {
            $attachments_wrapper.find('#automation-edit-attachment-generate').removeClass('hidden');
            $attachments_wrapper.find(".attachment_process_docx").val("1");
            $attachments_wrapper.find(".attachment_convert_docx_to_pdf").val("1");
            if (selected.indexOf('generate_publish') === 0) {
                $attachments_wrapper.find(".attachment_share").val("1");
            }
        }
    }

    function attachment_upload_selected_handler()
    {
        var $upload_wrapper = $(this).parents(".automation-edit-attachment-upload");
        var data = new FormData();
        var file = this.files[0];
        data.append('file', file);

        $.ajax({
                url         :'/admin/automations/upload_attachment_tmp',
                type        : 'POST',
                data        : data,
                contentType : false,
                processData : false
            }
        ).done(function(response){
            //$upload_wrapper.find('.file_previews').html();
            $upload_wrapper.find('.file_previews').append(
                '<div class="file_preview">' +
                    '<span>' + response.name + '&nbsp;&nbsp;&nbsp;</span>' +
                    '<input type="hidden" class="attachment_file_id" value="' + response.file_id + '" />' +
                    '<a class="file_preview_remove" onclick="$(this).parent().remove()">remove</a>' +
                '</div>'
            );
            set_sequence_index();
        }).fail(function(){
            alert("Failed to upload file");
        });
    }

    function add_sequence(data)
    {
        var trigger = automations.triggers[$("[name=trigger]").val()];

        var $sequence_wrapper = $sequence_wrapper_template.clone();
        var sequence_number = 0;
        $(".automation-edit-sequence").each(function(){
            sequence_number = Math.max(sequence_number, parseInt($(this).attr("data-sequence_number")));
        });
        ++sequence_number;

        var run_type = $("#automations-edit-action").val();
        var $sequence = null;
        if (run_type == "email" || (data != null && (data.run_type == "message" && data.message_driver == "email"))) {
            $("#automation-edit-do_this-subtitle").html("Send an email");
            $sequence = $edit_email_template.clone();
            $sequence_wrapper.find(".run_type").val("message");
            $sequence_wrapper.find(".message_driver").val("email");
            $sequence.find(".automation-edit-email-attachments").removeClass("hidden");
            $automation_write_email_wrapper.remove();
            $automation_write_email_wrapper.removeClass("hidden");
            $sequence.find(".automation-write-email").append($automation_write_email_wrapper);
            if (data) {
                $sequence.find(".message_from").val(data.message_from);
                $sequence.find(".message_template_id").val(data.message_template_id);
                $sequence.find(".message_subject").val(data.message_subject);
                $sequence.find(".message_body").val(data.message_body);

                for (var recipient_index in data.recipients) {
                    $sequence.find(".automation-write-email").append('<input type="hidden" class="recipient_type" value="' + data.recipients[recipient_index].recipient_type + '" />');
                    $sequence.find(".automation-write-email").append('<input type="hidden" class="recipient_target" value="' + data.recipients[recipient_index].recipient + '" />');
                    $sequence.find(".automation-write-email").append('<input type="hidden" class="recipient_x_details" value="' + data.recipients[recipient_index].x_details + '" />');
                }
            }
            $automation_write_email_wrapper.reset_inputs();
        } else if (run_type == 'sms' || (data != null && (data.run_type == "message" && data.message_driver == "sms"))) {
            $sequence = $edit_sms_template.clone();
            $sequence_wrapper.find(".run_type").val("message");
            $sequence_wrapper.find(".message_driver").val("sms");
            if (data) {
                $sequence.find(".message_from").val(data.message_from);
                $sequence.find(".message_template_id").val(data.message_template_id);
                $sequence.find(".message_subject").val("");
                $sequence.find(".message_body, textarea").val(data.message_body);

                for (var recipient_index in data.recipients) {
                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_type" value="' + data.recipients[recipient_index].recipient_type + '" />');
                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_target" value="' + data.recipients[recipient_index].recipient + '" />');
                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_x_details" value="' + data.recipients[recipient_index].x_details + '" />');
                    messaging_target_add(
                        $sequence.find('.contact-list-labels'),
                        "sms_recipient",
                        {value: data.recipients[recipient_index].recipient, label: data.recipients[recipient_index].recipient, category: "TAG"},
                        {x_details: "to"}
                    );
                }
            }
            var $send_sms_to = $sequence.find('.send-sms-to');
            try {
                $send_sms_to.autocomplete('destroy');
            } catch (exc) {

            }
            $send_sms_to.autocomplete({
                source: function (data, callback) {
                    var term = $send_sms_to.val();
                    data.driver = 'sms';
                    $.get('/admin/messaging/to_autocomplete', data, function (response) {
                        var trigger = automations.triggers[$("[name=trigger]").val()];
                        var options = [];
                        for (var i in trigger.generated_message_params) {
                            if (trigger.generated_message_params[i].indexOf(term) != -1) {
                                options.push(
                                    {
                                        category: 'TAG',
                                        label: trigger.generated_message_params[i],
                                        value: trigger.generated_message_params[i]
                                    }
                                );
                            }
                        }
                        for (var i in response) {
                            options.push(response[i]);
                        }
                        console.log(response);
                        callback(options);
                        $(".ui-helper-hidden-accessible").addClass("sr-only");
                        $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
                    });
                },
                select: function (event, ui) {
                    event.preventDefault();

                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_type" value="' + ui.item.category + '" />');
                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_target" value="' + ui.item.value + '" />');
                    $sequence.find(".automation-write-sms").append('<input type="hidden" class="recipient_x_details" value="to" />');

                    messaging_target_add($(this).find('\+.contact-list-labels'), "sms_recipient", ui.item, {x_details: "to"});
                    this.value = '';
                    set_sequence_message_recipients_index();
                }
            });
            set_sequence_message_recipients_index();
        } else if (run_type == 'alert' || (data != null && (data.run_type == "message" && data.message_driver == "dashboard"))) {
            $sequence = $edit_alert_template.clone();
            $sequence_wrapper.find(".run_type").val("message");
            $sequence_wrapper.find(".message_driver").val("dashboard");
            if (data) {
                $sequence.find(".message_from").val(data.message_from);
                $sequence.find(".message_template_id").val(data.message_template_id);
                $sequence.find(".message_subject").val("");
                $sequence.find(".message_body, textarea").val(data.message_body);

                for (var recipient_index in data.recipients) {
                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_type" value="' + data.recipients[recipient_index].recipient_type + '" />');
                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_target" value="' + data.recipients[recipient_index].recipient + '" />');
                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_x_details" value="' + data.recipients[recipient_index].x_details + '" />');
                    messaging_target_add(
                        $sequence.find('.contact-list-labels'),
                        "sms_recipient",
                        {value: data.recipients[recipient_index].recipient, label: data.recipients[recipient_index].recipient, category: "TAG"},
                        {x_details: "to"}
                    );
                }
            }
            var $send_sms_to = $sequence.find('.send-sms-to');
            try {
                $send_sms_to.autocomplete('destroy');
            } catch (exc) {

            }
            $send_sms_to.autocomplete({
                source: function (data, callback) {
                    var term = $send_sms_to.val();
                    data.driver = 'sms';
                    $.get('/admin/messaging/to_autocomplete', data, function (response) {
                        var trigger = automations.triggers[$("[name=trigger]").val()];
                        var options = [];
                        for (var i in trigger.generated_message_params) {
                            if (trigger.generated_message_params[i].indexOf(term) != -1) {
                                options.push(
                                    {
                                        category: 'TAG',
                                        label: trigger.generated_message_params[i],
                                        value: trigger.generated_message_params[i]
                                    }
                                );
                            }
                        }
                        for (var i in response) {
                            options.push(response[i]);
                        }
                        console.log(response);
                        callback(options);
                        $(".ui-helper-hidden-accessible").addClass("sr-only");
                        $(".ui-autocomplete").css("max-height", "300px").css("overflow", "auto");
                    });
                },
                select: function (event, ui) {
                    event.preventDefault();

                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_type" value="' + ui.item.category + '" />');
                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_target" value="' + ui.item.value + '" />');
                    $sequence.find(".automation-write-alert").append('<input type="hidden" class="recipient_x_details" value="to" />');

                    messaging_target_add($(this).find('\+.contact-list-labels'), "sms_recipient", ui.item, {x_details: "to"});
                    this.value = '';
                    set_sequence_message_recipients_index();
                }
            });
            set_sequence_message_recipients_index();
        } else if (run_type == 'create_todo' || (data != null && data.run_type == "create_todo")) {
            $sequence_wrapper.find(".run_type").val("create_todo");
            $sequence = $edit_todo_template.clone();
            $sequence.removeClass("hidden");
            $schedule_select = $sequence.find(".todo_schedule_id");
            $schedule_select.multiselect({
                'enableCaseInsensitiveFiltering': true,
                'enableFiltering': true,
                'includeSelectAllOption': true,
                'maxHeight': 460,
                'selectAllText': 'ALL'
            })
            /*$.get(
             "/admin/courses/autocomplete_schedules",
             function (response) {
             for (var i in response) {
             var option = document.createElement('option');
             option.value = response[i].value;
             option.innerHTML = response[i].label;
             $select.append(option);
             }
             $select.multiselect('rebuild');
             $select.removeClass("hidden");
             }
             )*/

            $contact_select = $sequence.find(".todo_contact_id");
            $contact_select.multiselect({
                'enableCaseInsensitiveFiltering': true,
                'enableFiltering': true,
                'includeSelectAllOption': true,
                'maxHeight': 460,
                'selectAllText': 'ALL'
            })
            $sequence.find(".todo_assignee").on("change", todo_assignee_change_handler);
            todo_assignee_change_handler.call($sequence.find(".todo_assignee")[0]);
            $sequence.find(".form-select").first().addClass("hidden");
            if (data) {
                $sequence.find(".todo_title").val(data.todo_title);
                $sequence.find(".todo_assignee[value=" + data.todo_assignee + "]").prop("checked", true);
                for (var i in data.todo_schedules) {
                    $sequence.find(".todo_schedule_id option[value=" + data.todo_schedules[i].schedule_id + "]").prop("selected", true);
                }
                for (var i in data.todo_contacts) {
                    $sequence.find(".todo_contact_id option[value=" + data.todo_contacts[i].contact_id + "]").prop("selected", true);
                }
            }
            $contact_select.multiselect('rebuild');
            $schedule_select.multiselect('rebuild');
        } else if (run_type == 'automation') {
            $sequence_wrapper.find(".run_type").val("automation");
        } else {
            if ($("#automations-edit-action option:selected").data("action") == "yes" || (data != null && data.run_type == "action")) {
                $sequence = $edit_predefined_function_template.clone();
                $sequence_wrapper.find(".run_type").val("action");
                $sequence_wrapper.find(".action").val($("#automations-edit-action").val());
                $sequence.find(".trigger_variables").addClass("hidden no_variable");
                $sequence.find(".run_function").html(run_type);
                if (data) {
                    $sequence.find(".run_function").html(data.action);
                    $sequence_wrapper.find(".action").val(data.action);
                }
            } else {

            }
        }
        $sequence_wrapper.attr("data-sequence_number", sequence_number);
        $sequence_wrapper.attr("id", "automation-sequence-" + sequence_number);
        $sequence.find("> .panel").attr("id", "automation-sequence-panel-" + sequence_number);
        $sequence.find("> .panel > .panel-body").attr("id", "automation-sequence-panel-body-" + sequence_number);
        $sequence.find("> .panel > .panel-heading").attr("data-target", "#automation-sequence-panel-body-" + sequence_number);

        $sequence.addClass('automation-sequence');

        $sequence_wrapper.append($sequence);

        var $conditions = $conditions_template.clone();
        $conditions.attr("id", "automation-edit-conditions_are_met" + sequence_number);
        $conditions.find("> .panel-body").attr("id", "automation-sequence-conditions-panel-body-" + sequence_number);
        $conditions.find("> .panel-heading").attr("data-target", "#automation-sequence-conditions-panel-body-" + sequence_number);
        $sequence.find(".panel").first().find('.panel-body').first().append($conditions);
        $sequence.attr("id", "automation-edit-sequence-" + sequence_number);

        var $interval = $interval_template.clone();
        $interval.attr("id", "automation-edit-at_this_moment" + sequence_number);
        $interval.find("> .panel-body").attr("id", "automation-sequence-intervals-panel-body-" + sequence_number);
        $interval.find("> .panel-heading").attr("data-target", "#automation-sequence-intervals-panel-body-" + sequence_number);
        $interval.ib_initialize_datepickers();

        $sequence.find(".panel").first().find('.panel-body').first().append($interval);
        $interval.find(".interval_amount, .interval_type, .interval_execute, .interval_operator, .interval_relative_field").on("focus", function(){
            $interval.find(".is_periodic[value=1]").prop("checked", true);
        });
        $interval.find(".form-datepicker").on("focus", function(){
            $interval.find(".is_periodic[value=0]").prop("checked", true);
        });

        $sequence_wrapper.find(".conditions_mode").on("click", conditions_mode_change_handler);

        $sequence.removeClass("hidden");
        $sequence_wrapper.removeClass("hidden");
        $sequence_wrapper.find(".remove-sequence").on("click", remove_sequence_click_handler);
        $("#automation-edit-sequence-list").append($sequence_wrapper);

        $sequence.find(".panel-body").first().collapse('show');

        set_trigger_variables();
        set_condition_fields($sequence_wrapper.find(".automation-edit-condition-add-type"));
        set_interval_relative_field($sequence_wrapper);
        $sequence_wrapper.find(".automation-edit-condition-add-btn").on("click", add_condition_click_handler);
        $sequence_wrapper.find(".interval_when").on("click", interval_when_click_handler);
        $sequence_wrapper.find(".interval_type, .frequency_hour_minute, .frequency_day_time, .frequency_day_of_week, .frequency_day_of_month")
            .on("click", interval_frequency_change_handler)
            .on("change", interval_frequency_change_handler);
        $sequence_wrapper.find(".automation-sequence > .panel > .panel-heading").on("click", sequence_panel_heading_click_handler);
        $sequence_wrapper.find(".interval_type").on("change", interval_type_change_handler);
        $sequence_wrapper.find(".form-timepicker").datetimepicker({datepicker: false, format: 'H:i'});
        $sequence_wrapper.find(".attachment_option").on("change", attachment_option_change_handler);
        $sequence_wrapper.find("input[type=file]").on("change", attachment_upload_selected_handler);

        // Number the subsections
        $('.automation-sequence').each(function(number, element) {
            $(element).find('.section-number').first().html(number+2 + '.');

            $(element).find('.panel .panel .section-number').each(function(subnumber, subelement) {
                $(subelement).html((number+2) + '.' + (subnumber+1) + '.');
            });
        });

        if (trigger.initiator != InitiatorType.CRON) {
            $sequence_wrapper.find(".interval_when_wrapper .radio-icon").first().show();
        } else {
            $sequence_wrapper.find(".interval_when_wrapper .radio-icon").first().hide();
        }

        if (data) {
            $sequence_wrapper.find(".sequence_id").val(data.id);
            $sequence_wrapper.find(".conditions_mode").val(data.conditions_mode);
            if (data.attachments.length > 0) {
                for (var a in data.attachments) {
                    if (data.attachments[a].share == 1) {
                        $sequence_wrapper.find(".attachment_option[value=generate_publish]").prop("checked", true);
                        $sequence_wrapper.find(".attachment_template").val(data.attachments[a].file_id);
                        $sequence_wrapper.find(".attachment_option[value=generate_publish]").trigger("change");
                    } else if (data.attachments[a].process_docx == 1) {
                        $sequence_wrapper.find(".attachment_option[value=generate_attach]").prop("checked", true);
                        $sequence_wrapper.find(".attachment_template").val(data.attachments[a].file_id);
                        $sequence_wrapper.find(".attachment_option[value=generate_attach]").trigger("change");
                    } else {
                        $sequence_wrapper.find(".attachment_option[value=upload]").prop("checked", true);
                        $sequence_wrapper.find(".attachment_option[value=upload]").trigger("change");

                        var $upload_wrapper = $sequence_wrapper.find(".automation-edit-attachment-upload");
                        $upload_wrapper.find('.file_previews').append(
                            '<div class="file_preview">' +
                            '<span>' + data.attachments[a].name + '&nbsp;&nbsp;&nbsp;</span>' +
                            '<input type="hidden" class="attachment_file_id" value="' + data.attachments[a].file_id + '" />' +
                            '<a class="file_preview_remove" onclick="$(this).parent().remove()">remove</a>' +
                            '</div>'
                        );
                    }
                }
            }


            for (var condition_index in data.conditions) {
                add_condition($conditions, data.conditions[condition_index]);
            }

            for (var interval_index in data.intervals) {
                add_interval($interval, data.intervals[interval_index]);
            }
        }
        set_sequence_index();
    }
    function save_click_handler(e)
    {
        $automation_write_email_wrapper.copy_values();
        set_sequence_index();
        return true;
        var data = $("#automation-edit").serialize();
        $.post(
            "/api/automations/save",
            data,
            function (response) {
                console.log(response);
            }
        )
        e.preventDefault();
        return false;
    }
    function set_trigger_variables()
    {
        var trigger = automations.triggers[$("#automation-edit [name=trigger]").val()];
        $(".trigger_variables").addClass("hidden");
        if (trigger.generated_message_params)
            if (trigger.generated_message_params.length > 0) {
                $(".trigger_variables").not(".no_variable").removeClass("hidden");
                $(".trigger_variables ul").html("");
                for(var i in trigger.generated_message_params) {
                    $(".trigger_variables ul").append("<li>" + trigger.generated_message_params[i] + "</li>");
                }
            }
    }

    function trigger_change_handler()
    {
        var trigger = automations.triggers[$("[name=trigger]").val()];

        set_action_options();
        set_trigger_variables();

        if (trigger.initiator != InitiatorType.CRON) {
            $(".interval_when_wrapper > .radio-icon").first().show();
        } else {
            $(".interval_when_wrapper > .radio-icon").first().hide();
        }
    }
    function add_sequence_click_handler()
    {
        add_sequence();
    }
    function remove_sequence_click_handler(e)
    {
        e.preventDefault();
        if ($(this).parents(".automation-edit-sequence").find(".automation-write-email").length > 0) {
            $automation_write_email_wrapper.detach();
            $automation_write_email_wrapper.addClass("hidden");
        }
        $(this).parents(".automation-edit-sequence").remove();
        set_sequence_index();
        return false;
    }
    function conditions_mode_change_handler(e)
    {
        e.preventDefault();
        return false;
    }
    function list_activities()
    {
        var automation_id = $("[name=id]").val();
        $("#automation_activity tbody").html("");
        $.get(
            "/api/automations/log_list?automation_id=" + automation_id,
            function (response) {
                var tbody = '';
                for (var i in response.log) {
                    tbody += '<tr>' +
                        '<td>' + response.log[i].executed + '</td>' +
                        '<td>' +
                        (response.log[i].message_id ? '<a target="_blank" href="/admin/messaging/details?message_id=' + response.log[i].message_id + '">view</a>' : '') +
                        (response.log[i].todo_id ? '<a target="_blank" href="/admin/todos/edit/' + response.log[i].todo_id + '">view</a>' : '') +
                        '</td>' +
                        '</tr>';
                }
                $("#automation_activity tbody").html(tbody);
            }
        )
    }
    function activity_tab_click_handler()
    {
        list_activities();
    }

    function test_preview(automation_id, now)
    {
        $.post(
            "/api/automations/test",
            {
                automation_id: automation_id,
                now: now
            },
            function (response) {
                $("#automation_preview").html("");
                if (response.result.length == 0) {
                    $("#automation_preview").append("<p>No records found</p>");
                } else {
                    var table = '';
                    for (var i in response.result) {
                        if (i > 0) {
                            table += '<hr style="clear: both;" />';
                        }
                        table += '<b>Sequence: ' + i + '</b><br clear="both" />';
                        table += '<table class="table datatable">' +
                            '<thead>' +
                            '<tr>';
                        for (var column in response.result[i][0]) {
                            table += '<td>' + column + '</td>'
                        }
                        table += '</tr>' +
                            '</thead>' +
                            '<tbody>';

                        for (var row in response.result[i]) {
                            table += '<tr>';
                            for (var column in response.result[i][row]) {
                                table += '<td>' + response.result[i][row][column] + '</td>';
                            }
                            table += '</tr>';
                        }
                        table += '</tbody>' +
                            '</table>';

                    }

                    $("#automation_preview").append(table);
                    $("#automation_preview table").dataTable();
                }
            }
        );
    }
    $("#test_preview").on("click", function(){
        var automation_id = $("[name=id]").val();
        var now = $("[name=for_date_test]").val();
        test_preview(automation_id, now)
    });
    $("[name=for_date_test]").datetimepicker();

    $("#automation-edit-select_trigger").on("change", trigger_change_handler);
    $("#automation-edit-add-sequence-btn").on("click", add_sequence_click_handler);
    $("[href='#automation-edit-tab-activity']").on("click", activity_tab_click_handler);
    $("button[type=submit]").on("click", save_click_handler);

    // When an image is uploaded...
    window.automation_image_uploaded = function(filename, path, data, upload_wrapper)
    {
        if (data.media_id) {
            $.post(
                '/admin/files/copy_from_media',
                {
                    media_id: data.media_id,
                    folder: '/attachments/',
                    filename: filename
                },
                function (response) {
                    console.log(response);
                    var $upload_wrapper = $(".upload_by_media").parents(".automation-edit-attachment-upload");
                    $upload_wrapper.find('.file_previews').append(
                        '<div class="file_preview">' +
                        '<span>' + response.name + '&nbsp;&nbsp;&nbsp;</span>' +
                        '<input type="hidden" class="attachment_file_id" value="' + response.file_id + '" />' +
                        '<a class="file_preview_remove" onclick="$(this).parent().remove()">remove</a>' +
                        '</div>'
                    );
                    set_sequence_index();
                }
            )
            // Record the image in the hidden field
            $('#automation-edit-attachment_file').val(filename);

            // Set the preview image
            var $preview = $('#automation-edit-attachment_file-preview-wrapper').find('img');
            $preview.prop('src', window.location.protocol + '//' + window.location.host + "" + path).removeClass('hidden');
            $('#automation-edit-attachment_file-preview-wrapper').removeClass('hidden');
        }
    };

    $(document).on(':ib-browse-image-selected', '.image_thumb', function()
    {
        var img         = this.querySelector('img');
        var path        = img.src.replace(/^.*\/\/[^\/]+/, ''); // URL, minus the domain
        var last_slash  = img.src.lastIndexOf('/');
        var filename    = img.src.substring(last_slash + 1); // Portion of the URL, after the last "/"
        var data        = {media_id: $(img).parent().data('id')};

        automation_image_uploaded(filename, path, data);
    });

    if (window.automation_data) {
        fill_automation_data(window.automation_data);
    }
    function fill_automation_data(data)
    {
        $("[name=name]").val(data.name);

        for (var sequence_index in data.sequences) {
            add_sequence(data.sequences[sequence_index]);
        }
        set_sequence_index();
    }
});