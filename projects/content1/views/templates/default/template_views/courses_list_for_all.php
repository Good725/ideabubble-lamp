<div class="newsHolder">

    <?php
    $list = Model_Schedules::get_listing_for_feed(true);
    if (count($list) == 0) {
        echo 'There are no available courses at the moment. Please try view our courses <a href="/courses.html">page</a> for more information.';
    }
    if (is_array($list) AND count($list) > 0):
        foreach ($list as $elem => $val):;?>
            <a style="border:none;display:block;"
               href="/courses/<?php echo IbHelpers::generate_friendly_url($val['category']) . '/' . IbHelpers::generate_friendly_url($val['title']) . '.html'; ?>/?id=<?= $val['id'] ?>">
                <article class="newsBox">
                    <header>
                        <div
                            class="date"><?= strtoupper(date("D", strtotime($val['start_date']))) . "<br/>" . date("jS", strtotime($val['start_date'])) . "<br/>" . date("M", strtotime($val['start_date'])); ?></div>
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