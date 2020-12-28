<?php if (count($user_favorites) > 0): ?>
	<div class="dashboard-tabs" id="dashboard-tabs">
		<ul class="nav nav-tabs">
			<?php foreach ($user_favorites as $user_favorite): ?>
				<li class="dashboard-tab<?= ($user_favorite->dashboard->id == $dashboard->id) ? ' active' : '' ?>">
					<a href="#" class="dashboard-tab-link" data-url="/admin/dashboards/view_dashboard/<?= $user_favorite->dashboard->id?>"><?= $user_favorite->dashboard->title ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
<div id="dashboard-wrapper" class="dashboard-wrapper <?= empty($edit_mode) ? '' : ' edit_mode' ?><?= $dashboard->user_has_edit_permission() ? ' can_edit' : '' ?>">
	<div<?= (count($user_favorites) > 0) ? ' class="tab-content"' : '' ?>>
		<div id="dashboard_view_alerts"></div>
		<input type="hidden" name="id" id="view_dashboard_id" value="<?= $dashboard->id ?>" />
		<?= isset($alert) ? $alert : '' ?>
		<?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>
		<div>
			<div class="view-dashboard-title">
				<h2><?= $dashboard->title ?></h2>
			</div>

			<div class="dashboard-tools">
				<?php if ( ! empty($edit_mode)): ?>
					<button type="button" class="btn btn-actions dashboard-tool-add-gadget" data-toggle="modal" data-target="#dashboard-gadget-modal" data-column="1">
						<span class="icon-plus"></span> Add Gadget
					</button>
				<?php endif; ?>

				<?php if ($dashboard->user_has_edit_permission()): ?>
					<div class="action-btn">
						<a class="btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="icon-ellipsis-h" aria-hidden="true"></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-right view-dashboard-dropdown" aria-labelledby="view-dashboard-icons">
							<?php if (empty ($edit_mode)): ?>
								<li><a href="/admin/dashboards/add_edit_dashboard">Add Dashboard</a></li>
								<li><a href="/admin/dashboards/add_edit_dashboard/<?= $dashboard->id ?>">Edit Dashboard</a></li>
							<?php else: ?>
								<li><a href="/admin/dashboards/view_dashboard/<?= $dashboard->id ?>">View Dashboard</a></li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>

			</div>

			<hr style="clear: both;" />

			<?php if ($dashboard->date_filter): ?>
				<div class="clearfix reportrange-wrapper" style="clear: both;text-align:center;">
					<?php
					$date_from  = isset($_GET['dashboard-from'])       ? $_GET['dashboard-from']       : date('Y-m-d', strtotime(date('Y-m-d').' -1 year + 1 day'));
					$date_to    = isset($_GET['dashboard-to'])         ? $_GET['dashboard-to']         : date('Y-m-d');
					$range_type = isset($_GET['dashboard-range_type']) ? $_GET['dashboard-range_type'] : 'Year to today';
					?>

					<button type="button" disabled class="btn btn-default btn-subtle" id="reportrange-prev"><span class="icon-chevron-left"></span></button>

					<button type="button" id="reportrange" class="btn btn-default btn-subtle" data-from="<?= $date_from ?>" data-to="<?= $date_to ?>" data-range_type="<?= $range_type ?>">
						<span class="icon-calendar"></span>&nbsp;
						<span id="reportrange-rangetext"><?= $range_type.' ('.(date('F j, Y', strtotime($date_from))) ?> &ndash; <?= date('F j, Y', strtotime($date_to)).')' ?></span>
					</button>

					<button type="button" disabled class="btn btn-default btn-subtle" id="reportrange-next"><span class="icon-chevron-right"></span></button>
				</div>
			<?php endif; ?>

		</div>

		<div class="dashboard-layout clearfix" id="dashboard-layout">
			<div class="col-sm-12 dashboard-layout-top-widget">
				<ul class="dashboard-layout-gadget-list" data-column="0">
					<li class="dashboard-layout-gadget-list-blank">
						<a href="#" class="dashboard-add-gadget-link" data-toggle="modal" data-target="#dashboard-gadget-modal" data-column="0">add gadget</a>
					</li>
					<?php $gadget_index = 0; ?>
					<?php for (; $gadget_index < count($gadgets) AND isset($gadgets[$gadget_index]) AND $gadgets[$gadget_index]->column == '0'; $gadget_index ++): ?>
						<?php $sparkline_id = isset($gadget_html[0]->sparkline) ? $gadget_html[0]->sparkline->id : '' ?>
						<li class="dashboard-layout-gadget-list-item" data-id="<?= $gadgets[0]->id ?>"<?= $sparkline_id ? ' data-sparkline_id="'.$sparkline_id.'"' : '' ?> data-order="<?= $gadget_index+1 ?>">
							<?= isset($gadget_html[0]) ? $gadget_html[0] : '' ?>
						</li>
					<?php endfor; ?>
				</ul>
			</div>
			<div class="dashboard-layout-columns">
				<?php for ($col_index = 1; $col_index <= $dashboard->columns; $col_index++): ?>
					<div class="col-sm-<?= 12 / $dashboard->columns ?>">
						<ul class="dashboard-layout-gadget-list" data-column="<?= $col_index ?>">
							<li class="dashboard-layout-gadget-list-blank">
								<a href="#" class="dashboard-add-gadget-link" data-toggle="modal" data-target="#dashboard-gadget-modal" data-column="<?= $col_index ?>">add a new gadget</a>
							</li>
							<?php for (; $gadget_index < count($gadgets) AND isset($gadgets[$gadget_index]) AND $gadgets[$gadget_index]->column == $col_index; $gadget_index ++): ?>
								<?php $sparkline_id = isset($gadget_html[$gadget_index]->sparkline) ? $gadget_html[$gadget_index]->sparkline->id : '' ?>
								<li class="dashboard-layout-gadget-list-item" data-id="<?= $gadgets[$gadget_index]->id ?>"<?= $sparkline_id ? ' data-sparkline_id="'.$sparkline_id.'"' : '' ?> data-order="<?= $gadget_index + 1 ?>">
									<?= isset($gadget_html[$gadget_index]) ? $gadget_html[$gadget_index] : '' ?>
								</li>
							<?php endfor; ?>
						</ul>
					</div>
				<?php endfor; ?>
			</div>
		</div>

	</div>

	<div class="modal fade dashboard-gadget-modal" id="dashboard-gadget-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-gadget-modal-title">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="dashboard-gadget-modal-title">Add a gadget</h4>
				</div>
				<div class="modal-body clearfix">
					<div class="col-sm-3 dashboard-gadget-modal-types">
						<ul>
							<li><a href="#" class="dashboard-gadget-modal-type" data-type="all">All</a></li>
							<li><a href="#" class="dashboard-gadget-modal-type" data-type="widget">Widgets</a></li>
							<li><a href="#" class="dashboard-gadget-modal-type" data-type="sparkline">Sparklines</a></li>
						</ul>
					</div>
					<div class="col-sm-9">
						<div class="dashboard-gadget-modal-list">
							<!-- Sparklines -->
							<?php foreach ($sparklines as $sparkline): ?>
								<div class="gadget-list-item" data-report_id="<?= $sparkline->report_id ?>" data-sparkline_id="<?= $sparkline->id ?>">
									<div class="col-sm-3 gadget-list-item-thumbnail"></div>
									<div class="col-sm-7 gadget-list-item-description">
										<h4><?= $sparkline->title ?></h4>
										<p>Summary</p>
									</div>
									<div class="col-sm-2 gadget-list-item-actions">
										<button type="button" class="btn btn-default add-gadget-button add-gadget-button-sparkline" data-item_id="<?= $sparkline->id ?>">Add gadget</button>
									</div>
								</div>
							<?php endforeach; ?>


							<!-- Widgets -->
							<?php foreach ($report_widgets as $widget): ?>
								<div class="gadget-list-item"  data-report_id="<?= $widget->get_id() ?>" data-widget_id="<?= $widget->get_widget_id() ?>">
									<div class="col-sm-3 gadget-list-item-thumbnail">
										<img width="137" height="60" src="" />
									</div>
									<div class="col-sm-7 gadget-list-item-description">
										<h4><?= $widget->get_widget_name() ? $widget->get_widget_name() : $widget->get_name() ?></h4>
										<p>Report summary</p>
									</div>
									<div class="col-sm-2 gadget-list-item-actions">
										<button type="button" class="btn btn-default add-gadget-button add-gadget-button-report_widget">Add gadget</button>
									</div>
								</div>
							<?php endforeach; ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="dashboard-remove-gadget-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirm removal</h4>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to remove this gadget?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="dashboard-remove-gadget-confirm">Yes</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</div>
</div>
