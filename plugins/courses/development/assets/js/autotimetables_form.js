$(document).ready(function() {
    // Add year button
    $('#add_year_btn').on('click', function(){
        var year = $('#att_year_picker').find('option:selected');
        if (year.val() != '0' || year.val() == '')
        {
            $('#att_year_names').append(year.text()+'\n');
            $('#att_year').find('option[value='+year.val()+']').prop('selected', true);
        }

    });

    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    // CKEditor Configuration
    CKEDITOR.replace('att_description', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList', '-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['Image', 'Table'],
                    ['Link','Unlink','Anchor','-','SpecialChar'],
                    [ 'Maximize', 'ShowBlocks']
                ],

            // Editor width
            width   : '100%'

        }
    );

    $("#form_add_edit_autotimetable").validate();
    $("#publish_yes").click(function(ev){
        ev.preventDefault();
        $("#publish").val('1');
    });
    $("#publish_no").click(function(ev){
        ev.preventDefault();
        $("#publish").val('0');
    });
    $("#btn_delete").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_autotimetable', {id: id}, function (data) {
            if (data.redirect !== '' || data.redirect !== undefined) {
                window.location = data.redirect;
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");


    });
});