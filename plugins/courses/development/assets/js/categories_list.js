$(document).ready(function () {
    initTable('#category_table');
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_category', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Category is successfully unpublished.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Category is successfully published.</div>';
                    $("#main").prepend(smg);
                }
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }

        }, "json");
    });

    $(document).on("click", ".tutorial", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('grinds_tutorial');
        $.post('/admin/courses/ajax_tutorial_category', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".tutorial[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".tutorial[data-id='" + id + "']").data('grinds_tutorial', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Category is successfully set as tutorial.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".tutorial[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".tutorial[data-id='" + id + "']").data('grinds_tutorial', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Category is successfully unset as tutorial.</div>';
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
        $.post('/admin/courses/ajax_remove_category', {id: id}, function (data) {
            if (data.message === 'success') {
                initTable('#category_table');
                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Category is successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });


    function initTable(id) {
        var ajax_source = "/admin/courses/ajax_get_categories";
        var settings = {
            "aoColumns": [
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "summary", "bSearchable": true, "bSortable": true},
                {"mDataProp": "start_time", "bSearchable": false, "bSortable": true},
                {"mDataProp": "end_time", "bSearchable": false, "bSortable": true},
                {"mDataProp": "order", "bSearchable": true, "bSortable": true},
                {"mDataProp": "grinds_tutorial", "bSearchable": false, "bSortable": false},
                {"mDataProp": "last_modified", "bSearchable": false, "bSortable": true},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": false}
            ],
            "bDestroy": true,
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