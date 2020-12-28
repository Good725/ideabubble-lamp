<?php
$course = $result['course'];
$topic = $course->get_linked_page();
$show_course_data = Settings::instance()->get('show_in_course_result');
$show_course_data = is_array($show_course_data) ? $show_course_data : [];
$data_icons = '';
$filters = empty($_POST) && isset($args) ? $args : $_POST;
$next_timeslot = $course->get_next_timeslot($filters);

// Use the schedule of the next timeslot
// If there are no timeslots, use whichever schedule the code finds first.
$schedule = $next_timeslot->id
    ? $next_timeslot->schedule
    : $course->schedules->where_available()->find();
?>

<form
    action="<?= $result['link'] ?>"
    method="get"
    class="course-list-item list_only bg-white border-category shadow"
    data-type="<?= $result['type'] ?>"
    data-id="<?= $result['id'] ?>"
    <?= $course->get_color() ? 'style="--category-color: '.$course->get_color().';"' : '' ?>
>
    <input type="hidden" name="id" value="<?= $result['id'] ?>" />
    <?php if (!in_array('date_selector', $show_course_data) && !empty($result['link'])): ?>
        <?php if ($schedule->id): ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule->id ?>" />
        <?php endif; ?>
    <?php endif; ?>

    <div class="pl-lg-3">
        <div class="row no-gutters d-lg-flex pb-2 border-bottom">
            <div class="<?= (in_array('date_selector', $show_course_data)) ? 'col-md-8' : 'col-md-12' ?>">
                <?php if (in_array('category', $show_course_data)): ?>
                    <span class="course-list-item-category"><?= htmlspecialchars($course->category->category) ?></span>
                <?php endif; ?>

                <h4 class="course-list-item-header"><?= htmlspecialchars($course->title) ?></h4>
            </div>

            <?php if (in_array('date_selector', $show_course_data)): ?>
                <div class="col-md-4 pl-lg-4 mt-2 mt-lg-0 d-flex flex-column">
                    <?php if ($result['times_options']): ?>
                        <?php $select_id = 'search_'.$result['type'].' '.$result['id'].'_times'; ?>

                        <label class="sr-only" for="<?= $select_id ?>"><?= __('Time and date')?></label>

                        <?php ob_start(); ?>
                            <option value=""><?= __('Choose date') ?></option>
                            <?php foreach ($result['times_options'] as $option): ?>
                                <option<?= html::attributes($option['attributes']) ?>><?= $option['text'] ?></option>
                            <?php endforeach; ?>
                        <?php $options = ob_get_clean(); ?>

                        <div class="form-select-plain mx-lg-2">
                            <?= Form::ib_select(null, null, $options, null, ['class' => 'course-widget-schedule', 'id' => $select_id ]); ?>
                        </div>
                    <?php elseif ($result['date_start']): ?>
                        <div class="course-widget-time_and_date-text">
                            <div>
                                <span class="nowrap"><?= date('F j, g:i a', strtotime($result['date_start'])) ?></span>
                                <?= ($result['date_end'] && $result['date_end'] != $result['date_start']) ?  ' &ndash; <span class="nowrap">'.date('F j, g:i a', strtotime($result['date_end'])).'</span>'  : '' ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row no-gutters d-lg-flex">
            <div class="col-md-8">
                <?php if (array_intersect(['duration', 'county', 'start_date'], $show_course_data)): ?>
                    <?php
                    $items = [];
                    if (in_array('duration', $show_course_data)) {
                        $days = $schedule->timeslots->where_undeleted()->count_all();
                        if (!empty($days)) {
                            $items[] = ['type' => 'duration', 'svg' => 'clock', 'text' => ($days == 1) ? '1 session' : $days.' sessions'];
                        }
                    }
                    if (in_array('county', $show_course_data)) {
                        $county = $schedule->location->get_county()->name;
                        if ($county) {
                            $items[] = ['type' => 'location', 'svg' => 'location', 'text' => $county];
                        }
                    }
                    if (in_array('start_date', $show_course_data)) {
                        $date = $schedule->get_next_timeslot()->datetime_start;
                        if ($date) {
                            $items[] = ['type' => 'date', 'svg' => 'calendar', 'text' => date('j F Y', strtotime($date))];
                        }
                    }
                    ?>

                    <?php ob_start(); ?>
                        <ul class="course-list-item-data">
                            <?php foreach ($items as $item): ?>
                                <li class="d-flex align-items-center" data-type="<?= $item['type'] ?>">
                                    <?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/'.$item['svg'].'.svg') ?>
                                    <?= htmlspecialchars($item['text']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php echo $data_icons = ob_get_clean(); ?>
                <?php endif; ?>

                <div class="course-list-item-summary py-2">
                    <?= $course->summary ?>
                </div>

                <?php if (in_array('topics', $show_course_data) && $topic->text): ?>
                    <h6 class="m-0" style="font-size: 14px">
                        Topic:
                        <?php if ($topic->page->name_tag): ?>
                            <a href="/<?= $topic->page->name_tag ?>" class="text-category"><?= htmlentities($topic->text) ?></a>
                        <?php else: ?>
                            <span class="text-category"><?= htmlentities($topic->text) ?></span>
                        <?php endif; ?>
                    </h6>
                <?php endif; ?>
            </div>

            <div class="col-md-4 pl-lg-4 mb-1 d-lg-flex flex-column clearfix">
                <?php if ($data_icons): ?>
                    <button type="submit" class="button--plain course-list-item-read_more mt-auto ml-auto text-right">
                        <?= htmlspecialchars(__('More info')) ?>
                    </button>
                <?php else: ?>
                    <button type="submit" class="button course-list-item-button mt-auto mx-lg-2 px-5 px-lg-1 py-lg-3">
                        <?= htmlspecialchars(__('See details')) ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<div class="grid_only col-xs-12 col-md-4 d-flex mb-4">
    <form
        action="<?= $result['link'] ?>"
        method="get"
        class="course-list-item grid_only d-flex flex-column border-top-category bg-white shadow"
        style="<?= $course->get_color() ? '--category-color: '.$course->get_color().';' : '' ?>flex: 1;"
        data-type="<?= $result['type'] ?>"
        data-id="<?= $result['id'] ?>"
    >
        <input type="hidden" name="id" value="<?= $result['id'] ?>" />
        <?php if (!in_array('date_selector', $show_course_data) && !empty($result['link'])): ?>
            <?php if ($schedule->id): ?>
                <input type="hidden" name="schedule_id" value="<?= $schedule->id ?>" />
            <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('category', $show_course_data)): ?>
            <span class="course-list-item-category"><?= htmlspecialchars($course->category->category) ?></span>
        <?php endif; ?>

        <div class="course-list-item-header-wrapper">
            <h2 class="course-list-item-header mt-0 mb-4 hidden--tablet hidden--desktop"><?= htmlspecialchars($course->title) ?></h2>
            <h4 class="course-list-item-header mt-0 mb-4 hidden--mobile"><?= htmlspecialchars($course->title) ?></h4>
        </div>

        <?php if (in_array('topics', $show_course_data)): ?>
            <h6 class="mt-0 mb-3" style="font-size: 14px; line-height: 19px;">
                Topic:
                <?php if ($topic->text): ?>
                    <a href="/<?= $topic->page->name_tag ?>" class="text-category"><?= htmlentities($topic->text) ?></a>
                <?php else: ?>
                    <span class="text-category"><?= htmlentities($topic->text) ?></span>
                <?php endif; ?>
            </h6>
        <?php endif; ?>

        <?php if (in_array('date_selector', $show_course_data) && $result['times_options']): ?>
            <div class="mt-auto mb-1">
                <label class="sr-only" for="<?= $select_id ?>_grid"><?= __('Time and date')?></label>

                <div class="form-select-plain d-block d-sm-inline-block d-md-block">
                    <?= Form::ib_select(null, 'schedule_id', $options, null, ['class' => 'course-widget-schedule text-left text-md-center', 'id' => $select_id.'_grid' ]); ?>
                </div>
            </div>
        <?php endif; ?>

        <?= $data_icons ?>

        <?php if ($data_icons): ?>
            <div class="d-flex mt-1">
                <button type="submit" class="button--plain course-list-item-read_more mt-auto ml-auto text-right">
                    <?= htmlspecialchars(__('More info')) ?>
                </button>
            </div>
        <?php else: ?>
            <div class="mt-1">
                <button type="submit" class="button course-list-item-button d-block d-sm-inline-block d-md-block">
                    <?= htmlspecialchars(__('See details')) ?>
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>