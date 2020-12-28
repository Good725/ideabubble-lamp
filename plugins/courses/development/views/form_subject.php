<?php $data = (isset($_POST) && count($_POST) > 0) ? $_POST : (isset($data) ? $data : []) ?>
<div><?= (isset($alert)) ? $alert : '' ?></div>

<?php
$form = new IbForm('form_add_edit_subject', '/admin/courses/save_subject');
$form->type = 'subject';
$form->name_field = 'name';
$form->load_data($data);
$form->cancel_url = '/admin/courses/subjects';
$form->delete_url = '/admin/courses/delete_subject/'.$data['id'];
$form->delete_permission = 'courses_subject_edit';

$cycles = ['Junior' => 'Junior', 'Transition' => 'Transition', 'Senior' => 'Senior'];
$uploader_args = ['duplicate' => 0, 'onsuccess' => 'subject_image_uploaded', 'preset' => 'courses', 'presetmodal' => 'no', 'single' => true];

echo $form->start();
echo $form->hidden('id');

echo $form->multiselect('Cycle', 'cycle[]', $cycles, @$data['cycles']);
echo $form->textarea('Summary', 'summary');
echo $form->colorpicker('Colour', 'color');
echo $form->image_uploader('Image', 'image', null, [], $uploader_args);
echo $form->input('Order', 'order');


echo $form->action_buttons();
echo $form->end();
?>
