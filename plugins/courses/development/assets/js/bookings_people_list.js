
$(document).ready(function () {
    initTable('#booking_people_table');

    function initTable(id) {
        var cid = parseInt($("#schedule_data").data("id"));
        var ajax_source = "/admin/courses/ajax_get_bookings_people/?id="+cid;
        var settings = {
            "aoColumns": [
                {"mDataProp": "first_name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "last_name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "email", "bSearchable": true, "bSortable": true},
                {"mDataProp": "phone", "bSearchable": true, "bSortable": true},
                {"mDataProp": "comments", "bSearchable": true, "bSortable": true},
                {"mDataProp": "school", "bSearchable": true, "bSortable": true},
                {"mDataProp": "school_address", "bSearchable": true, "bSortable": true},
                {"mDataProp": "roll_no", "bSearchable": true, "bSortable": true},
                {"mDataProp": "school_phone", "bSearchable": true, "bSortable": true},
                {"mDataProp": "county", "bSearchable": true, "bSortable": true},
                {"mDataProp": "paid", "bSearchable": true, bSortable: true}
            ],
            "bDestroy": true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                });
            },
            "fnCreatedRow" : function(row, data)
            {
                $('td:eq(10)', row).html( '<span class="hidden">'+data.paid+'</span><i class="icon-'+((data.paid == 1) ? 'ok' : 'remove')+'"></i>' );
            }

        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }


});