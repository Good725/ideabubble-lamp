<? $form_identifier = (isset($post['form_identifier']) AND trim ($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_'; ?>

Name: <?=@$form[$form_identifier.'form_name']?><br/>
Address: <?=@$form[$form_identifier.'form_address']?><br/>
Phone: <?=@$form[$form_identifier.'form_tel']?><br/>
Email: <?=@$form[$form_identifier.'form_email_address']?><br/>
Message: <?=@$form[$form_identifier.'form_message']?><br />
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

<?php if (!empty($form['interested_in_course']) && !empty($form['interested_in_course']['id'])): ?>
    Interested in: Course #<?= $form['interested_in_course']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_course']['title']) ?><br />
<?php endif; ?>

<?php if (!empty($form['interested_in_schedule']) && !empty($form['interested_in_schedule']['id'])): ?>
    Interested in: Schedule #<?= $form['interested_in_schedule']['id'] ?>: <?= htmlspecialchars(@$form['interested_in_schedule']['name']) ?><br />
<?php endif; ?>