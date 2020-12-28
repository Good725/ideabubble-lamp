$(document).ready(initialize_dashboard);

function initialize_dashboard()
{
	// Remove default link functionality from move handle
	$('.widget-move-handle').on('click', function(ev) { ev.preventDefault(); });

	// Make it possible to drag and reorder gadgets
	$('#dashboard-wrapper.can_edit').find('.dashboard-layout-gadget-list').sortable(
	{
		placeholder : 'widget-move-placeholder',
		handle      : '.widget-move-handle, .widget-menu-bar, .mini-widget',
		cancel      : '.widget-actions-dropdown-toggle, widget-actions-dropdown, .mini-widget button, .mini-widget :input',
		connectWith : '.dashboard-layout-gadget-list',
		items       : '.dashboard-layout-gadget-list-item',
		update      : function(ev, ui) // called after a widget has been moved
		{
			// Save the order;
			save_gadget_order();

			// Widgets containing raw JavaScript might need to be refreshed
			if ($(ui.item.context).find('.widget_container').hasClass('widget_type-raw_html'))
			{
				$(ui.item.context).find('.widget_refresh_button').click();
			}

		}
	});
}

// When the "Add Gadget" modal appears, set an attribute to say which column it is referring to,
// depending on the link/button used to open it
$('#dashboard-gadget-modal').on('show.bs.modal', function (event)
{
	var $button = $(event.relatedTarget);
	var column = $button.data('column');
	$(this).attr('data-for_column', column);
});

// Temporary. This should eventually be replaced with an AJAX filter
$('.dashboard-gadget-modal-type').on('click', function(ev)
{
	ev.preventDefault();
	var type = this.getAttribute('data-type');

	if (type == 'all')
	{
		$('.gadget-list-item').show();
	}
	else
	{
		$('.gadget-list-item').hide();
		$('.gadget-list-item[data-'+type+'_id]').show();
	}
});

// When the modal is opened, load sparkline graphs, if they have not already been loaded
$('.dashboard-add-gadget-link').on('click', function()
{
	$('.gadget-list-item[data-sparkline_id]')
		.each(function()
		{
			var $thumbnail = $(this).find('.gadget-list-item-thumbnail');
			$thumbnail.load('/admin/reports/ajax_get_sparkline/'+this.getAttribute('data-sparkline_id'));
		});
});

// Add a sparkline to a column
$('.add-gadget-button-sparkline').on('click', function(ev)
{
	ev.preventDefault();
	var $modal        = $('#dashboard-gadget-modal');

	// Append the HTML
	var column_number = $modal[0].getAttribute('data-for_column');
	var $column       = $('.dashboard-layout-gadget-list[data-column="'+column_number+'"]');
	var sparkline_id  = this.getAttribute('data-item_id');
	var dashboard_id  = document.getElementById('view_dashboard_id').value;
	var report_id     = $(this).parents('.gadget-list-item').attr('data-report_id');

	$column[0].innerHTML += '<li class="dashboard-layout-gadget-list-item"></li>';
	$column.find('> li:last-child').load('/admin/reports/ajax_get_sparkline/'+sparkline_id, function()
	{
		// Save the relationship serverside
		$.ajax({
			url  : '/admin/dashboards/ajax_add_sparkline',
			data : {dashboard_id: dashboard_id, sparkline_id: sparkline_id, report_id: report_id, column: column_number},
			type : 'post'
	   	}).done(function(gadget_id)
			{
				$column.find('li:last-child').attr('data-id', gadget_id);

				// Save the order of gadgets server-side
				save_gadget_order();

				// Refresh the drag and drop JavaScript, so the newly added gadget can be moved
				$('.dashboard-layout-gadget-list').sortable('refresh');

				// Dismiss the modal
				$modal.modal('hide');
			});
	});

	// Dismiss the modal
	$modal.modal('hide');
});

// Add a report widget
$('.add-gadget-button-report_widget').on('click', function()
{
	var $modal        = $('#dashboard-gadget-modal');

	var column_number = $modal[0].getAttribute('data-for_column');
	var $column       = $('.dashboard-layout-gadget-list[data-column="'+column_number+'"]');
	var widget_id     = $(this).parents('.gadget-list-item').data('widget_id');
	var report_id     = $(this).parents('.gadget-list-item').data('report_id');
	var dashboard_id  = document.getElementById('view_dashboard_id').value;

	// Save the relationship serverside
	$('<div></div>').load('/admin/dashboards/ajax_add_report_widget', {dashboard_id: dashboard_id, widget_id: widget_id, report_id: report_id, column: column_number}, function()
	{
		// Append the gadget's HTML
		$column.append($(this).find('li').addClass('dashboard-layout-gadget-list-item'));

		// Save the order of gadgets server-side
		save_gadget_order();

		// Refresh the drag and drop JavaScript, so the newly added gadget can be moved
		$('.dashboard-layout-gadget-list').sortable('refresh');

		// Dismiss the modal
		$modal.modal('hide');
	});
});

// Remove a gadget (display confirmation prompt)
$(document).on('click', '.widget-action-remove', function(ev)
{
	ev.preventDefault();
	var id = $(this).parents('.dashboard-layout-gadget-list-item').data('id');
	document.getElementById('dashboard-remove-gadget-confirm').setAttribute('data-id', id);
	$('#dashboard-remove-gadget-modal').modal();
});

// Remove a gadget
$('#dashboard-remove-gadget-confirm').on('click', function()
{
	var button = this;
	var id     = button.getAttribute('data-id');

	// Remove the dashboard-gadget relationship serverside
	$.ajax('/admin/dashboards/ajax_remove_gadget/'+id)
		.done(function(result)
		{
			if (result == 1)
			{
				// remove the HTML
				$('.dashboard-layout-gadget-list-item[data-id="'+id+'"]').remove();
				// Dismiss the modal
				button.removeAttribute('data-id');
				$('#dashboard-remove-gadget-modal').modal('hide');
			}
			else
			{
				// Serverside error
				console.log(result);
			}
		});
});

// Refresh a a gadget
$(document).on('click', '.widget_refresh_button', function(ev)
{
	ev.preventDefault();
	var $container = $(this).parents('.widget_container');
	var report_id = $container.find('.widget_report_id').val();

	$.get('/admin/reports/ajax_refresh_widget/'+report_id, function(data)
	{
		$container.replaceWith(data);
	});
});

function save_gadget_order()
{
	/** Record the order and column of each gadget **/
	var gadgets = [];
	var column  = 0;
	var order   = 1;
	// Loop through each column
	$('.dashboard-layout-gadget-list').each(function()
	{
		// Loop through each gadget in the column
		$(this).find('.dashboard-layout-gadget-list-item').each(function()
		{
			gadgets.push({id: this.getAttribute('data-id'), column: column, order: order});
			order++;
		});
		column++;
	});

	/** Send the ordering data to the server to save **/
	$.post('/admin/dashboards/ajax_save_gadget_order', {gadgets: JSON.stringify(gadgets)});
}

// Display a message in the alert area
function set_message(message, type)
{
	document.getElementById('dashboard_view_alerts').innerHTML += '<div class="alert alert-'+type+'">'+message+'<a href="#" class="close" data-dismiss="alert">&times;</a></div>';
}

// AJAX load dashboards as their tabs are clicked
$('.dashboard-tab-link').on('click', function()
{
	// Change the active tab
	$('.dashboard-tab.active').removeClass('active');
	$(this).parents('li').addClass('active');

	// Load the content
	var href = this.getAttribute('data-url');
	$('#dashboard-wrapper').find('> .tab-content').load(href+' #dashboard-wrapper > .tab-content', function()
	{
		initialize_dashboard();

		// If any of the widgets specify additional JavaScript files that need to be loaded, load them if they have not already been loaded
		var additional_scripts = $('[data-load_scripts]').data('load_scripts');
		for (var i = 0; additional_scripts && i < additional_scripts.length; i++)
		{
			if ($('script[src="'+additional_scripts[i]+'"]').length == 0)
			{
				$.getScript(additional_scripts[i]);
			}
		}

		// Refresh the widgets
		$('.widget_refresh_button').each(function()
		{
			$(this).trigger('click');
		});
	});
});



/*------------------------------------*\
 #Date range picker
\*------------------------------------*/

/** deprecated - start **/

$('#widget-date-range-options').find('[data-range]').on('click', function(ev)
{
	ev.preventDefault();
	var from_input    = document.getElementById('widget-date-range-input-from');
	var to_input      = document.getElementById('widget-date-range-input-to');
	var custom_fields = document.getElementById('widget-date-range-custom');
	var date          = new Date();
	custom_fields.style.display = 'none';

	// Remove highlight from previously selected item
	$(this).parents('ul').find('.dropout-highlight').removeClass('dropout-highlight');

	switch (this.getAttribute('data-range'))
	{
		// Make input boxes available for custom entry
		case 'custom':
			custom_fields.style.display = 'block';
			break;

		// Put dates from chosen range into the input boxes
		default:
			var from_date = new Date();
			var   to_date = new Date();
			from_date.setDate(from_date.getDate() - this.getAttribute('data-minus'));
			from_input.value = from_date.getDate()+'-'+(from_date.getMonth()+1)+'-'+from_date.getFullYear();
			to_input.value =   to_date.getDate()+'-'+(  to_date.getMonth()+1)+'-'+  to_date.getFullYear();
			break;
	}

	// Display dates in the main box using the format "January 1, 2001"
	var to_date_parts   =   to_input.value.split('-');
	var from_date_parts = from_input.value.split('-');
	if (to_date_parts.length == 3 && from_date_parts.length == 3)
	{
		document.getElementById('widget-date-range-display-from').innerHTML = get_month_name(from_date_parts[1]-1)+' '+from_date_parts[0]+', '+from_date_parts[2];
		document.getElementById('widget-date-range-display-to'  ).innerHTML = get_month_name(  to_date_parts[1]-1)+' '+  to_date_parts[0]+', '+  to_date_parts[2];
	}

	// Highlight the selected option
	$(this).addClass('dropout-highlight');
});

$('#widget-date-range-cancel').on('click', function()
{
	document.getElementById('widget-date-range').querySelector('.expand-dropout.expanded').click();
});
$('#widget-date-range-apply').on('click', function()
{
	var date_from = document.getElementById('widget-date-range-input-from').value;
	var date_to   = document.getElementById('widget-date-range-input-to').value;
	window.old_map_div = document.getElementById("map");
	if(window.old_map_div){
		window.old_map_div.parentNode.removeChild(window.old_map_div);
	}
	$('#displayWidgets').load('/admin/reports/ajax_render_dashboard_reports?dashboard-from='+date_from+'&dashboard-to='+date_to);
	update_displayed_date();
	document.getElementById('widget-date-range').querySelector('.expand-dropout.expanded').click();
});

// Display dates in the main box using the format "January 1, 2001"
function update_displayed_date()
{
	var from_date_parts = document.getElementById('widget-date-range-input-from').value.split('-');
	var to_date_parts   = document.getElementById('widget-date-range-input-to'  ).value.split('-');

	if (to_date_parts.length == 3 && from_date_parts.length == 3)
	{
		document.getElementById('widget-date-range-display-from').innerHTML = get_month_name(from_date_parts[1]-1)+' '+from_date_parts[0].replace(/^[0]+/g,"")+', '+from_date_parts[2];
		document.getElementById('widget-date-range-display-to'  ).innerHTML = get_month_name(  to_date_parts[1]-1)+' '+  to_date_parts[0].replace(/^[0]+/g,"")+', '+  to_date_parts[2];
	}
}

function get_month_name(number)
{
	switch(number)
	{
		case 0:  return 'January';   break;
		case 1:  return 'February';  break;
		case 2:  return 'March';     break;
		case 3:  return 'April';     break;
		case 4:  return 'May';       break;
		case 5:  return 'June';      break;
		case 6:  return 'July';      break;
		case 7:  return 'August';    break;
		case 8:  return 'September'; break;
		case 9:  return 'October';   break;
		case 10: return 'November';  break;
		case 11: return 'December';  break;
		default: return '';          break;
	}
}

/** deprecated - end **/

$(document).ready(function()
{
	$('.popinit').popover({trigger:'hover', container: 'body'});

	var $dashboard_daterange = $('#reportrange');
	var dashboard_id = document.getElementById('view_dashboard_id').value;

	if ($dashboard_daterange.length > 0) {
		function rangepicker_cb(start, end, label) {
			var before = label ? label + ' (' : '';
			var after = label ? ')' : '';
			$('#reportrange-rangetext').html(before + start.format('MMMM D, YYYY') + ' &ndash; ' + end.format('MMMM D, YYYY') + after);

			// Previous and next buttons don't work for custom ranges
			$('#reportrange-prev, #reportrange-next').prop('disabled', (label == '' || label == 'Custom Range'));
		}

		rangepicker_cb(moment().subtract(1, 'year').add(1, 'day'), moment(), 'Year to today');

		$dashboard_daterange.daterangepicker({
			alwaysShowCalendars: true,
			locale: {
				format: 'YYYY-MM-DD'
			},
			autoapply: false,
			applyClass: 'btn btn-primary dashboard-daterange-apply',
			cancelClass: 'btn btn-subtle',
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'This Week': [moment().startOf('week'), moment().endOf('week')],
				'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'This Year': [moment().startOf('year'), moment().endOf('year')],
				'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
				'Year to today': [moment().subtract(1, 'year').add(1, 'day'), moment()]
			}
		}, rangepicker_cb);

		// return the the range type of the dashboard (Day, Week, Month, Year) and the number of days in the range
		$.fn.get_dashboard_range = function () {
			var label = $(this).data('daterangepicker').chosenLabel;
			var date_from = $(this).data('daterangepicker').startDate;
			var date_to = $(this).data('daterangepicker').endDate;
			var days_diff = date_from.diff(date_to, 'days');
			var range_type;

			if (typeof label != 'undefined') {
				var range_types = {
					'Today': 'day',
					'Yesterday': 'day',
					'This Week': 'week',
					'Last Week': 'week',
					'This Month': 'month',
					'Last Month': 'month',
					'This Year': 'year',
					'Last Year': 'year',
					'Year to today': 'year'
				};
				range_type = (typeof range_types[label] == 'undefined') ? label : range_types[label];
				range_type = range_type.charAt(0).toUpperCase() + range_type.substr(1); // capitalise the first letter
			}
			else {
				switch (Math.abs(days_diff)) {
					case 1   :
						range_type = 'Day';
						break;
					case 7   :
						range_type = 'Week';
						break;
					case 365 :
						range_type = 'Year';
						break;
					case 366 :
						range_type = 'Year';
						break;
					default  :
						range_type = '';
						break;
				}
			}

			return {"type": range_type, "days_diff": days_diff};
		};

		$dashboard_daterange.data('daterangepicker').setStartDate($dashboard_daterange.data('from'));
		$dashboard_daterange.data('daterangepicker').setEndDate($dashboard_daterange.data('to'));


		$dashboard_daterange.on('apply.daterangepicker', function (ev, picker) {
			if (typeof picker == 'undefined') picker = $(this).data('daterangepicker');
			var date_from = picker.startDate.format('YYYY-MM-DD');
			var date_to = picker.endDate.format('YYYY-MM-DD');
			var range = $(this).get_dashboard_range();

			window.old_map_div = document.getElementById("map");
			if (window.old_map_div) {
				window.old_map_div.parentNode.removeChild(window.old_map_div);
			}
			// window.location = '/admin/dashboards/view_dashboard/'+dashboard_id+'?dashboard-from='+date_from+'&dashboard-to='+date_to;
			var query_string = '?dashboard-from=' + date_from + '&dashboard-to=' + date_to + '&dashboard-range_type=' + range.type;
			$('#dashboard-layout').load('/admin/dashboards/view_dashboard/' + dashboard_id + query_string + ' #dashboard-layout', function (result) {
				$('#dashboard-layout').html($(result).find('#dashboard-layout').html());
			});
		}).trigger('apply.daterangepicker');

		$('#reportrange-prev, #reportrange-next').on('click', function () {
			var is_prev_btn = ($(this).attr('id') == 'reportrange-prev');
			var date_from = $dashboard_daterange.data('daterangepicker').startDate;
			var date_to = $dashboard_daterange.data('daterangepicker').endDate;
			var range = $dashboard_daterange.get_dashboard_range();

			$dashboard_daterange.data('daterangepicker').chosenLabel = range.type;

			if (range.type != '') {
				is_prev_btn ? date_from.subtract(1, range.type) : date_from.add(1, range.type);
				is_prev_btn ? date_to.subtract(1, range.type) : date_to.add(1, range.type);
			}
			else {
				is_prev_btn ? date_from.subtract(range.days_diff, 'days') : date_from.add(range.days_diff, 'days');
				is_prev_btn ? date_to.subtract(range.days_diff, 'days') : date_to.add(range.days_diff, 'days');
			}

			$dashboard_daterange.data('daterangepicker').setStartDate(date_from.format('YYYY-MM-DD'));
			$dashboard_daterange.data('daterangepicker').setEndDate(date_to.format('YYYY-MM-DD'));
			$dashboard_daterange.trigger('apply.daterangepicker');
			rangepicker_cb(date_from, date_to, range.type);
		});
	}
});


$(document).on('show.bs.modal', '.survey_question_group-answers_modal', function(ev)
{
    console.log(this);
    var responses = $(ev.relatedTarget).parents('.survey_question_group-question').find('.survey_question_group-view_responses').html();
    $(this).find('.modal-body').html(responses);
});


