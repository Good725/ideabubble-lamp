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
				'/admin/news/ajax_toggle_publish',
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


	//Delete a News Story
	$(".delete").on("click", function(event){
		var click_item = $(this);
		//Get the id from the id attribute
		var item_id = $(this).attr('id');
		var item_id_parts = item_id.split("delete_");

		//Prompt user before to Delete this Item
		var ok_to_delete = confirm(
				'You are about to DELETE a News Story #'+item_id_parts[1]+
				'\n\nPlease NOTE that this cannot be un-done!\n\n'+
				'Do you want to DELETE News Story #'+item_id_parts[1]
		);

		//OK To Delete Item
		if(ok_to_delete){
			//Remove alerts, prevent stack
			$(".alert").remove();

			//Delete Item
			$.post(
					'/admin/news/ajax_toggle_delete',
					{
						item_id : item_id_parts[1]
					},
					function(response){
						if(response.err_msg != ''){
							$("#main").prepend(response.err_msg);
						}else if(response.success_msg != ''){
							//show Response message
							$("#main").prepend(response.success_msg);
							click_item.parent().remove();
						}
					},
					'json'
			);
		}
	});

});
