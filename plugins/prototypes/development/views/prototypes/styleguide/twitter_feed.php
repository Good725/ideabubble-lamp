<div id="ibTwitterFeed">
    <?php
    $apc_cache_key = 'cms_twitter_api_data:' . $_SERVER['HTTP_HOST'];
    /**
     * Cache results from the Twitter API. Re-check every hour.
     * We want to avoid calling the API on every page load, so we don't exceed the rate limit
     **/

    // If alternative PHP caching has been set up and the cached data exists
    if (function_exists('apc_exists') AND apc_exists($apc_cache_key))
    {
        // Get the cached data
        $twitter_api_data = apc_fetch($apc_cache_key);
    }
    else
    {
        // Get the data from the API
        $twitter_api = new IbTwitterApi(
            Settings::instance()->get('twitter_api_key_right'),
            Settings::instance()->get('twitter_api_secret_key_right'),
            Settings::instance()->get('twitter_api_access_token_right'),
            Settings::instance()->get('twitter_api_secret_access_token_right')
        );
        $twitter_api_data = array(
            'account' => $twitter_api->get('account/settings'),
            'tweets'  => $twitter_api->get_tweets()
        );

        if (function_exists('apc_exists'))
        {
            // Cache the data for one hour
            apc_store($apc_cache_key, $twitter_api_data, 60 * 60);
        }
    }

    $tweets = $twitter_api_data['tweets'];
    $account = $twitter_api_data['account'];
    ?>

    <div class="panel twitter-panel">
        <?php if (is_array($tweets) AND count($tweets) > 0): ?>
            <?php $twitter_account = $tweets[0]->user; ?>

            <div class="panel-heading">
                <h2>
                    <span class="icon-twitter"></span>
                    <strong>Tweets</strong>
                    by <a href="http://twitter.com/<?= $twitter_account->screen_name ?>" rel="author">@<?= $twitter_account->screen_name ?></a>
                </h2>
            </div>

            <div class="panel-body">
                <?php foreach ($tweets as $tweet): ?>
                    <div class="twitter-panel-tweet">
                        <div class="tweet-heading">
                            <img src="<?= $twitter_account->profile_image_url ?>" alt="" width="30" />
                            <span class="tweet-account_name"><?= $twitter_account->name ?></span>
                            <a class="tweet-screen_name" href="http://twitter.com/<?= $twitter_account->screen_name ?>" rel="author">@<?= $twitter_account->screen_name ?></a>
                            <span class="icon-twitter"></span>
                        </div>
                        <div class="tweet-message">
                            <?= nl2br($tweet->text) ?>
                        </div>
                        <div class="tweet-footer">
                            <a class="tweet-date" href="http://twitter.com/<?= $tweet->user->screen_name ?>/status/<?= $tweet->id ?>">
                                <?= date('j F Y', strtotime($tweet->created_at)) ?>
                            </a>

                            <div class="tweet-actions">
                                <a class="tweet-like" href="https://twitter.com/intent/like?tweet_id=<?= $tweet->id ?>" target="_blank" title="Like">
                                    <span class="sr-only">Like</span>
                                    <span class="icon-heart"></span>
                                </a>

                                <a class="tweet-retweet" href="https://twitter.com/intent/retweet?tweet_id=740935501599920128" title="Retweet">
                                    <span class="sr-only">Retweet</span>
                                    <span class="icon-arrow-right"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <div class="panel-body">
                <p><?= __('No Tweets to display') ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
