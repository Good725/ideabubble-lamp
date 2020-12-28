<?php $user = Auth::instance()->get_user(); ?>
<div id="swiper-container1" class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <table class="custom-calendar">
                <tbody>
                <? if (sizeof($prv_time_slots)>0): ?>
                    <div class="arrow-left for-time-slots"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
                <? endif ?>
                <tr>
                    <?php foreach ($week_days_header as $week_day_header):?>
                        <td class="week_dates"><?=$week_day_header['d']?><br><?=$week_day_header['m']?></td>
                    <?php endforeach;?>
                </tr>
                <? if (sizeof($future_time_slots)>0): ?>
                    <div class="arrow-right for-time-slots"><i class="fa fa-angle-right" aria-hidden="true"></i></div>
                <? endif ?>
                <?php foreach ($time_rows as $time_row):?>
                    <tr>
                        <?php foreach ($week_days as $week_day):?>
                            <?php $no_slot = true; ?>
                            <?php
                            foreach ($time_slots as $time_slot){
                                if($time_slot['start_date'] == $week_day and $time_slot['start_time'] == $time_row){
                                    if ($time_slot[ 'fee_per' ] == 'Schedule') {
                                        $fee = $time_slot[ 'schedule_fee_amount' ];
                                    }
                                    else {
                                        $fee = ( $time_slot[ 'time_slot_fee' ] ) ? $time_slot[ 'time_slot_fee' ] : $time_slot[ 'schedule_fee_amount' ];
                                    }
                                    if (!$fee) {
                                        $fee = 0;
                                    }
                                    // check if logged in
                                    $logged_in_user_permission = 'Add to cart';
                                    if($time_slot['payment_type'] == 1){
                                        $pay_type =  "Pre-Pay";
                                    }else if($time_slot['payment_type'] == 2){
                                        $pay_type = "Pay As You Go";
                                    }else{
                                        $pay_type = '';
                                    }

                                    echo '<td class="alt-date-book" 
                                                       data-date="' . $time_slot[ "start_date" ] . '" 
                                                       data-event-id="' . $time_slot[ "event_id" ] . '" 
                                                       data-course-title="' . $time_slot[ "course_title" ] . '" 
                                                       data-schedule-id="' . $time_slot[ "id" ] . '"
                                                       data-start-time="' . $time_slot[ "start_time" ] . '" 
                                                       data-end-time="' . $time_slot[ "end_time" ] . '"  
                                                       data-room="' . $time_slot[ "room" ] . '"
                                                       data-location="' . $time_slot[ "location" ] . '"
                                                       data-trainer="' . $time_slot[ "trainer" ]. '"
                                                       data-pay-type="' . $pay_type. '"
                                                       data-fee-per="' . $time_slot[ "fee_per" ]. '"
                                                       data-booking-type="' . $time_slot[ "booking_type" ]. '"
                                                       data-logged-in-user-permission="' . $logged_in_user_permission. '"
                                                       data-fee="' . $fee. '"
                                               ><span>' . $time_slot[ 'start_time' ] . ' - ' . $time_slot[ 'end_time' ] . '</span> € ' . $fee . '</td>';
                                    $no_slot = false;
                                }
                            }
                            if($no_slot){
                                echo '<td class="not-allowed" data-date="'.$week_day.'" data-schedule-id="'.$time_slot["id"].'" ><span>'.$time_row.'</span><i class="fa fa-ban" aria-hidden="true"></i></td>';
                            }
                            ?>
                        <?php endforeach; ?>
                        <?php // Todo add discount and seats ?>
                        <!--                            <td class="alt-date-book"><span>11:00 - 12:00</span>-->
                        <!--                                <span>From</span> € 250-->
                        <!--                            </td>-->
                        <!--                            <td class="not-allowed">-->
                        <!--                                <span>11:00 - 12:00</span>-->
                        <!--                                <i class="fa fa-ban" aria-hidden="true"></i>-->
                        <!--                            </td>-->
                        <!--                            <td class="alt-date-book"><span>11:00 - 12:00</span>-->
                        <!--                                <span>From</span> € 250-->
                        <!--                            </td>-->
                        <!--                            <td class="pack-purchase">-->
                        <!--                                <span>11:00 - 12:00</span>-->
                        <!--                                <span>€ 250</span>-->
                        <!--                                <span class="prv-price">€ 350</span>-->
                        <!--                                <span class="pending-pack">4 seats left</span>-->
                        <!--                            </td>-->
                        <!--                            <td class="alt-date-book"><span>11:00 - 12:00</span>-->
                        <!--                                <span>From</span> € 250-->
                        <!--                            </td>-->
                        <!--                            <td class="alt-date-book"><span>11:00 - 12:00</span>-->
                        <!--                                <span>From</span> € 250-->
                        <!--                            </td>-->
                        <!--                            <td class="alt-date-book"><span>11:00 - 12:00</span>-->
                        <!--                                <span>From</span> € 250-->
                        <!--                            </td>-->
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(".arrow-right").click(function(){
        $('.week_dates').fadeTo(1000, 0);
    });

    $(".arrow-left").click(function(){
        $('.week_dates').fadeTo(1000, 0);
    });

</script>