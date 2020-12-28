<? $form_identifier = '';?>

<b>Order number:</b> <?= (isset($form['order_no']))     ? $form['order_no']       : ''; ?><br />
<b>First Name:</b> <?=   (isset($form['first_name']))   ? $form['first_name']     : ''; ?><br />
<b>Surname:</b> <?=      (isset($form['surname']))      ? $form['surname']        : ''; ?><br />
<b>Address:</b> <?=      (isset($form['address']) AND is_string($form['address']))  ? nl2br($form['address']) : ''; ?><br />
<b>County:</b> <?=       (isset($form['county']))       ? $form['county']         : ''; ?><br />
<b>County:</b> <?=       (isset($form['country']))      ? $form['country']        : ''; ?><br />
<b>Postcode:</b> <?=     (isset($form['postcode']))     ? $form['postcode']       : ''; ?><br />
<b>Email:</b> <?=        (isset($form['email']))        ? $form['email']          : ''; ?><br />
<b>Telephone:</b> <?=    (isset($form['phone']))        ? $form['phone']          : ''; ?><br />

<?php if (isset($form['return_action']) AND $form['return_action'] == 'refund'): ?>
	<br />
	<br />
	<b>Action:</b> Refund<br />
	<b>Reason:</b> <?= (isset($form['refund_message']) AND is_string($form['refund_message'])) ? nl2br($form['refund_message']) : '' ?>
<?php endif; ?>

<?php if (isset($form['return_action']) AND $form['return_action'] == 'replacement'): ?>
	<br />
	<br />
	<b>Action:</b> Replacement<br />
	<?php if (isset($form['replacement_reason']) AND $form['replacement_reason'] == 'wrong_size'): ?>
		<b>Reason:</b> Wrong size<br />
		<b>Correct Size:</b> <?= isset($form['correct_size']) ? $form['correct_size'] : '' ?><br />
	<?php endif; ?>

	<?php if (isset($form['replacement_reason']) AND $form['replacement_reason'] == 'other'): ?>
		<b>Reason:</b> Other: <?= (isset($form['other_replacement_reason']) AND is_string($form['other_replacement_reason'])) ? nl2br($form['other_replacement_reason']) : '' ?><br />
	<?php endif; ?>
<?php endif; ?>
<br />


<b>Ship to:</b> <?= (isset($form['ship_to'])) ? $form['ship_to'] : ''; ?><br />
<b>Payment Method:</b> <?= (isset($form['payment_method'])) ? $form['payment_method'] : ''; ?><br /><br />


<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</p>

