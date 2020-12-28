$('body').append('<div id="image_editor_holder"></div><div id="browse_images_holder"></div>');
$('#image_editor_holder').load('/admin/media/image_editor #image_editor_wrapper');

$.fn.add_existing_image_button = function(preset_name, open_editor)
{
	open_editor = (typeof open_editor == 'undefined') ? true : open_editor;
    $(this).click(function()
    {
        open_image_selector(preset_name, open_editor);
    });
};

$.fn.add_existing_image_option = function(preset_name)
{
    var upload_option = this;
    $(this).parent('select').on('change', function()
    {
        if (this.value == upload_option.val())
        {
            open_image_selector(preset_name)
        }
    });
};

$(document).on('click', '.upload_by_media', function() {
	var directory   = $(this).data('directory');
	var open_editor = (directory != 'content');
	open_image_selector(directory, open_editor);
});

function open_image_selector(preset_name, open_editor)
{
    open_editor = (typeof open_editor == 'undefined') ? true : open_editor;

    var location = (!open_editor || preset_name == 'videos' || preset_name == 'audios' || preset_name == 'docs') ? preset_name : 'content';
    $('#browse_images_holder').load('/admin/media/browse_images #browse_images_wrapper', {location: location}, function()
    {
        var browse_modal = $('#browse_files_modal');
        browse_modal.modal();

        $('.browse-images-list li').click(function() {
            // create an event
            $(this).trigger(':ib-browse-image-selected', $(this).data('id'));
            // dismiss the browse modal
            browse_modal.modal('hide');
        });
    });
}

var imageSaveCallback = null;
function imageSaveListen(event)
{
    var data = JSON.parse(event.data);
    if (data.image) {
        if (imageSaveCallback) {
            imageSaveCallback(data.image);
        } else {
            window.top.location.reload();
        }
    }
}

window.addEventListener("message", imageSaveListen, false);

function existing_image_editor(filepath, preset, callback, lock_preset)
{
    if (callback) {
        imageSaveCallback = callback;
    } else {
        imageSaveCallback = null;
    }
	lock_preset   = (typeof lock_preset != 'undefined' && lock_preset) ? 1 : 0;
    filepath      = filepath.substr(0, (filepath+'?').indexOf('?')); // remove timestamps
    var modal     = $('#edit_image_modal');
    var filename  = filepath.split('/').pop().replace(/%20/g, ' ');
    var directory = filepath.substring(filepath.indexOf('/photos/')+8, filepath.indexOf('/_thumbs')).replace(/^\/|\/$/g, '');
    filepath = filepath.replace('/_thumbs_cms/', '/');

	if (typeof preset == 'undefined' || preset == '' || preset == 'undefined')
	{
		preset = directory;
	}

    $("#image-edit-frame").prop("src", "/admin/media/image_edit_frame?image=" + encodeURIComponent(filepath) + "&preset=" + preset + '&lock_preset=' + lock_preset);

	modal.css('opacity', 0);
    modal.modal();

	setTimeout(function()
	{
		modal.css('opacity', '');
	}, 1000);
}

$.fn.image_editor = function()
{
    prepare_editor();
};


// Force any instances of the image on the page to show the newest version, after it has been edited
$(document).on('click', '#cropped_image_done_btn', function()
{
    var image_with_timestamp    = $(this).parents('.modal').find('.modal-body > img').attr('src');
    var image_without_timestamp = image_with_timestamp.substr(0, (image_with_timestamp+'?').indexOf('?'));

    $('[src*="'+image_without_timestamp.split('/').pop()+'"]').each(function()
    {
        var old_src = $(this).attr('src');
        $(this).attr('src', old_src.substr(0, (old_src+'?').indexOf('?')) + '?' + new Date().getTime());

    });
});

$(document).on('click', '#edit_image_modal .save_btn', function(ev)
{
    ev.stopPropagation();
    var msg = {};
    msg.save = true;

	if (window.frames["image-edit-frame"].contentWindow)
	{
		window.frames["image-edit-frame"].contentWindow.postMessage(JSON.stringify(msg), "*");
	}
	else
	{
		document.getElementById("image-edit-frame").contentWindow.postMessage(JSON.stringify(msg), "*");
	}

});

// Filter results with the searchbar
$(document).on('keyup', '#browse-images-search', function()
{
	var search_term = this.value;
	var $modal = $(this).parents('.modal');

	// Show all images
	$modal.find('.image_thumb').show();

	// Loop through filenames
	$modal.find('.filename').each(function()
	{
		// If the filename doesn't contain the search term, hide the image
		if (this.innerHTML.indexOf(search_term) == -1)
		{
			$(this).parents('.image_thumb').hide();
		}
	});
});
