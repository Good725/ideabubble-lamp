$(document).ready(function()
{
	$(document).foundation();

	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev) {
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid) this.submit();
	});

	var $nav = $('#home-menu');
	var $menu_wrapper = $('#menu-wrapper');

	$(window).scroll(function () {
		if ($(this).scrollTop() > $menu_wrapper.position().top) {
			$nav.addClass("fixed");
			$menu_wrapper.css('height', $nav.height());
		} else {
			$nav.removeClass("fixed");
			$menu_wrapper.css('height', '');
		}
	});

});