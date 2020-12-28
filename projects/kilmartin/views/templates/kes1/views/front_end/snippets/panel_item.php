<div class="panel">
    <?php if (isset($title) && isset($title_position) && $title_position == 'above'): ?>
        <div class="panel-title">
            <div>
                <h3><?= __($title) ?></h3>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($image)): ?>
        <a class="panel-image" href="<?= $link ?>" tabindex="-1">
            <img src="<?= $image ?>" alt="" />

            <?php if (!empty($date)): ?>
                <span class="panel-date">
                    <span class="panel-date-day"><?= date('d', strtotime($date)) ?></span>
                    <span class="panel-date-month"><?= date('M', strtotime($date)) ?></span>
                </span>
            <?php endif; ?>
        </a>
    <?php endif; ?>

    <?php if (isset($title) && isset($title_position) && $title_position == 'below'): ?>
        <div class="panel-title">
            <div>
                <h3><?= __($title) ?></h3>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($text)): ?>
        <div class="panel-text">
            <div><?= __($text) ?></div>
        </div>
    <?php endif; ?>

    <a href="<?= $link ?>" class="panel-link button"><?= __($button_text) ?></a>
</div>
