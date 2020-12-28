$(document).ready(function () {
    var table = $('#autotimetables_table');
    initTable('#autotimetables_table');

    $(document).on("click", '.publish', function(ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_autotimetable', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Timetable successfully unpublished.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Timetable successfully published.</div>';
                    $("#main").prepend(smg);
                }
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }

        }, "json");
    });

    $(document).on("click", '.delete', function(ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });

    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_autotimetable', {id: id}, function (data) {
            var smg = '';
            if (data.message === 'success') {
                initTable('#autotimetables_table');
                smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Timetable successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });

    table.find('.search_init').on('change', function ()
    {
        table.dataTable().fnFilter(this.value, table.find('tr .search_init').index(this) );
    });

    function initTable(id)
    {
        var filter_arr = ["search_id", "search_name", "search_category", "search_location", "search_date_start", "search_date_end", "", "", ""];
        var ajax_source = "/admin/courses/ajax_get_autotimetables";
        var settings = {
            "aoColumns": [
                {"mDataProp": "id", "bSearchable": true, "bSortable": true},
                {"mDataProp": "name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "location", "bSearchable": true, "bSortable": true},
                {"mDataProp": "date_start", "bSearchable": true, "bSortable": true},
                {"mDataProp": "date_end", "bSearchable": true, "bSortable": true},
                {"mDataProp": "edit", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": true},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false},
            ],
            "bServerSide": true,
            "bProcessing": false,
            "sDom": '<"top"if<"clear">>rt<"bottom"ilp<"clear">>',
            "bLengthChange": false,
            "bDestroy": true,
            "fnInitComplete": function(oSettings, json) {
                var cols = oSettings.aoPreSearchCols;
                for (var i = 0; i < cols.length; i++) {
                    var value = cols[i].sSearch;
                    if (value.length > 0) {
                        $("#"+filter_arr[i]).val(value);
                    }
                }
            },
            "sAjaxSource": "/admin/courses/ajax_get_autotimetables",
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