<div id="course-menu-left">
    <h3>Courses</h3>
    <ul>
        <?php foreach ($items as $elem => $val): ?>
            <li>
                <a href="/courses/<?php echo IbHelpers::generate_friendly_url($val['category']) . '.html'; ?>"><?= $val['category'] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
