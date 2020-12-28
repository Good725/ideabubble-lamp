<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<style>
    .table-sticky th {
        background: var(--primary);
        background: linear-gradient(#fff, var(--primary) 1px);
        color: #fff;
    }

    .table-sticky.table-sticky th {
        border: none;
    }

    .table-sticky th:not(:empty) {
        position: sticky;
        top: 0;
        vertical-align: middle;
        z-index: 2;
    }
</style>

<?php
ob_start();
$form = new IbForm('safety-precheck-form', '#', 'post', ['layout' => 'vertical']);
$form->published_field = false;

echo $form->start(['title' => false]);
echo $form->hidden('id');
?>

<div class="precheck-modal-page in-use">
    <p>Select the type for this pre-check:</p>

    <div class="text-center mb-3">
        <?php foreach ($top_level_prechecks as $precheck): ?>
            <label class="radio-icon">
                <input type="radio" class="precheck-type-toggle" name="precheck_type" value="<?= $precheck->id  ?>" />
                <span class="radio-icon-unchecked btn btn-lg btn-default"><?= htmlspecialchars($precheck->title) ?></span>
                <span class="radio-icon-checked btn btn-lg btn-primary"><?= htmlspecialchars($precheck->title) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<div id="safety-precheck-quiz-wrapper" data-current_page=""></div>

<div class="precheck-modal-buttons hidden" id="precheck-modal-buttons-single">
    <div class="form-action-group text-center">
        <button type="button" class="btn btn-primary" id="submit-precheck">Save</button>

        <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
</div>

<div class="d-flex align-items-center form-action-group precheck-modal-buttons hidden"
     id="precheck-modal-buttons-paginated" style="justify-content: center;">

    <input type="hidden" id="precheck-modal-pages" />

    <button type="button" class="btn btn-default mx-0" id="precheck-modal-prev">← Previous</button>

    <div class="mx-3">
        Page
        <span id="precheck-modal-pagination-page"></span> of
        <span id="precheck-modal-pagination-total"></span>
    </div>

    <button type="button" class="btn btn-default mx-0" id="precheck-modal-next">Next →</button>
    <button type="button" class="btn btn-primary mx-0 hidden" id="precheck-modal-last">Submit</button>
</div>


<?php
echo $form->end();

$form = ob_get_clean();

echo View::factory('snippets/modal')->set([
    'id'     => 'safety-precheck-modal',
    'title'  => 'Pre-check',
    'size'   => 'lg',
    'body'   => $form,
]);
?>
