<?php
$attributes = ['multiple' => 'multiple'];
if (isset($id)) {
    $attributes['id'] = $id;
}
if (isset($class)) {
    $attributes['class'] = $class;
}
$args = [
    'multiselect_options' => [
        'enableCaseInsensitiveFiltering' => true,
        'enableClickableOptGroups' => true,
        'enableFiltering' => true,
        'enableHTML' => true,
        'includeSelectAllOption' => true,
        'numberDisplayed' => 1,
        'selectAllText' => __('ALL')
    ],
    'has_parent_label' => true
];

$options = '';
$progress_bar = '';
foreach ($schedules as $schedule) {
    /*
    $schedule_statuses = [
        ['name' => 'complete',  'amount' => $schedule['complete'],  'color' => '#365eaa'],
        ['name' => 'scheduled', 'amount' => $schedule['scheduled'], 'color' => '#3b9e48'],
        ['name' => 'available', 'amount' => $schedule['available'], 'color' => '#ff9505']
    ];
    $progress_bar = View::factory('snippets/progress_bar')->set('statuses', $schedule_statuses);
    */

    $options .= '<option value="'.$schedule['id'].'">#'.htmlspecialchars($schedule['id'].' - '.$schedule['name'].$progress_bar). '</option>';
}
?>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary form-btn btn--full">
        <?= __('Select schedules') ?>
        <span class="form-select-mask-count"></span>
        <span class="arrow_caret-down"></span>
    </button>
<?php $args['mask'] = ob_get_clean(); ?>

<div class="schedule-selector applications-schedules-wrapper"<?= !empty($id) ? 'id="'.$id.'-wrapper"' : '' ?>>
    <?= Form::ib_select(null, 'schedule_ids[]', $options, null, $attributes, $args); ?>
</div>