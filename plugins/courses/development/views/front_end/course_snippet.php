    <div class="date-and-package hidden--mobile">
        <div class="search-calendar-wrapper">
            <div class="search-calendar-slider">
                <table class="custom-calendar search-calendar">
                    <tbody>
                        <tr>
                            <td class="search-calendar-course-data"><h2>Courses Available</h2></td>
                             <?php foreach ($week_days_header as $key => $week_day_header):?>
                                <td class="booking_week_dates" data-date="<?=$week_days[$key]?>">
                                    <?php if ($key == 0): ?>
                                        <button type="button" class="button--plain arrow-left <?=$class_left?>">
                                            <span class="fa fa-angle-left" aria-hidden="true"></span>
                                        </button>
                                    <?php endif; ?>

                                    <?= $week_day_header['d'] ?>
                                    <br />
                                    <?= $week_day_header['m'] ?>

                                    <?php if ($key == count($week_days_header) - 1): ?>
                                        <button type="button" class="button--plain arrow-right <?=$class_right?>">
                                            <span class="fa fa-angle-right" aria-hidden="true"></span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                             <?php endforeach;?>
                        </tr>

                        <?php
                        $courses_displayed = array();

                        if (count($courses) == 0) {
                        ?>
                            <tr><td colspan="8"><?=__('No course found')?></td> </tr>
                        <?php
                        }

                        foreach ($courses as $course):
                            if (!isset($courses_displayed[$course['id']])) {
                                $courses_displayed[$course['id']] = $course;
                            } else {
                                if ($course['display_availability'] == 'per_course') {
                                    continue;
                                }
                            }
                        ?>
                            <tr class="search-calendar-course_row">
                                <td class="search-calendar-course-data">
                                    <button type="button" class="button--plain search-calendar-course-image" data-course_id="<?= $course['id'] ?>" data-schedule_id=<?= $course['display_availability'] == 'per_course' ? 0 : $course['schedule_id'] ?> data-course-is_fulltime="<?=$course['is_fulltime']?>" data-course-fulltime_price="<?=$course['fulltime_price']?>">
                                        <?php $filename = isset($course['images'][0]) ? $course['images'][0]['file_name'] : 'course-placeholder.png'; ?>

                                        <img src="<?= Model_Media::get_image_path($filename, 'courses') ?>" width="84" alt="" />

                                        <span class="fa fa-info-circle" aria-hidden="true"></span>
                                    </button>

                                    <p><?= $course['display_availability'] == 'per_course' ? $course['title'] : $course['schedule']?></p>
                                    <p><?= $course['category'] ?></p>
                                    <!--
                                    <p><?= $course['subject']    ?></p>
                                    <p><?= $course['year']    ?></p>
                                    <p><?= $course['level']   ?></p> -->
                                </td>

                                <?php foreach ($week_days as $week_day):?>
                                    <?php
                                    $slots_for_date = array();
                                    $schedule_ids = array();

                                    foreach ($schedules as $schedule){
                                        if ($schedule['course_id']==$course['id'] AND $schedule['start_date']==$week_day AND ($schedule['id']==$course['schedule_id'] || $course['display_availability'] == 'per_course')){

                                            array_push(
                                                $slots_for_date,
                                                array(
                                                    'date'                => $week_day,
                                                    'schedule_id'         => $schedule['id'],
                                                    'event_id'            => $schedule['event_id'],
                                                    'is_schedule_fee'     => ($schedule['fee_per'] == 'Schedule'),
                                                    'schedule_fee_amount' => ($schedule['schedule_fee_amount']) ? $schedule['schedule_fee_amount'] : 0,
                                                    'time_slot_fee'       => ($schedule['time_slot_fee'])       ? $schedule['time_slot_fee']       : 0,
                                                    'day_fee'             => ($schedule['fee_per'] == 'Day'     ? $schedule['schedule_fee_amount'] : 0),
                                                    'amendable'           => $schedule['amendable']
                                                )
                                            );
                                            $schedule_ids[] = $schedule['id'];
                                        }

                                        if ($course['is_fulltime'] == 'YES') {
                                            array_push(
                                                $slots_for_date,
                                                array(
                                                    'date'                => $week_day,
                                                    'schedule_id'         => 0,
                                                    'event_id'            => 0,
                                                    'is_schedule_fee'     => 1,
                                                    'schedule_fee_amount' => $course['fulltime_price'],
                                                    'time_slot_fee'       => 0,
                                                    'day_fee'             => 0,
                                                    'amendable'           => 0
                                                )
                                            );
                                        }
                                    }

                                    ?>

                                    <?php if(sizeof($slots_for_date) == 0): ?>
                                        <?php // no schedule time slots for this date ?>
                                        <td
                                            style="margin-top: 60px; overflow:visible;"
                                            data-date="<?= $week_day ?>"
                                            data-course-id="<?= $course["id"] ?>"
                                            data-event-id="0"
                                            data-schedule-id="<?= $course['display_availability'] == 'per_course' ? implode(' ', $schedule_ids) : $course['schedule_id'] ?>"
                                            class="not-allowed"
                                            >
                                            <div class="tooltip" data-tooltip-position="top">
                                                <div class="tooltip-trigger">
                                                    <span class="fa fa-ban" aria-hidden="true"></span>
                                                </div>
                                                <div class="tooltip-text">Sorry there are no courses available on this day.</div>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <?php
                                        $event_id = $slots_for_date[0]["event_id"];
                                        $whole_schedule = false;
                                        $button_text = '';

                                        if (sizeof($slots_for_date) == 1 || $display_timeslots == 0)
                                        {
                                            if ($slots_for_date[0]['is_schedule_fee'])
                                            {
                                                // case when price is for whole schedule
                                                $whole_schedule = true;
                                                $button_text = '&euro;'.$slots_for_date[0]['schedule_fee_amount'];
                                            }
                                            else
                                            {
                                                // case when price is for time slot
                                                if ($slots_for_date[0]['schedule_fee_amount'] != 0 OR $slots_for_date[0]['schedule_fee_amount'] != null)
                                                {
                                                    if($slots_for_date[0]['day_fee']){
                                                        $fee_text = '<span>Daily</span> &euro; '.$slots_for_date[0]['day_fee'];
                                                    } else if(($slots_for_date[0]['time_slot_fee'] < $slots_for_date[0]['schedule_fee_amount']) ){
                                                        $fee_text = '<span>From</span> &euro; '.$slots_for_date[0]['time_slot_fee'];
                                                    }else if(($slots_for_date[0]['time_slot_fee'] > $slots_for_date[0]['schedule_fee_amount'])){
                                                        $fee_text = '<span>From</span> &euro; '.$slots_for_date[0]['schedule_fee_amount'];
                                                    }else if (($slots_for_date[0]['time_slot_fee'] == $slots_for_date[0]['schedule_fee_amount'])){
                                                        $fee_text = '&euro; '.$slots_for_date[0]['schedule_fee_amount'];
                                                    }

                                                    $button_text = $fee_text;
                                                }
                                                else
                                                {
                                                    $button_text = '&euro; '.$slots_for_date[0]['time_slot_fee'];
                                                }
                                            }

                                        }
                                        else
                                        {
                                            // case when several time slots for a dat
                                            if ($course['is_fulltime'] == 'YES') {
                                                $button_text = '<span>Full Time</span> &euro; ' . $course['fulltime_price'];
                                            } else if ($slots_for_date[0]['is_schedule_fee']) {
                                                $button_text = '<span>Multiple classes</span> &euro; '.$slots_for_date[0]['schedule_fee_amount'];
                                            } else {
                                                $min = $slots_for_date[0]['time_slot_fee'];
                                                foreach ($slots_for_date as $slots) {
                                                    if ($min > $slots['time_slot_fee']) {
                                                        $min = $slots['time_slot_fee'];
                                                        $new_event_id = $slots['event_id'];
                                                    }
                                                }
                                                // check if all values are equal

                                                if ($slots_for_date[0]['day_fee']) {
                                                    $button_text = '<span>Daily</span> <span>From</span>&euro; '. $slots_for_date[0]['day_fee'];
                                                } else if ($min == $slots_for_date[0]['time_slot_fee']) {
                                                    $button_text = '<span>Multiple classes</span> <span>From</span>&euro; '.$min;
                                                }
                                                else {
                                                    $event_id = $new_event_id;
                                                    $button_text = '<span>Multiple classes</span> &euro; '.$min;
                                                }
                                            }
                                        }
                                        ?>

                                        <td data-date="<?= $week_day ?>">
                                            <button
                                                type="button"
                                                class="booking-date-button"
                                                data-amendable="<?= $slots_for_date[0]["amendable"] ?>"
                                                data-course-id="<?= $course["id"] ?>"
                                                data-course-is_fulltime="<?= $course["is_fulltime"] ?>"
                                                data-date="<?= $week_day ?>"
                                                data-event-id="<?= $event_id ?>"
                                                data-schedule-id="<?=$course['display_availability'] == 'per_course' ? implode(' ', $schedule_ids) : $slots_for_date[0]["schedule_id"]?>">
                                                <?= $button_text ?>
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach;?>
                    </tbody>

                    <tfoot class="hidden">
                        <tr class="package-offers-tr">
                            <td colspan="8">
                                <div class="package-offers-wrap"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php require 'availability_results_mobile.php'; ?>





