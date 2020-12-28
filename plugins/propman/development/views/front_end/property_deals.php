<?php // Basic default engine view. This is overwritten at project level for Rent a Cottage ?>
<?php foreach ($counties as $county => $deals): ?>
	<?php if (count($deals) > 0): ?>
		<h2><?= $county ?></h2>
		<div class="search-results-wrapper">
			<?php foreach ($deals as $property): ?>
				<?php foreach (Model_Propman::getDeals($property->id) as $price) { ?>
				<a href="/property-details.html/<?= $property->url ?>"><?= $property->name ?></a><br />
				<?php } ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endforeach ?>