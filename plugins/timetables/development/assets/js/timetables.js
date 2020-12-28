$(document).ready(function() {
    function initDateRangePicker(id, periods, updateCallback)
    {
        periods = periods ? periods : [];

        var $daterange = $(id);
        console.log($daterange.length);

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
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month')
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

            if (range.date_start == range.date_end && range.type == "") {
                range.type = "Day";
            }

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

    $('.timetables-view_toggle').on('change', function()
    {
        var view = $('.timetables-view_toggle:checked').val();

        $('.hidden--timetables-people, .hidden--timetables-locations, .hidden--timetables-courses').removeClass('hidden');
        $('.hidden--timetables-'+view).addClass('hidden');
    });


    if (!window.ibpdata) {
        window.ibpdata = {};
    }

    initDateRangePicker('#timetables-daterange-selector', window.ibpdata.academicyears, onchange_handler);

    function set_people_autocomplete()
    {
        var $filter = $("#timetables-filter_object");
        $filter.autocomplete({
            select: function(e, ui) {
                $('#timetables-filter_object').val(ui.item.label);
                $('#timetables-filter_object_id').val(ui.item.value);
                onchange_handler();
                return false;
            },

            source: function(data, callback){
                $.get("/admin/timetables/autocomplete_contacts",
                    data,
                    function(response){
                        callback(response);
                    }
                );
            }
        });
        $filter.attr('placeholder', $filter.data('people-placeholder'));
    }

    function set_locations_autocomplete()
    {
        var $filter = $("#timetables-filter_object");
        $filter.autocomplete({
            select: function(e, ui) {
                $('#timetables-filter_object').val(ui.item.label);
                $('#timetables-filter_object_id').val(ui.item.value);
                onchange_handler();
                return false;
            },

            source: function(data, callback){
                $.get("/admin/timetables/autocomplete_locations",
                    data,
                    function(response){
                        callback(response);
                    }
                );
            }
        });
        $filter.attr('placeholder', $filter.data('locations-placeholder'));
    }

    function set_courses_autocomplete()
    {
        var $filter = $("#timetables-filter_object");
        $filter.autocomplete({
            select: function(e, ui) {
                $('#timetables-filter_object').val(ui.item.label);
                $('#timetables-filter_object_id').val(ui.item.value);
                onchange_handler();
                return false;
            },

            source: function(data, callback){
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

    set_people_autocomplete();

    $("[name=view]").on("change", function(){
        $('#timetables-filter_object').val("");
        $('#timetables-filter_object_id').val("");

        var autocomplete_type = $("[name=view]:checked").val();
        if (autocomplete_type == "courses") {
            set_courses_autocomplete();
        } else if (autocomplete_type == "locations") {
            set_locations_autocomplete();
        } else {
            set_people_autocomplete();
        }
    });


    function onchange_handler()
    {
        var range = $('#timetables-daterange-selector').get_range();
        var date_start = range.date_start;
        var date_end = range.date_end;
        var view = $("[name=view]:checked").val();
        var view_filter_id = $("#timetables-filter_object_id").val();
        var activities = $("[name='activities[]']").val();
        var blackouts = null;
        if (!view) {
            view = "people";
        }
        if ($("#mytimetables_only").val()) {
            view_filter_id = $("#mytimetables_only").val();
        }

        var start_moment = new moment(date_start);
        var end_moment = new moment(date_end);
        var period_text = start_moment.format('D/MMM/YYYY') + ' &ndash; ' + end_moment.format('D/MMM/YYYY');

        $('#timetables-period').html(period_text);

        load_timetables(date_start, date_end, view, view_filter_id, activities, blackouts);
    }

    $("[name=view], #timetable-activities").on("change", onchange_handler);

    function load_timetables(date_start, date_end, view, view_filter_id, activities, blackouts)
    {
        var params = {
            date_start: date_start,
            date_end: date_end,
            view: view,
            view_filter_id: view_filter_id,
            activities: activities,
            blackouts: blackouts
        };
        $.post(
            "/admin/timetables/load_data",
            params,
            function (response) {
                render_reports(response.reports);
                render_calendar(response.calendar);
            }
        )
    }

    var report_div = $('#timetables-reports').find('.timeoff-report');
    // report_div.remove();
    // report_div.removeClass();

    function render_reports(data)
    {
        $("#timetables-reports").html("");
        for (var i in data) {
            var div = report_div.clone();

            div.find(".timeoff-report-amount").html(data[i].amount);
            div.find(".timeoff-report-title").html(data[i].title);
            div.find(".timeoff-report-period").html(data[i].period);

            $("#timetables-reports").append(div);
        }
    }

    function render_calendar(data)
    {
        var $calendar = $('#timetables-fullcalendar');

        try {
            $calendar.fullCalendar('destroy');
        } catch (exc) {

        }
        
        $calendar.fullCalendar({
            defaultView: $("[name=grid_period]:checked").val(),
            defaultDate: $('#timetables-daterange-selector').data('daterangepicker').startDate,
            header: {
                left: 'prev,title,next',
                right: 'agendaWeek,month,listMonth'
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
                element.attr('data-type', event.type);

                if (event.allDay) {
                    element.attr('data-allDay', 1);
                }
            }
        });
    }

    $('.timetables-grid_period').on('change', function()
    {
        $('#timetables-fullcalendar, #timetable-planner-fullcalendar').fullCalendar('changeView', $("[name=grid_period]:checked").val());
    });

    $(".timeoff-range--current.active").click();
    //onchange_handler();
});
