<div id="content_for_courses--mobile" class="hidden--tablet hidden--desktop">
    <?php
    $courses_displayed = array();
    $availability_course_details_index = 0;
    //ob_clean();print_r($courses);exit;
    foreach ($categories_list as $category) {
        // Check that this category has not been excluded by the user's filters
        if (empty($args['category_ids']) || in_array($category['id'], $args['category_ids'])) {
            // Get all results which apply to the current filters and are in this category
            $category_courses = array();
            foreach ($courses as $course) {
                if ($course['category_id'] == $category['id']) {
                    $category_courses[] = $course;
                }
            }

            // Only continue if there are results in this category
            if (count($category_courses)) {
                echo '<h5 style="margin: .5em 0;">' . $category['category'] . '</h5>';

                foreach ($category_courses as $result) {
                    $display_index = $result['display_availability'] == 'per_schedule' ? 'schedule-' . $result['schedule_id'] : 'course' . $result['id'];
                    if (!isset($courses_displayed[$display_index])) {
                        $courses_displayed[$display_index] = $result;

                        if ($result['display_availability'] == 'per_schedule') {
                            ++$availability_course_details_index;
                            include 'availability_result.php';
                        } else {
                            ++$availability_course_details_index;
                            include 'availability_result.php';
                        }
                    } else {

                    }
                }
            }
        }
    }

    ?>


    <input type="submit" value="Continue" class="button button--continue button--full" />
</div>

<div class="hidden" id="availability-date-template">
    <a href="#" class="timeline-swiper-date availability-date">
        <strong class="timeline-swiper-highlight timeline-swiper-date-formatted"></strong><br />
        <span class="availability-date-price_range">
            <?= __('From $1', array('$1' => '&euro;<span class="availability-date-minimum"></span>')) ?>
        </span>
    </a>
</div>

<div class="hidden" id="availability-date-empty-template">
    <div class="timeline-swiper-date timeline-swiper-date--empty availability-date">
        <strong class="timeline-swiper-highlight timeline-swiper-date-formatted"></strong>
        <br />
        <span class="fa fa-ban"></span>
    </div>
</div>

<div class="hidden" id="availability-timeslot-template">
    <div class="availability-timeslot">
        <div class="row no-gutters">
            <div class="col-xs-6">
                <h5 class="availability-timeslot-date"></h5>
            </div>

            <div class="col-xs-6 availability-timeslot-remaining_spaces">
                <span class="singular hidden"><?= __('Only $1 place left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
                <span class="plural hidden"><?= __('Only $1 places left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
            </div>
        </div>

        <div class="availability-timeslot-details">
            <div class="text-center">
                <strong class="availability-timeslot-duration"></strong>
            </div>

            <div class="row no-gutters highlight text-center">
                <div class="col-xs-2">
                    <strong class="availability-timeslot-start_time"></strong>
                </div>

                <div class="col-xs-8 sidelines">
                    <span class="icon_book"></span>
                </div>

                <div class="col-xs-2">
                    <strong class="availability-timeslot-end_time"></strong>
                </div>
            </div>

            <div class="row no-gutters text-center">
                <div class="col-xs-2">
                    <span class="availability-timeslot-location"></span>
                </div>

                <div class="col-xs-offset-2 col-xs-4">
                    <div class="availability-timeslot-room"></div>
                </div>

                <div class="col-xs-4 text-right">
                    <div class="availability-timeslot-trainer"></div>
                </div>
            </div>
        </div>

        <div class="availability-timeslot-payment_type text-center"></div>

        <div class="availability-timeslot-per_schedule text-center">
            <button type="button" class="booked-hide button button--book availability-book">
                <?= __('Add to cart') ?>
                <span class="highlight">
                    &euro;<span class="availability-timeslot-price"></span>
                </span>
            </button>
            <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1">
                <span class="highlight">
                    <?= __('Free trial')?>
                </span>
            </button>
            <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
        </div>

        <div class="row gutters availability-timeslot-per_timeslot vertically_center">
            <div class="col-xs-6 text-left">
                <?= __('Price per class') ?>
                <strong class="availability-per_timeslot_price">&euro;<span class="availability-timeslot-price"></span></strong>
            </div>

            <div class="col-xs-6 text-right">
                <button type="button" class="booked-hide button button--book availability-book"><?= __('Add to cart') ?></button>
                <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1"><?= __('Free trial') ?></button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="hidden" id="availability-schedule_notimeslot-template">
    <div class="availability-timeslot">
        <div class="row no-gutters">
            <div class="col-xs-6 availability-timeslot-remaining_spaces">
                <span class="singular hidden"><?= __('Only $1 place left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
                <span class="plural hidden"><?= __('Only $1 places left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
            </div>
        </div>

        <div class="availability-timeslot-details">
            <div class="row no-gutters highlight text-center">
                <div class="col-xs-offset-2 col-xs-8 sidelines">
                    <span class="icon_book"></span>
                </div>

            </div>

            <div class="row no-gutters text-center">
                <div class="col-xs-2">
                    <span class="availability-timeslot-location"></span>
                </div>

                <div class="col-xs-offset-2 col-xs-4">
                    <div class="availability-timeslot-room"></div>
                </div>

                <div class="col-xs-4 text-right">
                    <div class="availability-timeslot-trainer"></div>
                </div>
            </div>
        </div>

        <div class="availability-timeslot-payment_type text-center"></div>

        <div class="availability-timeslot-per_schedule text-center">
            <div class="col-xs-6 text-left">
                <?= __('Price per class') ?>
                <strong class="availability-per_timeslot_price">&euro;<span class="availability-timeslot-price"></span></strong>
            </div>
            <div class="col-xs-6 text-right">
                <button type="button" class="booked-hide button button--book availability-book">
                    <?= __('Add to cart') ?>
                    <span class="highlight">
                        &euro;<span class="availability-timeslot-price"></span>
                    </span>
                </button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
            </div>
            <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1">
                <span class="highlight">
                    <?=__('Free trial')?>
                </span>
            </button>
        </div>

        <div class="row gutters availability-timeslot-per_timeslot vertically_center">
            <div class="col-xs-6 text-left">
                <?= __('Price per class') ?>
                <strong class="availability-per_timeslot_price">&euro;<span class="availability-timeslot-price"></span></strong>
            </div>

            <div class="col-xs-6 text-right">
                <button type="button" class="booked-hide button button--book availability-book"><?= __('Add to cart') ?></button>
                <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1"><?= __('Free Trial') ?></button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="hidden" id="availability-schedule_subscription-template">
    <div class="availability-timeslot">
        <div class="row no-gutters">
            <div class="col-xs-6 availability-timeslot-remaining_spaces">
                <span class="singular hidden"><?= __('Only $1 place left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
                <span class="plural hidden"><?= __('Only $1 places left', array('$1' => '<span class="availability-timeslot-remaining_spaces-amount"></span>')) ?></span>
            </div>
        </div>

        <div class="availability-timeslot-details">
            <div class="text-center">
                <strong class="availability-timeslot-duration"></strong>
            </div>

            <div class="row no-gutters highlight text-center">
                <div class="col-xs-2">
                    <strong class="availability-timeslot-start_time"></strong>
                </div>

                <div class="col-xs-2">
                    <strong class="availability-timeslot-end_time"></strong>
                </div>
            </div>

            <div class="row no-gutters highlight text-center">
                <div class="col-xs-offset-2 col-xs-8 sidelines">
                    <span class="icon_book"></span>
                </div>

            </div>

            <div class="row no-gutters text-center">
                <div class="col-xs-2">
                    <span class="availability-timeslot-location"></span>
                </div>

                <div class="col-xs-offset-2 col-xs-4">
                    <div class="availability-timeslot-room"></div>
                </div>

                <div class="col-xs-4 text-right">
                    <div class="availability-timeslot-trainer"></div>
                </div>
            </div>
        </div>

        <div class="availability-timeslot-payment_type text-center"></div>

        <div class="availability-timeslot-per_schedule text-center">
            <div class="col-xs-6 text-left">
                <?= __('Price per month') ?>
                <strong class="availability-per_timeslot_price">&euro;<span class="availability-timeslot-price"></span></strong>
            </div>
            <div class="col-xs-6 text-right">
                <button type="button" class="booked-hide button button--book availability-book">
                    <?= __('Add to cart') ?>
                    <span class="highlight">
                        &euro;<span class="availability-timeslot-price"></span>
                    </span>
                </button>
                <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1">
                    <span class="highlight">
                        <?=__('Free trial')?>
                    </span>
                </button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
            </div>
        </div>

        <div class="row gutters availability-timeslot-per_timeslot vertically_center">
            <div class="col-xs-6 text-left">
                <?= __('Price per class') ?>
                <strong class="availability-per_timeslot_price">&euro;<span class="availability-timeslot-price"></span></strong>
            </div>

            <div class="col-xs-6 text-right">
                <button type="button" class="booked-hide button button--book availability-book"><?= __('Add to cart') ?></button>
                <button type="button" class="booked-hide button button--book availability-book trial" data-trial_timeslot_free_booking="1"><?= __('Free trial') ?></button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook"><?= __('Remove booking') ?></button>
            </div>
        </div>
    </div>
</div>
