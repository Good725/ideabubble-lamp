<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<?php
$form = new IbForm('course-learning_outcome-edit', '/admin/courses/save_learning_outcome/'.$learning_outcome->id);
$form->load_data($learning_outcome);
$form->delete_url        = '/admin/courses/delete_item/learning_outcome/'.$learning_outcome->id;
$form->delete_permission = 'courses_course_edit';
$form->cancel_url        = '/admin/courses/learning_outcomes';

echo $form->start();
echo $form->hidden('id');
echo $form->action_buttons();
echo $form->end();
?>