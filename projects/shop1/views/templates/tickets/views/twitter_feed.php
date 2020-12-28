<div class="twitter_feed">
    <?php if (is_array($tweets) AND count($tweets) > 0): ?>
        <header>
            <h4 class="twitter_feed-title"><span class="flaticon-twitter"></span> <?= __('Twitter Updates') ?></h4>
            <strong>
                <?= $account->name ?>
                <a href="http://twitter.com/<?= $account->screen_name ?>" rel="author">@<?= $account->screen_name ?></a>
            </strong>
        </header>

        <ul class="list-unstyled twitter_feed-list">
            <?php foreach ($tweets as $tweet): ?>
                <li>
                    <a href="http://twitter.com/<?= $tweet->user->screen_name ?>/status/<?= $tweet->id ?>">
                        <?= $tweet->expanded_text ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p><?= __('No Tweets to display') ?></p>
    <?php endif; ?>
</div>