<div class="header-buttons">
    <?php
    echo Form::btn_options(
        'mode',
        ['overview' => 'Overview', 'details' => 'Details'],
        'overview', false,
        ['class' => 'attendance-mode-toggle'],
        ['class' => 'stay_inline d-inline-block w-auto']
        );
    ?>
</div>