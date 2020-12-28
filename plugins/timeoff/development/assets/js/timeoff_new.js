function fmt_date(str)
{
    if (str) {
        return moment(str).format('D/MMM');
    }
    return "";
}

function fmt_duration(duration) {
    var hours, minutes;
    if (!duration) {
        return '';
    } else if (typeof duration == 'object') {
        hours = Math.floor(duration.minutes / 60);
        minutes = duration.minutes % 60;
    } else {
        hours   = Math.floor(duration / 60);
        minutes = Math.floor(duration % 60);
    }

    return (hours == 0 ? '' : hours+'h ') + (minutes == 0 ? '' : minutes+'m');
}

function time_format(minutes, day_length_minutes, forceDayUnit)
{
    var days = Math.floor(minutes / day_length_minutes);
    var hours = Math.round((minutes % day_length_minutes) / 60);
    if (hours < 1) {
        return days.toString() + (forceDayUnit ? 'd' : '');
    } else {
        return days.toString() + 'd ' + hours.toString() + 'h';
    }

}

function update_screen()
{
    if ($("#display_details").hasClass("btn-primary")) {
        display_details();
    } else {
        display_overview();
    }
}

function update_stats()
{
    var contact_id = $("#filterStaffId").val();
    var department_id = $("#timeoff-department").val();
    var params = {};

    // Stats apply to the entire year. e.g. if you are just viewing March, you still see the "days left" for the entire year.
    params.period_start_date = $('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-01-01') || new Date($('#timeoff-daterange-selector-start_date').val()).dateFormat('Y-01-01');
    params.period_end_date   = $('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-12-31') || new Date($('#timeoff-daterange-selector-start_date').val()).dateFormat('Y-12-31');

    params.level = department_id ? 'department' : 'contact';
    params.level_id = department_id ? department_id : contact_id;
    if (params.level_id == "") {
        params.level = 'department';
    }
    get_stats(
        params,
        function(data){
            $(".timeoff-report.days_available .timeoff-report-amount").html(time_format(data.days_available, data.day_length_minutes));
            $(".timeoff-report.days_pending_approval .timeoff-report-amount").html(time_format(data.days_pending_approval, data.day_length_minutes));
            $(".timeoff-report.days_in_lieu .timeoff-report-amount").html(time_format(data.days_in_lieu, data.day_length_minutes));
            $(".timeoff-report.days_approved .timeoff-report-amount").html(time_format(data.days_approved, data.day_length_minutes));
            $(".timeoff-report.days_left .timeoff-report-amount").html(time_format(data.days_left, data.day_length_minutes));

            $("#timeoff-days-remaining").html(time_format(data.days_available + data.days_in_lieu - data.days_approved, data.day_length_minutes, true))
            $("#timeoff-days-total").html(time_format(data.days_available, data.day_length_minutes, true))

            $(".timeoff-report .timeoff-report-period").html($('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY'));
        }
    )
}

function get_stats(params, callback)
{
    var url = '/api/timeoff/stats';
    $.get(
        url,
        params,
        function (data) {
            callback(data);
        }
    )
}

var overview_params = {
    offset: 0,
    limit: 10,
    order_by: 'period_start_date',
    order_dir: 'asc',
    period_end_date: null,
    period_start_date: null,
    period_type: 'days',
    text: '',

};
var $requests_row_template = $("#timeoff_requests_table").find(".request_row").first();
$requests_row_template.remove();
$requests_row_template.removeClass("hidden");
function display_overview()
{
    $("#display_details").removeClass("btn-primary");
    $("#display_overview").addClass("btn-primary");

    $("#timeoff-overview").removeClass("hidden");
    $("#timeoff-details").addClass("hidden");

    $(".timeoff-details-options").addClass("hidden");

    $(".timeoff-request_filters").children('.form-filter').removeClass('col-sm-6 pl-1 pr-1');


    update_stats();

    var filters = [];
    $('.form-filter-selected-field').each(function(i, element) {
        if (element.name) {
            if (filters[element.name]) {
                filters[element.name].push(element.value);
            } else {
                filters[element.name] = [element.value];
            }
        }
    });

    overview_params.period_start_date = $('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    overview_params.period_end_date = $('#timeoff-daterange-selector').data('daterangepicker').endDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    overview_params.limit = parseInt($("#timeoff-requests .pagination-limit").val());
    overview_params.department_id = filters['department_id[]'] || [];
    overview_params.staff_id = filters['staff_id[]'] || [];
    overview_params.text = $("#timesheets_table-search").val();
    overview_params.status = filters['status[]'] || [];
    overview_params.type = filters['type[]'] || [];
    overview_params.order_by = 'manager_updated_at';
    overview_params.order_dir = 'desc';
    //$("#timesheets_params").val();

    $.get(
        '/api/timeoff',
        overview_params,
        function (response) {
            var tbody = $("#timeoff_requests_table tbody");
            tbody.empty();
            $("#timeoff-requests").removeClass("hidden");
            for (var i in response.items) {
                var $row = $requests_row_template.clone();
                $row.find("td")[0].innerHTML = response.items[i].staff ? response.items[i].staff.id : "";
                $row.find("td")[1].innerHTML = response.items[i].staff ? response.items[i].staff.name : "";
                $row.find("td")[2].innerHTML = response.items[i].department ? response.items[i].department.name : "";
                $row.find("td")[3].innerHTML = response.items[i].staff && response.items[i].staff.position ? response.items[i].staff.position : "";
                $row.find("td")[4].innerHTML = fmt_date(response.items[i].period[0]);
                $row.find("td")[5].innerHTML = fmt_date(response.items[i].period[1]);
                $row.find("td")[6].innerHTML = response.items[i].type;
                $row.find("td")[7].innerHTML = fmt_duration(response.items[i].period[2]);
                $row.find("td")[8].innerHTML = response.items[i].status;
                $row.find("td")[9].innerHTML = response.items[i].manager_updated_at;
                $row.find("button.view").attr("data-request_id", response.items[i].id);
                tbody.append($row.clone());
            }

            tbody.find("button.view").on("click", function(){
                open_request_modal($(this).attr("data-request_id"));
                return false;
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

function display_pagination(selector, offset, limit, total, click_handler)
{
    if (total == 0) {
        $("#timeoff-requests " + ".no_records").removeClass("hidden");
        $("#timeoff-requests " + ".pagination-wrapper").addClass("hidden");
    } else {
        $("#timeoff-requests " + ".no_records").addClass("hidden");
        $("#timeoff-requests " + ".pagination-wrapper").removeClass("hidden");

        $("#timeoff-requests " + ".pagination-wrapper .from").html((offset + 1));
        $("#timeoff-requests " + ".pagination-wrapper .to").html((Math.min(limit + offset, total)));
        $("#timeoff-requests " + ".pagination-wrapper .total").html(total);

        var $ul = $("#timeoff-requests " + ".pagination-wrapper ul");

        $ul.find(".page").remove();

        var page_count = Math.ceil(total / limit);
        for (var i = 0 ; i < page_count ; ++i) {
            $('<li class="page' + (offset == i * limit ? ' active ' : '') + '"><button type="button" data-offset="' + (i*limit) + '">' + (i + 1) + '</button></li>').insertBefore($ul.find(".next"));
        }

        $ul.find("button").on("click", click_handler);
    }
}
var last_requests = null;
function display_details()
{
    $("#display_details").addClass("btn-primary");
    $("#display_overview").removeClass("btn-primary");

    $("#timeoff-overview").addClass("hidden");
    $("#timeoff-details").removeClass("hidden");

    $(".timeoff-details-options").removeClass("hidden");

    $(".timeoff-request_filters").children('.form-filter').addClass('col-sm-6 pl-1 pr-1');

    update_stats();

    var params = {
        offset: 0,
        limit: null,
        order_by: 'period_start_date',
        order_dir: 'asc',
    }

    var filters = [];
    $('.form-filter-selected-field').each(function(i, element) {
        if (element.name) {
            if (filters[element.name]) {
                filters[element.name].push(element.value);
            } else {
                filters[element.name] = [element.value];
            }
        }
    });

    params.period_type = $("[name=grid_period]:checked").val();
    params.period_start_date = $('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    params.period_end_date = $('#timeoff-daterange-selector').data('daterangepicker').endDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    params.department_id = null;
    params.staff_id = null;
    params.type = $('#leave_type_filter').val();
    if ($("#timeoff-department").val()) {
        params.department_id = $("#timeoff-department").val();
    } else {
        params.staff_id = $("#timeoff-department").val();
    }
    params.department_id = filters['department_id[]'] || [];
    params.staff_id = filters['staff_id[]'] || [];
    overview_params.status = filters['status[]'] || [];
    params.type = filters['type[]'] || [];

    $("#timeoff-grid_period-button").html($("[name=grid_period]:checked").val() == 'days' ? 'Grid view (Days)' : 'Grid view (Weeks)');

    $.get(
        '/api/timeoff/details',
        params,
        function (response) {
            $("#timeoff-details-table").html(response);
            $('.timeoff-details-table-request').on('click', openRequests);
        }
    );

    overview_params.limit = null;
    $.get(
        '/api/timeoff',
        params,
        function (response) {
            last_requests = response;
        }
    );
}

var $select_request_row = $(".select-request-row.hidden");
$select_request_row.removeClass("hidden");
$select_request_row.remove();

function openRequests()
{
    var requests = $(this).data('request_ids');

    $("#select-request").modal();
    $("#select-request table tbody").html("");

    for (var i = 0 ; i < requests.length ; ++i) {
        var id = requests[i];
        for (var l = 0 ; l < last_requests.items.length ; ++l) {
            if (last_requests.items[l].id == id) {
                var $row = $select_request_row.clone();
                $row.find("td")[0].innerHTML = fmt_date(last_requests.items[l].period[0]);
                $row.find("td")[1].innerHTML = fmt_date(last_requests.items[l].period[1]);
                $row.find("td")[2].innerHTML = last_requests.items[l].type;
                $row.find("td")[3].innerHTML = fmt_duration(last_requests.items[l].period[2]);
                $row.find("td")[4].innerHTML = last_requests.items[l].status;
                $row.find("td")[5].innerHTML = last_requests.items[l].status == "approved" ? last_requests.items[l].manager_updated_at : "";
                $row.find(".view").attr("data-request_id", id);
                $("#select-request table tbody").append($row);
            }
        }
    }

    $("#select-request .view").on("click", function(){
        open_request_modal($(this).attr("data-request_id"));
        return false;
    });
}

function load_request_data(id, callback)
{
    if (id) {
        $.get('/api/timeoff/getrequest?id=' + id)
            .done(function (data) {
                callback(data);
            })
            .fail(function () {
                console.log('Error retrieving request data');
                callback(null);
            });
    } else {
        callback(null);
    }
}

function open_request_modal(id, leave_type)
{
    $("#conflict-requests").addClass("hidden");
    $("#timeoff-request-modal-request_conflicts-none").removeClass("hidden");
    $("#conflict-requests tbody").html();

    $("#timeoff-request-modal-schedule_conflicts-section").addClass("hidden");
    $("#timeoff-request-modal-schedule_conflicts-none").addClass("hidden");
    $("#timeoff-request-modal-schedule_conflicts tbody").html("");
    // Get data for the selected request
    $('#timeoff-request-modal-form').attr("data-staff_id", "");
    load_request_data(
        id,
        function(data) {
            var $modal = $('#timeoff-request-modal');
            var $edit_actions     = $modal.find('.timeoff-edit-actions');
            var $readonly_actions = $modal.find('.timeoff-readonly-actions');
            var $editable_fields  = $('#timeoff-request-modal-form').find(':input');

            $("#timeoff-request-modal-title").html('Submit a request');
            if (data) {

                $("#timeoff-request-modal-title").text('View request from '+data.staff.name);
                $edit_actions.toggleClass('hidden', !data.can_edit);
                $readonly_actions.toggleClass('hidden', data.can_edit);
                $editable_fields.prop('disabled', !data.can_edit);

                // Fill in fields with the request's data

                // Different text in the heading between new and existing requests
                $modal.find('.hidden--existing').addClass('hidden');
                $modal.find('.hidden--new').removeClass('hidden');

                var start_date = '', end_date = '', start_time = '', end_time = '';

                if (data.period && data.period[0]) {
                    start_date = data.period[0].split(' ')[0];
                    start_time = data.period[0].split(' ')[1].substr(0, 5);
                }

                if (data.period && data.period[1]) {
                    end_date = data.period[1].split(' ')[0];
                    end_time = data.period[1].split(' ')[1].substr(0, 5);
                }

                var has_range = start_date != end_date;
                if (data.staff && data.staff.id) {
                    $('#timeoff-request-modal-form').attr("data-staff_id", data.staff.id);
                }
                $('#timeoff-request-modal-id').val(data.id);
                if (data.department){
                    $('#timeoff-request-modal-department').val(data.department.id);
                }
                $('#timeoff-request-modal-type').val(data.type);
                $('#timeoff-request-modal-date').val(start_date).trigger('change');
                $('#timeoff-request-modal-date_range-toggle').prop('checked', has_range).trigger('change');
                $('#timeoff-request-modal-end_date').val(end_date).trigger('change');
                $('#timeoff-request-modal-start_time').val(start_time);
                $('#timeoff-request-modal-end_time').val(end_time);
                request_date_changed();
                $("#timeoff-request-modal-status").val(data.status);
                var notes = "";
                for (var i in data.notes) {
                    notes += data.notes[i].content + "\r\n";
                }
                $('#timeoff-request-modal-staff_note').val(notes);
                $('#timeoff-request-modal-status').val(data.status);
            } else {
                $edit_actions.removeClass('hidden');
                $readonly_actions.addClass('hidden');
                $editable_fields.prop('disabled', false);
                $('#timeoff-request-modal-department').prop('disabled', $('#timeoff-role').val() == 'staff');

                $('#timeoff-request-modal-id').val("");
                // Different text in the heading between new and existing requests
                $modal.find('.hidden--new').addClass('hidden');
                $modal.find('.hidden--existing').removeClass('hidden');

                leave_type = leave_type || '';
                $modal.find('form')[0].reset();

                // Default the request period to today's hours
                var today = new moment().format('YYYY-MM-DD');
                $.ajax({
                    url: '/api/timeoff/get_day_config',
                    data: {
                        date: today,
                        department_id: $('#timeoff-request-modal-department').val()
                    }
                }).done(function(data) {
                    data = JSON.parse(data);
                    if (data) {
                        $('#timeoff-request-modal-date').val(today).trigger('change');
                        $('#timeoff-request-modal-date_range-toggle').prop('checked', false).trigger('change');
                        $('#timeoff-request-modal-end_date').val('').trigger('change');
                        $('#timeoff-request-modal-start_time').val(data.start_time.substr(0, 5));
                        $('#timeoff-request-modal-end_time').val(data.end_time.substr(0, 5));
                        $('#timeoff-request-modal-duration_formatted').val(data.value);
                    }
                });
            }

            // Some fields do not apply to "time in lieu"
            if (leave_type && leave_type.toLowerCase() == 'time in lieu') {
                $modal.find('.hidden--time_in_lieu').addClass('hidden');
            } else {
                $modal.find('.hidden--time_in_lieu').removeClass('hidden');
            }
            $modal.find('.form-input :input').trigger('change');
            $modal.modal();
        }
    );
}

function close_request_modal()
{
    $("#timeoff-request-modal").modal("hide");
}

function request_date_changed()
{
    $("#timeoff-request-modal-duration_formatted").val("");

    var type = $('#timeoff-request-modal-type').val();
    var start = $("#timeoff-request-modal-date-input").val();
    var end = "";
    if (start == "") {
        return;
    }
    if ($("#timeoff-request-modal-date_range-toggle").prop("checked") && $("#timeoff-request-modal-date-input").val() != $("#timeoff-request-modal-end_date-input").val()) {
        start = moment(start).format("YYYY-MM-DD");
        end = $("#timeoff-request-modal-end_date-input").val();
        if (end == "") {
            return;
        }
        end = moment(end).toDate();
        end.setDate(end.getDate() + 1);
        end = moment(end).format("YYYY-MM-DD");
    } else {
        start = end = moment(start).format("YYYY-MM-DD");

        if (type == 'lieu' || ($("#timeoff-request-modal-start_time").val() != '' && $("#timeoff-request-modal-start_time").val() != '00:00')) {
            start += " " + $("#timeoff-request-modal-start_time").val();
            end += " " + $("#timeoff-request-modal-end_time").val();
        }
    }

    var department_id = $("#timeoff-request-modal-department").val();

    calculate_duration(
        start,
        end,
        department_id,
        type,
        function (response){
            $("#timeoff-request-modal-duration_formatted").val(fmt_duration(response.minutes));
            $("#timeoff-request-modal-duration_formatted").attr("data-minutes", response.minutes);
        }
    );

    check_conflict_schedules();
    check_conflict_requests();
}

var $conflict_request_row = $(".conflict-request-row.hidden");
$conflict_request_row.removeClass("hidden");
$conflict_request_row.remove();

function check_conflict_requests()
{
    $("#conflict-requests tbody").html("");

    var params = {
        offset: 0,
        limit: null,
        order_by: 'period_start_date',
        order_dir: 'asc',
        period_type: 'days',
        text: '',

    };

    var start = $("#timeoff-request-modal-date-input").val();
    var end = "";
    if (start == "") {
        return;
    }
    start = moment(start).format("YYYY-MM-DD");
    if ($("#timeoff-request-modal-date_range-toggle").prop("checked")) {
        end = $("#timeoff-request-modal-end_date-input").val();
        if (end == "") {
            return;
        }
        end = moment(end).format("YYYY-MM-DD");
    } else {
        end = start;
        end = moment(end).format("YYYY-MM-DD");


        start += " " + $("#timeoff-request-modal-start_time").val();
        end += " " + $("#timeoff-request-modal-end_time").val();
    }

    params.period_start_date = start;
    params.period_end_date = end;
    params.staff_id = $("#filterStaffId").val();
    if (isNaN(parseInt(params.staff_id))) {
        params.staff_id = $('#timeoff-request-modal-form').attr("data-staff_id");
    }
    params.exclude_id = $('#timeoff-request-modal-id').val();
    console.log(params);
    $.get(
        '/api/timeoff',
        params,
        function (response) {
            $("#conflict-requests tbody").html("");

            if (response.items.length > 0) {
                $("#conflict-requests").removeClass("hidden");
                $("#timeoff-request-modal-request_conflicts-none").addClass("hidden");
            } else {
                $("#timeoff-request-modal-request_conflicts-none").removeClass("hidden");
                $("#conflict-requests").addClass("hidden");
            }
            for (var i = 0 ; i < response.items.length ; ++i) {
                var $row = $conflict_request_row.clone();
                $row.find("td")[0].innerHTML = fmt_date(response.items[i].period[0]);
                $row.find("td")[1].innerHTML = fmt_date(response.items[i].period[1]);
                $row.find("td")[2].innerHTML = response.items[i].type;
                $row.find("td")[3].innerHTML = fmt_duration(response.items[i].period[2]);
                $row.find("td")[4].innerHTML = response.items[i].status;
                $row.find("td")[5].innerHTML = response.items[i].status == "approved" ? response.items[i].manager_updated_at : "";
                $("#conflict-requests tbody").append($row);
            }
        }
    );
}

var $conflict_schedule_row = $("#schedule-conflict-template tr");
$conflict_schedule_row.removeClass("hidden");
$conflict_schedule_row.remove();

function check_conflict_schedules()
{
    $("#timeoff-request-modal-schedule_conflicts tbody").html("");

    var params = {
        offset: 0,
        limit: null,
        order_by: 'period_start_date',
        order_dir: 'asc',
        period_type: 'days',
        text: '',

    };

    var start = $("#timeoff-request-modal-date-input").val();
    var end = "";
    if (start == "") {
        return;
    }
    start = moment(start).format("YYYY-MM-DD");
    if ($("#timeoff-request-modal-date_range-toggle").prop("checked")) {
        end = $("#timeoff-request-modal-end_date-input").val();
        if (end == "") {
            return;
        }
        end = moment(end).format("YYYY-MM-DD");
    } else {
        end = start;
        end = moment(end).format("YYYY-MM-DD");


        start += " " + $("#timeoff-request-modal-start_time").val();
        end += " " + $("#timeoff-request-modal-end_time").val();
    }

    params.period_start_date = start;
    params.period_end_date = end;
    params.staff_id = $("#filterStaffId").val();

    $.get(
        '/api/timeoff/schedule_conflicts',
        params,
        function (response) {
            if (response.items.length > 0) {
                $("#timeoff-request-modal-schedule_conflicts-section").removeClass("hidden");
                $("#timeoff-request-modal-schedule_conflicts-none").addClass("hidden");
            } else {
                $("#timeoff-request-modal-schedule_conflicts-none").removeClass("hidden");
                $("#timeoff-request-modal-schedule_conflicts-section").addClass("hidden");
            }
            $("#timeoff-request-modal-schedule_conflicts tbody").html("");
            for (var i = 0 ; i < response.items.length ; ++i) {
                var $row = $conflict_schedule_row.clone();
                $row.find("td")[0].innerHTML = response.items[i].id;
                $row.find("td")[1].innerHTML = response.items[i].title;
                $row.find("td")[2].innerHTML = response.items[i].date;
                $row.find("td")[3].innerHTML = response.items[i].time;
                $row.find("td")[4].innerHTML = response.items[i].course;
                $("#timeoff-request-modal-schedule_conflicts tbody").append($row);
            }
        }
    );
}

function calculate_duration(start, end, department_id, type, callback)
{
    $.get(
        "/api/timeoff/duration",
        { period_start_date :start, period_end_date: end, department_id: department_id, type: type },
        function (response) {
            callback(response);
        }
    )
}

function export_csv()
{
    var params = {
        offset: 0,
        limit: null,
        order_by: 'period_start_date',
        order_dir: 'asc',
        mode: 'csv'
    }

    params.period_type = $("[name=grid_period]:checked").val();
    params.period_start_date = $('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    params.period_end_date = $('#timeoff-daterange-selector').data('daterangepicker').endDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val();
    params.department_id = null;
    params.staff_id = null;
    if ($("#timeoff-department").val()) {
        params.department_id = $("#timeoff-department").val();
    } else {
        params.staff_id = $("#timeoff-department").val();
    }


    var url = '/api/timeoff/details?offset=0&limit=&mode=csv';
    url += '&period_type=' + $("[name=grid_period]:checked").val();
    url += '&period_start_date=' + ($('#timeoff-daterange-selector').data('daterangepicker').startDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val());
    url += '&period_end_date=' + ($('#timeoff-daterange-selector').data('daterangepicker').endDate.format('YYYY-MM-DD') || $('#timeoff-daterange-start_date').val());
    if ($("#timeoff-department").val()) {
        url += '&department_id=' + $("#timeoff-department").val();
    } else {
        url += '&staff_id' + $("#timeoff-department").val();
    }

    location.href = url;
}

function request_modal_date_range_toggle()
{
    if ($("#timeoff-request-modal-date_range-toggle").prop("checked")) {
        $("#timeoff-request-modal-time_range").addClass("hidden");
        $("#timeoff-request-modal-end_date-wrapper").removeClass("hidden");
    } else {
        $("#timeoff-request-modal-time_range").removeClass("hidden");
        $("#timeoff-request-modal-end_date-wrapper").addClass("hidden");
    }
}

function submit_request()
{
    if (!$('#timeoff-request-modal-form').validationEngine('validate')) {
        return false;
    }

    var data = {};
    data.request = {id: $("#timeoff-request-modal-id").val()};
    data.note = $("#timeoff-request-modal-staff_note").val();
    if (data.request.id == "") {
        data.request.status = "pending";
    } else {
        data.request.status = $("#timeoff-request-modal-status").val();
    }
    data.request.type = $("#timeoff-request-modal-type").val();
    data.request.department = {id: $("#timeoff-request-modal-department").val()};
    data.request.period = [];

    var start = $("#timeoff-request-modal-date").val();
    var end = "";
    if ($("#timeoff-request-modal-date_range-toggle").prop("checked")) {
        end = $("#timeoff-request-modal-end_date").val();
        end = moment(end).format("YYYY-MM-DD");

        if (start == end) {
            var minutes = parseInt($("#timeoff-request-modal-duration_formatted").attr("data-minutes"));
            var hour = 9 + Math.floor(minutes / 60);
            minutes = minutes % 60;
            end = start + " " + hour + ":" + minutes;
            start = start + " 09:00";
        }
    } else {
        start = end = moment(start).format("YYYY-MM-DD");

        start += " " + $("#timeoff-request-modal-start_time").val();
        end += " " + $("#timeoff-request-modal-end_time").val();
    }

    data.request.period.push(start);
    data.request.period.push(end);
    data.request.period.push(0);

    $.ajax(
        {
            type: "POST",
            //the url where you want to sent the userName and password to
            url: "/api/timeoff/" + (data.request.id == "" ? "submit" : "save"),
            dataType: "json",
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                close_request_modal();
                update_screen();

                console.log(response);

                if (response && response.status == 'error') {
                    $('#page-wrapper').add_alert(response.error, 'danger popup_box');
                }
            }
        }
    )
}

function approve_request()
{
    $.post(
        '/api/timeoff/approve',
        {
            id: $("#timeoff-request-modal-id").val(),
            note: $("#timeoff-request-modal-staff_note").val()
        },
        function (response) {
            close_request_modal();
            update_screen();
        }
    )
}

function decline_request()
{
    $.post(
        '/api/timeoff/decline',
        {
            id: $("#timeoff-request-modal-id").val(),
            note: $("#timeoff-request-modal-staff_note").val()
        },
        function (response) {
            close_request_modal();
            update_screen();
        }
    );
}

$(document).on("ready", function(){

    $('#timeoff-make_request').on('click', function() {
        open_request_modal(null, null);
    });

    $("#timeoff-request-modal-date, #timeoff-request-modal-end_date-input, #timeoff-request-modal-start_time, #timeoff-request-modal-end_time, #timeoff-request-modal-department").on("change", request_date_changed);

    $("#timeoff-department, #timeoff-request_status, #timesheets_table-search, #timeoff-request-modal-date_range-toggle").on("change", update_screen);


    $('#timeoff-daterange-selector').on('apply.daterangepicker', update_screen);

    $("#display_overview").on("click", display_overview);
    $("#display_details").on("click", display_details);
    $("[name=grid_period]").on("change", display_details);
    $(document).on(':ib-form-filter-change', update_screen);
    $("#export-csv").on("click", export_csv);
    $("#timeoff-request-modal .btn-cancel").on("click", close_request_modal);
    $("#timeoff-request-modal-date_range-toggle").on("change", request_modal_date_range_toggle);
    $("#timeoff-request-modal-submit, #timeoff-request-modal-save").on("click", submit_request);
    $("#timeoff-request-modal-approve").on("click", approve_request);
    $("#timeoff-request-modal-decline").on("click", decline_request);
    display_overview();
    $('.timepicker').datetimepicker({datepicker:false, format:'H:i'});
});
