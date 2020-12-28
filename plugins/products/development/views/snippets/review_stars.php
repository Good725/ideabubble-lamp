<div class="review-form-rating-stars">
	<?php for ($i = 5; $i >= 1 AND $i <= 5; $i--): ?>
		<input
			type="radio"
			id="review-form-rating-<?= $i ?><?= $number ? '_'.$number : '' ?>"
			name="rating<?= $number ? '_'.$number : '' ?>"
			value="<?= $i ?>"
			<?= ($rating == $i) ? ' checked="checked"' : '' ?>
			/>
		<label for="review-form-rating-<?= $i ?><?= $number ? '_'.$number : '' ?>" title="Rate <?= $i ?> <?= ($i ==1) ? 'star' : 'stars' ?>"><?= $i ?></label>
	<?php endfor; ?>
</div>
