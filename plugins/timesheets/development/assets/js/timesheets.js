$(document).on("ready", function(){
    $('.timepicker').datetimepicker({datepicker:false, format:'H:i'});

    function parse_duration(str)
    {
        var parts = str.split(' ');
        var minutes = 0;
        for (var i in parts) {
            if (parts[i].length > 1) {
                var val = parts[i].slice(0,-1);
                var unit = parts[i].slice(-1);
                if (unit === 'h') {
                    minutes += parseInt(val)*60;
                }
                if (unit === 'm') {
                    minutes += parseInt(val);
                }
            }
        }
        return minutes;
    }

    function minute_to_hour(minutes)
    {

        var minute = minutes % 60;
        var hours = Math.floor(minutes / 60);

        if (isNaN(parseInt(minutes))) {
            return "0";
        }

        return hours + "h" + (minute > 0 ? ((minute < 10 ? " 0" : " ") + minute + "m") : "");
    }

    function fmt_date(str)
    {
        if (str) {
            return moment(str).format('D/MMM');
        }
        return "";
    }

    var $recent_timesheet_li = $(".timesheets-period-status-dropdown-item");
    $recent_timesheet_li.removeClass("hidden");
    $recent_timesheet_li.remove();

    function display_recent_timesheets()
    {
        var recent_params = {
            level: 'contact',
            staff_id: $(".timeoff-header").data("staff_id")
        };

        $.get(
            '/api/timesheets/timesheets',
            recent_params,
            function (response) {
                if (response.items.length == 0) {

                } else {
                    for (var i in response.items) {
                        var $li = $recent_timesheet_li.clone();
                        $li.attr("data-timesheet_id", response.items[i].id);
                        $li.find(".timesheets-period-status-item-title").html("");
                        $li.find(".timesheets-period-status-item-status").html(response.items[i].status);
                        $li.find(".timesheets-period-status-item-bottom").html(response.items[i].period[0] + ' - ' + response.items[i].period[1]);

                        var logged = response.items[i].stats.course_minutes_logged + response.items[i].stats.internal_minutes_logged;
                        var available = response.items[i].stats.minutes_available;
                        $li.find(".minutes_logged").html(minute_to_hour(logged));
                        $li.find(".minutes_available").html(minute_to_hour(available));
                        $li.find("button.submit").attr("data-timesheet_id", response.items[i].id);
                        if (response.items[i].status == 'open' || response.items[i].status == 'ready') {
                            $li.find("button.submit").html("Submit");
                        } else {
                            $li.find("button.submit").html("View");
                        }


                            $(".timesheets-period-status-bar-logged").css("width", Math.ceil((logged / available) * 100) + '%');
                            $(".timesheets-period-status-bar-due").css("width", Math.ceil(((available - logged) / available) * 100) + '%');
                            $(".timesheets-period-status-bar-logged_to_due").css("width", Math.ceil((logged / available) * 100) + '%');

                        $("#recent_timesheets").append($li);
                    }

                    $("#recent_timesheets button.submit").on("click", function(){
                        view_timesheet($(this).data("timesheet_id"), "submit");
                    });
                }
            }
        );
    }


    function display_stats()
    {
        if (!isNaN(parseInt($(".timeoff-header").data("staff_id")))) {
            display_recent_timesheets();
        }

        var start_date = $('#timesheets-daterange').data('daterangepicker').startDate.format('YYYY-MM-DD') || $('#timesheets-daterange-start_date').val();
        var end_date = $('#timesheets-daterange').data('daterangepicker').endDate.format('YYYY-MM-DD') || $('#timesheets-daterange-end_date').val();
        var level = "department";
        var level_id = $(".timeoff-header").data("staff_id");
        level_id = "";
        if ($("#timesheets-department").length > 0) {
            level = "department";
            level_id = $("#timesheets-department").val();
            if (!level_id) {
                level_id = "";
            }
        } else {
            level = "contact";
            level_id = $(".timeoff-header").data("staff_id");
        }

        $.get(
            '/api/timesheets/stats?period_start_date='+start_date+'&period_end_date='+end_date+'&level='+level+'&level_id='+level_id,
            {

            },
            function (response) {
                //$(".timeoff-report-period.total-available").html(minute_to_hour(response.minutes_available));
                $(".timeoff-report-amount.total-available").html(minute_to_hour(response.minutes_available));

                $(".timeoff-report-amount.course-logged").html(minute_to_hour(response.course_minutes_logged));
                $(".timeoff-report-amount.internal-logged").html(minute_to_hour(response.internal_minutes_logged));
                $(".timeoff-report-amount.total-logged").html(minute_to_hour(response.course_minutes_logged + response.internal_minutes_logged));
                $(".timeoff-report-amount.hours-left").html(minute_to_hour(response.minutes_available - response.course_minutes_logged - response.internal_minutes_logged));

                $(".timesheets-hours_logged.time_logged").html(minute_to_hour(response.course_minutes_logged + response.internal_minutes_logged));
                $(".timesheets-hours_logged.time_available").html(minute_to_hour(response.minutes_available));
            }
        );
    }

    function display_pagination(selector, offset, limit, total, click_handler)
    {
        if (total == 0) {
            $("#timesheets-overview " + ".no_records").removeClass("hidden");
            $("#timesheets-overview " + ".pagination-wrapper").addClass("hidden");
        } else {
            $("#timesheets-overview " + ".no_records").addClass("hidden");
            $("#timesheets-overview " + ".pagination-wrapper").removeClass("hidden");

            $("#timesheets-overview " + ".pagination-wrapper .from").html((offset + 1));
            $("#timesheets-overview " + ".pagination-wrapper .to").html((Math.min(limit + offset, total)));
            $("#timesheets-overview " + ".pagination-wrapper .total").html(total);

            var $ul = $("#timesheets-overview " + ".pagination-wrapper ul");

            $ul.find(".page").remove();

            var page_count = Math.ceil(total / limit);
            for (var i = 0 ; i < page_count ; ++i) {
                $('<li class="page' + (offset == i * limit ? ' active ' : '') + '"><button type="button" data-offset="' + (i*limit) + '">' + (i + 1) + '</button></li>').insertBefore($ul.find(".next"));
            }

            $ul.find("button").on("click", click_handler);
        }
    }

    var overview_params = {
        offset: 0,
        limit: 10,
        period_end_date: null,
        period_start_date: null,
        type: 'interval',
        text: ''
    };
    var $timesheet_row_template = $("#timesheets_table").find(".timesheet_row").first();
    var $timesheet_modal_row_template = $("#timesheets_table_period").find(".timesheet_row").first();
    $timesheet_row_template.remove();
    $timesheet_row_template.removeClass("hidden");
    function display_overview()
    {
        $("#timesheets-overview").removeClass("hidden");
        $("#timesheet-details, #timesheet-approvals").addClass("hidden");
        $(".timesheet-request_status-wrapper").removeClass("hidden");
        $(".timesheet-grid-wrapper").addClass("hidden");
        $(".timesheets-status-wrapper").addClass("hidden");

        display_stats();

        var $department = $("#timesheets-department");

        overview_params.period_start_date = $('#timesheets-daterange-start_date').val();
        overview_params.period_end_date = $('#timesheets-daterange-end_date').val();
        overview_params.limit = parseInt($("#timesheets-overview .pagination-limit").val());
        overview_params.type = $("#timesheets-log_types").val().join(',');
        overview_params.level = ($department.length > 0) ? "department" : "contact";
        overview_params.department_id = get_selected_departments();
        overview_params.text = $("#timesheets_table-search").val();
        overview_params.staff_id = $(".timeoff-header").data("staff_id");
        //$("#timesheets_params").val();

        $.get(
            '/api/timesheets',
            overview_params,
            function (response) {
                var tbody = $("#timesheets_table tbody");
                var $row, days_in_range, start_date, end_date;
                tbody.empty();
                for (var i in response.items) {
                    start_date     = new moment(response.items[i].period[0]);
                    end_date       = new moment(response.items[i].period[1]);
                    days_in_range  = 1 + end_date.diff(start_date, 'days');

                    $row = $timesheet_row_template.clone();
                    $row.find("td")[0].innerHTML = format_range(response.items[i].period[0], response.items[i].period[1]);
                    $row.find("td")[1].innerHTML = response.items[i].staff.name;
                    $row.find("td")[2].innerHTML = response.items[i].department ? response.items[i].department.name : "";
                    $row.find("td")[3].innerHTML = response.items[i].type;
                    $row.find("td")[4].innerHTML = response.items[i].item ? response.items[i].item.title : "";
                    $row.find("td")[5].innerHTML = response.items[i].description;
                    $row.find("td")[6].innerHTML = minute_to_hour(response.items[i].period[2] * days_in_range);
                    $row.find("button.view").attr("data-request_id", response.items[i].id);
                    tbody.append($row.clone());
                }

                tbody.find("button.view").on("click", function(){
                    edit_request($(this).data("request_id"));
                });

                display_pagination(
                    "#timesheets-overview",
                    overview_params.offset,
                    overview_params.limit,
                    response.total,
                    function (){
                        overview_params.offset = $(this).data("offset");
                        display_overview();
                    }
                );
            }
        )
    }

    function format_range(start_date, end_date)
    {
        start_date     = new moment(start_date);
        end_date       = new moment(end_date);
        var days_in_range  = 1 + end_date.diff(start_date, 'days');
        var time_range;

        if (days_in_range > 1) {
            if (start_date.format('YYYY') != end_date.format('YYYY')) {
                time_range = '<span class="nowrap">'+start_date.format('ddd D/MMM/YYYY') + '</span> - <span class="nowrap">' + end_date.format('ddd D/MMM/YYYY')+'</span>';
            } else {
                time_range = '<span class="nowrap">'+start_date.format('ddd D/MMM') + '</span> - <span>' + end_date.format('ddd D/MMM/YYYY')+'</span>';
            }
        } else {
            time_range = start_date.format('ddd D/MMM/YYYY');
        }

        return time_range;
    }

    function get_selected_departments()
    {
        var $department = $("#timesheets-department");
        var all_departments_selected = ($department.find('option:not(:selected)').length == 0);

        // If all are selected, don't apply any filter. (This way items with no department will appear.)
        return ($department.val() && !all_departments_selected) ? $department.val().join(',') : null;
    }

    var details_params = {
        period_end_date: null,
        period_start_date: null,
        period_type: 'days',
        department_id: "",
        type: "",
        text: ''
    };
    function display_details()
    {
        $("#timesheet-details").removeClass("hidden");
        $("#timesheets-overview, #timesheet-approvals").addClass("hidden");
        $(".timesheet-request_status-wrapper").addClass("hidden");
        $(".timesheet-grid-wrapper").removeClass("hidden");
        $(".timesheets-status-wrapper").addClass("hidden");


        display_stats();

        details_params.period_start_date = $('#timesheets-daterange-start_date').val();
        details_params.period_end_date = $('#timesheets-daterange-end_date').val();
        details_params.period_type = $("[name=grid_period]:checked").val();
        details_params.department_id = get_selected_departments();
        $.get(
            "/api/timesheets/details",
            details_params,
            function (response) {
                $("#timesheet-details-table").html(response);
            }
        )
    }

    $("#timesheet-details-table").on("click", "tbody td", function(){
        var td_index = $(this).index();
        if (td_index < 4) {
            return;
        }
        var $ths = $(this).parent().parent().parent().find("thead tr th");
        var start = $($ths[td_index]).data("period_start");
        var end = $($ths[td_index]).data("period_end");
        var contact_id = $(this).parent().data("user_id");

        var params = {};
        params.period_start_date = start;
        params.period_end_date = end;
        //params.limit = parseInt($("#timesheets-overview .pagination-limit").val());
        params.type = $("#timesheets-log_types").val().join(',');
        params.level = "contact";
        params.staff_id = contact_id;

        $.get(
            '/api/timesheets',
            params,
            function (response) {
                var tbody = $("#timesheets_table_period tbody");
                var start_date, end_date, days_in_range;
                tbody.html("");
                for (var i in response.items) {
                    start_date     = new moment(response.items[i].period[0]);
                    end_date       = new moment(response.items[i].period[1]);
                    days_in_range  = 1 + end_date.diff(start_date, 'days');

                    var $row = $timesheet_modal_row_template.clone();
                    $row.removeClass("hidden");
                    $row.find("td")[0].innerHTML = format_range(response.items[i].period[0], response.items[i].period[1]);
                    $row.find("td")[1].innerHTML = response.items[i].staff.name;
                    $row.find("td")[2].innerHTML = response.items[i].department ? response.items[i].department.name : "";
                    $row.find("td")[3].innerHTML = response.items[i].type;
                    $row.find("td")[4].innerHTML = response.items[i].item ? response.items[i].item.title : "";
                    $row.find("td")[5].innerHTML = response.items[i].description;
                    $row.find("td")[6].innerHTML = minute_to_hour(response.items[i].period[2] * days_in_range);
                    $row.find("button.view").attr("data-request_id", response.items[i].id);
                    tbody.append($row);
                }

                tbody.find("button.view").on("click", function(){
                    edit_request($(this).data("request_id"));
                });
                $("#period-log").modal();
            }
        );
    });

    var approval_params = {
        offset: 0,
        limit: null,
        period_end_date: null,
        period_start_date: null,
        type: 'interval',
        text: ''
    };
    var $approval_row = $(".timesheets-list-table-group > tr");
    $approval_row.remove();
    $approval_row.removeClass("hidden");
    $approval_row = $($approval_row[0]);
    var $timesheet_details_row = $("#timesheet-details-list-table tbody > tr");
    $timesheet_details_row.remove();
    $timesheet_details_row.removeClass("hidden");
    function display_approvals()
    {
        $("#timesheet-approvals").removeClass("hidden");
        $("#timesheet-details, #timesheets-overview").addClass("hidden");
        $(".timesheet-request_status-wrapper").addClass("hidden");
        $(".timesheet-grid-wrapper").addClass("hidden");
        $(".timesheets-status-wrapper").removeClass("hidden");

        display_stats();

        approval_params.period_start_date = $('#timesheets-daterange-start_date').val();
        approval_params.period_end_date   = $('#timesheets-daterange-end_date').val();
        approval_params.limit = null;
        approval_params.type = $("#timesheets-log_types").val().join(',');
        approval_params.status = $("#timesheets-status").val().join(',');
        approval_params.department_id = get_selected_departments();
        //$("#timesheets_params").val();

        $.get(
            '/api/timesheets/timesheets',
            approval_params,
            function (response) {
                if (response.items.length == 0) {
                    $("#timesheet-approvals-table").addClass("hidden");
                    $("#timesheet-approvals-no-data").removeClass("hidden");
                } else {
                    $("#timesheet-approvals-table").removeClass("hidden");
                    $("#timesheet-approvals-no-data").addClass("hidden");
                    var totals = {
                        'open' : 'Open',
                        'pending' : 'Pending',
                        'declined' : 'Declined',
                        'approved' : 'Approved',
                        'ready' : 'Ready'
                    };
                    var counts = {
                        'open' : 0,
                        'pending' : 0,
                        'declined' : 0,
                        'approved' : 0,
                        'ready' : 0
                    };
                    for (var i in response.items) {
                        if (!counts[response.items[i].status]) {
                            counts[response.items[i].status] = 0;
                            $(".timesheets-list-heading." + response.items[i].status).removeClass("hidden");
                            $(".timesheets-list-heading2." + response.items[i].status).removeClass("hidden");
                            $(".timesheets-list-table-group." + response.items[i].status).removeClass("hidden");
                            $(".timesheets-list-table-group." + response.items[i].status).html("");
                        }
                        ++counts[response.items[i].status];

                        var $row = $approval_row.clone();
                        $row.find("input[type=checkbox]").attr("id", "timesheets-approvals-list-select-" + response.items[i].id);
                        $row.find("input[type=checkbox]").val(response.items[i].id);
                        $row.find(".timesheets-approvals-list-staff").html(response.items[i].staff.name);
                        if (response.items[i].reviewer) {
                            $row.find(".timesheet-reviewer").removeClass("hidden");
                            $row.find(".timesheet-reviewer").html(response.items[i].reviewer.name);
                        }
                        $row.find(".timesheets-status-badge").html(totals[response.items[i].status]);
                        $row.find(".timesheets-list-actions").attr("data-timesheet_id", response.items[i].id);
                        if (response.items[i].status == "open") {
                            $row.find(".timesheets-list-actions button.submit").removeClass("hidden");
                            $row.find(".timesheets-list-actions button.view").data("action", "submit");
                        } else if (response.items[i].status == "pending") {
                            if ($(".timeoff-header").data("staff_role") == 'manager') {
                                $row.find(".timesheets-list-actions button.approve").removeClass("hidden");
                                $row.find(".timesheets-list-actions button.reject").removeClass("hidden");
                                $row.find(".timesheets-list-actions button.view").data("action", "approve");
                            }
                        }
                        $row.find(".timesheet_logged").html(minute_to_hour(response.items[i].stats.course_minutes_logged + response.items[i].stats.internal_minutes_logged));
                        $row.find(".timesheet_available").html(Math.ceil(
                                (
                                    (response.items[i].stats.course_minutes_logged + response.items[i].stats.internal_minutes_logged)
                                    /
                                    response.items[i].stats.minutes_available
                                ) * 100
                            )
                        );
                        $(".timesheets-list-table-group." + response.items[i].status).append($row);

                    }

                    $(".timesheets-list-table-group .timesheets-list-actions button[data-action]").on("click", function(){
                        var timesheet_id = $(this).parents(".timesheets-list-actions").attr("data-timesheet_id");
                        view_timesheet(timesheet_id, $(this).data("action"));
                    });

                    for (var status in counts) {
                        $(".timesheets-list-heading." + status + " .timesheet_count").html(counts[status]);
                        if (counts[status] == 1) {
                            $(".timesheets-list-heading." + status + " .timesheet_plural").html(" is");
                        } else {
                            $(".timesheets-list-heading." + status + " .timesheet_plural").html("s are ");
                        }
                    }
                }
            }
        );
    }

    function view_timesheet(id, action)
    {
        $.get(
            "/api/timesheets/timesheet",
            {
                id : id
            },
            function (response) {
                var $modal = $("#timesheet-details-modal");
                $modal.find("h3.timesheet").addClass("hidden");
                $modal.find("button.action > span").addClass("hidden");
                $modal.find("button.action").removeClass("hidden");
                if (response.timesheet.status == "open" || response.timesheet.status == "ready") {
                    $modal.find("h3.timesheet.submit").removeClass("hidden");
                    $modal.find("button.action > .submit").removeClass("hidden");
                    $modal.find("button.action").attr("data-action", "submit");
                } else if (response.timesheet.status == "pending") {
                    if (action == "approve") {
                        $modal.find("h3.timesheet.approve").removeClass("hidden");
                        $modal.find("button.action > .approve").removeClass("hidden");
                        $modal.find("button.action").attr("data-action", "approve");
                    }
                    if (action == "reject") {
                        $modal.find("h3.timesheet.reject").removeClass("hidden");
                        $modal.find("button.action > .reject").removeClass("hidden");
                        $modal.find("button.action").attr("data-action", "reject");
                    }
                } else {
                    $modal.find("button.action").addClass("hidden");
                }
                var logged = 0;
                var available = response.timesheet.minutes_available;
                for (var i in response.timesheet.requests.items) {
                    //available += response.timesheet.requests.items[i].stats.minutes_available;
                    //logged += response.timesheet.requests.items[i].stats.course_minutes_logged;
                    logged += parseInt(response.timesheet.requests.items[i].period[2]);
                }

                var $tbody = $("#timesheet-details-list-table tbody");
                $tbody.empty();
                for (var j in response.timesheet.requests.items) {
                    var $row = $timesheet_details_row.clone();
                    $row.find(".date").html(response.timesheet.requests.items[j].period[0]);
                    $row.find(".type").html(response.timesheet.requests.items[j].type);
                    if (response.timesheet.requests.items[j].item) {
                        $row.find(".title").html(response.timesheet.requests.items[j].item.title);
                    }
                    $row.find(".description").html(response.timesheet.requests.items[j].description);
                    $row.find(".period").html(minute_to_hour(response.timesheet.requests.items[j].period[2]));
                    $tbody.append($row);
                }

                $modal.find("button.action").attr("data-id", id);
                $modal.find(".timesheets-submit-work_required .logged-time").html(minute_to_hour(logged));
                $modal.find(".timesheets-submit-work_required .required-time").html(minute_to_hour(available));
                $modal.find("#timesheets-comment").val(response.timesheet.note);
                $modal.find("[name=reviewer_id]").val(response.timesheet.reviewer_id);
                $modal.modal();
            }
        )
    }

    $("#timesheet-details-modal button.action").on("click", function(){
        var $modal = $("#timesheet-details-modal");
        var data = [];
        data[0] = {
            id: $modal.find("button.action").attr("data-id"),
            reviewer_id: $modal.find("[name=reviewer_id]").val(),
            note: $modal.find("[name=comment]").val()
        };
        save_timesheet(
            data,
            $modal.find("button.action").attr("data-action"),
            function (response) {
                $("#timesheet-details-modal").modal("hide");
                if (response.error) {
                    error_message(response.error);
                } else {
                    /*if ($(".timeoff-header").data("staff_role") == "staff") {
                        update_timesheets();
                    } else {
                        display_approvals();
                    }*/
                    display_approvals();
                }
            }
        );
    });

    function save_timesheet(data, action, callback)
    {
        var url = "";
        if (action == "submit") {
            url = '/api/timesheets/ts_submit';
        }
        if (action == "approve") {
            url = '/api/timesheets/ts_approve';
        }
        if (action == "reject") {
            url = '/api/timesheets/ts_reject';
        }
        $.post(
            url,
            {timesheets: data},
            function (response) {
                if (callback) {
                    callback(response);
                }
            }
        );
    }

    function update_timesheets()
    {
        if ($(".btn.timesheet-view.btn-primary").hasClass("overview") || $(".btn.timesheet-view.btn-primary").length == 0) {
            display_overview();
        }

        if ($(".btn.timesheet-view.btn-primary").hasClass("details")) {
            display_details();
        }

        if ($(".btn.timesheet-view.btn-primary").hasClass("approvals")) {
            display_approvals();
        }
        $("#details-export-csv").attr("href", "/api/timesheets/detailscsv?" + $.param(details_params));
    }

    function edit_request(id)
    {
        var $modal = $("#timesheet-edit-modal");
        if (id) {
            $.post(
                "/api/timesheets/request",
                {id: id},
                function (response) {
                    $modal.find("#timesheet-edit_timesheet_id").val(response.request.id);
                    $modal.find("#timesheets-log_work-description").val(response.request.description);
                    if ($modal.find("#timesheets-log_work-person").find("option[value=" + response.request.staff_id + "]").length == 0) {
                        $modal.find("#timesheets-log_work-person").append('<option value="' + response.request.staff_id + '">' + response.request.staff + '</option>');
                    }

                    $modal.find("#log-work-todos-autocomplete").val(response.request.todo);
                    $modal.find("#log-work-todos-id").val(response.request.todo_id);
                    if (response.request.todo != "") {
                        $(".todos-select a").tab("show");
                    }

                    $modal.find("#timesheets-log_work-person").val(response.request.staff_id);
                    $modal.find("#log-work-schedule-autocomplete").val(response.request.schedule);
                    $modal.find("#log-work-schedule-id").val(response.request.schedule_id);
                    if (response.request.schedule != "") {
                        $(".schedule-select a").tab("show");
                    }

                    $("#timesheets-log_work-period").prop('checked', (response.request.start_date != response.request.end_date)).trigger('change');
                    $('#timesheets-log_work-start_date').val(response.request.start_date).change();
                    $('#timesheets-log_work-end_date').val(response.request.end_date).change();
                    $("#timesheets-log_work-worked").val(minute_to_hour(response.request.duration));
                    $modal.modal();
                }
            )
        } else {
            $modal.find("#timesheet-edit_timesheet_id").val("");
            $modal.find("#timesheets-log_work-description").val("");
            $modal.find("#timesheets-log_work-person").val($(".timeoff-header").data("staff_id"));
            $modal.find("#log-work-schedule-autocomplete").val("");
            $modal.find("#log-work-schedule-id").val("");
            $modal.find("#log-work-todos-autocomplete").val("");
            $modal.find("#log-work-todos-id").val("");
            var today = new Date();
            $("#timesheets-log_work-start_date").val(today.dateFormat('Y-m-d')).change();
            $("timesheets-log_work-period").prop('checked', false).change();
            $("#timesheets-log_work-end_date").val('').change();
            $("#timesheets-log_work-worked").val("");
            $modal.modal();
        }
    }

    $(".btn.timesheet-view").on("click", function(){
        $(".btn.timesheet-view").removeClass("btn-primary");
        $(this).addClass("btn-primary");
        update_timesheets();
    });

    update_timesheets();

    $('#timesheets-daterange').on('apply.daterangepicker', update_timesheets);

    $("#timesheets-log_work").on("click", function(){
        edit_request();
    });

    $("#log-work-schedule-autocomplete").autocomplete({
        select: function(e, ui) {
            $('#log-work-todos-autocomplete').val("");
            $('#log-work-todos-id').val("");
            $('#log-work-schedule-autocomplete').val(ui.item.label);
            $('#log-work-schedule-id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            data.alltime = 'yes';
            $.get("/admin/timesheets/autocomplete_schedules",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });

    $("#log-work-todos-autocomplete").autocomplete({
        select: function(e, ui) {
            $('#log-work-schedule-autocomplete').val("");
            $('#log-work-schedule-id').val("");
            $('#log-work-todos-autocomplete').val(ui.item.label);
            $('#log-work-todos-id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/todos/autocomplete_todos",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });

    $("#timesheet-edit-modal .btn-primary").on("click", function(){
        var $modal = $("#timesheet-edit-modal");
        var data = {};
        data.id = $modal.find("#timesheet-edit_timesheet_id").val();
        data.description = $modal.find("#timesheets-log_work-description").val();
        data.staff_id = $modal.find("#timesheets-log_work-person").val();
        data.schedule_id = $modal.find("#log-work-schedule-id").val();
        data.todo_id = $modal.find("#log-work-todos-id").val();
        data.status = "pending";
        data.type = data.schedule_id != "" ? "course" : "internal";
        data.start_date = $("#timesheets-log_work-start_date").val();
        data.end_date = $("#timesheets-log_work-period").is(":checked") ? $("#timesheets-log_work-end_date").val() : data.start_date;
        data.duration = parse_duration($("#timesheets-log_work-worked").val());
        if (data.end_date == "") {
            data.end_date = data.start_date;
        }

        submit_request(data, function(){
            if ($(".log-another input").prop("checked")) {
                edit_request();
            } else {
                $("#timesheet-edit-modal").modal('hide');
                update_timesheets();
            }
        });
    });

    function submit_request(data, callback)
    {
        $.post(
            "/api/timesheets/submit",
            data,
            function (response) {
                if (response.status == "success") {
                    if (callback) {
                        callback();
                    } else {
                        $("#timesheet-edit-modal").modal('hide');
                        update_timesheets();
                    }
                }
            }
        )
    }

    $("#timesheets-log_types").on("change", function(){
        update_timesheets();
    });

    if ($(".timesheet-view.overview").length > 0) {
        $(".timesheet-view.overview").click();
    } else {
        display_overview();
    }

    $("#timesheets-department").on("change", function(){
        update_timesheets();
    });

    $(".view-timesheet").on("click", function(){
        if ($("#timesheet-details-list").hasClass("hidden")) {
            $("#timesheet-details-list").removeClass("hidden");
        } else {
            $("#timesheet-details-list").addClass("hidden");
        }
    });

    $("#timesheets-log_work-period").on("change", function(){
        $(".worked-label").addClass("hidden");
        if (this.checked) {
            $("#timesheets-log-work-end_date-wrapper-input").removeClass("hidden");
            $(".worked-label.multiple").removeClass("hidden");
        } else {
            $("#timesheets-log-work-end_date-wrapper-input").addClass("hidden");
            $(".worked-label.single").removeClass("hidden");
        }
    });

    $("[name=grid_period]").on("click", function(){
        $("#timeoff-grid_period-button").html($("[name=grid_period]:checked").next().html());
        update_timesheets();
    });

    var search_input_delay = 0;
    $("#timesheets_table-search").on("keyup", function(){
        clearTimeout(search_input_delay);
        search_input_delay = setTimeout(
            function(){
                update_timesheets();
            },
            250
        );
    });

    $("#details-export-csv").on("click", function(){

    });

    function error_message(error)
    {
        $("#timeoff-error-modal-message").html(error);
        $("#timeoff-error-modal").modal();
    }
});