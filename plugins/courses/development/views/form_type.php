<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="col-sm-12">
	<?=(isset($alert)) ? $alert : ''?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
</div>

<?php
$form = new IbForm('form_add_edit_type', '/admin/courses/save_type/');
$form->name_field = 'type';
$form->load_data(isset($data) ? $data : []);
$form->cancel_url = 'admin/courses/types';
$form->delete_permission = 'courses_type_edit';
$form->delete_url = '/admin/courses/remove_type/'.(isset($data['id']) ? $data['id'] : '');

echo $form->start();
echo $form->hidden('id');
echo $form->hidden('redirect');
echo $form->colorpicker('Colour', 'color');
echo $form->textarea('Summary', 'summary');
echo $form->action_buttons();
echo $form->end();
?>