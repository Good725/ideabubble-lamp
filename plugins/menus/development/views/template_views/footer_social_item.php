<?php if ($social_media[strtolower($name).'_url']): ?>
    <li class="footer-social-item footer-social-item-<?= $icon ?>">
        <a target="_blank" href="<?= $social_media[strtolower($name).'_url'] ?>" title="<?= __($name) ?>">
            <span class="show-for-sr"><?= __($name) ?></span>
            <span class="fa fa-<?= $icon ?>"></span>
        </a>
    </li>
<?php endif; ?>