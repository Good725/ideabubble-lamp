<? $form_identifier = (isset($form['form_identifier']) AND trim ($form['form_identifier']) != '') ? $form['form_identifier'] : 'contact_'; ?>

<p>
	<b>Thank you <?=@$form[$form_identifier.'form_name']?> for subscribing to our newsletter. We will be in touch at your <?=@$form[$form_identifier.'form_email_address']?> address through out the year.</b>
</p>


