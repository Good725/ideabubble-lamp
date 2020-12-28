$(document).on("ready", function()
{
	$("#edit-homework-schedule").autocomplete({
		select: function(e, ui)
		{
			$('#edit-homework-schedule').val(ui.item.label);
			$('#edit-homework-schedule').data("schedule-id", ui.item.value);
			$("#edit-homework-schedule-event-id").html('<option value="">Select</option>');
			$("#edit-homework-schedule-event-id").val("");
			$.get(
				"/admin/courses/autocomplete_schedule_events",
				{schedule_id: ui.item.value},
				function(response)
				{
					for (var i in response)
					{
						$("#edit-homework-schedule-event-id").append(
							'<option value="' + response[i].value + '">' + response[i].label + '</option>'
						);
					}
				}
			);
			return false;
		},

		source: function(data, callback)
		{
			$.get(
				"/admin/courses/autocomplete_schedules",
				data,
				function(response)
				{
					callback(response);
				}
			);
		}
	});
	var settings = {
		"sPaginationType": 'bootstrap',
		'aLengthMenu': [10, 25, 50, 100],
		'iDisplayLength': 10,
	};
	var drawback_settings ={
		"fnDrawCallback": function() {
			// Add the necessary data attributes for the responsive dataTable
			var $table = $(this);
			var headings = [];

			// Get the text inside each column heading
			$table.find('thead:first th').each(function() {
				headings.push($(this).text());
			});

			// Add the heading text to the cells as a data attribute
			var i, $cols;
			$table.find('tbody tr').each(function() {
				$cols = $(this).find('td');
				for (i = 0; i < $cols.length; i++) {
					$($cols[i]).data('label', headings[i]).attr('data-label', headings[i]);
				}
			});
		}
	};
	$('#homework_table').ib_serverSideTable('/admin/homework/homeworks_list_data', settings, drawback_settings);
	$('#homework_table')
		// Open link by clicking on the row
		.on('click', 'tbody tr', function(ev)
			{
				// If the clicked element is a link or form element or is inside one, do nothing
				if (!$(ev.target).is('a, label, button, :input') && !$(ev.target).parents('a, label, button, :input')[0])
				{
					// Find the edit link
					var link = $(this).find('.edit-link').attr('href');

					// If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab.
					// Otherwise open it in the same tab
					if (ev.ctrlKey || ev.metaKey || ev.which == 2)
					{
						window.open(link, '_blank');
					}
					else
					{
						window.location.href = link;
					}
				}
			})
		.on('click', '.delete-button', function()
			{
				var id = $(this).attr('data-id');
				$('#delete-homework-id').val(id);
				$('#delete-homework-modal').modal();
			});

	$("#homework-edit").on("submit", function()
	{
		if ($("#edit-homework-title").val() == "")
		{
			alert("Please enter homework title");
			return false;
		}
		if (!$("#edit-homework-schedule").data("schedule-id"))
		{
			alert("Please select a schedule");
			return false;
		}
		if (!$("#edit-homework-schedule-event-id").val())
		{
			alert("Please select a time");
			return false;
		}
		return true;
	});

	$("#delete-homework-modal-btn").on("click", function()
	{
		var id = $(this).data("id");

		$.post('/admin/homework/ajax_homework_delete/' + id, function(data, status)
		{
			if (status == 'success')
			{
				window.location.replace("/admin/homework");
			}
		});
	});

    $('#homework_table').on('click', '.action-btn > a', function() {
        $(this).toggleClass('open');
        $(this).siblings('.action-btn ul').slideToggle();
        return false;
    });
});
