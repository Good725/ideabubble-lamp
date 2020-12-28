<?php // deprecated: use booking_calendar.php ?>
<input type="hidden" id="timetable_from_date" value="<?= date('Y-m-d',strtotime($start_date)); ?>"/>
<input type="hidden" id="timetable_to_date" value="<?= date('Y-m-d',strtotime($end_date)); ?>"/>

<div class="hidden" id="timetable_view_area">
    <div id="timetable_navigation">
        <i class="arrow_icon icon-arrow-left"></i>
        <b id="timetable_navigation_date_range"><?= date('d-m-Y',strtotime($start_date)); ?> &mdash; <?= date('d-m-Y',strtotime($end_date)); ?></b>
        <i class="arrow_icon icon-arrow-right"></i>
    </div>

    <?php if ( ! empty($booking_items)) : ?>
        <table class="table booking_timetable">
            <thead>
            </thead>
            <tbody>
            <?php foreach($weeks as $key=>$week):?>
                <tr class="period_table_date_heading">
                <th colspan="8">
                    <h3>Week: <?=$key;?></h3>
                    <button class="minimize_button" title="Minimise" data-minimize="timetable_week_view_number_<?=$key;?>" type="button">_</button>
                    </th>
                </tr>
            <tbody  id="timetable_week_view_number_<?=$key;?>">
                    <tr>
                        <th scope="col">Time</th>
                        <?php foreach($week['dates'] as $date):?>
                            <th scope="col"><?= date('D, d M', strtotime($date)) ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php foreach ($week['times'] as $time): ?>
                    <tr>
                        <th scope="row"><?= $time ?></th>
                        <?php foreach ($week['dates'] as $d=>$date): ?>
                            <td>
                                <?php foreach ($week['cells'][$date] as $booking): ?>
                                    <?php
                                    $test = array_filter($booking);
                                    if (! empty($test)):
                                    if (date('H:i', strtotime($booking['datetime_start'])) == $time): ?>
                                        <div class="timetable_item" style="background-color:<?= $booking['color'] ?>;">
                                            <div><a href="admin/courses/edit_schedule/?id=<?= $booking['schedule_id'] ?>"><?= $booking['schedule'] ?></a></div>
                                            <div><?= date('H:i', strtotime($booking['datetime_start'])) ?>&mdash;<?= date('H:i', strtotime($booking['datetime_end'])) ?></div>
                                        </div>
                                    <?php endif; endif; ?>
                                <?php endforeach; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>There are no Timetables to display for this period</p>
    <?php endif; ?>

</div>