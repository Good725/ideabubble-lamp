<div class="alert_area"><?= isset($alert) ? $alert : IbHelpers::get_messages() ?></div>
<div class="">
<?php
$form = new IbForm('ccsaas-host-form', '/api/ccsaas/edit_bserver', 'post', ['layout' => 'vertical']);
$form->data_object = $data;
echo $form->start(['title' => false]);

//$form->id = $data['id'];
echo $form->hidden('id');
echo $form->input('Host', 'host', $data->host, ['placeholder' => 'Host']);
echo $form->input('IP', 'ip4', $data->ip4, ['placeholder' => 'IP']);
?>
<?php
$form->delete_permission = 'ccsaas_edit';
$form->delete_url = '/api/ccsaas/delete_bserver';
echo $form->action_buttons();
echo $form->end();
?>
</div>