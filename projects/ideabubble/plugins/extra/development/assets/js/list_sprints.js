$(document).ready(function () {
    var table_id = '#sprints_table';
    $(table_id).dataTable().fnDestroy();
    initTable(table_id);

    function initTable(id) {
        return $(id).dataTable({
            "aoColumns": [
                {"mDataProp": "id", "bSearchable": false, "bSortable": true},
                {"mDataProp": "customer", "bSearchable": true, "bSortable": true},
                {"mDataProp": "sprint", "bSearchable": false, "bSortable": true},
                {"mDataProp": "summary", "bSearchable": false, "bSortable": false, "sWidth": "20%"},
                {"mDataProp": "budget", "bSearchable": false, "bSortable": true},
                {"mDataProp": "spent", "bSearchable": false, "bSortable": true},
                {"mDataProp": "balance", "bSearchable": false, "bSortable": true},
                {"mDataProp": "progress", "bSearchable": false, "bSortable": true},
                {"mDataProp": "status", "bSearchable": false, "bSortable": true, "sWidth": "15%"},
                {"mDataProp": "last_synced", "bSearchable": false, "bSortable": true}
            ],
            "bServerSide": true,
            "bProcessing": false,
            "bDestroy": true,
            "sAjaxSource": "/admin/extra/ajax_get_sprints2",
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
        });
    }
});

$(document).on('click', '.edit-summary-button', function () {
    if ($(this).parent().find('.edit-box').length == 1) {
        $(this).parent().find('.edit-box').remove();
    } else {
        var id = $(this).attr("val");
        $(this).parent().append('<div class="edit-box"><textarea rows="4" val="' + id + '" ></textarea><p><button class="save-summary save-button" val="' + id + '" type="button">Save</button></p></div>');
    }
});

$(document).on('click', '.edit-budget-button', function () {
    if ($(this).parent().find('.edit-box').length == 1) {
        $(this).parent().find('.edit-box').remove();
    } else {
        var id = $(this).attr("val");
        $(this).parent().append('<div class="edit-box">€<input type="text" val="' + id + '" name="budget"><p><button class="save-budget save-button" val="' + id + '" type="button">Save</button></p></div>');
    }
});

$(document).on('click', '.edit-progress-button', function () {
    if ($(this).parent().find('.edit-box').length == 1) {
        $(this).parent().find('.edit-box').remove();
    } else {
        var id = $(this).attr("val");
        $(this).parent().append('<div class="edit-box">%<input type="text" val="' + id + '" name="progress"><p><button class="save-progress save-button" val="' + id + '" type="button">Save</button></p></div>');
    }
});

$(document).on('change', '.status-dropdown', function () {
    var id = $(this).attr("val");
    var sprint_status_id = this.value;
    $.post("/admin/extra/ajax_save_sprint2", {
        "sprint_id": id,
        "sprint_status_id": sprint_status_id
    }, function () {
        location.reload();
    });
});

$(document).on('click', '.save-summary', function () {
    var id = $(this).attr("val");
    var summary_content = $("textarea[val='" + id + "']").val();
    $.post("/admin/extra/ajax_save_sprint2", {
        "sprint_id": id,
        "content": summary_content
    }, function () {
        location.reload();
    });
});

$(document).on('click', '.save-budget', function () {
    var id = $(this).attr("val");
    var budget = $("input[val='" + id + "']").val();
    $.post("/admin/extra/ajax_save_sprint2", {
        "sprint_id": id,
        "budget": budget
    }, function () {
        location.reload();
    });
});

$(document).on('click', '.save-progress', function () {
    var id = $(this).attr("val");
    var progress = $("input[val='" + id + "']").val();
    $.post("/admin/extra/ajax_save_sprint2", {
        "sprint_id": id,
        "progress": progress
    }, function () {
        location.reload();
    });
});