var InitiatorType = {
    USER : 'USER',
    CRON : 'CRON'
};

$(document).on("ready", function(){
    var $sequence_template = $(".sequence.hidden");
    var $interval_template = $sequence_template.find("tr.interval.hidden");
    $interval_template.remove();
    $sequence_template.remove();

    $(".btn.new").on("click", function(){
        $("#automation_edit_form [name=id]").val("");
        $("#automation_edit_form .conditions_wrapper tbody").html("");
        $("#automation_edit_form .intervals_wrapper tbody").html("");
        $("#automation_edit_form #conditions").addClass("hidden");
        $("#automation_edit_form [name=trigger]").val("");
        $("#automation_edit_form [name=action]").val("");
        $("#automation_edit_form [name=action]").html("");
        $("#automation_test_tab").addClass("hidden");
        $(".sequence-list .sequence").remove();
        $("#edit_automation_modal").modal();
    });

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
        var automation_id = $("#automation_edit_form [name=id]").val();
        var now = $("#automation_edit_form [name=for_date_test]").val();
        test_preview(automation_id, now)
    });
    $("#automation_edit_form [name=for_date_test]").datetimepicker();

    $(".btn.view").on("click", function(){
        var automation_id = $(this).parents("tr").data("automation_id");
        $.get(
            "/api/automations/get?id=" + automation_id,
            function (response) {
                $("#automation_edit_form .intervals_wrapper tbody").html("");
                $("#automation_edit_form .intervals_wrapper tbody").html("");
                $("#automation_edit_form #conditions").addClass("hidden");
                $("#automation_edit_form [name=trigger]").val("");
                $("#automation_edit_form [name=action]").val("");
                $("#automation_edit_form [name=action]").html("");
                $("#automation_test_tab").removeClass("hidden");
                $(".sequence-list .sequence").remove();

                $("#automation_edit_form [name=id]").val(response.automation.id);
                $("#automation_edit_form [name=name]").val(response.automation.name);
                $("#automation_edit_form [name=published]").prop("checked", response.automation.published == 1);
                $("#automation_edit_form [name=trigger]").val(response.automation.trigger);

                for (var i in response.automation.sequences) {
                    add_sequence(response.automation.sequences[i]);
                }

                $("#edit_automation_modal").modal();
            }
        )
    });

    $("[href='#automation_edit_form-tab-activity']").on("click", function(){
        var automation_id = $("#automation_edit_form [name=id]").val();
        $("#automation_activity tbody").html("");
        $.get(
            "/api/automations/log_list?id=" + automation_id,
            function (response) {
                var tbody = '';
                for (var i in response.log) {
                    tbody += '<tr>' +
                            '<td>' + response.log[i].executed + '</td>' +
                        '<td>' + (response.log[i].message_id ? '<a target="_blank" href="/admin/messaging/details?message_id=' + response.log[i].message_id + '">view</a>' : '') + '</td>' +
                        '</tr>';
                }
                $("#automation_activity tbody").html(tbody);
            }
        )
    });

    $("#automation_edit_form .btn.add-sequence").on("click", function(){
        add_sequence();
    });

    function set_sequence_index()
    {
        var $sequences = $("#automation_edit_form .sequence-list .sequence");
        var index = 0;
        $sequences.each(function(){
            $(this).data("index", index);
            $(this).find(".sequence_id").attr("name", "sequence[" + index + "][id]");
            $(this).find(".repeat_by_field_wrapper select").attr("name", "sequence[" + index + "][repeat_by_field]");
            $(this).find(".run_type").attr("name", "sequence[" + index + "][run_type]");
            $(this).find(".conditions-mode").attr("name", "sequence[" + index + "][conditions_mode]");
            $(this).find(".automation_action").attr("name", "sequence[" + index + "][action]");
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
            $(this).find(".todo_schedule_select").attr("name", "sequence[" + index + "][schedule]");

            ++index;
        });
    }
    function add_sequence(data)
    {
        var trigger = automations.triggers[$("#automation_edit_form [name=trigger]").val()];
        var run_type = $("#sequence_run_type_select").val();

        var $sequence = $sequence_template.clone();
        $sequence.removeClass("hidden");
        $("#automation_edit_form .sequence-list").append($sequence);

        $sequence.find(".remove-sequence").on("click", function(){
            $sequence.remove();
            set_sequence_index();
        });
        $sequence.find(".btn.add_condition").on("click", add_condition_click_handler);
        $sequence.find(".btn.add_interval").on("click", add_interval_click_handler);

        set_action_options($sequence.find(".automation_action"));

        $sequence.find(".wait_wrapper").addClass("hidden");
        $sequence.find(".action_wrapper").addClass("hidden");
        $sequence.find(".another_automation_wrapper").addClass("hidden");
        $sequence.find(".message_wrapper").addClass("hidden");
        $sequence.find(".create_todo_wrapper").addClass("hidden");

        if (run_type == "action") {
            $sequence.find(".action_wrapper").removeClass("hidden");
        }
        if (run_type == "automation") {
            $sequence.find(".another_automation_wrapper").removeClass("hidden");
        }
        if (run_type == "email") {
            run_type = "message";
            $sequence.find(".message_driver").val("email");
        }
        if (run_type == "sms") {
            run_type = "message";
            $sequence.find(".message_driver").val("sms");
        }
        if (run_type == "message") {
            $sequence.find(".message_wrapper").removeClass("hidden");
        }
        if (run_type == "create_todo") {
            $sequence.find(".create_todo_wrapper").removeClass("hidden");
        }

        $sequence.find(".run_type").val(run_type);

        set_sequence_index();
        /*
        if (trigger.repeat_fields.length > 0) {
            $sequence.find(".repeat_by_field_wrapper").removeClass("hidden");
            $sequence.find(".repeat_by_field_wrapper select option[data-field]").remove();
            for (var i in trigger.repeat_fields) {
                $sequence.find(".repeat_by_field_wrapper select").append(
                    '<option value="' + trigger.repeat_fields[i].field + '" data-field="yes">' + trigger.repeat_fields[i].label + '</option>'
                );
            }
        }*/

        autocomplete_set('#sequence_n_todo_schedule', '/admin/courses/autocomplete_schedules');

        if (data) {
            $sequence.find(".sequence_id").val(data.id);
            $sequence.find(".run_type").val(data.run_type);
            $sequence.find(".conditions-mode").val(data.conditions_mode);
            $sequence.find(".message_driver").val(data.message_driver);
            $sequence.find(".message_subject").val(data.message_subject);
            $sequence.find(".message_body").val(data.message_body);

            for (var i in data.conditions) {
                add_condition($sequence.find(".conditions_wrapper"), data.conditions[i]);
            }

            for (var i in data.intervals) {
                add_interval($sequence.find(".intervals_wrapper"), data.intervals[i]);
            }
        }
    }

    function autocomplete_set(name, url)
    {
        var last_id = null;
        var last_label = null;
        var input = $(name)[0];
        var source = null;
        if (Array.isArray(url)) {
            source = url;
        } else {
            source = function(data, callback){
                if (last_label != data.term) {
                    $(name + "_id").val("");
                }

                var json_url = '';
                if (typeof(url) == "function") {
                    json_url = url();
                } else {
                    json_url = url;
                }

                $.getJSON(
                    json_url, {
                        term: $(name).val(),
                    },
                    callback
                );
            };
        }

        $(name).autocomplete({
            source: source,
            open: function () {
                if (last_label != input.value) {
                    $(name + "_id").val("");
                }
            },
            select: function (event, ui) {
                if (ui.item.label) {
                    if (ui.item.id) {
                        $(name + "_id").val(ui.item.id);
                    } else {
                        $(name + "_id").val(ui.item.value);
                    }
                    $(name).val(ui.item.label);
                    last_label = ui.item.label;
                    last_id = ui.item.value;
                } else {
                    $(name + "_id").val(ui.item.id);
                    last_label = ui.item.value;
                    last_id = ui.item.id;
                }

                $(name + "_id")[0].selected_data = ui.item;
                $(name + "_id").change();
                return false;
            },
        });

        $(input).on('blur', function(){
            if (input.value == '') {
                $(name + "_id").val("");
            }
            if ($(name + "_id").val() == "") {
                input.value = "";
            }
        });
    }

    function set_filter_autocomplete()
    {
        var input_id = this.id.replace("_field", "");
        var field = this.value;
        var select_interval =
            '<select>' +
                '<option value="minute">Minute</option>' +
                '<option value="hour">Hour</option>' +
                '<option value="day">Day</option>' +
                '<option value="week">Week</option>' +
                '<option value="month">Month</option>' +
            '</select>';


        if (field == "contact_id") {
            autocomplete_set('#' + input_id, '/admin/contacts3/autocomplete_contacts');
        } else if (field == "trainer_id") {
            autocomplete_set('#' + input_id, '/admin/courses/autocomplete_trainers');
        } else if (field == "course_id") {
            autocomplete_set('#' + input_id, '/admin/courses/find_course');
        } else if (field == "schedule_id") {
            autocomplete_set('#' + input_id, '/admin/courses/autocomplete_schedules');
        } else if (field == "contact_type_id") {
            autocomplete_set('#' + input_id, '/admin/contacts3/autocomplete_types');
        } else if (field == "category_id") {
            autocomplete_set('#' + input_id, '/admin/courses/autocomplete_categories');
        } else if (field == "subject_id") {
            autocomplete_set('#' + input_id, '/admin/courses/find_subject');
        } else if (field == "booking_start_date") {
            $('#' + input_id).datetimepicker();
        } else if (field == "booking_end_date") {
            $('#' + input_id).datetimepicker();
        } else if (field == "start_date") {
            $('#' + input_id).datetimepicker();
        } else if (field == "end_date") {
            $('#' + input_id).datetimepicker();
        } else if (field == "start_date_interval") {
            //$('#' + input_id).attr("placeholder", "n minute/hour/day/week/month");
            $('#' + input_id).parent().append(select_interval);
        } else if (field == "end_date_interval") {
            //$('#' + input_id).attr("placeholder", "n minute/hour/day/week/month");
            $('#' + input_id).parent().append(select_interval);
        } else if (field == "transaction_type") {
            autocomplete_set(
                '#' + input_id,
                [
                    {value: "invoice", label: "Credit(invoice)"},
                    {value: "cc", label: "Prepay(creditcard)"},
                    {value: "quote", label: "Sales Quote"},
                ]
            );
        } else if (field == "task_status" || field == "assignment_status" || field == "assessment_status") {
            autocomplete_set(
                '#' + input_id,
                [
                    {value: "open", label: "Open"},
                    {value: "in progress", label: "In Progress"},
                    {value: "done", label: "Done"},
                ]
            );
        } else if (field == "application_status") {

        } else if (field == "survey_status") {
            autocomplete_set('#' + input_id, '/admin/surveys/autocomplete');
        } else {

        }
    }
    function set_filter_operator_select()
    {
        var trigger = automations.triggers[$("#automation_edit_form [name=trigger]").val()];
        var $operator = $(this).parents("tr").find(".filter_operator");
        $operator.html("");

        for (var i in trigger.filters) {
            if (trigger.filters[i].field == this.value) {
                if (trigger.filters[i].operators) {
                    for (var o in trigger.filters[i].operators) {
                        var operator = trigger.filters[i].operators[o];
                        $operator.append('<option value="' + o + '">' + operator + '</option>');
                    }
                }
            }
        }
    }

    function set_action_options($action)
    {
        if (!$action) {
            $action = $("#automation_edit_form .automation_action");
        }
        var options = '<option value="">select</option>';
        var trigger = automations.triggers[$("#automation_edit_form [name=trigger]").val()];
        for (var i in automations.actions) {
            var action = automations.actions[i];

            if (action.purpose == trigger.purpose) {
                for (var p in trigger.params) {
                    var param = trigger.params[p];
                    if (action.params.indexOf(param) != -1) {
                        options += '<option value="' + action.name + '">' + action.name + '</option>';
                    }
                }
            }
        }

        $action.html(options);
    }

    function trigger_change_handler()
    {
        set_action_options();
        var trigger = automations.triggers[this.value];
        if (trigger.initiator == InitiatorType.CRON) {

        }
        $("#trigger_variables").addClass("hidden");
        if (trigger.generated_message_params)
        if (trigger.generated_message_params.length > 0) {
            $("#trigger_variables").removeClass("hidden");
            $("#trigger_variables ul").html("");
            for(var i in trigger.generated_message_params) {
                $("#trigger_variables ul").append("<li>" + trigger.generated_message_params[i] + "</li>");
            }
        }

        $("#automation_edit_form .conditions_wrapper tbody").html("");
        $("#automation_edit_form .conditions_wrapper").addClass("hidden");
        if (trigger.filters)
        if (trigger.filters.length > 0) {
            $("#automation_edit_form .conditions_wrapper").removeClass("hidden");
        }

        $("#automation_edit_form .intervals_wrapper tbody").html("");

        $("#sequence_run_type_select option[data-action=yes]").remove();
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
        $("#sequence_run_type_select").append(options);

        /*
        if (trigger.repeat_fields.length > 0) {
            $(".repeat_by_field_wrapper").removeClass("hidden");
            $(".repeat_by_field_wrapper select option[data-field]").remove();
            for (var i in trigger.repeat_fields) {
                $(".repeat_by_field_wrapper select").append(
                    '<option value="' + trigger.repeat_fields[i].field + '" data-field="yes">' + trigger.repeat_fields[i].label + '</option>'
                );
            }
        }*/
    }
    $("#automation_edit_form [name=trigger]").on("change", trigger_change_handler);

    function set_sequence_conditions_index()
    {
        var sequence_index = 0;
        var $sequences = $("#automation_edit_form .sequence-list .sequence");
        $sequences.each(function(){
            var $conditions = $(this).find(".conditions_wrapper table tbody tr");
            var condition_index = 0;
            $conditions.each(function(){
                $(this).find(".filter_field").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][field]");
                $(this).find(".filter_operator").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][operator]");
                $(this).find(".filter_val").attr("name", "sequence[" + sequence_index + "][condition][" + condition_index + "][val]");
                ++condition_index;
            });
            ++sequence_index;
        });
    }
    function remove_condition_click_handler()
    {
        $(this).parent().parent().remove();
        set_sequence_conditions_index();
    }
    function add_condition($conditions_wrapper, data)
    {
        var trigger = automations.triggers[$("#automation_edit_form [name=trigger]").val()];
        var max_index = -1;
        $(".conditions_wrapper tbody > tr").each (function(){
            max_index = Math.max(max_index, parseInt($(this).data("index")));
        });
        ++max_index;

        var select = '<select name="condition[sequence][index][field]" id="filter_' + max_index + '_field" class="filter_field" >';
        select += '<option value=""></option>';
        for (var i in trigger.filters) {
            select += '<option value="' + trigger.filters[i].field + '" ' + (data ? (data.field == trigger.filters[i].field ? 'selected="selected"' : '') : '') + '>' + trigger.filters[i].label + '</option>';
        }
        select += '</select>';

        var select_operator = '<select name="condition[sequence][index][operator]" id="filter_' + max_index + '_operator" class="filter_operator" >';
        select_operator += '<option value=""></option>';
        select_operator += '</select>';

        var tr = '<tr data-index="' + max_index + '">';
        tr += '<td>' + select + '</td>';
        tr += '<td>' + select_operator + '</td>';
        tr += '<td>' +
            '<input class="filter_val" type="hidden" name="condition[sequence][index][val]" id="filter_' + max_index + '_id" value="' + (data ? data.values[0].val : "") + '" />' +
            '<input type="text" id="filter_' + max_index + '"  value="' + (data ? data.values[0].val : "") + '" />' +
            '</td>';
        tr += '<td><button type="button" class="remove btn">remove</button>';
        tr += '</tr>';


        $conditions_wrapper.find("tbody").append(tr);

        $('#filter_' + max_index + '_field').on("change", set_filter_autocomplete);
        $('#filter_' + max_index + '_field').on("change", set_filter_operator_select);
        if (data) {
            $('#filter_' + max_index + '_field').change();
            $('#filter_' + max_index + '_field').parents("tr").find(".filter_operator").val(data.operator);
        }

        set_sequence_conditions_index();

        $conditions_wrapper.find("tbody .remove.btn").off("click", remove_condition_click_handler);
        $conditions_wrapper.find("tbody .remove.btn").on("click", remove_condition_click_handler);
    }
    function add_condition_click_handler()
    {
        add_condition($(this).parents(".conditions_wrapper"));
    }

    function interval_is_periodic_change_handler()
    {
        var $tr = $(this).parents("tr");
        $tr.find(".interval_frequency, .execute_once_at_datetime").addClass("hidden");
        if (this.value == 1) {
            $tr.find(".interval_frequency").removeClass("hidden");
        } else if (this.value == 0) {
            $tr.find(".execute_once_at_datetime").removeClass("hidden");
        }
    }
    function set_sequence_intervals_index()
    {
        var sequence_index = 0;
        var $sequences = $("#automation_edit_form .sequence-list .sequence");
        $sequences.each(function(){
            var $intervals = $(this).find(".intervals_wrapper table tbody tr");
            var interval_index = 0;
            $intervals.each(function(){
                $(this).find(".is_periodic").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][is_periodic]");
                $(this).find(".execute_once_at_datetime").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][execute_once_at_datetime]");
                $(this).find(".frequency").attr("name", "sequence[" + sequence_index + "][interval][" + interval_index + "][frequency]");
                ++interval_index;
            });
            ++sequence_index;
        });
    }
    function remove_interval_click_handler()
    {
        $(this).parent().parent().remove();
        set_sequence_intervals_index();
    }
    function frequency_change_handler()
    {
        var frequency = {
            minute: "*",
            hour: "*",
            day_of_week: "*",
            day_of_month: "*",
            month: "*"
        };
        var $frequency_wrapper = $(this).parents(".interval_frequency");
        frequency.minute = $frequency_wrapper.find(".minute").val();
        frequency.hour = $frequency_wrapper.find(".hour").val();
        frequency.day_of_week = $frequency_wrapper.find(".day_of_week").val();
        frequency.day_of_month = $frequency_wrapper.find(".day_of_month").val();
        frequency.month = $frequency_wrapper.find(".month").val();
        $frequency_wrapper.find("input.frequency").val(JSON.stringify(frequency));
    }
    function add_interval($intervals_wrapper, data)
    {
        var trigger = automations.triggers[$("#automation_edit_form [name=trigger]").val()];
        var max_index = -1;

        $("#automation_edit_form .intervals_wrapper tbody > tr").each (function(){
            max_index = Math.max(max_index, parseInt($(this).data("index")));
        });
        ++max_index;

        var $interval = $interval_template.clone();
        $interval.find(".date").datetimepicker({});
        $interval.removeClass("hidden");
        $interval.find(".interval_frequency select").on("change", frequency_change_handler);
        $intervals_wrapper.find("tbody").append($interval);

        set_sequence_intervals_index();

        $interval.find(".remove.btn").on("click", remove_interval_click_handler);
        $interval.find(".is_periodic").on("click", interval_is_periodic_change_handler);

        if (data) {
            $interval.find(".is_periodic").val(data.is_periodic);
            $interval.find(".execute_once_at_datetime").val(data.execute_once_at_datetime);
            $interval.find(".frequency").val(data.frequency);
        }
    }
    function add_interval_click_handler()
    {
        add_interval($(this).parents(".intervals_wrapper"));
    }


    function update_frequency()
    {
        var frequency = {
            minute: $("#minute").val(),
            hour: $("#hour").val(),
            day_of_month: $("#day_of_month").val(),
            month:$("#month").val(),
            day_of_week:$("#day_of_week").val()
        };
        $("#frequency").val(JSON.stringify(frequency));
    }

    $("#minute, #hour, #day_of_month, #month, #day_of_week").on('change', update_frequency);
});
