<div style="margin: 0 -15px;">
	<?php if ($event['dates'] AND count($event['dates']) > 0): ?>
		<div class="col-md-4">
			<h4>Times</h4>
			<ul class="list-unstyled">
				<?php foreach ($event['dates'] as $date): ?>
					<li><?= date('H:i D M Y', strtotime($date['starts'])) ?> &ndash; <?= date('H:i D M Y', strtotime($date['ends'])) ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if ($event['venue']): ?>
		<?php
		$address = $event['venue']['address_1']."\n".$event['venue']['address_2']."\n".$event['venue']['address_3']."\n".$event['venue']['city'];
		$address = trim(nl2br(preg_replace("/[\r\n]+/", "\n", $address))); // remove excessive line breaks, then convert the remainder to <br>
		?>
		<?php if ($address): ?>
			<div class="col-md-4">
				<h4>Address</h4>
				<address><?= $address ?></address>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($event['organizers'] AND count($event['organizers']) > 0): ?>
		<div class="col-md-4">
			<h4>Organisers</h4>
			<ul class="list-unstyled">
				<?php foreach ($event['organizers'] as $organizer): ?>
					<li><?= $organizer['first_name'] ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>