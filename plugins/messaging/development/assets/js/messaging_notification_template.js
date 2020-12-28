$(document).ready(function()
{

	$("[name=messaging_notification_template_form]").on("submit", function(ev)
	{
		// Validation
		$('.messaging-template-error').remove();
		var $alert_area = $('#message-template-alerts');
		if (this.template_name.value == "")
		{
			$alert_area.add_alert('<strong>Error</strong>: Please select a name.', 'warning messaging-template-error');
			$alert_area[0].scrollIntoView();
			this.template_name.focus();
			return false;
		}

		var has_interval = false;
		$("[name*=interval]").each(function()
		{
			if(this.selectedIndex > 0){
				has_interval = true;
			}
		});
		$("[name*=interval]").each(function()
		{
			if(this.selectedIndex < 1 && has_interval){
				this.selectedIndex = 1;
			}
		});
		return true;
	});

	$("#attachments-add-button").on("click", function(){
		var index = $("#attachments-list").children().length;
		$("#attachments-list").append(
				'<div>' +
				'<label>File Id:</label>' +
				'<input type="text" name="attachment[' + index + '][file_id]" value="" />' +
				'</div>'
		);
	});
});

$('#message-template-to').messaging_catcomplete({
	source: function(data, callback)
	{
		data.driver = document.getElementById('message-template-driver').value;
		data.template = 1;
		$.get('/admin/messaging/to_autocomplete', data, function(response)
		{
			callback(response);
			$(".ui-helper-hidden-accessible").addClass("sr-only");
			$(".ui-autocomplete").css("max-height", "300px").css("overflow","auto");
		});
	},
	select: function(event, ui)
	{
		event.preventDefault();
		messaging_target_add("#message-template-to-contact-list", "recipient", ui.item, {x_details: "to"});
		this.value = '';
	}
});
$('#message-template-cc').messaging_catcomplete({
	source: function(data, callback)
	{
		data.driver = document.getElementById('message-template-driver').value;
		data.template = 1;
		$.get('/admin/messaging/to_autocomplete', data, function(response)
		{
			callback(response);
			$(".ui-helper-hidden-accessible").addClass("sr-only");
			$(".ui-autocomplete").css("max-height", "300px").css("overflow","auto");
		});
	},
	select: function(event, ui)
	{
		event.preventDefault();
		messaging_target_add("#message-template-cc-contact-list", "recipient", ui.item, {x_details: "cc", label_more: "email"});
		this.value = '';
	}
});
$('#message-template-bcc').messaging_catcomplete({
	source: function(data, callback)
	{
		data.driver = document.getElementById('message-template-driver').value;
		data.template = 1;
		$.get('/admin/messaging/to_autocomplete', data, function(response)
		{
			callback(response);
			$(".ui-helper-hidden-accessible").addClass("sr-only");
			$(".ui-autocomplete").css("max-height", "300px").css("overflow","auto");
		});
	},
	select: function(event, ui)
	{
		event.preventDefault();
		messaging_target_add("#message-template-bcc-contact-list", "recipient", ui.item, {x_details: "bcc", label_more: "email"});
		this.value = '';
	}
});