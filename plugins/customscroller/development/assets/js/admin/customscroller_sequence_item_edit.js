$(document).ready(
	function(){
		// Set the HTML TEXTAREA to a CKEDITOR
		CKEDITOR.replace(
			'sequence_item_html',
			{
				// CUSTOM Toolbar
				// More Info @ http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
				/*toolbar :
				 [
				 ['Source', '-', 'NewPage'],
				 ['Font', 'FontSize'],
				 ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
				 ['TextColor','BGColor'],
				 ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
				 ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
				 ['Image', 'Table'],
				 ['Link', 'Unlink', 'Anchor'],
				 ['Maximize', 'ShowBlocks']
				 ],*/
				width: '620px',
				height: '200px'
			}
		);

		// Actions for the Buttons Groups: Publish / Un-publish; Yes / No etc
		$('.btn-group .btn').click(
			function(event){
				event.preventDefault();
				$('#'+$(this).parent().data('toggle-name')).val($(this).val());
			}
		);
	}
);

function update_item_editor_image_preview(type)
{
    type = type || '';

    var $preview = $('#image_preview');
    var $selector = $('#sequence_item_image');

    if (type == 'mobile') {
        $preview = $('#mobile_image_preview');
        $selector = $('#sequence_item_mobile_image');
    }

    // Get the details of the current image in the image preview box
    var current_image = $preview.find('img');
    // Get the details fo the new image to be updated to the image preview box
    var new_image_location = $selector.find(':selected').data('image_location');
    var new_image_filename = $selector.find(':selected').data('image_filename');

    // Load / update image preview box only when new_image_location & new_image_filename are passed
    if ($.trim(new_image_location) != '' && $.trim(new_image_filename) != '') {

        // Update the item image location: sequence_item_image_location in the editor view
        $('#sequence_item_image_location').val(new_image_location);

        // There was no image displayed in the image preview box -=> create it
        if (typeof current_image.attr('src') == 'undefined') {
			// Will have to call Server to render an IMG Tag for selected Image
			$.ajax({
				url: '/admin/customscroller/ajax_get_img_html/',
				data: {
					'image_location' : new_image_location,
					'image_filename' : new_image_filename
				},
				type: 'post',
				dataType: 'json'
			}).done(function(result){
						if($.trim(result.err_msg) != '') $('#scroller_item_pop_up_editor .modal-error-area').html(result.err_msg);
						$preview.html(result.img_html);
					});

		// Just update the Image name and Source
		}else{
			// Get ALL Parts of the current_image SRC into Array, which is in the form: 'http://kilmartin.websitecms.dev/media/photos/IMAGE_LOCATION/_thumbs_cms/IMAGE_FILENAME'
			var image_src_parts = current_image.attr('src').split('/');

			// UPDATE the: IMAGE_LOCATION (third-last element) and IMAGE_FILENAME (last element)
			image_src_parts[image_src_parts.length - 3] = new_image_location;
			image_src_parts[image_src_parts.length - 1] = new_image_filename;

			// Update the #image_preview img
			current_image.attr('src', image_src_parts.join('/'));
		}
	}else{
		// No image was selected -=> clear the preview
		$preview.html('');
	}
}

