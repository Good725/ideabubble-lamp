<div class="news-feed-item d-flex flex-column w-100 <?= !empty($bg_class) ? $bg_class : 'bg-white' ?> shadow">
    <?php if ($item->image): ?>
        <a href="<?= $item->get_url() ?>" tabindex="-1" class="text-decoration-none">
            <img class="d-block w-100" src="<?= $item->get_image_url() ?>" alt="<?= htmlspecialchars($item->alt_text) ?>" />
        </a>
    <?php endif; ?>

    <div class="news-feed-item-body mt-auto">
        <header class="mt-0 mb-3 mb-md-4">
            <a href="<?= $item->get_url() ?>" tabindex="-1" class="text-decoration-none">
                <h4 class="news-feed-item-title <?= !empty($text_class) ? $text_class : 'text-black' ?>">
                    <?= htmlspecialchars($item->title) ?>
                </h4>
            </a>

            <div class="mt-3 news-feed-item-data">
                <?php if ($item->author): ?>
                    By <span><?= htmlspecialchars($item->author) ?></span> |
                <?php endif; ?>
                <time datetime="<?= $item->get_date('Y-m-d') ?>"><?= htmlspecialchars($item->get_date('j F Y')) ?></time>
            </div>
        </header>

        <a href="<?= $item->get_url() ?>" class="news-feed-item-button <?= !empty($button_class) ? $button_class : 'button bg-primary' ?>">
            <?= htmlspecialchars(!empty($button_text) ? $button_text : __('Read full article')) ?>
        </a>
    </div>
</div>