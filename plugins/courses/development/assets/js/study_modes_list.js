$(document).ready(function () {
    initTable('#study_mode_table');
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_study_mode', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Study mode is successfully unpublished.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Study mode is successfully published.</div>';
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
        $.post('/admin/courses/ajax_remove_study_mode', {id: id}, function (data) {
            if (data.message === 'success') {
                initTable('#study_mode_table');
                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Study mode is successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });


    function initTable(id) {
        var ajax_source = "/admin/courses/ajax_get_study_modes";
        var settings = {
            "aoColumns": [
                {"mDataProp": "study_mode", "bSearchable": true, "bSortable": true},
                {"mDataProp": "summary", "bSearchable": true, "bSortable": true},
                {"mDataProp": "edit", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": false},
                {"mDataProp": "remove", "bSearchable": false, "bSortable": false}
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
            }
        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }


});