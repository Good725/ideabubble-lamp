    <a href="javascript:void(0)" class="close-package"><span class="fa fa-times" aria-hidden="true"></span></a>

    <h2 class="course-title-text">
        <?php
        $title_text = '';

        if (isset($course_schedule_details[0])) {
            $per_course = ($course['display_availability'] == 'per_course');
            $details    = current(array_filter($course_schedule_details));
            $details    = ($per_course && isset($details[0])) ? $details[0] : $details;

            $title_text .= isset($details['course_title']) ? $details['course_title'] : '';
            $title_text .= isset($details['level'])        ? ', '.$details['level']   : '';
            $title_text .= !$per_course && $date_formatted ? ', '.$date_formatted     : '';
        }

        echo trim(preg_replace("/, +/", ", ", $title_text), ',');
        ?>

        <span class="course-title-badges">
            <?php if ( ! empty($course['category'])): ?>
                <span class="badge"><?= $course['category'] ?></span>
            <?php endif; ?>
        </span>
    </h2>

    <?php if ($last_booking || count($schedule_views) > 0) { ?>
    <div class="course-activity-alert">
        <?php
        if ($last_booking) {
            $difftime = time() - strtotime($last_booking['created_date']);
            $hours = floor($difftime / 3600);
            $difftime -= $hours * 3600;
            $minutes = ceil($difftime / 60);
            if ($hours < 24) {
                ?>
                <p style="float: left;">Last booking made <?= ($hours ? $hours . ' hours ' : '') . $minutes ?> minutes ago</p>
                <?php
            }
        }
        ?>
        <?php if (count($schedule_views)) { ?>
        <p class="number-of-people-viewing" style="float: right"><?=count($schedule_views) . (count($schedule_views) == 1 ? ' person is' : ' people are')?> viewing this course now</p>
        <?php } ?>
    </div>
    <?php } ?>

    <?php // Todo add discounts and how many booked ?>

    <?php $summary = trim($course['display_availability'] == 'per_course' ? $course['summary'] : @$course_schedule_details[0]['description']); ?>

    <?php if ($summary): ?>
        <div class="summary-wrap page-content">
            <h3><?= __('Summary') ?></h3>

            <div class="toggleable_height show_less" data-height="8.5em"><?= $summary ?></div>

            <div class="toggleable_height-toggles">
                <button type="button" class="button--link toggleable_height-show_more"><?= __('Show more') ?></button>
                <button type="button" class="button--link toggleable_height-show_less"><?= __('Show less') ?></button>
            </div>
        </div>
    <?php endif; ?>

    <?php
    if ($course['display_availability'] == 'per_schedule')
    if (sizeof($topics) > 0):
    ?>
        <div class="topics-list">
            <h3>Topics</h3>

            <ul class="check-bullets">
                <?php foreach ($topics as $topic):?>
                    <li><?= $topic['name'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>


    <div class="classes-details-wrap full--view">
        <?php
        $user = Auth::instance()->get_user();
        $logged_in_user_permission = 'Add to cart';
        $contact_id = 0;
        $is_child = false;
        if (@$user['id']) {
            $contact = current(Model_Contacts3::get_contact_ids_by_user($user['id']));
            $contact_id = @$contact['id'];
            $contact_details = new Model_Contacts3($contact_id);
            $is_child = $contact_details->has_role('student');
        }
        ?>

        <?php if ($course['is_fulltime'] == 'YES'): ?>
            <?php $capacity_status = isset($schedule['id']) ? Model_KES_Bookings::check_schedule_capacity(['schedule_id' => $schedule['id']]) : []; ?>

            <ul class="details-wrap">
                <li>
                    <span class="flaticon-files-text"></span>
                </li>

                <?php if (@$schedule['location']): ?>
                    <li>
                        <span style="display: inline-block;">
                            <span class="fa fa-map-marker"></span>
                        </span>

                        <span style="display: inline-block; margin-left: .5em; text-align: left;">
                            <span><?= __('Location') ?>:</span>
                            <span><?= $schedule['plocation'] ? $schedule['plocation']: $schedule['location'] ?></span>
                        </span>
                    </li>
                <?php endif; ?>

                <?php if (@$schedule['plocation'] && @$schedule['location']): ?>
                    <li>
                        <span style="display: inline-block;">
                            <span class="fa fa-book"></span>
                        </span>

                        <span style="display: inline-block; margin-left: .5em; text-align: left;">
                            <span><?= __('Room') ?>:</span>
                            <span><?= $schedule['location'] ?></span>
                        </span>
                    </li>
                <?php endif; ?>

                <li>
                    <div class="price-wrap">
                        <span class="price">&euro; <?= $course['fulltime_price'] ?></span>
                        <span class="payfor"><?= __('Pre-pay') ?></span>
                    </div>

                    <span class="left-place">
                        <?php if ($capacity_status['remaining'] == 1) {
                            echo __('Only 1 place left.');
                        } elseif ($capacity_status['remaining'] > 1 && $capacity_status['remaining'] < 10) {
                            echo __('Only $1 places left', ['$1' => $capacity_status['remaining']]);
                        }
                        ?>
                    </span>
                </li>

                <li>
                    <?php if (!empty($capacity_status['full'])): ?>
                        <span class="button button--cancel"><?= __('You just missed it!') ?><br /><?= __('Our class is $1!', ['$1' => '<strong>'.__('FULL').'</strong>']) ?></span>
                    <?php else: ?>
                        <button type="button" class="button cart button--book fulltime" data-course_id="<?= $course['id'] ?>" data-fulltime_price="<?= $course['fulltime_price'] ?>"><?= __('Apply') ?></button>
                    <?php endif; ?>
                </li>
            </ul>

        <?php elseif((isset($course_schedule_details[0][0]) && @$course_schedule_details[0][0]['display_timeslots_on_frontend'] == 0) || (isset($course_schedule_details[0]['display_timeslots_on_frontend']) && @$course_schedule_details[0]['display_timeslots_on_frontend'] == 0)): ?>
        <?php
            if (isset($course_schedule_details[0][0])) {
                $pcourse_schedule_detail = current($course_schedule_details);
                $course_schedule_detail = current($pcourse_schedule_detail);
            } else {
                $course_schedule_detail = current($course_schedule_details);
            }
            $capacity_status = Model_KES_Bookings::check_schedule_capacity(['schedule_id' => $course_schedule_detail['id']]);
        ?>
        <ul class="details-wrap"
            data-amendable="<?=$course_schedule_detail['amendable']?>"
            data-booking-type="<?=$course_schedule_detail['booking_type']?>"
            data-course-title="<?=$course_schedule_detail['course_title']?>"
            data-date-formatted=""
            data-event-id=""
            data-fee-per="<?=$course_schedule_detail['fee_per']?>"
            data-logged-in-user-permission="<?=$logged_in_user_permission?>"
            data-schedule-id="<?=$course_schedule_detail['id']?>"
            data-schedule-type="<?=$course_schedule_detail['booking_type']?>"
            data-when-to-pay="<?=$course_schedule_detail['payment_type'] == 1 ? 'Pre-Pay' : 'PAYG'?>"
            data-year="<?= $course_schedule_detail['year'] ?>"
            data-year-id="<?= @$course_schedule_detail['year_id'] ?>"
            data-attend_all_default="<?= @$course_schedule_detail['attend_all_default'] ?>"
            data-trial_timeslot_free_booking="<?= @$course_schedule_detail['trial_timeslot_free_booking'] ?>">
            <li>
                <span class="flaticon-files-text"></span>
            </li>

            <?php if (@$schedule['location']): ?>
                <li>
                        <span style="display: inline-block;">
                            <span class="fa fa-map-marker"></span>
                        </span>

                        <span style="display: inline-block; margin-left: .5em; text-align: left;">
                            <span><?= __('Location') ?>:</span>
                            <span><?= $schedule['plocation'] ? $schedule['plocation']: $schedule['location'] ?></span>
                        </span>
                </li>
            <?php endif; ?>

            <?php if (@$schedule['plocation'] && @$schedule['location']): ?>
                <li>
                        <span style="display: inline-block;">
                            <span class="fa fa-book"></span>
                        </span>

                        <span style="display: inline-block; margin-left: .5em; text-align: left;">
                            <span><?= __('Room') ?>:</span>
                            <span><?= $schedule['location'] ?></span>
                        </span>
                </li>
            <?php endif; ?>

            <li>
                <div class="price-wrap">
                    <?php
                    if($course_schedule_detail['fee_per']=='Schedule'){
                        $fee = $course_schedule_detail['schedule_fee_amount'];
                    }else{
                        $fee = ($course_schedule_detail['time_slot_fee']) ? $course_schedule_detail['time_slot_fee'] : $course_schedule_detail['schedule_fee_amount'];
                    }
                    if(!$fee){
                        $fee = 0;
                    }
                    ?>
                    <span class="price">&euro; <?= $fee ?></span>
                                <span class="payfor"><?php
                                    if($course_schedule_detail['payment_type'] == 1){
                                        echo __("Pre-Pay");
                                    }else if($course_schedule_detail['payment_type'] == 2){
                                        echo __("Pay as you go");
                                    }else{
                                        echo '';
                                    }
                                    ?></span>
                </div>

                    <span class="left-place">
                        <?php if ($capacity_status['remaining'] == 1) {
                            echo __('Only 1 place left.');
                        } elseif ($capacity_status['remaining'] > 1 && $capacity_status['remaining'] < 10) {
                            echo __('Only $1 places left', ['$1' => $capacity_status['remaining']]);
                        }
                        ?>
                    </span>
            </li>

            <li>
                <?php if (!empty($capacity_status['full'])): ?>
                    <span class="button button--cancel"><?= __('You just missed it!') ?><br /><?= __('Our class is $1!', ['$1' => '<strong>'.__('FULL').'</strong>']) ?></span>
                <?php else: ?>
                    <button type="button" class="button cart button--book"><?= __('Add to Cart') ?></button><br />
                    <?php if ($course_schedule_detail['trial_timeslot_free_booking'] == 1) { ?>
                    <button type="button" class="button cart button--book trial"><?= __('Free trial') ?></button><br />
                    <?php } ?>
                    <br />

                    <?php if ($contact_id): ?>
                        <?php $in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $course_schedule_detail['id']))); ?>
                        <button class="button wishlist add button--book<?= $in_wishlist ? ' hidden' : '' ?>"><?= __('Add to wishlist') ?></button>
                        <a class="wishlist remove<?= $in_wishlist ? '' : ' hidden' ?>"><?= __('Remove from wishlist') ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        </ul>
        <?php else: ?>
            <h3 class="class-date-text"><?= __('Classes') ?> - <?= @$date_formatted?></h3>

            <?php foreach ($course_schedule_details as $pcourse_schedule_detail): ?>
                <?php
                if ($course['display_availability'] == 'per_schedule') {
                    $pcourse_schedule_detail = array($pcourse_schedule_detail);
                }
                ?>

                <?php foreach ($pcourse_schedule_detail as $course_schedule_detail): ?>
                    <?php $capacity_status = Model_KES_Bookings::check_schedule_capacity(['timeslot_id' => $course_schedule_detail['event_id']]); ?>

                    <ul
                        class="details-wrap"
                        data-amendable="<?=$course_schedule_detail['amendable']?>"
                        data-booking-type="<?=$course_schedule_detail['booking_type']?>"
                        data-course-title="<?=$course_schedule_detail['course_title']?>"
                        data-date-formatted="<?=$date_formatted?>"
                        data-event-id="<?=$course_schedule_detail['event_id']?>"
                        data-fee-per="<?=$course_schedule_detail['fee_per']?>"
                        data-logged-in-user-permission="<?=$logged_in_user_permission?>"
                        data-schedule-id="<?=$course_schedule_detail['id']?>"
                        data-schedule-type="<?=$course_schedule_detail['booking_type']?>"
                        data-when-to-pay="<?=$course_schedule_detail['payment_type'] == 1 ? 'Pre-Pay' : 'PAYG'?>"
                        data-year="<?= $course_schedule_detail['year'] ?>"
                        data-year-id="<?= @$course_schedule_detail['year_id'] ?>"
                        data-attend_all_default="<?= @$course_schedule_detail['attend_all_default'] ?>"
                        data-trial_timeslot_free_booking="<?= @$course_schedule_detail['trial_timeslot_free_booking'] ?>"
                        >
                        <li>
                            <span class="month"><?= date('F', strtotime($date)) ?></span>
                            <span class="date"><?= date('jS', strtotime($date)) ?></span>
                        </li>
                        <li>
                            <span class="time"><?= ($course_schedule_detail['start_time']) ? $course_schedule_detail['start_time'] : ''?></span>
                            <span class="location"><?= ($course_schedule_detail['location']) ? $course_schedule_detail['location'] : ''?></span>
                        </li>
                        <li class="sidelines">
                            <?php
                                // find time diff
                                $s = explode(":",$course_schedule_detail['start_time']);
                                $e = explode(":",$course_schedule_detail['end_time']);
                                $min = $e[1]-$s[1];
                                $hour = $e[0]-$s[0];
                                $diff = 60*$hour + $min;
                            ?>
                            <span class="mints"><?= $diff ?>mins</span>
                            <span class="icon">
                                    <span class="course-details-icon fa fa-book" aria-hidden="true"></span>
                                </span>
                            <span class="room-no"><?= ($course_schedule_detail['room']) ? $course_schedule_detail['room'] : ''?></span>
                        </li>
                        <li>
                            <span class="time"><?= ($course_schedule_detail['end_time']) ? $course_schedule_detail['end_time'] : ''?></span>
                            <span class="name"><?= ($course_schedule_detail['trainer']) ? $course_schedule_detail['trainer'] : ''?></span>
                        </li>
                        <li>
                            <div class="price-wrap">
                                <?php
                                    if($course_schedule_detail['fee_per']=='Schedule'){
                                        $fee = $course_schedule_detail['schedule_fee_amount'];
                                    }else{
                                        $fee = ($course_schedule_detail['time_slot_fee']) ? $course_schedule_detail['time_slot_fee'] : $course_schedule_detail['schedule_fee_amount'];
                                    }
                                    if(!$fee){
                                        $fee = 0;
                                    }
                                ?>
                                <?php if ($course['is_fulltime'] == 'NO') { ?>
                                    <?php if ($details['booking_type'] == 'Subscription') { ?>
                                    <span class="price">€ <?= $fee ?>/<?= $details['fee_per'] ?></span>
                                    <?php } else { ?>
                                    <span class="price">€ <?= $fee ?>/<?= !empty($details['is_group_booking']) ? 'delegate' : 'course' ?></span>
                                    <?php } ?>
                                <?php } ?>
                                <span class="schedule_count">
                                    <?php
                                    if ( $course_schedule_detail['booking_type'] == 'Whole Schedule') {
                                        $count = Model_Schedules::get_whole_schedule_events_count($course_schedule_detail['id']);
                                        echo 'inc ' .count($count) .  ' sessions';
                                    }
                                    ?>
                                </span>

                                <?php // Todo add prev ammount ?>
            <!--                    <span class="prv-price">€ 250</span>-->
                                <span class="payfor">
                                    <?php
                                    if($course_schedule_detail['payment_type'] == 1){
                                        echo "Pre-pay";
                                    }else if($course_schedule_detail['payment_type'] == 2){
                                        echo "Pay as you go";
                                    }else{
                                        echo '';
                                    }
                                    ?>
                                </span>
                                <br /><span class="amendable-text"><?= ($course_schedule_detail['amendable'] == 1 ? __('Amendable') : __('Not Amendable')) ?></span>
                                <span class="left-place">
                                    <?php if ($capacity_status['remaining'] == 1) {
                                        echo __('Only 1 place left.');
                                    } elseif ($capacity_status['remaining'] > 1 && $capacity_status['remaining'] < 10) {
                                        echo __('Only $1 places left', ['$1' => $capacity_status['remaining']]);
                                    }
                                    ?>
                                </span>
                            </div>
                        </li>

                        <?php if (!empty($details['is_group_booking']) ): ?>
                            <li>
                                <label for="course-offer-<?= $details['id'] ?>-delegates"><?= __('Delegates') ?></label>

                                <select class="course-offer-delegates form-input" id="course-offer-<?= $details['id'] ?>-delegates" style="display: block; width: 4em; margin: auto;">
                                    <?php for ($i = 1; $i <= $capacity_status['remaining']; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </li>
                        <?php endif; ?>

                        <li>
                            <?php if ($course['is_fulltime'] == 'NO'): ?>
                                <?php if (!empty($capacity_status['full'])): ?>
                                    <span class="button button--cancel"><?= __('You just missed it!') ?><br /><?= __('Our class is $1!', ['$1' => '<strong>'.__('FULL').'</strong>']) ?></span>
                                <?php else: ?>
                                    <button type="button" class="button cart button--book"><?= __('Add to Cart') ?></button><br /><br />
                                    <?php if ($course_schedule_detail['trial_timeslot_free_booking'] == 1) { ?>
                                    <button type="button" class="button cart button--book trial" data-trial_timeslot_free_booking="<?=$course_schedule_detail['trial_timeslot_free_booking']?>"><?= __('Free trial') ?></button><br /><br />
                                    <?php } ?>

                                    <?php if ($contact_id): ?>
                                        <?php $in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $course_schedule_detail['id']))); ?>
                                        <button class="button wishlist add button--book<?= $in_wishlist ? ' hidden' : '' ?>"><?= __('Add to wishlist') ?></button>
                                        <a class="wishlist remove<?= $in_wishlist ? '' : ' hidden' ?>"><?= __('Remove from wishlist') ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    </ul>
                <?php endforeach ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        $(".arrow-right").click(function(){
            $('.week_dates').fadeTo(1000, 0);
        });

        $(".arrow-left").click(function(){
            $('.week_dates').fadeTo(1000, 0);
        });

    </script>