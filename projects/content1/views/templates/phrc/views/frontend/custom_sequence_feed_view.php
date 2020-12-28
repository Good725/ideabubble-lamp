<?php $slides = substr_count($sequence_items, 'class="orbit-slide'); ?>

<div class="orbit"
	 role="region"
	 data-orbit<?= ($sequence_data['animation_type'] == 'fade') ? ' data-options="animInFromLeft:fade-in; animInFromRight:fade-in; animOutToLeft:fade-out; animOutToRight:fade-out;"' : '' ?>
	 data-timer-delay=<?= $sequence_data['timeout'] ? $sequence_data['timeout'] : 8000 ?>
	>
	<?php if ($sequence_data['controls']): ?>
		<button class="orbit-previous"><span class="show-for-sr"><?= __('Previous slide') ?></span>&#9664;&#xFE0E;</button>
		<button class="orbit-next"><span class="show-for-sr"><?= __('Next slide') ?></span>&#9654;&#xFE0E;</button>
	<?php endif; ?>
	<ul class="orbit-container">
		<?= (isset($sequence_items)) ? $sequence_items : '' ?>
	</ul>

	<?php if ($sequence_data['pagination'] == 1 AND count($slides) > 0): ?>
		<nav class="orbit-bullets">
			<?php for ($i = 0; $i < $slides; $i++): ?>
				<button data-slide="<?= $i ?>"<?= ($i == 0) ? ' class="is-active"' : '' ?>><span class="show-for-sr">slide <?= $i + 1 ?></span></button>
			<?php endfor; ?>
		</nav>
	<?php endif; ?>
</div>
