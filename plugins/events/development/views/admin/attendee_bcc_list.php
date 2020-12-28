<?php if ( ! empty($attendees)): ?>
	<?php $listed = array(); ?>
	<table class="table">
		<tbody>
			<?php foreach ($attendees as $attendee): ?>
				<?php if ( ! in_array($attendee['email'], $listed)): // prevent duplicate emails being listed ?>
					<tr>
						<td><?= trim($attendee['firstname'].' '.$attendee['lastname']) ?></td>
						<td><?= $attendee['email'] ?></td>
					</tr>
					<?php $listed[] = $attendee['email']; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>