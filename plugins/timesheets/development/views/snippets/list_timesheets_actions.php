 <?php
if (strtolower($status) == 'waiting for approval') {
    $buttons = [
        ['action' => 'approve', 'text' => __('Approve'), 'click' => 'vm.approveTimesheet(null)', 'context' => 'primary'],
        ['action' => 'reject',  'text' => __('Reject'),  'click' => 'vm.rejectTimesheet(null)']
    ];
} else {
    $buttons = [
        ['action' => 'submit',  'text' => __('Submit'),  'click' => 'vm.submitTimesheet(null)',  'context' => 'primary']
    ];
}

$disabled_attribute = empty($affects_entire_table) ? '' : ' disabled="disabled"';
?>

<div class="timesheets-list-actions">
    <?php foreach ($buttons as $button): ?>
        <button
            type="button"
            class="btn btn-<?= !empty($button['context']) ? $button['context']  : 'default' ?>"
            ng-click="<?= $button['click'] ?>"
            <?= $disabled_attribute ?>
        >
            <?= $button['text'] ?>
            <?php if (!empty($affects_entire_table)): ?>
                <span class="timesheets-list-actions-amount"></span>
            <?php endif; ?>
        </button>
    <?php endforeach; ?>

    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"<?= $disabled_attribute ?>>
            <span class="flaticon-more"></span>
        </button>

        <ul class="dropdown-menu pull-right">
            <li>
                <button type="button" class="btn-link" data-action="view"><?= __('View timesheet') ?></button>
            </li>
        </ul>
    </div>
</div>