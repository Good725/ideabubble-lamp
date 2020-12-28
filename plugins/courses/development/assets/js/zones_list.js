(function () {
    $(document).ready(function () {

        var table =  $('#zones_table');

        table.on('click','.action-btn > a',function () {
            $(this).toggleClass('open').siblings('.action-btn ul').slideToggle(500);
            return false;
        });

        initTable('#zones_table');

        $(document).on("click", ".action_edit_zone", function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/ajax_edit_zone', {id: id}, function (zone) {
                $('#edit_zone_popup_id').val(zone['id']);
                $('#edit_zone_popup_name').val(zone['name']);
                $('#edit-zone-popup-modal').modal('show');
                // if (data.message === 'success') {
                //     if (state === 1) {
                //         $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                //         $(".publish[data-id='" + id + "']").data('publish', 0);
                //         var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Year is successfully unpublished.</div>';
                //         $("#main").prepend(smg);
                //     } else {
                //         $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                //         $(".publish[data-id='" + id + "']").data('publish', 1);
                //         var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Year is successfully published.</div>';
                //         $("#main").prepend(smg);
                //     }
                // } else {
                //     var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                //     $("#main").prepend(smg);
                // }

            }, "json");
        });

        $(document).on("click", '#edit_zone_popup_save', function(ev) {
            ev.preventDefault();
            var id = $('#edit_zone_popup_id').val();
            var name = $('#edit_zone_popup_name').val();
            $.post('/admin/courses/ajax_update_zone', {id: id, name:name}, function (data) {


                if (data.message === 'success') {
                    initTable('#zones_table');
                    // show_msg('The note has been added', 'topics_table', 'success');
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Zone is successfully updated.</div>';
                    $("#message").prepend(smg);

                } else {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                    $("#message").prepend(smg);
                }

            }, "json");

            $('#edit-zone-popup-modal').modal('hide');

        });

        $(document).on("click", ".action_delete_zone", function(ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $("#btn_delete_yes").data('id', id);
            $("#confirm_delete").modal();
        });
        $("#btn_delete_yes").click(function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/ajax_remove_zone', {id: id}, function (data) {
                var smg;
                if (data.message === 'success') {
                    initTable('#zones_table');
                    smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Zone is successfully removed.</div>';
                    $("#topics_table_wrapper").prepend(smg);
                } else {
                    smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                    $("#topics_table_wrapper").prepend(smg);
                }
                $("#confirm_delete").modal('hide');

            }, "json");


        });


    });

    function initTable(id) {
        var ajax_source = "/admin/courses/ajax_get_zones";
        var settings = {
            "aoColumns": [
                {"mDataProp": "name", "bSearchable": true, "bSortable": true},
                // {"mDataProp": "price", "bSearchable": false, "bSortable": false},
                {"mDataProp": "edit", "bSearchable": false, "bSortable": false, "sClass":  "text-center"}
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

})();