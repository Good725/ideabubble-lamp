<div class="course-list">
    <?php
    if (count($list) == 0) {
        echo '<p>There are no available courses for this category at the moment. View our <a href="/courses.html">courses page</a> for more information.</p>';
    }
    if ($list === false) {
        echo "</p>No courses found for this category</p>";
    }
    if (is_array($list) AND count($list) > 0):
        foreach ($list as $elem => $val):;?>
            <article class="Box">
                <header>
                    <div class="date"></div>
                    <h2><?= $val['title'] ?></h2>
                </header>
                <section class="box-content">
                    <p><?= $val['summary'] ?></p>
                </section>
                <section class="box-content">
                    <a target="_blank" href="https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom">
                        <img src="<?=URL::site()?>assets/02//images/applynow.png" alt="Apply Now" />
                    </a>
                </section>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
