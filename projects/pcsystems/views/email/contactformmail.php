<? $form_identifier = (isset($post['form_identifier']) AND trim ($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_'; ?>

Name: <?=@$form[$form_identifier.'form_name']?><br/>
Address: <?=@$form[$form_identifier.'form_address']?><br/>
Phone: <?=@$form[$form_identifier.'form_tel']?><br/>
Email: <?=@$form[$form_identifier.'form_email_address']?><br/>
Message: <?=@$form[$form_identifier.'form_message']?>
<?php
if(isset($_POST[$form_identifier.'request_for']))
{
    echo 'Request For: '.@$form[$form_identifier.'request_for'];
}
if(isset($_POST[$form_identifier.'hear_from']))
{
    echo 'Where did you hear about us: '.@$form[$form_identifier.'hear_from'];
}
?>