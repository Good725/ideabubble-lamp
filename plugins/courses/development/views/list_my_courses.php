<div class="my_courses-table-wrapper">
    <table class="table dataTable" id="my_courses-table">
        <thead>
            <tr>
                <th scope="col">Course</th>
                <th scope="col">Type</th>
                <th scope="col">Booked</th>
                <th scope="col">Access open</th>
                <th scope="col">Access closed</th>
                <th scope="col">Progress</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($my_bookings as $booking): ?>
                <?php foreach ($booking->schedules->find_all() as $schedule): ?>
                    <?php $complete_lessons = $schedule->content->count_user_complete_subsections(); ?>
                    <tr>
                        <td><?= htmlentities($schedule->course->title) ?></td>
                        <td><?= htmlentities($schedule->study_mode->study_mode) ?></td>
                        <td><?= htmlentities(date('H:i j F Y', strtotime($booking->created_date))) ?></td>
                        <td><?= Ibhelpers::relative_time_with_tooltip($booking->content_available_from_date()) ?></td>
                        <td><?= Ibhelpers::relative_time_with_tooltip($booking->content_available_to_date()) ?></td>
                        <td><?= $complete_lessons ?> / <?= $schedule->content->count_lessons() ?></td>
                        <td>
                            <?php if ($schedule->content->is_booked()): ?>
                                <div class="action-btn">
                                    <a class="btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                                    </a>

                                    <ul class='dropdown-menu'>
                                        <li><a href="/admin/courses/my_course/<?= $schedule->id ?>"><?= $complete_lessons ? 'Continue' : 'Start' ?></a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="my_courses-table-empty hidden">
    <p>No courses to display</p>
</div>