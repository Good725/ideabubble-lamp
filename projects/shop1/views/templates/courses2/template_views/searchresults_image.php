<?php ob_start(); ?>
<h4><?= $property_data->name ?></h4>
<ul class="property-quick-details">
	<li><?= $property_data->building_type->name ?></li>
	<li>Sleeps <?= $property_data->max_occupancy ?></li>
	<li><?= $property_data->count_beds() ?> Beds</li>
</ul>
<?php $caption = ob_get_clean(); ?>
<section class="col-xsmall-12 col-small-6 col-medium-4 related-property property-card">
	<?= View::factory('property_thumbnail')->set('property_item', $property_data)->set('description', $caption)->set('link', $link) ?>
</section>
