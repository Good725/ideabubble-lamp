<?php
$thumbnail = $property_item->get_thumbnail();
// Placeholder image, if no thumbnail exists
$thumbnail = $thumbnail ? $thumbnail : '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/properties/_thumbs/no_image_available.png';
$on_wishlist = $property_item->is_on_wishlist();
$wishlist_remove_text = __('Remove from wishlist');
$wishlist_add_text = __('Add to wishlist');
$show_heart = (!isset($show_heart) OR $show_heart);
?>

<?php if ($thumbnail AND $show_heart): ?>
	<button
		type="button"
		class="button-link heart-icon button-wishlist-<?= $on_wishlist ? 'remove' : 'add' ?>"
		data-id="<?= $property_item->id ?>"
		data-add_text="<?= $wishlist_add_text ?>"
		data-remove_text="<?= $wishlist_remove_text ?>"
		title="<?= $on_wishlist ? $wishlist_remove_text : $wishlist_add_text ?>"
		>
		<span class="sr-only"><?= __('Wishlist') ?></span>
	</button>
<?php endif; ?>

<a href="<?= isset($link) ? $link : '/property-details.html/'.$property_item->url ?>">

	<?php if (isset($is_deal) AND $is_deal) : ?>
		<h4><?= $property_item->name ?></h4>
		<div><?= $property_item->address1 ?></div>
	<?php endif; ?>

	<div class="figure-wrapper">
		<?php if (isset($is_deal) AND $is_deal) : ?>
			<div class="property-deal-icon"><span><?= __('deal') ?></span></div>
		<?php endif; ?>

		<figure>
			<?php if ($thumbnail): ?>
				<img class="image-full" src="<?= $thumbnail ?>"/>
			<?php endif; ?>

			<?php if (isset($dateRange)): ?>
				<figcaption class="related-property-deal">
					<span class="related-property-price">&euro;<?= $dateRange['weekly_price'] ?></span>
					<span class="related-property-rate"><?= __('for 7 nights stay') ?></span>

					<div>valid from <strong><?= date('d-m-Y', strtotime($dateRange['starts'])) ?></strong> to
						<strong><?= date('d-m-Y', strtotime($dateRange['ends']))?></strong></div>
				</figcaption>
			<?php else: ?>
				<?php $rates = Model_Propman::getRates($property_item->group_id, $property_item->property_type_id, time()) ?>
				<?php if (@$rates[0]): ?>
					<figcaption class="related-property-pricerate">
						From
						<span class="related-property-price">&euro;<?= $rates[0]['weekly_price'] ?></span>
						<span class="related-property-rate"><?= __('Per week') ?></span>
					</figcaption>
				<?php endif; ?>
			<?php endif; ?>
			<?php
			/*
			 * $description === TRUE or not defined => use the name and summary as caption
			 * $description === FALSE               =>  no caption
			 * $description === anything else       => use the value of $description as the caption
			 */
			?>
			<?php if (!isset($description) OR $description): ?>
				<figcaption class="related-property-description">
					<?php if (isset($description) AND $description !== TRUE): ?>
						<?= $description ?>
					<?php else: ?>
						<h4><?= $property_item->name ?></h4>
						<?= $property_item->summary ?>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>
		</figure>
	</div>
</a>
