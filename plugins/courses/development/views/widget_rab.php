<div class="rab_wrapper">
	<h3>Room Allocation Board (RAB)</h3>
	<div class="rab_filters">
		<div class="rab_filter_wrapper">
			<label for="rab_filter-location">Locations / Rooms</label>
			<select class="rab_filter" id="rab_filter-location" name="room">
				<?php
				$location_html = $all_location_ids = '';
				foreach ($locations as $location)
				{
					$all_location_ids .= $location['id'].',';
					$location_html    .= '<option value="'.(rtrim($location['id'].','.$location['sublocations'], ',')).'">'.$location['name'].'</option>';
				}
				?>
				<option value="<?= rtrim($all_location_ids,',') ?>">-- All Rooms --</option>
				<?= $location_html ?>
			</select>
		</div>

		<div class="rab_filter_wrapper">
			<label for="rab_filter-trainer">Teachers</label>
			<select class="rab_filter" id="rab_filter-trainer">
				<?php
				$trainer_html = $all_trainer_ids = '';
				foreach ($trainers as $trainer)
				{
					$all_trainer_ids .= $trainer['id'].',';
					$trainer_html     .= '<option value="'.$trainer['id'].'">'.(trim($trainer['first_name'].' '.$trainer['last_name'])).'</option>';
				}
				?>
				<option value="<?= rtrim($all_trainer_ids,',') ?>">-- All Teachers --</option>
				<?= $trainer_html ?>
			</select>
		</div>
	</div>

	<div class="rab-results-wrapper">
		<ul class="rab-view_picker">
			<li>
				<a href="#" class="rab-view_picker-master" id="rab-view_picker-master"
				   data-start_date="<?= date('Y-m-d', strtotime('Monday this week')) ?>"
				   data-end_date="<?= date('Y-m-d', strtotime('Monday next week')) ?>"
					>Master</a>
			</li>
			<?php $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'); ?>
			<?php foreach ($days as $day): ?>
				<li>
					<a href="#" class="rab-view_picker-day<?= ($day == date('l')) ? ' rab-view_picker-selected' : '' ?>"
					   data-start_date="<?= date('Y-m-d', strtotime($day.' this week')) ?>"
					   data-end_date="<?= date('Y-m-d', strtotime($day.' this week + 1 day')) ?>"
						><?= $day ?></a>
				</li>
			<?php endforeach ?>
		</ul>

		<div class="rab-report_view">
			<div id="rab-report_view-daily">
				<table class="table datatable table-striped rab-day_view_table" id="rab-day_view_table"></table>
			</div>
			<div id="rab-report_view-master"></div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		// Show the table for whichever option is selected, on the initial load
		$('.rab-view_picker-selected').click();
	});

	// Change the table content when a specific day option or the master option is clicked
	$(document).on('click', '.rab-view_picker-master, .rab-view_picker-day', function(ev)
	{
		ev.preventDefault();

		// Change the selected option
		$('.rab-view_picker-selected').removeClass('rab-view_picker-selected');
		$(this).addClass('rab-view_picker-selected');

		refresh_rab_table();
	});
	$(document).on('change', '.rab_filter', refresh_rab_table);

	function refresh_rab_table()
	{
		var selected = document.getElementsByClassName('rab-view_picker-selected')[0];

		// Data to send to the server
		var data = {};
		data.id  = <?= $report->get_id() ?>;
		data.sql = <?= json_encode($report->get_sql()) ?>;
		data.parameters = 'parameter_id_|date|From|'+selected.getAttribute('data-start_date') +
			',parameter_id_|date|To|'+selected.getAttribute('data-end_date') +
			',parameter_id_|text|LocationIDs|'+(document.getElementById('rab_filter-location').value.replace(/,/g,'&#44;')) +
			',parameter_id_|text|TrainerIDs|'+(document.getElementById('rab_filter-trainer').value.replace(/,/g,'&#44;'));

		if (selected.id == 'rab-view_picker-master')
		{
			// Run the report query server-side and return the master-view table
			$.post('/admin/courses/ajax_get_rab_master_view/<?= $report->get_id() ?>', data, function(result)
			{
				// Put the table HTML in the widget
				document.getElementById('rab-report_view-master').innerHTML = result;
				// Switch between master and daily view
				document.getElementById('rab-report_view-daily').style.display = 'none';
				document.getElementById('rab-report_view-master').style.display = 'block';
			});
		}
		else
		{
			// Run the report query server-side and return the daily-view table
			$.post('/admin/reports/get_report_table', data, function(result)
			{
				// Put the table HTML in the widget
				$('#rab-day_view_table').html(result).prepend('<caption>'+selected.innerHTML+' view'+'</caption>');
				// Switch between master and daily view
				document.getElementById('rab-report_view-master').style.display = 'none';
				document.getElementById('rab-report_view-daily').style.display = 'block';
			});
		}
	}

</script>
<style>
	.rab_wrapper {
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		overflow-x: scroll;
		padding: .5em;
	}
	.rab_filters {
		text-align: center;
	}
	.rab_filter_wrapper {
		display: inline-block;
		text-align: left;
		margin: 0 1em;
	}
	.rab-results-wrapper {
		display: table;
		width: 100%;
	}
	.rab-view_picker,
	.rab-report_view {
		display: table-cell;
	}
	.rab-view_picker {
		list-style: none;
	}
	.rab-report_view {
		border: 1px solid #aaa;
	}
	.rab-report_view {
		width: 100%
	}
	.rab-report_view table {
		width: 100%;
	}
	.rab-report_view caption {
		font-weight: bold;
	}
	.rab-report_view a:hover {
		text-decoration: underline;
	}
	.rab-day_view_table th:first-child {
		width: 6em;
	}
	.rab-day_view_table th:nth-child(2) {
		width: 8em;
	}
	.rab-day_view_table tfoot {
		display: none;
	}
	.rab-view_picker-master,
	.rab-view_picker-day{
		background: #eee;
		border: solid #aaa;
		border-width: 1px 0 1px 1px;
		display: block;
		margin-bottom: 1px;
		padding: .2em;
	}
	.rab-view_picker-selected {
		background: #fff;
		position: relative;
		left: 1px;
	}
	.rab-view_picker a:hover {
		text-decoration: none;
	}
</style>