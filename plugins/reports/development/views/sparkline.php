<?php $is_comparison_total = $sparkline->chart_type->stub == 'comparison_total'; ?>
<div class="mini-widget-wrapper">
	<div class="mini-widget clearfix<?= $is_comparison_total? ' widget-comparison_totals' : '' ?>" style="background-color: <?= $sparkline->background_color ?>;color:<?= $sparkline->text_color ?>;">

		<button type="button" class="btn-link widget-action-remove">
			<span class="icon-times"></span>
		</button>

		<?php if (Auth::instance()->has_access('reports')): ?>
			<a class="widget-action-view" href="/admin/reports/read/<?= $sparkline->report_id ?>" title="View report">
				<span class="sr-only">View report</span>
				<span class="icon-eye"></span>
			</a>
		<?php endif; ?>

		<?php if ($sparkline->chart_type->stub == 'single_value'): ?>
			<div class="col-sm-12">
				<span class="chart-info-widget-<?= $sparkline->chart_type->stub ?>"><?= $graph_points ?></span>
			</div>
		<?php elseif ($sparkline->chart_type->stub == 'total'): ?>
			<div class="text-center">
				<div class="chart-info-widget-<?= $sparkline->chart_type->stub ?>">
					<h3><?= $sparkline->title ?></h3>
					<span><?= $currency.number_format($total) ?></span>
					<?php if ($sparkline->dashboard_link_id): ?>
                        <hr />
						<a class="sparkline-dashboard-link" href="/admin/dashboards/view_dashboard/<?= $sparkline->dashboard_link_id ?>">View Dashboard</a>
					<?php endif; ?>
				</div>
			</div>
		<?php else: ?>
			<div class="mini-widget-overlay">
				<h4><?= $sparkline->title ?></h4>

				<?php if ( ! $is_comparison_total): ?>
					<span>Total <strong><?= number_format($total) ?></strong></span>
				<?php else: ?>
					<span class="widget-total"><?= $currency.number_format($total) ?></span>

					<?php $range = ($comparisons['range'] == 'Custom') ? $comparisons['days_in_range'].' days' : strtolower($comparisons['range']); ?>
					<?php $percentage_increase = ($comparisons['prev_total'] == 0) ? '' : number_format(round(($total / $comparisons['prev_total'] - 1) * 100, 2)); ?>

					<div class="widget-comparison_totals-comparisons">
						<div>
							<span class="widget-comparison-amount"><?= $currency.number_format($comparisons['prev_total']) ?></span>
							<br />
							<span class="widget-comparison-text">Previous&nbsp;<?= $range ?></span>
						</div>

						<div>
							<?php if ($percentage_increase != '' AND $percentage_increase > 0): ?>
								<span class="arrow-icon left icon-arrow-up"></span>
							<?php elseif ($percentage_increase != '' AND $percentage_increase < 0): ?>
								<span class="arrow-icon left icon-arrow-down"></span>
							<?php endif; ?>

							<span class="widget-comparison-amount"><?= $percentage_increase ? $percentage_increase : '&mdash;' ?>%</span>
							<br />
							<span class="widget-comparison-text">Since last <?= $range ?></span>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! $is_comparison_total): ?>
				<div class="col-md-4">
				</div>
				<div class="col-md-8">
					<div class="mini-widget-chart">
						<span class="chart-info-widget-<?= $sparkline->chart_type->stub ?>"><?= $graph_points ?></span>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<script>
	$(document).on("ready", function(){
		if (typeof sparkline_ns !== 'undefined') {
			sparkline_ns.load_sparklines();
		}
	});
</script>
