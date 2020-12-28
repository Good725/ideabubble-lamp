<p>Someone has requested a demonstration, using the following details.</p>

<?php if (isset($form['name']) AND trim($form['name']) != ''): ?>
	<p><strong>Name</strong>: <?= $form['name'] ?></p>
<?php endif; ?>

<?php if (isset($form['email']) AND trim($form['email']) != ''): ?>
	<p><strong>Email</strong>: <?= $form['email'] ?></p>
<?php endif; ?>

<?php if (isset($form['company']) AND trim($form['company']) != ''): ?>
	<p><strong>Company</strong>: <?= $form['company'] ?></p>
<?php endif; ?>

<?php if (isset($form['phone']) AND trim($form['phone']) != ''): ?>
	<p><strong>Phone</strong>: <?= $form['phone'] ?></p>
<?php endif; ?>

<?php if (isset($form['interested_in']) AND trim($form['interested_in']) != ''): ?>
	<p><strong>Interested in</strong>: <?= $form['interested_in'] ?></p>
<?php endif; ?>

<p><strong>Contact this customer to arrange a
		demo?</strong>: <?= (isset($form['contact_me']) AND (!is_null($form['contact_me'])) AND (trim($form['contact_me']) != '')) ? 'Yes' : 'No' ?>
</p>

<p>This email was sent from the following web address:<br/><?= URL::base() ?></p>