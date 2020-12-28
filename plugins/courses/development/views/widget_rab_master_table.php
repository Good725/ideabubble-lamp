<table class="table datatable table-striped">
	<caption>Master View</caption>
	<thead>
		<tr>
			<th scope="col">Day</th>
			<?php // Create columns for each room; one for timeslots and one for classes ?>
			<?php foreach ($rooms as $room): ?>
				<th scope="col"><?= is_null($room) ? 'No room defined' : $room ?></th>
				<th scope="col">Subject</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($data as $day => $day_data): ?>
			<tr>
				<?php // day column cell ?>
				<th scope="row"><?= $day ?></th>

				<?php foreach ($rooms as $room): ?>
					<?php if (isset($day_data[$room])): ?>
						<?php // timeslots column cell ?>
						<td>
							<?php foreach ($day_data[$room] as $class): ?>
								<div><?= $class['time'] ?></div>
							<?php endforeach; ?>
						</td>
						<?php // class column cell ?>
						<td>
							<?php foreach ($day_data[$room] as $class): ?>
								<div><?= $class['class'] ?></div>
							<?php endforeach; ?>
						</td>
					<?php else: ?>
						<td></td>
						<td></td>
					<?php endif; ?>
				<?php endforeach ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
