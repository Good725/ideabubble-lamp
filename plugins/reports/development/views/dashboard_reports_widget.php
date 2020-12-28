<?php
$widget_record_count = $max_x_elements = 0;
$widget_title = $report->get_widget_name() ? $report->get_widget_name() : $report->get_name();

$combined_widget = (($report->sparkline->id && $report->sparkline->publish) && $report->get_widget_id());
?>
<div
	class="widget_container<?=
    $combined_widget ? ' widget_container-combined'           : '' ?><?=
	// Add extra classes for map and RAB widgets
	$is_map ? ' custom_widget_container map_widget_container' : '' ?><?=
	$is_rab ? ' custom_widget_container rab_widget_container' : '' ?><?=
	// Add the widget type as a class
	' widget_type-'.$widget_type->stub?>"
	<?php // number of widgets per row ?>
	data-per_row="<?= ($is_map OR $is_rab) ? 1 : $per_row ?>"
	>
    <?php if ($combined_widget): ?>
        <?= $report->sparkline->render(); ?>
    <?php else: ?>
        <div class="widget-menu-bar">
            <span class="widget-title" title="<?= $widget_title ?>"><?= $widget_title ?></span>
            <div class="widget-actions">
                <a href="#" title="Drag to reorder" type="button" class="icon-move widget-move-handle"></a>
                <a href="#" class="widget-actions-dropdown-toggle">
                    <span class="icon icon-angle-down" aria-hidden="true"></span>
                </a>

                <ul class="widget-actions-dropdown">
                    <li><a href="#" class="widget_minimize_button">Minimise</a></li>
                    <li><a href="#" class="widget-action-remove">Delete</a></li>
                    <li><a href="/admin/reports/add_edit_report/<?= $report->get_id() ?>" class="widget-action-edit">Details</a></li>
                    <?php if ( ! $is_rab AND ! $is_map): ?>
                        <li><a href="#" class="widget_refresh_button">Refresh</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

	<?php if ($custom_body !== FALSE): ?>
		<div class="widget-body"><?=$custom_body ?></div>
	<?php else: ?>
		<div class="widget-body" style="overflow-x: auto;overflow-y: hidden;">
            <?php
			$all_widget_data = $widget_data = $report->run_report(true);
			$widget_record_count = $all_widget_data ? count($all_widget_data) : 0;
			$widget_width = "auto";
			$modal_widget_width = 248;
			$max_x_elements = 5;
			$show_widget_modal_link = false;
			if ($report->get_widget_type() == 2 && $widget_record_count > $max_x_elements) { // bar graph
				$widget_data = array_slice($widget_data, 0, $max_x_elements);
				$modal_widget_width = 50 * $widget_record_count;
				$show_widget_modal_link = true;
			}
			?>

			<div class="highcharts-wrapper flex-center" style="padding: 0 7px;min-width:248px; width: <?=$widget_width?>; <?=$report->get_widget_type() >= 5 ? '':'height:'.($combined_widget ? '150' : '248').'px;';?>" id="widget_<?=$report->get_widget_id();?>">
				<?php if ($widget_data): ?>
					<script><?= $widget_js = $report->get_widget_json($widget_data) ?></script>
				<?php else: ?>
					<h3>No data available</h3>
				<?php endif; ?>
			</div>
			<?php if ($show_widget_modal_link) { ?>
			<a class="widget-more-link" data-toggle="modal" data-target="#modal_widget_<?=$report->get_widget_id();?>">more...</a>
			<?php } ?>
		</div>

        <?php if ($combined_widget): ?>
            <div class="widget-body widget-body-extra">
                <?= $report->get_widget_extra_text(); ?>
            </div>
        <?php endif; ?>

	<?php endif; ?>
	<input type="hidden" class="widget_report_id" value=<?= $report->get_id() ?> />
</div>
<?php
if ($widget_record_count > $max_x_elements) {
	$modal_widget_js = str_replace(
		'widget_' . $report->get_widget_id(),
		'big_widget_' . $report->get_widget_id(),
		$report->get_widget_json($all_widget_data)
	);
?>
<div id="modal_widget_<?=$report->get_widget_id();?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_widget_<?=$report->get_widget_id();?>" aria-hidden="true">
	<div class="modal-dialog" style="width: 90%;">
		<div class="modal-content" style="">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?= $report->get_widget_name() ? $report->get_widget_name() : $report->get_name() ?></h3>
			</div>
			<div class="modal-body form-horizontal" style="overflow: auto">
				<div id="big_widget_<?=$report->get_widget_id()?>" style="width: <?=$modal_widget_width?>px; max-height:600px"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		<?=$modal_widget_js?>
	});
</script>
	<?php
}
?>