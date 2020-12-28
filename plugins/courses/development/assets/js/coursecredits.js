$(document).ready(function() {
    function initDateRangePicker(id, periods, updateCallback)
    {

        var $daterange = $(id);

        var ranges = {};
        var start_date, end_date;

        ranges['Yesterday']  = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
        ranges['Today']      = [moment(), moment()];
        ranges['Tomorrow']   = [moment().add(1, 'days'), moment().add(1, 'days')];

        ranges['Last Week']  = [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')];
        ranges['This Week']  = [moment().startOf('week'), moment().endOf('week')];
        ranges['Next Week']  = [moment().add(1, 'week').startOf('week'), moment().add(1, 'week').endOf('week')];

        ranges['Last Month'] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
        ranges['This Month'] = [moment().startOf('month'), moment().endOf('month')];
        ranges['Next Month'] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

        ranges['Last Year']  = [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')];
        ranges['This Year']  = [moment().startOf('year'), moment().endOf('year')];
        ranges['Next Year']  = [moment().add(1, 'year').startOf('year'), moment().add(1, 'year').endOf('year')];

        for (var i = 0; i < periods.length; i++) {
            start_date = new Date(clean_date_string(periods[i].start_date));
            start_date = moment(start_date);
            end_date = new Date(clean_date_string(periods[i].end_date));
            end_date = moment(end_date);

            ranges['Period: '+periods[i].title] = [start_date, end_date];
        }

        $daterange.daterangepicker({
            alwaysShowCalendars: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoapply: false,
            applyClass: 'btn btn-primary timeoff-daterange-apply',
            cancelClass: 'btn-cancel',
            linkedCalendars: false,
            ranges: ranges,
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year')
        }, updateCallback);

        // return the the range type of the dashboard (Day, Week, Month, Year) and the number of days in the range
        $.fn.get_range = function () {
            var label = $(this).data('daterangepicker').chosenLabel;
            var date_from = $(this).data('daterangepicker').startDate;
            var date_to = $(this).data('daterangepicker').endDate;
            var days_diff = date_from.diff(date_to, 'days');
            var range_type;

            if (typeof label != 'undefined') {
                var range_types = {
                    'Today': 'day',
                    'Yesterday': 'day',
                    'This Week': 'week',
                    'Last Week': 'week',
                    'This Month': 'month',
                    'Last Month': 'month',
                    'This Year': 'year',
                    'Last Year': 'year'
                };

                for (var i = 0; i < periods.length; i++) {
                    range_types['Period: '+periods[i].title] = 'year';
                }

                range_type = (typeof range_types[label] == 'undefined') ? label : range_types[label];
                range_type = range_type.charAt(0).toUpperCase() + range_type.substr(1); // capitalise the first letter
            }
            else {
                switch (Math.abs(days_diff)) {
                    case 1   :
                        range_type = 'Day';
                        break;
                    case 7   :
                        range_type = 'Week';
                        break;
                    case 365 :
                        range_type = 'Year';
                        break;
                    case 366 :
                        range_type = 'Year';
                        break;
                    default  :
                        range_type = '';
                        break;
                }
            }

            return {"type": range_type, "days_diff": days_diff, date_start: date_from.format("YYYY-M-D"), date_end: date_to.format("YYYY-M-D")};
        };

        $('#timeoff-daterange-prev, #timeoff-daterange-next').on('click', function ()
        {
            var is_prev_btn = ($(this).attr('id') == 'timeoff-daterange-prev');
            var date_from = $daterange.data('daterangepicker').startDate;
            var date_to = $daterange.data('daterangepicker').endDate;
            var range = $daterange.get_range();
            var range_text = $('#timeoff-period').text();
            var is_period  = (range_text.indexOf('Period') != -1);

            var period = range_text.replace('Period: ', '').trim();

            $daterange.data('daterangepicker').chosenLabel = range.type;

            if (range.type != '') {
                is_prev_btn ? date_from.subtract(1, range.type) : date_from.add(1, range.type);
                is_prev_btn ? date_to.subtract(1, range.type) : date_to.add(1, range.type);
            }
            else {
                is_prev_btn ? date_from.subtract(range.days_diff, 'days') : date_from.add(range.days_diff, 'days');
                is_prev_btn ? date_to.subtract(range.days_diff, 'days') : date_to.add(range.days_diff, 'days');
            }

            $daterange.data('daterangepicker').setStartDate(date_from.format('YYYY-MM-DD'));
            $daterange.data('daterangepicker').setEndDate(date_to.format('YYYY-MM-DD'));
            $daterange.trigger('apply.daterangepicker');

            var new_range_text = range.type;

            if (is_period && !isNaN(period)) {
                new_range_text = 'Period: '+(is_prev_btn ? parseInt(period) - 1 :parseInt(period) + 1);
            }

            onchange_handler();
        });

        $daterange.on('show.daterangepicker', function()
        {
            var $container = $daterange.data('daterangepicker').container;

            $container.addClass('timeoff-daterangepicker-container');

            var $ranges = $container.find('.ranges');

            // Add tabs to the range selector
            // A bit messy, but the daterangepicker does not natively support this
            if ($ranges.find('.nav-tabs').length == 0) {
                var item_text;
                var periods = 0;

                $ranges.find('li').each(function() {
                    item_text = $(this).text().trim().toLowerCase();

                    $(this).addClass('timeoff-range');

                    if (item_text.indexOf('last') == 0 || item_text == 'yesterday') {
                        $(this).addClass('timeoff-range--last');
                    } else if (item_text.indexOf('next') == 0 || item_text == 'tomorrow') {
                        $(this).addClass('timeoff-range--next');
                    } else if (item_text.indexOf('period') == 0) {
                        if (periods == 0) {
                            $(this).before('<li>Period</li>');
                        }
                        $(this).addClass('timeoff-range--all timeoff-range--period');
                        periods++;
                    } else if (item_text == 'custom range') {
                        $(this).addClass('timeoff-range--all');
                    } else {
                        $(this).addClass('timeoff-range--current');
                    }
                });

                var $tabs = $('#timeoff-daterange-tabs');

                $tabs.removeClass('hidden').prependTo($ranges);
                $tabs.find('[data-tab]').on('click', function() {
                    $('.timeoff-range:not(.timeoff-range--all)').addClass('hidden');
                    $('.timeoff-range--'+$(this).data('tab')).removeClass('hidden');

                    $tabs.find('[data-tab]').removeClass('active');
                    $(this).addClass('active');
                });

                $container.find('.cancelBtn').removeClass('btn');

                $tabs.find('[data-tab="current"]').trigger('click');
            }
        });

        onchange_handler();

        return $daterange;
    }

    function display_credit_details(data)
    {
        var $form = $("#credit-details-form");
        $("#credit-schedules-list ul li").remove();
        if (data) {
            $form.find("[name='id']").val(data.id);
            $form.find("[name='academicyear_id']").val(data.academicyear_id);
            $form.find("[name='subject_id']").val(data.subject_id);
            $form.find("[name='type']").val(data.type);
            $form.find("[name='credit']").val(data.credit);
            $form.find("[name='hours']").val(data.hours);
            $form.find("[name='course_id']").val(data.course_id);
            $form.find("[name='course']").val(data.course);
            for (var i in data.schedules) {
                add_schedule(data.schedules[i].schedule_id, data.schedules[i].schedule);
            }
        } else {
            $form.find("[name='id']").val("");
            $form.find("[name='academicyear_id']").val("");
            $form.find("[name='subject_id']").val("");
            $form.find("[name='type']").val("");
            $form.find("[name='credit']").val("");
            $form.find("[name='hours']").val("");
            $form.find("[name='course_id']").val("");
            $form.find("[name='course']").val("");
        }
        $("#credit-details-modal").modal();
    }

    function set_courses_autocomplete()
    {
        var $filter = $("#credit-details-form [name=course]");
        $filter.autocomplete({
            select: function(e, ui) {
                $('#credit-details-form [name=course]').val(ui.item.value);
                $('#credit-details-form [name=course_id]').val(ui.item.id);
                // Reset Fields
                $('select[name=type]').val("");
                $('select[name=type] > option').attr('hidden', true);
                $('select[name=type] > option[selected]').attr('selected', false);
                $('select[name=subject_id]').val("");
                $('select[name=subject_id] > option').attr('hidden', true);
                $('select[name=subject_id] > option[selected]').attr("selected", false);
                $('#credit-schedules-list > ul > li').remove();
                if(ui.item.subject_id !== null){
                    $('select[name=subject_id] > option[value=' + ui.item.subject_id + ']').attr('hidden', false).attr('selected', true);
                    $('select[name=subject_id]').val(ui.item.subject_id);
                }
                if (ui.item.type !== null) {
                    // Remove whitespace, value is not Course type ID yet
                    $('select[name=type] > option:contains(' + ui.item.type.replace(/\s/g, "") + ')').attr('hidden', false).attr('selected', true).val(ui.item.type);
                    $('select[name=type]').val(ui.item.type);
                }

                return false;
            },

            source: function(data, callback){
                $.get("/admin/bookings/find_course",
                    data,
                    function(response){
                        callback(response);
                    }
                );
            }
        });
        $filter.attr('placeholder', $filter.data('courses-placeholder'));
    }

    function set_schedules_autocomplete()
    {
        var $filter = $("#credit-schedules-autocomplete");
        $filter.autocomplete({
            select: function(e, ui) {
                $('#credit-schedules-autocomplete').val(ui.item.label);
                $('#credit-schedules-autocomplete').data("schedule-id", ui.item.value);
                return false;
            },

            source: function(data, callback){
                data.course_id = $('#credit-details-form [name=course_id]').val();
                $.get("/admin/timetables/autocomplete_schedules",
                    data,
                    function(response){
                        callback(response);
                    }
                );
            }
        });
        $filter.attr('placeholder', $filter.data('courses-placeholder'));
    }

    var $schedule_li = $("#credit-schedules-list ul li.hidden");
    $schedule_li.remove();
    function remove_schedule_handler()
    {
        $(this).parents("li").remove();
    }
    function add_schedule(id, schedule)
    {
        var $li = $schedule_li.clone();
        $li.find("span").html(schedule);
        $li.find("input").val(id);
        $li.find(".remove").on("click", remove_schedule_handler);
        $li.removeClass("hidden");
        $("#credit-schedules-list ul").append($li);
    }

    function set_add_schedule_button()
    {
        $("#credit-schedules-add").on("click", function(){
            if (!isNaN(parseInt($('#credit-schedules-autocomplete').data("schedule-id")))) {
                add_schedule(
                    $('#credit-schedules-autocomplete').data("schedule-id"),
                    $('#credit-schedules-autocomplete').val()
                );

                $('#credit-schedules-autocomplete').val("");
                $('#credit-schedules-autocomplete').data("schedule-id", "");
            } else {
                $("#warning-select-schedule-modal").modal();
            }
        });
    }

    function save_credit()
    {
        var data = {};
        data.id = $("#credit-details-form [name=id]").val();
        data.academicyear_id = $("#credit-details-form [name=academicyear_id]").val();
        data.course_id = $("#credit-details-form [name=course_id]").val();
        data.subject_id = $("#credit-details-form [name=subject_id]").val();
        data.type = $("#credit-details-form [name=type]").val();
        data.credit = $("#credit-details-form [name=credit]").val();
        data.hours = $("#credit-details-form [name=hours]").val();
        data.schedule_ids = [];

        $("#credit-details-form [name='schedule_id[]']").each(function(){
            data.schedule_ids.push(this.value);
        });

        if (data.course_id.length == 0 || data.subject_id == 0 || data.type == 0 || data.credit == 0 || data.hours == 0 || data.schedule_ids.length == 0) {
            alerts = $('#credit-details-modal .alert-area');
            alerts.add_alert('Please fill in the required form fields.', 'warning');
        } else {
            $.post(
                "/admin/coursecredits/save",
                data,
                function (response) {
                    $("#credit-details-modal").modal("hide");
                    onchange_handler();
                }
            )
        }
    }

    function set_save_button()
    {
        $("#save-credit-button").on("click", function(){
            save_credit();
        });
    }

    function set_credits_list_table(id, params)
    {
        var $credits_table = $(id);
        var ajax_source = '/admin/coursecredits/list';
        var settings =  {
            "fnServerParams" : function(aoData) {
                if ($("#course-credits-course").val()) {
                    aoData.push({name: 'course_id', value: $("#course-credits-course").val()});
                }
                if ($("#course-credits-filters-modules").val()) {
                    aoData.push({name: 'subject_id', value: $("#course-credits-filters-modules").val()});
                }
                if ($("#course-credits-filters-types").val()) {
                    aoData.push({name: 'type', value: $("#course-credits-filters-types").val()});
                }
                var range = $('#coursecredits-daterange-selector').get_range();
                var date_start = range.date_start;
                var date_end = range.date_end;
                aoData.push({name: 'after', value: date_start});
                aoData.push({name: 'before', value: date_end});

                if (params) {
                    for (var i in params) {
                        aoData.push({name: i, value: params[i]});
                    }
                }
            },
            "aoColumnDefs" : [
                {
                    "aTargets": [1],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {

                    }
                }
            ],
        };
        var drawback_settings = {
            "fnDrawCallback": function() {
                $credits_table.find(".btn.view").on("click", function (){
                    var id = $(this).data("id");
                    $.get(
                        "/admin/coursecredits/load",
                        {id: id},
                        function (response) {
                            if (response.credit) {
                                display_credit_details(response.credit);
                            }
                        }
                    )
                });
            }};
        $credits_table.ib_serverSideTable(ajax_source, settings, drawback_settings);
    }

    // Add an alert to a message area.
    // e.g. $('#page_notification_area').add_alert('Page successfully saved', 'success');
    (function ($) {
        $.fn.add_alert = function (message, type, args) {
            var $alert = $('<div class="alert' + ((type) ? ' alert-' + type : '') + '">' +
                '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
                '</div>');
            $(this).append($alert);

            var autoscroll = (args && args.autoscroll) ? args.autoscroll : true;

            if (autoscroll && typeof (this[0]) != 'undefined') {
                this[0].scrollIntoView();
            }

            // Dismiss the alert after 10 seconds
            setTimeout(function () {
                $alert.fadeOut();
            }, 10000);
        };
    })(jQuery);

    function get_trainer_totals()
    {
        var params = {};
        if ($("#course-credits-course").val()) {
           params.course_id = $("#course-credits-course").val();
        }
        if ($("#course-credits-filters-modules").val()) {
            params.subject_id = $("#course-credits-filters-modules").val();
        }
        if ($("#course-credits-filters-types").val()) {
            params.type = $("#course-credits-filters-types").val();
        }
        var range = $('#coursecredits-daterange-selector').get_range();
        var date_start = range.date_start;
        var date_end = range.date_end;
        params.after = date_start;
        params.before = date_end;

        $.get(
            "/admin/coursecredits/trainer_totals",
            params,
            function (response) {
                var $tbody = $("#credit-totals-details-table > tbody");
                $tbody.html("");
                var total_credit = 0;
                var total_hours = 0;
                for (var i in response) {
                    var tr = '<tr data-trainer_id="' + response[i].trainer_id + '">' +
                            '<td class="trainer">' + response[i].first_name + ' ' + response[i].last_name + '</td>' +
                            '<td>' + response[i].total_credit + '</td>' +
                            '<td>' + response[i].total_hours + '</td>' +
                            '</tr>';
                    $tbody.append(tr);

                    total_credit += parseFloat(response[i].total_credit);
                    total_hours += parseFloat(response[i].total_hours);
                }

                var tr = '<tr>' +
                    '<td>Total: </td>' +
                    '<td>' + total_credit + '</td>' +
                    '<td>' + total_hours + '</td>' +
                    '</tr>';
                $("#credit-totals-details-table > tfoot").html(tr);

                $tbody.find(".trainer").on("click", function(){
                    var trainer_id = $(this).parents("tr").data("trainer_id");
                    get_trainer_details(trainer_id);
                });
            }
        );
    }

    function get_calendar_totals()
    {
        var params = {};
        if ($("#course-credits-course").val()) {
            params.course_id = $("#course-credits-course").val();
        }
        if ($("#course-credits-filters-modules").val()) {
            params.subject_id = $("#course-credits-filters-modules").val();
        }
        if ($("#course-credits-filters-types").val()) {
            params.type = $("#course-credits-filters-types").val();
        }
        if ($("#course-credits-filters-unit").val()) {
            params.unit = $("#course-credits-filters-unit").val();
        }
        var range = $('#coursecredits-daterange-selector').get_range();
        var date_start = range.date_start;
        var date_end = range.date_end;
        params.after = date_start;
        params.before = date_end;

        $.get(
            "/admin/coursecredits/calendar_totals",
            params,
            function (response) {
                render_calendar(response);
            }
        );
    }

    function render_calendar(data)
    {
        var $calendar = $('#credit-totals-calendar');

        try {
            $calendar.fullCalendar('destroy');
        } catch (exc) {

        }

        $calendar.fullCalendar({
            header: {
                left: 'title',
                //right: 'agendaWeek,month,listMonth'
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
            events: data,
            eventRender: function(event, element, view)
            {
                element.on("click", function(){
                    set_credits_list_table("#trainer-credits-list-details-table", {date: event.start.format("YYYY-MM-DD")});
                    $("#trainer-credit-details-modal").modal();
                });
                element.attr('data-type', event.type);

                if (event.allDay) {
                    element.attr('data-allDay', 1);
                }
            }
        });
    }

    function get_trainer_details(trainer_id)
    {
        set_credits_list_table("#trainer-credits-list-details-table", {trainer_id: trainer_id});
        $("#trainer-credit-details-modal").modal();
    }

    function get_stats()
    {
        var params = {};
        if ($("#course-credits-course").val()) {
            params.course_id = $("#course-credits-course").val();
        }
        if ($("#course-credits-filters-modules").val()) {
            params.subject_id = $("#course-credits-filters-modules").val();
        }
        if ($("#course-credits-filters-types").val()) {
            params.type = $("#course-credits-filters-types").val();
        }
        if ($("#course-credits-filters-unit").val()) {
            params.unit = $("#course-credits-filters-unit").val();
        }
        var range = $('#coursecredits-daterange-selector').get_range();
        var date_start = range.date_start;
        var date_end = range.date_end;
        params.after = date_start;
        params.before = date_end;

        $.get(
            "/admin/coursecredits/stats",
            params,
            function (response) {
                $($(".timeoff-report")[0]).find(".timeoff-report-amount").html(response.target);
                $($(".timeoff-report")[1]).find(".timeoff-report-amount").html(response.to_schedule);
                $($(".timeoff-report")[2]).find(".timeoff-report-amount").html(response.planned);
                $($(".timeoff-report")[3]).find(".timeoff-report-amount").html(response.completed);
                $($(".timeoff-report")[4]).find(".timeoff-report-amount").html(response.pending);

                var range = $('#coursecredits-daterange-selector').get_range();
                var date_start = range.date_start.split("-").reverse().join("/");
                var date_end = range.date_end.split("-").reverse().join("/");
                $(".timeoff-report .timeoff-report-period").html(
                    date_start + " - " + date_end
                );
            }
        );
    }

    function switch_view(view)
    {
        $(".credits-list-view").addClass("hidden");
        $("#credits-list-" + view).removeClass("hidden");
    }

    var onchange_handler_timeout = false;
    function onchange_handler()
    {
        clearTimeout(onchange_handler_timeout);
        onchange_handler_timeout = setTimeout(function(){
            set_credits_list_table("#credits-list-overview-table");
            get_trainer_totals();
            get_calendar_totals();
            get_stats();
        }, 30);
    }

    set_courses_autocomplete();
    set_schedules_autocomplete();
    set_add_schedule_button();
    set_save_button();
    initDateRangePicker('#coursecredits-daterange-selector', window.ibpdata.academicyears, onchange_handler);
    $("#course-credits-create-btn").on("click", function (){
        display_credit_details();
    });
    $("#switch-view").on("click", function(){
        if ($("#switch-view .coursecredits-view_toggle:checked").val() == "details") {
            switch_view("details");
        } else {
            switch_view("overview");
        }
        onchange_handler();
        //return false;
    });

    $("#course-credits-course, #course-credits-filters-modules, #course-credits-filters-types, #course-credits-filters-unit").on("change", onchange_handler);

    onchange_handler();

    get_stats();
});
