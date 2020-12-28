$(document).ready(function() {
    $('.btn-group button').click(function() {
        var name = $(this).parent('').data('toggle-name');

        $('#' + name).attr('value', $(this).val());
    });

    $('body')
        .on('click', '.edit', function() {
            var c = DYNAMIC_TABLE.get_configuration($(this).parents('table').attr('id'));
            var o = window[$(this).data('objects_array')][$(this).data('object_id')];

            window.location = c.edit_url + '?id=' + o.id;
        })
        .on('click', '.publish', function() {
            var c = DYNAMIC_TABLE.get_configuration($(this).parents('table').attr('id'));
            var o = window[$(this).data('objects_array')][$(this).data('object_id')];

            execute_action(c.publish_url, o.id, window[c.f_on_success], window[c.f_on_error]);
        })
        .on('click', '.delete', function() {
            var c = DYNAMIC_TABLE.get_configuration($(this).parents('table').attr('id'));

            window['item_selected'] = $(this);

            $('#warning_message').html('<p>' + c.warning_message + '</p>');
            $('#confirm_delete').modal();
        });

    $("#btn_delete").click(function() {
        var c = DYNAMIC_TABLE.get_configuration(window['item_selected'].parents('table').attr('id'));
        var o = window[c.objects_array][window['item_selected'].data('object_id')];

        $('#confirm_delete').modal('hide');
        $(".alert").remove();

        execute_action(c.delete_url, o.id, window[c.f_on_success], window[c.f_on_error]);
    });
});

/**
 * Execute a POST request.
 * @param {string} url The URL.
 * @param {string} id The object identifier.
 * @param {function} f_on_success The callback function to be called on success.
 * @param {function} f_on_error The callback function to be called on error.
 */
function execute_action(url, id, f_on_success, f_on_error) {
    AJAX.make_post_request(url, { id: id }, f_on_success, f_on_error);
}

//
// DYNAMIC TABLE
//

/*
    EXAMPLE OF USE

    var c = {};

    // Set the configuration
    c.table_id
    c.columns
    c.filters         (optional)
    c.data_source
    c.edit_class
    c.edit_url        (optional if c.edit_class is not 'edit')
    c.delete_class
    c.delete_url      (optional if c.delete_class is not 'delete')
    c.publish_class
    c.publish_url     (optional if c.publish_class is not 'publish')
    c.f_on_success    (optional)
    c.f_on_error      (optional)
    c.f_on_completion (optional)
    c.warning_message
    c.objects_array

    // Build the table
    DYNAMIC_TABLE.build(c);
*/

var DYNAMIC_TABLE = DYNAMIC_TABLE || {};

// Used to store the configuration of all the tables created using this object.
DYNAMIC_TABLE.tables_configuration = {};

/**
 * Get the configuration of the specified table.
 * @param {string} table_name The table name.
 * @returns {object} The table configuration.
 */
DYNAMIC_TABLE.get_configuration = function(table_name) {
    return DYNAMIC_TABLE.tables_configuration[table_name];
};

/**
 * Build a row using the specified data.
 * @param {object} data The data.
 * @param {object} c The table configuration.
 * @param {int} object_id The object identifier.
 * @returns {Array} An array with the value of each column.
 */
DYNAMIC_TABLE._build_row = function(data, c, object_id) {
    var row = [];

    for (var i = 0; i < c.columns.length; i++) {
        var span_class;
        var span_value;

        switch (c.columns[i]) {
            case 'edit':
                span_class = c.edit_class;
                span_value = '<i class="icon-pencil"></i>';
                break;

            case 'publish':
                span_class = c.publish_class;
                span_value = ((data.publish == '1') ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>');
                break;

            case 'delete':
                span_class = c.delete_class;
                span_value = '<i class="icon-remove"></i>';
                break;

            default:
                span_class = c.edit_class;
                span_value = (c.filters != null && c.columns[i] in c.filters) ?  window[c.filters[c.columns[i]]](data) : data[c.columns[i]];
                break;
        }

        row[i] = '<span class="' + span_class + '" data-object_id="' + object_id + '" data-objects_array="' + c.objects_array + '">' + span_value + '</td>';
    }

    return row;
};

/**
 * Build a table using data encoded in JSON.
 * @param {string} json The data.
 * @param {object} c The table configuration.
 */
DYNAMIC_TABLE._build_table = function(json, c) {
	if (json == '') json = 'null';
    var data = jQuery.parseJSON(json);

    if (data != null) {
        var data_table = $('#' + c.table_id).dataTable();
        var rows       = [];

        // Set the objects array
        window[c.objects_array] = data;

        // Store the table configuration
        DYNAMIC_TABLE.tables_configuration[c.table_id] = c;

        // Generate the rows
        for (var i = 0; i < data.length; i++) {
            rows[i] = DYNAMIC_TABLE._build_row(data[i], c, i);
        }

        // Clear the table and add the rows
        data_table.fnClearTable();
        data_table.fnAddData(rows);

        // Call, if any, the function to be called on completion
        (c.f_on_completion != null) && window[c.f_on_completion]();
    }
};

/**
 * Build a table using external data.
 * @param {object} c The table configuration.
 */
DYNAMIC_TABLE.build = function(c) {
    c.filters         = (typeof c.filters         === 'undefined') ? null : c.filters;
    c.f_on_success    = (typeof c.f_on_success    === 'undefined') ? null : c.f_on_success;
    c.f_on_error      = (typeof c.f_on_error      === 'undefined') ? null : c.f_on_error;
    c.f_on_completion = (typeof c.f_on_completion === 'undefined') ? null : c.f_on_completion;

    AJAX.make_get_request(c.data_source, DYNAMIC_TABLE._build_table, c);
};

//
// AJAX OPERATIONS
//

var AJAX = AJAX || {};

/**
 * Make an AJAX GET Request. Once the request is completed, a callback function will be called with the response.
 * @param {string} url The URL of the action to be performed.
 * @param {function} f The callback function.
 * * @param {object} a An object with te parameters to be passed to the callback function.
 */
AJAX.make_get_request = function(url, f, a) {
    $.get(url)
        .done(function(r) {
            f(r, a);
        })
        .fail(function() {
            Alert.display_alert(Alert.E_ERROR, 'Cannot connect with the server.');
        });
};

/**
 * Make an AJAX POST Request. Once the request is completed, an alert will be displayed and a callback function will be called depending on the response.
 * @param {string} url The URL of the action to be performed.
 * @param {object} data The data to be attached to the POST request.
 * @param {function} f_on_success The callback function to be called on success. If this argument is null, no function will be called.
 * @param {function} f_on_error The callback function to be called on error. If this argument is null, no function will be called.
 */
AJAX.make_post_request = function(url, data, f_on_success, f_on_error) {
    $.post(url, { data: JSON.stringify(data) })
        .done(function(r) {
            switch (r) {
                // Error
                case '0':
                    Alert.display_alert(Alert.E_INFO   , 'Unable to complete the requested operation. Please, review the fields.');

                    (f_on_error   != null) && f_on_error  ();
                    break;

                // Success
                case '1':
                    Alert.display_alert(Alert.E_SUCCESS, 'Operation successfully completed.');

                    (f_on_success != null) && f_on_success();
                    break;

                default:
                    break;
            }
        })
        .fail(function() {
            Alert.display_alert(Alert.E_ERROR, 'Cannot connect with the server.');
        });
};

//
// ALERT
//

var Alert = Alert || {};

Alert.E_SUCCESS = 0;
Alert.E_INFO    = 1;
Alert.E_ERROR   = 2;

/**
 * Display an alert.
 * @param {int} severity_level One of the following values: E_SUCCESS or E_ERROR.
 * @param {string} msg The message to be displayed.
 */
Alert.display_alert = function (severity_level, msg) {
    var div_class = '';

    // Remove all the previous alerts to avoid stacking
    $(".alert").remove();

    switch (severity_level) {
        case Alert.E_SUCCESS:
            div_class = 'alert-success';
            msg       = '<strong>Success: </strong>' + msg;
            break;

        case Alert.E_INFO:
            div_class = 'alert-info';
            msg       = '<strong>Attention: </strong>' + msg;
            break;

        case Alert.E_ERROR:
            div_class = 'alert-error';
            msg       = '<strong>Warning: </strong>' + msg;
            break;

        default:
            break;
    }

    if (div_class != '')
        $("#main").prepend('<div class="alert ' + div_class + '"><a class="close" data-dismiss="alert">×</a>' + msg + '</div>');
};
