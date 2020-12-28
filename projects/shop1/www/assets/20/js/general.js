$(document).ready(function()
{
	// Show/hide sidebar
	$('#sidebar-toggle').on('click', function()
	{
		var $body = $('body');

		if ($body.hasClass('sidebar-expanded'))
		{
			$body.removeClass('sidebar-expanded');
		}
		else
		{
			$body.addClass('sidebar-expanded');
		}
	});

	$('[action*="frontend/formprocessor"]').on('submit',function(ev) {
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid) this.submit();
	});


	// Expand/Collapse footer menus
	$('.footer-links-inner > li > a, .footer-section-header').on('click', function(ev)
	{
		if ($(window).width() < 700)
		{
			ev.preventDefault();
			var $section;
			if ($(this).hasClass('footer-section-header'))
			{
				$section = $(this).find('\+ .footer-section-body');
			}
			else
			{
				$section = $(this).parent('li').find('ul');
			}

			$section.is(':visible') ? $section.hide() : $section.show();
		}
	});
	// Always show footer menus on >= 700 viewports
	$(window).on('resize', function()
	{
		if ($(window).width() >= 700)
		{
			$('.footer-links-inner > li > ul, .footer-section-body').css('display', '');
		}
	});

	// Show CAPTCHA below the subscription form, after the initial "submit" button is clicked
	$('#newsletter-captcha-toggle').on('click', function()
	{
		$('#newsletter-captcha-section').removeClass('hidden');
		$(this).addClass('hidden');
	});
});
