/*
 * This file is considered deprecated.
 * Functionality should be transferred to timetable_view.js
 */
$(document).ready(function()
{
    clear_on_generateslots = false;
    var $calendar = $('#timetable-planner-fullcalendar');

    function render_calendar(events)
    {
        var calendar_view = 'agendaWeek';

        try {
            calendar_view = $calendar.fullCalendar('getView').name;
        } catch (exc) {
            calendar_view = 'agendaWeek';
        }

        try {
            $calendar.fullCalendar('destroy');
        } catch (exc) {
            calendar_view = 'agendaWeek';
        }

        if (!calendar_view) {
            calendar_view = $("[name=grid_period]:checked").val();
        }
        var i = 0;
        events.forEach(function(event) {
            events[i].title = event.schedule;
            i++;
        });

        $calendar.fullCalendar({
            header: {
                left: 'prev,title,next',
                right: 'agendaDay,agendaWeek,month,listMonth'
            },
            buttonText: {
                day: 'Day',
                week: 'Week',
                month: 'Month',
                listMonth: 'List'
            },
            firstDay: 1,
            views: {
                'week': {
                    columnFormat: 'DD/MM ddd'
                },
                'month': {
                    eventLimit: 5
                }
            },
            defaultView: calendar_view,
            events: events,
            slotEventOverlap: false,
            eventRender: function(event, element, view)
            {
                element.attr('data-status', event.status).data('status', event.status);
                element.attr("data-timeslot_id", event.id);
                element[0].timeslot = event;

                var $attendance = $('#timetable-attendance-template').clone();
                $attendance.removeAttr('id').removeClass('hidden');
                $attendance.find('.timetable-attendance-amount').html(event.booking_count);

                element.find('.fc-content').prepend($attendance);

                element.attr({
                    'data-container': 'body',
                    'data-content':   '&#32;',
                    'data-html':      'true',
                    'data-trigger':   'click',
                    'rel':            'popover',
                    'tabindex':       '0'
                });
                element.addClass('fc-event--has_popover');
                element.popover();

                // Only show one popover at a time
                element.on('click', function () {
                    $('.fc-event--has_popover').not(this).popover('hide');
                });
            },
            eventAfterRender: function(event, element, view) {
            },
            eventDestroy: function(event, element, view) {
                // Return the form to its original position
                // That way it does not get destroyed when the popover vanishes and can be reused in other popovers
                $('#timetable-planner-event-edit').append($('#timetable-planner-event-edit-form-popover'));
                $(element).popover('destroy');
            },
            viewRender: function(view, element) {

                if (view.name == 'agendaDay') {
                    var $clone = $('#timetable-agendaDay-header').clone();
                    $clone.find('.timetable-agendaDay-header-title').text(view.start.format('dddd Do MMMM YYYY'));

                    var counters = [
                        {status: 'Done', amount: 5},
                        {status: 'Booked', amount: 15},
                        {status: 'Pending', amount: 8},
                        {status: 'Conflict', amount: 2},
                        {status: 'Available', amount: 30}
                    ];
                    var total = 60;

                    for (var i = 0; i < counters.length; i++) {
                        $clone.find('[data-status="' + counters[i].status + '"]')
                            .css('width', (counters[i].amount * 100 / total) + '%')
                            .html(counters[i].amount == 0 ? '' : counters[i].amount);
                    }

                    $calendar.find('.fc-day-header').html($clone.html());
                }
                else if (view.name == 'agendaWeek')
                {
                    /*
                     * Each row uses one cell for all days. Split this into seven cells.
                     * With a cell per time per day, we can to do things like add click events and hover states to empty slots.
                     */

                    // Get the cells used in the header row. The new cells are to be the same width and correspond to the same days as these.
                    var $days = $('.fc-agendaWeek-view .fc-widget-header .fc-axis ~ th');

                    // New properties for the existing cell, which is to become a cell for just the first day
                    var first_cell_date = $days.first().data('date');

                    // Create six new cells
                    var new_cells =
                        '<td data-date="' + $days.eq(1).data('date') + '" class="fc-widget-content"></td>' +
                        '<td data-date="' + $days.eq(2).data('date') + '" class="fc-widget-content"></td>' +
                        '<td data-date="' + $days.eq(3).data('date') + '" class="fc-widget-content"></td>' +
                        '<td data-date="' + $days.eq(4).data('date') + '" class="fc-widget-content"></td>' +
                        '<td data-date="' + $days.eq(5).data('date') + '" class="fc-widget-content"></td>' +
                        '<td data-date="' + $days.eq(6).data('date') + '" class="fc-widget-content"></td>';

                    // Apply these changes to each row.
                    $('.fc-agendaWeek-view .fc-slats .fc-axis + .fc-widget-content').each(function()
                    {
                        $(this).data('date', first_cell_date).attr('data-date', first_cell_date);
                        $(this).after(new_cells);
                    });
                }

                $('#timetable-planner-requests').removeClass('hidden');
            }
        });

        if (godate) {
            $calendar.fullCalendar('gotoDate', new Date(godate));
        }
    }

    $calendar.on('click', '.fc-month-view .fc-day, .fc-agendaWeek-view .fc-widget-content, .fc-agendaDay-view .fc-slats tr', function(ev) {
        // If the clicked area is an event or is within an event, do nothing
        if (!$(ev.target).hasClass('fc-event') && $(ev.target).parents('.fc-event').length == 0) {
            $('.fc-event--has_popover').popover('hide');

            //$('#timetable-planner-add_slot-modal').modal();
            $("#timetable-planner-schedule_timeslots-modal input, #timetable-planner-schedule_timeslots-modal select").val("");
            timeslots_cache = [];
            render_timeslots();
            update_results_count();
            $('#timetable-planner-schedule_timeslots-modal').modal();

            var date = $(ev.target).data("date");
            var time = $(ev.target).parents("tr").data("time");

            if (time && !date) {
                date = $calendar.fullCalendar("getDate").format("YYYY-MM-D");
            }

            if (date) {
                if (time) {
                    date += " " + time;
                }
                add_date_to_schedulex(date);
            }

        }
    });

    $(document).on("click", ".fc-prev-button, .fc-next-button", function(){
        update_calendar();
    });

    var godate = null;
    function update_calendar()
    {
        var date_range = $('#timetable-planner-daterange').data('daterangepicker');
        var start_date = null;
        var end_date = null;

        try {
            start_date = $calendar.fullCalendar("getView").intervalStart.format("YYYY-MM-DD");
            end_date = $calendar.fullCalendar("getView").intervalEnd.format("YYYY-MM-DD");
        } catch (exc) {
            start_date = moment().startOf('week').format("YYYY-MM-DD");
            end_date = moment().endOf('week').format("YYYY-MM-DD");
        }

        if (start_date == null) {
            return;
        }

        /*var range_type = date_range.get_range_type();
        if (['week', 'day', 'month'].indexOf(range_type) > -1) {
            $('#timetable-planner-view-options').find('[name="grid_period"][data-range_type="'+range_type+'"]').prop('checked', true).trigger('change');
        }*/

        godate = start_date;

        $.post(
            '/admin/timetables/get_calendar',
            {
                after: start_date,
                before: end_date,
                course_id: $("#calendar-filter-course_id").val(),
                schedule_id: $("#timetable-planner-schedule").val(),
                trainer_id: $("#timetable-planner-trainer_id").val(),
                location_id: $("#timetable-planner-location_id").val(),
                topic_id: $("#timetable-planner-topic_id").val(),
            },
            function (response) {
                try {
                    render_calendar(response.slots);
                } catch (exc) {

                }
            }
        );

        set_table('#timetable-planner-requests-requires_resolution', 'conflict');
        set_table('#timetable-planner-requests-other', 'other');
    }

    $('#timetable-planner-daterange, #calendar-filter-course_id, #timetable-planner-schedule, #timetable-planner-trainer_id, #timetable-planner-location_id, #timetable-planner-topic_id').on('change', function() {
        update_calendar();
    });

    render_calendar([]);
    var first_load_timer = setInterval(
        function(){

            var start_date = null;
            try {
                start_date = $calendar.fullCalendar("getView").intervalStart.format("YYYY-MM-DD");
            } catch (exc) {

            }
            if (start_date) {
                clearInterval(first_load_timer);
                update_calendar();
            } else {
                console.log("no time");
            }
        },
        50
    );

    $('.timetables-grid_period').on('change', function() {
        $('#timetable-planner-fullcalendar').fullCalendar('changeView', $('.timetables-grid_period:checked').val());
    });

    function autocomplete_set(name, url)
    {
        var last_id = null;
        var last_label = null;
        var input = $(name)[0];

        $(name).autocomplete({
            source: function(data, callback){
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
            },
            open: function () {
                if (last_label != input.value) {
                    $(name + "_id").val("");
                }
            },
            select: function (event, ui) {
                if (ui.item.label) {
                    if (ui.item.id) {
                        $(name + "_id").val(ui.item.id).change();
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
                if (name == "#timetable-planner-add_slot-modalx-schedule") {
                    $("#timetable-planner-add_slot-modalx-contact").val(ui.item.trainer);
                    $("#timetable-planner-add_slot-modalx-contact_id").val(ui.item.trainer_id);
                    $("#timetable-planner-add_slot-modalx-location").val(ui.item.location);
                    $("#timetable-planner-add_slot-modalx-location_id").val(ui.item.location_id);
                } else if($(name).parents('#timetable-planner-event-edit-form-popover').length > 0) {
                    // The main location was updated, wipe the sub location so the main location can be saved instead of it
                    if(name === '#timetable-planner-add_slot-popover-location') {
                        $('#timetable-planner-add_slot-popover-sub_location').val('');
                        $('#timetable-planner-add_slot-popover-sub_location_id').val('');
                    }
                }
                return false;
            }
        });

        $(input).on('blur', function(){
            if (input.value == '') {
                $(name + "_id").val("");
            }
        });
    }

    autocomplete_set('#calendar-filter-course', '/admin/courses/find_course');

    autocomplete_set('#timetable-planner-add_slot-popover-course', '/admin/courses/find_course');
    autocomplete_set('#timetable-planner-add_slot-modal-course', '/admin/courses/find_course');
    autocomplete_set('#timetable-planner-add_slot-modalx-course', '/admin/courses/find_course');

    autocomplete_set(
        '#timetable-planner-add_slot-popover-schedule',
        function (){
            var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-popover-course_id").val();
            return url;
        }
    );
    autocomplete_set(
        '#timetable-planner-add_slot-modal-schedule',
        function (){
            var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-modal-course_id").val();
            return url;
        }
    );
    autocomplete_set(
        '#timetable-planner-add_slot-modalx-schedule',
        function (){
            var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-modalx-course_id").val();
            return url;
        }
    );

    autocomplete_set('#timetable-planner-add_slot-popover-topic', '/admin/courses/autocomplete_topics');
    autocomplete_set('#timetable-planner-add_slot-modal-topic', '/admin/courses/autocomplete_topics');
    autocomplete_set('#timetable-planner-add_slot-modalx-topic', '/admin/courses/autocomplete_topics');

    autocomplete_set('#timetable-planner-add_slot-popover-contact', '/admin/courses/autocomplete_trainers');
    autocomplete_set('#timetable-planner-add_slot-modal-contact', '/admin/courses/autocomplete_trainers');
    autocomplete_set('#timetable-planner-add_slot-modalx-contact', '/admin/courses/autocomplete_trainers');

    autocomplete_set('#timetable-planner-add_slot-popover-location', '/admin/timetables/autocomplete_locations?children_identifier=1');
    autocomplete_set('#timetable-planner-add_slot-popover-sub_location', '/admin/timetables/autocomplete_locations?children_identifier=2');
    autocomplete_set('#timetable-planner-add_slot-modal-location', '/admin/courses/autocomplete_locations');
    autocomplete_set('#timetable-planner-add_slot-modalx-location', '/admin/courses/autocomplete_locations');

    $("#calendar-filter-course").on("change", function() {
        var course_id          = this.value && $('#calendar-filter-course_id').val();
        var course_selected    = !!course_id;
        var $schedule_dropdown = $('#timetable-planner-schedule');

        if (course_selected) {
            // If a course is selected, only allow schedules for that course to be selected.
            $schedule_dropdown.find('option').prop('disabled', true).addClass('hidden');
            $schedule_dropdown.find('option[data-course_id="'+course_id+'"]').prop('disabled', false).removeClass('hidden');
        } else {
            // If no course is selected, allow any schedule to be selected.
            $schedule_dropdown.find('option').prop('disabled', false).removeClass('hidden');
        }

        // Ensure options hidden and disabled in the select list are also hidden and disabled in the stylised dropdown.
        $schedule_dropdown.multiselect('rebuild');
    });

    $('#timetable-planner-filters').on('reset', function() {
        $(this).find('[multiple]').multiselect('refresh');
        $('#calendar-filter-course').trigger('change');

        update_calendar();
    });

    $('#timetable-planner-add_slot-modalx-schedule').on("change", function(){
        $.post(
            "/admin/courses/timetable_get_dates",
            {
                schedule_id: $('#timetable-planner-add_slot-modalx-schedule_id').val()
            },
            function (response) {
                timeslots_cache = response;
                timeslots_display_page = 0;
                render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);

                update_results_count();
            }
        )
    });

    $("#timetable-planner-add_slot-popover-starts, #timetable-planner-add_slot-popover-ends, #timetable-planner-add_slot-modal-starts, #timetable-planner-add_slot-modal-ends").datetimepicker(
        {
            datepicker : false,
            format: 'H:i',
            formatTime: 'H:i',
            step: '15'
        }
    );

    function save_calendar_timeslot(form)
    {
        var data = {};
        data.id = form.find("[name=id]").val();
        data.academicyear_id = form.find("[name=academicyear_id]").val();
        data.schedule_id = form.find("[name=schedule_id]").val();
        data.topic_id = form.find("[name=topic_id]").val();
        data.trainer_id = form.find("[name=contact_id]").val();
        // If sub location is empty, use the main location
        data.location_id = (form.find("[name=sub_location_id]").val()) ? form.find("[name=sub_location_id]").val() : form.find("[name=location_id]").val();
        data.datetime_start = form.find("[name=date]").val() + " " + form.find("[name=starts]").val();
        data.datetime_end = form.find("[name=date]").val() + " " + form.find("[name=ends]").val();
        $.post(
            "/admin/timetables/save_slot",
            data,
            function (response) {
                update_calendar();
            }
        )
    }

    $(".timetable-planner-event-edit-form .btn.save").on("click", function(){
        var form = $(this).parents("form");
        save_calendar_timeslot(form);
    });

    function edit_slot(form, data)
    {
        $(form).find("input").val("");

        if (data) {
            form.find("[name=id]").val(data.id);
            form.find("[name=academicyear_id]").val(data.academicyear_id);
            form.find("[name=academicyear]").val(data.academicyear);
            form.find("[name=schedule_id]").val(data.schedule_id);
            form.find("[name=schedule]").val(data.schedule);
            form.find("[name=topic_id]").val(data.topic_id);
            form.find("[name=topic]").val(data.topic);
            form.find("[name=contact_id]").val(data.contact_id);
            form.find("[name=contact]").val(data.contact);
            form.find("[name=location_id]").val(data.location_id);
            form.find("[name=location]").val(data.location);
            form.find("[name=date]").val(data.starts);
            form.find("[name=starts]").val(data.starts);
            form.find("[name=ends]").val(data.ends);
        }
    }

    function set_table(id, status)
    {
        var start_date = null;
        var end_date = null;
        try {
            start_date = $calendar.fullCalendar("getView").intervalStart.format("YYYY-MM-DD");
            end_date = $calendar.fullCalendar("getView").intervalEnd.format("YYYY-MM-DD");
        } catch (exc) {

        }
        var $table = $(id);

        $table.ib_serverSideTable(
            '/admin/timetables/planner_data',
            {
                aaSorting: [[ 5, 'desc']],
                sServerMethod: "POST",
                fnServerParams: function (params) {
                    params.push({name: "status", value: status});
                    params.push({name: "after", value: start_date});
                    params.push({name: "before", value: end_date});
                    params.push({name: "course_id", value: $("#calendar-filter-course_id").val()});
                    params.push({name: "schedule_id", value: $("#timetable-planner-schedule").val()});
                    params.push({name: "trainer_id", value: $("#timetable-planner-trainer_id").val()});
                    params.push({name: "location_id", value: $("#timetable-planner-location_id").val()});
                    params.push({name: "topic_id", value: $("#timetable-planner-topic_id").val()});
                }
            },
            {
                responsive: true,
                //row_data_ids: true,
                draw_callback: function() {
                    var status;
                    $table.find('.timetable-planner-timeslot-status').each(function() {
                        status = $(this).data('status');
                        $(this).parents('td').attr('data-status', status).data('status', status);
                    });
                }
            }
        );
    }

    $("#timetable-planner-requests-requires_resolution, #timetable-planner-requests-requires_approval, #timetable-planner-requests-other").on("click", ".btn.remove", function(){
        $("#slot_confirm_remove .btn.remove").attr("data-id", $(this).attr('data-id'));
        $("#slot_confirm_remove").modal();
    });

    $("#slot_confirm_remove .btn.remove").on("click", function(){
        var id = $(this).attr("data-id");

        $.post(
            '/admin/timetables/remove_slot',
            {
                id: id
            },
            function (response) {
                update_calendar();
                $("#slot_confirm_remove").modal('hide');
            }
        )
    });

    var $timeslot_conflict_tr = $("#schedule_timeslot_conflicts > tbody > tr.hidden");
    var $timeslot_conflict_tr_sub = $timeslot_conflict_tr.find("tr.hidden");
    $timeslot_conflict_tr_sub.remove();
    $timeslot_conflict_tr_sub.removeClass("hidden");
    $timeslot_conflict_tr.remove();
    $timeslot_conflict_tr.removeClass("hidden");

    function display_timeslot_conflicts(schedule_id, timelot_id, editable = true)
    {
        var $tbody = $("#schedule_timeslot_conflicts > tbody");
        $.post(
            "/admin/timetables/get_conflicts",
            {
                schedule_id: schedule_id,
                timeslot_id: timelot_id
            },
            function (response) {
                $tbody.html('');
                $("#timetable-timeslots-conflicts-modal h2 .schedule").html(response.schedule.name);
                for (var i in response['conflicts']) {
                    var timeslot = response['conflicts'][i];
                    if (timeslot.schedule_id == schedule_id ) {
                        var date = timeslot.datetime_start.split(" ");
                        var time = date[1];
                        date = date[0].split("-");
                        date = date[2] + "/" + date[1] + "/" + date[0];
                        var date2 = timeslot.datetime_end.split(" ");
                        var time2 = date2[1];
                        var $tr = $timeslot_conflict_tr.clone();
                        $tr.find(".date").val(date);
                        $tr.find(".date").datepicker({format: 'dd/mm/yyyy'});
                        $tr.find(".location").val(timeslot.location);
                        $tr.find(".location").attr("id", "resolve_timeslot_" + timeslot.id + "_location");
                        $tr.find(".location_id").val(timeslot.location_id);
                        $tr.find(".location_id").attr("name", "timeslot[" + timeslot.id + "][location_id]");
                        $tr.find(".location_id").attr("id", "resolve_timeslot_" + timeslot.id + "_location_id");
                        $tr.find(".trainer").val(timeslot.contact);
                        $tr.find(".trainer").attr("id", "resolve_timeslot_" + timeslot.id + "_trainer");
                        $tr.find(".trainer_id").val(timeslot.trainer_id ? timeslot.trainer_id : response.schedule.trainer_id);
                        $tr.find(".trainer_id").attr("name", "timeslot[" + timeslot.id + "][trainer_id]");
                        $tr.find(".trainer_id").attr("id", "resolve_timeslot_" + timeslot.id + "_trainer_id");
                        $tr.find(".start").val(time);
                        $tr.find(".start").attr("name", "timeslot[" + timeslot.id + "][datetime_start]");
                        $tr.find(".start").datetimepicker({
                            datepicker : false,
                            format: 'H:m',
                            formatTime: 'H:m'
                        });
                        $tr.find(".end").val(time2);
                        $tr.find(".end").attr("name", "timeslot[" + timeslot.id + "][datetime_end]");
                        $tr.find(".end").datetimepicker({
                            datepicker : false,
                            format: 'H:m',
                            formatTime: 'H:m'
                        });
                        $tr.find(".update").attr("name", "timeslot[" + timeslot.id + "][update]");
                        $tr.find(".update").val(timeslot.id).data('conflicts', response['conflicts']);
                        for (var j in response['conflicts']) {
                            var ctimeslot = response['conflicts'][j];
                            var dt1_start = new Date(ctimeslot.datetime_start);
                            var dt1_end = new Date(ctimeslot.datetime_end);
                            var dt2_start = new Date(timeslot.datetime_start);
                            var dt2_end = new Date(timeslot.datetime_end);
                            if (
                                ctimeslot.id != timeslot.id &&
                                (
                                    (dt1_start >= dt2_start && dt1_start < dt2_end)
                                    ||
                                    (dt1_end > dt2_start && dt1_end <= dt2_end)
                                    ||
                                    (dt2_start >= dt1_start && dt2_start < dt1_end)
                                    ||
                                    (dt2_end > dt1_start && dt2_start <= dt1_end)
                                )
                            ) {
                                var $tr_sub = $timeslot_conflict_tr_sub.clone();
                                $tr_sub.data('timeslot_2_id', ctimeslot.id);
                                $tr_sub.find(".course").html(ctimeslot.course);
                                $tr_sub.find(".schedule").html(ctimeslot.schedule);
                                $tr_sub.find(".location").html(ctimeslot.location);
                                $tr_sub.find(".staff").html(ctimeslot.contact);
                                $tr_sub.find(".conflict_start").html(ctimeslot.datetime_start);
                                $tr_sub.find(".conflict_end").html(ctimeslot.datetime_end);
                                $tr.find("tbody").append($tr_sub);
                            }
                        }

                        $tbody.append($tr);
                        autocomplete_set('#resolve_timeslot_' + timeslot.id + '_location', '/admin/courses/autocomplete_locations');
                        autocomplete_set('#resolve_timeslot_' + timeslot.id + '_trainer', '/admin/courses/autocomplete_trainers');
                    }
                }
                $("#timetable-timeslots-conflicts-modal").modal();
                if(editable === false)
                {
                    $('#schedule_timeslot_conflicts input:not(input[type="checkbox"])').prop('disabled', true);
                }
            }
        );
    }

    $("#timetable-planner-requests-requires_resolution").on("click", ".btn.resolve", function(){
        $("#schedule_timeslot_conflicts .btn.resolve").attr("data-schedule_id", $(this).attr('data-schedule_id'));
        display_timeslot_conflicts($(this).attr('data-schedule_id'), $(this).attr('data-id'));
        $("#timetable-timeslots-conflicts-modal").find('.btn.ignore-action').removeClass('ignore-action').text('Update selected slots');
    });

    $("#timetable-planner-requests-requires_resolution").on("click", ".btn.ignore", function () {
        display_timeslot_conflicts($(this).attr('data-schedule_id'), $(this).attr('data-id'), false);
        $("#schedule_timeslot_conflicts .btn.ignore").attr("data-schedule_id", $(this).attr('data-schedule_id'));
        $("#timetable-timeslots-conflicts-modal").find('.btn.resolve').addClass('ignore-action').text('Ignore selected slots');
    });


    $("#timetable-timeslots-conflicts-modal .btn.resolve, #timetable-timeslots-conflicts-confirm-modal .btn.resolve").on("click", function(){
        var timeslots = [];
        $("#schedule_timeslot_conflicts > tbody > tr .update:checked").each(function(){
            var tr = $(this).parents("tr");
            var slot = {
                id: this.value,
                location_id: tr.find(".location_id").val(),
                trainer_id: tr.find(".trainer_id").val(),
                datetime_start: tr.find(".date").datepicker("getDate").dateFormat("YYYY-MM-DD") + " " + tr.find(".start").val(),
                datetime_end: tr.find(".date").datepicker("getDate").dateFormat("YYYY-MM-DD") + " " + tr.find(".end").val(),
                conflicts: $(this).data('conflicts')
            };
            timeslots.push(slot);
        });
        if (timeslots.length > 0) {
            if ($(this).hasClass('ignore-action')) {
                $.post(
                    "/admin/timetables/ignore_timeslots",
                    {
                        timeslots: timeslots
                    },
                    function (response) {
                        $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
                        $("#timetable-timeslots-conflicts-modal").modal("hide");
                        update_calendar();
                    }
                )
            } else {
                $.post(
                    "/admin/timetables/resolve_timeslots",
                    {
                        timeslots: timeslots
                    },
                    function (response) {
                        $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
                        $("#timetable-timeslots-conflicts-modal").modal("hide");
                        update_calendar();
                    }
                )
            }
        } else {
            $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
            $("#timetable-timeslots-conflicts-modal").modal("hide");
        }
    });

    $("#slot_confirm_approve .btn.approve").on("click", function(){
        var id = $(this).attr("data-id");

        $.post(
            '/admin/timetables/approve_slot',
            {
                id: id
            },
            function (response) {
                $("#slot_confirm_approve").modal('hide');
            }
        )
    });

    var $datepicker = $("#datepicker");
    $datepicker.eventCalendar();
    $datepicker.find(".eventsCalendar-list-wrap").addClass("hidden");
    $datepicker.find(".eventsCalendar-monthWrap").css("min-width", "100%");

    function save_timeslots()
    {
        $.post(
            "/admin/timetables/save_timeslots",
            {
                schedule_id: $("#timetable-planner-add_slot-modalx-schedule_id").val(),
                location_id: $("#timetable-planner-add_slot-modalx-location_id").val(),
                trainer_id: $("#timetable-planner-add_slot-modalx-contact_id").val(),
                topic_id: $("#timetable-planner-add_slot-modalx-topic_id").val(),
                timeslots: JSON.stringify(timeslots_cache)
            },
            function (response) {
                //console.log(response);
                update_calendar();
                $("#timetable-planner-schedule_timeslots-modal").modal('hide');
                clear_dates();
            }
        )
    }

    $(".modal-header .close, .modal-footar .btn-cancel").on("click", function(){
        clear_dates();
    });

    $("#timetable-planner-schedule_timeslots-modal .btn.save, #timetables-calendar-schedule_timeslots-modal .btn.save").on("click", function(){
        save_timeslots();
    });

    function clear_slots_form()
    {
        $("#timetable-planner-add_slot-modalx-course").val("");
        $("#timetable-planner-add_slot-modalx-course_id").val("");
        $("#timetable-planner-add_slot-modalx-schedule").val("");
        $("#timetable-planner-add_slot-modalx-schedule_id").val("");
        $("#timetable-planner-add_slot-modalx-topic").val("");
        $("#timetable-planner-add_slot-modalx-topic_id").val("");
        $("#timetable-planner-add_slot-modalx-contact").val("");
        $("#timetable-planner-add_slot-modalx-contact_id").val("");
        $("#timetable-planner-add_slot-modalx-location").val("");
        $("#timetable-planner-add_slot-modalx-location_id").val("");
        $("#repeat").val("");
        $("#repeat").change();
        clear_dates();
    }

    $(".btn.add-slot").on("click", function(){
        clear_slots_form();
    });
});

$(document).on('shown.bs.popover', function(ev) {
    // timetable-planner-event-edit
    var $target  = $(ev.target);
    var is_event = ($target.hasClass('fc-event') || $target.parents('.fc-event').length);

    if (is_event) {
        var timeslot = $target[0].timeslot;
        var $form = $("#timetable-planner-event-edit-form-popover");
        $form.find("[name=id]").val(timeslot.id);
        $form.find("#timetable-planner-add_slot-popover-academicyear_id").val(timeslot.academic_year_id);
        $form.find("#timetable-planner-add_slot-popover-academicyear_id-input").val(timeslot.academic_year);
        $form.find("#timetable-planner-add_slot-popover-course").val(timeslot.course);
        $form.find("#timetable-planner-add_slot-popover-course_id").val(timeslot.course_id);
        $form.find("#timetable-planner-add_slot-popover-schedule").val(timeslot.schedule);
        $form.find("#timetable-planner-add_slot-popover-schedule_id").val(timeslot.schedule_id);
        $form.find("#timetable-planner-add_slot-popover-topic").val(timeslot.topic);
        $form.find("#timetable-planner-add_slot-popover-topic_id").val(timeslot.topic_id);
        $form.find("#timetable-planner-add_slot-popover-contact").val(timeslot.contact);
        $form.find("#timetable-planner-add_slot-popover-contact_id").val(timeslot.trainer_id);
        if (timeslot.plocation != null) {
            $form.find("#timetable-planner-add_slot-popover-location").val(timeslot.plocation);
            $form.find("#timetable-planner-add_slot-popover-location_id").val(timeslot.plocation_id);
            $form.find("#timetable-planner-add_slot-popover-sub_location").val(timeslot.location);
            $form.find("#timetable-planner-add_slot-popover-sub_location_id").val(timeslot.location_id);
        } else {
            $form.find("#timetable-planner-add_slot-popover-location").val(timeslot.location);
            $form.find("#timetable-planner-add_slot-popover-location_id").val(timeslot.location_id);
            $form.find("#timetable-planner-add_slot-popover-sub_location").val("");
            $form.find("#timetable-planner-add_slot-popover-sub_location_id").val("");
        }

        $form.find(".booking_count").html(timeslot.booking_count);
        var start = timeslot.datetime_start.split(' ');
        var end = timeslot.datetime_end.split(' ');
        $form.find("#timetable-planner-add_slot-popover-date-input").val(start[0]);
        $form.find("#timetable-planner-add_slot-popover-starts").val(start[1].substr(0, 5));
        $form.find("#timetable-planner-add_slot-popover-ends").val(end[1].substr(0, 5));
        $('.popover').find('.timetable-planner-slot_form').remove();
        var event    = $target.data('fcSeg').event;
        var end_time = new moment(event.datetime_end);
        var $popover = $target.data('bs.popover').$tip;
        var $form    = $('#timetable-planner-event-edit-form-popover');

        // Populate the form with data from the event
        document.getElementById('timetable-planner-add_slot-popover-date').value       = timeslot.start.format('YYYY-MM-DD');
        document.getElementById('timetable-planner-add_slot-popover-date-input').value = timeslot.start.format('MMMM D, YYYY');
        document.getElementById('timetable-planner-add_slot-popover-starts').value     = timeslot.start.format('HH:mm');
        document.getElementById('timetable-planner-add_slot-popover-ends').value       = end_time.format('HH:mm');
        document.getElementById('timetable-planner-add_slot-attending').innerHTML      = timeslot.attending || 0;

        $form.find('#timetable-planner-add_slot-status').val(event.status).prop('disabled', true).multiselect('refresh')
            .find('\+ .btn-group button').prop('disabled', true);

        $form.find('[name=course],[name=schedule], #timetable-planner-add_slot-popover-academicyear_id-input').prop('readonly', true);

        $popover.addClass('timetable-add_slot-popover');
        // This form needs to be passed around. .clone() or .html() solutions will cause it to lose its event listeners
        $popover.find('.popover-content').append($form);
    }
});

// Dismiss popovers when clicked away from, 'click' causes issues when mouse is released not over popup, it disappears
$(document).on('mousedown', function(ev)
{
    var $target    = $(ev.target);
    var is_event   = ($target.hasClass('fc-event') || $target.parents('.fc-event').length);
    var is_popover = ($target.hasClass('popover')  || $target.parents('.popover').length);
    var is_popover_autocomplete = ($target.parents('.ui-autocomplete').length > 0);
    if (!is_popover && !is_popover_autocomplete) {
        // Return the form to its original position, so it does not get destroyed when the popover vanishes
        $('#timetable-planner-event-edit').append($('#timetable-planner-event-edit-form-popover'));
    }

    if (!is_event && !is_popover && !is_popover_autocomplete) {
        $('.fc-event--has_popover').popover('hide');
    }
});

$(document).on('change', '#calendar-filter-course_id', function() {
    update_schedule_filter($(this).val());
});

function update_schedule_filter(course_id) {
    $('#timetable-planner-schedule').multiselect("deselectAll", false);
    $(`#timetable-planner-schedule option[data-course_id="${course_id}"]`).prop('selected', true);
    $('#timetable-planner-schedule').multiselect("refresh");
    $('#timetable-planner-schedule').change();
}