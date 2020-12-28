$(document).ready(function ()
{
    // Toggle The publish column
    $(document).on("click", ".publish", function (ev)
    {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        console.log('ID:'+id+'  State:'+state);
        $.post('/admin/surveys/ajax_publish_group', {id: id, state: state}, function (data)
        {
            if (data.status === 'success')
            {
                if (state === 1)
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>').data('publish', 0);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Group successfully Archived.</div>');
                }
                else
                {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>').data('publish', 1);
                    $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Group successfully published.</div>');
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
        var $button = $("#list-facilitygroups-table tbody tr[data-id=" + id + "]");
        $.post('/admin/surveys/ajax_delete_group', {id: id}, function (data)
        {
            if (data.status === 'success')
            {
                $button.remove();
                $("#main").prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong>Group successfully removed.</div>');
            }
            else
            {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>');
            }
            $("#confirm_delete").modal('hide');
        }, "json");
    });
});