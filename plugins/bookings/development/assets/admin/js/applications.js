(function() {
    var stage = document.getElementById('application-stage').value;

    // When a course is selected, update the list of schedules, then refresh the reports
    $('#applications-course').on('change', function() {
        update_schedules_list(this.value, refresh_reports);
    });

    // When any field with the potential to filter the displayed applications is changed, update the reports
    $('.refresh_application_reports').on('change', refresh_reports);

    // Load the DataTable on the initial page load
    $(document).ready(initialize_table);

    // Update statuses on the spot
    $('#applications-list-table').on('change', '.application-status-radio', function() {
        var name       = $(this).attr('name');
        var status     = $('.application-status-radio[name="'+name+'"]:checked').val();
        var booking_id = $(this).data('booking_id');

        // Save data server-side
        $.ajax({
            url: '/admin/applications/ajax_update_status',
            data: {
                booking_id   : booking_id,
                status_group : $(this).data('status_group'),
                status       : status
            }
        }).done(function(data) {
            data = JSON.parse(data);
            // Display success/error message
            var type = (data.success ? 'success' : 'danger');
            $('#applications-alert_area').add_alert(data.message, type);

            refresh_reports();
            refresh_datatable();
        });
    });

    // When the interviews modal is opened, pre-fill it with data for the selected interview
    $('#applications-interviews-edit-modal').on('show.bs.modal', function(ev) {
        // Get the booking ID from the button used to open the modal
        var booking_id = $(ev.relatedTarget).data('booking_id');

        // Fetch data serverside
        $.ajax('/admin/applications/ajax_get_interview_details/?booking_id='+booking_id)
            .done(function(data) {
                data = JSON.parse(data);

                // Update modal title and form fields
                $('#applications-interviews-edit-modal-name').text(data.applicant_name);

                // Timeslots dropdown to only show timeslots for the selected schedule
                var options_html = html_options_from_rows('id', 'label', data.timeslots, data.interview_slot_id, {value: '', label: 'Please select'});
                $('#edit-interview-slot').html(options_html);

                $('#edit-interview-academic_period').find('[value="' + data.academic_period_id + '"]').prop('selected', true);
                $('#edit-interview-booking_id'     ).val(data.booking_id );
                $('#edit-interview-schedule'       ).val(data.schedule_id).trigger('change');
                $('#edit-interview-course'         ).val(data.course_id  ).trigger('change');
            });
    });

    // When the interview's course is changed, update the schedules dropdown to suit
    $('#edit-interview-course').on('change', function(event) {
        // Get the schedule IDs
        $.ajax('/admin/courses/ajax_get_schedules_data?course_id=' + this.value).done(function (data) {
            // Put the schedule IDs in an array.
            var schedules = JSON.parse(data);
            var schedule_ids = [];
            for (var i = 0; i < schedules.length; i++) {
                schedule_ids.push(schedules[i].id);
            }

            // Hide and disable irrelevant schedules from the schedule selector.
            var $selector = $('#edit-interview-schedule');
            var schedule_options = document.querySelectorAll('#edit-interview-schedule option');
            var show_option;
            for (i = 0; i < schedule_options.length; i++) {
                show_option = (schedule_ids.indexOf(schedule_options[i].value) > -1 || schedule_options[i].value == '');

                schedule_options[i].disabled = !show_option;
                $(schedule_options[i]).toggleClass('hidden', !show_option);
            }

            // If the previously selected option has been disabled, select the empty option
            if ($selector.find(':selected:disabled').length) {
                $selector.val('').trigger('change');
            }
        });
    });

    // When the interview's schedule is changed, update the timeslots dropdown to suit
    $('#edit-interview-schedule').on('change', function(event) {
        $.ajax('/admin/applications/ajax_get_schedule_timeslots?schedule_id=' + this.value).done(function (data) {
            data = JSON.parse(data);

            var $slot = $('#edit-interview-slot');
            var options_html = html_options_from_rows('id', 'label', data, $slot.val(), {value: '', label: 'Please select'});
            $slot.html(options_html);
        });
    });

    // Edit interview data on the spot
    // This currently only works if the new timeslot is for the same interview. We need to repopulate the dropdown, when the user changes schedule
    $('.edit-interview-save').on('click', function() {
        // Update data server-side
        $.ajax({
            url: '/admin/applications/ajax_change_interview_date',
            data: {
                booking_id : $('#edit-interview-booking_id').val(),
                period_id  : $('#edit-interview-slot').val(),
                send_email : $(this).data('send_email')
            }
        })
            .done(function(data) {
                data = JSON.parse(data);

                // Display success/error message
                var type = data.success ? 'success' : 'danger';
                var $alerts = $('#applications-alert_area');
                $alerts.add_alert(data.message, type);

                if (data.email_message) {
                    type = data.email_sent ? 'success' : 'danger';
                    $alerts.add_alert(data.email_message, type);
                }

                // If successful, refresh the DataTable and dismiss the modal.
                if (data.success) {
                    refresh_datatable();
                    $('#applications-interviews-edit-modal').modal('hide');
                }
            });
    });

    // Update the schedules multiselect to only show options for the selected course
    function update_schedules_list(course_id, callback)
    {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/admin/courses/ajax_get_schedules_data?course_id='+course_id);

        xhr.onload = function() {
            var $schedules_wrapper = $('.applications-schedules-wrapper');

            // Put the schedule IDs in an array.
            var schedules = JSON.parse(xhr.responseText);
            var schedule_ids = [];
            for (var i = 0; i < schedules.length; i++) {
                schedule_ids.push(schedules[i].id);
            }

            // Hide and disable irrelevant schedules from the schedule selector.
            var schedule_options = document.querySelectorAll('.applications-schedules-wrapper option');
            var schedule_dropdown_items = document.querySelectorAll('.applications-schedules-wrapper .multiselect-item-text');
            var show_schedule;
            var total = 0;
            for (i = 0; i < schedule_options.length; i++) {
                show_schedule = (schedule_ids.indexOf(schedule_options[i].value) > -1);
                schedule_options[i].disabled = !show_schedule;

                $(schedule_dropdown_items[i]).parents('li').toggleClass('hidden', !show_schedule);
                $(schedule_dropdown_items[i]).parents('li').find('[type="checkbox"]').prop('disabled', !show_schedule);

                total += show_schedule ? 1 : 0;
            }

            var schedules_selected = $schedules_wrapper.find('select').val();
            $schedules_wrapper.find('.multiselect-counters-total').html(total);
            $schedules_wrapper.find('.form-select-mask-count').html((schedules_selected && schedules_selected.length) ? schedules_selected.length : '');

            if (typeof callback == 'function') {
                callback();
            }
        };

        xhr.send();
    }

    // Update reports to suit the active filters
    function refresh_reports()
    {
        $.ajax({
            url: '/admin/applications/ajax_get_reports_data',
            data: {filters: get_filters()}
        }).done(function(data) {
            data = JSON.parse(data);

            var $reports = $('.application-report');

            for (var i = 0; i < data.length && i < $reports.length; i++) {
                $reports.eq(i).find('.application-report-amount').html(data[i].amount);
                $reports.eq(i).find('.application-report-text').text(data[i].text);
            }

            $('.application-report-period').text($('#applications-daterange').val());
        });

        initialize_table();
    }

    // Reload the DataTable, with the latest changes applied
    function refresh_datatable()
    {
        $('#applications-list-table').dataTable().fnDraw();
    }

    // Get data from all form fields that can filter the applications to display
    function get_filters()
    {
        return {
            stage        : stage,
            course_id    : $('#applications-course').val(),
            schedule_ids : $('#applications-schedules').val(),
            start_date   : $('#applications-daterange-start_date').val(),
            end_date     : $('#applications-daterange-end_date').val(),
            application_statuses : $('#applications-application_statuses').val(),
            interview_statuses   : $('#applications-interview_statuses').val(),
            offer_statuses       : $('#applications-offer_statuses').val()
        };
    }

    // Set up the DataTable
    function initialize_table()
    {
        var table_filters = {filters:get_filters()}; // Various fields throughout the page can filter content in the table
        var $table = $('#applications-list-table');

        $table.ib_serverSideTable(
            '/admin/applications/ajax_get_' + stage + 's_datatable?'+$.param(table_filters),
            {},
            {
                responsive: true,
                draw_callback: function() {
                    // After fetching the data, hide the "please wait" text, show either the table or the "no records" text.
                    $('#applications-loading-message').addClass('hidden');
                    var has_records = ($table.dataTable().fnGetData().length > 0);

                    // Hide the reports, if there are no results
                    $('#applications-reports').toggleClass('hidden', !has_records);

                    // Hide the DataTable if there are no results.
                    // However don't hide it if something has been typed in the searchbar. Otherwise, it could be impossible to make it reappear.
                    var show_table = Boolean(has_records || $('#applications-list-table_filter').find('input').val());

                    $('#applications-list').toggleClass('hidden', !show_table);
                    $('#applications-empty-message').toggleClass('hidden', show_table);
                }
            });
    }

    // Format a message and add it to the alert area
    $.fn.add_alert = function(message, type) {
        var $alert = $(
            '<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
            '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + htmlentities(message) +
            '</div>');
        $(this).append($alert);
    };
})();