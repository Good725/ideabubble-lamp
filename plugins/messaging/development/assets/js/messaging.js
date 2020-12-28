// Toggle hidden fields based on clicking an element
$('.show-toggle').on('click', function(ev)
{
	ev.preventDefault();
	var $target = $(this.getAttribute('data-target'));
	$target[0].style.display = $target.is(':visible') ? 'none' : 'block';
	return false;
});

// Contact autocompletes
$.widget( "custom.messaging_catcomplete", $.ui.autocomplete, {
	_create: function() {
		this._super();
		this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
	},
	_renderMenu: function( ul, items ) {
		ul.addClass('messaging-ui-autocomplete');
		var that = this,
			currentCategory = "";
		$.each( items, function( index, item ) {
			var li;
			if ( item.category != currentCategory ) {
				ul.append( "<li class='ui-autocomplete-category'>" + item.category.replace(/_/g, ' ').toLowerCase() + "</li>" );
				currentCategory = item.category;
			}
			li = that._renderItemData( ul, item );
			if ( item.category ) {
				li.attr( "aria-label", item.category + " : " + item.label );
			}
		});
	}
});

// Add contact details under the autocomplete field, after an item has been selected from the autocomplete
function messaging_target_add(container, name, item, x_params) {
	var contact_aleady_recipient = false;
	$('.messaging-recipient').find('input[name="' + name + '[db_id][]"]').map(function () {
		if ($(this).val() == item.value) {
			contact_aleady_recipient = true;
			return true;
		}
	});
	if (contact_aleady_recipient) {
		return false;
	}
	var final_target = (typeof item.final_target !== 'undefined') ? item.final_target : '';
    var final_target_type = (typeof item.final_target_type !== 'undefined') ?  item.final_target_type : '';

	var html = '';
	if (item && item.ask_input) {
		item.db_id = item.db_id || item.value;
		html =
		'<span class="messaging-recipient label label-primary">' +
		'<input type="hidden" name="' + name + '[db_id][]" value="' + (item.db_id ? item.db_id : 'new') + '"/>' +
		'<input type="hidden" name="' + name + '[pid][]" value="' + item.category + '"/>' +
		'<input type="hidden" name="' + name + '[x_details][]" value="' + (x_params.x_details ? x_params.x_details : '') + '"/>' +
		'<input type="text" name="' + name + '[id][]" value="' + item.value + '" style="color:#000000;"/>' +
		'&nbsp;<a href="#" class="remove-to" onclick="return messaging_target_remove(this);">&times;</a></span>'
	} else {
		item.db_id = item.db_id || item.value;
		html =
		'<span class="messaging-recipient label label-primary" data-toggle="tooltip" data-placement="top" data-html="true" title="' +
            item.label + (x_params.label_more && item[x_params.label_more] ? ' &lt;' + item[x_params.label_more] + '&gt;' : '')  + '<br />' +
            (item.email && name === 'email_recipient' && item.final_target_type === "EMAIL" ? item.email + '<br />' : '')
			+ (item.sms && name === 'sms_recipient' && final_target_type === "PHONE" ? item.sms + '<br />'  : '') +
        '">' +
		'<input type="hidden" name="' + name + '[db_id][]" value="' + (item.db_id ? item.db_id : 'new') + '"/>' +
		'<input type="hidden" name="' + name + '[id][]" value="' + item.value + '"/>' +
		'<input type="hidden" name="' + name + '[pid][]" value="' + item.category + '"/>' +
		'<input type="hidden" name="' + name + '[x_details][]" value="' + (x_params.x_details ? x_params.x_details : '') + '"/>' +
        '<input type="hidden" name="' + name + '[final_target][]" value="' + final_target+'" />' +
        '<input type="hidden" name="' + name + '[final_target_type][]" value="' + final_target_type+'" />' +
		'<input type="hidden" name="' + name + '[template_data_id][]" value="' + (item.template_data_id ? item.template_data_id : "") +'" />' +
		'<input type="hidden" name="' + name + '[template_helper_function][]" value="' + (item.template_helper_function ? item.template_helper_function : "") +'" />' +
		item.label +
		'&nbsp;<a href="#" class="remove-to" onclick="return messaging_target_remove(this);">&times;</a></span>';
	}
	$(container).append(html);

    $(container).find('[data-toggle="tooltip"]').tooltip();
}
// Remove a contact that has been added via an autocomplete
function messaging_target_remove(a_remove)
{
	var db_id = parseInt($(a_remove).parent().find('[name*="[db_id][]"]').val());
	if(db_id){
		$(a_remove).parents(".send-message-form").append('<input type="hidden" name="messaging_target_remove[]" value="' + db_id + '" />');
	}

	$(a_remove).parent().siblings('.tooltip').remove();
	$(a_remove).parent().remove();
	return false;
}


// Add an alert to a message area.
// e.g. $('#selector').add_alert('Save successful', 'success');
(function($)
{
	$.fn.add_alert = function(message, type)
	{
		var $alert = $(
			'<div class="alert'+((type) ? ' alert-'+type : '')+'">' +
				'<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
				'</div>');
		$(this).append($alert);
	};
})(jQuery);