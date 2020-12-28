$(document).ready(function(){
    $("#form_text_alert").find("[name=send]").on("click", function(){
        var data = $("#form_text_alert").serialize();

        $.post(
            "/admin/bookings/alert",
            data,
            function (response)
			{

				if (response.success) {
					$('#textalert-response-button').removeClass('btn-warning').addClass('btn-success');
				}
				else {
					$('#textalert-response-button').removeClass('btn-success').addClass('btn-warning');
				}

				$('#textalert-response-message').text(response.message);
				$('#textalert-response-modal').appendTo('body').modal('show');
            }
        );
    });

	$('.textalert-toggle').on('click', function() {
		var $expanded_area  = $('#form_text_alert');
		var $collapsed_area = $('#textalert-area-collapsed');
		var $textalert_area = $('.textalert-area');

		if ($collapsed_area.hasClass('hidden'))
		{
			$expanded_area.addClass('hidden');
			$collapsed_area.removeClass('hidden');
			$textalert_area.removeClass('expanded');
		}
		else
		{
			$collapsed_area.addClass('hidden');
			$expanded_area.removeClass('hidden');
			$textalert_area.addClass('expanded');
		}
	});
});
