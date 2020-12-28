<p>Someone has requested an enquiry download. The following details were submitted.</p>

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

<?php if (isset($form['message']) AND trim($form['message']) != ''): ?>
	<p><strong>Message</strong>: <?= nl2br($form['message']) ?></p>
<?php endif; ?>

<p>This email was sent from the following web address:<br/><?= URL::base() ?></p>