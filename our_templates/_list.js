$(document).ready(function () {
    $('#academic_year_table').dataTable().fnDestroy();
    initTable('#academic_year_table');

    // Toggle The publish column
    $(document).on("click", ".publish", function (ev) {
        ev.preventDefault();
        var id    = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/____/ajax_publish_', {id: id, state: state}, function (data)
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

    // Click Delete open Modal box
    $(document).on("click", ".delete", function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });

    // Confirm and delete list entry
    $("#btn_delete_yes").click(function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/_____/ajax_delete_', {id: id}, function (data)
        {
            if (data.status === 'success')
            {
                initTable('#subjects_table');
                $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Academic Year successfully removed.</div>');
            }
            else
            {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>');
            }
            $("#confirm_delete").modal('hide');
        }, "json");
    });

    // Build the table
    function initTable(id)
    {
        var ajax_source = "/admin/courses/ajax_get_all_";
        var settings = {
            // List the table columns to build server side in order
            "aoColumns": [
                {"mDataProp": "id",            "bSearchable": true,  "bSortable": true},
                {"mDataProp": "",         "bSearchable": true,  "bSortable": true}
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