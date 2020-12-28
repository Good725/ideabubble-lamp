$(document).ready(function () {

    $("#use_limits").on("change", function(){
        $("table#daily_rates").css("display", "none");
        $("table#per_day_rates").css("display", "none");
        $("table#qty_rates").css("display", "none");
        $("input#amount").css("display", "none");

        if (this.value == 'MAX') {
            $("table#daily_rates").css("display", "");
            $("table#per_day_rates").css("display", "");
        } else if (this.value == 'QTY') {
            $("table#qty_rates").css("display", "");
        } else {
            $("input#amount").css("display", "");
        }
    });

    var $qty_rate_template = $("#qty_rates tbody > tr.template");
    $qty_rate_template.remove();
    $qty_rate_template.removeClass("template");
    $("#qty_rates button.add").on("click", function(){
        var $tbody = $("#qty_rates tbody");
        var index = $tbody.find(">tr").length;
        var $tr = $qty_rate_template.clone();

        $tr.html($qty_rate_template.html().replace(/index/g, index));
        $tbody.append($tr);
    });

    $("#qty_rates tbody").on("click", "button.remove", function(){
        $(this).parent().parent().remove();
    });

    var $daily_rate_template = $("#daily_rates tbody > tr.template");
    $daily_rate_template.remove();
    $daily_rate_template.removeClass("template");
    $("#daily_rates button.add").on("click", function(){
        var $tbody = $("#daily_rates tbody");
        var index = $tbody.find(">tr").length;
        var $tr = $daily_rate_template.clone();

        $tr.html($daily_rate_template.html().replace(/index/g, index));
        $tbody.append($tr);
    });

    $("#daily_rates tbody").on("click", "button.remove", function(){
        $(this).parent().parent().remove();
    });

    var $per_day_rate_template = $("#per_day_rates tbody > tr.template");
    $per_day_rate_template.remove();
    $per_day_rate_template.removeClass("template");
    $("#per_day_rates button.add").on("click", function(){
        var $tbody = $("#per_day_rates tbody");
        var index = $tbody.find(">tr").length;
        var $tr = $per_day_rate_template.clone();

        $tr.html($per_day_rate_template.html().replace(/index/g, index));
        $tbody.append($tr);
    });

    $("#per_day_rates tbody").on("click", "button.remove", function(){
        $(this).parent().parent().remove();
    });

    $(".save_btn").on('click', function () {
        if (!$("#discount_edit_form").validationEngine('validate')) {
            return false;
        }
        if ($("[name=amount_type]").val() == 'Percent' && parseFloat($("[name=amount]").val()) > 100) {
            $("#percent_over_100_warning").modal("show");
            return false;
        }
        $("#redirect").val(this.getAttribute('data-action'));
        $("#discount_edit_form").submit();
    });

    $('#discount_publish_toggle').find('button').on('click', function () {
        $("#publish").val(this.value);
    });

    $(".multipleselect").multiselect();

    $(".for-contact").each(function () {
        for_contact_autocomplete_set(this);
    });

    has_schedule_autocomplete_set();
    has_previous_schedule_autocomplete_set();
    has_course_autocomplete_set();
    has_previous_course_autocomplete_set();
    has_previous_category_autocomplete_set();

    $("#has_schedules .btn.add").on("click", function () {
        var schedule_id = $("#has_schedules input.schedule.id").val();
        var schedule = $("#has_schedules input.schedule.name").val();
        $("#has_schedules tbody").append(
            '<tr>' +
            '<td class="schedule" data-fee_per="' + $("#has_schedules input.schedule.id").data("fee_per") + '" data-fee_amount="' + $("#has_schedules input.schedule.id").data("fee_amount") + '"><input type="hidden" name="has_schedules[]" value="' + schedule_id + '"/> ' + schedule + ' </td>' +
            '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_schedules input.schedule.id").val("");
        $("#has_schedules input.schedule.name").val("");
    });

    $("#has_courses .btn.add").on("click", function () {
        var course_id = $("#has_courses input.course.id").val();
        var course = $("#has_courses input.course.name").val();
        $("#has_courses tbody").append(
            '<tr>' +
            '<td><input type="hidden" name="has_courses[]" value="' + course_id + '"/> ' + course + ' </td>' +
            '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_courses input.course.id").val("");
        $("#has_courses input.course.name").val("");
    });


    $("#has_previous_schedules .btn.add.previous").on("click", function () {
        var schedule_id = $("#has_previous_schedules input.schedule.id").val();
        var schedule = $("#has_previous_schedules input.schedule.name").val();
        $("#has_previous_schedules tbody").append(
            '<tr>' +
            '<td><input type="hidden" name="has_previous_schedules[]" value="' + schedule_id + '"/> ' + schedule + ' </td>' +
            '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_previous_schedules input.schedule.id").val("");
        $("#has_previous_schedules input.schedule.name").val("");
    });

    $("#has_previous_courses .btn.add.previous").on("click", function () {
        var course_id = $("#has_previous_courses input.course.id").val();
        var course = $("#has_previous_courses input.course.name").val();
        $("#has_previous_courses tbody").append(
            '<tr>' +
            '<td><input type="hidden" name="has_previous_courses[]" value="' + course_id + '"/> ' + course + ' </td>' +
            '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_previous_courses input.course.id").val("");
        $("#has_previous_courses input.course.name").val("");
    });


    $("#has_previous_category .btn.add.previous").on("click", function () {
        var category_id = $("#has_previous_category input.category.id").val();
        var category = $("#has_previous_category input.category.name").val();
        $("#has_previous_category tbody").append(
            '<tr>' +
            '<td><input type="hidden" name="has_previous_category[]" value="' + category_id + '"/> ' + category + ' </td>' +
            '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_previous_category input.category.id").val("");
        $("#has_previous_category input.category.name").val("");
    });


    $(document).on("click", "#has_schedules td.schedule", function(){
        var schedule_id = $(this).find("input").val();
        var fee_per = $(this).data("fee_per");
        var fee_amount = $(this).data("fee_amount");
        $("#schedule_details_modal .fee_per").html(fee_per);
        $("#schedule_details_modal .fee").html(fee_amount);
        $.get (
            "/admin/courses/get_active_timetable",
            {
                schedule: schedule_id
            },
            function (response) {
                var timetable_id = parseInt(response);
                $.post(
                    "/admin/courses/timetable_get_dates",
                    {
                        timetable_id: timetable_id
                    },
                    function (response) {
                        $("#schedule_details_modal table tbody").html(response);
                        $("#schedule_details_modal table tbody").find("td.delete_me").remove();
                        $("#schedule_details_modal table tbody > tr").each (function(){

                        });
                        $("#schedule_details_modal").modal();
                    }
                );
            }
        );
    });
});

function for_contact_autocomplete_set(input) {
    var id = input.id;
    var hid_id = id.replace('[', '_').replace(']', '');
    var $hid = $('#' + hid_id);
    var last_id = null;
    var last_label = null;

    $(input).autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $hid.val('');
            }
            $.get("/admin/contacts3/ajax_get_all_contacts_ui",
                data,
                function (response) {
                    callback(response);
                });
        },
        open: function () {
            if (last_label != input.value) {
                $hid.val('');
            }
        },
        select: function (event, ui) {
            $hid.val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $hid.val('');
        }
    });
}

function has_schedule_autocomplete_set() {
    var last_id = null;
    var last_label = null;
    var input = $("#has_schedules .schedule.name")[0];

    $("#has_schedules .schedule.name").autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $("#has_schedules .schedule.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_schedule", {
                    location: "",
                    category: "",
                    year: "",
                    term: $("#has_schedules .schedule.name").val(),
                    payment_type: ""
                },
                callback
            );

            /*$.get("/admin/bookings/find_schedule?location=&category=&year=",
             data,
             function(response){
             callback(response);
             });
             */
        },
        open: function () {
            if (last_label != input.value) {
                $("#has_schedules .schedule.id").val("");
            }
        },
        select: function (event, ui) {
            $("#has_schedules .schedule.id").val(ui.item.id);
            $("#has_schedules .schedule.id").data("fee_per", ui.item.fee_per);
            $("#has_schedules .schedule.id").data("fee_amount", ui.item.fee_amount);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $("#has_schedules .schedule.id").val("");
        }
    });
}

function has_course_autocomplete_set() {
    var last_id = null;
    var last_label = null;
    var input = $("#has_courses .course.name")[0];

    $("#has_courses .course.name").autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $("#has_courses .course.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_course", {
                    term: $("#has_courses .course.name").val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#has_courses .course.id").val("");
            }
        },
        select: function (event, ui) {
            $("#has_courses .course.id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $("#has_courses .course.id").val("");
        }
    });
}


function has_previous_schedule_autocomplete_set() {
    var last_id = null;
    var last_label = null;
    var input = $("#has_previous_schedules .schedule.name")[0];

    $("#has_previous_schedules .schedule.name").autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $("#has_previous_schedules .schedule.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_schedule", {
                    location: "",
                    category: "",
                    year: "",
                    term: $("#has_previous_schedules .schedule.name").val(),
                    payment_type: ""
                },
                callback
            );

            /*$.get("/admin/bookings/find_schedule?location=&category=&year=",
             data,
             function(response){
             callback(response);
             });
             */
        },
        open: function () {
            if (last_label != input.value) {
                $("#has_previous_schedules .schedule.id").val("");
            }
        },
        select: function (event, ui) {
            $("#has_previous_schedules .schedule.id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $("#has_previous_schedules .schedule.id").val("");
        }
    });
}

function has_previous_course_autocomplete_set() {
    var last_id = null;
    var last_label = null;
    var input = $("#has_previous_courses .course.name")[0];

    $("#has_previous_courses .course.name").autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $("#has_previous_courses .course.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_course", {
                    term: $("#has_previous_courses .course.name").val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#has_previous_courses .course.id").val("");
            }
        },
        select: function (event, ui) {
            $("#has_previous_courses .course.id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $("#has_previous_courses .course.id").val("");
        }
    });
}

function has_previous_category_autocomplete_set() {
    var last_id = null;
    var last_label = null;
    var input = $("#has_previous_category .category.name")[0];

    $("#has_previous_category .category.name").autocomplete({
        source: function (data, callback) {
            if (last_label != data.term) {
                $("#has_previous_category .category.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_category", {
                    term: $("#has_previous_category .category.name").val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#has_previous_category .category.id").val("");
            }
        },
        select: function (event, ui) {
            $("#has_previous_category .category.id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function () {
        if (input.value == '') {
            $("#has_previous_category .category.id").val("");
        }
    });
}

window.discount_image_uploaded = function(filename, path, data, upload_wrapper)
{
	if (data.media_id)
	{
		var $image_section = $('#edit_discount-image');
		$('#edit_discount-image_media_id').val(data.media_id);
		$image_section.find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path).removeClass('hidden');

		upload_wrapper.find(".file_previews").focus();
		try {
			upload_wrapper.find(".file_previews")[0].scrollIntoView();
		} catch (exc) {

		}

		existing_image_editor(
			window.location.protocol + '//' + window.location.host + "" + path,
			"courses",
			function (response) {
				$("[name=event_image_media_id]").val(response.media_id);
				$image_section.find('img').prop('src', window.location.protocol + '//' + window.location.host + "" + path.replace('/content/', '/courses/').replace('/_thumbs_cms/', '/')).removeClass('hidden');
				$('#edit_image_modal').modal('hide');
			},
			'locked'
		);
	}
};