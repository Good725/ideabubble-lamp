$(document).ready(function(){
    $('.alert, .checkout_message_error').on('click', '.close', function(){
        $(this).parent().remove();
    });

    $('.product_description_text').appendTo('#product').css('clear', 'both');

	$("#submit-newsletter").click(function (ev) {
		ev.preventDefault();
		var valid = ($("#form-newsletter").validationEngine('validate'));
		if (valid) {
			$('#form-newsletter').attr('action', '/frontend/formprocessor').submit();
		}
		else {
			setTimeout('removeBubbles()', 5000);
		}

	});




});