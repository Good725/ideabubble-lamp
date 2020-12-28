<table id="timesheet-approvals-table" class="timesheets-list-table dataTable-collapse hidden">
    <thead></thead>

    <?php
    $statuses = array(
        'open' => 'Open',
        'pending' => 'Pending',
        'declined' => 'Declined',
        'approved' => 'Approved',
        'ready' => 'Ready'
    );
    foreach ($statuses as $status => $status_label) {
    ?>
    <tbody class="timesheets-list-heading <?=$status?> hidden">
    <tr>
        <td data-label="<?= __('Select all') ?>">
            <span class="timesheets-list-select">
                <?= Form::ib_checkbox(null, null, 1, false, [
                    'id' => 'timesheets-approvals-list-select_all-' . $status,
                ]) ?>
            </span>
        </td>

        <td colspan="4" data-label="Group">
            <label for="timesheets-approvals-list-select_all-<?=$status?>">
                <strong><span class="timesheet_count"></span> timesheet<span class="timesheet_plural"></span> <span class="timesheet_status_label"><?=$status_label?></span></strong>
            </label>
        </td>

        <td data-label="<?= __('Group actions') ?>">
            <div class="timesheets-list-actions hidden">
                <button type="button" class="btn btn-primary submit"><?= __('Submit') ?></button>

                <button type="button" class="btn btn-primary approve"><?= __('Approve') ?></button>

                <button type="button" class="btn btn-default reject"><?= __('Reject') ?></button>

                <div class="btn-group" role="group">
                    <button
                        type="button"
                        class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    ><span class="flaticon-more"></span></button>

                    <ul class="dropdown-menu pull-right">
                        <li>
                            <button type="button" class="btn-link view"
                                    data-action="view" disabled="disabled"><?= __('View timesheet') ?></button>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
    <thead class="timesheets-list-heading2 <?=$status?> hidden">
    <tr>
        <th></th>
        <th>Team member</th>
        <th>Status</th>
        <th>Last transaction</th>
        <th>Hours</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody class="timesheets-list-table-group <?=$status?> hidden">
    <tr class="hidden">
        <td>
            <span class="timesheets-list-select">
                <?= Form::ib_checkbox(null, 'timesheets[]', null, false, [
                    'id' => 'timesheets-approvals-list-select-item_id',
                ]) ?>
            </span>
        </td>
        <td>
            <label class="timesheets-approvals-list-staff"></label>
        </td>
        <td>
            <span class="badge timesheets-status-badge" data-status=""></span>
            <br>
            <span class="timesheet-reviewer hidden">
                Reviewer: <span></span>
            </span>
        </td>
        <td data-label="Last transaction"><span class="timesheet-last_transaction"></span></td>
        <td data-label="Hours">
            <span class="timesheet_logged"></span>
            <br/>
            <span class="timesheet_available"></span>%
        </td>
        <td>
            <div class="timesheets-list-actions">
                <button type="button" class="btn submit btn-primary hidden" data-action="submit"><?= __('Submit') ?></button>
                <button type="button" class="btn approve btn-primary hidden" data-action="approve"><?= __('Approve') ?></button>
                <button type="button" class="btn reject btn-default hidden" data-action="reject"><?= __('Reject') ?></button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="flaticon-more"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <button type="button" class="btn-link view" data-action="view"><?= __('View timesheet') ?></button>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
    <?php
    }
    ?>
</table>

<div id="timesheet-approvals-no-data" class="hidden">
    <p><?= __('No timesheets to display') ?></p>
</div>