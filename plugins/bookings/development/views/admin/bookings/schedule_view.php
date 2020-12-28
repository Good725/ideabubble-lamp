<?php // deprecated: use booking_calendar.php ?>
<header class="weekly_schedule_header">
	<h3>Weekly Schedule</h3>
	<div id="schedule_view_times">
		<i class="icon-arrow-left timetable_selection_arrow timetable_selection_prev" tabindex="0"></i>
		<b class="timetable_selection_range_button" tabindex="0"><?=date('M jS',$length['first_day']);?> - <?=date('M jS',$length['last_day']);?></b>
		<i class="icon-arrow-right timetable_selection_arrow timetable_selection_next" tabindex="0"></i>
	</div>
	<button type="button" class="minimize_button" data-minimize="period_table" title="Minimise">_</button>
</header>

<?php if ($length['rows'] > 0): ?>
	<table id="period_table" class="period_table">
        <tbody>
			<?php
			$weeks      = $length['size'] / 7;
			$week_start = $length['first_day'];
			$week_end   = strtotime('+7 days', $length['first_day']);
			$s          = 0; // schedule index
			?>
			<?php for ($w = 0; $w < $weeks; $w++): ?>
                <tr class="period_table_week_header">
                    <th class="period_table_date_heading" colspan="8">
                        <h3>Week <?= date('W', $week_start) ?></h3>
                        <button class="minimize_button" title="Minimise" data-minimize="week_view_number_<?=$week_start;?>" type="button">_</button>
                    </th>
                </tr>
                    <tr id="week_view_number_<?=$week_start;?>">
                        <th scope="row" class="period_table_week_cell">
                            <span class="period_table_week">Week <?= date('W', $week_start) ?></span>
                        </th>
                        <?php for ($d = 0; $d < 7; $d++): ?>
                            <td class="period_table_cell">
                                <h4 class="period_table_date_heading"><?= date('l jS', strtotime('+'.$d.' days', $week_start)) ?></h4>
                                <?php
                                $gyear = null;
                                $gtitle = null;
                                while (isset($schedules[$s]) AND strtotime($schedules[$s]['datetime_start']) < strtotime('+'.($d+1).' days', $week_start)):
                                    $schedule = $schedules[$s];
                                    if($gyear != $schedule['year']){
                                        if($gyear != null){
                                            echo '</div><hr>';
                                        }
                                        $gyear = $schedule['year'];
                                        echo '<div class="year_group"><h5 class="year">'. $gyear . '</h5>';
                                    }
                                    if($gtitle == null){
                                        $gtitle = $schedule['title'];
                                    } else {
                                        if($gtitle != $schedule['title']){
                                            $gtitle = $schedule['title'];
                                            echo '<br>'; // add extra space between different names
                                        }
                                    }
                                ?>
                                    <div class="schedule_container" data-period_id="<?=$schedule['period_id'];?>" data-schedule_id="<?=$schedule['schedule_id'];?>">
                                        <?php
                                        $current = FALSE;
                                        $booked = FALSE;
                                        $booking = FALSE;
                                        if (isset($current_bookings[$schedule['schedule_id']][$schedule['period_id']]))
                                        {
                                            $current     = TRUE;
                                            $period_data = $current_bookings[$schedule['schedule_id']][$schedule['period_id']];
                                            $booking     = (isset($period_data['attending']) AND $period_data['attending'] == 1);
                                        }
                                        ?>
                                        <div class="schedule_title" tabindex="0" title="<?= $schedule['name'] ?>" style="background-color:<?= $schedule['color'] ?>;">
                                            <?= $schedule['name'] ;?>
                                        </div>
                                        <div class="period_details">
                                            <div class="side_data">
                                                <div class="room_no" title="Room: <?=$schedule['room_no'];?>"><?=$schedule['room_no'];?></div>
                                                <div title="Booked: <?=$schedule['no_of_enquiries'];?>"><?=$schedule['no_of_enquiries'];?></div>
                                                <div title="Places available: <?=$schedule['places_available'];?>"><?=$schedule['places_available'];?></div>
                                            </div>
                                            <div>
                                                <div class="period_category"><?= $schedule['category'] ?></div>
                                                <div class="schedule_teacher"><?= $schedule['teacher'] ?></div>
                                                <div class="time">
                                                    <?=date('H:i',strtotime($schedule['datetime_start'])).' - '.date('H:i',strtotime($schedule['datetime_end']));?>
                                                </div>
                                                <?php if ($booking_id == ''): ?>
                                                    <?php if (array_key_exists('attending', $schedule)): ?>
                                                        <button type="button" class="button_registration btn-primary">Attending</button>
                                                    <?php else: ?>
                                                        <button
                                                            type="button"
                                                            class="button_registration <?= $booking ? 'cancel_place btn-danger' : 'register_place btn-success' ?>"
                                                            <?= $booking ? ' data-show="false"' : ' data-toggle="modal"' ?>><?= $booking ? 'Cancel' : 'Register' ?>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else:?>
                                                    <button
                                                        type="button" class="button_registration <?= $booking ? 'cancel_place btn-danger' : 'register_place btn-success';?> "
                                                        <?= $booking ? ' data-show="false"' : ' data-toggle="modal"' ?>>
                                                        <?= $booking ? 'Cancel' : 'Register' ?>
                                                    </button>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php $s++ ?>
                                <?php
                                    echo '</div>';
                                endwhile;
                                ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
				<?php $week_start = strtotime('+7 days', $week_start); ?>
			<?php endfor; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="course_filter_error">
        <p>There are no courses available for these dates. Try changing your search options.</p>
    </div>
<?php endif; ?>