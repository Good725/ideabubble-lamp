//
// INITIAL
//

$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    //
    // FORMATS
    //

    $('#cancel_format_update').click(function() {
        var f = $('#form_add_edit_format');

        $("#role option:selected").removeAttr("selected");
        $('#edit_format_actions').hide();
        $('#add_format_actions' ).show();

        f.data('validator').resetForm();
        f.removeClass('update-mode');
    });

    $(document).on('click', '.edit-format', function() {
        var f = $('#form_add_edit_format');
        var o = format_objects[$(this).data('object_id')];

        f.data('validator').resetForm();
        f.addClass('update-mode');

        $('#update_format').data('object_id', $(this).data('object_id'));

        $('#format_title'       ).val(o.title       );
        $('#format_description' ).val(o.description );
        $('#format_type'        ).val(o.type_id     );
        $('#code'               ).val(o.code        );
        $('#datetimepicker_from').val(o.date_available_from);
        $('#datetimepicker_to'  ).val(o.date_available_till);

        $('#role').prop('selectedIndex',-1);

        for(var key in o.role){
            $("#role").find("[value=" + o.role[key].id + ']').prop("selected", true);
        }

        $("[name='has_category[]']").prop("checked", false);
        $("[name='has_category[]']").each (function(){
            if (o.has_category.indexOf(this.value) != -1){
                this.checked = true;
            }
        });

        $('#add_format_actions' ).hide();
        $('#edit_format_actions').show();
    });

    $("#form_add_edit_format").validate(
        {
            submitHandler: function(f) {
                $(f).hasClass('update-mode') ? update_format() : add_format();
            }
        });

    build_formats_table();

    //
    // RATES
    //

    $('#cancel_rate_update').click(function() {
        var f = $('#form_add_edit_rate');

        $('#edit_rate_actions').hide();
        $('#add_rate_actions' ).show();

        f.data('validator').resetForm();
        f.removeClass('update-mode');
    });

    $(document).on('click', '.edit-rate', function() {
		var f = $('#form_add_edit_rate');
        var o = rate_objects[$(this).data('object_id')];

        f.data('validator').resetForm();
        f.addClass('update-mode');

        $('#update_rate').data('object_id', $(this).data('object_id'));

        $('#format_id'    ).val(o.format_id    );
        $('#range_from'   ).val(o.range_from   );
        $('#range_to'     ).val(o.range_to     );
        $('#discount_rate').val(o.discount_rate);
        $('#discount_rate_percentage').val(o.discount_rate_percentage);

        $('#add_rate_actions' ).hide();
        $('#edit_rate_actions').show();
    });

    $("#form_add_edit_rate").validate(
        {
            submitHandler: function(f) {
                $(f).hasClass('update-mode') ? update_rate() : add_rate();
            }
        });

    build_rates_table();
});

//
// COMMON
//

var format_objects = [];
var rate_objects   = [];

/**
 * Update a drop-down list using an array of objects.
 * @param {Array} objects_array The objects array.
 * @param {string} field_id The name of the field for the value attribute of the option.
 * @param {string} field_value The name of the field for the text of option.
 * @param {string} ddl_id The drop-down list identifier.
 * @param {string} empty_option The empty option. This is, the one with no value.
 */
function update_ddl(objects_array, field_id, field_value, ddl_id, empty_option) {
    var ddl = $('#' + ddl_id).get();

    $(ddl).empty();

    if (empty_option != null) {
        $(ddl).prepend('<option value="" selected="selected">' + empty_option + '</option>');
    }

    $.each(objects_array, function(i, v) {
        $(ddl).append($('<option></option>').val(v[field_id]).html(v[field_value]));
    });
}

//
// FORMATS
//

/**
 * Build the formats table.
 */
function build_formats_table() {
    var c = {};

    c.table_id        = 'formats_table';
    c.columns         = ['id', 'title', 'description', 'type', 'code', 'role', 'date_available_from', 'date_available_till',  'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_discount_get_all_formats/';
    c.edit_class      = 'edit-format';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_discount_delete_format/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_discount_toggle_format_publish_option/';
    c.f_on_success    = 'build_formats_table';
    c.f_on_completion = 'update_rate_section';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected format.';
    c.objects_array   = 'format_objects';
	DYNAMIC_TABLE.build(c);
}

/**
 * Add a new format.
 */
function add_format() {
    var has_category = [];
    $("[name='has_category[]']:checked").each(function(){
        has_category.push(this.value);
    });
    AJAX.make_post_request('/admin/products/ajax_discount_add_format/',
        {
            title               : $('#format_title'         ).val(),
            description         : $('#format_description'   ).val(),
            type_id             : $('#format_type'          ).val(),
            code                : $('#code'                 ).val(),
            role                : $('#role'                 ).val(),
            date_available_from : $('#datetimepicker_from'  ).val(),
            date_available_till : $('#datetimepicker_to'    ).val(),
            has_category: has_category
        }, clean_after_add_format, null);
}

/**
 * Update an existing format.
 */
function update_format() {
    var o = format_objects[$('#update_format').data('object_id')];
    var has_category = [];
    $("[name='has_category[]']:checked").each(function(){
        has_category.push(this.value);
    });

    AJAX.make_post_request('/admin/products/ajax_discount_update_format/',
        {
            id                  : o.id,
            title               : $('#format_title'       ).val(),
            description         : $('#format_description' ).val(),
            type_id             : $('#format_type'        ).val(),
            code                : $('#code'               ).val(),
            role                : $('#role'               ).val(),
            date_available_from : $('#datetimepicker_from').val(),
            date_available_till : $('#datetimepicker_to'  ).val(),
            has_category: has_category
        }, clean_after_update_format, null);
}

/**
 * Clean fields and build the formats table after a successful adding operation.
 */
function clean_after_add_format() {
    $('#form_add_edit_format').data('validator').resetForm();

    build_formats_table();
}

/**
 * Clean fields and build the formats table after a successful editing operation.
 */
function clean_after_update_format() {
    var f = $('#form_add_edit_format');

    f.data('validator').resetForm();
    f.removeClass('update-mode');

    $('#edit_format_actions').hide();
    $('#add_format_actions' ).show();

    build_formats_table();
}

//
// RATES
//

/**
 * Update the rate section.
 */
function update_rate_section() {
    update_ddl(format_objects, 'id', 'title', 'format_id', '-- Select Format --');

    build_rates_table();
}

/**
 * Build the rates table.
 */
function build_rates_table() {
    var c = {};

    c.table_id        = 'rates_table';
    c.columns         = ['id', 'title', 'type', 'range_from', 'range_to', 'discount_rate', 'discount_rate_percentage', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_discount_get_all_rates/';
    c.edit_class      = 'edit-rate';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_discount_delete_rate/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_discount_toggle_rate_publish_option/';
    c.f_on_success    = 'build_rates_table';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected rate.';
    c.objects_array   = 'rate_objects';

    DYNAMIC_TABLE.build(c);
}

/**
 * Add a new rate.
 */
function add_rate() {
    AJAX.make_post_request('/admin/products/ajax_discount_add_rate/',
        {
            format_id     : $('#format_id'    ).val(),
            range_from    : $('#range_from'   ).val(),
            range_to      : $('#range_to'     ).val(),
            discount_rate : $('#discount_rate').val(),
            discount_rate_percentage : $('#discount_rate_percentage').val()
        }, clean_after_add_rate, null);
}

/**
 * Update an existing rate.
 */
function update_rate() {
    var o = rate_objects[$('#update_rate').data('object_id')];

    AJAX.make_post_request('/admin/products/ajax_discount_update_rate/',
        {
            id            : o.id,
            format_id     : $('#format_id'    ).val(),
            range_from    : $('#range_from'   ).val(),
            range_to      : $('#range_to'     ).val(),
            discount_rate : $('#discount_rate').val(),
            discount_rate_percentage : $('#discount_rate_percentage').val()
        }, clean_after_update_rate, null);
}

/**
 * Clean fields and build the rate table after a successful adding operation.
 */
function clean_after_add_rate() {
    $('#form_add_edit_rate').data('validator').resetForm();

    build_rates_table();
}

/**
 * Clean fields and build the rate table after a successful editing operation.
 */
function clean_after_update_rate() {
    var f = $('#form_add_edit_rate');

    f.data('validator').resetForm();
    f.removeClass('update-mode');

    $('#edit_rate_actions').hide();
    $('#add_rate_actions' ).show();

    build_rates_table();
}
