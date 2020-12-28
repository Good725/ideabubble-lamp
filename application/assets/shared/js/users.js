function setup_users_list_datatable()
{
    // Server-side datatable
    var $table = $('#users_table');
    var role_id = $table.data("role_id");

        $table.ready(function() {
            var ajax_source = '/admin/usermanagement/users_datatable' + (role_id ? '?role_id=' + role_id : '');
            var settings = {
                "aLengthMenu"     : [10, 25, 50, 100],
                "aaSorting"       : [[ 3, "desc" ]],
                "sPaginationType" : "bootstrap",
                "aoColumnDefs"    : [{
                    "aTargets": [1],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                    {
                        // Add data attribute, with the contact ID to each row
                        $(nTd).parent().attr({'data-id': oData[0]});
                    }
                }]
            };
            $table.ib_serverSideTable(ajax_source, settings);
    });

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });

}


$(document).on('ready', function(){
    setup_users_list_datatable();
});