<section class="col-xsmall-12 property-card">
	<div class="property-card-image-wrapper">
        <div class="property-card-figure">
            <?= View::factory('property_thumbnail')->set('property_item', $property_data)->set('description', FALSE)->set('link', $link) ?>
        </div>
	</div>

	<div class="property-card-details">
		<div class="property-card-heading">
			<a href="<?= $link ?>">
				<h2><?= $property_data->name ?></h2>
			</a>
		</div>

		<ul class="property-quick-details">
			<li><?= $property_data->building_type->name ?></li>
			<li>Sleeps <?= $property_data->max_occupancy ?></li>
			<li><?= $property_data->count_beds() ?> Beds</li>
		</ul>

		<div class="property-card-description">
			<?= $property_data->summary ?>
		</div>
		<div class="property-card-actions">
            <?php $ratecard = Model_Propman::getRates($property_data->group_id, $property_data->property_type_id, time(), TRUE); ?>
            <?php if (isset($ratecard[0])): ?>
				<a href="<?= $link ?>" class="button-primary property-card-button"><?= __('Book Now') ?></a>
			<?php else: ?>
				<a href="<?= $link ?>" class="button-secondary property-card-button"><?= __('View Details') ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>