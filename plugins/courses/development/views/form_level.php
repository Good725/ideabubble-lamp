<?= (isset($alert)) ? $alert : '' ?>
<?php
$data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : []);

$form = new IbForm('form_add_edit_level', '/admin/courses/save_level/');
$form->name_field = 'level';
$form->delete_url = '/admin/courses/remove_level/'.(isset($data['id']) ? $data['id'] : '');
$form->delete_permission = 'courses_level_edit';
$form->cancel_url = '/admin/courses/levels';
$form->load_data($data);

echo $form->start();
echo $form->hidden('id');
echo $form->hidden('redirect');
echo $form->input('Short name', 'short_name', null, ['placeholder' => 'e.g. "H" for "Higher"']);
echo $form->textarea('Summary', 'summary');
echo $form->numeric_input('Order', 'order');
echo $form->action_buttons();
echo $form->end();
?>
