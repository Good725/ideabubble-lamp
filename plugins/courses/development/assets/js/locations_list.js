$(document).ready(function () {
     initTable('#location_table');

    $(document).on("click", ".delete", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_location', {id: id}, function (data) {
            if (data.message === 'success') {
                initTable('#location_table');
                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Location is successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });


    function initTable(id) {
        var ajax_source = "/admin/courses/ajax_get_locations";
        var settings = {
            "aoColumns": [
                {"mDataProp": "parent", "bSearchable": true, "bSortable": true},
                {"mDataProp": "name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "city", "bSearchable": true, "bSortable": true},
                {"mDataProp": "county", "bSearchable": true, "bSortable": true},
                {"mDataProp": "edit", "bSearchable": false, "bSortable": false},
                {"mDataProp": "remove", "bSearchable": false, "bSortable": false}
            ],
            "bDestroy": true,
            "bStateSave" : true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                });
            }
        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }


});