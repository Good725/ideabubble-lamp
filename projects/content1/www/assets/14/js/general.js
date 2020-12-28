$(document).ready(function ()
{
	// Validate any formbuilder forms and any forms with the class "validate-on-submit"
	$('[action*="frontend/formprocessor"], .validate-on-submit').on('submit',function(ev) {
		ev.preventDefault();
		var valid = $(this).validationEngine('validate');
		if (valid) this.submit();
	});

    $('#submit-quick-contact').click(function (ev)
    {
        ev.preventDefault();
        var form = $('#form-quick-contact');
        var captcha = form.find('#recaptcha_response_field');

        var valid = (form.validationEngine('validate'));
        var captcha_valid = (captcha.val() != '' || captcha === 'undefined');

        if (valid && captcha_valid) {
            form.attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-newsletter--").validationEngine('validate'));
        if (valid) {
            $('#form-newsletter--').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });

});