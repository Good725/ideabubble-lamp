$(document).ready(function() {
    // CKEditor Configuration
    CKEDITOR.replaceAll(function (textarea, config) {

            // Toolbar settings
            config.toolbar =
            [
                ['Font', 'FontSize'],
                ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                ['TextColor','BGColor']
            ];

            // Editor width
            config.width = '538px';
        }
    );

    // Listener for '.remove-recipient'
    $('.recipients').on('click', '.remove-recipient', function(e) {
        $(this).parent().remove();
    });

    // Listener for '.to'
    $('.to').click(function() {
        var id        = $(this).data('id');
        var email     = $(this).data('email');
        var eventName = $(this).data('event-name');

        // HTML Code
        html = '<span class="recipient"><button class="close remove-recipient" type="button">×</button>' + email + '<input type="hidden" name="to[]" value="' + id + '"/>' + '</span>';

        $('#to_recipients' + '-' + eventName).append(html);
    });

    // Listener for '.cc'
    $('.cc').click(function() {
        var id        = $(this).data('id');
        var email     = $(this).data('email');
        var eventName = $(this).data('event-name');

        // HTML Code
        html = '<span class="recipient"><button class="close remove-recipient" type="button">×</button>' + email + '<input type="hidden" name="cc[]" value="' + id + '"/>' + '</span>';

        $('#cc_recipients' + '-' + eventName).append(html);
    });

    // Listener for '.bcc'
    $('.bcc').click(function() {
        var id        = $(this).data('id');
        var email     = $(this).data('email');
        var eventName = $(this).data('event-name');

        // HTML Code
        html = '<span class="recipient"><button class="close remove-recipient" type="button">×</button>' + email + '<input type="hidden" name="bcc[]" value="' + id + '"/>' + '</span>';

        $('#bcc_recipients' + '-' + eventName).append(html);
    });

	// When the "confirm deletion" modal appears, put the ID of the notification to be deleted onto the "confirm delete" button
	$('#delete-notification-modal').on('show.bs.modal', function (ev)
	{
		var button = ev.relatedTarget;
		var id = button.getAttribute('data-id');
		document.getElementById('confirm-delete-notification').setAttribute('data-id', id);
	});

	// When the "confirm delete" button is clicked, go to the delete controller
	$('#confirm-delete-notification').on('click', function()
	{
		window.location = '/admin/notifications/delete/'+this.getAttribute('data-id');
	});

    $('#form_new_notification, .form-horizontal').validationEngine();

    $("button[type=reset]").on("click", function(){
        CKEDITOR.instances.description.setData("");
    });
});