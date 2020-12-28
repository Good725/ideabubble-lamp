<?php $page = Model_Pages::get_page(trim($_SERVER['SCRIPT_URL'],'/')); ?>

<?php if ( ! empty($page[0]) AND in_array($page[0]['layout'], array('home', 'content'))): ?>
    <div class="fix-container">
        <ul class="grid-view">
            <?= $feed_items ?>
        </ul>
    </div>
<?php else: ?>
    <section class="full-row feedback-column">
        <div class="rotate-img"></div>
        <div class="fix-container">
            <div class="swiper-container" id="courses-carousel">
                <ul class="swiper-wrapper">
                    <?= $feed_items ?>
                </ul>
            </div>

            <div class="swiper-button-prev" id="courses-carousel-prev"></div>
            <div class="swiper-button-next" id="courses-carousel-next"></div>
        </div>
    </section>
<?php endif; ?>