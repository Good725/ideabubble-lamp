$(document).ready(function() {
    $(".multipleselect").multiselect();
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    $("#form_add_edit_subject").validate();

});

window.subject_image_uploaded = function(filename, path, data, upload_wrapper)
{
    if (data.media_id) {
        // Record the image in the hidden field
        $('#form_add_edit_subject-image').val(filename);
        // Set the preview image
        var $preview = $('#form_add_edit_subject-image-preview').find('img');
        $preview.prop('src', window.location.protocol + '//' + window.location.host + "" + path).removeClass('hidden');

        // Open the image editor, so the user can apply the preset
        existing_image_editor(
            window.location.protocol + '//' + window.location.host + "" + path,
            'courses',
            function (response) {
                // Update the preview image
                $preview.prop('src', $preview.prop('src').replace('/content/_thumbs_cms/', '/courses/'));
                $('#edit_image_modal').modal('hide');
                $('#form_add_edit_subject-image-preview-wrapper').removeClass('hidden');
            },
            'locked'
        );
    }
};

$(document).on(':ib-browse-image-selected', '.image_thumb', function()
{
    var img         = this.querySelector('img');
    var path        = img.src.replace(/^.*\/\/[^\/]+/, ''); // URL, minus the domain
    var last_slash  = img.src.lastIndexOf('/');
    var filename    = img.src.substring(last_slash + 1); // Portion of the URL, after the last "/"
    var data        = {media_id: $(img).parent().data('id')};

    subject_image_uploaded(filename, path, data);
});