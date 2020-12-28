<? $form_identifier = 'register_account_'; ?>
Company: <?=@$form['account_type'] . ' ' . @$form['account_type_other']?><br/>
Name: <?=@$form['firstname'] . ' ' . @$form['surname']?><br/>
Company: <?=@$form['company']?><br/>
Address: <?=@$form['address_line1'] . ' ' . $form['address_line2'] . "<br>" . $form['county'] . "/" . $form['country']?><br/>
Phone: <?=@$form['phone']?><br/>
Email: <?=@$form['email']?><br/>
Where did you hear about us: <?=@$form['heard_from']?>
