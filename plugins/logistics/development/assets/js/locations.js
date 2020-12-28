$(document).ready(function() {

    initialize_table();

    // When the "add" or "edit" button is clicked
    // fetch the transfer data serverside and display it in the form in the modal box
    $(document).on('click', '.location-modal-toggle', function() {
        var id = $(this).data('id') || '';
        $.ajax('/admin/logistics/ajax_get_location/'+id).done(function(data) {

            // Populate the form
            $('#location-modal-id').val(data.id);
            $('#location-modal-title').val(data.title);
            $('#location-modal-address_1').val(data.address_1);
            $('#location-modal-address_2').val(data.address_2);
            $('#location-modal-address_3').val(data.address_3);
            $('#location-modal-county').val(data.county_id);
            $('#location-modal-city').val(data.city_id);
            $('#location-modal-new_city').val('');
            $('#location-modal-longitude').val(data.longitude);
            $('#location-modal-latitude').val(data.latitude);
            $('#location-modal-note').val(data.note.note);
            $('#transfer-modal-add_another').prop('checked', false);

            initialize_map();

            // Toggle visibility, depending on whether this is add or edit mode
            var existing_item = !!data.id;
            $('.location-modal-add_only').toggleClass('hidden', existing_item);
            $('.location-modal-edit_only').toggleClass('hidden', !existing_item);

            // Open the modal
            $('#location-modal').modal();
        });
    });

    // AJAX save the data
    $('#location-modal-save').on('click', function() {
        var $form = $('#location-modal-form');

        if ($form.validationEngine('validate')) {
            $.ajax({
                url    : '/admin/logistics/ajax_save_location/',
                method : 'post',
                data   : $form.serialize()
            }).done(function(data) {
                // After saving, display message
                $('.alert_area').add_alert(data.message, (data.success ? 'success' : 'danger')+' popup_box');

                // If successful, refresh the table
                if (data.success) {
                    initialize_table();

                    // Dismiss the modal, unless the "Add another" checkbox is checked
                    var $add_another = $('#location-modal-add_another');
                    if ($add_another.is(':visible') && $add_another.prop('checked')) {
                        // Adding another => Reset the form
                        $('#location-add').trigger('click');
                    } else {
                        // Not adding another => Dismiss the modal
                        $('#location-modal').modal('hide');
                    }

                    // If a new city was created, update the dropdown to include it
                    if (data.cities) {
                        update_cities_dropdown(data.cities);
                    }

                }
            });
        }
    });

    // Deleting a location
    $(document).on('click', '.location-delete-modal-toggle', function() {
        $('#location-delete-btn').data('id', $(this).data('id'));
        $('#location-delete-modal').modal();
    });

    $(document).on('click', '#location-delete-btn', function() {
        var data = {id: $(this).data('id')};
        $.ajax({url: '/admin/locations/ajax_delete', data: {data: JSON.stringify(data)} , type: 'POST'}).done(function(data) {
            var success = JSON.parse(data);

            if (success) {
                $('.alert_area').add_alert('Location has been deleted', 'success popup_box');
                initialize_table();
                $('#location-delete-modal').modal('hide');
            } else {
                $('.alert_area').add_alert('Error deleting location', 'danger popup_box');
            }
        });
    });

    // Initialise or refresh the datatable
    function initialize_table()
    {
        var $table = $('#locations-table');

        $table.ib_serverSideTable(
            '/admin/logistics/ajax_get_locations_datatable',
            {},
            {responsive: true}
        );
    }
    function initialize_map() {
        var $latitude = $('#location-modal-latitude');
        var $longitude = $('#location-modal-longitude');

        set_up_google_map_form({
            container: document.getElementById('location-modal-map_summary'),
            lat: $latitude.val()  || 53.32693558541906,
            lng: $longitude.val() || -6.416015625,
            lat_field: $latitude,
            lng_field: $longitude,
            coordinates_field: $('#location-modal-coordinates'),
            targetX: $latitude[0],
            targetY: $longitude[0],
            search_field: document.getElementById('location-modal-map_search'),
            find_btn: $('#location-modal-find_location'),
            get_address_function: get_address
        });
    }

    function get_address()
    {
        var city = $('#location-modal-new_city').val().trim();

        if (!city) {
            var city_id = $('#location-modal-city').val();
            city = city_id ? $('#location-modal-city').find(':selected').text() : '';
        }

        var address = $('#location-modal-address_1').val()+' '+$('#location-modal-address_2').val()+' '+
            $('#location-modal-address_3').val()+' '+ city + ' ' + $('#location-modal-county').val()+ ' '+
            $('#location-modal-country').find(':selected').text();

        return address.trim();
    }

    function update_cities_dropdown(cities)
    {
        var html = '<option>-- Please select</option>';
        for (var city in cities) {
            html += '<option value="'+cities[city]+'">'+city+'</option>';
        }
        $('#location-modal-city').html(html);
    }
});