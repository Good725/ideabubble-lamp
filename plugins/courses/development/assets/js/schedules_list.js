$(document).ready(function () {
    var table = $('#schedule_table');
    $("#courses").click();
    initTable('#schedule_table');
    $(document).on("click", ".publish", function(ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        var state = $(this).data('publish');
        $.post('/admin/courses/ajax_publish_schedule', {id: id, state: state}, function (data) {
            if (data.message === 'success') {
                if (state === 1) {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ban-circle"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 0);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Schedule is successfully unpublished.</div>';
                    $("#main").prepend(smg);
                } else {
                    $(".publish[data-id='" + id + "']").html('<i class="icon-ok"></i>');
                    $(".publish[data-id='" + id + "']").data('publish', 1);
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Schedule is successfully published.</div>';
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
        $.post('/admin/courses/schedule_has_booking', {id: id}, function (data) {
            if (data.status === 'success') {
                alert('Schedule has booking it is not possible to delete !!!');
            }
            else {
                $("#confirm_delete").modal();
            }
        }, "json");
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_schedule', {id: id}, function (data) {
            if (data.message === 'success') {
                initTable('#schedule_table');
                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">X</a><strong>Success: </strong> Schedule is successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });

    // Search by individual columns
    table.find('.search_init').on('change', function ()
    {
        table.dataTable().fnFilter(this.value, table.find('tr .search_init').index(this) );
    });

    function initTable(id) {
		var filter_arr = ["search_id", "search_course", "search_name", "search_status_label", "search_category", "search_fee_amount", "search_repeat_name", "search_start_date", "search_location", "", "search_trainer", "", "search_date_modified"];
		var settings = {
            "fnServerParams": function ( aoData ) { //pass id to dataTable if present
                var course_id = $('#form_add_edit_course #id').val();
                if (course_id){
                    aoData.push( { "name": "course_id", "value": course_id } );
                }
            },
            "aaSorting": [[ 11, "desc"]],
            "sDom": '<"top"if<"clear">>rt<"bottom"ilp<"clear">>',
            "aoColumns": [
                // Update the aaSorting value to the "last_modified" column number, when adding/removing/reordering columns
                {"mDataProp": "id", "bSearchable": true, "bSortable": true},
                {"mDataProp": "course", "bSearchable": true, "bSortable": true},
                {"mDataProp": "name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "fee", "bSearchable": true, "bSortable": true},
                {"mDataProp": "location", "bSearchable": true, "bSortable": true},
                {"mDataProp": "status_label", "bSearchable": true, "bSortable": true},
                {"mDataProp": "start_date", "bSearchable": true, "bSortable": true},
                {"mDataProp": "repeat_name", "bSearchable": true, "bSortable": true},
                {"mDataProp": "times", "bSearchable": false, "bSortable": false},
                {"mDataProp": "trainer", "bSearchable": true, "bSortable": true},
                {"mDataProp": "confirmed", "bSearchable": false, "bSortable": true},
                {"mDataProp": "last_modified", "bSearchable": true, "bSortable": true},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false},
                {"mDataProp": "availability", "bSearchable": false, "bSortable": false},
                {"mDataProp": "publish", "bSearchable": false, "bSortable": true}
            ],
            "bStateSave" : true,
            "fnInitComplete": function(oSettings, json) {
                var cols = oSettings.aoPreSearchCols;
                for (var i = 0; i < cols.length; i++) {
                    var value = cols[i].sSearch;
                    if (value.length > 0) {
                        $("#"+filter_arr[i]).val(value);
                    }
                }
            },
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
		return $(id).ib_serverSideTable("/admin/courses/ajax_get_schedules", settings);
    }
    $(document.body).on("mouseenter",".more_info,.schedule_dates_list",function(){
        console.log("hovering");
        var id = $(this).data('schedule');
        $("#more_times_"+id).show();
    });
    $(document.body).on("mouseleave",".more_info,.schedule_dates_list",function(){
        console.log("leaving");
        var id = $(this).data('schedule');
        $("#more_times_"+id).hide();
    });
	$("#resetdatatable").click(function (ev) {
		table.fnFilterClear();
		$(".search_init").val("");
	})

});