<?php if (count($social_media) > 0): ?>
    <ul class="social_media-list">
        <?php foreach ($social_media as $site => $details): ?>
            <li>
                <a target="_blank" href="<?= $details['url'] ?>" title="<?= $details['name'] ?>">
                    <span class="flaticon-<?= $site ?>"></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>