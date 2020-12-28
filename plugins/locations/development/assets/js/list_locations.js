//
// INITIAL
//

$(document).ready(function() {
    build_table();
});

var objects = [];

/**
 * Build the table.
 */
function build_table() {
    var c = {};

    c.table_id        = 'locations_table';
    c.columns         = ['id', 'type', 'title', 'county', 'email', 'phone', 'edit', 'publish', 'delete'];
    c.data_source     = '/admin/locations/ajax_get_all/';
    c.edit_class      = 'edit';
    c.edit_url        = '/admin/locations/edit/';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/locations/ajax_delete/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/locations/ajax_toggle_publish_option/';
    c.f_on_success    = 'build_table';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected location.';
    c.objects_array   = 'objects';

    DYNAMIC_TABLE.build(c);
}
