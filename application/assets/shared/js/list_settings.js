$(document).ready(function()
{
    function rgb2hex(rgb){
        rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
        return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    }

    /* Colour picker */
    var $custom_color_link = $('.custom_color_link');

    // Dismiss palette when clicked away from
    $(window).on('click', function(ev)
    {
        ev.stopPropagation();
        $('.color_palette').hide();
    });
    // Show palette when colour box is clicked
    $('.select_color_preview, .select_color_text, .custom_color_link').on('click', function(ev)
    {
        ev.stopPropagation();
        $(this).parents('.color_picker_wrapper, .controls, .form-group').find('.color_palette').show();
    });

    // Set colour, when a colour from the palette is clicked
    $('.color_palette').on('click touchup', 'tbody td[style]:not([colspan])', function()
    {
        var color  = ($(this).hasClass('transparent_option')) ? 'transparent' : $(this).css('background-color');
        var $section = $(this).parents('.color_picker_wrapper');
        $section.find('.select_color_preview').css('background-color', color);
        $('.color_picker_input').val(rgb2hex(color));
        $section.find('.color_palette').hide();
    });

    // make spectrum appear when "custom" is clicked
    $custom_color_link.on('click touchup', function(ev)
    {
        ev.preventDefault();
        $(this).find('input').spectrum({
            change: function (color) {
                var $section = $(this).parents('.color_picker_wrapper');
                $section.find('.color_picker_input').val(color.toHexString());
                $section.find('.select_color_preview').css('background-color', color.toHexString());
                $('.color_picker_input').val(color.toHexString());
                $section.find('.color_palette').hide();
            }
        });
        $(this).find('.sp-replacer').click();
        $('.sp-container').appendTo($(this).parents('.color_picker_wrapper').find('.custom_color_link'));
    });

    // put value from spectrum into empty custom colour cell
    $custom_color_link.find('input').on('change', function()
    {
        var $custom_palette = $(this).parents('.controls').find('.custom_palette');
        // Create a new row, if necessary
        if ($custom_palette.find('td:not([style])').length == 0)
        {
            $custom_palette.append('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
        }

        var color = $(this).find('.sp-preview-inner').css('background-color');
        $custom_palette.find('td:not([style])').first().css('background-color', color);
    });

	$('.multipleselect').multiselect();

	// Update "Default Theme" dropdown to only show options from the available themes list
	$('#available_themes, #template_folder_path').on('change', function()
	{
		var available_themes = $('#available_themes').val();
		var template = $('#template_folder_path').val();

		$('#assets_folder_path').find('option').each(function()
		{
			if (this.getAttribute('data-template') != template)
			{
				$(this).prop('disabled', true).css('display', 'none');
			}
			else
			{
				$(this).prop('disabled', false).css('display', '');
			}
		});

	}).trigger('change');

});