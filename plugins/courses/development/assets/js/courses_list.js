$(document).ready(function () {
    var table = $('#course_table');
    initTable('#course_table');
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_course', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Course is successfully unpublished.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Course is successfully published.</div>';
                    $("#main").prepend(smg);
                }
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }

        }, "json");
    });

    $(document).on("click", ".delete", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_course', {id: id}, function (data) {
            if (data.message === 'success') {
                initTable('#course_table');
                $('.alert_area').add_alert('Course is successfully removed', 'success popup_box');
            } else {
                $('.alert_area').add_alert(data.error_msg,'error popup_box');
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });

    // Search by individual columns
    table.find('.search_init').on('change', function ()
    {
        table.dataTable().fnFilter(this.value, table.find('tr .search_init').index(this) );
    });

   function initTable(id) {
        var filter_arr = ["search_title", "search_code", "search_year", "search_subject", "search_type", "search_provider", "search_topics", "", "", ""];
        var ajax_source = "/admin/courses/ajax_get_courses";
        var settings = {
            "sDom": '<"top"if<"clear">>rt<"bottom"ilp<"clear">>',
            "bLengthChange": false,
            "aoColumns": [
                {"mDataProp": "title", "bSearchable": true, "bSortable": true},
                {"mDataProp": "code", "bSearchable": true, "bSortable": true},
                {"mDataProp": "year", "bSearchable": true, "bSortable": true},
                {"mDataProp": "level", "bSearchable": true, "bSortable": true},
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "subject", "bSearchable": true, "bSortable": true},
                {"mDataProp": "type", "bSearchable": true, "bSortable": true},
                {"mDataProp": "provider", "bSearchable": true, "bSortable": true},
                {"mDataProp": "topics", "bSearchable": true, "bSortable": true},
                {"mDataProp": "edit", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": true},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false}
            ],
            "bDestroy": true,
            "fnInitComplete": function (oSettings, json) {
                var cols = oSettings.aoPreSearchCols;
                for (var i = 0; i < cols.length; i++) {
                    var value = cols[i].sSearch;
                    if (value.length > 0) {
                        $("#" + filter_arr[i]).val(value);
                    }
                }
            },
            "sPaginationType": "bootstrap",
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