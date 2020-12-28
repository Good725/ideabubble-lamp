<?php foreach ($counties as $county => $deals): ?>
	<?php if (count($deals) > 0): ?>
		<h2 class="property-deal-heading">Co. <?= $county ?></h2>
		<div class="space-between-cols search-results-wrapper property-deal-wrapper">
			<?php foreach ($deals as $property): ?>
				<?php foreach (Model_Propman::getDeals($property->id) as $dateRange) { ?>
				<section class="col-xsmall-6 col-small-4 related-property">
					<?= View::factory('property_thumbnail')
						->set('property_item', $property)
						->set('show_heart', FALSE)
						->set('is_deal', TRUE)
						->set('description', FALSE)
						->set('dateRange', $dateRange);
					?>
				</section>
				<?php } ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endforeach ?>