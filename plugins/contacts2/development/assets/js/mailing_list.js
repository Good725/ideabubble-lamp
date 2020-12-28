$(document).ready(function()
{
    // Type select for adding contacts to the mailing list
    $.widget('custom.mailing_list_catcomplete', $.ui.autocomplete,
    {
        _create: function() {
            this._super();
            this.widget().menu('option', 'items', '> :not(.ui-autocomplete-category)');
        },
        _renderMenu: function(ul, items)
        {
            ul.addClass('messaging-ui-autocomplete');
            var that = this;
            var $li;
            $.each(items, function(index, item)
            {
                $li = that._renderItemData(ul, item);
                $li.attr('aria-label', item.label);
            });
        }
    });

    $('#edit-mlist-type_to_add').mailing_list_catcomplete(
    {
        source: function(data, callback)
        {
			// Get contacts matching the search term
            $.get('/admin/contacts2/ajax_get_contacts', data, function(response)
            {
				// Render as autocomplete items
                response = JSON.parse(response);
                callback(response);
                $('.ui-helper-hidden-accessible').addClass('sr-only');
                $('.ui-autocomplete').css('max-height', 300).css('overflow', 'auto');
            });
        },
        select: function(ev, ui)
        {
			// When an item is selected from the autocomplete...
            ev.preventDefault();

			// Check if the contact is part of an existing mailing list
			if (ui.item.mailing_list_id && ui.item.mailing_list_id != $('#edit-mlist-id').val())
			{
				$('#edit-mlist--otherwise_listed-data').val(JSON.stringify(ui.item));
				$('#edit-mlist--otherwise_listed-contact_name').html(ui.item.first_name+' '+ui.item.last_name);
				$('#edit-mlist--otherwise_listed-mailing_list').html(ui.item.mailing_list);
				$('#edit-mlist-otherwise_listed').modal();
			}
			else
			{
				add_contact(ui.item);
			}


            this.value = '';
        }
	});

	function add_contact(data)
	{
		var $table = $('#edit-mlist-contacts');

		// Copy the table row template, put data for this contact in it and append it to the table
		var $clone = $('#edit-mlist-contact-template').find('tr').clone();
		$clone.attr('data-id', data.id);
		$clone.find('.edit-mlist-contact-id'   ).val(data.id);
		$clone.find('.edit-mlist-contact-name' ).html(data.first_name+' '+data.last_name);
		$clone.find('.edit-mlist-contact-email').html(data.email);
		$table.find('tbody').append($clone);

		// Ensure the table is visible now that it has at least one record
		$table.removeClass('hidden');
	}

	$('#edit-mlist-otherwise_listed-confirm').on('click', function()
	{
		var data = JSON.parse($('#edit-mlist--otherwise_listed-data').val());
		add_contact(data);
		$('#edit-mlist-otherwise_listed').modal('hide');
	});

});

// Remove a contact from the mailing list
$(document).on('click', '.edit-mlist-contact-remove', function()
{
	$(this).parents('tr').remove();
	var $table = $('#edit-mlist-contacts');

	// If this empties the table records, hide the table
	if ($table.find('tbody tr').length == 0)
	{
		$table.addClass('hidden');
	}
});