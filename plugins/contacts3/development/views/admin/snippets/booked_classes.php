<ul class="swiper-wrapper">
    <?php if ($bookedCourses) foreach ($bookedCourses as $course): ?>
        <li class="swiper-slide attendance-day-item" tabindex="0">
            <a
                class="timeline-swiper-date class"
                href="javascript:void(0)"
                data-contact_id="<?=$course['contact_id']?>"
                data-id="<?= $course['booking_item_id'] ?>"
                data-attending="<?= $course['attending'] ?>"
                data-date="<?=date('Y-m-d', strtotime($course['datetime_start']))?>"
                data-time="<?=date('H:i', strtotime($course['datetime_start']))?>"

                data-toggle="popover"
                data-trigger="hover"
                data-html="true"
                data-placement="top"
                data-content="<?= trim($course['note']) ?>"
                >
                <span><?= date('D j M', strtotime($course['datetime_start'])) ?></span>

                <div class="attendance-course-name timeline-swiper-highlight"><?= $course['course'] ?></div>

                <span><?= date('h a', strtotime($course['datetime_start'])) ?></span>

                <?php if ($course['attending']): ?>
                    <?php if (in_array('Absent', $course['timeslot_status'])): ?>
                        <span class="text-absent icon_error-circle_alt" aria-hidden="true" title="Absent"></span>
                    <?php elseif (in_array('Late', $course['timeslot_status'])): ?>
                        <span class="text-late icon icon_error-triangle_alt" aria-hidden="true" title="Late"></span>
                    <?php elseif (in_array('Early Departures', $course['timeslot_status'])): ?>
                        <span class="text-left_early icon icon_error-oct_alt" aria-hidden="true" title="Early Departures"></span>
                    <?php else: ?>
                        <span class="text-present icon_check" aria-hidden="true"></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-not_attending icon_error-circle_alt" aria-hidden="true"></span>
                <?php endif; ?>
            </a>

            <?php $can_manage_attendance = ( ! empty($logged_in_contact) AND $logged_in_contact->has_preference('db-mn-ads-gu')); ?>

            <?php if (trim($course['note']) OR $can_manage_attendance): ?>
                <ul class="edit-attendance-menu hidden">
                    <?php if (trim($course['note'])): ?>
                        <li><a class="view-note">View Note <span class="icon-angle-right" aria-hidden="true"></span></a>
                            <?php include 'view_notes.php'; ?></li>
                        <li><hr /></li>
                    <?php endif; ?>

                    <?php if ($can_manage_attendance): ?>
                        <li><p><?= $course['attending'] ? 'Will Not Attend' : 'Will Attend' ?></p></li>
                        <li class="bullets"><a class="edit-attendance-one">This One <span class="icon-angle-right" aria-hidden="true"></span></a> </li>
                        <li class="bullets"><a class="edit-attendance-until">Until <span class="icon-angle-right" aria-hidden="true"></span></a></li>
                        <li class="bullets"><a class="edit-attendance-weekly">Weekly <span class="icon-angle-right" aria-hidden="true"></span></a> </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<div class="timeline-swiper-prev">
    <span class="arrow_caret-left"></span>
</div>

<div class="timeline-swiper-next">
    <span class="arrow_caret-right"></span>
</div>