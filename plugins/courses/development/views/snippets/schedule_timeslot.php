<tr class="schedule-timeslot-row well well-small"<?= (isset($timeslot_id)) ? ' data-timeslot_id="'.$timeslot_id.'"' : '' ?>>
    <td class="schedule-timeslot-order" data-label="Order"><?= isset($order) ? $order : '' ?></td>

    <td class="schedule-timeslot-start_day column-date" data-label="Day" data-day="<?= isset($start_date) ? date('d', strtotime($start_date)) : '' ?>">
        <?= isset($start_date) ? date('l', strtotime($start_date)) : '' ?>
    </td>

    <td class="schedule-timeslot-start_start_date column-date"
        data-label="Date"
        data-month="<?= isset($start_date) ? (date('n', strtotime($start_date)) - 1) : '' ?>"
        data-year="<?=  isset($start_date) ? date('Y', strtotime($start_date))       : '' ?>"
        >
        <?= isset($start_date) ? date('d / M / Y', strtotime($start_date)) : '' ?>
    </td>

    <td data-label="Price">
        <input
            type="text"
            class="form-control timeslot_price"
            name="timeslot_price[]"
            value="<?= isset($price) ? $price : '' ?>"
            <?= (isset($price_disabled) && $price_disabled) ? ' disabled="disabled"' : '' ?>
            />
    </td>

    <td data-label="Start time">
        <input
            type="text"
            name="start_time"
            class="form-control timepicker start_time time_range_picker"
            value="<?= isset($start_date) ? date("H:i", strtotime($start_date)) : '' ?>"
            <?= (isset($start_time_disabled) && $start_time_disabled) ? ' disabled="disabled"' : '' ?>
            />
    </td>

    <td data-label="End time">
        <input
            type="text"
            name="end_time"
            class="form-control timepicker end_time  time_range_picker"
            value="<?= isset($end_date) ? date("H:i", strtotime($end_date)) : '' ?>"
            <?= (isset($end_time_disabled) && $end_time_disabled) ? ' disabled="disabled"' : '' ?>
            />
     </td>

    <td data-label="Trainer">
        <?php
        $options = isset($trainer_options) ? $trainer_options : '';
        echo Form::ib_select(null, null, $options, null, array('class' => 'trainer_select')) ?>
    </td>

    <td data-label="Monitored">
        <?php
        $checked = isset($monitored) ? $monitored : true;
        echo Form::ib_checkbox(null, 'timeslot_monitored[]', 1, $checked, array('class' => 'schedule-timeslot-monitored'));
        ?>
    </td>

    <td data-label="Topic">
        <?php
        $options = isset($topics) ? html::optionsFromRows('id', 'name', $topics) : array();
        echo Form::ib_select(null, null, $options, null, array('class' => 'topic')) ?>
    </td>

    <td data-label="Delete">
        <button type="button" class="btn-link delete_me">
            <span class="icon-times"></span>
        </button>
    </td>
</tr>