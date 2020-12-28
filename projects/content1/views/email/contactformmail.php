<h3>Contact Form Request</h3>

<?php if (isset($form['contact_form_name'])): ?>
	Name: <?= $form['contact_form_name'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_address'])): ?>
	Address: <?= $form['contact_form_address'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_tel'])): ?>
	<?php if (Kohana::$config->load('config')->get('db_id') == 'lsomusic'): ?>
		Enquiring about: <?= $form['contact_form_tel'] ?><br/>
	<?php else: ?>
		Telephone: <?= $form['contact_form_tel'] ?><br/>
	<?php endif; ?>
<?php endif; ?>

<?php if (isset($form['contact_form_email_address'])): ?>
	Email: <?= $form['contact_form_email_address'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_event_date'])): ?>
	Event date: <?= $form['contact_form_event_date'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_venue'])): ?>
	Venue: <?= $form['contact_form_venue'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_comments'])): ?>
	Comments: <?= $form['contact_form_comments'] ?>
<?php endif; ?>

<?php if (isset($form['contact_form_message'])): ?>
	Message: <?= $form['contact_form_message'] ?>
<?php endif; ?>

<?php if (isset($form['contact_form_request_for'])): ?>
	Request for: <?= $form['contact_form_request_for'] ?><br/>
<?php endif; ?>

<?php if (isset($form['contact_form_hear_about'])): ?>
	Where did you hear about us: <?= $form['contact_form_hear_about'] ?><br/>
<?php endif; ?>


<?php if (isset($form['name'])): ?>
	<strong>Name:</strong> <?= $form['name'] ?><br/>
<?php endif; ?>

<?php if (isset($form['phone'])): ?>
	<strong>Phone:</strong> <?= $form['phone'] ?><br/>
<?php endif; ?>

<?php if (isset($form['address'])): ?>
	<strong>Address:</strong> <br/><?= nl2br(''.$form['address']) ?><br/>
<?php endif; ?>

<?php if (isset($form['email'])): ?>
	<strong>Email:</strong> <?= $form['email'] ?><br/>
<?php endif; ?>

<?php if (isset($form['message'])): ?>
	<strong>Message:</strong> <br/><?= nl2br(''.$form['message']) ?><br/>
<?php endif; ?>

<?php if (isset($form['location'])): ?>
	<strong>Location:</strong> <?= $form['location'] ?><br/>
<?php endif; ?>

<?php if (isset($form['request_for'])): ?>
	<strong>Request for:</strong> <?= $form['request_for'] ?><br/>
<?php endif; ?>

<?php if (isset($form['hear_about'])): ?>
	Where did you hear about us: <?= $form['hear_about'] ?><br/>
<?php endif; ?>

<?php if (!empty($form['interested_in_course']) && !empty($form['interested_in_course']['id'])): ?>
    Interested in: Course #<?= $form['interested_in_course']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_course']['title']) ?><br />
<?php endif; ?>

<?php if (!empty($form['interested_in_schedule']) && !empty($form['interested_in_schedule']['id'])): ?>
    Interested in: Schedule #<?= $form['interested_in_schedule']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_schedule']['name']) ?><br />
<?php endif; ?>