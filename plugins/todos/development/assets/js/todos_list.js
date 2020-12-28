$(document).ready(function () {
    var $table = $('#todo_table');
    var searchParams = new URLSearchParams(window.location.search);
    var ajax_source =  '/admin/todos/list_datatable';
    var aoColumns = (searchParams.get('my') == '1') ? [
        {"mDataProp": "title", "bSearchable": true, "bSortable": true},
        {"mDataProp": "delivery_mode", "bSearchable": true, "bSortable": true},
        {"mDataProp": "category", "bSearchable": true, "bSortable": true},
        {"mDataProp": "type", "bSearchable": false, "bSortable": false},
        {"mDataProp": "schedule", "bSearchable": true, "bSortable": true},
        {"mDataProp": "reporter", "bSearchable": false, "bSortable": true},
        {"mDataProp": "status", "bSearchable": false, "bSortable": true},
        {"mDataProp": "date", "bSearchable": false, "bSortable": true},
        {"mDataProp": "actions", "bSearchable": false, "bSortable": false}
    ] : [
        {"mDataProp": "title", "bSearchable": true, "bSortable": true},
        {"mDataProp": "delivery_mode", "bSearchable": true, "bSortable": true},
        {"mDataProp": "category", "bSearchable": true, "bSortable": true},
        {"mDataProp": "type", "bSearchable": false, "bSortable": true},
        {"mDataProp": "schedule", "bSearchable": true, "bSortable": true},
        {"mDataProp": "reporter", "bSearchable": false, "bSortable": true},
        {"mDataProp": "assignee", "bSearchable": false, "bSortable": false},
        {"mDataProp": "status", "bSearchable": false, "bSortable": true},
        {"mDataProp": "date", "bSearchable": false, "bSortable": true},
        {"mDataProp": "updated", "bSearchable": false, "bSortable": true},
        {"mDataProp": "actions", "bSearchable": false, "bSortable": false}
    ];
    var settings = {
        "aoColumns": aoColumns,
        "aaSorting": (searchParams.get('my') == '1') ? [[5, "desc"]] : [[8, "desc"]],
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            aoData.push({
                "name": "my",
                "value": (searchParams.get('my') == '1') ? true : false
            });
            oSettings.jqXHR = $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        }
    };
    $table.ib_serverSideTable(ajax_source, settings);

    $(document.body).on("mouseenter", ".more_info", function () {
        $(".assignees_list").show();
    });
    $(document.body).on("mouseleave", ".more_info", function () {
        $(".assignees_list").hide();
    });

    $(document).on("click", ".delete", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var $tr = $(this).parents("tr");
        $("#btn_delete_yes").data('id', id);
        $("#btn_delete_yes")[0].tr = $tr;
        $("#confirm_delete").modal();
    });

    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post(
            '/admin/todos/delete',
            {id: id},
            function (data) {
                if (data.success == 1) {
                    $("#btn_delete_yes")[0].tr.remove(); //find("td").css("text-decoration", "line-through");
                    var smg = '<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Todo has been successfully removed.</div>';
                    $("#todo_table").parent().prepend(smg);
                } else {
                    var smg = '<div class="alert alert-error popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.message + '</div>';
                    $("#todo_table").parent().prepend(smg);
                }
                $("#confirm_delete").modal('hide');
            }
        );
    });


    $(document).on("click", ".email", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post(
            '/admin/todos/email/' + id,
            {},
            function (data) {
                if(data.length == 0) {
                    var smg = '<div class="alert alert-info popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Not sent: </strong> No results have been detected for the assessment.</div>';
                } else {
                    var smg = '<div class="alert alert-success popup_box"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Assessment results have been emailed.</div>';
                }
                $("#todo_table").parent().prepend(smg);
            }
        );
    });
});
