$(document).ready(function() {
    var item_selected;

    // Listener to toggle contact publish
    $(".publish").click(function() {
        // Save the object (to be used later)
        item_selected = $(this);

        // Remove all the alerts, preventing stacking
        $(".alert").remove();

        $.post('/admin/gallery/ajax_toggle_publish/' + $(item_selected).data('id'))
            .done(function(r) {
                var msg;

                if (r == '1') {
                    img = $(item_selected).children();

                    // Update icon
                    if(img.hasClass('icon-ok'))
                    {
                        img.removeClass('icon-ok');
                        img.addClass('icon-remove');
                    } else {
                        img.removeClass('icon-remove');
                        img.addClass('icon-ok');
                    }

                    // Set the message
                    msg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong>Gallery successfully updated.</div>';
                } else {
                    msg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Unable to complete the requested operation.</div>';
                }

                // Show a notification
                $("#main").prepend(msg);
            })
            .fail(function() {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Cannot connect with the server.</div>');
            })
    });

    // Listener to show the confirmation window when the user wants to delete a contact
    $(".delete").click(function() {
        // Save the object (to be used later)
        item_selected = $(this);

        // Show the confirmation window
        $('#confirm_delete').modal();
    })

    // Listener to delete a contact
    $("#btn_delete_yes").click(function() {
        // Hide the confirmation window
        $('#confirm_delete').modal('hide');

        // Remove all the alerts, preventing stacking
        $(".alert").remove();

        $.post('/admin/gallery/ajax_delete/' + $(item_selected).data('id'))
            .done(function(r) {
                var msg;

                if (r == '1') {
                    // Remove the row // TODO: Use API function to remove the row (see http://www.datatables.net/)
                    item_selected.parent().remove();

                    // Set the message
                    msg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong>Gallery successfully deleted.</div>';
                } else {
                    msg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Unable to complete the requested operation.</div>';
                }

                // Show a notification
                $("#main").prepend(msg);
            })
            .fail(function() {
                $("#main").prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong>Cannot connect with the server.</div>');
            })
    });

    // Listener to select a category (only used in list_gallery.php)
    $("#category").change(function() {
        if ($("#form_list_gallery")) {
            $("#form_list_gallery").submit();
        }
    });

    // Listener to update the image preview
    $("#photo_name").change(function() {
        if ($("#photo_name").val() == 'dummy') {
            $("#picture-preview").attr('alt', '');
            $("#picture-preview").attr('src', '');
        } else {
            $("#picture-preview").attr('alt', $("#photo_name").val());
            $("#picture-preview").attr('src', $("#picture-preview").data('media-root')+ $("#photo_name").val());
        }
    });

    // Listener for the submission of the form
    $("#form_add_edit_gallery").submit(function(e) {
        return validate_form();
    });

    // Trigger the corresponding event to load the picture preview, if any
    $("#photo_name").trigger('change');
});

/**
 * Validate the form.
 * @return {Boolean}
 */
function validate_form() {
    // TODO: Validate fields (see model validation function).

    return true;
}
