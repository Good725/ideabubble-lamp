function todo_category_autocomplete()
{
       $("#todo-category-search-autocomplete").autocomplete({
        source: function(data, callback){
            $.getJSON(
                "/admin/todos/ajax_search_item/category", {
                    term: $("#todo-category-search-autocomplete").val(),
                },
                callback
            );
        },
        select: function (event, ui) {
            $("#todo-category-id").val(ui.item.id);
        }
    });
}

function has_course_autocomplete_set()
{
    var last_id = null;
    var last_label = null;
    var input = $("#todo-course-search-autocomplete")[0];

    $("#todo-course-search-autocomplete").autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $("#todo-course-search-autocomplete-id").val("");
            }

            $.getJSON(
                "/admin/courses/find_course", {
                    term: $("#todo-course-search-autocomplete").val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#todo-course-search-autocomplete-id").val("");
            }
        },
        select: function (event, ui) {
            $("#todo-course-search-autocomplete-id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#todo-course-search-autocomplete-id").val("");
        }
    });
}

function has_schedule_autocomplete_set_1()
{
    var autocomplete_input_id = "#todo-group-schedule-search-autocomplete1";
    var last_id = null;
    var last_label = null;
    var input = $(autocomplete_input_id)[0];
    var select_dropdown_menu = $('select[name="student-picker-multiselect[]"');
    var dropdown_menu = $('select[name="student-picker-multiselect[]"').next(".btn-group").find(".multiselect-container.dropdown-menu");
    var selected_text = $('select[name="student-picker-multiselect[]"').next(".btn-group").find(".multiselect-selected-text");
    var select_list_clone = select_dropdown_menu.children("option:first-child").clone(true);
    var list_clone = dropdown_menu.children("li:not(.multiselect-item)").first().clone(true);
    $(autocomplete_input_id).autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $("#todo-schedule-search-autocomplete-id1").val("");
            }

            $.getJSON(
                "/admin/courses/find_schedule", {
                    location: "",
                    category: "",
                    year: "",
                    payment_type: 1,
                    num_students_in_schedule: true,
                    ignore_fee: true,
                    term: $(input).val()
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#todo-schedule-search-autocomplete-id1").val("");
            }
        },
        select: function (event, ui) {
            // When an item is selected in the autocomplete
            $("#todo-group-schedule-search-autocomplete-id1").val(ui.item.id);
            populate_assignee_dropdown(ui.item.id);
        },
        change: function (event, ui) {
            // When the input is blanked
            if (this.value == '') {
                $('#todo-group-schedule-search-autocomplete-id1').val('');
                populate_assignee_dropdown(null);
            }
        }
    });

    $('#todo-group-schedule-search-multiselect').on('change', function() {
        populate_assignee_dropdown($(this).val());
    });

    function populate_assignee_dropdown(schedule_ids)
    {
        $.post("/admin/courses/get_students",
            {
                schedule_id: schedule_ids
            },
            function (data, status) {

                select_dropdown_menu.children().remove("option");
                selected_text.remove();
                var select_dropdown_menu_html = select_dropdown_menu.clone(true);
                dropdown_menu.children().remove("li:not(.multiselect-item)");
                var dropdown_menu_html = dropdown_menu.clone(true);
                dropdown_menu.children().remove("li");
                for (var i = 0; i < data.length; i++) {
                    list_clone.find(".checkbox.form-checkbox > input[type=checkbox]").val(data[i].student_id);
                    list_clone.find(".checkbox.form-checkbox > span.multiselect-item-text").text(data[i].student_id + " - " + data[i].first_name + " " + data[i].last_name);
                    select_list_clone.text(data[i].student_id + " - " + data[i].first_name + " " + data[i].last_name);
                    select_list_clone.val(data[i].student_id);
                    dropdown_menu_html.append(list_clone.clone(true));
                    select_dropdown_menu_html.append(select_list_clone.clone(true));
                }
                dropdown_menu.append(dropdown_menu_html.find("li"));
                select_dropdown_menu.append(select_dropdown_menu_html.find("option"));
                select_dropdown_menu.multiselect('selectAll', false);
                select_dropdown_menu.multiselect('updateButtonText');
                var selected_text_parent = select_dropdown_menu.siblings(".btn-group").children("button").first();
                if(selected_text_parent.children("span").length < 1) {
                    select_dropdown_menu.siblings(".btn-group").children("button").first()
                        .append("<span class='multiselect-selected-text'>All selected</span>")
                } else {
                    select_dropdown_menu.siblings(".btn-group").children("button").children()
                        .text(data.length ? 'All selected' : '');
                }
                selected_text_parent.siblings(".multiselect-container").find(".multiselect-counters-total, .multiselect-counters-shown").text(data.length);
                select_dropdown_menu.trigger('change');
            });

    }

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#todo-schedule-search-autocomplete-id1").val("");
        }
    });
}

function has_person_autocomplete_set_1() {
    var autocomplete_input_id = "#todo-group-student-search-autocomplete1";
    var last_id = null;
    var last_label = null;
    var input = $(autocomplete_input_id)[0];
    $(autocomplete_input_id).autocomplete({
        source: function (data, callback) {
            $.getJSON(
                "/admin/contacts3/find_contact", {
                    term: $(input).val(),
                },
                callback
            );
        },
        open: function () {

        },
        select: function (event, ui) {
            $("#todo-group-student-search-autocomplete-id1").val(ui.item.id);
        }
    });
}

function has_schedule_autocomplete_set()
{
    var last_id = null;
    var last_label = null;
    var input = $("#todo-schedule-search-autocomplete")[0];

    $("#todo-schedule-search-autocomplete").autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $("#todo-schedule-search-autocomplete-id").val("");
            }

            $.getJSON(
                "/admin/courses/find_schedule", {
                    location: "",
                    category: "",
                    year: "",
                    term: $("#todo-schedule-search-autocomplete").val(),
                    payment_type: 1
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#todo-schedule-search-autocomplete-id").val("");
            }
        },
        select: function (event, ui) {
            $("#todo-schedule-search-autocomplete-id").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#todo-schedule-search-autocomplete-id").val("");
        }
    });
}

function todo_course_add()
{
    var id = $("#todo-course-search-autocomplete-id").val();
    var label = $("#todo-course-search-autocomplete").val();

    var $row = $("#todo-course-template").clone();
    $row.removeClass("hidden").removeAttr('id');
    $row.find(".course-name").html(label);
    $row.find(".course-id").val(id);
    $row.find(".remove").on("click", todo_course_remove);

    $("#todo-courses-list").append($row);

    $("#todo-course-search-autocomplete-id").val("");
    $("#todo-course-search-autocomplete").val("").trigger("change");
}

function todo_course_remove()
{
    $(this).parents(".todo-course").remove();
}

function todo_schedule_add()
{
    var id = $("#todo-schedule-search-autocomplete-id1").val();
    var label = $("#todo-schedule-search-autocomplete1").val();

    var $row = $("#todo-schedule-template").clone();
    $row.removeClass("hidden").removeAttr('id');
    $row.find(".schedule-name").html(label);
    $row.find(".schedule-id").val(id);
    $row.find(".remove").on("click", todo_schedule_remove);

    $("#todo-schedules-list").append($row);

    $("#todo-schedule-search-autocomplete-id1").val("");
    $("#todo-schedule-search-autocomplete1").val("").trigger("change");

    add_students(id);
}

function todo_schedule_remove()
{
    remove_students($(this).parents(".todo-schedule").find(".schedule-id").val());
    $(this).parents(".todo-schedule").remove();
}

function has_subject_autocomplete_set()
{
    var last_id = null;
    var last_label = null;
    var input = $("#todo-subject-search-autocomplete1")[0];

    $("#todo-subject-search-autocomplete1").autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $("#todo-subject-search-autocomplete11").val("");
            }

            $.getJSON(
                "/admin/courses/find_subject", {
                    location: "",
                    category: "",
                    year: "",
                    payment_type: 1,
                    term: $("#todo-subject-search-autocomplete1").val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $("#todo-subject-search-autocomplete-id1").val("");
            }
        },
        select: function (event, ui) {
            $("#todo-subject-search-autocomplete-id1").val(ui.item.id);
            last_label = ui.item.value;
            last_id = ui.item.id;
        }
    });

    $(input).on('blur', function(){
        if (input.value == '') {
            $("#todo-subject-search-autocomplete-id1").val("");
        }
    });
}

var $todo_subject_template = $("#todo-subject-template");
$todo_subject_template.remove();

function todo_subject_add()
{
    var id = $("#todo-subject-search-autocomplete-id1").val();
    var label = $("#todo-subject-search-autocomplete1").val();

    var $row = $todo_subject_template.clone();
    $row.removeClass("hidden").removeAttr('id');
    $row.find(".subject-name").html(label);
    $row.find(".subject-id").val(id);
    $row.find(".remove").on("click", todo_subject_remove);

    $("#todo-subjects-list").append($row);

    $("#todo-subject-search-autocomplete-id1").val("");
    $("#todo-subject-search-autocomplete1").val("").trigger("change");
}

function todo_subject_remove()
{
    $(this).parents(".todo-subject").remove();
}

function related_to_autocomplete() {
    var autocomplete_input_id = "#related_to";
    var last_id = null;
    var last_label = null;
    var input = $(autocomplete_input_id)[0];
    $(autocomplete_input_id).autocomplete({
        source: function (data, callback) {
            $.getJSON(
                "/admin/todos/ajax_autocomplete_regarding", {
                    term: $(input).val(),
                    regarding_id: $("#related_to_id").val()
                },
                callback
            );
        },
        open: function () {

        },
        select: function (event, ui) {
            $("#related_to_value").val(ui.item.id);
        }
    });
}

var $todo_result_template = $(".todo-result.hidden");
$todo_result_template.remove();

function show_students()
{
    var schedule_id = $("#todo-schedule-search-autocomplete-id").val();
    add_students(schedule_id);
}

function add_students(schedule_id)
{
    $.post(
        "/admin/courses/get_students",
        {
            schedule_id: schedule_id
        },
        function (data) {
            var row_index = parseInt($("#todo-results").data("student-index"));
            for (var i in data) {
                var $tr = $todo_result_template.clone();
                $tr.removeClass("hidden");
                $tr.find("span.student_id").html(data[i].student_id);

                $tr.find("input.student_id").val(data[i].student_id);
                $tr.find("input.student_id").attr("name", "result[" + row_index + "][student_id]");
                $tr.find("input.result_id").attr("name", "result[" + row_index + "][id]");

                $tr.find("span.student_name").html(data[i].first_name + " " + data[i].last_name);
                $tr.find("span.course").html(data[i].course);
                $tr.find("input.schedule_id").attr("name", "result[" + row_index + "][schedule_id]");
                $tr.find("input.schedule_id").val(schedule_id);

                $tr.find("input.result").attr("name", "result[" + row_index + "][result]");
                $tr.find("select.grade").attr("name", "result[" + row_index + "][grade]");
                $tr.find("input.points").attr("name", "result[" + row_index + "][points]");
                $tr.find("input.comment").attr("name", "result[" + row_index + "][comment]");

                $("#todo-results > tbody").append($tr);
                ++row_index;
            }
        }
    );
}

function remove_students(schedule_id)
{
    schedule_id = parseInt(schedule_id);
    $("#todo-results > tbody > tr input.schedule_id").each(function(){
        if (parseInt(this.value) == schedule_id) {
            $(this).parents("tr").remove();
        }
    });
}

var $role_tr = $("#permissions-table tr.role.hidden");
$role_tr.remove();

function remove_permission()
{
    $(this).parents("tr").remove();
}

function add_permission()
{
    var role = $("#permission-roles option:selected").text();
    var role_id = $("#permission-roles option:selected").val();

    $("#permission-roles").prop("selectedIndex", -1);

    var $tr = $role_tr.clone();
    $tr.removeClass("hidden");
    $tr.find("span.role").html(role);
    $tr.find("input.role_id").val(role_id);
    $tr.find(".delete").on("click", remove_permission);
    $("#permissions-table > tbody").append($tr);
}


$(document).on("ready", function(){
    has_course_autocomplete_set();
    has_schedule_autocomplete_set_1();
    has_person_autocomplete_set_1();
    has_schedule_autocomplete_set();
    has_subject_autocomplete_set();
    related_to_autocomplete();
    todo_category_autocomplete();

    $("#todo-course-add").on("click", todo_course_add);
    $("#todo-courses .remove").on("click", todo_course_remove);

    $("#todo-schedule-add").on("click", todo_schedule_add);
    $("#todo-schedules .remove").on("click", todo_schedule_remove);

    $("#todo-subject-add").on("click", todo_subject_add);
    $("#todo-subjects .remove").on("click", todo_subject_remove);

    $(".datetimepicker.date").datetimepicker({
        defaultDate: new Date(),
        format:'Y-m-d',
        closeOnDateSelect: true,
        timepicker: false
    });

    $(".datetimepicker.time").datetimepicker({
        defaultDate: new Date(),
        format:'H:i',
        step: 15,
        datepicker: false
    });

    $("#show_students").on("click", show_students);

    $("#add-permission").on("click", add_permission);

    $("#permissions-table .delete").on("click", remove_permission);
    $('#delivery-mode').on('change', '[name="delivery_mode"]', function() {
        var delivery_mode = $(this).val();
        if (delivery_mode == 'Online') {
            $('#todo_location').addClass('hidden');
            $('#location_id').attr('disabled', 'disabled');
        } else {
            $('#location_id').removeAttr('disabled');
            $('#todo_location').removeClass('hidden');
        }
    });

    $("#new-location").on("click", function(){$("#location-add-modal").modal();});
});

$('.assignee-selection').on('click', '[name="assignee-type"]', function () {
    $('.todo-type-assignee-section').addClass('hidden');
    $('.examiner-picker').find('select').attr('disabled', 'disabled');
    $('.todo-type-assignee-section[data-todo_assignee_type="' + this.value + '"]').find('.examiner-picker').find('select').removeAttr('disabled').trigger('change');
    $('.todo-type-assignee-section[data-todo_assignee_type="' + this.value + '"]').find('.examiner-picker').find('button').removeAttr('disabled').removeClass('disabled');
    $('.todo-type-assignee-section[data-todo_assignee_type="' + this.value + '"]').removeClass('hidden');
});

$('body').on('click', '.todo-type', function(){
    var value = this.value;
    if (value== 'State-Exam') {
        $('.todo-type-section[data-todo_type="State-Exam"]').removeClass('hidden');
    } else {
        $('.todo-type-section[data-todo_type="State-Exam"]').addClass('hidden');

    }
});

$('.todo-edit-subject_id, .todo-edit-level_id, .result').on('change', function() {
    var $row       = $(this).parents('tr');

    var data = {
        schema_id:  $('#todo-edit-schema_id').val(),
        subject_id: $row.find('.todo-edit-subject_id').val(),
        level_id:   $row.find('.todo-edit-level_id').val(),
        percent:    $row.find(':input.result').val()
    };

    $.ajax({url: '/admin/todos/ajax_calculate_points', data: data}).done(function(result) {
        $row.find(':input.points').val(result.points);
        $row.find(':input.grade').val(result.grade);
    });
});

$('#todo-edit-schema_id').on('change', function() {
    $('.todo-edit-level_id').each(function() {
        $(this).trigger('change');
    });
});

window.subject_image_uploaded = function(filename, path, data, upload_wrapper)
{
    if (data.media_id) {
        // Record the image in the hidden field
        $('#questionnaire-builder-banner_image').val(filename);
        // Set the preview image
        // Open the image editor, so the user can apply the preset
        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            'courses',
            function (response) {
                // Update the preview image
                $('#edit_image_modal').modal('hide');
                $('#form_add_edit_subject-image-preview-wrapper').removeClass('hidden');
            },
            'locked'
        );
    }
};