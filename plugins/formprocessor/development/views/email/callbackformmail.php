<?php if (isset($form['callback_form_name']) OR isset($form['contact_form_name'])): ?>
	Name: <?= isset($form['callback_form_name']) ? $form['callback_form_name'] : $form['contact_form_name'] ?><br />
<?php endif; ?>

<?php if (isset($form['callback_form_tel']) OR isset($form['contact_form_tel'])): ?>
	Telephone: <?= isset($form['callback_form_tel']) ? $form['callback_form_tel'] : $form['contact_form_tel'] ?><br />
<?php endif; ?>

<?php if (isset($form['callback_form_email']) OR isset($form['contact_form_email'])): ?>
	Email: <?= isset($form['callback_form_email']) ? $form['callback_form_email'] : $form['contact_form_email'] ?><br />
<?php endif; ?>

<?php if (isset($form['callback_form_email_address']) OR isset($form['contact_form_email_address'])): ?>
	Email: <?= isset($form['callback_form_email_address']) ? $form['callback_form_email_address'] : $form['contact_form_email_address'] ?><br />
<?php endif; ?>

<?php if (isset($form['callback_form_comments']) OR isset($form['contact_form_comments'])): ?>
	Message: <?= nl2br(isset($form['callback_form_comments']) ? $form['callback_form_comments'] : $form['contact_form_comments']) ?><br />
<?php endif; ?>
