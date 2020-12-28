<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<?php
$form = new IbForm('todos-category-edit', '/admin/todos/save_category/'.$category->id);
$form->load_data($category);
$form->delete_url = '/admin/todos/delete_item/category/'.$category->id;
$form->delete_permission = 'todos_course_edit';
$form->cancel_url = '/admin/todos/categories';

echo $form->start();
echo $form->hidden('id');

echo $form->action_buttons();
echo $form->end();
?>