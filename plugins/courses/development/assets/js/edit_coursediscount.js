$(document).ready(function()
{
    $(".save_btn").on('click',function()
    {
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

    $('#discount_publish_toggle').find('button').on('click', function()
    {
        $("#publish").val(this.value);
    });

    $(".multipleselect").multiselect();

    $(".for-contact").each(function(){
        for_contact_autocomplete_set(this);
    });

    has_schedule_autocomplete_set();
    has_course_autocomplete_set();

    $("#has_schedules .btn.add").on("click", function(){
        var schedule_id = $("#has_schedules input.schedule.id").val();
        var schedule = $("#has_schedules input.schedule.name").val();
        $("#has_schedules tbody").append(
            '<tr>' +
                '<td><input type="hidden" name="has_schedules[]" value="' + schedule_id + '"/> ' + schedule + ' </td>' +
                '<td><button type="button" class="btn remove" onclick="$(this.parentNode.parentNode).remove()">Remove</button> </td>' +
            '</tr>'
        );

        $("#has_schedules input.schedule.id").val("");
        $("#has_schedules input.schedule.name").val("");
    });

    $("#has_courses .btn.add").on("click", function(){
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
});

function for_contact_autocomplete_set(input)
{
    var id = input.id;
    var hid_id = id.replace('[', '_').replace(']', '');
    var $hid = $('#' + hid_id);
    var last_id = null;
    var last_label = null;

    $(input).autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $hid.val('');
            }
            $.get("/admin/contacts3/ajax_get_all_contacts_ui",
                data,
                function(response){
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

    $(input).on('blur', function(){
        if (input.value == '') {
            $hid.val('');
        }
    });
}

function has_schedule_autocomplete_set()
{
    var last_id = null;
    var last_label = null;
    var input = $("#has_schedules .schedule.name")[0];

    $("#has_schedules .schedule.name").autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $("#has_schedules .schedule.id").val("");
            }

            $.getJSON(
                "/admin/bookings/find_schedule", {
                    location: "",
                    category: "",
                    year: "",
                    term: $("#has_schedules .schedule.name").val(),
                    payment_type: 1
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
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#has_schedules .schedule.id").val("");
        }
    });
}

function has_course_autocomplete_set()
{
    var last_id = null;
    var last_label = null;
    var input = $("#has_courses .course.name")[0];

    $("#has_courses .course.name").autocomplete({
        source: function(data, callback){
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

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#has_courses .course.id").val("");
        }
    });
}
