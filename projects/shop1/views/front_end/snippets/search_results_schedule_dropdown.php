<?php if (is_array($item['schedules']) AND count($item['schedules']) > 0): ?>
    <option value="">-- Time &amp; Date --</option>
    <?php foreach ($item['schedules'] as $schedule): ?>

        <?php if (isset($schedule['booking_type']) AND $schedule['booking_type'] == 'One Timeslot' AND ! empty($schedule['timeslots'])): ?>
            <?php foreach ($schedule['timeslots'] as $timeslot): ?>
                <option value="<?= $timeslot['id'] ?>" data-schedule_id="<?= $schedule['id'] ?>">
                    <?= $schedule['location'].' - '.date('H:i D j F Y', strtotime($timeslot['datetime_start'])); ?>
                </option>
            <?php endforeach; ?>

        <?php else: ?>
            <option value="<?= $schedule['event_id'] ?>" data-schedule_id="<?= $schedule['id'] ?>">
                <?= $schedule['location'].' - '.date('H:i D j F Y', strtotime($schedule['start_date'])).(( ! is_null($schedule['repeat'])) ? ' - '.$schedule['repeat']: ''); ?>
            </option>
        <?php endif; ?>

    <?php endforeach; ?>
<?php else: ?>
    <option value="">No dates and times defined</option>
<?php endif; ?>