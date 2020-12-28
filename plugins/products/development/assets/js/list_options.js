//
// INITIAL
//

$(document).ready(function() {
    build_table();
});

var option_objects = [];

/**
 * Build the options table.
 */
function build_table() {
    var c = {};

    c.table_id        = 'options_table';
    c.columns         = ['group', 'label', 'value', 'default', 'price', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/products/ajax_get_all_options/';
    c.edit_class      = 'edit';
    c.edit_url        = '/admin/products/edit_option/';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_delete_option/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_toggle_option_publish_option/';
    c.f_on_success    = 'build_table';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected option.';
    c.objects_array   = 'option_objects';

    DYNAMIC_TABLE.build(c);
}
