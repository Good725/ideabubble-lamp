<? $form_identifier = (isset($post['form_identifier']) AND trim ($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_'; ?>

<?php if (isset($form) AND isset($form[$form_identifier.'form_name'])): ?>
	Name: <?=@$form[$form_identifier.'form_name']?><br/>
<?php endif; ?>
<?php if (isset($form) AND isset($form[$form_identifier.'form_address'])): ?>
	Address: <?= isset($form[$form_identifier.'form_address']) ? nl2br($form[$form_identifier.'form_address']) : '' ?><br/>
<?php endif; ?>
<?php if (isset($form) AND isset($form[$form_identifier.'form_tel'])): ?>
	Phone: <?=@$form[$form_identifier.'form_tel']?><br/>
<?php endif; ?>
<?php if (isset($form) AND isset($form[$form_identifier.'form_email_address'])): ?>
	Email: <?=@$form[$form_identifier.'form_email_address']?><br/>
<?php endif; ?>
<?php if (isset($form) AND isset($form[$form_identifier.'form_message'])): ?>
	Message: <?= nl2br($form[$form_identifier.'form_message']) ?><br />
<?php endif ?>
<?php
if(isset($_POST[$form_identifier.'request_for']))
{
    echo 'Request For: '.@$form[$form_identifier.'request_for'].'<br />';
}
if(isset($_POST[$form_identifier.'hear_from']))
{
    echo 'Where did you hear about us: '.@$form[$form_identifier.'hear_from'].'<br />';
}
?>
<?php if (isset($_POST[$form_identifier.'form_date_range_from']) OR isset($_POST[$form_identifier.'form_date_range_to'])): ?>
	<?php $date_from = (isset($_POST[$form_identifier.'form_date_range_from']) AND $_POST[$form_identifier.'form_date_range_from'] != '') ? date('d F Y', strtotime(date::dmy_to_ymd($_POST[$form_identifier.'form_date_range_from']))) : ''; ?>
	<?php $date_to   = (isset($_POST[$form_identifier.'form_date_range_to'])   AND $_POST[$form_identifier.'form_date_range_to']   != '') ? date('d F Y', strtotime(date::dmy_to_ymd($_POST[$form_identifier.'form_date_range_to'])))   : ''; ?>
	Date range: <?= $date_from ?> &ndash; <?= $date_to ?><br />
<?php endif; ?>

<?php if (!empty($form['interested_in_course']) && !empty($form['interested_in_course']['id'])): ?>
    Interested in: Course #<?= $form['interested_in_course']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_course']['title']) ?><br />
<?php endif; ?>

<?php if (!empty($form['interested_in_schedule']) && !empty($form['interested_in_schedule']['id'])): ?>
    Interested in: Schedule #<?= $form['interested_in_schedule']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_schedule']['name']) ?><br />
<?php endif; ?>