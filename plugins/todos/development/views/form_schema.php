<style>
    .schema-edit-grades-table-wrapper { overflow-y: auto; }
    .schema-edit-grades-table .form-input { min-width: 5em; }
    .schema-edit-grades-table .form-select .form-input { min-width: 10em; }
</style>
<?php
$form = new IbForm('schema-edit', '/admin/todos/save_schema/'.$schema->id);
$form->load_data($schema);
$form->delete_url = '/admin/todos/delete_schema/'.$schema->id;
$form->delete_permission = 'grades_edit';
$form->cancel_url = '/admin/todos/schemas';

echo $form->start();
echo $form->hidden('id');

$selected = $schema->get_grades();
$args = [
    'allow_new' => true,
    'extra_columns' => [
        'percent_min' => 'Min %',
        'percent_max' => 'Max %',
        'subject_id'  => ['type' => 'select', 'options' => $subjects, 'title' => 'Subject']
    ] + $level_columns,
    'title_field' => 'grade',
    'orderable' => true,
];
echo $form->multiselect_table('Grade', 'grades', [], $selected, [], $args);

echo $form->action_buttons();
echo $form->end();
?>

