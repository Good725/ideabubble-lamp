<div class="twitter-feed">
	<?php if (is_array($tweets) AND count($tweets) > 0): ?>

		<div class="twitter-feed-heading">
			<h2 class="twitter-feed-title">
                <?= __(
                    ':subject <span>by :object</span>',
                    array(
                        ':subject' => '<strong>'.__('Tweets').'</strong>',
                        ':object' => '<a href="http://twitter.com/'.$account->screen_name.'" rel="author">@'.$account->screen_name.'</a>'
                    )
                ) ?>
			</h2>
		</div>

		<div class="twitter-feed-body">
			<?php foreach ($tweets as $tweet): ?>
				<div class="twitter-feed-tweet">
					<div class="tweet-heading">
						<span class="fa fa-twitter"></span>

						<?php if ($tweet->retweeted): ?>
							<div class="retweet-heading">
								<div>
									<span class="fa fa-retweet"></span> <?= $tweet->user->name ?>
								</div>
								<?= __('Retweeted') ?>
							</div>

							<img class="tweet-heading-img" src="<?= $tweet->retweeted_status->user->profile_image_url ?>" alt="" width="30" />
							<span class="tweet-account_name"><?= $tweet->retweeted_status->user->name ?></span>
							<a class="tweet-screen_name" href="http://twitter.com/<?= $tweet->retweeted_status->user->screen_name ?>" rel="author">@<?= $tweet->retweeted_status->user->screen_name ?></a>

						<?php else: ?>
							<img class="tweet-heading-img" src="<?= $tweet->user->profile_image_url ?>" alt="" width="30" />
							<span class="tweet-account_name"><?= $tweet->user->name ?></span>
							<a class="tweet-screen_name" href="http://twitter.com/<?= $tweet->user->screen_name ?>" rel="author">@<?= $tweet->user->screen_name ?></a>
						<?php endif; ?>
					</div>

					<div class="tweet-message">
						<?= $tweet->expanded_text ?>
					</div>

					<div class="tweet-footer">
						<?php $time = strtotime($tweet->created_at); ?>
						<a class="tweet-date" href="http://twitter.com/<?= $tweet->user->screen_name ?>/status/<?= $tweet->id ?>">
							<?= __(date('j', $time)) ?>
							<?= __(date('M', $time)) ?>
							<?= (date('Y', $time) == date('Y')) ? '' : __(date('Y', $time)) ?>
						</a>

						<div class="tweet-actions">
							<a class="tweet-like" href="https://twitter.com/intent/like?tweet_id=<?= $tweet->id ?>" target="_blank" title="<?= __('Like') ?>">
								<span class="sr-only"><?= __('Like') ?></span>
								<span class="fa fa-heart"></span>
							</a>

							<a class="tweet-retweet" href="https://twitter.com/intent/retweet?tweet_id=740935501599920128" title="<?= __('Retweet') ?>">
								<span class="sr-only"><?= __('Retweet') ?></span>
								<span class="fa fa-arrow-right"></span>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach ?>
		</div>
	<?php else: ?>
		<div class="twitter-feed-body">
			<p><?= __('No Tweets to display') ?></p>
		</div>
	<?php endif; ?>
</div>
