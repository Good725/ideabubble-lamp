<div class="form-group availability-course">
    <button type="button" class="availability-course-toggle button button--continue button--full"
            data-hide_toggle="#availability-course-details--<?= $availability_course_details_index ?>-<?= $result['schedule_id'] ?><?= !empty($are_packages) ? '-package' : '' ?>"
            data-is_fulltime="<?=$result['is_fulltime']?>"
            data-display_availability="<?=$result['display_availability']?>"
            data-course-id="<?=$result['id']?>"
            data-schedule_id="<?=$result['display_availability'] == 'per_course' ? 'all' : $result['schedule_id']?>"
            data-display_timeslots="<?=$result['display_timeslots_on_frontend'] == 1 ? 'YES' : ''?>">
        <span class="col-xs-offset-2 col-xs-8">
            <span><?= $result['display_availability'] == 'per_course' ? $result['title'] : $result['schedule']?></span><br />
            <?php if ($result['is_fulltime'] == 'NO') { ?>
            <!-- <small>only n places left</small> -->
            <?php } ?>
        </span>

        <span class="col-xs-2">
            <span class="availability-course-toggle-icon">
                <span class="arrow_caret-down"></span>
            </span>
        </span>
    </button>

    <div class="availability-course-details hidden" id="availability-course-details--<?= $availability_course_details_index ?>-<?= $result['schedule_id'] ?><?= !empty($are_packages) ? '-package' : '' ?>"
         data-course-id="<?= $result['id'] ?>"
         data-is_fulltime="<?=$result['is_fulltime']?>"
         data-display_availability="<?=$result['display_availability']?>"
         data-course-id="<?=$result['id']?>"
         data-schedule_id="<?=$result['display_availability'] == 'per_course' ? 'all' : $result['schedule_id']?>"
         data-display_timeslots="<?=$result['display_timeslots_on_frontend'] == 1 ? 'YES' : ''?>"
        >
        <div class="availability-course-summary">
            <?= $result['summary'] ?>

            <button type="button" class="button--plain availability-date-read_more" data-course_id="<?= $result['id'] ?>" data-is_fulltime="<?=$result['is_fulltime']?>">
                <?= __('Read more') ?>
            </button>

            <?php if ($result['is_fulltime'] == 'YES') { ?>
            <div class="availability-fulltime text-center">
                <button type="button" class="booked-hide button button--book availability-book" data-course_id="<?= $result['id'] ?>">
                    <?= __('Add to cart') ?>
                    <span class="highlight">
                    &euro;<span class="availability-timeslot-price"><?=number_format($result['fulltime_price'], 2)?></span>
                </span>
                </button>
                <button type="button" class="unbooked-hide button--plain highlight availability-unbook" data-course_id="<?= $result['id'] ?>"><?= __('Remove booking') ?></button>
            </div>
            <?php } ?>
        </div>

        <?php if ($result['is_fulltime'] == 'NO') { ?>
        <div class="swiper-container timeline-swiper fullwidth--mobile">
            <div class="swiper-wrapper"></div>

            <div class="timeline-swiper-prev">
                <span class="arrow_caret-left"></span>
            </div>

            <div class="timeline-swiper-next">
                <span class="arrow_caret-right"></span>
            </div>
        </div>

        <div class="availability-timeslots">

        </div>
        <?php } ?>
    </div>
</div>