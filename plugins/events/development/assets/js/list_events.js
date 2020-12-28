$(document).ready(function()
{
	// Server-side datatable
	var $table = $('#list-events-table');

	$table.ready(function()
	{
			var ajax_source = '/admin/events/datatable';
			var settings = {
				"bSort" : false,
				"sPaginationType" : "bootstrap",
				"aoColumnDefs": [{
					"aTargets": [1],
					"fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
					{
						// Add data attribute, with the contact ID to each row
						$(nTd).parent().attr({'data-id': oData[0]});
						$(nTd).find('[data-toggle="popover"]').popover();
					}
				}]
			};
			$table.ib_serverSideTable(ajax_source, settings);
	});

	// Search by individual columns
	$table.find('.search_init').on('change', function ()
	{
		$table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
	});

	// Toggle the publish state
	$table.on('click', '.publish-btn', function()
	{
		var $this       = $(this);
		var id          = this.getAttribute('data-id');
		var old_publish = parseInt(this.getElementsByClassName('publish-value')[0].innerHTML);
		var new_publish = (old_publish + 1) % 2;
		$.ajax('/admin/events/ajax_toggle_event_publish/'+id+'?publish='+new_publish).done(function(result)
		{
			$this.find('.publish-value').html(new_publish);
			if (new_publish == 1)
			{
				$this.find('.publish-icon').removeClass('icon-ban-circle').addClass('icon-ok');
			}
			else
			{
				$this.find('.publish-icon').removeClass('icon-ok').addClass('icon-ban-circle');
			}

		});
	});

	// Open the delete modal and pass the relevant review's ID into it
	$table.on('click', '.list-delete-button', function()
	{
		var id = $(this).data('id');
		$($(this).data('target')).find('[name="id"]').val(id);
	});

	$("#csv_filter_ticket_type_id").multiselect({nonSelectedText: 'All'}).hide();

	$table.on('click', '.btn-link.download-attendees', function(){
		var event_id = $(this).data('id');
		var date_id = $(this).data('date_id');
		$("#csv_filter_ticket_type_id").html($(this).find('select').html());
		$("#csv_filter_ticket_type_id").multiselect('rebuild').hide();
		$('#download-attendees-modal [name=id]').val(event_id);
		$('#download-attendees-modal [name=date_id]').val(date_id);
	});

	$table.find('[data-toggle="popover"]').popover();

	// Let the email attendees modal know which event it is referring to.
	// (Get the ID corresponding to the button used to open the modal.)
	$('#email-attendees-modal').on('show.bs.modal', function (ev)
	{
		var button = ev.relatedTarget;
		if (button && button.getAttribute('data-id'))
		{
			var event_id = button.getAttribute('data-id');
            var date_id = button.getAttribute('data-date_id');

			$.ajax('/admin/events/ajax_get_event/'+event_id + "?date_id=" + date_id)
				.done(function(result)
				{
					result = JSON.parse(result);
					$('#email-attendees-event_id').val(result.event.id);
                    $('#email-attendees-date_id').val(date_id);
					var $bcc =$('#email-attendees-bcc');
					$bcc.val(result.event.name+' attendees');
					$bcc.data('content', result.attendee_html).attr('data-content', result.attendee_html);

					$bcc.popover({content: function() { return $(this).data('content') }});
				})
				.fail(function()
				{
					$('#email-attendees-modal').modal('hide');
				});
		}
		else {
			return false;
		}
	});

	$("#status-event-form button[type=submit]").on("click", function(){
        this.form.clickedButton = this;
	});

	$("#status-event-form").on("submit", function(e){
        e.preventDefault();
        var form = this;
        var data = $(this).serialize();
        $.post(
            this.action,
            data,
            function (response) {
                if (form.clickedButton.id == "update-email-event-button") {
                    try {
                        $("#status-event-modal").modal('hide');
                        $("#email-attendees-modal").modal('show');
                        $('[data-target="#email-attendees-modal"][data-id=' + response.id + ']').click();
                        $("#email-attendees-modal [name=subject]").val("Event: " + response.name + " is " + response.status);
                        $("#email-attendees-modal [name=message]").val(
                            response.status_reason + "<br />" +
                            '<a href="http://' + window.location.host + '/event/' + response.url + '">' + response.url + '</a>'
                        );
                        CKEDITOR.instances['email-attendees-message'].updateElement();
                        CKEDITOR.instances['email-attendees-message'].setData(
                            response.status_reason + "<br />" +
                            '<a href="http://' + window.location.host + '/event/' + response.url + '">' + response.url + '</a>'
                        );
                        CKEDITOR.instances['email-attendees-message'].updateElement();
                    } catch (exc) {
                        console.log(exc);
                    }
                } else {
                    window.location.reload();
                }
            }
        );
        return false;
    });


	$("#download-attendees-form button[type=submit]").on("click", function(){
		$("#download-attendees-modal").modal('hide');
	});
});
