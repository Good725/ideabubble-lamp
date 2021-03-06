$(document).ready(function ()
{
    initTable('#answers_table');

    // Toggle The publish column
    $(document).on("click", ".publish", function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/surveys/ajax_publish_answer', {id: id, state: state}, function (data)
        {
            if (data.status === 'success')
            {
                if (state === 1)
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>').data('publish', 0);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Answer successfully Archived.</div>');
                }
                else
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>').data('publish', 1);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Answer successfully published.</div>');
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
        $.post('/admin/surveys/ajax_delete_answer', {id: id}, function (data)
        {
            if (data.status === 'success')
            {
                initTable('#answers_table');
                $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong>Answer successfully removed.</div>');
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
        var ajax_source = "/admin/surveys/ajax_get_all_answers";
        var settings = {
            // List the table columns to build server side in order
            "aoColumns": [
                {"mDataProp": "id", "bSearchable": true, "bSortable": true},
                {"mDataProp": "title", "bSearchable": true, "bSortable": true},
                {"mDataProp": "type", "bSearchable": false, "bSortable": true},
                {"mDataProp": "group_name", "bSearchable": false, "bSortable": true},
                {"mDataProp": "created", "bSearchable": false, "bSortable": true},
                {"mDataProp": "updated", "bSearchable": false, "bSortable": true},
                {"mDataProp": "user", "bSearchable": false, "bSortable": true},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": true}
            ],
            "bDestroy": true,
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings)
            {
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