<div class="header-buttons">
    <button
        type="button" class="btn btn-primary text-uppercase"
        <?php //data-toggle="modal" data-target="#safety-precheck-modal" ?>
        id="safety-precheck-add-btn"
    >Submit pre-check</button>

    <?php if (!empty($view_toggle)): ?>
    <?= Form::btn_options(
            'mode',
            ['overview' => 'Overview', 'details' => 'Details'],
            'overview', false,
            ['class' => 'safety-precheck-mode-toggle'],
            ['class' => 'stay_inline d-inline-block w-auto']); ?>
    <?php endif; ?>
</div>