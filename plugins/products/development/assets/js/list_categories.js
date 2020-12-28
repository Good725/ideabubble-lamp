//
// INITIAL
//

$(document).ready(function() {
    build_table();
});

var category_objects = [];

/**
 * Build the categories table.
 */
function build_table() {
    var c = {};

    c.table_id        = 'categories_table';
    c.columns         = ['image_html', 'id', 'category', 'order', 'edit', 'publish', 'delete'];
    c.filters         = { 'category' : 'filter_category_column' };
    c.data_source     = '/admin/products/ajax_get_all_categories/';
    c.edit_class      = 'edit';
    c.edit_url        = '/admin/products/edit_category/';
    c.delete_class    = 'delete';
    c.delete_url      = '/admin/products/ajax_delete_category/';
    c.publish_class   = 'publish';
    c.publish_url     = '/admin/products/ajax_toggle_category_publish_option/';
    c.f_on_success    = 'build_table';
    c.warning_message = 'This action is <strong>irreversible</strong>! Please confirm you want to delete the selected category and its subcategories.';
    c.objects_array   = 'category_objects';

    DYNAMIC_TABLE.build(c);
}

/**
 * Filter the value for the category column.
 * @param data The current data for this row.
 * @returns {string} The value of the cell.
 */
function filter_category_column(data) {
    var v = '';

    for (var i = 0; i < data['depth']; i++) {
        v += '<i class="icon-arrow-right"></i>';
    }

    return v + data['category'];
}
