<?php
$next_timeslot = $course->get_next_timeslot([
    'unstarted_only' => Settings::instance()->get('upcoming_course_feed_order') == 'start_date'
]);

$schedule = $next_timeslot->id ? $next_timeslot->schedule : $course->schedules->where_available()->find();
?>
<a
    href="<?= $course->get_url().'&schedule_id='.$next_timeslot->schedule_id ?>"
    class="course-feed-item d-flex flex-column w-100"
    style="background-image: url('<?= $course->get_image_url(['fallback' => true]) ?>');"
>
    <div class="course-feed-item-header">
        <span class="course-feed-item-category">
            <?= htmlspecialchars($course->category->category) ?>
        </span>

        <h3 class="course-feed-item-title"><?= htmlspecialchars($course->title) ?></h3>
    </div>

    <div class="course-feed-item-footer mt-auto d-flex">
        <span class="course-feed-item-date">
            <?= $next_timeslot->datetime_start ? date('j M Y', strtotime($next_timeslot->datetime_start)) : '' ?>
        </span>

        <span class="course-feed-item-read_more ml-auto">More info</span>
    </div>
</a>