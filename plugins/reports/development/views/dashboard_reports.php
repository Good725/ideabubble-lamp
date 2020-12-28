<div id="dashboard_report_widgets">
	<?= $widgets ?>
	<script>
		// Hacky fix for charts being overly clipped
		$('#displayWidgets').find('[clip-path]').removeAttr('clip-path');

		<?php if (trim($widgets != '')): ?>
			$(function()
			{
				// Make it possible to drag and reorder widgets
				$('#dashboard_report_widgets').sortable(
				{
					handle: '.widget-menu-bar, .widget-move-handle',
					cancel: '.widget-actions-dropdown-toggle, widget-actions-dropdown',
					update: function(ev, ui) // called after a widget has been moved
					{
						// Store the widget IDs in the order they appear.
						var report_ids = [];
						$('.widget_report_id').each(function() { report_ids.push(this.value); });
						// Send the data to the server to save as the order as the user's preference
						$.post('/admin/reports/ajax_save_order', { report_ids: report_ids });

						// Widgets containing raw JavaScript might need to be refreshed
						if ($(ui.item.context).hasClass('widget_type-raw_html'))
						{
							$(ui.item.context).find('.widget_refresh_button').click();
						}

					}
				});

				$('.widget-move-handle').on('click', function(ev) { ev.preventDefault(); });
				$('.widget_refresh_button').on('click', function(ev)
				{
					ev.preventDefault();
					var $container = $(this).parents('.widget_container');
					var report_id = $container.find('.widget_report_id').val();

					$.get('/admin/reports/ajax_refresh_widget/'+report_id, function(data)
					{
						$container.replaceWith(data);
					});
				});
			});
		<?php endif; ?>
	</script>
	<script src="//platform.twitter.com/widgets.js"></script>
</div>
