<div class="news-category-embed fullwidth">
    <div class="container">
        <div class="news-category-embed-intro row p-0">
            <h1 class="news-category-embed-intro-title">
                <span class="news-category-embed-intro-title-prefix">News / </span>
                <a class="news-cateogry-embed-intro-title-link" href="/news/<?= urlencode($category->category) ?>">
                    <?= htmlspecialchars($category->category) ?>
                </a>
            </h1>

            <?php if (!empty($intro)): ?>
                <p><?= $intro ?></p>
            <?php endif; ?>
        </div>

        <div class="row gutters d-md-flex">
            <?php for ($i = 0; $i < count($news) && $i < 3; $i++): ?>
                <?php $item = $news[$i]; ?>

                <div class="news-category-column col-sm-4 d-md-flex mb-4">
                    <?php
                    $theme = Model_Engine_Theme::get_current_theme();
                    $bg_class = ($i % 2 == 0) ? 'bg-white' : 'bg-success';
                    $text_class   = ($i % 2 == 0) ? 'text-black' : 'text-white';

                    if ($theme == '49') { // ITT
                        $button_class = ($i % 2 == 0) ? 'button bg-success' : 'button bg-primary';
                        $button_text  = __('Read full article');
                    } else {
                        $button_class = ($i % 2 == 0) ? 'text-primary' : 'text-white';
                        $button_text  = __('Read more');
                    }
                    include 'snippets/news_item_embed.php';
                    ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="container clearfix text-center mt-0 mt-sm-5">
        <a href="/news" class="button news-category-embed-see_more">
            See more <span class="news-category-embed-see_more-type">news</span>
        </a>
    </div>
</div>