<section class="news-section news-section--<?= $feed_type ?>" id="<?= $feed_type ?>-section">
    <div class="row">
        <div class="swiper-button-prev"></div>

        <a class="news-slider-title-link" href="<?= ($feed_type == 'testimonials') ? '/testimonials.html' : '/news.html' ?>">
            <h2 class="news-slider-title"><?= ($feed_type == 'testimonials') ? __('Testimonials') : __('Latest News') ?></h2>
        </a>

        <div class="swiper-container" id="<?= $feed_type ?>-slider">
            <div class="swiper-wrapper">
                <?php foreach ($feed_items as $item): ?>
                    <div class="swiper-slide">
                        <div class="news-slider-summary">
                            <p title="<?= $item['summary'] ?>"><?= $item['summary'] ?></p>

                            <?php if ($feed_type == 'testimonials'): ?>
                                <p class="text-right"><strong>&mdash; <?= trim(trim($item['item_signature'].', '.$item['item_company']), ',') ?></strong></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($feed_type == 'news'): ?>
                            <a class="news-slider-link" href="/news/<?= $item['category'] ?>/<?= $item['news_url'] ?>"><?= __('Read more') ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="swiper-button-next"></div>

        <div class="swiper-container-horizontal">
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>