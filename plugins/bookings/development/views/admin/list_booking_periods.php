<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
</div>
<header>
    <h3><?= $data['student_name'] ?> - Booking periods</h3>
    <p><strong>Booking  #<?= $data['booking_id'] ?>, Schedule #<?= $data['schedule_id'] ?></strong></p>
</header>

<?php if ( ! empty($data['periods'])): ?>
    <table class="table table-striped dataTable contact_booking_periods_table">
        <thead>
            <tr>
                <th scope="col">Course</th>
                <th scope="col">Schedule</th>
                <th scope="col">Day</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">Attend</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data['periods'] as $key=>$item): ?>
            <tr data-schedule_id="<?=$key;?>">
                <td><?=$item['title'];?></td>
                <td><?=$item['name'];?></td>
                <td><?=date('D',strtotime($item['datetime_start']));?></td>
                <td><?=date('M j',strtotime($item['datetime_start']));?></td>
                <td><?=date('H:i',strtotime($item['datetime_start'])).' - '.date('H:i',strtotime($item['datetime_end']));?></td>
                <td>
                    <?php if (isset($item['attend']) AND isset($item['id'])): ?>
                        <i class="icon-<?=$item['attend'] == 1 ? 'ok' : 'remove';?>" data-period_id="<?=$item['id'];?>"></i>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no confirmed periods.</p>
<?php endif; ?>
