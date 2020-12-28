(function () {
    $(document).ready(function () {


        var table =  $('#topics_table');

        table.on('click','.action-btn > a',function () {
            $(this).toggleClass('open').siblings('.action-btn ul').slideToggle(500);
            return false;
        });

        initTable('#topics_table');

        $(document).on('mouseover','.tooltip-txt',
            function () {
                var str = $( this ).text();
                $( this ).append( "<span class='tooltip-more'></span>" )
                    .find(".tooltip-more").text( str );
            }
        );
        $(document).on('mouseleave','.tooltip-txt',
            function () {
                $( ".tooltip-more" ).remove();
            }
        );

        $(document).on("click", ".topic_title", function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/ajax_edit_topic', {id: id}, function (topic) {

                $('#edit_topic_popup_id').val(topic['id']);
                $('#edit_topic_popup_name').val(topic['name']);
                $('#edit_topic_popup_description').val(topic['description']);
                $('#course-popup-modal').modal('show');

            }, "json");
        });

        $(document).on("click", ".action_edit_topic", function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/ajax_edit_topic', {id: id}, function (topic) {

                $('#edit_topic_popup_id').val(topic['id']);
                $('#edit_topic_popup_name').val(topic['name']);
                $('#edit_topic_popup_description').val(topic['description']);
                $('#course-popup-modal').modal('show');
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

        $(document).on("click", '#edit_topic_popup_save', function(ev) {
            ev.preventDefault();
            var id = $('#edit_topic_popup_id').val();
            var name = $('#edit_topic_popup_name').val();
            var description = $('#edit_topic_popup_description').val();
            $.post('/admin/courses/ajax_update_topic', {id: id, name:name, description:description}, function (data) {


                if (data.message === 'success') {
                    initTable('#topics_table');
                    // show_msg('The note has been added', 'topics_table', 'success');
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Topic is successfully updated.</div>';
                    $("#message").prepend(smg);

                } else {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                    $("#message").prepend(smg);
                }

            }, "json");

            $('#course-popup-modal').modal('hide');

        });

        $(document).on("click", ".action_delete_topic", function(ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $("#btn_delete_yes").data('id', id);
            $("#confirm_delete").modal();
        });
        $("#btn_delete_yes").click(function (ev) {
            ev.preventDefault();
            var id = $(this).data('id');
            $.post('/admin/courses/ajax_remove_topic', {id: id}, function (data) {
                var smg;
                if (data.message === 'success') {
                    initTable('#topics_table');
                    smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Topic is successfully removed.</div>';
                    $("#topics_table_wrapper").prepend(smg);
                } else {
                    smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                    $("#topics_table_wrapper").prepend(smg);
                }
                $("#confirm_delete").modal('hide');

            }, "json");


        });


        function initTable(id) {
            var ajax_source = "/admin/courses/ajax_get_topics/?editable=1";
            var settings = {
                "aoColumns": [
                    {"mDataProp": "name", "bSearchable": true, "bSortable": true},
                    {"mDataProp": "description", "bSearchable": true, "bSortable": true},
                    {"mDataProp": "action", "bSearchable": false, "bSortable": false}
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
            var drawback_settings = {
                "fnDrawCallback": function () {
                    var tooltip_txt = $( ".tooltip-txt");
                    tooltip_txt.closest("td").addClass("tooltip-wrap");

                    // tooltip_txt.on('hover',
                    //     function () {
                    //         debugger;
                    //         var str = $( this ).text();
                    //         $( this ).append( "<span class='tooltip-more'></span>" )
                    //                  .find(".tooltip-more").html( str );
                    //     }
                    //     ,
                    //     function () {
                    //         $( ".tooltip-more" ).remove();
                    //     }
                    // );
                }
            };
            return $(id).ib_serverSideTable(ajax_source, settings, drawback_settings);
        }

    });
})();