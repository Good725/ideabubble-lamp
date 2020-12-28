$(document).ready(function(){

    //Delete a Media Item (Photo)
	$(".delete_photos").on("click", function(event){
		var click_item = $(this);
		//Get the id from the id attribute
		var item_id = $(this).attr('id');
		var item_id_parts = item_id.split("delete_");
		//Get the Media Type of the Item to delete from the class attribute
		var item_class = $(this).attr('class');
		var item_class_parts = item_class.split('delete_');


	});

    // Image editor
	$('#media-list-photos').on('click', 'tr', function(event)
	{
		if ($(event.target).is(':input, button, a') && ! $(event.target).hasClass('edit-link'))
			return;
		event.preventDefault();
        var image = $(this).find('td:first-child img')[0];
        existing_image_editor(
			image.src,
			$(event.target).data("preset")
		);
    });

    $("[id*='media-list']").on("click", "a[data-id]", function(){
		if (window != window.top) {
            var msg = {};
            msg.mediaId = $(this).data("id");
            msg.url = $(this).data("url");
            msg.thumbUrl = $(this).data("thumb-url");
            msg.filename = $(this).data("filename");
            window.top.postMessage(JSON.stringify(msg), "*");
        }
	});

	$(".close-dialog").on("click", function (){
		if (window != window.top) {
			var msg = {};
			msg.action = "close-dialog";
			window.top.postMessage(JSON.stringify(msg), "*");
		}
	});

	$("#btn_delete_yes").click(function(){
		var btn_delete_yes = this;
		//Remove alerts, prevent stack
		$(".alert").remove();

		//Delete Item
		$.post(
				'/admin/media/ajax_toggle_delete',
				{
					item_id : $(this).data('item-id'),
					item_type : $(this).data('item-type')
				},
				function(response){
					if(response.err_msg != ''){
						$("#main").prepend(response.err_msg);
					}else if(response.success_msg != ''){
						//show Response message
						$("#main").prepend(response.success_msg);
						$(btn_delete_yes.delete_caller).parents('tr').remove();
					}
				},
				'json'
		);

		//Close Dialog box
		$('#confirm_delete').modal('hide');
	});
});

function set_preset_details(preset_selector_id){
	var element = $('#'+preset_selector_id+' :selected');

	//Prepare the selected Preset Details to be sent with this Upload
	$('#preset_title').val(element.data('title'));
	$('#preset_directory').val(element.data('directory'));
	$('#preset_height_large').val(element.data('height_large'));
	$('#preset_width_large').val(element.data('width_large'));
	$('#preset_action_large').val(element.data('action_large'));
	$('#preset_thumb').val(element.data('thumb'));
	$('#preset_height_thumb').val(element.data('height_thumb'));
	$('#preset_width_thumb').val(element.data('width_thumb'));
	$('#preset_action_thumb').val(element.data('action_thumb'));
}//end of function

function open_media_uploader(uploader_type){


	//Set up the Media Uploader based on the requested: media_type
	switch(uploader_type){
		case 'photos':
				//Set the Header of the Media Uploader
				$('#media_uploader_type').text('Photos');
				//Not used at the moment: $('#media_uploader_action')
				$('#file_to_upload_label').text('Choose your Image to Upload');
				$('#media_upload_item_title').text('Photo');
				$('#media_tab_preview').val('photos');

				//Open the Presets Area
				if($('#presets_area').css('display') == 'none') $('#presets_area').show();
			break;

		case 'docs':
				//Set the Header of the Media Uploader
				$('#media_uploader_type').text('Documents');
				$('#file_to_upload_label').text('Choose your Document to Upload');
				$('#media_upload_item_title').text('Document');
				$('#media_tab_preview').val('docs');

				//Close the Presets Area as not required for Documents uploads
				if($('#presets_area').css('display') == 'block') $('#presets_area').hide();
			break;

        case 'fonts':
            //Set the Header of the Media Uploader
            $('#media_uploader_type').text('Fonts');
            $('#file_to_upload_label').text('Choose your font to upload');
            $('#media_upload_item_title').text('Font');
            $('#media_tab_preview').val('fonts');

            //Close the Presets Area as not required for Documents uploads
            if($('#presets_area').css('display') == 'block') $('#presets_area').hide();
            break;

		case 'audios':
				//Set the Header of the Media Uploader
				$('#media_uploader_type').text('Audios');
				$('#file_to_upload_label').text('Choose your Audio to Upload');
				$('#media_upload_item_title').text('Audio');
				$('#media_tab_preview').val('audios');

				//Close the Presets Area as not required for Documents uploads
				if($('#presets_area').css('display') == 'block') $('#presets_area').hide();
			break;

		case 'videos':
				//Set the Header of the Media Uploader
				$('#media_uploader_type').text('Videos');
				$('#file_to_upload_label').text('Choose your Video to Upload');
				$('#media_upload_item_title').text('Video');
				$('#media_tab_preview').val('videos');

				//Close the Presets Area as not required for Documents uploads
				if($('#presets_area').css('display') == 'block') $('#presets_area').hide();
			break;
	}//end of switch

	//Open the Media Uploader View
	$('#media_uploader').modal();

}//end of function


function delete_media_item(caller){
	var item_id = $(caller).attr('id');
	var item_id_parts = item_id.split("delete_");
	//Get the Media Type of the Item to delete from the class attribute
	var item_class = $(caller).attr('class');
	var item_class_parts = item_class.split('delete_');
	$("#btn_delete_yes").data('item-id', item_id_parts[1]);
	$("#btn_delete_yes").data('item-type', item_class_parts[1]);
	$("#btn_delete_yes")[0].delete_caller = caller;

	//Prompt user before to Delete this Item
	$('#confirm_delete').modal();
}//end of function