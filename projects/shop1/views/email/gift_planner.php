<?php if (isset($form['name']) AND trim($form['name']) != ''): ?>
	<p><b>Name</b>: <?= $form['name'] ?></p>
<?php endif; ?>

<?php if (isset($form['address']) AND trim($form['address']) != ''): ?>
    <p><b>Address</b>:<br /><?= nl2br($form['address']) ?>
<?php endif; ?>

<?php if (isset($form['mobile']) AND trim($form['mobile']) != ''): ?>
	<p><b>Mobile</b>: <?= $form['mobile'] ?></p>
<?php endif; ?>

<?php if (isset($form['email']) AND trim($form['email']) != ''): ?>
	<p><b>Email</b>: <?= $form['email'] ?></p>
<?php endif; ?>

<?php if (isset($form['events']) AND count($form['events']) > 0): ?>
	<p><b>Events</b></p>
	<?php foreach ($form['events'] as $event): ?>
		<p><?= $event ?><?= isset($form[$event.'_date']) ? ' &ndash; '.$form[$event.'_date'] : '' ?></p>
	<?php endforeach; ?>
<?php endif; ?>

<?php if (isset($form['custom_events']) AND count($form['custom_events']) > 0): ?>
	<p><b>Custom events</b></p>
	<?php
	$i = 0;
	foreach ($form['custom_events'] as $event)
	{
		if (isset($form['custom_events_dates'][$i]) AND trim($form['custom_events_dates'][$i]) != '')
		{
			echo '<p>'.$event.(isset($form['custom_events_dates'][$i] ) ? ' &ndash; '.$form['custom_events_dates'][$i] : '').'</p>';
		}
		$i++;
	}
	?>
<?php endif; ?>