var day_selector = [];
var custom_repeat = [];
var saving_timetable = false;
var myMinTime, myMaxTime, myInterval;
var timeslots_cache = [];
var timeslots_display_limit = 10;
var timeslots_display_page = 0;
var clear_on_generateslots = true;
var check_navision_date = true;
update_results_count();

function render_timeslots(timeslots, offset, limit)
{
    var schedule_id = parseInt($("#id, #schedule_id").val());
    var dayNames = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday"
    ];
    var trainers = $("#trainer_id").html();
    var rooms = $("#all_locations").html();
    var topics = $("#edit_schedule-topics-add").html();

    var tbody = $("#selected_dates table > tbody")[0];
    var fee_disabled = $("#fee_per").val() != "Timeslot";
    var disabled = $("#fee_amount").prop("disabled");
    if (disabled) {
        fee_disabled = true;
    }

    tbody.innerHTML = '';

    var max_capacity = parseInt($("#max_capacity").val());
    if (isNaN(max_capacity)) {
        max_capacity = "";
    }
    for (var i = offset ; i < (offset + limit)  && i < timeslots.length ; ++i) {
        if (timeslots[i].delete == 1) {
            continue;
        }
        var tr = document.createElement("tr");
        var dt = new Date(clean_date_string(timeslots[i].datetime_start));
        var dt_valid = !isNaN(dt.getDate());
        var dt_end = new Date(clean_date_string(timeslots[i].datetime_end));
        var dt_end_valid = !isNaN(dt.getDate());

        tr.className = "schedule-timeslot-row well well-small";
        tr.setAttribute("data-datetime_start", timeslots[i].datetime_start);
        if (timeslots[i].id) {
            tr.setAttribute("data-timeslot_id", timeslots[i].id);
        }
        tr.setAttribute("data-order", i);

        var td = document.createElement("td");
        td.setAttribute('data-label', 'Order');
        td.innerHTML = (i + 1);
        tr.appendChild(td);

        td = document.createElement("td");
        td.className = "schedule-timeslot-start_day column-date";
        td.setAttribute('data-label', 'Day');
        td.setAttribute("data-day", dt_valid ? dt.dateFormat('d') : '');
        td.innerHTML = dt_valid ? dayNames[dt.getDay()] : '';
        tr.appendChild(td);

        td = document.createElement("td");
        td.className = "schedule-timeslot-start_start_date column-date";
        td.setAttribute('data-label', 'Date');
        td.setAttribute("data-day", timeslots[i].day);
        var input = document.createElement("input");
        input.type = "text";
        input.className = "form-control timeslot_date datepicker";
        input.value = dt_valid ? dt.dateFormat('d-m-Y') : '';
        $(input).datepicker(
            {
                autoclose: true,
                orientation: "auto bottom",
                format: 'dd-mm-yyyy'
            }
        );
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Price');
        input = document.createElement("input");
        input.type = "text";
        input.className = "form-control timeslot_price";
        input.value = timeslots[i].fee_amount ? timeslots[i].fee_amount : "";
        input.disabled = fee_disabled;
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Start time');
        input = document.createElement("input");
        input.type = "text";
        input.className = "form-control timepicker start_time time_range_picker";
        input.value = dt_valid ? (dt.getHours() < 10 ? "0" : "") + dt.getHours() + ":" + (dt.getMinutes() < 10 ? "0" : "") + dt.getMinutes() : '';
        $(input).datetimepicker(
            {
                datepicker : false,
                format: 'H:i',
                formatTime: 'H:i',
                minTime: myMinTime,
                maxTime: myMaxTime,
                step: myInterval
            }
        );
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'End time');
        input = document.createElement("input");
        input.type = "text";
        input.className = "form-control timepicker end_time time_range_picker";
        input.value = dt_end_valid ? (dt_end.getHours() < 10 ? "0" : "") + dt_end.getHours() + ":" + (dt_end.getMinutes() < 10 ? "0" : "") + dt_end.getMinutes() : '';
        $(input).datetimepicker(
            {
                datepicker : false,
                format: 'H:i',
                formatTime: 'H:i',
                minTime: myMinTime,
                maxTime: myMaxTime,
                step: myInterval
            }
        );
        td.appendChild(input);
        tr.appendChild(td);


        td = document.createElement("td");
        td.setAttribute('data-label', 'Trainer');
        input = document.createElement("select");
        input.className = "form-control trainer_select";
        input.innerHTML = trainers;
        var trainer_id = timeslots[i].trainer_id ? timeslots[i].trainer_id : ($("#timetable-planner-add_slot-modalx-contact").length > 0 ? $("#timetable-planner-add_slot-modalx-contact_id").val() : $("#trainer_id").val());
        if (trainer_id) {
            for (var t in input.options) {
                input.options[t].selected = (input.options[t].value == trainer_id);
                timeslots[i].trainer_id = trainer_id;
            }
        }
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Room');
        input = document.createElement("select");
        input.className = "form-control room_select";
        input.innerHTML = rooms;
        var room_id = timeslots[i].location_id ? timeslots[i].location_id : $("#child_location_id").val();
        $(input).val(room_id);
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Monitored');
        input = document.createElement("input");
        input.type = "checkbox";
        input.className = "schedule-timeslot-monitored";
        input.value = 1;
        input.checked = (timeslots[i].monitored == 1);
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Topic');
        input = document.createElement("select");
        input.className = "form-control topic";
        input.innerHTML = topics;
        var topic_id = timeslots[i].topic_id;
        if (topic_id) {
            for (t in input.options) {
                input.options[t].selected = (input.options[t].value == topic_id);
            }
        }
        td.appendChild(input);
        tr.appendChild(td);

        td = document.createElement("td");
        td.className = "max_capacity";
        td.setAttribute('data-label', 'Max capacity');
        input = document.createElement("input");
        input.type = "text";
        input.className = "form-control max_capacity";
        input.value = timeslots[i].max_capacity ? timeslots[i].max_capacity : max_capacity;
        td.appendChild(input);
        if (schedule_id) {
            var span = document.createElement("span");
            if (parseInt(timeslots[i].booking_count) > 0) {
                span.innerHTML = "&nbsp;(" + timeslots[i].booking_count + " / " + (timeslots[i].max_capacity ? timeslots[i].max_capacity : max_capacity) + ")";
                td.appendChild(span);
            }
        }

        tr.appendChild(td);

        td = document.createElement("td");
        td.setAttribute('data-label', 'Delete');
        input = document.createElement("button");
        input.type = "button";
        input.className = "btn-link delete_me";
        input.innerHTML = '<span class="icon-times"></span>';
        td.appendChild(input);
        tr.appendChild(td);
        tbody.appendChild(tr);
    }
}

function clear_dates()
{
	custom_repeat = [];
	$(".eventsCalendar-day").removeClass('day_selected');
	$("#selected_dates").find("tbody tr").each(function () {
		$(this).remove();
	});

    timeslots_cache = [];
    timeslots_display_page = 0;
    update_results_count();
}

function change_zones_table_with_ajax(location_id) {
    $.ajax({
        url: '/admin/courses/get_rows_for_location',
        data: { "location_id": location_id },
        global: false,
        type: 'POST',
        dataType: 'text'
    })
        .success(function (data) {
            if (data == '') {
                data = '<tr><td colspan="3">There are no rows for this location.</td></tr>'

            }
            $("#schedule_zones_table").find("tbody").html(data);

        });
}
$(document).ready(function () {
    var custom_table_settings = {};
    var navision_events_table = $("#navision-events-table");
    navision_events_table.ib_serverSideTable(
        '/admin/navapi/events_datatable?schedule_id=' + $("#id").val(),
        { aaSorting: [[ 1, 'desc']] },
        { responsive: true}
    );
    // Search by individual columns
    navision_events_table.find('.search_init').on('change', function ()
    {
        navision_events_table.dataTable().fnFilter(this.value, navision_events_table.find('tr .search_init').index(this) );
    });

    navision_events_table.find('.search_init').on('keypress', function (e)
    {
        if (e.keyCode == 13){ // enter
            e.preventDefault();
            navision_events_table.dataTable().fnFilter(this.value, navision_events_table.find('tr .search_init').index(this) );
            return false;
        }
    });

    // Keep things in sync when changing section via the tabs or the mobile dropdown menu
    $('#edit_schedule-section_toggle li, #edit_schedule-tabs li').click(function() {
        $('#edit_schedule-section_toggle li, #edit_schedule-tabs li').removeClass('active');
        $('#edit_schedule-section_toggle-text').text($(this).text());
    });

    $("#booking_type").on("change", function(){
        if (this.value == "Subscription") {
            $("#payment_type").val(2);
            $("#fee_per").val("Month");
            $("#payg_period").val("month");
            $("#payg_period-wrapper").removeClass('hidden');
            $("#payg_apply_fees_when_absent_div").removeClass("hidden");
        }
    });

    $("#fee_per").on("change", function(){
        if (this.value == "Month") {
            $("#payg_period").val("month");
        }
    });

    $("#payg_period").on("change", function(){
        if (this.value == "month") {
            $("#fee_per").val("Month");
        }
    });

    $("#max_capacity").on("change", function(){
        render_timeslots(timeslots_cache, 0, timeslots_display_limit);
    });

    $("[name=is_interview]").on("change", function(){
        if ($("[name=is_interview]:checked").val() == "YES") {
            $("#has_course_ids_selector").removeClass("hidden");
            $("#course_id_selector").addClass("hidden");
        } else {
            $("#has_course_ids_selector").addClass("hidden");
            $("#course_id_selector").removeClass("hidden");
        }
    });

    $("#selected_dates table tbody").on("change", ".trainer_select", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].trainer_id = this.value;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".timeslot_price", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].fee_amount = this.value;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".schedule-timeslot-monitored", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].monitored = this.checked ? 1 : 0;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".topic", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].topic_id = this.value;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", "input.max_capacity", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].max_capacity = this.value;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
                break;
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".timeslot_date", function(){
        if (this.value == "") {
            return;
        }
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                // Update the start and end dates when the date is changed
                var new_date = $tr.find('.timeslot_date').datepicker('getDate').dateFormat('Y-m-d');

                var start_time = new_date + ' ' + $tr.find('.start_time').val() + ':00';
                var end_time   = new_date + ' ' + $tr.find('.end_time').val() + ':00';

                timeslots_cache[index].datetime_start = start_time;
                timeslots_cache[index].datetime_end = end_time;
                $tr.data("datetime_start", start_time);
                $tr.data("datetime_end", end_time);
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".room_select", function(){
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                timeslots_cache[index].location_id = this.value;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".start_time", function(){
        // Only continue, if this is a valid time
        if (this.value == '' || /^\d?\d:\d\d$/.test(this.value) == false) {
            return;
        }
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                var new_dt = $tr.find('.timeslot_date').datepicker('getDate').dateFormat('Y-m-d') + ' ' + this.value + ':00';

                timeslots_cache[index].datetime_start = new_dt;
                $tr.data("datetime_start", new_dt);
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $("#selected_dates table tbody").on("change", ".end_time", function(){
        if (this.value == '' || /^\d?\d:\d\d$/.test(this.value) == false) {
            return;
        }
        var $tr   = $(this).parents('tr');
        var change_index = $tr.data('order');
        for (var index in timeslots_cache) {
            if (change_index == index) {
                var new_dt = $tr.find('.timeslot_date').datepicker('getDate').dateFormat('Y-m-d') + ' ' + this.value + ':00';

                timeslots_cache[index].datetime_end = new_dt;
                if (timeslots_cache[index].id) {
                    timeslots_cache[index].update = 1;
                }
            }
        }
    });

    $('#start_date').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        format: 'dd-mm-yyyy'
    });

    $('#end_date').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        format: 'dd-mm-yyyy'
    });

    $('#start_date').on("change", startDateChanged);
    $('#end_date').on("change", endDateChanged);


    $('#payment_type').on('change', function() {
        var hide = (this.value != 2);
        $('#payg_period-wrapper, #payg_apply_fees_when_absent_div').toggleClass('hidden', hide);
    });

    $.get('/admin/courses/get_schedule_min_start',function(data){
        myMinTime = JSON.parse(data);
    });
    $.get('/admin/courses/get_schedule_max_start',function(data){
        myMaxTime = JSON.parse(data);
    });
    $.get('/admin/courses/get_schedule_interval_setting',function(data){
        myInterval = $.parseJSON(data);
        myInterval != '' ? parseInt(myInterval) : 15;
    });

    if (jQuery.validator) {
        jQuery.extend(jQuery.validator.messages, {
            required: "Required!"
        });
    }

	initDateRangePicker(false);
    $(document).on('click','.time_range_picker',function()
    {
        initDateRangePicker(false);
    });
    $(document).on('blur','.time_range_picker, .timepicker',function()
    {
        /* Allow for variants like "9.00" => "09:00" */
        // strip special characters.
        var new_value = this.value.replace(/[^\w]+/g, '');
        // If the remaining string is three or four digits...
        if (/^\d?\d\d\d$/.test(new_value) == true) {
            // Add the colon before the second-last number
            new_value = (new_value.substr(0, new_value.length - 2)+':'+new_value.substr(new_value.length - 2));
            // Add leading zero, if needed
            new_value = new_value.padStart(5, 0);
            this.value = new_value;
            $(this).change();
        }

        /* Reset if still invalid */
        if (/^\d?\d:\d\d$/.test(this.value) == false) {
            alert("Please enter a valid time (HH:mm)");
            this.value = '00:00';
            $(this).change();
            //this.focus();
        }
    });

	var month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];


	var original_timeslots = $('#selected_dates').find('tbody').html(); // timeslots from the initial page load

    $('.day_timetable').hide();
    $('#Monday_tab').hide();
    $('#Tuesday_tab').hide();
    $('#Wednesday_tab').hide();
    $('#Thursday_tab').hide();
    $('#Friday_tab').hide();
    $('#Saturday_tab').hide();
    $('#Sunday_tab').hide();
    if ($('#payment_type').val() == 1)
    {
        $('#rental_fee_display').hide();
    }
    else
    {
        $('#rental_fee_display').show();
    }

    $('.timepicker').datetimepicker({
        datepicker : false,
        format: 'H:i',
        formatTime: 'H:i',
        minTime: myMinTime,
        maxTime: myMaxTime,
        step: myInterval
    });
    // CKEditor Configuration
    if ($("#description").length > 0)
    CKEDITOR.replace('description', {

            // Toolbar settings
            startupFocus: false,
            toolbar: [
                ['Format'],
                ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                ['NumberedList', 'BulletedList'],
                ['Link', 'Unlink', 'Image']
            ],

            // Editor width
            width: '100%'

        }
    );
    if (document.getElementById('form_add_edit_schedule')) {
        $("#form_add_edit_schedule").validate();
    }
    $("#publish_yes").click(function (ev) {
        ev.preventDefault();
        $("#publish").val('1');
    });
    $("#publish_no").click(function (ev) {
        ev.preventDefault();
        $("#publish").val('0');
    });
    $("#zone_management_yes").on('click',function (ev) {
        ev.preventDefault();
        $("#zone_management_yes").val('1');
    });
    $("#zone_management_yes_no").on('click',function (ev) {
        ev.preventDefault();
        $("#zone_management_yes").val('0');
    });
    $("#confirm_yes").click(function (ev) {
        ev.preventDefault();
        $("#is_confirmed").val('1');
    });
    $("#confirm_no").click(function (ev) {
        ev.preventDefault();
        $("#is_confirmed").val('0');
    });
    $("#fee_yes").click(function (ev) {
        ev.preventDefault();
        $("#is_fee_required").val('1');
        $(".edit_schedule-tab-paymentplan").removeClass("hidden");
    });
    $("#fee_no").click(function (ev) {
        ev.preventDefault();
        $("#is_fee_required").val('0');
        $(".edit_schedule-tab-paymentplan").addClass("hidden");
    });

    $("#is_fee_required").on("change", function(){
        if (this.checked) {
            $(".edit_schedule-tab-paymentplan").removeClass("hidden");
        } else {
            $(".edit_schedule-tab-paymentplan").addClass("hidden");
        }
    });
    $("#btn_delete").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        $.post(
            '/admin/courses/schedule_has_booking',
            {
                id: id
            },
            function (response) {
                if (response.status == "success") {
                    $("#cannot_delete").modal();
                } else {
                    $("#btn_delete_yes").data('id', id);
                    $("#confirm_delete").modal();
                }
            }
        );
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/remove_schedule', {id: id}, function (data) {
            if (data.redirect !== '' || data.redirect !== undefined) {
                window.location = data.redirect;
            }
            else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");
    });
    $("#add_frequency").click(function (ev) {
        ev.preventDefault();
        var frequency = $.trim($("#new_frequency").val());
        if (frequency.length < 1) {
            alert("Please enter proper type name!");
            return false;
        }
        $.post("/admin/courses/ajax_add_frequency", {frequency: frequency}, function (data) {
            $.post('/admin/courses/ajax_get_frequencies', function (data) {
                $("#frequency_id").html(data);
                $("#new_frequency").val('');
            }, "text");
        });
        return false;
    });

    $("#create_events").click(function (ev) {
        ev.preventDefault();
        var monday = ($("#weekdays_monday").is(":checked")) ? 1 : 0;
        var tuesday = ($("#weekdays_tuesday").is(":checked")) ? 1 : 0;
        var wednesday = ($("#weekdays_wednesday").is(":checked")) ? 1 : 0;
        var thursday = ($("#weekdays_thursday").is(":checked")) ? 1 : 0;
        var friday = ($("#weekdays_friday").is(":checked")) ? 1 : 0;
        var saturday = ($("#weekdays_saturday").is(":checked")) ? 1 : 0;
        var sunday = ($("#weekdays_sunday").is(":checked")) ? 1 : 0;
        var start = $("#start_hour").val();
        var end = $("#end_hour").val();
        var input_s = '<input type="text" value="' + start + '" class="timepicker" id="start_';
        var input_e = '<input type="text" value="' + end + '" class="timepicker" id="end_';
        var message = '';
        if ($.trim($("#name").val()).length < 1) {
            message = message + '<span class="label label-danger">Important</span>Please insert name for schedule first<br/>';
        }
        if ($.trim($("#course_id").val()).length < 1) {
            message = message + '<span class="label label-danger">Important</span>Please select course for schedule first<br/>';
        }
        if ($.trim($("#start_date").val()).length < 8) {
            message = message + '<span class="label label-danger">Important</span>Please select start date for schedule first<br/>';
        }
        if ($.trim($("#end_date").val()).length < 8) {
            message = message + '<span class="label label-danger">Important</span>Please select end date for schedule first<br/>';
        }

        if (message.length > 0) {
            $("#events-body").html(message);
            $("#ev_title").html('Warning');
            $("#btn_confirm").hide();
            $("#confirm_events").modal();
        }
        else {
            $("#btn_confirm").show();
            if ((monday + tuesday + wednesday + thursday + friday + saturday + sunday) > 0) {
                var html = '';
                if (monday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Mondays events hours<br>' + input_s + 'monday" name="start_monday"/>' + input_e + 'monday" name="end_monday"/></div></div>';
                }
                if (tuesday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Tuesdays events hours<br>' + input_s + 'tuesday" name="start_tuesday"/>' + input_e + 'tuesday" name="end_tuesday"/></div></div>';
                }
                if (wednesday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Wednesdays events hours<br>' + input_s + 'wednesday" name="start_wednesday"/>' + input_e + 'wednesday" name="end_wednesday"/></div></div>';
                }
                if (thursday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Thursdays events hours<br>' + input_s + 'thursday" name="start_thursday"/>' + input_e + 'thursday" name="end_thursday"/></div></div>';
                }
                if (friday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Fridays events hours<br>' + input_s + 'friday" name="start_friday"/>' + input_e + 'friday" name="end_friday"/></div></div>';
                }
                if (saturday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Saturdays events hours<br>' + input_s + 'saturday" name="start_saturday"/>' + input_e + 'saturday" name="end_saturday"/></div></div>';
                }
                if (sunday === 1) {
                    html = html + '<div class="form-group"><div class="controls">Sundays events hours<br>' + input_s + 'sunday" name="start_sunday"/>' + input_e + 'sunday" name="end_sunday"/></div></div>';
                }
                $("#events-body").html(html);
                $("#ev_title").html('Events configuration');
                $("#confirm_events").modal();
            }
            else {
                alert("Please select days of week first!");
            }
        }
        return false;
    });

    $(document).on('change','#course_id',function(){
        var grinds = $(this).find(':selected').data('grinds');
        if ( grinds == 1)
        {
            if ($("#course_id").data("edit_all") == 1) {
                $('#payment_type > option[value="2"]').prop('selected', true);
            } else {
                $('#payment_type > option[value="1"]').prop('selected', true);
            }
            $('#rental_fee_display').show();
            if ($('#rental_fee').val() == '') {
                $('#rental_fee').val($('#rental_fee').data('default-value'));
            }
        }
        else
        {
            $('#payment_type > option[value="1"]').prop('selected', true);
            $('#rental_fee_display').hide();
        }
        if ($(this).find(':selected').data('category_start_time') != '')
        {
            $('#start_day_time').val($(this).find(':selected').data('category_start_time'));
            $('#start_day_time').attr('disabled','disabled');
        }
        else
        {
            $('#start_day_time').removeAttr('disabled');
        }
        if ($(this).find(':selected').data('category_end_time') != '')
        {
            $('#end_day_time').val($(this).find(':selected').data('category_end_time'));
            $('#end_day_time').attr('disabled','disabled');
        }
        else
        {
            $('#end_day_time').removeAttr('disabled');
        }

        var $selected_option = $(this).find(':selected');
        if ($(this).data("edit_all") == 0) {
            if ($selected_option.data("schedule_allow_price_override") == "1") {
                $("#fee_yes, #fee_no, #fee_amount, #fee_per").prop("disabled", false);
                $("#fee_yes, #fee_no").parent().parent().prop("disabled", false);
            } else {
                $("#fee_yes, #fee_no, #fee_amount, #fee_per").prop("disabled", true);
                $("#fee_yes, #fee_no").parent().parent().prop("disabled", true);
            }

            $("#fee_amount").val($selected_option.data("schedule_fee_amount"));
            $("#fee_per").val($selected_option.data("schedule_fee_per"));
            $("#is_fee_required").val($selected_option.data("schedule_is_fee_required"));
        }
        if ($("#subject_id").val() == "") {
            $("#subject_id").val($selected_option.data("subject_id"));
        }
    });

    $(document).on('change','#parent_location_id',function()
    {
        $('#location_id').val($(this).val());
        var data =
        {
            id : $(this).val(),
            location_id : $('#location_id').val()
        };
        $.ajax({
            type:'POST',
            data:data,
            url:'/admin/courses/ajax_get_children_location',
        })
            .done(function(output)
            {
                $('#child_location_id').html(output);
            });
    });
    $(document).on('change','#child_location_id',function()
    {
        $('#location_id').val($(this).val());
    });
    $(document).on('change', '#fee_per', function(){
        if (this.value != 'Timeslot') {
            $(".timeslot_price").prop('disabled', true);
        } else {
            var fee_amount = $("#fee_amount").val();
            $(".timeslot_price").prop('disabled', false);
            if (fee_amount) {
                $(".timeslot_price").each(function () {
                    if (this.value == "") {
                        this.value = fee_amount;
                    }
                });
            }
        }
    });


    function createEvents(schedule_id) {
        var monday = ($("#weekdays_monday").is(":checked")) ? 1 : 0;
        var tuesday = ($("#weekdays_tuesday").is(":checked")) ? 1 : 0;
        var wednesday = ($("#weekdays_wednesday").is(":checked")) ? 1 : 0;
        var thursday = ($("#weekdays_thursday").is(":checked")) ? 1 : 0;
        var friday = ($("#weekdays_friday").is(":checked")) ? 1 : 0;
        var saturday = ($("#weekdays_saturday").is(":checked")) ? 1 : 0;
        var sunday = ($("#weekdays_sunday").is(":checked")) ? 1 : 0;
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var start_hour = $("#start_hour").val();
        var end_hour = $("#end_hour").val();
        var start_monday = $("#start_monday").val();
        var end_monday = $("#end_monday").val();
        var start_tuesday = $("#start_tuesday").val();
        var end_tuesday = $("#end_tuesday").val();
        var start_wednesday = $("#start_wednesday").val();
        var end_wednesday = $("#end_wednesday").val();
        var start_thursday = $("#start_thursday").val();
        var end_thursday = $("#end_thursday").val();
        var start_friday = $("#start_friday").val();
        var end_friday = $("#end_friday").val();
        var start_saturday = $("#start_saturday").val();
        var end_saturday = $("#end_saturday").val();
        var start_sunday = $("#start_sunday").val();
        var end_sunday = $("#end_sunday").val();
        $.post('/admin/courses/ajax_create_events', {
            monday: monday,
            tuesday: tuesday,
            wednesday: wednesday,
            thursday: thursday,
            friday: friday,
            saturday: saturday,
            sunday: sunday,
            start_date: start_date,
            end_date: end_date,
            start_hour: start_hour,
            end_hour: end_hour,
            schedule_id: schedule_id,
            start_monday: start_monday,
            end_monday: end_monday,
            start_tuesday: start_tuesday,
            end_tuesday: end_tuesday,
            start_wednesday: start_wednesday,
            end_wednesday: end_wednesday,
            start_thursday: start_thursday,
            end_thursday: end_thursday,
            start_friday: start_friday,
            end_friday: end_friday,
            start_saturday: start_saturday,
            end_saturday: end_saturday,
            start_sunday: start_sunday,
            end_sunday: end_sunday
        }, function (response) {

        }, "json");
        return false;
    }

    $("#btn_confirm").click(function () {
        if ($("#id").val() > 0) {
            var id = $("#id").val();
            createEvents(id);
        }
        else {
            var form_data = $("#form_add_edit_schedule").serialize();
            $.post('/admin/courses/ajax_save_schedule', form_data, function (res) {
                if (res.message === 'success') {
                    var id = res.id;
                    $("#id").val(id);
                    createEvents(id);
                }
                else {
                    alert(res.message);
                }
            }, "json");

        }
        return false;
    });

    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_event', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Event is successfully unpublished.</div>';
                    $("#main").prepend(smg);
                }
                else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Event is successfully published.</div>';
                    $("#main").prepend(smg);
                }
            }
            else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
        }, "json");
    });

    $("#selected_dates").on("click", '.delete_me', function () {
		var $tr   = $(this).parents('tr');
        var day   = $tr.find('.schedule-timeslot-start_day').data('day');
        var month = $tr.find('.schedule-timeslot-start_start_date').data('month');
        var year  = $tr.find('.schedule-timeslot-start_start_date').data('year');

        var index = null;
        var change_index = timeslot_row_index($tr);
        for (var index in timeslots_cache) {
            if (change_index == index) {
                break;
            }
        }
        if (parseInt($tr.data("timeslot_id")) > 0) {
            timeslots_cache[index].delete = 1;
        }
		else {
            timeslots_cache.splice(index, 1);
        }
        $tr.remove();
        //render_timeslots(timeslots_cache, timeslots_display_limit * timeslots_display_page, timeslots_display_limit);
        var smg = '<div class="alert alert-success remove-date"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> The date ' + year + '-' + (month+1) + '-' + day + ' has been removed from the timetable.</div>';
        $('.alert.add-date').remove();
        $('.alert.remove-date').remove();
        $("#main").prepend(smg);

		update_results_count();
	});
    $('.frequency_table').on('click', '.delete_me', function () {
        $(this).parent().remove();
		update_results_count();
    });
    var timetable_dates = [];
    var trainers = [];
    custom_repeat = [];

    $("#datepicker").on("click", '.eventsCalendar-day', function ()
	{
        if ($('#course_id').val() == '' && $("#is_interview_no").prop("checked"))
        {
            $('#course_selection_modal').modal();
        }
        else
        {
            var that = this;
            var data = {id: $('#trainer_id').val()};
            var $datepicker = $('#datepicker');
            var day = $(this).attr('rel');
            var month = $datepicker.attr("data-current-month");
            var year = $datepicker.attr("data-current-year");

            var date = year + '-' + (month+1) + '-' + day;
            var valid = true;
            $.ajax({
                url: '/admin/courses/get_valid_date',
                data: {
                    date: date,
                    course_id: $('#course_id').val()
                },
                type: 'POST',
                dataType: 'json'
            })
                .success(function (result)
                {
                    valid = $.parseJSON(result);
                    if ($(that).hasClass('blackout-date'))
                    {
                        document.getElementById('blackout-date-warning-date').innerHTML = day + ' ' + month_names[month] + ' ' + year;
                        document.getElementById('blackout-date-warning-type').innerHTML = that.getAttribute('data-type');
                        document.getElementById('blackout-date-warning-proceed').setAttribute('data-day', day);
                        $('#blackout-date-warning-modal').modal();
                    }
                    else if (!valid)
                    {
                        document.getElementById('calendar-date-warning-date').innerHTML = day + ' ' + month_names[month] + ' ' + year;
                        document.getElementById('calendar-date-warning-proceed').setAttribute('data-day', day);
                        $('#category-date-warning-modal').modal();
                    }
                    else
                    {
                        add_date_to_schedule(that);
                    }
                });
        }
	});
	$('#blackout-date-warning-proceed').on('click', function()
	{
		var $date_element = $('#datepicker').find('.eventsCalendar-day[rel="'+this.getAttribute('data-day')+'"]');
		if ($date_element.length > 0)
		{
			add_date_to_schedule($date_element[0]);
		}
	});
    $('#calendar-date-warning-proceed').on('click', function()
    {
        var $date_element = $('#datepicker').find('.eventsCalendar-day[rel="'+this.getAttribute('data-day')+'"]');
        if ($date_element.length > 0)
        {
            add_date_to_schedule($date_element[0]);
        }
    });

	function add_date_to_schedule(date_element)
	{
		$(date_element).addClass("day_selected");

        var counter = 0;
        var data = {id: $('#trainer_id').val()};
		var day = ('00'+$(date_element).attr('rel')).slice(-2); // Ensure the date is two digits, with a leading zero if necessary
		var month = $("#datepicker").attr("data-current-month");
		var visual_month = parseInt(month) + 1;
        if (visual_month < 10) {
            visual_month = "0" + visual_month;
        }
		var year = $("#datepicker").attr("data-current-year");
        var category_start = $('#course_id').find(':selected').data('category_start_time');
        var category_end = $('#course_id').find(':selected').data('category_end_time');
        var fee_per = $("#fee_per").val();
        var fee_amount = $("#fee_amount").val();
        $.ajax({
            url: '/admin/courses/get_trainers',
            data:data,
            global: false,
            type: 'POST',
            dataType: 'json'
        })
            .success(function (data)
			{
                var timeslot = {
                    datetime_start: year + "-" + visual_month + "-" + day + " 00:00:00",
                    datetime_end: year + "-" + visual_month + "-" + day + " 00:00:00",
                    monitored: 1,
                    fee_amount: "",
                    trainer_id: "",
                    blackout: false,
                    topic_id: null,
                    location_id: null
                };
                timeslots_cache.push(timeslot);

                timeslots_display_page = 0;
                render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
                update_results_count();

                var smg = '<div class="alert alert-success add-date"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong>  The date ' + year + '-' + visual_month + '-' + day + ' has been added to the timetable.</div>';
                $('.alert.add-date').remove();
                $('.alert.remove-date').remove();
                $("#main").prepend(smg);
            });

    }

    $("#datepicker").on("click", '.eventsCalendar-day', function () {
        $("#selected_dates").find("tbody tr [data-month=" + $("#datepicker").attr("data-current-month") + "][data-year=" + $("#datepicker").attr("data-current-year") + "]").each(function () {
            $("#datepicker").find("[rel=" + parseInt($(this).siblings(':nth-child(2)').data('day'), 10) + "]").addClass("day_selected");
        });
    });
    $("#proceed_with_unmatched_navision").on("click", function(){
        check_navision_date = false;
        $(".save_timetable:first").trigger("click");
    });
    $(".save_timetable").click(function (ev) {
        ev.preventDefault();

        //prevent multiple clicks on "save" or "save & exit" buttons
        if(saving_timetable){
            return;
        } else {
            saving_timetable = true;
        }

        // Remove previous validation error messages
        $('.alert-validation').remove();

        // check if add zone is enabled
        var save_zone = ($("#zone_management_switch").is(':checked'));

        // $(".save_timetable").prop("disabled", true);
        var has_error = false;
        if ($("#name").val() == "") {
            $("#name").after('<div class="alert alert-warning alert-validation">You must enter a schedule name.</div>');
            $("html, body").animate({scrollTop: 0}, "slow");
            has_error = true;
        }
        if ($("#course_id").val() == "" && $("#is_interview_no").prop("checked")) {
            $("#course_id").after('<div class="alert alert-warning alert-validation">You must select a course.</div>');
            $("html, body").animate({scrollTop: 0}, "slow");
            has_error = true;
        }

        var $navision_event = $("#navision-events-table [name=navision_id]:checked");
        if ($navision_event.length > 0 && check_navision_date == true) {
            var nav_date = new Date($navision_event.data("eventdate"));
            var ts_date = null;
            for (var i = 0 ; i < timeslots_cache.length ; ++i) {
                //find earliers timeslot
                var tmp_date = new Date(timeslots_cache[i].datetime_start);
                if (ts_date == null) {
                    ts_date = tmp_date;
                } else {
                    if (tmp_date.getTime() < ts_date.getTime()) {
                        ts_date = tmp_date;
                    }
                }
            }

            if (nav_date.toDateString() != ts_date.toDateString()) {
                saving_timetable = false;
                $("#navision_date_warning_modal").modal();
                return false;
            }
        }

        if(has_error)
        {
            saving_timetable = false;
            $(".save_timetable").prop("disabled", false);

            // Open the tab containing the first error, so that the user can see it
            var $first_error = $('.alert-validation:first');
            var $tab_pane = $first_error.parents('.tab-pane');
            var $tab = $('.nav-tabs').find('[href="#'+$tab_pane.attr('id')+'"]');
            $tab.tab('show');

        }//build array of data, format "dd/mm/yyyy-HH:MM-HH:MM"
        else
        {
            $("#timeslots_json").val(JSON.stringify(timeslots_cache));
            var timetable_name = "";
            if (jQuery.trim($("#timetable_id").val()) == '' || jQuery.trim($("#new_timetable_name").val()) != '') {
                timetable_name = $("#new_timetable_name").val();
            }
            else {
                timetable_name = $("#timetable_id").val();
            }
            if (timetable_name == '') {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> Please enter a timetable name.</div>';
                $("#main").prepend(smg);
            }
            else {
                $("#timetable_post_name").val(timetable_name);

                // zones
                if (save_zone) {
                    var zone_rows = [];
                    var zone_zones = [];
                    var zone_prices = [];
                    $("#schedule_zones_table").find('tbody').find('tr').each(function () {
                        var $this = $(this);
                        if ($this.find('[data-row-id]').length) {
                            zone_rows.push($this.find("[data-content='row']").data('row-id'));
                            zone_zones.push($this.find("[data-content='zone']").find('select').val());
                            zone_prices.push($this.find("[data-content='price']").find('input').val());
                        }
                    });
                    $("#schedule_zones_area")
                        .append('<input type="hidden" name="zone_rows" value="'+zone_rows+'">')
                        .append('<input type="hidden" name="zone_zones" value="'+zone_zones+'">')
                        .append('<input type="hidden" name="zone_prices" value="'+zone_prices+'">');
                }

                $("#form_add_edit_schedule").submit();
            }
        }
    });
    $("#selected_dates button.next").on("click", function(){
        if (((timeslots_display_page + 1) * timeslots_display_limit) > timeslots_cache.length) {
            return;
        }
        ++timeslots_display_page;
        render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
        update_results_count();
    });
    $("#selected_dates button.prev").on("click", function(){
        if (timeslots_display_page <= 0) {
            return;
        }
        --timeslots_display_page;
        render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
        update_results_count();
    });
    $("#timetable_id").on("change", function () {
        if ($('#timetable_id').val() == 'select') {
            $('#datepicker-outer').hide();
            $('#selected_dates').hide();
        }
        else {
            $('#datepicker-outer').show();
            $('#selected_dates').show();

        }

        $.post("/admin/courses/timetable_get_dates", {timetable_id: $("#timetable_id").val(), schedule_id: $("#schedule_id").val()}).done(function (data) {
			var $selected_dates = $("#selected_dates");
			var $datepicker     = $("#datepicker");
            timeslots_cache = data;
            timeslots_display_page = 0;
            render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
            /*$selected_dates.find("tbody").html(data).promise().done(function () {

            });*/
            $selected_dates.find("tr [data-month=" + $datepicker.attr("data-current-month") + "][data-year=" + $datepicker.attr("data-current-year") + "]").each(function () {
                $datepicker.find("[rel=" + parseInt($(this).siblings(':nth-child(2)').data('day'), 10) + "]").addClass("day_selected");
            });

			get_blackout_dates();
            initDateRangePicker(false);

            if ( $('#duplicated').val()==1 || $('#schedule_id').val() == 'new')
            {
                $(".eventsCalendar-day").removeClass('day_selected');
                $("#selected_dates").find("tbody tr").each(function () {
                    $(this).remove();
                });
            }

			update_results_count();

		});
    });

    $("#bulk_timeslot_update_preview").on("click", function(){
        //var diff = $("#bulk_timeslot_update_action").val() == 'bring_forward' ? -1 : 1;
        var diff = 1;
        diff *= parseInt($("#bulk_timeslot_update_days").val());
        var from = $("#bulk_timeslot_update_from").datepicker('getDate');
        var to = $("#bulk_timeslot_update_to").datepicker('getDate');
        var trainer_id = $("#bulk_timeslot_update_trainer_id").val();
        var location_id = $("#bulk_timeslot_update_child_location_id").val();

        if (from) {
            from.setHours(0);
            from.setMinutes(0);
            from.setSeconds(0);
        }
        if (to) {
            to.setHours(0);
            to.setMinutes(0);
            to.setSeconds(0);
        }

        for (var i = 0 ; i < timeslots_cache.length ; ++i) {
            var startDate = new Date(clean_date_string(timeslots_cache[i]['datetime_start']));
            var endDate = new Date(clean_date_string(timeslots_cache[i]['datetime_end']));

            if (from && from > startDate) {
                continue;
            } else if (to && to < endDate) {
                continue;
            } else {
                if (timeslots_cache[i]['trainer_id'] != trainer_id) {
                    timeslots_cache[i]['trainer_id'] = trainer_id;
                    timeslots_cache[i].update = 1;
                }
                if (location_id) {
                    if (timeslots_cache[i]['location_id'] != location_id) {
                        timeslots_cache[i]['location_id'] = location_id;
                        timeslots_cache[i].update = 1;
                    }
                }
            }
        }

        var $tr = $("#bulk_timeslot_update_times tbody > tr");
        for (var i = 0 ; i < $tr.length ; ++i) {
            var start = $($tr[i]).data("start");
            var startday = $($tr[i]).data("day");
            var starttime = $($tr[i]).data("time");
            var new_start = $($tr[i]).find(".start_time").val();
            var new_end = $($tr[i]).find(".end_time").val();
            var dt_location_id = parseInt($($tr[i]).find(".dt_location_id").val());

            if (new_start != start || dt_location_id > 0) {
                for (var j = 0 ; j < timeslots_cache.length ; ++j) {
                    var startDate = new Date(clean_date_string(timeslots_cache[j]['datetime_start']));
                    var endDate = new Date(clean_date_string(timeslots_cache[j]['datetime_end']));

                    if (from && from > startDate) {
                        continue;
                    } else if (to && to < endDate) {
                        continue;
                    } else {
                        if (startDate.dateFormat("l H:i") == start) {
                            if (new_start != starttime) {
                                timeslots_cache[j]['datetime_start'] = startDate.dateFormat("Y-m-d") + " " + new_start + ":00";
                                timeslots_cache[j]['datetime_end'] = endDate.dateFormat("Y-m-d") + " " + new_end + ":00";
                                timeslots_cache[j].update = 1;
                            }

                            if (location_id) {
                                if (timeslots_cache[j]['location_id'] != location_id) {
                                    timeslots_cache[j]['location_id'] = location_id;
                                    timeslots_cache[j].update = 1;
                                }
                            }
                            if (dt_location_id) {
                                timeslots_cache[j]['location_id'] = dt_location_id;
                                timeslots_cache[j].update = 1;
                            }
                        }
                    }
                }
            }
        }

        for (var i = 0 ; i < timeslots_cache.length ; ++i) {
            var tsDate = new Date(clean_date_string(timeslots_cache[i]['datetime_start']));
            tsDate.setHours(0);
            tsDate.setMinutes(0);
            tsDate.setSeconds(0);
            var startDate = new Date(clean_date_string(timeslots_cache[i]['datetime_start']));
            var endDate = new Date(clean_date_string(timeslots_cache[i]['datetime_end']));

            if (from && from > tsDate) {
                continue;
            } else if (to && to < tsDate) {
                continue;
            } else {
                if (diff != 0) {
                    tsDate.setDate(tsDate.getDate() + diff);
                    endDate.setDate(endDate.getDate() + diff);

                    timeslots_cache[i]['datetime_start'] = tsDate.dateFormat("Y-m-d") + " " + startDate.dateFormat("H:i:00");
                    timeslots_cache[i]['datetime_end'] = tsDate.dateFormat("Y-m-d") + " " + endDate.dateFormat("H:i:00");
                    timeslots_cache[i].update = 1;
                    if (location_id) {
                        if (timeslots_cache[i]['location_id'] != location_id) {
                            timeslots_cache[i]['location_id'] = location_id;
                            timeslots_cache[i].update = 1;
                        }
                    }
                }
            }
        }

        render_timeslots(timeslots_cache, 0, timeslots_display_limit);
    });

    $.get("/admin/courses/get_active_timetable", {schedule: $("#schedule_id").val()}).done(function (result) {
		var $datepicker     = $("#datepicker");
        $("#timetable_id").val(result).change();
        $("#selected_dates").find("tr [data-month=" + $datepicker.attr("data-current-month") + "][data-year=" + $datepicker.attr("data-current-year") + "]").each(function () {
            $("#datepicker").find("[rel=" + parseInt($(this).siblings(':nth-child(2)').data('day'), 10) + "]").addClass("day_selected");
        });
    });

    $(".arrow").on("click", function () {
		var $datepicker     = $("#datepicker");
        $("#selected_dates").find("tr [data-month=" + $datepicker.attr("data-current-month") + "][data-year=" + $datepicker.attr("data-current-year") + "]").each(function () {
            $datepicker.find("[rel=" + parseInt($(this).siblings(':nth-child(2)').data('day'), 10) + "]").addClass("day_selected");
        });
		get_blackout_dates();
    });

    if ($('#timetable_id').val() == 'select') {
        $('#datepicker-outer').hide();
        $('#selected_dates').hide();
    }

    $("#run_off_schedule").on('change', function () {
        $.post('/admin/courses/get_location_spaces', {schedule_id: $("#run_off_schedule").val()}, function (data) {
            data = $.parseJSON(data);
            console.log(data);
            $("#room_size").text(" Room Capacity: " + data.size.capacity);
        });
    });

    $('#Preview_tab').on('click', function () {
        $("#Preview table > tbody").html('');
        // Copy All selected days table data to the preview
        $.each(day_selector, function (item, table) {
            var table_slots = [];
            $('#' + table + ' tbody tr').each(function () {
				var $tr = $(this).closest("tr");
                var newTr = $tr.clone();
                table_slots.push(newTr);
                newTr.appendTo($("#Preview table tbody"));
				var $trainer_select = $tr.find(".trainer_select");
				var day_row = $trainer_select.parents(".new-slot").data("day_row");
				try{
					$("#Preview tr[data-day_row='" + day_row + "'] .trainer_select").val($trainer_select.val());
				}catch(exc){
				}
            });
        });
        // Set the input and select remove to disabled
        $("#Preview").find("input,select").attr("disabled", "disabled");
        $("#Preview table tbody tr td").each(function () {
            if ($(this).closest('td').hasClass('delete_me')) {
                $(this).closest('td').remove();
            }
        })
    });

	$(document).on('change', '.day_button', function ()
	{
		var day = $(this).data('day');
		var $label = $(this).parents('label');

		if ($(this).prop('checked'))
		{
			$('#' + day + '_tab').show();
			day_selector.push($(this).data('day'));
			$label.find('.day_button_icon').addClass('icon-ok').removeClass('icon-remove');
			$label.find('.btn').addClass('btn-success');
		}
		else
		{
			$('#' + day + '_tab').hide();
			day_selector.splice($.inArray($(this).data('day'), day_selector), 1);
			$label.find('.day_button_icon').addClass('icon-remove').removeClass('icon-ok');
			$label.find('.btn').removeClass('btn-success');

		}
	});

    $(document).on('click', '#timeslot-trainers-reset', function(){
        var masterTrainerId = $("#trainer_id").val();
        $("#selected_dates tbody .trainer_select").val(masterTrainerId);
    });

    $(document).on('blur', '.timeslot_price', function(){
        if (this.value != "" && isNaN(parseFloat(this.value))) {
            this.value = '';
            alert("Please enter a correct price");
        }
    });

    $("#generate_dates").click(function ()
	{
		if (clear_on_generateslots){
            clear_dates();
        }
        $('#modal_data').val('');
        $('#start_time_warning').hide();
        $('#end_time_warning').hide();
        $('#dates_required').hide();
        $('#times_required').hide();
        $('#frequency_required').hide();
        var frequency = $("#repeat").val();
        var required_fields = true;
		if (document.getElementById('start_date').value == '' || document.getElementById('end_date').value == '')
		{
			required_fields = false;
            $('#dates_required').show();
		}
        if (frequency == 6 && $(".frequency_table > tbody > tr").length == 0)
        {
            required_fields = false;
            $('#frequency_required').show();
        }
        if ($('#course_id').val() == '' && $("#is_interview_no").prop("checked"))
        {
            $('#course_selection_modal').modal();
        }

        if ( ! required_fields)
        {
            $('#date-range-required-modal').modal();
        }
		else
		{
			// Custom Repeat options
			var data;
			if (frequency == 6)
			{
				var custom_frequency = $('#frequency').val();
                var week_days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                var from_date = new Date(clean_date_string($("#start_date").val()));
                var to_date = new Date(clean_date_string($("#end_date").val()));
                var from_day = week_days[from_date.getDay()];
                var to_day = week_days[to_date.getDay()];
				//var schedule_slots = [];
				if (custom_frequency != '') {
					$.each(day_selector, function (item, table) {
						var table_slots = [];
						$('#' + table).find('.new-slot').each(function (items, rows) {
							table_slots = [
                                day = table,
								start_time = $(rows).find('[name="start_time"]').val(),
								end_time = $(rows).find('[name="end_time"]').val(),
								trainer = $(rows).find('.trainer_select').val(),
                                id = $(rows).find('[name="interval_id"]').val(),
                                custom_frequency = custom_frequency,
                                location_id = $(rows).find('.room_select').val(),
							];
							custom_repeat.push(table_slots);
                            $('#custom_hidden').push(table_slots);
						});
                        //$('#custom_hidden').val(custom_repeat);
					});
				}
				data = {
					frequency: frequency,
					start_date: $("#start_date").val(),
					end_date: $("#end_date").val(),
					days: JSON.stringify(day_selector),
					duration: $("#days_duration").val(),
					timeslots: JSON.stringify(custom_repeat),
					custom_repeat: custom_frequency,
                    trainer_id: $('#trainer_id, #timetable-planner-add_slot-modalx-contact_id').val(),
                    course_id: $('#course_id').val()
				};
			}
			else
			{
				data = {
					frequency: frequency,
					start_date: $("#start_date").val()+' '+$('#start_day_time').val(),
					end_date: $("#end_date").val()+' '+$('#end_day_time').val(),
					days: JSON.stringify(day_selector),
					duration: $("#days_duration").val(),
                    trainer_id: $('#trainer_id, #timetable-planner-add_slot-modalx-contact_id').val(),
                    course_id: $('#course_id').val()
				}
			}
            data.location_id = $("#child_location_id").val();
            data.fee_per = $("#fee_per").val();
            data.fee_amount = $("#fee_amount").val();
			generate_timeslots(data);
		}
    });

    if($('#repeat').val() > 0)
    {
        if($('#repeat').val() == 6)
        {
            $('#repeat_start_time').hide();
        }
        else
        {
            $('#repeat_start_time').show();
        }
        $('#date_selection').show();
        $('#date_selection').removeClass('hidden');
    }
    else
    {
        $('#repeat_start_time').hide();
    }
    $("#days_of_week").hide();
    $('#custom_schedule').hide();
    if ($('#repeat').val() == 6) {
        if ($('#schedule_id').val() > 0)
        {
            $("#days_of_week").show();
            $('#custom_schedule').show();
            load_custom_interval($('#scedule_id').val());
        }
        $('#custom_schedule').show();
    }

    $(document).on('click', '.add_timeslot', function () {
        var day = $(this).closest('div').attr('id');//$('.day_name').val();
        var trainer = $('#trainer_id :selected').val();
        var row = $('#' + day + ' tr').length;
        var add_row = true ;
        if (add_row) {
            $.post('/admin/courses/get_custom_timeslot_row', {
                day: day,
                trainer: trainer,
                row: row,
                course_id: $('#course_id').val(),
                location_id: $("#child_location_id").val()
            }, function (data)
            {
                $('#' + day + ' table tbody').append(data);
                //$('#All table').append(data);
                initDateRangePicker(false);
                update_results_count();
            });
        }
    });

	$(document).on('change', '#timeslot_selection .trainer_select', function(){
		var day_row = $(this).parents(".new-slot").data("day_row");
		try{
			$("#Preview tr[data-day_row='" + day_row + "'] .trainer_select").val($(this).val());
		}catch(exc){
			alert(exc);
		}
	});

    $(".clear-timeslot").on("click", function(){
        if ($("#timetable-planner-schedule_timeslots-modal").length == 0){
            $("#clear-timeslots-modal").modal();
        } else {
            clear_dates();
        }
    });

	$("#clear_dates").click(
        function(){
            clear_dates();
            $("#clear-timeslots-modal").modal("hide");
        }
    );

    $("#repeat").change(function () {
        if ($(this).val() != "") {
            $("#repeat_start_time").removeClass("hidden");
            $("#date_selection").removeClass('hidden');
            $('#repeat_start_time').show();
        }
        else
        {
            $("#repeat_start_time").addClass("hidden");
            $('#repeat_start_time').hide();
            $("#date_selection").addClass('hidden');
            $("#days_of_week").hide();
            //$('#timeslot_selection').hide();
        }

        if ($(this).val() == "6") {
            if ($('#course_id').val() == '' && $("#is_interview_no").prop("checked")) {
                $('#course_selection_modal').modal();
                $("#repeat").val('');
            }
            else {
                $('#repeat_start_time').hide();
                $("#custom_schedule").removeClass("hidden");
                $("#custom_schedule").show();
                if ($(this).val() != "")
                {
                    $("#custom_schedule").show();
                    if ($(this).val() != "")
                    {
                        $("#days_of_week").show();
                    }
                }
            }
        }
        else {
            $("#custom_schedule").addClass("hidden");
            $("#custom_schedule").hide();
        }
    });

    $(document).on('change', '#frequency', function () {
        if ($(this).val() == "") {
            $("#days_of_week").hide();
        }
        else {
            $("#custom_schedule").show();
            if ($(this).val() != "") {
                $("#days_of_week").show();
            }
        }
    });

    $(document).on('click', '#get_timeslot_table', function () {
        $.post('/admin/courses/get_custom_timetable', {selected_days: day_selector}, function (data) {
            $('#timeslot_selection').removeClass('tabbable');
            $('#timeslot_selection').html('');
            $('#timeslot_selection').append(data);
        });
    });

    $(document).on('change', '#payment_type', function () {
        var payment_selected = $(this).val();
        if (payment_selected == 1) {
            $('#rental_fee_display').hide();
        }
        else {
            $('#rental_fee_display').show();
            if ($('#rental_fee').val() == '') {
                $('#rental_fee').val($('#rental_fee').data('default-value'));
            }
        }
    });

	// Reset timeslots when the rest of the form is reset
	$('#schedule-form-reset').on('click', function()
	{
		$('#selected_dates').find('tbody').html(original_timeslots);
		update_results_count();
	});

	function get_blackout_dates()
	{
        var data = {};
        data.blackout_calendar_event_ids = [];
        $("#blackout_calendar_event_ids option").each(function(){
            if (this.selected && this.value > 0) {
                data.blackout_calendar_event_ids.push(this.value);
            }
        });
        if (data.blackout_calendar_event_ids.length == 0){
            data.blackout_calendar_event_ids = 'none';
        }
		$.post('/admin/calendars/get_calendar_dates', data)
			.done(function(results)
			{
				var $datepicker = $('#datepicker');
				var blackout_dates = results;
                var $blackedoutDates = $datepicker.find('.blackout-date');
                $blackedoutDates.removeClass("blackout-date");
                $blackedoutDates.attr("data-type", "");
				for (var i = 0; i < blackout_dates.length; i++)
				{
					var date = new Date(clean_date_string(blackout_dates[i].date.split(' ')[0]));
					var month = date.getMonth();
					var year  = date.getFullYear();

					if ($datepicker[0].getAttribute('data-current-month') == month && $datepicker[0].getAttribute('data-current-year') == year)
					{
						$datepicker.find('[rel='+date.getDate()+']')
                            .addClass('blackout-date')
                            .attr('data-type', blackout_dates[i].type)
                            .data('type', blackout_dates[i].type)
                            .attr('title', blackout_dates[i].title)
                        ;
					}
				}
			});
	}

    $("#blackout_calendar_event_ids").multiselect({numberDisplayed: 1});
    $("#blackout_calendar_event_ids").on("change", function(){
        get_blackout_dates();
    });

    if ($(".add_contact3_btn").length > 0) {
        var c3dialog = new Contacts3Dialog(
            {
                type: "general",
                onselect: function(data){
                    if (data.contactId){
                        var tselect = $("#trainer_id")[0];
                        var option = new Option(data.contactName, data.contactId);
                        option.selected = true;
                        tselect.options[tselect.options.length] = option;
                        c3dialog.hide();
                    }
                }
            }
        );
        $(".add_contact3_btn").on("click", function () {
            c3dialog.display("general");
        });
    }

    // Zone Management
    var schedule_zones_area = $("#schedule_zones_area");
    var child_location_id = $("#child_location_id");

    $("#zone_management_yes_label").on('click',function(ev){
        var location_id = child_location_id.val();
        if(! location_id){
            $("#schedule_zones_table").find("tbody").html('<tr><td colspan="3">Please select Sub Location.</td></tr>');
            if(schedule_zones_area.hasClass("hide")){
                schedule_zones_area.removeClass("hide");
            }
        }else{
            change_zones_table_with_ajax(location_id);
            if(schedule_zones_area.hasClass("hide")){
                schedule_zones_area.removeClass("hide");
            }
        }

    });

    $("#zone_management_no_label").on('click',function(){
        if(!schedule_zones_area.hasClass("hide")){
            schedule_zones_area.addClass("hide");
        }
    });

    child_location_id.on('change',function(){
        var location_id = child_location_id.val();
        if(! location_id){
            $("#schedule_zones_table").find("tbody").html('<tr><td colspan="3">Please select Sub Location.</td></tr>');
        }else{
            change_zones_table_with_ajax(location_id);
        }
    });


    var $payment_option_tpl = $("#paymentoptions tfoot .payment_option");
    $payment_option_tpl.remove();
    var $custom_payment_plan_table_tpl = $(".custom.payment_plan.hidden.tpl");
    var $custom_plan_option_tpl = $custom_payment_plan_table_tpl.find("tfoot tr.custom_option");
    $custom_plan_option_tpl.remove();
    $custom_payment_plan_table_tpl.removeClass("tpl");
    $custom_payment_plan_table_tpl.remove();

    $("#paymentoptions > tbody > tr .custom.payment_plan .btn.add_custom").on("click", custom_plan_option_add);
    $("#paymentoptions > tbody > tr .custom.payment_plan .btn.remove_custom").on("click", custom_plan_option_remove);

    function payment_option_add()
    {
        var index = 0;
        $("#paymentoptions > tbody > tr").each(function(){
            index = Math.max(index, parseInt($(this).attr("data-index")));
            ++index;
        });

        var $payment_option = $payment_option_tpl.clone();
        var $custom_payment_plan_table = $custom_payment_plan_table_tpl.clone();
        $payment_option.find(".c3").append($custom_payment_plan_table);

        $payment_option.find("input, select").each(function(){
            this.name = this.name.replace("paymentoption[index]", "paymentoption[" + index + "]");
        });

        $payment_option.find(".btn.remove").on("click", payment_option_remove);
        $payment_option.attr("data-index", index);
        $payment_option.removeClass("hidden");
        $payment_option.find('.interest_type').on('change', payment_option_custom_changed);

        $custom_payment_plan_table.find(".btn.add_custom").on("click", custom_plan_option_add);

        $("#paymentoptions > tbody").append($payment_option);
    }

    function custom_plan_option_add()
    {
        var $payment_option = $(this).parents("tr.payment_option");
        var index = $payment_option.attr("data-index");
        var index2 = 0;
        $payment_option.find(".custom.payment_plan tbody tr").each(function(){
            index2 = Math.max(index2, parseInt($(this).attr("data-index2")));
            ++index2;
        });

        var $custom_plan_option = $custom_plan_option_tpl.clone();
        $custom_plan_option.attr("data-index2", index2);
        $custom_plan_option.removeClass("hidden");
        $custom_plan_option.find("input").each(function(){
            this.name = this.name.replace("paymentoption[index]", "paymentoption[" + index + "]");
            this.name = this.name.replace("[custom_payments][index2]", "[custom_payments][" + index2 + "]");
        });
        $custom_plan_option.find(".due_date").datepicker(
            {
                autoclose: true,
                orientation: "auto bottom",
                format: 'dd-mm-yyyy'
            }
        );
        $custom_plan_option.find(".btn.remove_custom").on("click", custom_plan_option_remove);
        $custom_plan_option.find(".amount, .interest").on("change", function(){
            var $tr = $(this).parents("tr");
            var total = 0;
            total = parseFloat($tr.find(".amount").val()) + parseFloat($tr.find(".interest").val());
            if(!isNaN(total)) {
                $tr.find(".total").val(total);
            } else {
                $tr.find(".total").val("");
            }
        });

        $payment_option.find(".custom.payment_plan tbody").append($custom_plan_option);
    }

    function custom_plan_option_remove()
    {
        $(this).parent().parent().remove();
    }

    function payment_option_remove()
    {
        $(this).parents("tr").remove();
    }

    function payment_option_custom_changed()
    {
        var interest_type = this.value;

        var $tr = $(this).parents("tr");
        var index = $tr.data("index");
        if (interest_type == 'Custom') {
            $tr.find(".c4,.c5").addClass("hidden");
            $tr.find(".c3 input.deposit").parents(".input_group").addClass("hidden");
            $tr.find(".c3 .custom.payment_plan").removeClass("hidden");
            $tr.find(".c3").attr("colspan", "4");
        } else {
            $tr.find(".c4,.c5").removeClass("hidden");
            $tr.find(".c3 input.deposit").parents(".input_group").removeClass("hidden");
            $tr.find(".c3 .custom.payment_plan").addClass("hidden");
            $tr.find(".c3").attr("colspan", "1");
        }
    }

    $("#paymentoptions .btn.add").on("click", payment_option_add);
    $("#paymentoptions .btn.remove").on("click", payment_option_remove);
});

function getDay(day, month, year) {
    var date = new Date(year, month, day);
    var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    return days[date.getDay()];
}

function get_trainers() {
    var trainer_data = '';
    var trainer_id = $('#trainer_id').val();
    var data = {id: trainer_id};
    $.ajax({
        url: '/admin/courses/get_trainers',
        data:data,
        global: false,
        type: 'POST',
        dataType: 'json'
    })
        .success(function (data) {
            return trainer_data = data;
        });
}

// Add an alert to a message area.
// e.g. $('#page_notification_area').add_alert('Page successfully saved', 'success');
(function($)
{
    $.fn.add_alert = function(message, type)
    {
        var $alert = $('<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
                       '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
                       '</div>');
        $(this).append($alert);

        // Dismiss the alert after 10 seconds
        setTimeout(function()
        {
            $alert.fadeOut();
        }, 10000);
    };
})(jQuery);

function startDateChanged()
{
    var date = $(this).datepicker('getDate');

    $('#start_date').off("change", startDateChanged);
    $('#end_date').off("change", endDateChanged);
    $('#end_date').datepicker("remove");
    $('#end_date').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        startDate: date,
        format: 'dd-mm-yyyy',
    });
    $('#end_date').on("change", endDateChanged);
}

function endDateChanged()
{
    var date = $(this).datepicker('getDate');

    $('#start_date').off("change", startDateChanged);
    $('#end_date').off("change", endDateChanged);
    $('#start_date').datepicker("remove");
    $('#start_date').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        endDate: date,
        format: 'dd-mm-yyyy'
    });

    $('#start_date').on("change", startDateChanged);
    $('#end_date').on("change", endDateChanged);
}

function initDateRangePicker(date){
    var from,
        to;

    if (date)
    {
        from = $('#start_date');
        to = $('#end_date');
    }
    else
    {
        from = $('.start_time');
        to = $('.end_time');
    }

    if (date) {
        from.datepicker({
            format: 'YYYY-MM-DD',
            //formatTime: 'H:mm',
            formatDate: 'YYYY-MM-DD',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: to.val() ? to.val() : false
                })
            }
        });

        to.datepicker({
            format: 'YYYY-MM-DD',
            //formatTime: 'H:mm',
            formatDate: 'YYYY-MM-DD',
            onShow: function (ct) {
                this.setOptions({
                    minDate: from.val() ? from.val() : false
                })
            }
        });
    }
    else
    {
        from.datetimepicker({
            datepicker : false,
            format: 'H:i',
            formatTime: 'H:i',
            minTime: myMinTime,
            maxTime: myMaxTime,
            step: myInterval
        });
        to.datetimepicker({
            datepicker: false,
            format: 'H:i',
            formatTime: 'H:i',
            minTime: myMinTime,
            maxTime: myMaxTime,
            step: myInterval
        });
    }
}

function load_custom_interval(schedule_id)
{
    $.ajax({
        url: '/admin/courses/ajax_get_custom_intervals',
        data: { schedule_id : $('#schedule_id').val() },
        global: false,
        type: 'POST',
        dataType: 'json'
    })
        .success(function(data)
        {
            $("#days_of_week").show();
            $.each(data,function(day,rows)
            {
                $('.day_button[data-day="'+day+'"]').prop('checked', true).trigger('change');
                $('#' + day + ' table tbody').append(rows);
            });
            $.each(day_selector, function (item, table) {
                var table_slots = [];
                $('#' + table + ' tbody tr').each(function () {
                    var newTr = $(this).closest("tr").clone();
                    table_slots.push(newTr);
                    newTr.appendTo($("#Preview table tbody"));
                });
            });
            // Set the input and select remove to disabled
            $("#Preview").find("input,select").attr("disabled", "disabled");
            $("#Preview table tbody tr td").each(function () {
                if ($(this).closest('td').hasClass('delete_me')) {
                    $(this).closest('td').remove();
                }
            })
        });
}

function generate_timeslots(data)
{
	if (clear_on_generateslots){
        clear_dates();
    }
    data.blackout_calendar_event_ids = [];
    $("#blackout_calendar_event_ids option").each(function(){
        if (this.selected && this.value > 0) {
            data.blackout_calendar_event_ids.push(this.value);
        }
    });
    if (data.blackout_calendar_event_ids.length == 0){
        data.blackout_calendar_event_ids = 'none';
    }
    $.post('/admin/courses/calculate_frequency', data, function (data) {
        window.disableScreenDiv.style.visibility = "visible";
        if (data.status == 'success') {
            var alerts = $('#schedule_alert_area');
            var $selected_dates = $("#selected_dates");
            var $datepicker = $("#datepicker");

            //timeslots_cache = data.result;
            for (var i in data.result) {
                timeslots_cache.push(data.result[i]);
            }
            timeslots_display_page = 0;
            render_timeslots(timeslots_cache, 0, timeslots_display_limit);

            if (data.message != '')
            {
                alerts.add_alert(data.message);
            }
            update_results_count();
            initDateRangePicker(false);
        }
        window.disableScreenDiv.style.visibility = "hidden";
        window.disableScreenDiv.autoHide = true;
    },"json");
    window.disableScreenDiv.autoHide = false;
}

function update_results_count()
{
	var number_of_timeslots = timeslots_cache.length;
	document.getElementById('schedule-form-timeslot-results').setAttribute('data-count', number_of_timeslots);
	document.getElementById('schedule-form-timeslot-results-count').innerHTML = ((timeslots_display_page * timeslots_display_limit) + 1) + ' / ' + Math.min(timeslots_cache.length, ((timeslots_display_page + 1) * timeslots_display_limit)) + ' of ' + timeslots_cache.length;

	$('#daily_frequency').find('li a').each(function()
	{
		// Find the number of timeslots in the tab's corresponding tab pane
		// Set the number in tab equal to the number of timeslots
		this.getElementsByClassName('timeslot-tab-count')[0].innerHTML = $(this.getAttribute('href')).find('[data-day_row]').length;
	});

    var has_timeslots = (document.getElementById('selected_dates-table').querySelectorAll('tbody .schedule-timeslot-row').length > 0);
    $('#selected_dates').toggleClass('hidden', !has_timeslots);
    $('#selected_dates-empty').toggleClass('hidden', has_timeslots);
}

$('#edit_schedule-topics-add').on('click', function()
{
    var topic_id    = parseInt($('#edit_schedule-topics').val());
    var schedule_id = parseInt($('#form_add_edit_schedule').find('[name="id"]').val());
    var $tbody      = $('#edit_schedule-topics-table').find('tbody');

    if (schedule_id > 0 && topic_id > 0) {
        if ($tbody.find('[name="topic_ids[]"][value="'+topic_id+'"]').length) {
            $('#schedule_alert_area').add_alert('Topic has already been added', 'warning');
        } else {
            $.post('/admin/courses/ajax_add_topic_to_schedule', {schedule_id: schedule_id, topic_id: topic_id})
                .done(function(result) {
                    $tbody.append(result);
                });
        }
    }

    return false;
});

$('#form_add_edit_schedule').on('click', '.delete_course_topic', function()
{
	$(this).parents('tr').remove();
});

$('[href="#edit_schedule-tab-timeslots"]').on('shown.bs.tab', function() {
    $(window).resize(); // Force the calendar to render at the correct dimensions
});

function add_date_to_schedulex(date_element)
{
    if (typeof(date_element) == "string") {
        var timeslot = {
            datetime_start: date_element,
            datetime_end: date_element,
            monitored: 1,
            fee_amount: "",
            trainer_id: "",
            blackout: false,
            topic_id: null
        };
    } else {
        $(date_element).addClass("day_selected");

        var counter = 0;
        var data = {id: $('#trainer_id').val()};
        var day = ('00' + $(date_element).attr('rel')).slice(-2); // Ensure the date is two digits, with a leading zero if necessary
        var month = $("#datepicker").attr("data-current-month");
        var visual_month = parseInt(month) + 1;
        if (visual_month < 10) {
            visual_month = "0" + visual_month;
        }
        var year = $("#datepicker").attr("data-current-year");
        var category_start = $('#course_id').find(':selected').data('category_start_time');
        var category_end = $('#course_id').find(':selected').data('category_end_time');
        var timeslot = {
            datetime_start: year + "-" + visual_month + "-" + day + " 00:00:00",
            datetime_end: year + "-" + visual_month + "-" + day + " 00:00:00",
            monitored: 1,
            fee_amount: "",
            trainer_id: "",
            blackout: false,
            topic_id: null
        };
    }
    var fee_per = $("#fee_per").val();
    var fee_amount = $("#fee_amount").val();

    timeslots_cache.push(timeslot);

    timeslots_display_page = 0;
    render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
    update_results_count();

    var smg = '<div class="alert alert-success add-date"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong>  The date ' + year + '-' + visual_month + '-' + day + ' has been added to the timetable.</div>';
    $('.alert.add-date').remove();
    $('.alert.remove-date').remove();
    $("#main").prepend(smg);

}

function timeslot_row_index($tr)
{
    var index = (timeslots_display_page * timeslots_display_limit) + $tr.index();
    return index;
}