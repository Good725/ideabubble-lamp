$(document).ready(function () {
    initTable('#subjects_table');
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id    = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_subject', {id: id, state: state}, function (data)
        {
            if (data.message === 'success')
            {
                if (state === 1)
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>').data('publish', 0);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Subject successfully unpublished.</div>');
                }
                else
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>').data('publish', 1);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Subject successfully published.</div>');
                }
            }
            else
            {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>');
            }

        }, "json");
    });

    $(document).on("click", ".delete", function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });

    $("#btn_delete_yes").click(function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_delete_subject', {id: id}, function (data)
        {
            if (data.message === 'success')
            {
                initTable('#subjects_table');
                $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Subject successfully removed.</div>');
            }
            else
            {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>');
            }
            $("#confirm_delete").modal('hide');
        }, "json");
    });

    function initTable(id)
    {
        var ajax_source = "/admin/courses/ajax_get_subjects";
        var settings = {
            "aoColumns": [
                {"mDataProp": "id",            "bSearchable": true,  "bSortable": true},
                {"mDataProp": "color",         "bSearchable": true,  "bSortable": true},
                {"mDataProp": "cycle",         "bSearchable": true,  "bSortable": true},
                {"mDataProp": "name",          "bSearchable": true,  "bSortable": true},
                {"mDataProp": "date_created",  "bSearchable": false, "bSortable": true},
                {"mDataProp": "date_modified", "bSearchable": false, "bSortable": true},
                {"mDataProp": "edit",          "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish",       "bSearchable": false, "bSortable": true},
                {"mDataProp": "delete",        "bSearchable": false, "bSortable": true}
            ],
            "bDestroy": true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings)
            {
                oSettings.jqXHR = $.ajax({
                    "dataType" : 'json',
                    "type"     : "POST",
                    "url"      : sSource,
                    "data"     : aoData,
                    "success"  : fnCallback
                });
            }
        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }
});