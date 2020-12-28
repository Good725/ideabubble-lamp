<?php
$attributes = ['multiple' => 'multiple'];
if (!empty($id)) {
    $attributes['id'] = $id;
}
if (!empty($class)) {
    $attributes['class'] = $class;
}
$args = [
    'multiselect_options' => [
        'includeSelectAllOption' => true,
        'numberDisplayed' => 1,
        'selectAllText' => __('ALL')
    ],
    'has_parent_label' => true
];
$options = array_combine($statuses, $statuses);
?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary form-btn btn--full form-select-mask">
        <?= $button_text ? $button_text : __('Status') ?>
        <span class="form-select-mask-count"></span>
        <span class="arrow_caret-down"></span>
    </button>
<?php $args['mask'] = ob_get_clean(); ?>

<?= Form::ib_select(null, 'status_ids[]', $options, null, $attributes, $args); ?>