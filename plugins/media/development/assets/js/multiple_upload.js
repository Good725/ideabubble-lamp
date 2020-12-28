$(document).ready(function()
{
    if ($(".multiple_upload_wrapper").length > 0)
    {
        uploader_ready();
    }

    $(".close-dialog").on("click", function (){
        if (window != window.top) {
            var msg = {};
            msg.action = "close-dialog";
            window.top.postMessage(JSON.stringify(msg), "*");
        }
    });
});

var files_to_upload = null;

if ($('#upload_files_modal').size() == 0)
{
    $('body').append('<div id="upload_files_modal" class="modal fade">'+
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">'+
        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
        '<h3>Upload Images</h3>'+
        '</div>'+
        '<div class="modal-body"></div>'+
        '<div class="modal-footer">'+
        '<a href="#" class="btn" id="multiple_upload_done_btn" data-dismiss="modal">Done</a>'+
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>');
}

// Call the uploader from particular buttons
$(document).on('click', '#multi_upload_button, .cke_button__uploadbutton', function()
{
    var upload_modal     = $('#upload_files_modal');
	var accepted_formats = $(this).attr('data-accept');
	var url              = '/admin/media/multiple_upload';
    var onsuccess        = $(this).data("onsuccess")
    var preset           = $(this).data("preset");

	if (accepted_formats)
	{
		url += '?formats='+accepted_formats;
	}

    upload_modal.find('.modal-body').load(url+' #multiple_upload_wrapper', function()
    {
        uploader_ready(preset, onsuccess);
        upload_modal.modal();
    });
});

// Call the uploader from a select list option
$('option.image_uploader').parents('select').on('change', function()
{
    if (this.value == $(this).find('.image_uploader').val())
    {
        var upload_modal = $('#upload_files_modal');

        upload_modal.find('.modal-body').load('/admin/media/multiple_upload #multiple_upload_wrapper', function()
        {
            uploader_ready();
            $('#preset_selector, [for="preset_selector"]').hide();
            upload_modal.modal();
        });
    }
});

function uploader_ready(preset, onsuccess)
{
    $(".drag_and_drop_area").each(function(){
        if (this.uploader_has_been_set) {
            return;
        } else {
            this.uploader_has_been_set = true;
        }
        var file_upload_button = $(this).find('.file_upload_button');
        var drop_area          = this;
        var multiple_upload_wrapper = $(this).parents('.multiple_upload_wrapper');
        var dnd_supported      = 'draggable' in document.createElement('span');
        var accepted_formats   = file_upload_button.find('[type="file"]').attr('accept') ? file_upload_button.find('[type="file"]').attr('accept').split(',') : [];
        var accept_all         = (accepted_formats.length == 0);
        var default_preset     = $(this).data("default-preset");
        var default_onsuccess  = $(this).data("onsuccess");
        var display_preset_modal = $(this).data("presetmodal");
        var check_duplicate    = $(this).data("check-duplicate");

        if (default_preset) {
            multiple_upload_wrapper.find(".preset_selector option").each(function(){
                if (this.innerHTML == default_preset) {
                    this.selected = true;
                }
            });
        }

        if (preset) {
            multiple_upload_wrapper.find(".preset_selector option").each(function(){
                if (this.innerHTML == preset) {
                    this.selected = true;
                }
            });
        }

        if (dnd_supported)
        {
            drop_area.ondragover = function () { $(this).addClass('hover');    return false; };
            drop_area.ondragend  = function () { $(this).removeClass('hover'); return false; };
            drop_area.ondrop     = function (e)
            {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('hover');

                files_to_upload = e.dataTransfer.files || e.target_files;

                var image_types = ['image/jpeg', 'image/png', 'image/gif','image/svg+xml', 'image/ico', 'image/vnd.microsoft.icon', 'image/x-icon'];

                var has_image = false;
                var extension;
                var valid = true;
                $.each(files_to_upload, function(key, value)
                {
                    extension = value.name.split('.').pop();
                    if (valid && ! accept_all && (accepted_formats.indexOf(extension) == -1 && accepted_formats.indexOf("." + extension) == -1)) {
                        valid = false;
                        multiple_upload_wrapper.find('.file_previews').append(
                            '<div class="upload_item error" data-name="'+files_to_upload[key]['name']+'">'+
                            '   <div class="upload_error_message">Error: &quot;'+extension+'&quot; files cannot be uploaded here.</div>'+
                            '   <div class="upload_name">'+files_to_upload[key]['name']+'</div>'+
                            '</div>')
                    }
                    else if (image_types.indexOf(value.type) != -1) {
                        has_image = true;
                    }
                });
                if ( ! valid)
                {
                    return false;
                }
                else if (has_image && display_preset_modal != "no") {
                    multiple_upload_wrapper.find('.select_preset_modal').modal();
                } else {
                    image_upload(files_to_upload);
                }
            }
        }
        else
        {
            $(this).find('.dnd_notice').html('Your browser does not support drag and drop. Please use the button below.');
        }

        file_upload_button.find('button').click(function()
        {
            $(this).next().click();
        });

        file_upload_button.find('input[type="file"]').on('change', function(event)
        {
            event.stopPropagation();
            event.preventDefault();
            var image_types = ['image/jpeg', 'image/png', 'image/gif','image/svg+xml', 'image/ico', 'image/vnd.microsoft.icon', 'image/x-icon'];

            files_to_upload = this.files;

            var has_image = false;
            $.each(files_to_upload, function(key, value){
                if (image_types.indexOf(value.type) != -1){
                    has_image = true;
                }
            });

            if (has_image && display_preset_modal != "no") {
                multiple_upload_wrapper.find('.select_preset_modal').modal();
            } else {
                image_upload(files_to_upload);
            }
        });

        $(multiple_upload_wrapper).find('.preset_selector_done_btn').on('click', function()
        {
            multiple_upload_wrapper.find('.select_preset_modal').modal('hide');
            image_upload(files_to_upload);
        });

        function image_upload(files)
        {
            var image_types = ['image/jpeg', 'image/png', 'image/gif','image/svg+xml', 'image/ico', 'image/vnd.microsoft.icon', 'image/x-icon'];
            // Add rows with progress bars

            if ( ! document.getElementById('media-list-photos'))
            {
                var $file_previews = multiple_upload_wrapper.find('.file_previews');
                $.each(files, function(key, value)
                {
                    $file_previews.append(
                        '<div class="upload_item" data-name="'+files[key]['name']+'">'+
                        '   <div class="uploading_notice">Uploading</div>'+
                        '   <div class="upload_name">'+files[key]['name']+'</div>'+
                        '</div>');
                });
            }

            $.each(files, function(key, value)
            {
                var error_message   = '';
                var data            = new FormData();
                var preset_selector = multiple_upload_wrapper.find('select.preset_selector');
                var preset_vars     = ['title', 'directory', 'height_large', 'width_large', 'action_large',
                    'thumb', 'height_thumb', 'width_thumb', 'action_thumb'];

                data.append('check_duplicate', check_duplicate);

                data.append(key, value);
                if (image_types.indexOf(value.type) != -1){
                    data.append('preset_id', preset_selector.val());
                    for (var p = 0; p < preset_vars.length; p++) {
                        var preset = preset_selector.find('option:selected');
                        data.append('preset_'+preset_vars[p], preset.data(preset_vars[p]));
                    }
                } else {
                    data.append('preset_id', 0);
                }

                $.ajax(
                    {
                        'url'        : '/admin/media/ajax_upload',
                        'type'       : 'POST',
                        'data'       : data,
                        'cache'      : false,
                        'dataType'   : 'json',
                        'async'      : true,
                        'processData': false,
                        'contentType': false,
                        'success'    : function(data, textStatus, jqXHR)
                        {
                            if (typeof data.error == 'undefined' && data.errors.length == 0)
                            {
                                if (data.errors.length != 0)
                                {
                                    for (var j = 0; j < data.errors.length; j++)
                                    {
                                        error_message += data.errors[j]+'<br />';
                                    }
                                }
                                else if (data.files.length < 1)
                                {
                                    upload_error(value, 'Image not found');
                                }
                                else if (document.getElementById('media-list-photos'))
                                {
                                    $('#media-list-photos').dataTable().fnDraw();
                                }
                                else
                                {
                                    var original_filename, upload_item;
                                    // Add thumbnail replace progress bar with details button
                                    for (var i= 0; i < data.files.length; i++)
                                    {
                                        original_filename = data.original_filenames[i].substr(data.original_filenames[i].lastIndexOf('/')+1);
                                        upload_item = $('.upload_item[data-name="'+original_filename+'"]').last();
                                        upload_item.prepend('<div class="uploaded_image">' +
                                            '<a href="'+data['shared_media']+'/media/photos/content/'+data.files[i]+'" target="_blank">' +
                                            '<img src="'+data['shared_media']+'/media/photos/content/_thumbs_cms/'+data.files[i]+'" alt="thumbnail" />' +
                                            '</a>' +
                                            '</div>');
                                        if (default_onsuccess && !onsuccess) {
                                            window[default_onsuccess](data.files[i], data['shared_media']+'/media/photos/content/_thumbs_cms/'+data.files[i], data, multiple_upload_wrapper);
                                        }
                                        if (onsuccess) {
                                            window[onsuccess](data.files[i], data['shared_media']+'/media/photos/content/_thumbs_cms/'+data.files[i], data, multiple_upload_wrapper);
                                        }

                                        upload_item.find('.uploading_notice').html('Upload successful').attr('class', 'uploaded_notice');
                                        // .html('[Details]')
                                        // .attr('class', 'details_button');
                                        upload_item.trigger(':ib-fileuploaded');
                                    }
                                }
                            }
                            else
                            {
                                var message = (typeof data.error != 'undefined') ? data.error : data.errors[0];
                                upload_error(value, message);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            upload_error(value, errorThrown);
                        }
                    });

            });
            files_to_upload = null;
            file_upload_button.find('input[type="file"]').val('');
        }

        function upload_error(image, error_message)
        {
            var filename    = image['name'].substr(image['name'].lastIndexOf('/')+1);
            var upload_item = $('.upload_item[data-name="'+filename+'"]').last();
            upload_item.addClass('error');
            upload_item.find('.uploading_notice, .uploaded_notice')
                .html('Error: '+error_message)
                .attr('class', 'upload_error_message');
            alert(error_message);
        }

        multiple_upload_wrapper.find('.file_previews').on('click','.details_button', function()
        {
            var row = $(this).parents('.upload_item');
            if ($(this).hasClass('showing'))
            {
                row.find('.upload_details').remove();
                $(this).removeClass('showing');
            }
            else
            {
                $.ajax(
                    {
                        'url'        : '/admin/media/ajax_show_upload_details',
                        'type'       : 'POST',
                        'data'       : {
                            'image'      : $(this).parents('.upload_item').data('name')
                        },
                        'dataType'   : 'json'
                    }).done(
                    function(data)
                    {
                        row.append(data);
                    });

                $(this).addClass('showing');
            }

        });
    });
}