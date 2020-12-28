$(document).ready(function () {
    // Server-side datatable
    var $table = $('#list_organisations_table');

    $table.ib_serverSideTable(
        '/admin/contacts3/ajax_get_organisation_datatable',
        {aaSorting: [[11, 'desc']]},
        {responsive: true, row_data_ids: true}
    );

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this));
    });
});