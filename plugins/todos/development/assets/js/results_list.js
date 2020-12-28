$(document).ready(function () {
    initTable('#results_table');

    function initTable(id) {
        var searchParams = new URLSearchParams(window.location.search);
        var ajax_source = "/admin/todos/results_datatable";
        var columns = [
            {"mDataProp": "title", "bSearchable": true, "bSortable": true},
            {"mDataProp": "type", "bSearchable": false, "bSortable": true},
            {"mDataProp": "datetime", "bSearchable": false, "bSortable": true},
            {"mDataProp": "student", "bSearchable": false, "bSortable": true},
            {"mDataProp": "result", "bSearchable": false, "bSortable": true},
            {"mDataProp": "grade", "bSearchable": false, "bSortable": true},
            {"mDataProp": "comment", "bSearchable": false, "bSortable": true}];

        var settings = {
            "aoColumns": columns,
            "aaSorting": [[2, "desc"]],
            "bDestroy": true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                aoData.push({
                    "name": "my",
                    "value": (searchParams.get('my') == '1') ? true : false
                });
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
