$(document).ready(function(){
    $("#form_text_note [name=save]").on("click", function(){
        var data = $("#form_text_note").serialize();

        $.post(
            "/admin/contacts3/dashboard_note_save",
            data,
            function (response) {
                if (response == true) {
                    alert('Note has been saved');

					var $expanded_area = $('#form_text_note');
					var $collapsed_area = $('#textnote-area-collapsed');
					var $textnote_area = $('.textnote-area');

					$expanded_area.addClass('hidden');
					$collapsed_area.removeClass('hidden');
					$textnote_area.removeClass('expanded');
                } else {
                    alert('An unexpected error occurred');
                }
            }
        );
    });

	$('.textnote-toggle').on('click', function() {
		var $expanded_area = $('#form_text_note');
		var $collapsed_area = $('#textnote-area-collapsed');
		var $textnote_area = $('.textnote-area');

		if ($collapsed_area.hasClass('hidden'))
		{
			$expanded_area.addClass('hidden');
			$collapsed_area.removeClass('hidden');
			$textnote_area.removeClass('expanded');
		}
		else
		{
			$collapsed_area.addClass('hidden');
			$expanded_area.removeClass('hidden');
			$textnote_area.addClass('expanded');
		}
	});

	$('#right-panel-text-note [name=contact_selector]').autocomplete({
		source : '/admin/contacts3/ajax_get_all_contacts_ui',
		open   : function () {
			//$(this).data("uiAutocomplete").menu.element.addClass('educate_ac');
		},
		select : function (event, ui) {
			$('#right-panel-text-note [name=contact_selector]').val(ui.item.value);
			$('#right-panel-text-note [name=contact_id]').val(ui.item.id);
		}
	});
});
