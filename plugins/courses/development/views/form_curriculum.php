<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<?php
$form = new IbForm('course-curriculum-edit', '/admin/courses/save_curriculum/'.$curriculum->id);
$form->load_data($curriculum);
$form->delete_url = '/admin/courses/delete_item/curriculum/'.$curriculum->id;
$form->delete_permission = 'courses_course_edit';
$form->cancel_url = '/admin/courses/curriculums';

echo $form->start();
echo $form->hidden('id');

$form->tab_start('Details', 'active');
    echo $form->wysiwyg('Summary', 'summary');

$form->tab_start('Specs');
    $options = html::optionsFromRows('id', 'title', $specs);
    $selected = $curriculum->specs->find_all_undeleted();
    echo $form->multiselect_table('Spec', 'specs', $options, $selected);

$form->tab_start('Learning outcomes');
    $options = html::optionsFromRows('id', 'title', $learning_outcomes);
    $selected = $curriculum->get_learning_outcomes();
    $args = ['allow_new' => true, 'orderable' => true];
    echo $form->multiselect_table('Learning outcome', 'learning_outcomes', $options, $selected, [], $args);

$form->tab_start('Content');
    echo $curriculum->content->render_editor([
        'edit_button_at_depth' => 1,
        'learning_outcomes' => $curriculum->get_learning_outcomes()
    ]);

echo $form->tabs();
echo $form->action_buttons();
echo $form->end();
?>