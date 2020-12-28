<?php
// The controller should really give us an array of feed items, rather than a string containing all of them merged
// This code splits the string into an array.
$feed_items = explode('<span class="dummy"></span>', $feed_items);
array_shift($feed_items);
?>

<?php if (count($feed_items) > 0): ?>
	<div class="orbit ib-news-orbit"
		 role="region"
		 data-orbit<?= ($animation_type == 'fade') ? ' data-options="animInFromLeft:fade-in; animInFromRight:fade-in; animOutToLeft:fade-out; animOutToRight:fade-out;"' : '' ?>
		 data-timer-delay=<?= $timeout ? $timeout : 8000 ?>
		>
		<button class="orbit-previous"><span class="show-for-sr"><?= __('Previous slide') ?></span>&#9664;&#xFE0E;</button>
		<button class="orbit-next"><span class="show-for-sr"><?= __('Next slide') ?></span>&#9654;&#xFE0E;</button>

		<ul class="orbit-container">
			<?php for ($i = 0; $i < count($feed_items); $i += 2): ?>
				<li class="orbit-slide<?= ($i == 0) ? ' active' : '' ?>">
					<?= $feed_items[$i] ?>
					<?= isset($feed_items[$i+1]) ? $feed_items[$i+1] : '' ?>
				</li>
			<?php endfor; ?>
		</ul>
		<nav class="orbit-bullets">
			<?php for ($i = 0; $i < count($feed_items); $i+=2): ?>
				<button data-slide="<?= $i/2 ?>"<?= ($i == 0) ? ' class="is-active"' : '' ?>><span class="show-for-sr">slide <?= $i / 2 + 1 ?></span></button>
			<?php endfor; ?>
		</nav>
	</div>
<?php endif; ?>