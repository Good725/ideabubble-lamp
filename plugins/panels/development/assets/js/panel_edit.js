$(document).ready(function(){
    CKEDITOR.replace(
        'panel_text',
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
            width  : '100%',
            height : '300px'
        }
    );

    initDateRangePicker(true);

    $("#btn_delete").click(function(){
        $('#confirm_delete').modal();
    })
    $("#btn_delete_yes").click(function(){
		$('#editor_action').val('delete');
		$("#editor_redirect").val("/admin/panels");
		$("#form_panel_add_edit").submit();
    })

    $("#btn_save").click(function(){
        if (validate_form()) {
            $("#editor_redirect").val("/admin/panels/add_edit_item");
            $("#form_panel_add_edit").submit();
        }
    })

	$("#btn_save_exit").click(function(){
        if (validate_form()) {
		    $("#editor_redirect").val("/admin/panels");
		    $("#form_panel_add_edit").submit();
        }
	})

    $(document).scroll(function(e){
        actionBarScroller();
    });

    $(document).resize(function(){
        actionBarScroller();
    });

    actionBarScroller();

    $('#panel_image').find('option[value="browse"]').add_existing_image_option('Panels');
    $(document).on('click', '#cropped_image_done_btn', function()
    {
        var image_path = $(this).parents('.modal').find('.modal-body img').attr('src');
        image_path = image_path.replace('/panels/', '/panels/_thumbs_cms/');
        image_path = image_path.substr(0, (image_path+'?').indexOf('?')); // remove timestamp
        var image_name = image_path.split('/').pop();

        $('#panel_image').append('<option value="' + image_name + '" data-thumb="' + image_path + '">' + image_name + '</option>');
        $('#panel_image').val(image_name).change();
    });

	$(document).on(':ib-browse-image-selected', '.image_thumb', function()
	{
		// Get the path to the uploaded image, which has no preset applied
		var src   = this.querySelector('img').src.replace('/_thumbs_cms/', '/');
		var $this = $(this);

		// Open the image editor, using the chosen image and the products preset
		existing_image_editor(
			src,
			'panels',
			function(image)
			{
				// Set the uploaded image as the panel's image
				var filepath = (typeof image == 'string') ? image : image.file;
				var filename = filepath.substring(filepath.lastIndexOf('/') + 1);
				var thumbnail = '/'+(filepath.replace('/panels/', '/panels/_thumbs_cms/'));

				$('#panel_image')
					.append('<option value="'+filename+'" data-thumb="'+thumbnail+'">'+filename+'</option>')
					.val(filename)
					.trigger('change');

				// Dismiss the editor
				$('#edit_image_modal').modal('hide');
			}
		);
	});

	$('#panelStaticDiv').find('#file_previews').hide();

	$(document).on(':ib-fileuploaded', '.upload_item', function()
	{
		// Get the path to the uploaded image, which has no preset applied
		var src = this.querySelector('img').src.replace('/_thumbs_cms/', '/');

		// Open the image editor, using the chosen image and the products preset
		existing_image_editor(
			src,
			'panels',
			function(image)
			{
				// Set the uploaded image as the panel's image
				var filepath = (typeof image == 'string') ? image : image.file;
				var filename = filepath.substring(filepath.lastIndexOf('/') + 1);
				var thumbnail = '/'+(filepath.replace('/panels/', '/panels/_thumbs_cms/'));

				$('#panel_image')
					.append('<option value="'+filename+'" data-thumb="'+thumbnail+'">'+filename+'</option>')
					.val(filename)
					.trigger('change');

				// Dismiss the editor
				$('#edit_image_modal').modal('hide');
			}
		);
	});


});

var multi_upload_images = '';

function multi_upload_and_add(i)
{
    if (i != 0 && i != multi_upload_images.length)
    {
        add_image($('#image_editor_filename').val());
    }

    if (i < multi_upload_images.length)
    {
        var filepath = $(multi_upload_images[i]).find('img')[0].src.replace('/_thumbs_cms/', '/');
        var filename = filepath.split('/').pop().replace('%20', '');
        var ext      = filename.split('.').pop();

		var title = $('#panel_title').val();

        if (title != '')
		{
            filename = title + '.' + ext;

            $.ajax({
                'url': '/admin/media/ajax_get_filename_suggestion',
                'type': 'POST',
                'data': {
                    'name': title,
                    'ext': ext,
                    'directory': 'panels'
                },
                'dataType': 'json'
            }).success(function (result) {
                filename = result;
                $('#image_editor_filename').val(filename);
                $('#edit_image_source').html(filepath);
                prepare_editor();
                $('#edit_image_modal').modal();

                $('#cropped_image_done_btn').attr('onclick', 'add_new_panel_image_to_list(filename, filepath);multi_upload_and_add('+i+'+1)');
            });
        }

    }
    else
    {
        multi_upload_images = '';
    }
}

function add_new_panel_image_to_list(filename, filepath)
{
	if (multi_upload_images == '') // "Upload Images" button does its own thing
	{
		if ( ! editing_image)
		{
			$('#panel_image')
				.append('<option value="'+filename+'" data-thumb="'+filepath+'">'+filename+'</option>')
				.val(filename)
				.trigger('change');
		}
		else
		{
			editing_image = false;
		}
	}
}

function actionBarScroller() {
    var viewportHeight = window.innerHeight ? window.innerHeight : $(window).height();
    var fromTop = $(window).scrollTop();
    var howFar = viewportHeight + fromTop;
    var pos = $('.floating-nav-marker').position().top;
    stuff = 'AP:'+pos+' PB:'+howFar;
    if (pos > howFar) {
        $('#ActionMenu').addClass("floatingMenu");
        $('#ActionMenu').removeClass("fixedMenu");
    } else {
        $('#ActionMenu').addClass("fixedMenu");
        $('#ActionMenu').removeClass("floatingMenu");
    }
}

$('#panel_image').on('change', function() {
	var selected_element = $('#panel_image :selected');
	var selected_image = selected_element.val();
	if (selected_image != 0 && selected_image != 'browse' && selected_image != 'upload')
    {
		var image_thumb = selected_element.data('thumb');
		$('#imagePreview').html('<img src="'+image_thumb+'" alt="'+selected_image+'"/>');
	}
	else if(selected_image == 0) $('#imagePreview').html('');
});


function linkChange(link_id) {
	//For External URLs
	if(link_id === '0'){
		$('#panel_link_url').show();
	//Internal Links, does not need this field
	}else {
		$('#panel_link_url').val("");
		$('#panel_link_url').hide();
	}
 }//end of function

function validate_form() {
    var ok = true;

    if ( ($("#panel_title").val()).length <= 0 || $("#panel_position").val() == '0' ) {
        $('#validation_failed').modal();

        $('#btn_review').click(function() {
            $('#validation_failed').modal('hide');
        })

        ok = false;
    }

    return ok;
}

function initDateRangePicker(date){
    var from,
        to;
    if (date)
    {
        from = $('#panel_date_publish');
        to = $('#panel_date_remove');
    }

    Date.parseDate = function( input, format ){
        return moment(input,format).toDate();
    };
    Date.prototype.dateFormat = function( format ){
        return moment(this).format(format);
    };
    if (date) {
        from.datetimepicker({
            format: 'YYYY-MM-DD H:mm',
            formatTime: 'H:mm',
            formatDate: 'YYYY-MM-DD',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: to.val() ? to.val() : false
                })
            }
        });
        to.datetimepicker({
            format: 'YYYY-MM-DD H:mm',
            formatTime: 'H:mm',
            formatDate: 'YYYY-MM-DD',
            onShow: function (ct) {
                this.setOptions({
                    minDate: from.val() ? from.val() : false
                })
            }
        });
    }
}