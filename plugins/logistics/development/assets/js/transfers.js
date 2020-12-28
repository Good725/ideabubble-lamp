$(document).ready(function() {

    initialize_table();

    // When the date range is change, update the datatable to only show records in that range
    $('#transfers-date_range').on('change', initialize_table);

    // When the "add" or "edit" button is clicked
    // fetch the transfer data serverside and display it in the form in the modal box
    $(document).on('click', '.transfer-modal-toggle', function() {
        var id = $(this).data('id') || '';
        $.ajax('/admin/logistics/ajax_get_transfer/'+id).done(function(data) {

            // Split datetime into separate values
            var date = '';
            var date_formatted = '';
            var time = '';
            if (data.scheduled_date) {
                var scheduled_date = new Date(data.scheduled_date);
                date = scheduled_date.dateFormat('Y-m-d');
                date_formatted = scheduled_date.dateFormat('d/M/Y');
                time = scheduled_date.dateFormat('H:i');
            }

            // Populate the form
            $('#transfer-id').val(data.id);
            $('#transfer-title').val(data.title);
            $('#transfer-driver_id').val(data.driver_id);
            $('#transfer-driver').val(data.driver_name);
            $('#transfer-type').val(data.type);
            $('#transfer-pickup').val(data.pickup_id).change();
            $('#transfer-date').val(date);
            $('#transfer-date-input').val(date_formatted);
            $('#transfer-time').val(time);
            $('#transfer-dropoff').val(data.dropoff_id).change();
            $('#transfer-passenger_id').val(data.passenger_id);
            // Store the booking ID in a data attribute, because the option might not exist yet
            $('#transfer-booking').val(data.booking_id).data('selected', data.booking_id);
            $('#transfer-passenger').val(data.passenger_name).change();
            $('#transfer-note').val(data.note.note);
            $('#transfer-modal-add_another').prop('checked', false);

            // Toggle visibility, depending on whether this is add or edit mode
            var existing_item = !!data.id;
            $('.transfer-modal-add_only').toggleClass('hidden', existing_item);
            $('.transfer-modal-edit_only').toggleClass('hidden', !existing_item);

            // Open the modal
            $('#transfer-modal').modal();
        });
    });

    // Get autocomplete data for the contact-selection fields
    $('.transfer-contact_ac').autocomplete({
        source : '/admin/contacts3/ajax_get_all_contacts_ui',
        select : function (event, ui)
        {
            var $hidden_field = $('#' + $(this).attr('id') + '_id');
            $hidden_field.val(ui.item.id);
        }
    });

    // When the passenger is changed, only show bookings for that passenger
    $('#transfer-passenger').on('change', function() {
        var $booking       = $('#transfer-booking');
        var booking_id     = $booking.data('selected');
        var passenger_id   = $('#transfer-passenger_id').val();
        var default_option = '<option value="">-- Please select --</option>';

        if (passenger_id) {
            // Put all bookings for the passenger in the dropdown
            $.ajax('/admin/logistics/ajax_get_passenger_bookings/'+passenger_id)
                .done(function(data) {
                    var options = html_options_from_rows('booking_id', 'label', data.bookings, booking_id);

                    $booking.html(default_option + options).data('selected', '');
                });
        } else {
            // Put no bookings in the dropdown
            $booking.html(default_option).data('selected', '');
        }
    });

    // AJAX save the data
    $('#transfer-save').on('click', function() {
        var $form = $('#transfer-form');

        if ($form.validationEngine('validate')) {
            var data = $form.serialize();

            $.ajax({
                url: '/admin/logistics/ajax_save_transfer',
                method: 'post',
                data: data
            }).done(function(data) {
                // After saving, display message
                for (var i = 0; i < data.messages.length; i++) {
                    $('.alert_area').add_alert(
                        data.messages[i].message,
                        (data.messages[i].success ? 'success' : 'danger')+' popup_box');
                }

                // If successful, refresh the table
                if (data.success) {
                    initialize_table();

                    var $add_another = $('#transfer-modal-add_another');
                    if ($add_another.is(':visible')  && $add_another.prop('checked')) {
                        // Adding another => Reset the form
                        $('#transfer-add').click();
                    } else {
                        // Not adding another => Dismiss the modal
                        $('#transfer-modal').modal('hide');
                    }
                }
            });
        }
    });

    // Initialise or refresh the datatable
    function initialize_table() {
        var $table = $('#transfers-table');
        var filters = {
            'start_date': $('#transfers-date_range-start_date').val(),
            'end_date': $('#transfers-date_range-end_date').val()
        };

        $table.ib_serverSideTable(
            '/admin/logistics/ajax_get_transfers_datatable?' + $.param({filters: filters}),
            {},
            {
                responsive: true,
                draw_callback: function() {
                    // Hide the DataTable if there are no results.
                    var has_records = ($table.dataTable().fnGetData().length > 0);
                    $('#transfers-table-wrapper').toggleClass('hidden', !has_records);
                    $('#transfers-table-empty').toggleClass('hidden', has_records);
                }
            }
        );
    }
});