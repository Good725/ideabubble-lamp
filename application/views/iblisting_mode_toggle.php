<?php if (!empty($add_timeslot_button)): ?>
    <button
        type="button"
        class="btn btn-primary form-btn add-slot w-100 w-sm-auto mb-3 mb-sm-0"
        data-toggle="modal"
        data-target="#<?= $id_prefix ?>-schedule_timeslots-modal"
        style="min-width: 0;"
        ><?= __('Add slot') ?></button>
<?php endif; ?>

<?php
if (count($views) > 1) {
    $view_options = [];
    if (in_array('calendar', $views)) {
        $view_options['calendar'] = 'Calendar';
    }
    if (in_array('overview', $views)) {
        $view_options['overview'] = 'Overview';
    }

    echo Form::btn_options(
        'mode',
        $view_options,
        $views[0],
        false,
        ['class' => 'iblisting-mode-toggle'],
        ['class' => 'stay_inline d-inline-block w-auto ml-0 ml-md-2']
    );
}