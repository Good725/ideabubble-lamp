$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    CKEDITOR.replace('edit_option_description', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['PasteFromWord']
                ],

            // Editor width
            width   : '538px'

        }
    );

    get_group_label();

    $('#image')
        .change(function() {
            var item = $('#image').val();

            if (item == '') {
                $('#image_preview').attr('alt', '').attr('src', '');
                $('#image_preview_container').hide();
            } else {
                $('#image_preview').attr('alt', item).attr('src', media_base_location + item);
                $('#image_preview_container').show();
            }
        });

    $("#form_add_edit_option").validate();

    AJAX.make_get_request('/admin/products/ajax_get_option_media_base_location/', function(r) { media_base_location = r; $('#image').trigger('change'); }, null);

    $("#group").change(function(){
        if(window.location.href.indexOf('edit_option') != -1){
            get_group_label();
        }
    });
});

var media_base_location;
function get_group_label() {
    var group_name = $("#group").val();
    $.post('/admin/products/get_group_label',{group_name: group_name},function(result){
        if(result != "") {
            $("#group_label").prop('readonly',true);
        }
        else if(result == "" && $("#id").val() == "") {
            $("#group_label").prop('readonly',false);
        }
        $("#group_label").val(result);
    });
}