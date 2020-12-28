<?php $timesheets = array(
    'Waiting for approval' => array(
        array('id' => 1, 'staff_name' => 'Fred Flintstone', 'reviewer' => 'Mr. Slate', 'last_transaction' => '07/Jan/2018 17:15', 'hours' => 10, 'required' => 40)
    ),
    'Open' => array(
        array('id' => 2, 'staff_name' => 'Barney Rubble',   'reviewer' => 'Mr. Slate', 'last_transaction' => '07/Jan/2018 17:15', 'hours' => 40, 'required' => 40),
        array('id' => 3,  'staff_name' => 'Staff member 3',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 4,  'staff_name' => 'Staff member 4',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 5,  'staff_name' => 'Staff member 5',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 6,  'staff_name' => 'Staff member 6',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 7,  'staff_name' => 'Staff member 7',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 8,  'staff_name' => 'Staff member 8',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 9,  'staff_name' => 'Staff member 9',  'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 10, 'staff_name' => 'Staff member 10', 'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40),
        array('id' => 11, 'staff_name' => 'Staff member 11', 'last_transaction' => '07/Jan/2018', 'hours' => 1, 'required' => 40)
    ),
); ?>

<?php $total_number_of_timesheets = 0; ?>

<table class="timesheets-list-table dataTable-collapse">
    <thead></thead>
    <?php foreach ($timesheets as $status => $per_status_timesheets): ?>
        <?php
        $number_of_timesheets = count($per_status_timesheets);
        $total_number_of_timesheets += $number_of_timesheets;
        ?>
        <?php if ($number_of_timesheets): ?>
            <tbody class="timesheets-list-heading" data-status="<?= $status ?>">
            <tr>
                <td data-label="<?= __('Select all') ?>">
                                    <span class="timesheets-list-select">
                                        <?= Form::ib_checkbox(null, null, 1, false, ['class' => 'timesheets-list-select-all', 'data-status' => $status]) ?>
                                    </span>
                </td>
                
                <td colspan="4" data-label="Group">
                    <strong class="timesheets-list-counter--singular<?= $number_of_timesheets != 1 ? ' hidden' : '' ?>">
                        <?= __('1 timesheet is $1 ', ['$1' => strtolower($status)]) ?>
                    </strong>
                    
                    <strong class="timesheets-list-counter--plural<?=   $number_of_timesheets == 1 ? ' hidden' : '' ?>">
                        <?= __('$1 timesheets are $2 ', [
                            '$1' => '<span class="timesheets-list-count" data-status="'.htmlentities($status).'">'.count($per_status_timesheets).'</span>',
                            '$2' => htmlentities(strtolower($status))
                        ]) ?>
                    </strong>
                </td>
                
                <td data-label="<?= __('Group actions') ?>">
                    <?= View::factory('snippets/list_timesheets_actions')->set(['status' => $status, 'affects_entire_table' => true]) ?>
                </td>
            </tr>
            </tbody>
            
            <thead data-status="<?= $status ?>">
            <tr>
                <th></th>
                <th scope="col"><?= __('Team member')      ?></th>
                <th scope="col"><?= __('Status')           ?></th>
                <th scope="col"><?= __('Last transaction') ?></th>
                <th scope="col"><?= __('Hours') ?></th>
                <th scope="col" style="width: 16em;"><?= __('Actions') ?></th>
            </tr>
            </thead>
            
            <tbody class="timesheets-list-table-group" data-status="<?= $status ?>">
            <?php foreach ($per_status_timesheets as $timesheet): ?>
                <tr>
                    <td data-label="<?= __('Select') ?>">
                                        <span class="timesheets-list-select">
                                            <?= Form::ib_checkbox(null, 'timesheets[]', $timesheet['id'], false, ['class' => 'timesheets-list-select-item', 'data-status' => $status]) ?>
                                        </span>
                    </td>
                    
                    <td data-label="<?= __('Team member') ?>"><?= $timesheet['staff_name'] ?></td>
                    
                    <td data-label="<?= __('Status') ?>">
                                        <span
                                            class="badge timesheets-status-badge"
                                            data-status="<?= strtolower($status) ?>"
                                        ><?= $status ?></span>
                        <?php if (strtolower($status) == 'waiting for approval' && !empty($timesheet['reviewer'])): ?>
                            <br /><?= $timesheet['reviewer'] ?>
                        <?php endif; ?>
                    </td>
                    
                    <td data-label="<?= __('Last transaction') ?>"><?= $timesheet['last_transaction'] ?></td>
                    
                    <?php
                    $percentage = ($timesheet['required'] == 0) ? false : round(($timesheet['hours'] / $timesheet['required']) * 100);
                    $percentage = ($percentage !== false && $percentage > 100) ? 100 : $percentage;
                    ?>
                    
                    <td data-label="<?= __('Hours') ?>"><?= $timesheet['hours'] ?><br /><?= ($percentage !== false) ? $percentage.'%' : '' ?></td>
                    
                    <td data-label="<?= __('Actions') ?>">
                        <?= View::factory('snippets/list_timesheets_actions')->set(['status' => $status]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        <?php endif; ?>
    <?php endforeach; ?>
</table>

<?php if (count($total_number_of_timesheets)): ?>
    <p><?= __('No timesheets were found.') ?></p>
<?php endif; ?>
