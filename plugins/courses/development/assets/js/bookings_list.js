var coursebooking_contact_editor = {
    setup: function(container) {
        try {
            coursebooking_list_init(true);
        } catch (exc) {

        }
    },

    validate: function(container) {

    }
};

if (window.contact_editor) {
    window.contact_editor.extensions.push(coursebooking_contact_editor);
}

function coursebooking_list_init(do_not_init_datatable)
{
    var booking_table = null;
    if (!do_not_init_datatable) {
        booking_table = initTable('#booking_table');
    }

    function initTable(id) {
        var ajax_source = "/admin/courses/ajax_get_bookings";
        var settings = {
            "aoColumns": [
                {"mDataProp": "id", "bSearchable": true, "bSortable": true},
                {"mDataProp": "course", "bSearchable": true, "bSortable": true},
                {"mDataProp": "category", "bSearchable": true, "bSortable": true},
                {"mDataProp": "schedule", "bSearchable": false, "bSortable": false},
                {"mDataProp": "provider", "bSearchable": false, "bSortable": false},
                {"mDataProp": "student", "bSearchable": false, "bSortable": false},
                {"mDataProp": "time", "bSearchable": false, "bSortable": false},
                {"mDataProp": "updated", "bSearchable": false, "bSortable": false},
                {"mDataProp": "total", "bSearchable": false, "bSortable": false},
                {"mDataProp": "outstanding", "bSearchable": false, "bSortable": false},
                {"mDataProp": "status", "bSearchable": false, "bSortable": false},
                {"mDataProp": "actions", "bSearchable": false, "bSortable": false}
            ],
            "bDestroy": true,
            "aaSorting" : [[ 5, "desc" ]],
            "sPaginationType" : "bootstrap",
            "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                });
            },
            "aoColumnDefs": [{
                "aTargets": [1],
                "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                {
                    // Add data attribute, with the contact ID to each row
                    $(nTd).parent().attr({'data-id': oData['id']});
                }
            }]
        };
        return $(id).ib_serverSideTable(ajax_source, settings);
    }

    $(document).on("click", "a.cancel[data-booking_id]", function(e){
        e.preventDefault();
        $("#btn_cancel_booking_yes").data("booking_id", $(this).data("booking_id"));
        if (parseFloat($(this).data("outstanding")) > 0) {
            $("#confirm_cancel_booking .outstanding").removeClass("hidden");
            $("#confirm_cancel_booking .outstanding .amount").html($(this).data("outstanding"));
            $("#confirm_clear_outstanding").prop("checked", true);
        } else {
            $("#confirm_cancel_booking .outstanding").addClass("hidden");
            $("#confirm_clear_outstanding").prop("checked", false);
        }
        $("#confirm_cancel_booking").modal("show");
        return false;
    });

    $("#btn_cancel_booking_yes").on("click", function(){
        var btn = this;
        btn.disabled = true;
        var booking_id = $(this).data("booking_id");
        $.post(
            "/admin/courses/cancel_booking",
            {
                booking_id: booking_id,
                clear_outstanding: $("#confirm_clear_outstanding").prop("checked") ? 1 : 0,
                note: $("#cancel_note").val()
            },
            function (response) {
                btn.disabled = false;
                $(btn).data("booking_id", "");
                $("#confirm_cancel_booking").modal("hide");
                window.location.reload();
            }
        )
    });

    var transfer_item_template = null;

    function calculate_fee_diff()
    {
        var diff = 0;
        $(".transfer_items .transfer_item").each(function(){
            var item = $(this);
            var from_schedule = item.find(".transfer_from_bookingschedule_id");
            var to_schedule = item.find(".transfer_to_schedule_id");
            if (from_schedule[0].selectedIndex > -1 && to_schedule[0].selectedIndex > 0) {
                diff += parseFloat(to_schedule.find("option:selected").data("fee")) - parseFloat(from_schedule.find("option:selected").data("fee"));
            }
        });

        $(".fee_diff_calculated").html(diff);


    }

    $(document).on("click", "a.transfer[data-booking_id]", function(e){
        e.preventDefault();
        $("#btn_transfer_booking_yes").data("booking_id", $(this).data("booking_id"));
        $.post(
            "/admin/courses/get_booking_data",
            {
                booking_id : $(this).data("booking_id")
            },
            function (response){

                if (transfer_item_template == null) {
                    transfer_item_template = $(".transfer_item.template");
                    transfer_item_template.remove();
                }
                var transfer_items = $(".transfer_items");
                transfer_items.html("");
                for(var i = 0 ; i < response.booking.has_schedules.length ; ++i) {
                    var transfer_item = transfer_item_template.clone();
                    transfer_item.removeClass("template");
                    transfer_item.find('.transfer_from_bookingschedule_id').append(
                        '<option selected="selected" value="' + response.booking.has_schedules[i].id + '" data-fee="' + response.booking.has_schedules[i].fee_amount + '">' + response.booking.has_schedules[i].name + ' (€' + response.booking.has_schedules[i].fee_amount + ')' + '</option>'
                    );

                    for(var i = 0 ; i < response.schedules.length ; ++i) {
                        transfer_item.find('.transfer_to_schedule_id').append(
                            '<option value="' + response.schedules[i].schedule_id + '" data-fee="' + response.schedules[i].fee_amount + '" data-timeslot_id="' + response.schedules[i].timeslot_id + '">' + response.schedules[i].item + ' (€' + response.schedules[i].fee_amount + ')' + '</option>'
                        );
                    }

                    transfer_items.append(transfer_item);

                }
                $("#transfer_booking").modal("show");

                $(".transfer_from_schedule_id, .transfer_to_schedule_id").on("change", calculate_fee_diff);
            }
        );
        return false;
    });

    $("#btn_transfer_booking_yes").on("click", function(e){
        e.preventDefault();
        var data = {transfer: [], booking_id: $(this).data("booking_id")};
        $(".transfer_items .transfer_item").each (function(){
            var item = {};
            item.from_bookingschedule_id = $(this).find(".transfer_from_bookingschedule_id").val();
            item.to_schedule_id = $(this).find(".transfer_to_schedule_id").val();
            item.to_timeslot_id = $(this).find(".transfer_to_schedule_id option:selected").data("timeslot_id");
            data.transfer.push(item);
        });

        $.post(
            "/admin/courses/transfer_booking",
            data,
            function (response) {
                if(response.new_booking) {
                    $("#transfer_booking").modal("hide");
                    window.location.reload();
                }
            }
        );

        return false;
    });

    $("#booking_table").on("click", "tbody > tr", function(){
        coursebooking_load($(this).data("id"));
    });
}

function coursebooking_load(id)
{
    $.get(
        "/admin/courses/edit_booking/" + id,
        {

        },
        function (response_html) {
            $(".course_booking_details").html(response_html);
            $(".course_booking_details .make-payment").on("click", function(){
                if ($(this).parents(".tab-content").parent().find("[href*=accounts]").length > 0) {
                    $(this).parents(".tab-content").parent().find("[href*=accounts]").click();
                    $(this).parents(".tab-content").parent().find(".table.transactions tr[data-id=" + $(this).data("transaction_id") + "]").click();
                    $(this).parents(".tab-content").parent().find(".add.payment").click();
                } else {
                    load_transaction($(document), $(this).data("transaction_id"));
                }
            });

            $(".course_booking_details .booking-btns a[data-action]").on("click", function(){
                $("form.course-booking-form [name=action]").val($(this).data("action"));
                //$("form.course-booking-form").submit();
                var data = $("form.course-booking-form").serialize();
                $.post(
                    "/admin/courses/save_booking",
                    {
                        data_json : JSON.stringify(data),
                    },
                    function (response) {
                        coursebooking_load(response.id);
                    }
                )
            });
        }
    )
}

$(document).ready(function () {
    coursebooking_list_init();
});
