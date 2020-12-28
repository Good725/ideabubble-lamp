$(document).ready(function () {
    initTable('#academic_year_table');
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id    = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_academic_year', {id: id, state: state}, function (data)
        {
            if (data.status === 'success')
            {
                if (state === 1)
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>').data('publish', 0);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Academic Year successfully Archived.</div>');
                }
                else
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>').data('publish', 1);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> AcademicYear successfully published.</div>');
                }
            }
            else
            {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>');
            }

        }, "json");
    });

    $(document).on("click", ".status", function (ev) {
        ev.preventDefault();
        var id    = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_status_academic_year', {id: id, state: state}, function (data)
        {
            if (data.status === 'success')
            {
                if (state === 1)
                {
                    $(".status[data-id='" + id + "']").html('Pending').data('status', 0);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Academic Year status is Pending.</div>');
                }
                else
                {
                    $(".status[data-id='" + id + "']").html('Active').data('status', 1);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Academic Year status is Active.</div>');
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
        $.post('/admin/courses/ajax_delete_academic_year', {id: id}, function (data)
        {
            if (data.status === 'success')
            {
                initTable('#academic_year_table');
                $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Academic Year successfully removed.</div>');
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
        var ajax_source = "/admin/courses/ajax_get_all_academic_years";
        var settings = {
            "aoColumns": [
                {"mDataProp": "id",            "bSearchable": true,  "bSortable": true},
                {"mDataProp": "title",         "bSearchable": true,  "bSortable": true},
                {"mDataProp": "start_date",    "bSearchable": false, "bSortable": true},
                {"mDataProp": "end_date",      "bSearchable": false, "bSortable": true},
                {"mDataProp": "status",        "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish",       "bSearchable": false, "bSortable": true},
                {"mDataProp": "edit",          "bSearchable": false, "bSortable": false},
                {"mDataProp": "updated_on",    "bSearchable": false, "bSortable": true},
                {"mDataProp": "delete",        "bSearchable": false, "bSortable": false}
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