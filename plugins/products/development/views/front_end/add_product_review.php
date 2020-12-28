<?php if (Settings::instance()->get('enable_customer_reviews') == '1'): ?>
	<div class="product-reviews-form-wrapper" id="product-review-form">
		<div>
			<?= IbHelpers::get_messages(); ?>
		</div>

		<?php if (isset($average_rating)): ?>
			<div class="product-review-rating">
				<span class="product-review-rating-amount"><?= round($average_rating) ?></span>
				<span class="product-review-rating-stars">
					<?php for ($i = 0; $i < 5; $i++): ?>
						<?php if ($i < round($average_rating)): ?>
							<span></span>
						<?php else: ?>
							<span class="empty_star"></span>
						<?php endif; ?>
					<?php endfor; ?>
				</span>
			</div>
			<?php if (isset($count_ratings)): ?>
				<div>(<?= $count_ratings ?> reviews)</div>
			<?php endif; ?>
		<?php endif; ?>

		<button type="button" class="product-review-button" id="write-product-review-button">Write a customer review</button>

		<form class="review-form add-product-review-form" id="add-product-review-form" action="/frontend/products/add_review/" method="post">
			<h3>Add your review</h3>

			<input type="hidden" name="product_id" value="<?= isset($product_id) ? $product_id : '' ?>" />
			<input type="hidden" name="return_page" value="<?= URL::site(Request::detect_uri(), TRUE) ?>" />

			<div class="review-form-group">
				<div class="review-form-label"><?= __('Please click how many stars you want to give this item') ?></div>
				<div class="review-form-controls">
					<div class="review-form-rating-stars">
						<?php for ($i = 5; $i >= 1 AND $i <= 5; $i--): ?>
							<input type="radio" class="validate[required]" id="review-form-rating-<?= $i ?>" name="rating" value="<?= $i ?>" />
							<label for="review-form-rating-<?= $i ?>" title="Rate <?= $i ?> <?= ($i ==1) ? 'star' : 'stars' ?>"><?= $i ?></label>
						<?php endfor; ?>
					</div>
				</div>
			</div>

			<div class="review-form-group">
				<label class="review-form-label" for="edit-review-title"><?= __('Title') ?></label>
				<div class="review-form-controls">
					<input type="text" class="review-form-control validate[required]" id="edit-review-title" name="title" />
				</div>
			</div>

			<div class="review-form-group">
				<label class="review-form-label" for="edit-review-review"><?= __('Review') ?></label>
				<div class="review-form-controls">
					<textarea class="review-form-control validate[required]" id="edit-review-review" name="review" rows="8"></textarea>
				</div>
			</div>

			<div class="review-form-group">
				<label class="review-form-label" for="edit-review-author"><?= __('Full name') ?></label>
				<div class="review-form-controls">
					<input type="text" class="review-form-control validate[required]" id="edit-review-author" name="author" value="" />
				</div>
			</div>

			<div class="review-form-group">
				<label class="review-form-label" for="edit-review-email"><?= __('Email') ?></label>
				<div class="review-form-controls">
					<input type="text" class="review-form-control validate[required,custom[email]]" id="edit-review-email" name="email" />
				</div>
			</div>

			<div class="review-form-group">
				<label class="review-form-label"></label>
				<div class="review-form-controls">
					<button type="submit" class="product-review-button"><?= __('Post my review') ?></button>
				</div>
			</div>
		</form>
	</div>

	<?php if ( ! isset($default_styles) OR $default_styles != FALSE): ?>
		<script>
			$('#write-product-review-button').on('click', function()
			{
				var $form = $('#add-product-review-form');
				$form.is(':visible') ? $form.hide() : $form.show();
			});

			$('#add-product-review-form').on('submit', function(ev)
			{
				ev.preventDefault();

				if ($(this).validationEngine('validate'))
				{
					this.submit();
				}
			});

		</script>
	<?php endif; ?>
<?php endif; ?>
