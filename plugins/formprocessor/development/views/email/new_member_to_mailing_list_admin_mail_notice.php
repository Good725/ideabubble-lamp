<? $form_identifier = (isset($form['form_identifier']) AND trim ($form['form_identifier']) != '') ? $form['form_identifier'] : 'contact_'; ?>

<p>
	<b>New Member has been Added to your Mailing List</b>
</p>

<p>
	Name: <?=@$form[$form_identifier.'form_name']?><br/>
	Email: <?=@$form[$form_identifier.'form_email_address']?>
</p>


