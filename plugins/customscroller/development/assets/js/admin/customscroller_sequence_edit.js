$(document).ready(
	function(){
		$('table.dataTable1').ready(function(){

			var oTable  = $('#custom_sequence_table').dataTable();
			var old_options = oTable.fnSettings();
			// Set Columns that will be Sortable
			var sortable_columns = [4];
			var table_columns = old_options.aoColumns;

			$(table_columns).each(
				function(index, item){
					// Disable The Sorting on the Current Column if it is NOT in the: sortable_columns
					if(sortable_columns.indexOf(index) == -1){
						item.bSortable = false;
					}
				}
			);

			// Set the New Options for This DataTable
			var new_options = $.extend(
				old_options,
				{
					'bPagination': false,
					'bFilter': true,
					'bSort': true,
					'sPaginationType': 'bootstrap',
//					'aLengthMenu': [[10, 25, 50, 100 , -1], [10, 25, 50, 100, "All"]],
					'aLengthMenu': [[-1], ["All"]],
					'iDisplayLength' : -1,
					'aaSorting': [[ 5, "asc" ]]
				}
			);

			// Re-Initialize the DataTable
			oTable.fnDestroy();
			$('#custom_sequence_table').dataTable(new_options);
		});

        // Update the sequence information on the initial load
        update_sequence_information('current');

        // Actions for the Buttons Groups: Publish / Un-publish; Yes / No etc
		$('.btn-group .btn').click(
			function(event){
				event.preventDefault();
				$('#'+$(this).parent().data('toggle-name')).val($(this).val());
			}
		);

        // Action for ADDING / UPDATING of a Custom Scroller Sequence Item
		$('#btn_add_to_sequence').click(
			function(event){
				event.preventDefault();
				var sequence_id = $('#sequence_id').val();
				var sequence_item_id = $('#sequence_item_id').val();

				if($('#sequence_item_id').val() == 'new'){
					// Get the ROW HTML to be Added to the: #custom_sequence_table
					$.ajax({
						url     : '/admin/customscroller/ajax_add_update_custom_sequence_item/',
						data    : {
							'sequence_id'      : $('#sequence_id').val(),
							'id'               : $('#sequence_item_id').val(),
							'title'            : $('#sequence_item_title').val(),
                            'image'            : $('#sequence_item_image').val(),
                            'mobile_image'     : $('#sequence_item_mobile_image').val(),
							'image_location'   : $('#sequence_item_image_location').val(),
							'order_no'         : $('#sequence_item_order_no').val(),
							'overlay_position' : $('#sequence_item_overlay_position').val(),
							'label'            : $('#sequence_item_label').val(),
							// NOT WORKING like that with CKEDITOR
	//						'html'             : $('#sequence_item_html').val(),
							// USE below instead
							'html'             : CKEDITOR.instances.sequence_item_html.getData(),
							'link_type'        : $('#sequence_item_link_type').val(),
							'link_url'         : $('#sequence_item_link_url').val(),
							'link_target'      : $('#sequence_item_link_target_yes').prop('checked') ? 1 : 0,
							'publish'     	   : $('#sequence_item_publish_yes').prop('checked') ? 1 : 0,
							'get_item_tr_html' : true
						},
						type     : 'post',
						dataType : 'json',
						async    : false
					}).done(function(result){
						if(result.err_msg != ''){
							$('#scroller_item_pop_up_editor .modal-error-area').html(result.err_msg);
						}
						else
						{
							var sequence_table = $('#custom_sequence_table');

							// Take the Sequence Item Image name without the extension
							var image_name = $('#sequence_item_image').val().substr(0, ($('#sequence_item_image').val().length - 4));
							var id = $('#sequence_item_id').val();
							// ADD / UPDATE a Row in the $('#custom_sequence_table').dataTable()
							// Using image_name, rather than an ID, for rows yet to be saved, so that they have a unique identifier.
							if(typeof $('#tr_id_'+image_name)[0] === 'undefined' && typeof $('#tr_id_'+id)[0] === 'undefined')
							{
								// Add the Sequence Item ROW to the Sequence Items dataTable: #custom_sequence_table
								sequence_table.dataTable().fnDestroy();
                                sequence_table.dataTable({"sPaginationType":"bootstrap"}).fnAddData(
                                    [{
                                        '0':result.item_tr_data.order_no,
                                        '1':result.item_tr_data.image_html,
                                        '2':result.item_tr_data.image_name,
                                        '3':result.item_tr_data.mobile_image_name,
                                        '4':result.item_tr_data.title,
                                        '5':result.item_tr_data.label,
                                        '6':result.item_tr_data.html,
                                        '7':result.item_tr_data.link_type,
                                        '8':result.item_tr_data.link_url,
                                        '9':result.item_tr_data.link_target,
                                        '10':result.item_tr_data.overlay_position,
                                        '11':result.item_tr_data.publish,
                                        '12':result.item_tr_data.deleted,
                                        'DT_RowId': 'tr_id_'+result.item_tr_data.id
                                    }]
                                );
							}
							else
							{
								var row_key = sequence_table.find('tbody').children().index($('#tr_id_'+id));

								// Update the Sequence Item ROW to the Sequence Items dataTable: #custom_sequence_table
								sequence_table.dataTable().fnDestroy();
								sequence_table.dataTable({"sPaginationType":"bootstrap"}).fnUpdate(
									[
										result.item_tr_data.order_no,
										result.item_tr_data.image_html,
                                        result.item_tr_data.image_name,
                                        result.item_tr_data.mobile_image,
										result.item_tr_data.title,
										result.item_tr_data.label,
										result.item_tr_data.html,
										result.item_tr_data.link_type,
										result.item_tr_data.link_url,
										result.item_tr_data.link_target,
										result.item_tr_data.overlay_position,
										result.item_tr_data.publish,
										result.item_tr_data.deleted
									],
									row_key
								);
							}

//								// Add the Sequence Item ROW to the Sequence Items dataTable: #custom_sequence_table
//								$('#custom_sequence_table').dataTable().fnAddData([
//									result.item_tr_data.number,
//									result.item_tr_data.image_html,
//									result.item_tr_data.image_name,
//									result.item_tr_data.title,
//									result.item_tr_data.order_no,
//									result.item_tr_data.html,
//									result.item_tr_data.link_type,
//									result.item_tr_data.link_url,
//									result.item_tr_data.link_target,
//									result.item_tr_data.publish,
//									result.item_tr_data.deleted
//								]);
						}
					});
				} else {
					var sequence_table = $('#custom_sequence_table');
					var row_key = sequence_table.find('tbody').children().index($('#tr_id_' + sequence_item_id));
					// Update the Sequence Item ROW to the Sequence Items dataTable: #custom_sequence_table
					sequence_table.dataTable().fnDestroy();
                    var row_data = [];
                    var item_image = $('#sequence_item_image').val();
                    var item_mobile_image = $('#sequence_item_mobile_image').val();
                    var item_image_base = item_image.replace(/\.jpg|\.jpeg|\.png|\.gif/i, "");
                    var name_pre = 'sequence_data[sequence_items][' + sequence_item_id + item_image_base + ']';
                    var overlay_position = $('#sequence_item_overlay_position').val();

                    row_data[0] = '<input type="text" class="span1" name="' + name_pre + '[order_no]" id="' + item_image + '_title" value="' + $('#sequence_item_order_no').val() + '" size="5" /><input type="hidden" name="' + name_pre + '[id]" value="' + sequence_item_id + '" /><input type="hidden" name="' + name_pre + '[sequence_id]" value="' + sequence_id + '" />';
                    row_data[1] = '<img class="span2" src="/media/photos/banners/_thumbs_cms/' + item_image + '" alt="' + item_image + '">';
                    row_data[2] = '<span id="item_' + sequence_item_id + '_image">' + item_image + '</span>' +
                        '<input type="hidden" name="' + name_pre + '[image]" value="' + item_image + '" />' +
                        '<input type="hidden" name="' + name_pre + '[image_location]" value="' + $('#sequence_item_image_location').val() + '" />';
                    row_data[3] = '<span id="item_' + sequence_item_id + '_mobile_image">' + item_mobile_image + '</span>' +
                        '<input type="hidden" name="' + name_pre + '[mobile_image]" value="' + item_mobile_image + '" />';
                    row_data[4] = '<input type="text" name="' + name_pre + '[title]" id="item_' + sequence_item_id + '_title" value="' + $('#sequence_item_title').val() + '" size="10"/>';
                    row_data[5] = '<input type="text" name="' + name_pre + '[label]" id="item_' + sequence_item_id + '_title" value="' + $('#sequence_item_label').val() + '" size="10"/>';
                    row_data[6] = '<span class="sequence_item_edit link" onclick="get_sequence_scroller_item_editor(' + sequence_item_id + ',\'' + item_image_base + '\')">Edit</span><textarea  cols="30" rows="2" style="display:none;" name="' + name_pre + '[html]" id="html_' + item_image + '">' + CKEDITOR.instances.sequence_item_html.getData() + '</textarea>';
                    row_data[7] = '<select name="' + name_pre + '[link_type]" id="link_type_' + item_image + '" class="span2" onchange="if(typeof $(\'#sequence_item_' + item_image_base + '_link_url_hidden\') !== \'undefined\') $(\'[id=&quot;sequence_item_' + item_image_base + '_link_url_hidden&quot;]\').val(this.value);update_sequence_urls_feed_based_on_link_type(this.value, \'sequence_item_' + item_image_base + '_link_url_holder\', \'' + item_image_base + '\');" >' + copy_options_html($('#sequence_item_link_type')[0]) + '</select>';
                    if($('select#sequence_item_link_url').length > 0){
                        row_data[8] = '<span class="sequence_item_' + item_image_base + '_link_url_holder"><select name="sequence_item_link_url" type="text" class="sequence_item_link_url span3" id="sequence_item_' + item_image_base + '_link_url" onchange="if(typeof $(\'#sequence_item_' + item_image_base + '_link_url_hidden\') !== \'undefined\') $(\'#sequence_item_' + item_image_base + '_link_url_hidden\').val(this.value);">' + copy_options_html($('#sequence_item_link_url')[0]) + '</select></span><input type="hidden" name="' + name_pre + '[link_url]" id="sequence_item_' + item_image_base + '_link_url_hidden" value="' + $('#sequence_item_link_url').val() + '" /><input type="hidden" name="tmp_item_ext_link_url" class="tmp_item_ext_link_url" value="" /><input type="hidden" name="tmp_item_int_link_url" class="tmp_item_int_link_url" value="' + $('#sequence_item_link_url').val() + '" />';
                    } else {
                        row_data[8] = '<span class="sequence_item_' + item_image_base + '_link_url_holder"><input type="text" name="sequence_item_link_url" id="sequence_item_' + item_image_base + '_link_url" class="sequence_item_link_url span3" value="' + $("#sequence_item_link_url").val() + '" onkeyup="if(typeof $(\'#sequence_item_' + item_image_base + '_link_url_hidden\') !== \'undefined\') $(\'#sequence_item_' + item_image_base + '_link_url_hidden\').val(this.value);" size="5" /></span><input type="hidden" name="' + name_pre + '[link_url]" id="sequence_item_' + item_image_base + '_link_url_hidden" value="' + $("#sequence_item_link_url").val() + '" /><input type="hidden" name="tmp_item_ext_link_url" class="tmp_item_ext_link_url" value="' + $("#sequence_item_link_url").val() + '" /><input type="hidden" name="tmp_item_int_link_url" class="tmp_item_int_link_url" value="" />';
                    }
                    row_data[9] = '<div class="span3 controls"><div class="btn-group" data-toggle="buttons"><label class="btn btn-plain ' + ($('#sequence_item_link_target_yes').prop('checked') ? 'active ' : '' ) + '"><input type="radio" name="' + name_pre + '[link_target]" value="0" ' + ($('#sequence_item_link_target_yes').prop('checked') ? 'checked ' : '' ) + '/>Same Tab</label><label class="btn btn-plain ' + ($('#sequence_item_link_target_yes').prop('checked') ? '' : 'active' ) + '"><input type="radio" name="' + name_pre + '[link_target]" value="1" ' + ($('#sequence_item_link_target_yes').prop('checked') ? '' : 'checked ' ) + '/>New Tab</label></div></div>';
                    row_data[10] = '<select name="' + name_pre + '[overlay_position]" id="item' + sequence_item_id + '_overlay_position">' +
                        '<option value="center"'+((overlay_position == 'center') ? ' selected="selected"' : '')+'>Centre</option>' +
                        '<option value="left"'+((overlay_position == 'left') ? ' selected="selected"' : '')+'>Left</option>' +
                        '<option value="right"'+((overlay_position == 'right') ? ' selected="selected"' : '')+'>Right</option>' +
                        '</select>';
                    row_data[11] = '<i class="icon-' + ($('#sequence_item_publish_yes').prop('checked') ? 'ok' : 'remove') + '" data-id="' + sequence_item_id + '"></i><input type="hidden" name="' + name_pre + '[publish]" value="' + ($('#sequence_item_publish_yes').prop('checked') ? 1 : 0) + '" />';
                    row_data[12] = '<i class="icon-remove-circle" onclick="toggleDelete(' + sequence_item_id + ');"></i>';
					/*
					result.item_tr_data.order_no,
										result.item_tr_data.image_html,
										result.item_tr_data.image_name,
										result.item_tr_data.title,
										result.item_tr_data.label,
										result.item_tr_data.html,
										result.item_tr_data.link_type,
										result.item_tr_data.link_url,
										result.item_tr_data.link_target,
										result.item_tr_data.publish,
										result.item_tr_data.deleted
					*/
					sequence_table.dataTable({"sPaginationType":"bootstrap"}).fnUpdate(row_data,row_key);
					$('[name*="[link_target]"][checked]:not(:checked)').prop('checked', true);
				}
				
				// Close the Sequence Item Editor:
				$('#scroller_item_pop_up_editor').modal('hide');
				// Clear the Sequence Item Editor view (to prevent duplicate POSTING of the Data for the LAST - ADDED / EDITED Sequence Item
				$('#scroller_item_pop_up_editor').children('.modal-body').html('<p>Sequence Item Editor Screen Goes Here</p>');
			}
		);

        $('#custom_sequence_table').on('click', 'tbody tr td:nth-last-child(2) i', function()
        {
            var publish_icon = $(this);
            var id = publish_icon.data('id');
            var publish = (publish_icon.hasClass('icon-remove')) ? 1 : 0;
            var new_icon_class = (publish == 1) ? 'ok' : 'remove';

            publish_icon.attr('class', 'icon-'+new_icon_class);
            publish_icon.parents('td').find('input[type=hidden]').val(publish);

            if (!isNaN(id))
            {
                $.ajax({
                    url  : '/admin/customscroller/publish/'+id+'/',
                    data : {'id' : id, 'publish' : publish },
                    type : 'post'
                }).done(function(result)
                {
                    if (result != 'success')
                    {
                        alert(result);
                    }
                });
            }

        });

    }
);


function get_sequence_scroller_item_editor(item_id, item_img_name){
	var scroller_item_editor = $('#scroller_item_pop_up_editor');
	var editor_header		 = 'Add Scroller Item';
	var editor_err_msg		 = '';
	var editor_view			 = '';
    if (item_id == '') item_id = 'new';

	// A Specific Scroller Item is to be LOADED for Editing
	if ((typeof item_id !== 'undefined' && Math.floor(item_id) == item_id && $.isNumeric(item_id)) && (typeof item_img_name !== 'undefined' && $.trim(item_img_name) !== ''))
	{
		// Set the Editor Header
		editor_header = '<h2>Update Scroller Sequence Item ID: #'+item_id+'</h2>';
		// Get the Editor View
		$.ajax({
			url     		: '/admin/customscroller/ajax_get_custom_sequence_item_editor_view/',
			data   			: {
				sequence_item_id  : item_id,
				item_img		: ((typeof item_img_name !== 'undefined')? item_img_name : '' ),
				plugin_name		: ((typeof $('#sequence_holder_plugin') !== 'undefined')? $('#sequence_holder_plugin').val() : ''),
				sequence_id		: ((typeof $('#existing_sequences') !== 'undefined')? $('#existing_sequences').val() : '0')
			},
			type    : 'post',
			dataType: 'json',
			async   : false
		}).done(function(result){
					if(result.err_msg == ''){
						editor_view = result.cs_item_editor_view;
					}else{
						editor_err_msg = result.err_msg;
					}
					// Update the title of the #btn_add_to_sequence Button
					$('#btn_add_to_sequence').text('Update Sequence Item: #'+item_id);
				});
	}

	// A NEW Image is to be added to this Scroller Sequence as a Scroller Item
	if ((typeof item_id !== 'undefined' && $.trim(item_id) !== '' && item_id == 'new') && (typeof item_img_name !== 'undefined' && $.trim(item_img_name) !== ''))
	{
		//image to add ID="scroller_available_img_+image_name"
		// Set the Editor Header
		editor_header = '<h2>Add New Item Image: "'+item_img_name+'" to this Custom Scroller Sequence</h2>';
		// Get the Editor View
		$.ajax({
			url     		: '/admin/customscroller/ajax_get_custom_sequence_item_editor_view/',
			data   			: {
				sequence_item_id 	: 'new',
				item_img		: item_img_name,
				plugin_name		: ((typeof $('#sequence_holder_plugin') !== 'undefined')? $('#sequence_holder_plugin').val() : ''),
				sequence_id		: ((typeof $('#existing_sequences') !== 'undefined')? $('#existing_sequences').val() : '0')
			},
			type    : 'post',
			dataType: 'json',
			async   : false
		}).done(function(result){
					if(result.err_msg == ''){
						editor_view = result.cs_item_editor_view;
					}else{
						editor_err_msg = result.err_msg;
					}
					// Update the title of the #btn_add_to_sequence Button
					$('#btn_add_to_sequence').text('Add Item to Sequence');
				});
	}

	// Prepare the Editor pop-up Header and Content
	scroller_item_editor.find('.modal-header').html(editor_header);
	if($.trim(editor_err_msg) != '') scroller_item_editor.children('.modal-error-area').html(editor_err_msg);
	scroller_item_editor.find('.modal-body').html(editor_view);
	scroller_item_editor.find('.modal-dialog').css('width', 815);
	// Open the Editor pop-up
	scroller_item_editor.modal();
}


function update_sequence_urls_feed_based_on_link_type(link_type_id, item_url_holder, item_img_identifier)
{
    var $item_url_holder_class = $('.' + item_url_holder);
	// Update the Link URL FIELD -=> INPUT or SELECT based on the Link Type
	switch(link_type_id)
    {
        case 'internal':

			// Keep the current external Link URL Value of the sequence_item_link_url in: tmp_item_ext_link_url
            $item_url_holder_class.parent().children('.tmp_item_ext_link_url').val($('[class="'+item_url_holder+' .sequence_item_link_url"]').val());

			// Kep the sequence_item_<?=$item_image?>_link_url_hidden if there is such, up to to date with the editor view
			if (typeof $item_url_holder_class.parent().children('[id="'+(item_url_holder.replace('_holder', '_hidden'))+'"]') != 'undefined')
            {
                $item_url_holder_class
                    .parent()
                    .children('[id="'+(item_url_holder.replace('_holder', '_hidden'))+'"]')
                    .val($('[class="'+item_url_holder+' .sequence_item_link_url"]').val());
			}

			// Set the LINK URL to be a Drop Down with THIS Website Pages
			$.ajax({
				url     : '/admin/customscroller/ajax_get_internal_links_as_select/',
				data    : {
					current_link_id: $item_url_holder_class.parent().children('.tmp_item_int_link_url').val(),
					link_image_id  : item_img_identifier
				},
				type    : 'post',
				dataType: 'json',
				async   : false
			}).done(function(result){
						if(result.err_msg == '' && result.pages_links_select != ''){
                            $item_url_holder_class.html(result.pages_links_select);
						}else{
							alert('Error:'+result.err_msg);
						}
					});
			break;
		case 'external':
			// Keep the current internal Link URL Value of the sequence_item_link_url in: tmp_item_int_link_url
            $item_url_holder_class.parent().children('.tmp_item_int_link_url').val($('[class="'+item_url_holder+' .sequence_item_link_url"]').val());

			// Kep the sequence_item_<?=$item_image?>_link_url_hidden if there is such, up to to date with the editor view
			if (typeof $item_url_holder_class.parent().children('[id="'+(item_url_holder.replace('_holder', '_hidden'))+'"]') != 'undefined'){
                $item_url_holder_class
                    .parent()
                    .children('[id="'+(item_url_holder.replace('_holder', '_hidden'))+'"]')
                    .val($('[class="'+item_url_holder+' .sequence_item_link_url"]').val());
			}

			// Set the Link URL to be an INPUT field
            $item_url_holder_class.html(
				'<input type="text" name="sequence_item_link_url" class="sequence_item_link_url" id="sequence_item_link_url" value="'
							+$item_url_holder_class.parent().children('.tmp_item_ext_link_url').val()
						+'"'
						+'  onkeyup="if(typeof $(\'[id=&quot;'+item_url_holder.replace('_holder', '_hidden')+'&quot;]\') !== \'undefined\') $(\'[id=&quot;'+item_url_holder.replace('_holder', '_hidden')+'&quot;]\').val(this.value);"'
						+' size="5"/>'
			);
			break;
		default: case 'none':
			// Kep the sequence_item_<?=$item_image?>_link_url_hidden if there is such, up to to date with the editor view
			if (typeof $item_url_holder_class.parent().children('#'+item_url_holder.replace('_holder', '_hidden')) != 'undefined'){
				$('.'+item_url_holder).parent().children('#'+item_url_holder.replace('_holder', '_hidden')).val('');
			}
			// Set the Link URL to be an INPUT field with NO value
            $item_url_holder_class.html(
				'<input type="text" name="sequence_item_link_url" class="sequence_item_link_url" id="sequence_item_link_url" value=""'
						+' onkeyup="if(typeof $(\'[id=&quot;'+item_url_holder.replace('_holder', '_hidden')+'&quot;]\') !== \'undefined\') $(\'[id=&quot;'+item_url_holder.replace('_holder', '_hidden')+'&quot;]\').val(this.value);"'
						+' size="5"/>'
			);
			break;
	}
}

function update_sequence_information(sequence_select) {
    if (sequence_select == 'current')
        var sequence = $('#sequence_id [selected]')[0];
    else
        var sequence = sequence_select.options[sequence_select.selectedIndex];

    change_input_box('sequence_title');
    change_input_box('animation_type');
    change_input_box('order_type');
    change_input_box('rotating_speed');
    change_input_box('timeout');

    toggle_buttons('pagination');
    toggle_buttons('controls');
    toggle_buttons('publish');

    update_sequence_items(sequence.value);

    // Change input boxes and select lists to match attributes from the sequence's option tag
    function change_input_box(box) {
        $('#'+box).val(sequence.getAttribute('data-'+box));
    }

    // Change buttons and hidden input tag
    function toggle_buttons(button) {
        var on_value = parseInt(sequence.getAttribute('data-'+button));

        if (isNaN(on_value))
        {
            var on_value = 1;
            var off_value = 0;
        }
        else
        {
            var off_value = (on_value + 1) % 2;
        }

        $('[data-toggle-name="'+button+'"] [value='+ on_value+']').addClass('active');
        $('[data-toggle-name="'+button+'"] [value='+off_value+']').removeClass('active');
        $('#'+button).val(on_value);
    }

    $('.popinit').popover({ html : true });
}

function update_sequence_items(sequence_id)
{
    // update sequence item table
    $.ajax({
        url : '/admin/customscroller/ajax_get_sequence_items/'+sequence_id+'/'
    }).done(function(result){
        $('#custom_sequence_table_wrapper tbody').html(result);
    });

    // update available images
    $.ajax({
        url : '/admin/customscroller/ajax_get_available_images/'+sequence_id+'/',
        data    : {
            plugin : ((typeof $('#sequence_holder_plugin') !== 'undefined')? $('#sequence_holder_plugin').val() : 'banners')
        },
        type    : 'post'
    }).done(function(result){
        $('#available_images').html(result);
    });
}

function toggleDelete(id)
{
    var title = $('#item_'+id+'_title').val();
    var sequence_id = $('#sequence_id').val();
//    var image_name = document.getElementById('item_'+id+'_image').innerHTML;

    var choice = confirm('Are you sure you want to remove \"'+title+'\" from the sequence?');

    if (choice)
    {
        $.ajax({
            url  : '/admin/customscroller/deleteitem/'+id+'/'
        }).done(function(result){
                if (result != 'success')
                    alert(result);
                else
                {
                    update_sequence_items(sequence_id);
                }
            });
    }
}
