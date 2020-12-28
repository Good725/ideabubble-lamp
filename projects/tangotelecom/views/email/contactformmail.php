<?php $form_identifier = (isset($post['form_identifier']) AND trim($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_'; ?>

Name: <?= isset($form[$form_identifier.'form_name']) ? $form[$form_identifier.'form_name'] : '' ?><br/>
Email: <?= isset($form[$form_identifier.'form_email_address']) ? $form[$form_identifier.'form_email_address'] : '' ?>
<br/>
Company: <?= isset($form[$form_identifier.'form_company']) ? $form[$form_identifier.'form_company'] : '' ?><br/>
Phone: <?= isset($form[$form_identifier.'form_tel']) ? $form[$form_identifier.'form_tel'] : '' ?><br/>
Address: <?= isset($form[$form_identifier.'interested_in']) ? $form[$form_identifier.'interested_in'] : '' ?><br/>
Message: <?= isset($form[$form_identifier.'form_message']) ? nl2br($form[$form_identifier.'form_message']) : '' ?>

<p>This email was sent from the following web address:<br/><?= URL::base() ?></p>