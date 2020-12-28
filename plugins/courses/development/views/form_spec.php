<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<?php
$subject_options        = html::optionsFromRows('id', 'name',  $subjects,          $spec->subject_id);
$qqi_component_options  = html::optionsFromRows('id', 'label', $qqi_components,    $spec->qqi_component_id);
$provider_options       = html::optionsFromRows('id', 'name',  $providers,         $spec->provider_id);
$requirement_options    = html::optionsFromRows('id', 'label', $requirement_types, $spec->requirement_type_id);
$grading_schema_options = html::optionsFromRows('id', 'title', $grading_schemas,   $spec->grading_schema_id);
$product_options        = html::optionsFromRows('id', 'title', $products);
$credit_type_options    = array_combine($credit_types, $credit_types);
$learning_methodology_options = html::optionsFromRows('id', 'label', $learning_methodologies);

$form = new IbForm('course-spec-edit', '/admin/courses/save_spec/'.$spec->id);
$form->load_data($spec);
$form->delete_url = '/admin/courses/delete_item/spec/'.$spec->id;
$form->delete_permission = 'courses_course_edit';
$form->cancel_url = '/admin/courses/specs';

echo $form->start();
echo $form->hidden('id');
?>

<?php $form->tab_start('Details', 'active'); ?>
    <?php
    echo $form->input('Code', 'code');
    echo $form->input('Version', 'version');
    echo $form->combobox('Module', 'subject_id', $subject_options);
    echo $form->combobox('QQI component', 'qqi_component_id', $qqi_component_options, null, [], ['allow_new' => true]);
    echo $form->select('Provider', 'provider_id', $provider_options);
    echo $form->select('Requirement', 'requirement_type_id', $requirement_options);
    echo $form->select('Grading schema', 'grading_schema_id', $grading_schema_options);
    echo $form->wysiwyg('Summary', 'summary');
    echo $form->wysiwyg('Aims', 'aims');
    echo $form->wysiwyg('Assessment methods and guidelines', 'assessment_methods');
    ?>

<?php $form->tab_start('Methodologies') ?>
    <?php
    $selected = $spec->get_learning_methodologies();
    $args = ['allow_new' => true, 'orderable' => true, 'title_field' => 'label'];
    echo $form->multiselect_table('Learning methodology', 'learning_methodologies', $learning_methodology_options, $selected, [], $args);
    ?>

<?php $form->tab_start('Material') ?>
    <p>Select an existing product from the dropdown or type the name of a new product or URL.</p>

    <?php
    $selected = $spec->get_recommended_material();
    $args = ['allow_new' => true, 'orderable' => true];
    $attributes = ['data-placeholder' => 'Select a product or type a URL'];
    echo $form->multiselect_table('Material', 'recommended_material', $product_options, $selected, $attributes, $args);
    ?>

<?php $form->tab_start('Time'); ?>
    <?php
    $extra_columns = [];
    foreach ($study_modes as $study_mode) {
        $extra_columns['hours_'.$study_mode->id] = ['title' => $study_mode->study_mode, 'attributes' => ['type' => 'number'], 'args' => ['right_icon' => '<span title="hours">h</span>']];
    }
    $args = ['extra_columns' => $extra_columns, 'id_field' => 'type', 'title_field' => 'type'];
    $selected = $spec->get_credits_by_type();
    echo $form->multiselect_table('Type', 'time_allocations', $credit_type_options, $selected, [], $args);
    ?>

    <div class="hidden"><!-- Hiding until total field has been reworked to work with the extra columns -->
    <div class="border-top pt-3<?= count($selected) ? '' : ' hidden' ?>" id="time_allocations_total-wrapper">
        <?= $form->input('Total', 'time_allocations_total', $spec->get_total_credit_hours(), ['disabled' => 'disabled'], ['right_icon' => '<span title="hours">h</span>']); ?>
    </div>
    </div>


<?php $form->tab_start('Marks'); ?>
    <?php
    $args     = [
        'extra_columns' => ['mark' => ['title' => 'Marks', 'attributes' => ['type' => 'number'], 'args' => ['right_icon'=> '%']]],
        'id_field'      => 'type',
        'title_field'   => 'type'
    ];
    $selected = $spec->marks->find_all();
    echo $form->multiselect_table('Type', 'mark_allocations', $credit_type_options, $selected, [], $args);
    ?>

    <div class="border-top pt-3<?= count($selected) ? '' : ' hidden' ?>" id="mark_allocations_total-wrapper">
        <?= $form->input('Total', 'mark_allocations_total', $spec->get_total_marks(), ['disabled' => 'disabled'], ['right_icon' => '%']); ?>
    </div>

<?php $form->tab_start('Credits'); ?>
    <?php
    echo $form->numeric_input('Number of credits', 'number_of_credits');
    echo $form->numeric_input('Number of exams', 'number_of_exams');
    echo $form->numeric_input('Exam duration', 'exam_duration', null, [], ['right_icon' => 'mins']);
    ?>

<?php
echo $form->tabs();


echo $form->action_buttons();
echo $form->end();
?>
<style>
    .table-multiselect .input_group { font-size: 1rem; width: auto; }
</style>

<script>
    (function(){
        $('#course-spec-edit-time_allocations-table').on('change', '[name*="[hours]"]', update_time_total);
        $('#course-spec-edit-time_allocations-selector').on(':ib-item-added :ib-item-removed', update_time_total);

        function update_time_total() {
            var total  = 0;
            var $hours = $('#course-spec-edit-time_allocations-table').find('tbody [name*="[hours]"]');
            $hours.each(function() {
                total += this.value ? parseFloat(this.value) : 0;
            });
            $('#time_allocations_total-wrapper').toggleClass('hidden', $hours.length == 0);
            $('#course-spec-edit-time_allocations_total').val(total);
        }
    })();

    (function(){
        $('#course-spec-edit-mark_allocations-table').on('change', '[name*="[mark]"]', update_marks_total);
        $('#course-spec-edit-mark_allocations-selector').on(':ib-item-added :ib-item-removed', update_marks_total);

        function update_marks_total() {
            var total  = 0;
            var $marks = $('#course-spec-edit-mark_allocations-table').find('tbody [name*="[mark]"]');
            $marks.each(function() {
                total += this.value ? parseFloat(this.value) : 0;
            });
            $('#mark_allocations_total-wrapper').toggleClass('hidden', $marks.length == 0);
            $('#course-spec-edit-mark_allocations_total').val(total);
        }
    })();
</script>
