<div class="panel twitter-panel">
	<?php if ( ! empty($errors)): ?>
		<p><?= __('Error rendering Twitter feed. If this problem persists, please contact the administration.') ?></p>
	<?php elseif (is_array($tweets) AND count($tweets) > 0): ?>

		<div class="panel-heading">
			<h2>
				<span class="icon-twitter"></span>
				<?= __('<strong>Tweets</strong> by') ?>
				<a href="http://twitter.com/<?= $account->screen_name ?>" rel="author">@<?= $account->screen_name ?></a>
			</h2>
		</div>

		<div class="panel-body">
			<?php foreach ($tweets as $tweet): ?>
				<div class="twitter-panel-tweet">
					<div class="tweet-heading">
						<img src="<?= $tweet->user->profile_image_url ?>" alt="" width="30" />
						<span class="tweet-account_name"><?= $tweet->user->screen_name ?></span>
						<a class="tweet-screen_name" href="http://twitter.com/<?= $tweet->user->screen_name ?>" rel="author">@<?= $tweet->user->screen_name ?></a>
						<span class="icon-twitter"></span>
					</div>
					<div class="tweet-message">
						<?= isset($tweet->expanded_text) ? $tweet->expanded_text : nl2br($tweet->text) ?>
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