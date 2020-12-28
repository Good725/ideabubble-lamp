$(document).on("ready", function(){
	var check_notifications_period = 60; // seconds

	function check_notifications()
	{
		var prev_xhr_hide = window.disableScreenDiv.hide;
		window.disableScreenDiv.hide = false;
		var time = new Date();
		$.get( (window.location.href.indexOf("admin") != -1 ? "/admin" : "/frontend") + "/messaging/check_notifications?time=" + Math.round(time.getTime() / 1000),
			function(response)
			{
				if (response.amount)
				{
					$(".user-tools-wrapper .messages_icon").addClass("has-new-message");
					$('#message-notifications-amount').html(response.amount);
				}
				else
				{
					$(".user-tools-wrapper .messages_icon").removeClass("has-new-message");
					$('#message-notifications-amount').html('');
				}

				var $notifications_area = $('.user-tools-messages');
				// (response.html == '') ? $notifications_area.hide() : $notifications_area.show();

				$("#message-notification-list").html(response.html);
				setTimeout(check_notifications, check_notifications_period * 1000);
		});
		window.disableScreenDiv.hide = prev_xhr_hide;
	}
	if ($("#message-notification-list").length > 0) {
		check_notifications();
	}

	// Show notification details when its chevron is clicked from the notifications dropdown
	$(document).on('click', '#user-notifications-list > li', function(ev)
	{
		ev.preventDefault();
		var id = $(this).data('message-id');
		$('#message-notification-view_message').load((window.location.href.indexOf("admin") != -1 ? "/admin" : "/frontend") + '/messaging/ajax_view_message/'+id, function()
		{
			//$(this).show();
			// hide the list of notifications
			//$('#message-notification-list').hide();
			$('#user-notifications-dropout').hide();
			/* mark comment as read */
			$.ajax('/admin/messaging/ajax_mark_as_read/'+id)
				.done(function()
				{
					// Refresh the notification tray
					check_notifications();
					// Refresh the messaging list, if it exists
					$('#list_messages_table').find('.search_init').trigger('change');

				});
			$('.user-notifications-wrapper').hide();
			var $sidebar = $('#messaging-sidebar');
			$sidebar.removeClass('hidden').show().trigger(':ib-popup-open');
			$.when($('.messaging-sidebar-open_list[data-name="inbox"]').click())
				.then($('#messaging-sidebar-messages').find('.medialist>li[data-id="'+id+'"]').click());
		});
	});

    // Show message tray when the icon is clicked
    $('#user-tools-messaging-expand').on('click', function()
    {
        $('#user-notifications-dropout').show();

        // Take note of when the notifications were last manually checked.
        $.get('/frontend/messaging/update_notifications_checked_time')
            .done(function() {
                // Hide "new messages" notice (even if there are unread messages.)
                $('#message-notifications-amount').html('');
            });
    });

	// Hide the message tray when clicked away from
	$('body').on('click', function(ev)
	{
		if ( ! $(ev.target).closest('#user-tools-messaging-expand').length && ! $(ev.target).closest('#user-notifications-dropout').length)
		{
			$('#user-notifications-dropout').hide();
		}
	});

    // return from showing notification details to showing the notifications list
	$(document).on('click', '#user-notifications-return', function(ev)
	{
		ev.preventDefault();
		$('#message-notification-list').show();
		$('#message-notification-view_message').hide();
	});
});
