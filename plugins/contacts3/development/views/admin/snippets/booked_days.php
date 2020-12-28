<?php if ( ! empty($bookedDays)): ?>
    <ul class="swiper-wrapper">
        <?php foreach ($bookedDays as $key => $day): ?>
            <li class="swiper-slide<?= $key == 3 ? ' active selected' : '' ?>">
                <a class="timeline-swiper-date" data-contact_id="<?= $day['contact_id'] ?>" data-date="<?= date('Y-m-d', strtotime($day['datetime_start'])) ?>">
                    <span class="timeline-swiper-highlight timeline-swiper-date-formatted"><?= date('D j M', strtotime($day['datetime_start'])); ?></span>

                    <?php if (@$day['classes']): ?>
                        <br /><span class="text-default">(<?= count($day['classes']) ?> slots)</span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="timeline-swiper-prev">
        <span class="arrow_caret-left"></span>
    </div>

    <div class="timeline-swiper-next">
        <span class="arrow_caret-right"></span>
    </div>
<?php else: ?>
    <div class="no-days">No days to display</div>
<?php endif; ?>