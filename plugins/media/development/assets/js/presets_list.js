$(document).ready(function(){

    //Change publish status, AJAX request
    $(".publish").on("click", function(event){
        var click_item = $(this);
        //Get the id from the id attribute
        var item_id = $(this).attr('id');
        var item_id_parts = item_id.split("publish_");
		var item_publish = click_item.data('item_publish');

        //Remove alerts, prevent stack
        $(".alert").remove();

		//Toggle Publish
		$.post(
				'/admin/presets/ajax_toggle_publish',
				{
					item_id : item_id_parts[1],
					publish : ((item_publish == 1)? 0 : 1 )
				},
				function(response){
					if(response.err_msg != ''){
						$("#main").prepend(response.err_msg);
					}else if(response.success_msg != ''){
						//show Response message
						$("#main").prepend(response.success_msg);
						//update the click_item
						if(item_publish == 1){
							//Item flag was Publish => change to Unpublished
							click_item.html('<i class="icon-ban-circle"></i>');
							click_item.data('item_publish', '0');
						}else{
							//Item flag was Un-publish => change it to Published
							click_item.html('<i class="icon-ok"></i>');
							click_item.data('item_publish', '1');
						}
					}
				},
				'json'
		);
    });


	//Delete a Preset from the Presets List view
	$(".delete_preset").on("click", function(event){
		var click_item = $(this);
		//Get the id from the id attribute
		var item_id = $(this).attr('id');
		var item_id_parts = item_id.split("delete_");

		//Prompt user before to Delete this Item
		$('#confirm_delete').modal();
		$("#btn_delete_yes").data('item_id', item_id_parts[1]);
	});


	//Open the Preset Pop-Up Editor
	$(".media_presets_editor_trigger").on("click", function(event){
		var click_item = $(this);
		//Get the id from the id attribute
		var item_id = $(this).attr('id');
		//Get the class from the class attribute
		var item_class = $(this).attr('class');
		var editor_view_id = item_class.split('_trigger');

		//Check if this Event was triggered by the General: "Add Preset" link, or a Preset Item is to be edited and Prepare the Editor View
		if(item_id == item_class){
			//Set Editor Heading to: "Add Preset"
			$('#preset_editor_action').text('Add');
			//Clear the Preset Title, as we are going to Add a new one
			$('#preset_editor_title').text('');
			//Reset The Editor View
			$('#preset_editor_title').text('');
			$('#item_title').val('');
			$('#item_directory').val(0);
			$('#item_height_large').val();
			$('#item_width_large').val();
			$('#item_action_large').val(0);
			$('#item_thumb').prop('checked', false);
			$('#item_thumb_details').hide();
			$('#item_height_thumb').val();
			$('#item_width_thumb').val();
			$('#item_action_thumb').val();
			//Set Publish Flag
			$('#item_publish').val(1);
			//Set ID
			$('#item_id').val('');
			//Hide the Delete Button, if not hidden yet
			if($('#delete_preset_holder').css('display') == 'block') $('#delete_preset_holder').hide();

		}else{
			//Set Editor Heading to: "Edit Preset"
			$('#preset_editor_action').text('Edit');

			//Get the Preset to Edit ID from this item's ID
			var item_id_parts = item_id.split('_trigger_');

			//Get The Preset to Edit Details and Update Editor View before to open it
			$.ajax({
				url: '/admin/presets/ajax_get_preset_details',
				data: {
					item_id: item_id_parts[1]
				},
				async: false,
				type: 'POST',
				dataType: 'json'
			}).done(function(preset_details){
						//Set the Preset Title
						$('#preset_editor_title').text('"'+preset_details.title+'"');
						$('#item_title').val(preset_details.title);
						//Set Directory
						$('#item_directory').val(preset_details.directory);
						//Set Preset Dimensions
						$('#item_height_large').val(preset_details.height_large);
						$('#item_width_large').val(preset_details.width_large);
						$('#item_action_large').val(preset_details.action_large);
						//Set Thumb details
						$('#item_thumb').prop('checked', ((preset_details.thumb == 1)? true : false));
						$('#item_height_thumb').val(preset_details.height_thumb);
						$('#item_width_thumb').val(preset_details.width_thumb);
						$('#item_action_thumb').val(preset_details.action_thumb);
						//Show Thumb Details area
						if(preset_details.thumb == 1) $('#item_thumb_details').show();
						//Set Publish Flag
						$('#item_publish').val(preset_details.publish);
						//Set ID
						$('#item_id').val(preset_details.id);
					});


			//Hide the Delete Button, if not hidden yet
			if($('#delete_preset_holder').css('display') == 'none') $('#delete_preset_holder').show();

		}//end of setting the Preset Editor view

		/* 1. Open the Editor View */
		$('#'+editor_view_id[0]).modal();

		/* 2. Process the Preset Editor View */
		// 2.1 Add/Edit Preset
		$('#btn_save_preset').click(function(){
			//Remove alerts. prevent stack
			$('.alert').remove();
			$('#preset_editor_alert_area').html();

			//Save/Edit Preset
			$.ajax({
				url: '/admin/presets/ajax_add_edit_item',
				data: {
					item_id: $('#item_id').val(),
					item_title: $('#item_title').val(),
					item_directory: $('#item_directory').val(),
					item_height_large: $('#item_height_large').val(),
					item_width_large: $('#item_width_large').val(),
					item_action_large: $('#item_action_large').val(),
					item_thumb: (($('#item_thumb').is(':checked'))? 1 : 0 ),
					item_height_thumb: $('#item_height_thumb').val(),
					item_width_thumb: $('#item_width_thumb').val(),
					item_action_thumb: $('#item_action_thumb').val(),
					item_publish: $('#item_publish').val()
				},
				type: 'POST',
				dataType: 'json'
			}).done(function(result){
						if (result.err_msg != '')
						{
							$('#preset_editor_alert_area').html(result.err_msg);
							setTimeout("$('#preset_editor_alert_area').html()", 10000);

						}else{
							//Item has been created/edited successfully -=> refresh page in order to get updates
							$('#presets_list_alert_area').html(result.success_msg);
							$('#'+editor_view_id[0]).modal('hide');
							//@TODO: Add Code to properly update the Presets List View
							setTimeout("document.location.reload(true)", 1000);
						}
					});
		});


		// 2.2 Delete Preset - within the Preset Editor
		$("#btn_delete_preset_yes").click(function(){
			//Prompt user before to Delete this Item
			$('#confirm_delete').modal();
			$("#btn_delete_yes").data('item_id', item_id_parts[1]);
		});

	});

	//Delete a Preset Item
	$("#btn_delete_yes").click(function(){

		var item_to_delete_id = $(this).data('item_id');

		//Remove alerts, prevent stack
		$(".alert").remove();

		//Delete Item
		$.post(
				'/admin/presets/ajax_toggle_delete',
				{
					item_id : item_to_delete_id
				},
				function(response){
					if(response.err_msg != ''){
						$("#main").prepend(response.err_msg);
					}else if(response.success_msg != ''){
						//show Response message
						$("#main").prepend(response.success_msg);
						$('#media_presets_editor_trigger_'+item_to_delete_id).parent().parent().remove();
						//$(this).parent().remove();
						$('.modal').modal('hide');

					}
				},
				'json'
		);
	});

});


function toggleOptDetails(caller_id) {
	if ($('#'+caller_id).is(':checked')){
		$('#'+caller_id+'_details').show(1000);
	}else{
		$('#'+caller_id+'_details').hide(1000);
	}
}//end of function