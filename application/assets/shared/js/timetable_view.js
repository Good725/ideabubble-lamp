/**
 * @member id            {string} HTML ID for the calender
 * @member events        {array}  List of events to populate the calendar
 * @member events_url    {string} AJAX URL used to get event data
 * @member events_method {string} Request method used for retrieving events data; 'GET' or 'POST'
 *
 * @member bookings_enabled {boolean} whether or not bookings can be conducted from this calendar
 * @member view_mode     {string} The way the calender is displayed (day, week, month, list)
 * @member popover_mode  {string} The mode of the popover; read, booking
 *
 * @member $wrapper      {jQuery} jQuery object for the wrapping HTML element
 * @member $calendar     {jQuery} jQuery object for the calendar
 * @member $start_date   {jQuery} jQuery object for the start date field
 * @member $end_date     {jQuery} jQuery object for the start date field
 *
 * @method render            Draw the calendar
 * @method render_reports    Draw the metrics
 * @method update            Redraw the calendar, after getting events corresponding to the selected range
 * @method get_filters       Get all filters applied to the calendar
 *
 * @method prepare_timeslot_form    Set up event listeners in the timeslot form
 * @method prepare_multiple_timeslots_form
 * @method prepare_autocomplete     Set up an input as an autocomplete by specifying an AJAX URL
 * @method update_timeslot          Update a timeslot using data from the timeslot form
 * @method create_timeslots         Create multiple timeslots
 */
class Ib_calendar
{
    constructor(data)
    {
        this.id = data.id || '';
        this.view_mode        = data.view_mode || 'month';
        this.popover_mode     = data.popover_mode || 'read';
        this.bookings_enabled = data.bookings_enabled || false;

        this.$calendar     = $('#'+this.id+'-fullcalendar');
        this.$wrapper      = $('#'+this.id+'-wrapper');
        this.$start_date   = this.$wrapper.find('.form-daterangepicker-start_date');
        this.$end_date     = this.$wrapper.find('.form-daterangepicker-end_date');
        this.$view_toggle  = this.$wrapper.find('.ibcalendar-view-toggle');

        this.events_url    = this.$wrapper.find('.ibcalendar-timeslots-url').val() || '/frontend/contacts3/get_timetables_data';
        this.events_method = this.$wrapper.find('.ibcalendar-timeslots-url_method').val() || 'post';

        var calendar = this;

        // Ensure the filters have been applied. (Necessary if the calendar HTML has been loaded dynamically.)
        this.$wrapper.find('.form-filter .dropdown-menu :checked').trigger('change');

        this.$calendar.data('ib_calendar', this);

        this.update();

        // If this is loaded dynamically, ensure the date-range picker gets initialised
        if (cms_ns && typeof cms_ns.initialize_daterangepickers == 'function') {
            cms_ns.initialize_daterangepickers();
        }

        // Refresh the calendar when the daterange or a filter is changed
        this.$wrapper.on('apply.daterangepicker change', '.daterangepicker-main', function() {
            calendar.update();
        });
        $(document).on(':ib-form-filter-change', '.form-filter', function() {
            calendar.update();
        });

        this.prepare_timeslot_form();
        this.prepare_multiple_timeslots_form();

        // Conflict resolution
        $('#' + this.id + '-table').on('click', '.resolve', function() {
            display_timeslot_conflicts($(this).data('schedule_id'), $(this).data('id'));
            $("#timetable-timeslots-conflicts-modal").find('.btn.ignore-action').removeClass('ignore-action').text('Update selected slots');
        });

        // Timeslot deletion
        $('#timetable-slot-remove-confirm').on('click', function() {
            $.post(
                '/admin/timetables/remove_slot',
                { id: $(this).data('id') },
                function () {
                    calendar.update();
                    $('#timetable-slot-remove-modal').modal('hide');
                }
            )
        });
    }

    /**
     *  Draw the calendar
     */
    render()
    {
        let events = this.events || [];

        let i = 0;
        events.forEach(function(event) {
            events[i].title = event.title || event.schedule || event.type;
            i++;
        });

        try {
            this.$calendar.fullCalendar('destroy');
        } catch (exc) {
            console.log(exc);
        }

        var calendar = this;

        this.$calendar.fullCalendar({
            defaultView: $("[name=grid_period]:checked").val(),
            defaultDate: $('.daterangepicker-main-wrapper').find('.form-daterangepicker-start_date').val(),
            header: {
                left: 'title',
                right: '',
//            right: 'agendaDay,agendaWeek,month,listMonth'
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
            events: events,
            eventRender: function(event, element, view)
            {
                const type = event.type || (event.booking_id ? 'booking' : '');

                element.attr('data-type', type);
                element.attr('data-status', event.status).data('status', event.status);
                element.attr('data-timeslot_id', event.id);
                element[0].timeslot = event;

                var $attendance = $('#timetable-attendance-template').clone();
                $attendance.removeAttr('id').removeClass('hidden');
                $attendance.find('.timetable-attendance-amount').html(event.booking_count);

                element.find('.fc-content').prepend($attendance);

                element.attr({
                    'data-container': 'body',
                    'data-content':   '&#32;',
                    'data-placement': 'auto right',
                    'data-html':      'true',
                    'data-trigger':   'click',
                    'rel':            'popover',
                    'tabindex':       '0'
                });
                element.addClass('fc-event--has_popover');
                //element.popover();

                // Only show one popover at a time
                element.on('click', function () {
                    $('.fc-event--has_popover').not(this).popover('hide');
                });

                if (type == 'booking') {
                    element.popover({
                        title: '<a href="/admin/courses/edit_schedule?id=' + event.schedule_id + '">' + event.title + '</a>',
                        placement: (view.type == 'listMonth' ? 'bottom' : 'auto right'),
                        container: '.ib-fullcalendar .fc-view',
                        html: true,
                        content: function () {
                            $("div.popover").remove();
                            var $clone = $('#calendar-popover-template').clone();

                            $clone.find('.btn-register').attr('data-schedule_id', event.schedule_id).data('schedule_id', event.schedule_id);
                            $clone.find('.btn-register').attr('data-booking_type', event.booking_type).data('booking_type', event.booking_type);
                            $clone.find('.btn-register').attr('data-event_id', event.period_id).data('event_id', event.period_id);
                            $clone.find('.calendar-popover-category').html(event.category).attr('title', event.category);
                            $clone.find('.calendar-popover-location').html(event.location);
                            $clone.find('.calendar-popover-room').html(event.room_no);
                            $clone.find('.calendar-popover-trainer').html(event.trainer);
                            $clone.find('.calendar-popover-start_time').html(event.start.format('HH:mm'));
                            if (event.end) {
                                $clone.find('.calendar-popover-end_time').html(event.end.format('HH:mm'));
                            }
                            $clone.find('.calendar-popover-registered').html(event.attending);

                            if (event.booked) {
                                $clone.find('.calendar-popover-is_attending').removeClass('hidden');
                                $clone.find('.customize_register').addClass('hidden');
                            }
                            else {
                                $clone.find('.register_place').removeClass('hidden');
                                if (event.booking_type == 'Whole Schedule') {
                                    $clone.find('.register_place-amount').html(event.timeslots_count > 1 ? event.timeslots_count + ' sessions' : ' 1 session');
                                } else if (event.booking_type == 'Subscription') {
                                    $clone.find('.register_place-amount').html('Subscribe');
                                } else {
                                    $clone.find('.register_place-amount').html(' 1 session');
                                }
                            }

                            if (event.category == 'Supervised Study' || event.attend_all_default == 'NO') {
                                $clone.find('.customize.yes').attr("checked", "checked");
                                $clone.find('.register_place-amount').addClass('hidden');
                            } else {
                                $clone.find('.customize.no').attr("checked", "checked");
                            }

                            return $clone.html();
                        },
                        trigger: 'click focus'
                    });
                }
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
                setTimeout(function() {
                    $('.ibcalendar-range-text').html(calendar.$calendar.find('.fc-toolbar .fc-left').html());
                }, 1);
            },
        });

        this.$view_toggle.find('.timetables-grid_period').on('change', function() {
            calendar.view_mode = calendar.$view_toggle.find('.timetables-grid_period:checked').val();
            calendar.$calendar.fullCalendar('changeView', calendar.view_mode);
        });

        // When switching back to calendar mode, from overview/details mode, ensure the calendar is rendered
        $('.iblisting-mode-toggle').on('change', function() {
            if (
                $('.iblisting-mode-toggle:checked').val() == 'calendar' &&
                calendar.$calendar.find('.fc-view').html().trim() == ''
            ) {
                calendar.update();
            }
        });

    }

    /**
     * Render reports.
     * This is normally handled in iblisting.php (can merge the two if the listing JS is mode object-oriented)
     * This is a special case for when the this.timeslots_url also returns reports data.
     */
    render_reports()
    {
        if (this.reports) {
            var $wrapper = this.$wrapper.find('.timeoff-reports');
            for (let i = 0; i < this.reports.length; i++) {
                $wrapper.find('.timeoff-report:nth-child('+(parseInt(i)+1)+') .timeoff-report-amount')
                    .text(this.reports[i].amount);
                $wrapper.find('.timeoff-report:nth-child('+(parseInt(i)+1)+') .timeoff-report-title')
                    .text(this.reports[i].text || this.reports[i].title);
            }

            let range_text = get_date_range_text(this.$start_date.val(), this.$end_date.val());
            range_text = range_text || 'All time';

            $wrapper.find('.timeoff-report-period').text(range_text);
        }
    }

    /**
     * Refresh the table in the overview tab
     */
    refresh_overview_table()
    {
        const $table = $('#timetables-calendar-table');
        if ($table.length) {
            $table.dataTable().fnDraw();
        }
    }

    /**
     * Get the events corresponding to the selected range. Then redraw the calendar.
     */
    update()
    {
        var start_date = this.$start_date.val();
        var end_date = this.$end_date.val();

        if (start_date == null) {
            return;
        }
        $.ajax({
            url:     this.events_url,
            method:  this.events_method,
            data:    this.get_filters(),
            context: this
        }).done(function(response) {
            this.events = response.slots || response.data || response.calendar || [];
            this.reports = response.reports || [];

            this.render();
            this.render_reports();
            this.refresh_overview_table();
        });
    }

    /**
     * Get all filters that are currently applied in the filter portion of the UI
     *
     * @returns {Array}
     */
    get_filters()
    {
        var value, i, filters = [];
        this.$wrapper.find('.'+this.id+'-table-filter, .form-filter-selected-field').each(function (index, element) {
            value = $(element).val();

            if (value) {
                if (Array.isArray(value) || element.name.indexOf('[') > -1) {
                    value = Array.isArray(value) ? value : [value];

                    for (i = 0; i < value.length; i++) {
                        filters.push({
                            name: 'filters[' + (element.name.replace('[]', '')) + '][]',
                            value: value[i]
                        });
                    }
                } else {
                    filters.push({name: 'filters[' + element.name + ']', value: value});
                }
            }
        });

        // Add the date filters, if they have not already been applied
        if (!filters.find((obj, i) =>{ if (obj.name == 'filters[start_date]') return true })) {
            filters.push({name: 'filters[start_date]', value: this.$start_date.val()});
        }

        if (!filters.find((obj, i) =>{ if (obj.name == 'filters[end_date]') return true })) {
            filters.push({name: 'filters[end_date]', value: this.$end_date.val()});
        }
        return filters;
    }

    /**
     * Add event listeners to the timeslot popover form
     *
     * Some of this was transferred directly from timetable_planner.js. That file can deleted after the new UI is fully stable.
     */
    prepare_timeslot_form()
    {
        const calendar = this;
        // When the save button is clicked, update the timeslot
        this.$wrapper.on('click', '.timetable-planner-event-edit-form .save', function() {
            calendar.update_timeslot();
        });

        // Set up type selects
        this.prepare_autocomplete('#calendar-filter-course', '/admin/courses/find_course');
        this.prepare_autocomplete('#timetable-planner-add_slot-popover-course', '/admin/courses/find_course');
        this.prepare_autocomplete('#timetable-planner-add_slot-modal-course', '/admin/courses/find_course');
        this.prepare_autocomplete('#timetable-planner-add_slot-modalx-course', '/admin/courses/find_course');

        this.prepare_autocomplete(
            '#timetable-planner-add_slot-popover-schedule',
            function (){
                var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-popover-course_id").val();
                return url;
            }
        );
        this.prepare_autocomplete(
            '#timetable-planner-add_slot-modal-schedule',
            function (){
                var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-modal-course_id").val();
                return url;
            }
        );
        this.prepare_autocomplete(
            '#timetable-planner-add_slot-modalx-schedule',
            function (){
                var url = '/admin/courses/autocomplete_schedules?alltime=yes&course_id=' + $("#timetable-planner-add_slot-modalx-course_id").val();
                return url;
            }
        );

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

        this.prepare_autocomplete('#timetable-planner-add_slot-popover-topic', '/admin/courses/autocomplete_topics');
        this.prepare_autocomplete('#timetable-planner-add_slot-modal-topic', '/admin/courses/autocomplete_topics');
        this.prepare_autocomplete('#timetable-planner-add_slot-modalx-topic', '/admin/courses/autocomplete_topics');

        this.prepare_autocomplete('#timetable-planner-add_slot-popover-contact', '/admin/courses/autocomplete_trainers');
        this.prepare_autocomplete('#timetable-planner-add_slot-modal-contact', '/admin/courses/autocomplete_trainers');
        this.prepare_autocomplete('#timetable-planner-add_slot-modalx-contact', '/admin/courses/autocomplete_trainers');

        this.prepare_autocomplete('#timetable-planner-add_slot-popover-location', '/admin/timetables/autocomplete_locations?children_identifier=1');
        this.prepare_autocomplete('#timetable-planner-add_slot-popover-sub_location', '/admin/timetables/autocomplete_locations?children_identifier=2');
        this.prepare_autocomplete('#timetable-planner-add_slot-modal-location', '/admin/courses/autocomplete_locations');
        this.prepare_autocomplete('#timetable-planner-add_slot-modalx-location', '/admin/courses/autocomplete_locations');
    }

    /**
     * Set up event listeners for the "add slot(s)" form
     */
    prepare_multiple_timeslots_form()
    {
        const calendar = this;
        const $modal = $('#' + this.id + '-schedule_timeslots-modal');
        const id_prefix = this.id;
        var id = '#' + this.id + '-schedule_timeslots-modal';

        // Reset the modal when it is opened
        $modal.on('show.bs.modal', function(ev) {

            // todo: Replace this hack fix.
            // Clicking the start and end date fields causes the modal to open.
            // Should figure out why that is happening in order to properly prevent it.
            // For now, just don't continue if either of those fields open the modal.
            if (ev.target && ev.target && (['start_date', 'end_date'].indexOf(ev.target.id) != -1 || ev.target.tagName == 'INPUT')) {
                return null;
            }

            // Reinitialise the calendar
            try {
                $modal.find('#datepicker').html('').eventCalendar();
            } catch (exc) {
                console.log(exc);
            }

            // Reset form fields
            $('#' + id_prefix + '-schedule_timeslots-modal input, #' + id_prefix + '-schedule_timeslots-modal select').val('');
            $('#frequency').find('option').first().prop('selected', true);

            // Show the fields relevant to the selected repeat method.
            $modal.find('#repeat').change();
            clear_dates();
        });

        // When the schedule is chosen, update the timeslots table within the modal.
        $modal.find('[name="schedule_id"]').on('change', function() {
            $.post(
                "/admin/courses/timetable_get_dates",
                {
                    schedule_id: $modal.find('[name="schedule_id"]')
                },
                function (response) {
                    // These global variables were set up in schedule_form.js
                    // The system could be tidied up
                    timeslots_cache = response;
                    render_timeslots(timeslots_cache, timeslots_display_page * timeslots_display_limit, timeslots_display_limit);
                    update_results_count();
                }
            )
        });

        $modal.find('.save').on('click', function() {
            calendar.create_timeslots();
        });

    }

    /**
     * Convert an input box into a type select by supplying an AJAX URL
     * This can probably be handled better using the `ajax_typeselect` function in `Ibform`.
     * This function was simply moved from timetable_planner.js to minimise regression.
     *
     * @param name  Selector for the form field
     * @param url   AJAX URL that returns records for the autocomplete
     */
    prepare_autocomplete(name, url)
    {
        const $wrapper = this.$wrapper;

        var last_id = null;
        var last_label = null;
        var input = $wrapper.find(name)[0];

        $wrapper.find(name).autocomplete({
            source: function(data, callback){
                if (last_label != data.term) {
                    $wrapper.find(name + '_id').val('');
                }

                var json_url = (typeof(url) == 'function') ? url() : url;

                $.getJSON(
                    json_url, {
                        term: $wrapper.find(name).val(),
                    },
                    callback
                );
            },
            open: function () {
                if (last_label != input.value) {
                    $wrapper.find(name + '_id').val('');
                }
            },
            select: function (event, ui) {
                if (ui.item.label) {
                    if (ui.item.id) {
                        $wrapper.find(name + '_id').val(ui.item.id).change();
                    } else {
                        $wrapper.find(name + '_id').val(ui.item.value);
                    }
                    $wrapper.find(name).val(ui.item.label);
                    last_label = ui.item.label;
                    last_id = ui.item.value;
                } else {
                    $wrapper.find(name + "_id").val(ui.item.id);
                    last_label = ui.item.value;
                    last_id = ui.item.id;
                }
                if (name == "#timetable-planner-add_slot-modalx-schedule") {
                    $("#timetable-planner-add_slot-modalx-contact").val(ui.item.trainer);
                    $("#timetable-planner-add_slot-modalx-contact_id").val(ui.item.trainer_id);
                    $("#timetable-planner-add_slot-modalx-location").val(ui.item.location);
                    $("#timetable-planner-add_slot-modalx-location_id").val(ui.item.location_id);
                } else if($wrapper.find(name).parents('#timetable-planner-event-edit-form-popover').length > 0) {
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
                $wrapper.find( + "_id").val("");
            }
        });
    }

    /**
     * Save a timeslot with data from the popover form
     */
    update_timeslot()
    {
        let data = {};
        const $form = this.$wrapper.find('.timetable-planner-event-edit-form');
        const calendar = this;

        data.id = $form.find('[name="id"]').val();
        data.academicyear_id = $form.find('[name="academicyear_id"]').val();
        data.schedule_id = $form.find('[name="schedule_id"]').val();
        data.topic_id = $form.find('[name="topic_id"]').val();
        data.trainer_id = $form.find('[name="contact_id"]').val();
        // If sub location is empty, use the main location
        data.location_id = ($form.find('[name="sub_location_id"]').val())
            ? $form.find('[name="sub_location_id"]').val()
            : $form.find('[name="location_id"]').val();
        data.datetime_start = $form.find("[name=date]").val() + " " + $form.find("[name=starts]").val();
        data.datetime_end = $form.find("[name=date]").val() + " " + $form.find("[name=ends]").val();

        $.ajax({
            url: '/admin/timetables/save_slot',
            method: 'post',
            data: data,
            context: this,
        }).done(function (response) {
                this.update();
            });
    }

    // Save multiple timeslots at once
    create_timeslots()
    {
        const $form = $('#' + this.id + '-schedule_timeslots-modal');
        const calendar = this;

        $.post(
            '/admin/timetables/save_timeslots',
            {
                schedule_id: $form.find('[name="schedule_id"]').val(),
                location_id: $form.find('[name="location_id"]').val(),
                trainer_id:  $form.find('[name="contact_id"]').val(),
                topic_id:    $form.find('[name="topic_id"]').val(),
                timeslots:   JSON.stringify(timeslots_cache)
            },
            function () {
                calendar.update();
                $form.modal('hide');
                clear_dates();
            }
        );
    }
}

/**
 * When a popover is shown in the calendar (Needs to be made object-oriented)
 */
$(document).on('shown.bs.popover', function(ev) {
    // timetable-planner-event-edit
    var $target  = $(ev.target);
    var is_event = ($target.hasClass('fc-event') || $target.parents('.fc-event').length);

    var calendar = $('.ib-fullcalendar').data('ib_calendar');

    if (is_event) {
        var timeslot = $target[0].timeslot;

        timeslot.datetime_start = timeslot.datetime_start || timeslot.start;
        timeslot.datetime_end   = timeslot.datetime_end   || timeslot.start;

        var $form = $("#timetable-planner-event-edit-form-popover");

        $form.find("[name=id]").val(timeslot.id);
        $form.find("#timetable-planner-add_slot-popover-academicyear_id").val(timeslot.academic_year_id);
        $form.find("#timetable-planner-add_slot-popover-academicyear_id-input").val(timeslot.academic_year);
        $form.find("#timetable-planner-add_slot-popover-course").val(timeslot.course);
        $form.find("#timetable-planner-add_slot-popover-course-read").text(timeslot.course);
        $form.find("#timetable-planner-add_slot-popover-course_id").val(timeslot.course_id);
        $form.find("#timetable-planner-add_slot-popover-schedule").val(timeslot.schedule);
        $form.find("#timetable-planner-add_slot-popover-schedule-read").text(timeslot.schedule);
        $form.find("#timetable-planner-add_slot-popover-schedule_id").val(timeslot.schedule_id);
        $form.find("#timetable-planner-add_slot-popover-topic").val(timeslot.topic);
        $form.find("#timetable-planner-add_slot-popover-topic-read").text(timeslot.topic);
        $form.find("#timetable-planner-add_slot-popover-topic_id").val(timeslot.topic_id);
        $form.find("#timetable-planner-add_slot-popover-contact").val(timeslot.contact);
        $form.find("#timetable-planner-add_slot-popover-contact-read").text(timeslot.contact);
        $form.find("#timetable-planner-add_slot-popover-contact_id").val(timeslot.trainer_id);
        if (timeslot.plocation != null) {
            $form.find("#timetable-planner-add_slot-popover-location").val(timeslot.plocation);
            $form.find("#timetable-planner-add_slot-popover-location-read").text(timeslot.plocation);
            $form.find("#timetable-planner-add_slot-popover-location_id").val(timeslot.plocation_id);
            $form.find("#timetable-planner-add_slot-popover-sub_location").val(timeslot.location);
            $form.find("#timetable-planner-add_slot-popover-sub_location_id").val(timeslot.location_id);
        } else {
            $form.find("#timetable-planner-add_slot-popover-location").val(timeslot.location);
            $form.find("#timetable-planner-add_slot-popover-location-read").text(timeslot.location);
            $form.find("#timetable-planner-add_slot-popover-location_id").val(timeslot.location_id);
            $form.find("#timetable-planner-add_slot-popover-sub_location").val("");
            $form.find("#timetable-planner-add_slot-popover-sub_location_id").val("");
        }

        $form.find(".booking_count").html(timeslot.booking_count);
        var start = timeslot.datetime_start.split(' ');
        var end = timeslot.datetime_end.split(' ');
        var date = start[0];
        var start_time = start[1].substr(0, 5);
        var end_time = end[1].substr(0, 5);
        $form.find("#timetable-planner-add_slot-popover-date-input").val(date);
        $form.find("#timetable-planner-add_slot-popover-starts").val(start_time);
        $form.find("#timetable-planner-add_slot-popover-ends").val(end_time);

        var start_date = new Date(start);
        $form.find("#timetable-planner-add_slot-popover-date-read").text(start_date.dateFormat('l j F Y'));
        $form.find("#timetable-planner-add_slot-popover-starts-read").text(start_time);
        $form.find("#timetable-planner-add_slot-popover-ends-read").text(end_time);

        $.ajax({
            url: '/admin/courses/timeslot_attendees_table/'+timeslot.id
        }).done(function(response) {
            $('#timetable-planner-event-tab-attendees-count').html(response.count);
            $('#timetable-planner-event-attendees-wrapper').html(response.html);

            // If the user does not have access, open the details tab and hide the tabs.
            if (!response.has_access) {
                $('[href="#timetable-planner-event-tab-details"]').tab('show');
            }
            $('#timetable-planner-event-tabs').toggleClass('hidden', !response.has_access);
        });

        $('.popover').find('.timetable-planner-slot_form').remove();
        var event    = $target.data('fcSeg').event;
        var end_time = new moment(event.datetime_end);
        var $popover = $target.data('bs.popover').$tip;

        if (!calendar.bookings_enabled) {
            $form.find('.calendar-popover-is_attending, .customize_register, .customize_register + hr').addClass('hidden');
        }
        else if (timeslot.booked) {
            $form.find('.calendar-popover-is_attending').removeClass('hidden');
            $form.find('.customize_register').addClass('hidden');
        }
        else {
            $form.find('.register_place').removeClass('hidden');
            if (timeslot.booking_type == 'Whole Schedule') {
                $form.find('.register_place-amount').html(timeslot.timeslots_count > 1 ? timeslot.timeslots_count + ' sessions' : ' 1 session');
            } else if (event.booking_type == 'Subscription') {
                $form.find('.register_place-amount').html('Subscribe');
            } else {
                $form.find('.register_place-amount').html(' 1 session');
            }
        }

        $form.find('.btn-register')
            .attr('data-schedule_id', timeslot.schedule_id).data('schedule_id', timeslot.schedule_id)
            .attr('data-booking_type', timeslot.booking_type).data('booking_type', timeslot.booking_type)
            .attr('data-event_id', timeslot.id).data('event_id', timeslot.id);


        $form.find('.customize_register').toggleClass('hidden', !calendar.bookings_enabled || timeslot.booked);

        // Populate the form with data from the event
        $('#timetable-planner-add_slot-popover-date').val(timeslot.start.format('YYYY-MM-DD'));
        $('#timetable-planner-add_slot-popover-date-input').val(timeslot.start.format('MMMM D, YYYY'));
        $('#timetable-planner-add_slot-popover-starts').val(timeslot.start.format('HH:mm'));
        $('#timetable-planner-add_slot-popover-ends').val(end_time.format('HH:mm'));
        $('#timetable-planner-add_slot-attending').html(timeslot.attending || 0);

        $form.find('#timetable-planner-add_slot-status').val(event.status).prop('disabled', true).multiselect('refresh')
            .find('\+ .btn-group button').prop('disabled', true);

        $form.find('[name=course],[name=schedule], #timetable-planner-add_slot-popover-academicyear_id-input').prop('readonly', true);

        $popover.addClass('timetable-add_slot-popover');
        // This form needs to be passed around.
        // .clone() or .html() solutions will cause it to lose its event listeners and IDs to not be unique
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


/** Conflict resolution
 * Copied from the old JS. Todo: make this more object-oriented
 *
 * @param schedule_id
 * @param timeslot_id
 * @param editable
 */
function display_timeslot_conflicts(schedule_id, timeslot_id, editable = true)
{
    var $tbody = $("#schedule_timeslot_conflicts > tbody");
    $.post(
        "/admin/timetables/get_conflicts",
        {
            schedule_id: schedule_id,
            timeslot_id: timeslot_id
        },
        function (response) {
            var $timeslot_conflict_tr = $("#schedule_timeslot_conflicts > tbody > tr.hidden");
            var $timeslot_conflict_tr_sub = $timeslot_conflict_tr.find("tr.hidden");

            $tbody.find('tr:not(.hidden)').remove();
            $("#timetable-timeslots-conflicts-modal h2 .schedule").html(response.schedule.name);
            for (var i in response['conflicts']) {
                var timeslot = response['conflicts'][i];
                if (timeslot.schedule_id == schedule_id ) {
                    var date = timeslot.datetime_start.split(" ");
                    var time = date[1] ? date[1].substr(0, 5) : '';
                    date = date[0].split("-");
                    date = date[2] + "/" + date[1] + "/" + date[0];
                    var date2 = timeslot.datetime_end.split(" ");
                    var time2 = date2[1] ? date2[1].substr(0, 5) : '';
                    var $tr = $timeslot_conflict_tr.clone();
                    $tr.find(".date").val(date);
                    $tr.find(".date").datepicker({format: 'dd/mm/yyyy', position: 'bottom'});
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
                        format: 'H:i',
                    });
                    $tr.find(".end").val(time2);
                    $tr.find(".end").attr("name", "timeslot[" + timeslot.id + "][datetime_end]");
                    $tr.find(".end").datetimepicker({
                        datepicker : false,
                        format: 'H:i',
                    });
                     $tr.removeClass('hidden');

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
                            $tr_sub.removeClass("hidden");

                            // Highlight conflicting information
                            $tr.find(".location").toggleClass('border-danger', (timeslot.location_id == ctimeslot.location_id));
                            $tr.find(".trainer").toggleClass('border-danger', (timeslot.trainer_id == ctimeslot.trainer_id));

                            $tr.find("tbody").append($tr_sub);
                        }
                    }

                    $tbody.append($tr);
                    autocomplete_set('#resolve_timeslot_' + timeslot.id + '_location', '/admin/courses/autocomplete_locations');
                    autocomplete_set('#resolve_timeslot_' + timeslot.id + '_trainer', '/admin/courses/autocomplete_trainers');
                }
            }
            $("#timetable-timeslots-conflicts-modal").modal();
            if (editable === false)
            {
                $('#schedule_timeslot_conflicts input:not(input[type="checkbox"])').prop('disabled', true);
            }
        }
    );
}

$(document).on('click', '#timetable-timeslots-conflicts-confirm-modal-trigger', function() {
    if ($('#schedule_timeslot_conflicts_form').validationEngine('validate')) {
        $('#timetable-timeslots-conflicts-confirm-modal').modal();
    }
});

$(document).on("click", '#timetable-timeslots-conflicts-confirm-modal .btn.resolve', function() {
    var timeslots = [];
    $("#schedule_timeslot_conflicts > tbody > tr .update:checked").each(function(){
        var tr = $(this).parents("tr");
        var slot = {
            id: this.value,
            location_id: tr.find(".location_id").val(),
            trainer_id: tr.find(".trainer_id").val(),
            datetime_start: tr.find(".date").datepicker("getDate").dateFormat("Y-m-d") + " " + tr.find(".start").val(),
            datetime_end: tr.find(".date").datepicker("getDate").dateFormat("Y-m-d") + " " + tr.find(".end").val(),
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
                    $("#timetable-timeslots-conflicts-modal").modal("hide");
                    $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
                    $('.ib-fullcalendar').data('ib_calendar').update()
                }
            )
        } else {
            $.post(
                "/admin/timetables/resolve_timeslots",
                {
                    timeslots: timeslots
                },
                function (response) {
                    $("#timetable-timeslots-conflicts-modal").modal("hide");
                    $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
                    $('.ib-fullcalendar').data('ib_calendar').update()

                    // Refresh the table
                    $('#timetables-calendar-table').dataTable().fnDraw();
                }
            )
        }
    } else {
        $("#timetable-timeslots-conflicts-modal").modal("hide");
        $("#timetable-timeslots-conflicts-confirm-modal").modal("hide");
    }
});

// Remove a timeslot, within the overview tab
$(document).on('click', '.timetable-slot-remove', function() {
    $('#timetable-slot-remove-confirm').data('id', $(this).data('id'));
    $('#timetable-slot-remove-modal').modal();
});

