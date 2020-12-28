<div class="newsHolder">

    <?php
    $list = Model_Schedules::get_listing_for_front(false);
    if (count($list) == 0) {
        echo 'There are no available courses at the moment. Please try view our courses <a href="/courses.html">page</a> for more information.';
    }
    if ($list === false) {
        echo "Invalid course search criteria.";
    }
    if (is_array($list) AND count($list) > 0):
        foreach ($list as $elem => $val):;?>
            <a style="border:none;display:block;"
               href="/courses/<?php echo IbHelpers::generate_friendly_url($val['category']) . '/' . IbHelpers::generate_friendly_url($val['title']) . '.html'; ?>/?id=<?= $val['id'] ?>">
                <article class="newsBox">
                    <header>
                        <div
                            class="date"><?= strtoupper(date("D", strtotime($val['schedule_day']))) . "<br/>" . date("jS", strtotime($val['schedule_day'])) . "<br/>" . date("M", strtotime($val['schedule_day'])); ?></div>
                        <h3><?= $val['title'] ?></h3>
                    </header>
                    <section class="box-content">
                        <p><?= $val['summary'] ?></p>
                    </section>
                </article>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>