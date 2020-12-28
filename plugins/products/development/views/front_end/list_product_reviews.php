<?php if (Settings::instance()->get('enable_customer_reviews') == '1'): ?>
	<div class="product-reviews-wrapper" id="product-reviews">
		<div class="product-reviews-feed-wrapper">
			<h2>Reviews</h2>

			<div class="product-reviews-feed">
				<?php foreach ($reviews as $review): ?>
					<section class="product-review">
						<header>
							<div class="product-review-rating">
								<span class="product-review-rating-amount"><?= $review->rating ?></span>
								<span class="product-review-rating-stars">
									<?php for ($i =0; $i < $review->rating AND $i < 5; $i++): ?>
										<span></span>
									<?php endfor; ?>
								</span>
							</div>
							<h3 class="product-review-heading"><?= $review->title ?></h3>
							<div>
								By <span class="product-review-author"><?= $review->author ?></span>
								on <time datetime="<?= $review->date_created ?>"><?= date('H:i j F Y', strtotime($review->date_created)) ?></time>
							</div>
						</header>
						<div class="product-review-comment">
							<?= $review->print_review() ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

	<?php if ( ! isset($default_styles) OR $default_styles != FALSE): ?>
		<link rel="stylesheet" type="text/css" href="/engine/plugins/products/css/front_end/product_reviews.css" />
	<?php endif; ?>
<?php endif; ?>
