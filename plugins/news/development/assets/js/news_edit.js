$(document).ready(function(){
    CKEDITOR.replace(
        'item_content',
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
    CKEDITOR.replace(
        'footer_editor',
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
            width: '100%'
        }
    );

    $("#btn_delete").click(function(){
        $('#confirm_delete').modal();
    });

    $("#btn_delete_yes").click(function(){
		$('#editor_action').val('delete');
		$("#editor_redirect").val("/admin/news");
		$("#form_news_story_add_edit").submit();
    });

    $("#btn_save").click(function(ev) {
		ev.preventDefault();
        if (validate_form()) {
            $("#editor_redirect").val("/admin/news/add_edit_item");
		    $("#form_news_story_add_edit").submit();
        }
    });
	$("#btn_save_exit").click(function(ev){
		ev.preventDefault();
        if (validate_form()) {
		    $("#editor_redirect").val("/admin/news");
		    $("#form_news_story_add_edit").submit();
        }
	});


    $(document).scroll(function(e){
        actionBarScroller();
    });

    $(document).resize(function(){
        actionBarScroller();
    });

    actionBarScroller();

	// The date selected by default
	var event_date = document.getElementById('item_event_date').value;
	event_date = event_date.substr(0, 10); // remove the hours, minutes, seconds

    $("#item_event_date_calendar")
		.datepicker({
			format: 'yyyy-mm-dd'
		})
		.on('changeDate', function(date)
			{
				// Set the date in a hidden field when one is selected on the calendar
				$('#item_event_date')
					.val(moment(date.date).format('DD-MM-YYYY'))
					.trigger('change');
			})
		.datepicker('update', event_date);

    $('#item_date_publish').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        format: 'dd-mm-yyyy'
    });

    $('#item_date_remove').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        format: 'dd-mm-yyyy'
    });

	$('#item_event_date').on('change', function()
	{
		var iso_date = this.value.split('-').reverse().join('-');
		var id = document.getElementById('edit-news-item_id').value;
		$.ajax({url: '/admin/news/ajax_get_news', dataType: 'json', data: {event_date: iso_date, current_id: id}}).done(function(result)
			{
				$('#edit-news-selected-date').text(moment(iso_date).format('D MMMM YYYY'));

				if (!result.success) {
					alert(result.message);
				}
				else
				{
					var $clone, html = '';
					for (var i = 0; i < result.items.length; i++)
					{
						$clone = $('#edit-news-selected-date-event-template').clone();
						$clone.find('.edit-news-existing_item-title').text(result.items[i].title);
						$clone.find('.edit-news-existing_item-link').attr('href', '/admin/news/add_edit_item/'+result.items[i].id);
						html += $clone.html();
						$clone.remove();
					}
					$('#edit-news-selected-date-events').html(html);
				}
			});
	});


    // $('#item_event_date').on("change", eventDateChanged);
    $('#item_date_publish').on("change", startDateChanged);
    $('#item_date_remove').on("change", endDateChanged);

    $("#shared_with").on("change", function(){
        if (this.value == "0") {
            $("#share_with_groups_wrapper").css("display", "none");
        } else {
            $("#share_with_groups_wrapper").css("display", "");
        }
    });

	uploader_ready();
});

function eventDateChanged()
{
    var date = $(this).datepicker('getDate');

    $('#item_date_publish').off("change", startDateChanged);
    $('#item_date_publish').datepicker("remove");
    $('#item_date_publish').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        endDate: date,
        format: 'dd-mm-yyyy'
    });
    $('#item_date_publish').on("change", startDateChanged);

}

function startDateChanged()
{
    var date = $(this).datepicker('getDate');

    $('#item_date_publish').off("change", startDateChanged);
    $('#item_date_remove').off("change", endDateChanged);
    $('#item_date_remove').datepicker("remove");
    $('#item_date_remove').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        startDate: date,
        format: 'dd-mm-yyyy'
    });
    $('#item_date_remove').on("change", endDateChanged);
}

function endDateChanged()
{
    var date = $(this).datepicker('getDate');

    $('#item_date_publish').off("change", startDateChanged);
    $('#item_date_remove').off("change", endDateChanged);
    $('#item_date_publish').datepicker("remove");
    $('#item_date_publish').datepicker({
        autoclose: true,
        orientation: "auto bottom",
        endDate: date,
        format: 'dd-mm-yyyy'
    });

    $('#item_date_publish').on("change", startDateChanged);
    $('#item_date_remove').on("change", endDateChanged);
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


function imageChange(img_selector_id) {
	var selected_element = $('#'+img_selector_id+' :selected');
	var selected_image = selected_element.val();
	if(selected_image != 0)
    {
		var image_thumb = selected_element.data('thumb');
		$('#imagePreview').html('<img src="'+image_thumb+'" alt="'+selected_image+'"/>');
        $('#image-preview-wrapper').removeClass('hidden');
        $('#item_alt_text_row').show();
        $('#item_title_text_row').show();
    }
	else
    {
        $('#image-preview-wrapper').addClass('hidden');
        $('#imagePreview').html('');
        $('#item_alt_text_row').hide();
        $('#item_title_text_row').hide();
        $('#item_alt_text').val('');
        $('#item_title_text').val('');
    }
}//end of function

function validate_form() {
    var ok = true;

    if ( ($("#item_title").val()).length <= 0 || $("#item_category_id").val() == '0' ) {
        $('#validation_failed').modal();

        $('#btn_review').click(function() {
            $('#validation_failed').modal('hide');
        });

        ok = false;
    }

    return ok;
}

// Upload an image
window.news_image_uploaded = function(filename, path, data, upload_wrapper)
{
	if (data.media_id) {
		// Record the image in the hidden field
		$('#edit-news-image_id').val(filename);
		// Set the preview image
		var $preview = $('#imagePreview').find('img');
		$preview.prop('src', window.location.protocol + '//' + window.location.host + "" + path).removeClass('hidden');

		// Open the image editor, so the user can apply the preset
		existing_image_editor(
			window.location.protocol + '//' + window.location.host + "" + path,
			'news',
			function (response) {
				// Update the preview image
				$preview.prop('src', $preview.prop('src').replace('/content/_thumbs_cms/', '/news/_thumbs/'));
				$('#edit_image_modal').modal('hide');
                $('#image-preview-wrapper').removeClass('hidden');

				$('#item_alt_text_row').show();
				$('#item_title_text_row').show();
			},
			'locked'
		);
	}
};

$('#image-preview-remove').on('click', function() {
    $('#image-preview-wrapper').addClass('hidden');
    $('#edit-news-image_id').val('');

    $('#item_alt_text_row').hide();
    $('#item_title_text_row').hide();
});

// Select an image with the "Browse" button
$(document).on(':ib-browse-image-selected', '.image_thumb', function()
{
    var img         = this.querySelector('img');
	var path        = img.src.replace(/^.*\/\/[^\/]+/, ''); // URL, minus the domain
	var last_slash  = img.src.lastIndexOf('/');
	var filename    = img.src.substring(last_slash + 1); // Portion of the URL, after the last "/"
	var data        = {media_id: $(img).parent().data('id')};

	news_image_uploaded(filename, path, data);
});