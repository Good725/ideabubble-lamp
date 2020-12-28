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

<?php if (isset($form['pharmacy_id']) AND $form['pharmacy_id'] != '' AND is_numeric($form['pharmacy_id'])): ?>
	<?php $pharmacy = Model_Location::get($form['pharmacy_id']) ?>
	<p><b>Pharmacy</b>: <?= $pharmacy['title'] ?></p>
<?php endif; ?>

<?php if (isset($form['items_needed']) AND trim($form['items_needed']) != ''): ?>
	<p><b>Items Needed</b>: <?= $form['items_needed'] ?></p>
<?php endif; ?>
