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
        $('#format_title').val(o.title);

        $('#add_format_actions' ).hide();
        $('#edit_format_actions').show();
    });

    $("#form_add_edit_format").validate(
        {
            submitHandler: function(f) {
                $(f).hasClass('update-mode') ? update_format() : add_format();
            }
        });

	$.ajax('/admin/products/ajax_postage_get_all_countries/').done(function(result)
	{
		country_objects = JSON.parse(result);
		build_formats_table();
	});

    //
    // ZONES
    //

    $('#cancel_zone_update').click(function() {
        var f = $('#form_add_edit_zone');

        $('#edit_zone_actions').hide();
        $('#add_zone_actions' ).show();

        f.data('validator').resetForm();
        f.removeClass('update-mode');
    });

    $(document).on('click', '.edit-zone', function() {
        var f = $('#form_add_edit_zone');
        var o = zone_objects[$(this).data('object_id')];

        f.data('validator').resetForm();
        f.addClass('update-mode');

        $('#update_zone').data('object_id', $(this).data('object_id'));
        $('#zone_title').val(o.title);

        $('#add_zone_actions' ).hide();
        $('#edit_zone_actions').show();
    });

    $("#form_add_edit_zone").validate(
        {
            submitHandler: function(f) {
                $(f).hasClass('update-mode') ? update_zone() : add_zone();
            }
        });

    build_zones_table();

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

        $('#format_id'  ).val(o.format_id  );
        $('#zone_id'    ).val(o.zone_id    );
        $('#weight_from').val(o.weight_from);
        $('#weight_to'  ).val(o.weight_to  );
        $('#price'      ).val(o.price      );

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

var country_objects = [];
var format_objects  = [];
var zone_objects    = [];
var rate_objects    = [];

/**
 * Update a drop-down list using an array of objects.
 * @param {Array} objects_array The objects array.
 * @param {string} field_id The name of the field for the value attribute of the option.
 * @param {string} field_value The name of the field for the text of option.
 * @param {string} ddl_id The drop-down list identifier.
 * @param {string} empty_option The empty option. This is, the one with no value.
 */
function update_ddl(objects_array, field_id, field_value, ddl_id, empty_option, all_option)
{
    all_option = typeof all_option !== 'undefined' ? all_option : false;
    var ddl = $('#' + ddl_id).get();

    $(ddl).empty();

    if (empty_option != null)
    {
        $(ddl).prepend('<option value="" selected="selected">' + empty_option + '</option>');
    }

    if (all_option)
    {
        $(ddl).append($('<option value="0">All</option>'));
    }

    $.each(objects_array, function(i, v)
    {
		if (typeof v.publish == 'undefined' || v.publish == 1)
		{
			$(ddl).append($('<option></option>').val(v[field_id]).html(v[field_value]));
		}
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
    c.columns         = ['id', 'title', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_postage_get_all_formats/';
    c.edit_class      = 'edit-format';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_postage_delete_format/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_postage_toggle_format_publish_option/';
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
    AJAX.make_post_request('/admin/products/ajax_postage_add_format/', { title: $('#format_title').val() }, clean_after_add_format, null);
}

/**
 * Update an existing format.
 */
function update_format() {
    var o = format_objects[$('#update_format').data('object_id')];

    AJAX.make_post_request('/admin/products/ajax_postage_update_format/', { id: o.id , title: $('#format_title').val() }, clean_after_update_format, null);
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
// ZONES
//

/**
 * Build the zones table.
 */
function build_zones_table() {
    var c = {};

    c.table_id        = 'zones_table';
    c.columns         = ['id', 'title', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_postage_get_all_zones/?published_only=1';
    c.edit_class      = 'edit-zone';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_postage_delete_zone/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_postage_toggle_zone_publish_option/';
    c.f_on_success    = 'build_zones_table';
    c.f_on_completion = 'update_rate_section';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected zone.';
    c.objects_array   = 'zone_objects';

    DYNAMIC_TABLE.build(c);
}

/**
 * Add a new zone.
 */
function add_zone() {
    AJAX.make_post_request('/admin/products/ajax_postage_add_zone/', { title: $('#zone_title').val() }, clean_after_add_zone, null);
}

/**
 * Update an existing zone.
 */
function update_zone() {
    var o = zone_objects[$('#update_zone').data('object_id')];

    AJAX.make_post_request('/admin/products/ajax_postage_update_zone/', { id: o.id , title: $('#zone_title').val() }, clean_after_update_zone, null);
}

/**
 * Clean fields and build the zones table after a successful adding operation.
 */
function clean_after_add_zone() {
    $('#form_add_edit_zone').data('validator').resetForm();

    build_zones_table();
}

/**
 * Clean fields and build the zones table after a successful editing operation.
 */
function clean_after_update_zone() {
    var f = $('#form_add_edit_zone');

    f.data('validator').resetForm();
    f.removeClass('update-mode');

    $('#edit_zone_actions').hide();
    $('#add_zone_actions' ).show();

    build_zones_table();
}

//
// RATES
//

/**
 * Update the rate section.
 */
function update_rate_section() {
	update_ddl(country_objects, 'id', 'title', 'country_id', '-- Select Country --', true);
	update_ddl(zone_objects   , 'id', 'title', 'zone_id'   , '-- Select Zone --');
    update_ddl(format_objects , 'id', 'title', 'format_id' , '-- Select Format --', true);

    build_rates_table();
}

/**
 * Build the rates table.
 */
function build_rates_table() {
    var c = {};

    c.table_id        = 'rates_table';
    c.columns         = ['id', 'country', 'format', 'zone', 'weight_from', 'weight_to', 'price', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_postage_get_all_rates/';
    c.edit_class      = 'edit-rate';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_postage_delete_rate/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_postage_toggle_rate_publish_option/';
    c.f_on_success    = 'build_rates_table';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected rate.';
    c.objects_array   = 'rate_objects';

    DYNAMIC_TABLE.build(c);
}

/**
 * Add a new rate.
 */
function add_rate() {
    AJAX.make_post_request('/admin/products/ajax_postage_add_rate/',
        {
			country_id  : $('#country_id' ).val(),
			format_id   : $('#format_id'  ).val(),
            zone_id     : $('#zone_id'    ).val(),
            weight_from : $('#weight_from').val(),
            weight_to   : $('#weight_to'  ).val(),
            price       : $('#price'      ).val()
        }, clean_after_add_rate, null);
}

/**
 * Update an existing rate.
 */
function update_rate() {
    var o = rate_objects[$('#update_rate').data('object_id')];

    AJAX.make_post_request('/admin/products/ajax_postage_update_rate/',
        {
            id          : o.id,
			country_id  : $('#country_id' ).val(),
			format_id   : $('#format_id'  ).val(),
            zone_id     : $('#zone_id'    ).val(),
            weight_from : $('#weight_from').val(),
            weight_to   : $('#weight_to'  ).val(),
            price       : $('#price'      ).val()
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
