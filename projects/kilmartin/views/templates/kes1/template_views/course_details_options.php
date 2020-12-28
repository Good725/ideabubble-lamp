<?php $account_bookings = Settings::instance()->get('account_managed_course_bookings'); ?>

<?php ob_start(); ?>
    <?php
    $cheapest = false;
    // Only accept available IDs for the selected schedule ID.
    // i.e. unless, the specified ID is in the dropdown, it will be ignored.
    $available_schedule_ids = array_column($course['schedules'], 'id');
    if (isset($_GET['schedule_id']) && in_array($selected_schedule_id, $available_schedule_ids)) {
        $selected_schedule_id = htmlspecialchars($_GET['schedule_id']);
    } else {
        $selected_schedule_id = '';
    }
    ?>

    <?php foreach ($course['schedules'] as $schedule): ?>
        <?php
        if (count($schedule['timeslots']) > 1 && $schedule['booking_type'] == 'One Timeslot') {
            foreach ($schedule['timeslots'] as $stimeslot) {
                if (strtotime($stimeslot['datetime_start']) < time()) continue;
                if (!empty($selected_schedule_id))  {
                    // If a schedule is selected by default, use it as the cheapest
                    // If no schedule is selected, get the cheapest out of all schedules
                    if (empty($schedule['id']) || $schedule['id'] == $selected_schedule_id) {
                        $cheapest = ($cheapest === false || ($stimeslot['fee_amount'] < $cheapest && $stimeslot['fee_amount'] > 0)) ? $stimeslot['fee_amount'] : $cheapest;
                    }
                } elseif(empty($selected_schedule_id)) {
                    $cheapest = ($cheapest === false || ($stimeslot['fee_amount'] < $cheapest && $stimeslot['fee_amount'] > 0)) ? $stimeslot['fee_amount'] : $cheapest;
                }
        ?>

            <option
                value="<?= $schedule['id'] ?>"
                <?= $schedule['id'] == $selected_schedule_id ? ' selected="selected"' : '' ?>
                data-fee="<?= $stimeslot['fee_amount'] ?: $schedule['fee_amount'] ?>"
                data-event_id="<?=$stimeslot['id']?>"
                data-is_group_booking="<?=$schedule['is_group_booking']?>"
            >
                <?php
                switch (Settings::instance()->get('schedule_selector_format')) {
                    case 'county_date':
                        $schedule_object = new Model_Course_Schedule($schedule['id']);
                        $county = $schedule_object->location->get_county()->name;
                        $date = date('j M Y', strtotime($start_date));

                        echo htmlspecialchars($county . ' - ' . $date);
                        break;

                    default:
                        echo $schedule['location'] ? $schedule['location'] . ' - ' : '';
                        echo date('D - d/m/Y - H:i', strtotime($stimeslot['datetime_start']));
                        break;
                }
                ?>
            </option>
        <?php
            }
            if ($cheapest == 0) {
                if (!empty($selected_schedule_id)) {
                    if (empty($schedule['id']) || $schedule['id'] == $selected_schedule_id) {
                        $cheapest = number_format($schedule['fee_amount'], 2);
                    }
                } elseif(empty($selected_schedule_id)) {
                    $cheapest = number_format($schedule['fee_amount'], 2);
                }
            }
        } else {
        ?>
        <?php
        $schedule_object = new Model_Course_Schedule($schedule['id']);
        $start_date = $schedule_object->get_next_timeslot()->datetime_start;
        $end_date   = ((isset($schedule['timeslots']) AND isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_end']   : $schedule['end_date']);
        if (!empty($selected_schedule_id)) {
            if (empty($schedule['id']) || $schedule['id'] == $selected_schedule_id) {
                $cheapest = ($cheapest === false || $schedule['fee_amount'] < $cheapest) ? $schedule['fee_amount'] : $cheapest;
            }
        } elseif(empty($selected_schedule_id)) {
            $cheapest = ($cheapest === false || $schedule['fee_amount'] < $cheapest) ? $schedule['fee_amount'] : $cheapest;
        }
        ?>
        <option
            value="<?= $schedule['id'] ?>"
            <?= $schedule['id'] == $selected_schedule_id ? ' selected="selected"' : '' ?>
            data-fee="<?= $schedule['fee_amount'] ?>"
            data-event_id="<?=$schedule['event_id']?>"
            data-is_group_booking="<?=$schedule['is_group_booking']?>"
        >
            <?php
            // Self-paced online course schedules don't have set dates.
            if ($schedule['learning_mode'] == 'self_paced' && $schedule['delivery_mode'] == 'online') {
                echo 'Online';
            } else {
                switch (Settings::instance()->get('schedule_selector_format')) {
                    case 'county_date':
                        $county = $schedule_object->location->get_county()->name;
                        $date = date('j M Y', strtotime($start_date));

                        echo htmlspecialchars($county . ' - ' . $date);
                        break;

                    default:
                        // Setting is not used.
                        if ($schedule['repeat']) {
                            $duration_in_seconds = date('U', strtotime($end_date)) - date('U', strtotime($start_date));
                            $duration_in_seconds = strtotime($end_date) - strtotime($start_date);
                            $duration_h = floor($duration_in_seconds / 3600);
                            $duration_m = (($duration_in_seconds % 3600) / 60);
                            $duration = ($duration_in_seconds > 0) ? ($duration_h . ($duration_m == 30 ? '.5' : '') . "h " . ($duration_m > 0 && $duration_m != 30 ? $duration_m . 'm' : '')) : false;

                            echo date('D - H:i', strtotime($start_date));
                            echo $schedule['location'] ? ' - ' . $schedule['location'] : '';
                            echo (!empty($schedule['trainer_name'])) ? ' - ' . @$schedule['trainer_name'] : '';
                            echo $duration ? ' - ' . $duration : '';
                            echo $schedule['fee_amount'] ? ' - €' . $schedule['fee_amount'] : '';
                        } else {
                            echo date('D - d/m/Y - H:i', strtotime($start_date));
                            echo $schedule['location'] ? ' - ' . $schedule['location'] : '';
                            echo (!empty($schedule['trainer_name'])) ? ' - ' . @$schedule['trainer_name'] : '';
                            echo $schedule['fee_amount'] ? ' - €' . $schedule['fee_amount'] : '';
                        }
                        break;
                }
            }
            ?>
        </option>
        <?php } ?>
    <?php endforeach; ?>
<?php $schedule_options = ob_get_clean(); ?>

