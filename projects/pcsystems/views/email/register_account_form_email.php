<? $form_identifier = (isset($post['form_identifier']) AND trim ($post['form_identifier']) != '') ? $post['form_identifier'] : 'register_account_'; ?>

Name: <?=@$form[$form_identifier.'form_name']?><br/>
Address: <?=@$form[$form_identifier.'form_address']?><br/>
Phone: <?=@$form[$form_identifier.'form_tel']?><br/>
Email: <?=@$form[$form_identifier.'form_email_address']?><br/>
